<?php
namespace Alma\Php;

/**
 * 継承したクラスをシングルトンクラスとして設定する基底クラス
 */
abstract class SingletonObject
{
    /**
     * 生成したインスタンスを保持する
     * @var array
     */
    private static $s_instances = array();

    /**
     * 外部からのインスタンス化と複数回のインスタンス化を禁止
     */
    private final function __construct()
    {
        if (isset(self::$s_instances[get_called_class()])) {
            throw new \Exception("A " . get_called_class() . " instance already exists.");
        }
        static::initialize();
    }

    /**
     * クローンの禁止
     */
    private final function __clone()
    {
        throw new \Exception('A ' . get_called_class() . ' cannot clone.');
    }

    /**
     * クラスの初期化
     */
    protected function initialize()
    {
        
    }

    /**
     * インスタンスを取得する
     * @return 
     */
    public static final function getInstance()
    {
        $c = get_called_class();
        if (!isset(self::$s_instances[$c])) {
            self::$s_instances[$c] = new static();
        }
        return self::$s_instances[$c];
    }
}