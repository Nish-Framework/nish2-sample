<?php


namespace Sample\Middlewares;

use Nish\Annotations\IAnnotation;


/**
 * @Annotation
 *
 * Class PHP7Annotation3
 * @package Sample\Middlewares
 */
class PHP7Annotation3
{

    private $params;

    public function __construct(...$params)
    {
        $this->params = $params;
    }

    public function thisWillRunAfterAll(...$args)
    {
        //You can get URL Args ==> Request::getFromGlobals()->getUrlArgs();

        if (is_array($args[0]) && is_array($args[0][0])) {
            foreach ($args[0][0] as $i => $v) {
                $args[0][0][$i] = $v+1;
            }
        }
        echo '<br><br>:::::: PHP7Annotation3::thisWillRunAfterAll :::::: <br>';
        print_r($args);
        return $args;
    }
}