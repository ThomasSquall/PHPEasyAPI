<?php

namespace PHPEasyAPI;

use PHPEasyAPI\Enrichment\Options;
use PHPEasyAPI\Enrichment\EndpointAnnotation;
use PHPAnnotations\Annotations\Annotation;

/**
 * Class ClientAnnotation.
 * @package PHPEasyAPI
 */
class ClientAnnotation extends Annotation
{
    /**
     * @param string $property
     * @param EndpointAnnotation $endpoint
     * @param Options|null $options
     */
    public function makeRequest($property, EndpointAnnotation $endpoint, Options $options = null)
    {
        if (is_null($options)) $options = new Options();

        $url = $endpoint->url;
        $method = $endpoint->method;

        $this->obj->$property = $this->cUrl($method, $url, $options);
    }

    private function cUrl($method, $url, Options $options)
    {
        $result = false;
        $method = strtolower($method);

        $curl = new \Curl\Curl();

        if (!is_null($options->username))
        {
            $curl->setBasicAuthentication($options->username, $options->password);
        }

        if (method_exists($curl, $method))
        {
            $curl->$method($url, (array)$options);
            $result = $curl->response;
        }

        return $result;
    }
}