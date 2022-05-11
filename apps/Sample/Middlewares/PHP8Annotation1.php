<?php


namespace Sample\Middlewares;

use Nish\Annotations\IAnnotation;

#[\Attribute]
class PHP8Annotation1 implements IAnnotation
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
                $args[0][$i] = ++$v;
            }
        }


        return $args;
    }


}