<?php
namespace Models\Services;


use Configs\Config;
use Exceptions\EntityExistsException;
use Exceptions\InvalidParameterException;
use Models\Entities\ORM\Translation;
use Models\Entities\ORM\TranslationKey;

class TranslationService extends DefaultService
{
    public const NAMESPACE_WEBSITE = 'website';

    public static function getNamespaces()
    {
        return [
            self::NAMESPACE_WEBSITE => 'Web Site'
        ];
    }

    /**
     * @param string $namespace
     * @param string $lang
     * @param string $key
     * @param bool $getAsArray
     * @return array|Translation[]
     */
    public function getTranslations($namespace = '', $lang = '', $key = '', $getAsArray = false)
    {
        $conditions = [];
        $bind = [];


        if (!empty($namespace)) {
            $conditions[] = 't.namespace = :namespace';
            $bind['namespace'] = $namespace;
        }

        if (!empty($lang)) {
            $conditions[] = 't.lang = :lang';
            $bind['lang'] = $lang;
        }

        if (!empty($key)) {
            $conditions[] = 't.trans_key = :trans_key';
            $bind['trans_key'] = $key;
        }

        return Translation::selectBy(implode(' AND ', $conditions), $bind, 'partial t.{id, namespace, lang, trans_key, trans_value}', $getAsArray);
    }

    /**
     * @param string $namespace
     * @param string $key
     * @param bool $getAsArray
     * @return array|TranslationKey[]
     */
    public function getTranslationKeys($namespace = '', $key = '', $getAsArray = false)
    {
        $conditions = [];
        $bind = [];


        if (!empty($namespace)) {
            $conditions[] = 't.namespace = :namespace';
            $bind['namespace'] = $namespace;
        }

        if (!empty($key)) {
            $conditions[] = 't.trans_key = :trans_key';
            $bind['trans_key'] = $key;
        }

        return TranslationKey::selectBy(implode(' AND ', $conditions), $bind, 'partial t.{id, namespace, trans_key}', $getAsArray);
    }


    public function saveKey($namespace, $key)
    {
        $keyObj = TranslationKey::findOneBy(['namespace' => $namespace, 'trans_key' => $key]);

        if (!empty($keyObj)) {
            throw new EntityExistsException("Translation key exists! namespace: $namespace, trans_key: $key");
        }

        $keyObj = new TranslationKey();
        $keyObj->setNamespace($namespace);
        $keyObj->setTransKey($key);

        $this->createEntity($keyObj);
    }

    public function saveTranslation($namespace, $lang, $key, $value)
    {
        $translation = Translation::findOneBy(['namespace' => $namespace, 'lang' => $lang, 'trans_key' => $key]);

        if (empty($translation)) {
            $id = null;
            $translation = new Translation();
            $translation->setNamespace($namespace);
            $translation->setLang($lang);
            $translation->setTransKey($key);
        } else {
            $id = $translation->getId();
        }

        $translation->setTransValue($value);

        if (is_numeric($id)) {
            $this->updateEntity($translation);
        } else {
            $this->createEntity($translation);
        }
    }

    /**
     * @param array $translations
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function saveTranslations($translations = [])
    {
        if (count($translations) > 0) {
            foreach ($translations as $tr) {
                $value = $tr['value'];
                $namespace = $tr['namespace'];
                $lang = $tr['lang'];
                $key = $tr['key'];

                $translation = Translation::findOneBy(['namespace' => $namespace, 'lang' => $lang, 'trans_key' => $key]);

                if (empty($translation)) {
                    $id = null;
                    $translation = new Translation();
                    $translation->setNamespace($namespace);
                    $translation->setLang($lang);
                    $translation->setTransKey($key);
                    $translation->setTransValue($value);

                    $this->entityManager->persist($translation);

                    $this->createEntity($translation, false);
                } else {
                    $translation->setTransValue($value);

                    $this->updateEntity($translation, false);
                }
            }

            $this->entityManager->flush();
            $this->entityManager->clear();
            Translation::flushCache();

            $dir = Config::getRootDir().'/_cache/translations';

            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                unlink("$dir/$file");
            }
        }

    }

    /**
     * @param $id
     * @param bool $realDelete
     * @throws InvalidParameterException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function deleteTranslationById($id, $realDelete = false)
    {
        if (!is_numeric($id)) {
            throw new InvalidParameterException('Invalid id: '.$id);
        }

        $item = Translation::findById($id);

        if ($item != null) {
            if ($realDelete === true) {
                $this->deleteFromDB($item);
            } else {
                $this->fakeDelete($item);
            }
        }
    }

    /**
     * @param $id
     * @param bool $realDelete
     * @throws InvalidParameterException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function deleteTranslationKeyById($id, $realDelete = false)
    {
        if (!is_numeric($id)) {
            throw new InvalidParameterException('Invalid id: '.$id);
        }

        $item = TranslationKey::findById($id);

        if ($item != null) {
            if ($realDelete === true) {
                $this->deleteFromDB($item);
            } else {
                $this->fakeDelete($item);
            }
        }
    }
}