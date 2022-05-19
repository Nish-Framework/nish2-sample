<?php


namespace Sample\Middlewares;

use Nish\Annotations\IAnnotation;
use Nish\Http\Request;

/**
 * @Annotation
 *
 * Class PHP7Annotation2
 * @package Sample\Middlewares
 */
class PHP7Annotation2 implements IAnnotation
{
    private $params;

    public function __construct(...$params)
    {
        $this->params = $params;
    }

    public function run(...$args)
    {
        //You can get URL Args ==> Request::getFromGlobals()->getUrlArgs();

        if (is_array($args[0])) {
            foreach ($args[0] as $i => $v) {
                $args[0][$i] = $v*2;
            }
        }
        return $args;
    }
}