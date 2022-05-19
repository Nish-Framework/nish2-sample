<?php
namespace Sample\Controllers\AnnotationTests;



use Nish\Annotations\OnAfterAction;
use Nish\Http\Response;
use Sample\Middlewares\PHP7Annotation1;
use Sample\Middlewares\PHP7Annotation2;
use Sample\Middlewares\PHP7Annotation3;

/**
 * Class Php7AnnotationTestController
 *
 * @\Sample\Middlewares\PHP7Annotation1(1, {"p1"="v1", "p2"="v2"}, "some other param") //will run before onJustBeforeAllActions method
 * @\Sample\Middlewares\PHP7Annotation2 //will run before onJustBeforeAllActions method
 * @\Nish\Annotations\OnAfterAction( {{\Sample\Middlewares\PHP7Annotation3::class, "thisWillRunAfterAll", {"some optional params"}}} ) //will run after all
 *
 * @package Sample\Controllers\AnnotationTests
 */
class Php7AnnotationTestController extends \Sample\Controllers\BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->disableView(); //will disable views for all actions
    }

    /**
     * @override
     *
     * This method will run immediately before action call and after other middleware.
     *
     * @param mixed ...$args
     */
    public function onJustBeforeAllActions(...$args)
    {
        echo '<br><br>:::::: Php7AnnotationTestController::onJustBeforeAllActions :::::: <br>';

        Response::sendResponse(json_encode(['onJustBeforeAllActions' => 'run before all', 'args' => $args]).'<br>');

        return $args;
    }

    /**
     * @override
     *
     * This method will run immediately after action call and before other middlewares added to OnAfterAction.
     *
     * @param mixed ...$args
     * @return array
     */
    public function onJustAfterAllActions(...$args)
    {
        echo '<br><br>:::::: Php7AnnotationTestController::onJustAfterAllActions :::::: <br>';

        Response::sendResponse(json_encode(['onJustBeforeAllActions' => 'run after all', 'args' => $args]).'<br>');

        return $args;
    }

    public function testAction(...$args)
    {

        echo '<br><br>:::::: Php7AnnotationTestController::testAction :::::: <br>';

        Response::sendResponse(json_encode([
            'UrlArgs' => $this->request->getUrlArgs(), //You can get URL args whenever you want from request

            'params_before_middlewares' => $args[0],
            'params_after_middlewares' => $args[1],
        ]).'<br>');

        return $args;
    }

    /**
     * @\Sample\Middlewares\PHP7Annotation1 //will be called second time
     * @\Sample\Middlewares\PHP7Annotation2 //will be called second time
     * @\Nish\Annotations\OnAfterAction({{\Sample\Middlewares\PHP7Annotation3::class, "thisWillRunAfterAll"}}) //will be called second time
     *
     * @param mixed ...$args
     * @return array
     */
    public function test2Action(...$args)
    {

        echo '<br><br>:::::: Php7AnnotationTestController::test2Action :::::: <br>';

        Response::sendResponse(json_encode([
                'UrlArgs' => $this->request->getUrlArgs(), //You can get URL args whenever you want from request

                'params_before_middlewares' => $args[0],
                'params_after_middlewares' => $args[1],
            ]).'<br>');

        return $args;
    }
}