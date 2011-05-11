<?php
/**
 * Short Description of Base64 file.
 * 
 * Long Description of Base64 file.
 * 
 * @category  <category>
 * @package   <package>
 * @copyright Copyright (c) t-kawamoto
 * @license   <license>
 * @version   <version>
 * @link      <link>
 */
namespace Alma\Image;

/**
 * Short Description of Base64 class.
 * 
 * Long Description of Base64 class.
 * 
 * @author t-kawamoto
 * @category  <category>
 * @package   <package>
 * @copyright Copyright (c) t-kawamoto
 * @license   <license>
 * @version   <version>
 * @link      <link>
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

        // キャッシュファイルの検索
        $cachefind = false;
        $cachefile = null;
        if ($cachedir) {
            $cachefile = sha1($imagepath);
            $cachefile = sprintf('%s/img/%s/%s/%s'
                    , $cachedir
                    , substr($cachefile, 0, 2)
                    , substr($cachefile, 2, 2)
                    , substr($cachefile, 4));

            if (file_exists($cachefile) && filemtime($imagepath) < filemtime($cachefile)) {
                $cachefind = true;
            }
        }

        if ($cachefind) {
            // キャッシュを読み込み
            $result = file_get_contents($cachefile);
        } else {
            // 画像データをBASE64化
            $bin = fread(fopen($imagepath, 'r'), filesize($imagepath));
            $base64 = base64_encode($bin);
            $result = sprintf('data:%s;base64,%s', $mime, $base64);
            if (!empty($cachedir) && !empty($cachefile)) {
                mkdir(dirname($cachefile), 777, true);
                file_put_contents($cachefile, $result);
            }
        }

        // 画像データ文字列を返却する
        return $result;
    }
}