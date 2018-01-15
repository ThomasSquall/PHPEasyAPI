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

    private static $availableMethods =
    [
        'GET', 'get', 'Get',
        'POST', 'post', 'Post',
        'PUT', 'put', 'Put',
        'PATCH', 'patch', 'Patch',
        'DELETE', 'delete', 'Delete'
    ];

    /**
     * EndpointAnnotation constructor.
     * @param string $method
     * @param string $url
     * @throws \Exception
     */
    public function __construct($method, $url)
    {
        if (!in_array($method, self::$availableMethods))
            throw new \Exception("No method $method available!");

        $this->method = $method;
        $this->url = $url;
    }
}