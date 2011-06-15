<?php
require_once dirname(__DIR__) . '/vendor/Alma.php';

/**
 * デバッグモードの設定
 * @var bool
 */
define('ALMA_DEBUG', true);

error_reporting(-1);

/**
 * アプリケーションディレクトリの定義
 * @var string
 */
define('ALMA_DIR_APPLICATION', dirname(__DIR__) . '/app');

/*
//Sinatora風に実行する
Alma::get('/', function() {
    echo 'ROOT';
});
*/

Alma::run();