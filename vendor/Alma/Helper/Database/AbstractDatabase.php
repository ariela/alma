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
namespace Alma\Helper\Database;

/**
 * データベースヘルパーの抽象クラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Helper
 * @package   Alma/Helper/Database
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
abstract class AbstractDatabase
{
    /**
     * データベース設定を保持するフィールド
     * @var array
     */
    protected $m_config;
    /**
     * 選択されているデータベース設定
     * @var array 
     */
    protected $m_configSelected;

    /**
     * データベースヘルパーの初期化を行う
     */
    public final function __construct()
    {
        include ALMA_DIR_CONFIG . '/database.php';
        $this->m_config = $database;
        $this->m_configSelected = $database['default'];
    }

    /**
     * 使用するデータベース設定を選択する
     * @param string $target データベース設定名
     */
    public function select($target)
    {
        $this->m_configSelected = @ $this->m_config[$target] ? : array();
    }

    /**
     * データベースに接続する
     */
    abstract public function connect();
}