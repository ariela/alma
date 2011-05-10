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
}