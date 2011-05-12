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
namespace Alma\Cache;

/**
 * システムによって自動的にキャッシュクラスを生成するFactoryクラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Cache
 * @package   Alma/Cache
 * @copyright Copyright (c) 2011 Takeshi Kawamoto
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
final class Factory
{
    private static $s_instance;

    /**
     * 新しいキャッシュクラスを取得する
     * @return \Alma\Cache\ICache
     */
    public static function getNewInstance()
    {
        $result = null;
        if (function_exists('apc_add')) {
            return new Apc();
        } else {
            return new File();
        }
    }

    /**
     * 唯一のキャッシュクラスを取得する。
     * @return \Alma\Cache\ICache
     */
    public static function getSingleton()
    {
        if (self::$s_instance === null) {
            self::$s_instance = self::getNewInstance();
        }
        return self::$s_instance;
    }
}