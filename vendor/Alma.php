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
require_once __DIR__ . '/alma/php/SingletonObject.php';
require_once __DIR__ . '/alma/php/UtilityObject.php';
require_once __DIR__ . '/alma/utils/ClassUtil.php';

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
final class Alma extends \alma\php\SingletonObject
{

    /**
     * ルーティング・コールバック情報を保持する
     * @var array
     */
    private $m_events = array();

    /**
     * GETメソッドにルーティング・コールバックを設定する
     * @param string   $route      ルーティング情報
     * @param callback $callback   コールバック関数
     * @param array    $conditions 処理条件
     */
    public static function get($route, $callback, array $conditions = array())
    {
        $self = self::getInstance();
        $self->event('GET', $route, $callback, $conditions);
    }

    /**
     * POSTメソッドにルーティング・コールバックを設定する
     * @param string   $route      ルーティング情報
     * @param callback $callback   コールバック関数
     * @param array    $conditions 処理条件
     */
    public static function post($route, $callback, array $conditions = array())
    {
        $self = self::getInstance();
        $self->event('POST', $route, $callback, $conditions);
    }

    /**
     * PUTメソッドにルーティング・コールバックを設定する
     * @param string   $route      ルーティング情報
     * @param callback $callback   コールバック関数
     * @param array    $conditions 処理条件
     */
    public static function put($route, $callback, array $conditions = array())
    {
        $self = self::getInstance();
        $self->event('PUT', $route, $callback, $conditions);
    }

    /**
     * DELETEメソッドにルーティング・コールバックを設定する
     * @param string   $route      ルーティング情報
     * @param callback $callback   コールバック関数
     * @param array    $conditions 処理条件
     */
    public static function delete($route, $callback, array $conditions = array())
    {
        $self = self::getInstance();
        $self->event('DELETE', $route, $callback, $conditions);
    }

    /**
     * 処理を開始する
     */
    public static function run()
    {
        $self = self::getInstance();
        $self->execute();
    }

    /**
     * Almaを初期化する
     */
    protected function initialize()
    {
        \alma\utils\ClassUtil::addIncludePath(__DIR__);
        
        // クラスオートローダの設定
        spl_autoload_register(function($classname) {
                    \alma\utils\ClassUtil::classFileLoad($classname);
                });
    }

    /**
     * ルーティング・コールバックを登録する
     * @param string   $method     HTTPメソッド名
     * @param string   $route      ルーティング情報
     * @param callback $callback   コールバック関数
     * @param array    $conditions 処理条件
     */
    public function event($method, $route, $callback, array $conditions) {
        // メソッド別の配列構築
        if (!isset($this->m_events[$route])) {
            $condition = array('http:method' => $method);
            $condition = array_merge($conditions, $condition);
        
            $this->m_events[$route] = array(
                \alma\Router::ROUTE_INFO => array($callback),
                \alma\Router::ROUTE_CONDITION => $condition,
            );
        }
    }

    /**
     * 処理を開始する
     */
    public function execute()
    {
        // 定数の初期値設定
        if (!defined('ALMA_DEBUG')) {
            define('ALMA_DEBUG', false);
        }
        if (!defined('ALMA_DIR_APPLICATION')) {
            define('ALMA_DIR_APPLICATION', realpath('./app'));
        }
        define('ALMA_DIR_SYSTEM', __DIR__ . '/alma');
        
        // 環境チェック
        $this->environmentCheck();

        // アプリケーションを自動読み込み対象に変更
        \alma\utils\ClassUtil::addIncludePath(ALMA_DIR_APPLICATION);
        
        try {
            // 環境の読み込み
            $env = \alma\Environment::getInstance();

            // カレントURIを取得する
            $router = new \alma\Router($this->m_events);
            $router->dispatch();
        } catch (\alma\Exception $e) {
            header('HTTP/1.0 404 Not Found');
            $view = new \alma\helper\view\Alma();
            $data = array(
                'error_title' => null,
                'error_body' => null,
                'error_extend' => null,
                'execution_time' => null,
            );

            if (ALMA_DEBUG) {
                $data['error_title'] = $e->getTitle();
                $data['error_body'] = $e->getMessage();
                $data['error_extend'] = $this->parseException($e);
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
     * 環境チェック
     */
    private function environmentCheck()
    {
        // バージョンチェック
        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            $this->show500('PHPのバージョンが5.2.0未満です。');
        }
    
        // アプリケーションディレクトリチェック
        if (ALMA_DIR_APPLICATION === false) {
            $this->show500('アプリケーションディレクトリが見つかりません。');
        }
        
        // キャッシュディレクトリチェック
        $d = realpath(ALMA_DIR_APPLICATION . '/caches');
        if (!$d) {
            $this->show500('キャッシュディレクトリが見つかりません。');
        }
        if (!is_writable($d)) {
            $this->show500('キャッシュディレクトリに書き込み権限が必要です。');
        }
        
        // テンプレートディレクトリチェック
        $d = realpath(ALMA_DIR_APPLICATION . '/templates');
        if (!$d) {
            $this->show500('テンプレートディレクトリが見つかりません。');
        }
    }
    
    /**
     * 500 Internal Server Errorを発行して終了する。
     */
    private function show500($message)
    {
        $buf = array();
        $buf[] = '<!DOCTYPE html>';
        $buf[] = '<html lang="ja">';
        $buf[] = '<head>';
        $buf[] = '    <meta charset="utf-8">';
        $buf[] = '    <title>Alma Application Error</title>';
        $buf[] = '</head>';
        $buf[] = '<body>';
        $buf[] = '    <h1>Alma アプリケーションエラー</h1>';
        $buf[] = '    <p>' . $message . '</p>';
        $buf[] = '</body>';
        $buf[] = '</html>';
        
        if (extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

        header('HTTP/1.0 500 Internal Server Error');
        echo implode("\n", $buf);
        ob_end_flush();
        
        die();
    }

    /**
     * アプリケーション内で発生した例外を出力用に解析する
     * @param \Exception $e     アプリケーション内で発生した例外
     * @param integer    $level 再帰レベル（デフォルト0)
     * @return string 例外情報文字列
     */
    private function parseException(\Exception $e, $level=0)
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