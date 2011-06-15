<?php
/**
 * Copyright 2011 Takeshi Kawamoto
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *    http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace alma;

/**
 * URLルーティングを行うクラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Framework
 * @package   Alma
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
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
    public function __construct(array $events = array())
    {
        // カレントURLの取得
        $this->m_current = '/';
        if (isset($_SERVER['PATH_INFO'])) {
            $this->m_current = $_SERVER['PATH_INFO'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $this->m_current = $_SERVER['ORIG_PATH_INFO'];
        }

        // ルート設定の取得
        $route = array();
        if (file_exists(ALMA_DIR_APPLICATION . '/config/route.php')) {
            include ALMA_DIR_APPLICATION . '/config/route.php';
        }
        $route = array_merge($route, $events);
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
                $call = 'checkCondition' . ucfirst($type);
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
        if (is_array($tr)) {
            // コールバック
            $callback = $tr[0];
            call_user_func($callback);
        } else {
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