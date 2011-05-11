<?php
namespace Alma;

/**
 * モデルの処理を行う基底クラス
 */
class Model
{

    /**
     * モデルの内容を配列として出力する
     * @return array データ配列
     */
    public function toArray()
    {
        // 処理時間
        $env = \Alma\Environment::getInstance();
        $this->execution_time = sprintf('%.3F', $env->getProcessTime());
        $result = get_object_vars($this);
        return $result;
    }

    /**
     * ヘルパーを読み込む
     * @param string $helpername ヘルパー名
     * @param string $alias      エイリアス名。この名前でModelのクラス変数に登録される。 
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