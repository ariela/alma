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
 * キャッシュクラスを表すインターフェース
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Cache
 * @package   Alma/Cache
 * @copyright Copyright (c) 2011 Takeshi Kawamoto
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
interface ICache
{
    
    /**
     * キャッシュ領域から保存されているデータを取得する
     * @param string  $key  キャッシュ保存名
     * @param integer $time 元となるデータの更新日付（未指定時は更新チェックを行わない）
     * @return mixed データが存在している場合はデータ内容、存在しない・有効期限切れ・更新されている
     *               場合はfalse
     */
    public function load($key, $time);
    
    /**
     * キャッシュ領域に保存されているデータが存在するかを確認する
     * @param string  $key  キャッシュ保存名
     * @param integer $time 元となるデータの更新日付
     * @return boolean データが存在する場合はtrue、存在しない・有効期限切れ・更新されている場合は
     *                 false
     */
    public function contains($key, $time);

    /**
     * キャッシュ領域にデータを保存する
     * @param string $key   保存名
     * @param string $value 保存するデータ
     * @param int    $ttl   保存期間(秒)
     * @return boolean 保存された場合はtrue、されていない場合はfalse
     */
    public function save($key, $value, $ttl);
    
    /**
     * 指定したデータをキャッシュ領域から削除する
     * @param string $key 保存名
     * @return boolean 削除された場合はtrue
     */
    public function delete($key);

}