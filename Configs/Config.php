<?php


namespace Configs;

use Nish\Cachers\NishCacherContainer;
use Nish\Commons\Di;
use Nish\Commons\Environment;
use Nish\NishApplication;
use Sample\Configs\SampleConfig;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Utils\DoctrineCacheBridge;

class Config
{
    public const PAGINATION_LIMIT = 50;

    protected static $settings = [];
    protected static $env;

    public static function loadConfigs()
    {
        self::$env = Environment::ENV_DEV;
        self::$settings = require_once('config_dev.php');
    }

    /**
     * @return mixed
     */
    public static function getEnv()
    {
        return self::$env;
    }

    public static function isInDebugMod()
    {
        return self::$settings['debugMode'];
    }

    public static function getLogLevel()
    {
        return self::$settings['logLevel'];
    }

    public static function getTimezone()
    {
        return self::$settings['timeZone'];
    }

    public static function getAvailableLanguages()
    {
        return self::$settings['availableLanguages'];
    }

    public static function getDefaultLanguage()
    {
        return self::$settings['defaultLang'];
    }

    public static function getSampleAppBasePath()
    {
        return self::$settings['sampleAppBasePath'];
    }

    public static function getFullBasePath()
    {
        if (!isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'off') {
            $protocol = 'http';
        } else {
            $protocol = 'https';
        }

        return $protocol . '://' . $_SERVER['HTTP_HOST'] . self::getSampleAppBasePath();
    }

    public static function getRedisDsn()
    {
        return self::$settings['redis']['dsn'];
    }

    public static function getRedisOptions()
    {
        return self::$settings['redis']['options'];
    }

    public static function getMailerClass()
    {
        return self::$settings['mailer'];
    }

    public static function getMailSender()
    {
        return self::$settings['mailSender'];
    }

    public static function getMailReceivers()
    {
        return self::$settings['mailReceivers'];
    }

    public static function getCacheSystem()
    {
        return self::$settings['cacheSystem'];
    }

    public static function getRootDir()
    {
        return self::$settings['rootDir'];
    }


    public static function getMysqlConnParams()
    {
        return self::$settings['mysql'];
    }


    public static function setRedisClient()
    {
        Di::put('redisClient', function () {
            try {
                return RedisTagAwareAdapter::createConnection(
                // provide a string dsn
                    SampleConfig::getRedisDsn(),

                    // associative array of configuration options
                    SampleConfig::getRedisOptions());
            } catch (\Exception $e) {

                /* @var \Nish\Logger\Logger $logger */
                $logger = NishApplication::getDefaultLogger();

                $logger->alert($e->getMessage());

                return null;
            }

        });
    }

    public static function configureDoctrine()
    {
        Di::put('doctrineCacher', function () {
            if (!self::getCacheSystem()) {
                return null;
            }

            $cache = null;

            if (NishCacherContainer::exists()) {
                $defaultCache = NishCacherContainer::get();

                if ($defaultCache != null) {
                    $cache = new DoctrineCacheBridge($defaultCache);
                }
            }

            return $cache;
        });


        Di::put('ormEntityManager', function () {
            $isDevMode = (Environment::getEnvName() == Environment::ENV_DEV);
            $proxyDir = null;
            $cache = Di::get('doctrineCacher');

            $useSimpleAnnotationReader = false;

            $ormConf =  \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([self::getRootDir().'/Models/Entities/ORM'], $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);

            if ($cache) {
                $regionsConf = new \Doctrine\ORM\Cache\RegionsConfiguration(1800);

                $factory = new \Doctrine\ORM\Cache\DefaultCacheFactory($regionsConf, $cache);
                $ormConf->setSecondLevelCacheEnabled(true);
                $ormConf->getSecondLevelCacheConfiguration()->setCacheFactory($factory);
                $ormConf->getSecondLevelCacheConfiguration()->getRegionsConfiguration()->setDefaultLifetime(1800);
            }

            $entityManager = \Doctrine\ORM\EntityManager::create(self::getMysqlConnParams(), $ormConf);

            return $entityManager;
        });
    }
}