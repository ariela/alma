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
namespace alma\php;

/**
 * 継承したクラスをユーティリティクラスとして設定する基底クラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Php
 * @package   Alma/Php
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
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