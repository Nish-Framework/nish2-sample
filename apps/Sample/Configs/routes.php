<?php

use Nish\Routes\Route;
use Sample\Controllers\AnnotationTests\Php8AnnotationTestController;
use Sample\Controllers\Index\FormsController;
use Sample\Controllers\Index\IndexController;
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
];