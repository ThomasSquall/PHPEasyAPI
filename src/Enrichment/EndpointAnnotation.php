<?php

namespace PHPEasyAPI\Enrichment;

use PHPAnnotations\Annotations\Annotation;

/**
 * Class EndpointAnnotation
 * @package PHPEasyAPI\Enrichment
 */
class EndpointAnnotation extends Annotation
{
    protected $method;
    protected $url;

    /**
     * EndpointAnnotation constructor.
     * @param string $method
     * @param string $url
     */
    public function __construct($method, $url)
    {
        $this->method = $method;
        $this->url = $url;
    }
}