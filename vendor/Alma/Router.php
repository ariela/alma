<?php
namespace Alma;

class Router
{
    /**
     * ルート情報
     * @var string
     */
    const ROUTE_INFO = 'route';

    /**
     * ルート使用条件
     * @var string
     */
    const ROUTE_CONDITION = 'condition';

    /**
     * リクエストメソッド GET
     */
    const METHOD_GET = 'GET';

    /**
     * リクエストメソッド POST
     */
    const METHOD_POST = 'POST';

    /**
     * リクエストメソッド PUT
     */
    const METHOD_PUT = 'PUT';

    /**
     * リクエストメソッド DELETE
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * カレントURL
     * @var string
     */
    private $m_current;
    /**
     * ルーティング設定情報
     * @var array
     */
    private $m_routeinfo;

    /**
     * ルート管理クラスの初期化
     */
    public function __construct()
    {
        // カレントURLの取得
        $this->m_current = '/';
        if (isset($_SERVER['PATH_INFO'])) {
            $this->m_current = $_SERVER['PATH_INFO'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $this->m_current = $_SERVER['ORIG_PATH_INFO'];
        }

        // ルート設定の取得
        include ALMA_DIR_CONFIG . '/route.php';
        $this->m_routeinfo = $route;
    }

    public function dispatch()
    {
        $tr = '';

        // ルーティング情報の処理
        $ri = $this->m_routeinfo;
        foreach ($ri as $pattern => $info) {
            $route = @$info[self::ROUTE_INFO] ? : array();
            $cond = @$info[self::ROUTE_CONDITION] ? : array();

            // 条件チェック
            $finded = false;
            $check = false;
            foreach ($cond as $k => $v) {
                $check = true;
                list($type, $target) = explode(':', $k);
                $call = 'checkCondition' . ucFirst($type);
                $finded = $finded || $this->$call($target, $v);
            }
            if ($check && !$finded) continue;

            // ルーティングチェック
            if ($this->m_current === $pattern) {
                $tr = $route;
            }

            // ルーティングパターンチェック
            if (empty($tr)) {
                $pattern = str_replace('[:all]', '(.+)', $pattern);
                $pattern = str_replace('[:any]', '([^/]+)', $pattern);
                $pattern = str_replace('[:num]', '(\\d+)', $pattern);
                $pattern = str_replace('/', '\\/', $pattern);
                $pattern = '/^' . $pattern . '\\/?$/';

                $result = preg_replace($pattern, $route, $this->m_current);
                if ($this->m_current !== $result) {
                    $tr = $result;
                }
            }

            if (!empty($tr)) {
                break;
            }
        }

        // コントローラ実行
        $current = array_filter(explode('/', $tr));
        $ctr = array_shift($current);
        $act = array_shift($current);

        // コントローラ名構築
        $ctr = '\\Controller\\' . ucfirst($ctr);

        // コントローラクラスがない場合
        if (!class_exists($ctr, true)) {
            throw new Exception("指定されたコントローラ「{$ctr}」が見つかりません。");
        }

        // コントローラオブジェクト構築
        $ctr = new $ctr();

        // アクションがない場合
        if (!method_exists($ctr, $act)) {
            $n = get_class($ctr);
            throw new Exception("指定されたアクション「{$n}::{$act}」が見つかりません。");
        }

        // アクションの実行
        call_user_func_array(array($ctr, $act), $current);
    }

    private function checkConditionHttp($target, $value)
    {
        switch ($target) {
            case 'method':
                return $_SERVER['REQUEST_METHOD'] === $value;
        }
        return false;
    }

    private function checkConditionEnv($target, $value)
    {
        switch ($target) {
            case 'https':
                return isset($_SERVER['HTTPS']) === $value;
        }
        return false;
    }
}