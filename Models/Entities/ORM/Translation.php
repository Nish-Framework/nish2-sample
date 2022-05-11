<?php
namespace Models\Entities\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="translations")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="translations")
 */
class Translation extends BaseEntity
{

    /**
     * @ORM\Column(type="string")
     */
    protected $namespace;


    /**
     * @ORM\Column(type="string")
     */
    protected $lang;

    /**
     * @ORM\Column(type="string")
     */
    protected $trans_key;

    /**
     * @ORM\Column(type="string")
     */
    protected $trans_value;

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
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang): void
    {
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    public function getTransKey()
    {
        return $this->trans_key;
    }

    /**
     * @param mixed $key
     */
    public function setTransKey($key): void
    {
        $this->trans_key = $key;
    }

    /**
     * @return mixed
     */
    public function getTransValue()
    {
        return $this->trans_value;
    }

    /**
     * @param mixed $value
     */
    public function setTransValue($value): void
    {
        $this->trans_value = $value;
    }

    public static function getCacheEntityRegion()
    {
        return 'translations';
    }
}