<?php
namespace Alma\View;

/**
 * Twigで出力処理を行うビュークラス
 */
class Twig implements IView
{
    /**
     * テンプレートディレクトリパス
     */
    protected $tmplatedir = array();
    /**
     * Twigエンジンを保持
     * @var Twig_Environment
     */
    protected $engine;

    /**
     * Twigを初期化する
     */
    public function __construct()
    {
        // テンプレートディレクトリの指定
        $loader = new \Twig_Loader_Filesystem(array(
                    realpath(ALMA_DIR_TEMPLATES),
                    realpath(ALMA_DIR_VENDOR . '/Alma/templates'),
                ));

        // テンプレートエンジンの作成
        $this->engine = new \Twig_Environment($loader, array(
                    'cache' => realpath(ALMA_DIR_CACHE),
                    'auto_reload' => true,
                ));
    }

    /**
     * Twigを利用して画面出力を行う
     * @param string $template テンプレート名
     * @param mixed  $data     出力データ 
     */
    public function display($template, $data = null)
    {
        if ($data === null) {
            $data = array();
        } elseif (is_object($data) && $data instanceof \Alma\Model) {
            $data = $data->toArray();
        }

        // 拡張子がない場合はhtmlをつける
        $info = pathinfo($template);
        if (!isset($info['extension'])) {
            $template .= '.html';
        }

        if (!file_exists(ALMA_DIR_TEMPLATES . '/' . $template)
                && !file_exists(dirname(__DIR__) . '/templates/' . $template)) {
            throw new \Alma\Exception("テンプレートファイル「{$template}」が見つかりません。");
        }

        // テンプレートエンジンを実行
        try {
            $template = $this->engine->loadTemplate($template);
            echo $template->render($data);
        } catch (\Exception $e) {
            throw new \Alma\Exception('Twigの処理でエラーが発生しました。', 'Twigエラー', $e);
        }
    }
}
