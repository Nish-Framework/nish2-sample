<?php


namespace Sample\Controllers\Miscellaneous;



use Nish\Http\Response;
use Nish\NishApplication;

class EventTestController extends \Sample\Controllers\BaseController
{
    public function __construct()
    {
        parent::__construct();

        //YOU MAY DEFINE EVENTS GLOBALLY AS WELL

        //Receiving default event manager object:
        //$eventManager = NishApplication::getDefaultEventManager();

        $this->eventManager->addEventListener('testEvent', 'eventGroup1', function (...$params) {
            //do something when triggered
            $this->logger->info('Test event run eventGroup1!' . print_r($params, true));
        }, true);

        $this->eventManager->addEventListener('testEvent', 'eventGroup2', function () {
            //do something when triggered
            $this->logger->info('Test event run! eventGroup2');
        });
    }

    public function testAction()
    {

        $this->disableView();

        $this->logger->info('Start running event testEvent.eventGroup1...');

        $this->eventManager->trigger('testEvent', 'eventGroup1', [1,2]); //run testEvent from group eventGroup1

        $this->logger->info('Start running event all testEvent listeners...');
        $this->eventManager->trigger('testEvent'); //run all testEvent listeners

        Response::sendJSONResponse(['response' => ['code' => 0]]);
    }

}