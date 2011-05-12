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
namespace Alma;

/**
 * モデルの処理を行う基底クラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Framework
 * @package   Alma
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Model
{

    /**
     * モデルの内容を配列として出力する
     * @return array データ配列
     */
    public function toArray()
    {
        // 処理時間
        $env = \Alma\Environment::getInstance();
        $this->execution_time = sprintf('%.3F', $env->getProcessTime());
        $result = get_object_vars($this);
        return $result;
    }

    /**
     * ヘルパーを読み込む
     * @param string $helpername ヘルパー名
     * @param string $alias      エイリアス名。この名前でModelのクラス変数に登録される。 
     */
    protected function loadHelper($helpername, $alias = null)
    {
        // ヘルパー名の構築
        $helper = explode('/', $helpername);
        array_unshift($helper, 'helper');
        array_unshift($helper, 'alma');
        $helper = array_map('ucfirst', $helper);
        $helper = '\\' . implode('\\', $helper);
        if (!class_exists($helper, true)) {
            throw new Exception("指定されたヘルパー「{$helper}」が見つかりません。");
        }

        // エイリアス名の構築
        if ($alias === null) {
            $alias = str_replace('/', '_', $helpername);
        }

        // ヘルパーのインスタンス化
        $helper = new $helper();

        // ヘルパーの登録
        $this->$alias = $helper;
    }
}