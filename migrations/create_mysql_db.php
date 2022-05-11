<?php

use Configs\Config;
use Nish\Commons\Di;
use Nish\NishApplication;

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set ('display_errors', 'off');
ini_set ('display_startup_errors', 'off');
ini_set("error_log", __DIR__.'/logs/php_error-'.date('Y-m-d', time()).'.log');

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

Config::loadConfigs();
\Nish\Utils\DateTime\NishDateTime::setTimezone(Config::getTimezone());

NishApplication::setLogLevel(Config::getLogLevel());

try {
    $connectionParams = Config::getMysqlConnParams();
    //unset($connectionParams['dbname']);

    $connection = new \mysqli();
    $connection->connect(
        $connectionParams['host'],
        $connectionParams['user'],
        $connectionParams['password']
    );

    $connection->multi_query(file_get_contents('create_mysql_db.sql'));

    $connection->close();

    echo 'Done.';
} catch (\Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}

