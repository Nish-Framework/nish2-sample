<?php

use Nish\Routes\Route;
use Sample\Controllers\AnnotationTests\Php7AnnotationTestController;
use Sample\Controllers\AnnotationTests\Php8AnnotationTestController;
use Sample\Controllers\Index\FormsController;
use Sample\Controllers\Index\IndexController;
use Sample\Controllers\Miscellaneous\MemoizeTestController;
use Sample\Controllers\RESTServices\RESTTestController;
use Sample\Controllers\Users\UserController;
use Sample\Controllers\Users\UsersModule;
use Sample\Middlewares\UserControl;


return [
    new Route('homepage', '/', [IndexController::class, 'homeAction'], null, ['GET']),
    new Route('contactUs', '/contact-us', [FormsController::class, 'contactUsAction'], null),

    new Route('dashboard', '/dashboard', [UserController::class, 'dashboardAction'], UsersModule::class, ['GET', 'POST'], [UserControl::class, 'banIfUserIsNotLoginned']),

    new Route('login', '/login', [UserController::class, 'loginAction'], UsersModule::class, ['GET', 'POST'], [UserControl::class, 'banIfUserIsLoginned']),
    new Route('logout', '/logout', [UserController::class, 'logoutAction'], UsersModule::class),

    new Route('php8AnnotTest', '/annot-test-php8/{firstParam<\d+>?1}/{secondParam<\d+>?2}',//firstParam: numeric and has default val (1); secondParam: numeric and has default val (2)
        [Php8AnnotationTestController::class, 'testAction']),

    new Route('php8AnnotTest2', '/annot-test2-php8/{firstParam<\d+>?1}/{secondParam<\d+>?2}',//firstParam: numeric and has default val (1); secondParam: numeric and has default val (2)
        [Php8AnnotationTestController::class, 'test2Action']),


    new Route('php7AnnotTest', '/annot-test-php7/{firstParam<\d+>?1}/{secondParam<\d+>?2}',//firstParam: numeric and has default val (1); secondParam: numeric and has default val (2)
        [Php7AnnotationTestController::class, 'testAction']),

    new Route('php7AnnotTes2', '/annot-test2-php7/{firstParam<\d+>?1}/{secondParam<\d+>?2}',//firstParam: numeric and has default val (1); secondParam: numeric and has default val (2)
        [Php7AnnotationTestController::class, 'test2Action']),

    new Route('memoizeTest', '/memoize-test', [MemoizeTestController::class, 'firstTestAction']),
    new Route('memoizeTest2', '/memoize-test-2', [MemoizeTestController::class, 'secondTestAction']),

    new Route('restTest', '/rest-test', [RESTTestController::class, 'testAction'], \Sample\Controllers\RESTServices\RESTModule::class),

    new Route('eventTest', '/event-test', [\Sample\Controllers\Miscellaneous\EventTestController::class, 'testAction']),
];