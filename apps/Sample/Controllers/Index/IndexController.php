<?php
namespace Sample\Controllers\Index;


class IndexController extends \Sample\Controllers\BaseController
{

    public function homeAction()
    {

        $this->view->contentText = 'Hello world!';


    }
}