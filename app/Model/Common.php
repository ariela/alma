<?php
namespace Model;

/**
 * 共通のモデル情報
 */
class Common extends \Alma\Model
{

    /**
     * モデルの初期化
     */
    public function __construct()
    {
        // システム情報追加
        $this->sys_name = 'Alma';
        $this->sys_url = 'http://alma.transrain.net/';
        $this->sys_version = '0.0.1';
        $this->assets_style = 'assets/styles';
    }

}