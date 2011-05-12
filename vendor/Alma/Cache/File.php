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
 * ファイルにキャッシュを行うクラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Cache
 * @package   Alma/Cache
 * @copyright Copyright (c) 2011 Takeshi Kawamoto
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class File implements ICache
{
    /**
     * キャッシュディレクトリを保持する
     * @var string
     */
    private $m_cachedir;

    /**
     * キャッシュクラスを初期化する
     */
    public function __construct()
    {
        $this->m_cachedir = ALMA_DIR_CACHE;
    }

    /**
     * キャッシュファイル名を取得する
     * @param string $key キャッシュ保存名
     * @return string キャッシュファイル名
     */
    private function getCacheFileName($key)
    {
        $cachefile = $this->m_cachedir . '/';
        if (strlen($key) > 4) {
            $cachefile .= substr($key, 0, 2) . '/'
                    . substr($key, 2, 2) . '/'
                    . substr($key, 4);
        } else {
            $cachefile .= $key;
        }
        return $cachefile;
    }

    /**
     * ファイルのキャッシュディレクトリを変更する
     * @param string $dir キャッシュディレクトリ
     */
    public function setCacheDirectory($dir)
    {
        $this->m_cachedir = $dir;
    }

    /**
     * キャッシュ領域から保存されているデータを取得する
     * @param string  $key  キャッシュ保存名
     * @param integer $time 元となるデータの更新日付（未指定時は更新チェックを行わない）
     * @return mixed データが存在している場合はデータ内容、存在しない・有効期限切れ・更新されている
     *               場合はfalse
     */
    public function load($key, $time = null)
    {
        // キャッシュチェック
        if (!$this->contains($key, $time)) return false;

        // キャッシュ情報取得
        $cachefile = $this->getCacheFileName($key);
        $value = unserialize(file_get_contents($cachefile));
        return $value['data'];
    }

    /**
     * キャッシュ領域に保存されているデータが存在するかを確認する
     * @param string  $key  キャッシュ保存名
     * @param integer $time 元となるデータの更新日付（未指定時は更新チェックを行わない）
     * @return boolean データが存在する場合はtrue、存在しない・有効期限切れ・更新されている場合は
     *                 false
     */
    public function contains($key, $time = null)
    {
        $cachefile = $this->getCacheFileName($key);

        // ファイルが存在しない場合はfalse
        if (!file_exists($cachefile)) return false;

        // キャッシュ元データが更新されている場合はfalse
        if (!is_null($time)) if (filemtime($cachefile) < $time) return false;

        // 有効期限切れの場合はfalse
        $value = unserialize(file_get_contents($cachefile));
        if (time() > filemtime($cachefile) + $value['ttl']) {
            return false;
        }

        // キャッシュが存在する場合はtrue
        return true;
    }

    /**
     * キャッシュ領域にデータを保存する
     * @param string $key   保存名
     * @param string $value 保存するデータ
     * @param int    $ttl   保存期間(秒)（未指定時は1時間）
     * @return boolean 保存された場合はtrue、されていない場合はfalse
     */
    public function save($key, $value, $ttl=3600)
    {
        $data = array(
            'ttl' => $ttl,
            'data' => $value,
        );
        $cachefile = $this->getCacheFileName($key);
        $cachedir = dirname($cachefile);
        if (!file_exists($cachedir)) {
            mkdir($cachedir, 0777, true);
        }
        return (bool) file_put_contents($cachefile, serialize($data));
    }

    /**
     * 指定したデータをキャッシュ領域から削除する
     * @param string $key 保存名
     * @return boolean 削除された場合はtrue
     */
    public function delete($key)
    {
        $cachefile = realpath($this->getCacheFileName($key));
        if (!$cachefile) return false;
        return unlink($cachefile);
    }
}