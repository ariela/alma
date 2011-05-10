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
}