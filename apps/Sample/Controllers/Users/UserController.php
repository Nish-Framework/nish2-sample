<?php
namespace Sample\Controllers\Users;


use Exceptions\InvalidDataException;
use Exceptions\InvalidParameterException;
use Models\Entities\ORM\User;
use Nish\Logger\Logger;
use Sample\Controllers\BaseController;
use Sample\Sessions\RequestUser;

class UserController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function dashboardAction()
    {
        $this->view->userFullName = RequestUser::getFullName();
    }


    public function loginAction()
    {
        if ($this->request->getMethod() == 'POST') {
            $email = trim($this->request->get('email', ''));
            $password = trim($this->request->get('password', ''));

            try {
                if (empty($email) || empty($password)) {
                    throw new InvalidParameterException();
                }

                /**
                 * @var User $user
                 */
                if ($user = RequestUser::login($email, $password)) {
                    $this->router->routeByName('dashboard');
                } else {
                    throw new InvalidDataException(RequestUser::getLastErrorMessage());
                }

            } catch (InvalidParameterException $e) {
                $this->showErrorMessageToClient($this->translator->translate('Please, enter required fields!'));
            } catch (InvalidDataException $e) {
                $this->showErrorMessageToClient($this->translator->translate($e->getMessage()));
            } catch (\Exception $e) {
                $this->logException($e, Logger::DEBUG);

                $this->showErrorMessageToClient($this->translator->translate('unexpected_error_warning','Unexpected error!'));
            } finally {
                $this->router->routeByName('login');
                $this->router->getPath('login');
            }
        }
    }

    public function logoutAction()
    {
        $this->disableView();

        RequestUser::logout();
        $this->gotoHomepage();
    }
}