<?php


namespace Sample\Controllers\Miscellaneous;


/**
 * Memoized callables will be cached only if a cache is configured.
 * Otherwise, it will work ordinarily.
 * Call results will be cached with parameters. Thus, if parameters change, a new cache will be created
 *
 * @package Sample\Controllers\Miscellaneous
 */
class MemoizeTestController extends \Sample\Controllers\BaseController
{
    public function firstTestAction()
    {

        $test = new Test();

        $sum = $this->memoizedCall([$test, 'sum'], [1,2]);

        $this->view->sum = $sum;
    }

    public function secondTestAction()
    {

        $test = new Test();

        $sum = $this->memoizedCall([Test::class, 'sum'], [1,5]);

        $this->view->sum = $sum;
    }

}