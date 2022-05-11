<?php


use Configs\Config;
use Nish\Commons\GlobalSettings;
use Nish\NishApplication;
use Sample\Configs\SampleConfig;
use Sample\Controllers\AnnotationTests\Php8AnnotationTestController;

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set ('display_errors', 'off');
ini_set ('display_startup_errors', 'off');
ini_set("error_log", __DIR__.'/logs/php_error-'.date('Y-m-d', time()).'.log');

require_once __DIR__ . '/../../../vendor/autoload.php'; // Autoload files using Composer autoload

Config::loadConfigs();
\Nish\Utils\DateTime\NishDateTime::setTimezone(Config::getTimezone());
NishApplication::setLogLevel(Config::getLogLevel());

try {
    define('LANG', 'en');
    GlobalSettings::put('crud_service', 'cli');
    GlobalSettings::put('crud_operator_id', 0);

    SampleConfig::setDefaultLogger();
    SampleConfig::setRedisClient();
    SampleConfig::setDefaultCacher();
    SampleConfig::configureDoctrine();
    SampleConfig::setDefaultTranslator();

    $app = new NishApplication();
    $app->setDebugMode(SampleConfig::isInDebugMod());
    $app->setViewDir(SampleConfig::getSampleAppViewBaseDir());
    $app->setAppRootDir(__DIR__);
    $app->setLayout(new \Sample\Layouts\DefaultLayout());

    /* Sample - 1 */
    $app->runControllerAction(Php8AnnotationTestController::class, 'testAction');

    /* Sample - 2 */
    $controller = new Php8AnnotationTestController();
    $app->runControllerAction($controller, 'test2Action', [['firstParam'=>1, 'secondParam'=>2, 'thirdParam'=>3]], \Sample\CLI\CLIModule::class);

} catch (\Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}

