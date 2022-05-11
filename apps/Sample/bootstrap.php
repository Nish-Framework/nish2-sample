<?php

use Nish\Commons\Di;
use Nish\Http\Response;
use Nish\NishApplication;
use Nish\Sessions\SessionManagerContainer;
use Nish\Utils\DateTime\NishDateTime;
use Sample\Configs\SampleConfig;
use Sample\Sessions\RequestUser;

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set ('display_errors', 'off');
ini_set ('display_startup_errors', 'off');
ini_set("error_log", __DIR__.'/logs/php_error-'.date('Y-m-d', time()).'.log');

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload

/*spl_autoload_register(function($className)
{

    $className = ltrim(str_replace("\\","/",$className), '/');

    $className = preg_replace("/^Sample\//", '', $className);

    $class = __DIR__ . '/' . "{$className}.php";

    include($class);
});*/

SampleConfig::loadConfigs();
NishDateTime::setTimezone(SampleConfig::getTimezone());

NishApplication::setLogLevel(SampleConfig::getLogLevel());
\Nish\Commons\GlobalSettings::put('crud_service', 'sample_app');
\Nish\Commons\GlobalSettings::put('crud_operator_id', '0');

\Nish\Sessions\SessionManagerContainer::set(function () {
    $session = new \Symfony\Component\HttpFoundation\Session\Session(new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage(), new \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag());

    if (!$session->isStarted()) {
        $session->start();
    }

    return $session;
},

    SessionManagerContainer::DEFAULT_MANAGER_CONTAINER_KEY
);

$uriToks = explode('/', trim(substr($_SERVER['REQUEST_URI'], strlen(SampleConfig::getSampleAppBasePath())), '/'));

if (array_key_exists($uriToks[0], SampleConfig::getAvailableLanguages()) && $uriToks[0] != SampleConfig::getDefaultLanguage()) {
    define('LANG', $uriToks[0]);
    define('ROOT_URL', SampleConfig::getFullBasePath().'/'.LANG);
} else {
    define('LANG', SampleConfig::getDefaultLanguage());
    define('ROOT_URL', SampleConfig::getFullBasePath());
}

SampleConfig::setDefaultLogger();

$routeManager = new \Nish\Routes\RouteManager();
$routeManager->setBasePath(SampleConfig::getSampleAppBasePath());
$routeManager->addRouteList(require_once ('Configs/routes.php'));
$routeManager->boot();
\Nish\Commons\Environment::setEnvName(SampleConfig::getEnv());

$app = new NishApplication();
$app->setDebugMode(SampleConfig::isInDebugMod());
$app->setViewDir(SampleConfig::getSampleAppViewBaseDir());
$app->setAppRootDir(__DIR__);
$app->setLayout(new \Sample\Layouts\DefaultLayout());


SampleConfig::setRedisClient();
SampleConfig::setDefaultCacher();
SampleConfig::configureDoctrine();
SampleConfig::setDefaultTranslator();

RequestUser::boot();

Response::setDefaultCharset('UTF-8');
Response::addDefaultHeader('Content-Type', 'text/html');
$app->run();