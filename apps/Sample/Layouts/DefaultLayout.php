<?php
namespace Sample\Layouts;


use Nish\MVC\Layout;
use Nish\Sessions\SessionManagerContainer;
use Sample\Configs\SampleConfig;
use Sample\Sessions\RequestUser;

class DefaultLayout extends Layout
{

    protected $fullBasePath;

    public function __construct()
    {
        parent::__construct();
    }

    public function setLayoutAction()
    {
        $viewDir = SampleConfig::getSampleAppViewBaseDir();

        $this->view->fullBasePath = $this->fullBasePath = SampleConfig::getFullBasePath();

        $this->view->skinPath = $this->view->fullBasePath.'/Skins';
        $this->view->requestURI = str_replace($this->view->fullBasePath,'', $_SERVER['REQUEST_URI']);

        $this->view->userIsLoginned = RequestUser::isLoginned();
        $this->view->stylesVersion = SampleConfig::getStylesVersion();

        if ($this->view->userIsLoginned) {
            $this->view->userFirstName = RequestUser::getFirstName();
            $this->view->userLastName = RequestUser::getLastName();
        }

        $this->view->languages = SampleConfig::getAvailableLanguages();
        $this->view->defaultLang = SampleConfig::getDefaultLanguage();

        ### BEGIN: Set Flash Messages ---
        $this->view->flashMessages = [];

        /**
         * @var \Symfony\Component\HttpFoundation\Session\Session $sessionManager
         */
        $sessionManager = SessionManagerContainer::getIfExists();

        if ($sessionManager) {
            $sessionFlashBag = $sessionManager->getFlashBag();


            foreach (['success', 'warning', 'error', 'info'] as $type) {
                if ($sessionFlashBag->has($type)) {
                    $this->view->flashMessages[$type] = [];

                    $messages = $sessionFlashBag->get($type, []);

                    foreach ($messages as $m) {
                        $this->view->flashMessages[$type][] = $m;
                    }
                }
            }
        }

        ### END: Set Flash Messages ---

        $this->setViewFile($viewDir . '/defaultLayout.phtml');
    }
}