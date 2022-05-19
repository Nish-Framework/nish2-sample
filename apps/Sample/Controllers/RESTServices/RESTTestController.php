<?php

namespace Sample\Controllers\RESTServices;

use Nish\Http\Response;
use Sample\Controllers\BaseController;

class RESTTestController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function testAction()
    {
        $someResponse = [
            'param1' => 1,
            'param2' => 'param-2',
            'arrayParam' => [1,2,3,4,5]
        ];

        Response::sendJSONResponse(['result' => $someResponse]);
    }
}