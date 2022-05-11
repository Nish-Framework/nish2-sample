<?php
namespace Sample\Controllers\Users;


use Nish\MVC\Module;
use Sample\Layouts\DefaultLayout;

class UsersModule extends Module
{

    public function configure()
    {
        $this->setLayout(new DefaultLayout());
        $this->setViewDir(self::getAppRootDir().'/Skins/UsersModule/');

        parent::configure();
    }
}