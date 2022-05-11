<?php
namespace Sample\Controllers\Index;


use Configs\Config;
use Nish\Exceptions\MailerException;
use Sample\Controllers\BaseController;
use Utils\SimpleMailer;

class FormsController extends BaseController
{
    public function contactUsAction()
    {
        if ($this->request->getMethod() == 'POST') {
            $this->disableView();


            $name = strip_tags($this->request->request->get('name'));
            $email = strip_tags($this->request->request->get('email'));
            $phone = strip_tags($this->request->request->get('phone'));
            $subject = strip_tags($this->request->request->get('subject'));
            $message = strip_tags($this->request->request->get('message'));

            if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
                $this->showErrorMessageToClient($this->translator->translate('Please, enter required fields!'));
                $this->router->routeByName('contactUs');

                return false;
            }


            try {
                SimpleMailer::sendBCCMail(
                    $subject,
                    "Name: $name<br>Email: $email <br> Phone: $phone <br> Message: $message",
                    Config::getMailReceivers()
                );

                $this->showSuccessMessageToClient($this->translator->translate('Message sent!'));
            } catch (MailerException $e) {
                $this->logException($e);

                $this->showErrorMessageToClient($this->translator->translate('Error!'));

            } catch (\Exception $e) {
                $this->logException($e);

                $this->showErrorMessageToClient($this->translator->translate('Error!'));
            }

            $this->router->routeByName('contactUs');
        } else {
            $this->renderView(false, $this->skinRootDir.'/Forms/contact-us.phtml');
        }
    }


}