<?php
$database = array();

$database['default'] = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'port' => 3306,
    'dbname' => 'cview',
    'user' => 'cview',
    'password' => 'cview',
    'charset' => 'utf8',
    'driverOptions' => array(
        \PDO::ATTR_EMULATE_PREPARES => false
    ),
);