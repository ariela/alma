<?php
namespace Alma;

/**
 * 環境情報を管理するクラス
 */
class Environment extends Php\SingletonObject
{
    /**
     * 開始時間を保持
     * @var float 開始時間(マイクロ秒)
     */
    private $m_start;

    /**
     * クラスの初期化処理
     */
    protected function initialize()
    {
        $this->m_start = microtime(true);
    }

    /**
     * 処理時間を取得する
     * @return float 処理時間(マイクロ秒)
     */
    public function getProcessTime()
    {
        return microtime(true) - $this->m_start;
    }
}
