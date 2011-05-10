<?php

use \Alma\Router as Router;

$route = array();

// ログイン設定
$route['/login'] = array(
    Router::ROUTE_INFO => 'admin/login',
    Router::ROUTE_CONDITION => array(
        'http:method' => Router::METHOD_GET,
        'env:https' => true,
    ),
);

// ログイン処理
$route['/login'] = array(
    Router::ROUTE_INFO => 'admin/doLogin',
    Router::ROUTE_CONDITION => array(
        'http:method' => Router::METHOD_POST,
        'env:https' => true,
    ),
);

// ログアウト処理
$route['/logout'] = array(
    Router::ROUTE_INFO => 'admin/logout',
);


// 未設定時 デフォルト設定
$route['/'] = array(
    Router::ROUTE_INFO => 'pages/home',
);

// コントローラ指定時 デフォルト設定
$route['/[:any]'] = array(
    Router::ROUTE_INFO => '$1/home',
);

// コントローラ/アクション指定時 デフォルト設定
$route['/[:any]/[:any]'] = array(
    Router::ROUTE_INFO => '$1/$2',
);

// コントローラ/アクション/パラメータ指定時 デフォルト設定
$route['/[:any]/[:any]/[:all]'] = array(
    Router::ROUTE_INFO => '$1/$2/$3',
);

