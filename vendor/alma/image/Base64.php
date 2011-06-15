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
namespace alma\image;

/**
 * 画像データをBase64に変換するためのクラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Image
 * @package   Alma/Image
 * @copyright Copyright (c) 2011 Takeshi Kawamoto
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
class Base64
{

    /**
     * 画像ファイルのデータをBASE64化してデータ出力用文字列を返す。
     * imgタグのsrcにデータ出力用文字列を設定することにより画像が表示されます。
     * 
     * @param string $imagepath 対象の画像ファイルパス
     * @param string $cachedir  キャッシュ化する場合はキャッシュディレクトリを渡す
     * @return mixed 失敗時はfalse、成功時は出力用文字列
     */
    public static function convert($imagepath, $cachedir = null)
    {
        // キャッシュディレクトリを絶対パスに変換
        if ($cachedir !== null) $cachedir = realpath($cachedir);

        // 画像ファイルのパスを絶対パスに変換
        $imagepath = realpath($imagepath);
        if (!$imagepath) return false;

        // 画像ファイルの種類を取得する
        $imginfo = getimagesize($imagepath);
        if (!$imginfo) return false;
        $mime = $imginfo['mime'];

        // ファイルキャッシュクラス取得
        $cache = new \alma\cache\File();
        $cache->setCacheDirectory($cachedir);

        // キャッシュのチェック
        $cachekey = sha1($imagepath);
        $cachetim = filemtime($imagepath);
        if ($cache->contains($cachekey, $cachetim)) {
            // キャッシュが存在する場合はキャッシュを利用
            $result = $cache->load($cachekey);
        } else {
            // キャッシュが存在しない場合は構築
            $bin = fread(fopen($imagepath, 'r'), filesize($imagepath));
            $base64 = base64_encode($bin);
            $result = sprintf('data:%s;base64,%s', $mime, $base64);
            $cache->save($cachekey, $result, 31536000); // 1年間保存
        }

        // 画像データ文字列を返却する
        return $result;
    }
}