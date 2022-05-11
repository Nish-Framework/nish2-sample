<?php
namespace Models\Entities\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="translation_keys")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="translation_keys")
 */
class TranslationKey extends BaseEntity
{

    /**
     * @ORM\Column(type="string")
     */
    protected $namespace;

    /**
     * @ORM\Column(type="string")
     */
    protected $trans_key;

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getTransKey()
    {
        return $this->trans_key;
    }

    /**
     * @param string $key
     */
    public function setTransKey($key)
    {
        $this->trans_key = $key;
    }

    public static function getCacheEntityRegion()
    {
        return 'translation_keys';
    }
}