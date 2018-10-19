<?php

namespace PHPEasyAPI;

use PHPAnnotations\Annotations\Annotation;

/**
 * Class ServerAnnotation.
 * @package PHPEasyAPI
 */
class ServerAnnotation extends Annotation
{
    protected $endpoint;

    public function __construct($endpoint = "")
    {
        $this->endpoint = $endpoint;
    }
}