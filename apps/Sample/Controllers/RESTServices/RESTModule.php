<?php
namespace Sample\Controllers\RESTServices;


use Nish\MVC\Module;
use Sample\Layouts\DefaultLayout;

class RESTModule extends Module
{

    public function configure()
    {
        /*
         * Views for all actions using this module will be disabled.
         * This may be called in the action itself, or in the constructor of the controller
         *
         * If you disable views on the module level, you will need to enable it back on the action level if required.
         */
        $this->disableViews();

        parent::configure();
    }
}