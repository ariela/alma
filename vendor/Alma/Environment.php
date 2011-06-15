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
namespace alma;

/**
 * 環境情報を管理するクラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Framework
 * @package   Alma
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Environment extends php\SingletonObject
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
