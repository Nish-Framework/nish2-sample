<?php


namespace Sample\Middlewares;


use Nish\NishApplication;
use Nish\Routes\RouteManager;
use Nish\Sessions\SessionManagerContainer;
use Nish\Translators\ITranslator;
use Sample\Sessions\RequestUser;

class UserControl
{
    /* @var RouteManager */
    private $routeManager;

    /* @var \Symfony\Component\HttpFoundation\Session\Session */
    private $sessionManager;

    /* @var ITranslator */
    private $translator;

    public function __construct()
    {
        $this->routeManager = new RouteManager();
        $this->sessionManager = SessionManagerContainer::get();

        $this->translator = NishApplication::getDefaultTranslator();
    }

    public function banIfUserIsLoginned()
    {
        if (RequestUser::isLoginned()) {
            $this->showWarningMessageToClient($this->translator->translate('You are already loginned!'));
            $this->routeManager->routeByName('dashboard');
        }
    }

    public function banIfUserIsNotLoginned()
    {
        if (!RequestUser::isLoginned()) {
            $this->showWarningMessageToClient($this->translator->translate('login_required_msg','Login required!'));
            $this->routeManager->routeByName('login');
        }
    }

    public function showWarningMessageToClient($message = 'Error!')
    {
        $this->sessionManager->getFlashBag()->set('warning', $message);
    }
}