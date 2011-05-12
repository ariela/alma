<?php
/**
 * ライブラリ設置ディレクトリのパス。
 * @var string
 */
define('ALMA_DIR_VENDOR', dirname(__DIR__) . '/vendor');

/**
 * アプリケーション設置ディレクトリのパス。
 * @var string
 */
define('ALMA_DIR_APP', dirname(__DIR__) . '/app');

/**
 * キャッシュ保存ディレクトリのパス。
 * 未定義時はALMA_DIR_APP/cachesが利用される。
 * @var string
 */
//define('ALMA_DIR_CACHE', ALMA_DIR_APP . '/caches');

/**
 * テンプレートファイル保存ディレクトリのパス。
 * 未定義時はALMA_DIR_APP/templatesが利用される。
 * @var string
 */
//define('ALMA_DIR_TEMPLATES', ALMA_DIR_APP . '/templates');

/**
 * 設定ファイル保存ディレクトリのパス。
 * 未定義時はALMA_DIR_APP/configが利用される。
 * @var string
 */
//define('ALMA_DIR_CONFIG', ALMA_DIR_APP . '/config');

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once ALMA_DIR_VENDOR . '/Alma/Bootstrap.php';
\Alma\Bootstrap::execute();