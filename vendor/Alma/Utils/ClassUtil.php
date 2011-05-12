<?php
/**
 * Copyright 2011 Takeshi Kawamoto
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *    http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Alma\Utils;

/**
 * クラスに関するユーティリティクラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Utils
 * @package   Alma/Utils
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
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
