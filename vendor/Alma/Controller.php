<?php
namespace Alma;

/**
 * コントローラの処理を定義した基底クラス
 */
class Controller
{
    /**
     * ビューオブジェクトを保持する
     * @var \Alma\View\IView
     */
    protected $view;

    /**
     * コントローラの初期化を行う
     */
    public function __construct()
    {
        include ALMA_DIR_CONFIG . '/system.php';

        // viewの構築
        $view = '\\Alma\\View\\' . ucfirst($system['view_type']);
        $this->view = new $view;
    }

    /**
     * モデルを取得する
     * @param string $modelname モデル名
     * @return \Alma\Model
     */
    protected function getModel($modelname)
    {
        $model = explode('/', $modelname);
        array_unshift($model, 'model');
        $model = array_map('ucfirst', $model);
        $model = '\\' . implode('\\', $model);

        if (!class_exists($model, true)) {
            throw new Exception("指定されたモデル「{$model}」が見つかりません。");
        }

        return new $model;
    }

    /**
     * ヘルパーを読み込む
     * @param string $helpername ヘルパー名
     * @param string $alias      エイリアス名。この名前でControllerのクラス変数に登録される。 
     */
    protected function loadHelper($helpername, $alias = null)
    {
        // ヘルパー名の構築
        $helper = explode('/', $helpername);
        array_unshift($helper, 'helper');
        array_unshift($helper, 'alma');
        $helper = array_map('ucfirst', $helper);
        $helper = '\\' . implode('\\', $helper);
        if (!class_exists($helper, true)) {
            throw new Exception("指定されたヘルパー「{$helper}」が見つかりません。");
        }

        // エイリアス名の構築
        if ($alias === null) {
            $alias = str_replace('/', '_', $helpername);
        }

        // ヘルパーのインスタンス化
        $helper = new $helper();

        // ヘルパーの登録
        $this->$alias = $helper;
    }
}