<?php
namespace Sample\Configs;

use Configs\Config;
use Models\Services\TranslationService;
use Nish\Cachers\NishCacherContainer;
use Nish\Commons\Di;
use Nish\Logger\Logger;
use Nish\NishApplication;
use Nish\Translators\Translator;
use Nish\Utils\DateTime\NishDateTime;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Sample\Sessions\RequestUser;

class SampleConfig extends Config
{

    public static function getSampleAppRootDir()
    {
        return self::$settings['sampleAppRootDir'];
    }

    public static function getSampleAppViewBaseDir()
    {
        return self::$settings['sampleAppRootDir'].'/Skins';
    }

    public static function getStylesVersion()
    {
        return self::$settings['sampleAppStylesVersion'];
    }

    public static function setDefaultLogger()
    {
        $streamHandler = new \Monolog\Handler\StreamHandler(self::getSampleAppRootDir().'/logs/'.(NishDateTime::format(time(),'Y-m-d')).'.log', NishApplication::getLogLevel());


        $streamHandler->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] || %channel%.%level_name% || %extra% || URI: ".$_SERVER['REQUEST_URI'].", Method: ".$_SERVER['REQUEST_METHOD']." || Message: %message% || Context: %context%".PHP_EOL.PHP_EOL));

        \Nish\Logger\NishLoggerContainer::configure(
            [$streamHandler],
            [function ($record) {
                if (RequestUser::isLoginned()) {
                    $record['extra']['userID'] = RequestUser::getId();
                    $record['extra']['userFullName'] = RequestUser::getFullName();
                } else {
                    $record['extra']['userID'] = '0';
                    $record['extra']['userFullName'] = 'None';
                }

                return $record;
            }]
        );
    }

    public static function setDefaultTranslator()
    {
        NishApplication::setDefaultTranslator(function () {

            $namespace = 'sample_app';
            $defaultLocale = 'tr';
            $languageList = array_keys(self::getAvailableLanguages());

            $translator = new Translator(LANG, self::getRootDir().'/_cache/translations', $defaultLocale, $namespace, function ($namespace, $key, $defaultTranslation) use ($languageList) {
                try {
                    $repo = new TranslationService();

                    $repo->saveKey($namespace, $key);

                    $translations = [];

                    foreach ($languageList as $lang) {
                        $translations[] = [
                            'namespace' => $namespace,
                            'key' => $key,
                            'lang' => $lang,
                            'value' => $defaultTranslation
                        ];
                    }

                    $repo->saveTranslations($translations);

                } catch (\Exception $e) {
                    $logger = NishApplication::getDefaultLogger();

                    if ($logger) {
                        $logger->warning('NishException: '.$e->getMessage().', Trace'.$e->getTraceAsString());
                    }
                }
            });

            if ($translator->isEmpty()) {
                try {

                    $repo = new TranslationService();

                    $__translations = $repo->getTranslations($namespace, LANG, null, true);

                    $resources = [];

                    foreach ($__translations as $tr) {
                        $resources[$tr['trans_key']] = $tr['trans_value'];
                    }

                    $translator->addTranslations($resources);

                } catch (\Exception $e) {

                    /* @var Logger $logger */
                    $logger = NishApplication::getDefaultLogger();

                    if (!empty($logger)) {
                        $logger->critical($e->getMessage());
                    }
                    echo '<h1>Translator is not loaded!</h1>';
                    exit();
                }
            }

            return $translator;
        });
    }

    public static function setDefaultCacher()
    {
        NishCacherContainer::configure(function () {
            if (!self::getCacheSystem()) {
                return null;
            }

            try {
                if (self::getCacheSystem() == 'file' || !Di::has('redisClient') || ($redisClient = Di::get('redisClient')) == null) {
                    return new \Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter('defaultCache',0, self::getRootDir().'/_cache/');
                } else {

                    if ($redisClient->isConnected()) {
                        return (new RedisTagAwareAdapter($redisClient,'defaultCache', 3600));
                    }

                    return null;
                }
            } catch (\Exception $e) {
                /* @var Logger $logger */
                $logger = NishApplication::getDefaultLogger();

                $logger->alert($e->getMessage());

                return null;
            }

        });
    }
}