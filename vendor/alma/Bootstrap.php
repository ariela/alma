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

require_once ALMA_DIR_VENDOR . '/Alma/Php/UtilityObject.php';
require_once ALMA_DIR_VENDOR . '/Alma/Utils/ClassUtil.php';
/**
 * アプリケーションを実行するブートクラス
 * 
 * @author    Takeshi Kawamoto <yuki@transrain.net>
 * @category  Framework
 * @package   Alma
 * @copyright Copyright (c) 2011 Takeshi Kawamoto <yuki@transrain.net>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.txt Apache License Version 2.0
 * @version   1.0.0
 * @link      https://github.com/ariela/alma
 */
final class Bootstrap extends Php\UtilityObject
{

    /**
     * アプリケーションを実行する
     */
    public static function execute()
    {
        // 未定義定数の初期化
        defined('ALMA_DEBUG')? : define('ALMA_DEBUG', true);
        defined('ALMA_DIR_SYSTEM')? : define('ALMA_DIR_SYSTEM', __DIR__);
        defined('ALMA_DIR_CACHE')? : define('ALMA_DIR_CACHE', ALMA_DIR_APP . '/caches');
        defined('ALMA_DIR_TEMPLATES')? : define('ALMA_DIR_TEMPLATES', ALMA_DIR_APP . '/templates');
        defined('ALMA_DIR_CONFIG')? : define('ALMA_DIR_CONFIG', ALMA_DIR_APP . '/config');

        // include_pathの追加
        Utils\ClassUtil::addIncludePath(ALMA_DIR_APP, ALMA_DIR_VENDOR);

        // クラスオートローダの設定
        spl_autoload_register(function($classname) {
                    \Alma\Utils\ClassUtil::classFileLoad($classname);
                });

        try {
            // 環境の読み込み
            $env = Environment::getInstance();

            // カレントURIを取得する
            $router = new Router();
            $router->dispatch();
        } catch (Exception $e) {
            header('HTTP/1.0 404 Not Found');
            $view = new Helper\View\Twig();
            $data = array(
                'error_title' => null,
                'error_body' => null,
                'error_extend' => null,
                'execution_time' => null,
            );

            if (ALMA_DEBUG) {
                $data['error_title'] = $e->getTitle();
                $data['error_body'] = $e->getMessage();
                $data['error_extend'] = self::parseException($e);
            } else {
                $data['error_title'] = $e->getTitle();
                $data['error_body'] = $e->getMessage();
            }

            $data['execution_time'] = sprintf('%.3F', $env->getProcessTime());
            $view->display('error', $data);
            exit(1);
        }
    }

    /**
     * アプリケーション内で発生した例外を出力用に解析する
     * @param \Exception $e     アプリケーション内で発生した例外
     * @param integer    $level 再帰レベル（デフォルト0)
     * @return string 例外情報文字列
     */
    private static function parseException(\Exception $e, $level=0)
    {
        $extend = '';
        if ($level === 0) {
            $extend = '<dl><dt class="t">例外発生：' . get_class($e) . '</dt>';
            $extend .= '<dd>';
        }
        $extend .= '<dl>';

        $extend .= '<dt>エラーメッセージ</dt>';
        $extend .= sprintf('<dd>%s</dd>', $e->getMessage());

        $extend .= '<dt>エラー箇所</dt>';
        $extend .= sprintf('<dd>%s :: %d行目</dd>', $e->getFile(), $e->getLine());

        $trace = $e->getTraceAsString();

        if (!empty($trace)) {
            $extend.= '<dt>バックトレース</dt>';
            $extend .= '<pre>' . $trace . '</pre>';
        }

        $previous = $e->getPrevious();
        if (!empty($previous)) {
            $extend .= '<dt class="t">以前の例外: ' . get_class($previous) . '</dt>';
            $extend .= '<dd>';
            $extend .= self::parseException($previous, $level + 1);
            $extend .= '</dd>';
        }

        $extend .= '</dl>';

        if ($level === 0) {
            $extend .= '</dd></dl>';
        }

        return $extend;
    }
}
