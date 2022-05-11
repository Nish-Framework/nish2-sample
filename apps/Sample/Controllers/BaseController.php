<?php

namespace Sample\Controllers;

use Nish\Http\Response;
use Nish\Logger\Logger;
use Sample\Configs\SampleConfig;
use Sample\Layouts\DefaultLayout;
use Sample\Sessions\RequestUser;

abstract class BaseController extends \Nish\MVC\Controller
{

    protected $fullBasePath;

    protected $websiteRootDir;
    protected $skinRootDir;
    protected $loggedRequestParams = null;

    public function __construct()
    {
        parent::__construct();

        if (empty($this->loggedRequestParams) && $this->loggedRequestParams !== false) {
            $this->loggedRequestParams = $this->request->request->all();
        }

        $this->logger->info('Request Params', ['GET' => $this->request->query->all(), 'POST' => $this->loggedRequestParams]);

        $this->websiteRootDir = SampleConfig::getSampleAppRootDir();
        $this->skinRootDir = SampleConfig::getSampleAppViewBaseDir();
        $this->view->fullBasePath = $this->fullBasePath = SampleConfig::getFullBasePath();
        $this->view->skinUrl = $this->view->fullBasePath.'/Skins';
        $this->view->stylesVersion = SampleConfig::getStylesVersion();


        $this->view->userIsLoginned = RequestUser::isLoginned();

        if ($this->view->userIsLoginned) {
            $this->view->userFirstName = RequestUser::getFirstName();
            $this->view->userLastName = RequestUser::getLastName();

            $this->view->userId = RequestUser::getId();
        } else {
            $this->view->userId = 0;
        }

        $this->setLayout(new DefaultLayout());
    }

    /**
     * @param \Exception $e
     * @param int $logLevel
     */
    public function logException($e, $logLevel = Logger::ERROR)
    {
        $this->logger->log($logLevel, 'NishException: '.$e->getMessage().' | Trace: '.$e->getTraceAsString());
    }

    public function showSuccessMessageToClient($message = 'Successful!')
    {
        $this->sessionManager->getFlashBag()->set('success', $message);
    }

    public function showInfoMessageToClient($message)
    {
        $this->sessionManager->getFlashBag()->set('info', $message);
    }

    public function showWarningMessageToClient($message = 'Error!')
    {
        $this->sessionManager->getFlashBag()->set('warning', $message);
    }

    public function showErrorMessageToClient($message = 'Error!', $useFlashBag = true)
    {
        if ($useFlashBag) {
            $this->sessionManager->getFlashBag()->set('error', $message);
        } else {
            $this->view->errorMsg = $message;
        }

    }

    public function showEmptyErrorPage($errorMessage = 'Unexpected error!')
    {
        $this->disableView();
        Response::sendResponse($errorMessage);
    }


    protected function gotoHomepage()
    {
        $this->router->routeByName('homepage');
    }

    protected function gotoLoginPage()
    {
        $this->router->routeByName('login');
    }
}