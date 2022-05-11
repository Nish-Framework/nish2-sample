<?php
namespace Sample\Sessions;

use Models\Entities\ORM\User as DBUser;
use Nish\Commons\GlobalSettings;
use Nish\PrimitiveBeast;
use Nish\Sessions\SessionManagerContainer;

class RequestUser extends PrimitiveBeast
{

    private static $userData = [];

    private static $lastErrorMessage = '';

    public static function bootFrom(array $userData)
    {
        self::$userData = $userData;
        GlobalSettings::put('crud_operator_id', self::getId());
    }

    public static function isLoginned()
    {
        return (
            is_numeric(self::getId())
            && self::getId() > 0
            && array_key_exists(self::getRole() ?? 'none', DBUser::getRoleTitles())
        );
    }


    public static function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            self::$lastErrorMessage = 'Please, enter your email and password!';
            return false;
        }

        /* @var DBUser $user */
        $user = DBUser::findOneBy(['email' => $email, 'password' => md5($password)]);

        if (!$user) {
            self::$lastErrorMessage = 'Wrong email or password!';
            return false;
        }

        /**
         * @var \Symfony\Component\HttpFoundation\Session\Session $sessionManager
         */
        $sessionManager = SessionManagerContainer::get();
        $sessionManager->set('user_data', $user->toArray());

        self::bootFrom($user->toArray());

        return $user;
    }

    public static function logout()
    {
        /**
         * @var \Symfony\Component\HttpFoundation\Session\Session $sessionManager
         */
        $sessionManager = SessionManagerContainer::get();
        $sessionManager->remove('user_data');
    }

    /**
     * @return string
     */
    public static function getLastErrorMessage(): string
    {
        return self::$lastErrorMessage;
    }

    public static function getId()
    {
        return self::$userData['id'] ?? null;
    }

    public static function getFirstName()
    {
        return self::$userData['first_name'] ?? '';
    }

    public static function getLastName()
    {
        return self::$userData['last_name'] ?? '';
    }

    public static function getFullName()
    {
        return self::getFirstName().' '.self::getLastName();
    }

    public static function getRole()
    {
        return self::$userData['role'] ?? null;
    }

    public static function getEmail()
    {
        return self::$userData['email'];
    }

    public static function isTester()
    {
        return self::$userData['flg_tester'] ? true : false;
    }


    public static function getGSM()
    {
        return self::$userData['gsm'];
    }

    public static function toArray()
    {
        return self::$userData;
    }

    public static function isAdmin()
    {
        return (self::getRole() == DBUser::ROLE_ADMIN);
    }

    public static function fulfillsRoleRequirement(?array $requiredRoles = null, $requiredId = null)
    {
        if (!self::isLoginned()) {
            return false;
        }

        if (!empty($requiredRoles) && !in_array(self::getRole(), $requiredRoles)) {
            return false;
        }

        if (is_numeric($requiredId) && $requiredId != self::getId()) {
            return false;
        }

        return true;
    }

    public static function boot()
    {
        /**
         * @var \Symfony\Component\HttpFoundation\Session\Session $sessionManager
         */
        $sessionManager = SessionManagerContainer::get();

        self::bootFrom($sessionManager->get('user_data', []));

        return true;
    }
}