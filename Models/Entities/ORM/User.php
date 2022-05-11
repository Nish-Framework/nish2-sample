<?php
namespace Models\Entities\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="users")
 */
class User extends BaseEntity
{
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    /**
     * @ORM\Column(type="string")
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $last_name;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     */
    protected $role;

    /**
     * @ORM\Column(type="string")
     */
    protected $gsm;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $default_lang;


    /**
     * @ORM\Column(type="smallint")
     */
    protected $flg_tester = 0;

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getGsm()
    {
        return $this->gsm;
    }

    /**
     * @param mixed $gsm
     */
    public function setGsm($gsm): void
    {
        $this->gsm = $gsm;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getDefaultLang()
    {
        return $this->default_lang;
    }

    /**
     * @param mixed $default_lang
     */
    public function setDefaultLang($default_lang): void
    {
        $this->default_lang = $default_lang;
    }

    /**
     * @return mixed
     */
    public function getFlgTester()
    {
        return $this->flg_tester;
    }

    /**
     * @param mixed $flg_tester
     */
    public function setFlgTester($flg_tester): void
    {
        $this->flg_tester = $flg_tester ? 1 : 0;
    }

    public static function getRoleTitles()
    {
        return [
           self::ROLE_USER => 'User',
           self::ROLE_ADMIN => 'Admin',
        ];
    }

    public static function getCacheEntityRegion()
    {
        return 'users';
    }
}