<?php
namespace Alma\Php;

/**
 * 継承したクラスをユーティリティクラスとして設定する基底クラス
 */
abstract class UtilityObject
{

    /**
     * インスタンス化の禁止
     */
    private final function __construct()
    {
        throw new \Exception('A ' . get_called_class() . ' cannot build instance.');
    }

    /**
     * クローンの禁止
     */
    private final function __clone()
    {
        throw new \Exception('A ' . get_called_class() . ' cannot clone.');
    }
}