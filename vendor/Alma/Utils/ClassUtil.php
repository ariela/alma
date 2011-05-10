<?php
namespace Alma\Utils;

/**
 * クラスに関するユーティリティクラス
 */
final class ClassUtil extends \Alma\Php\UtilityObject
{

    /**
     * 指定したパスをinclude_pathに追加する。
     * 最初に指定したパスから優先度が高くなる。
     * @param string $path 追加するパス
     * @param string $...
     */
    static public function addIncludePath()
    {
        // 変数初期化
        $args = array_map('realpath', func_get_args());
        $base = explode(PATH_SEPARATOR, get_include_path());

        // 元のパスを調整する
        foreach ($base as $path) {
            // パスの正規化
            $path = realpath($path);
            if (!$path) {
                continue;
            }

            if (!in_array($path, $args)) {
                $args[] = $path;
            }
        }

        set_include_path(implode(PATH_SEPARATOR, $args));
    }

    /**
     * 指定されたクラス名のクラスファイルを検索して追加する。
     * @param string $className クラス名
     */
    static public function classFileLoad($className)
    {
        if (!class_exists($className)) {
            // PEAR形式に合わせる
            $classPath = str_replace('\\', '_', $className);

            // パスを構築する
            $classPath = array_filter(explode('_', $classPath));
            $classPath = implode(DIRECTORY_SEPARATOR, $classPath);
            $classPath .= '.php';

            @include_once $classPath;
        }
    }
}
