<?php
namespace Sample\CLI;


use Nish\MVC\Module;
use Sample\Layouts\DefaultLayout;

class CLIModule extends Module
{

    public function configure()
    {
        $this->setLayout(new DefaultLayout());
        $this->setViewDir(self::getAppRootDir().'/Skins/CLIModule/');

        echo '<br>::: CLIModule::configure :::<br>';

        parent::configure();
    }
}