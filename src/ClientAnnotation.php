<?php

namespace PHPEasyAPI;

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

        foreach ($options->headers as $key => $value)
        {
            $curl->setHeader($key, $value);
        }

        if (method_exists($curl, $method))
        {
            if ($options->json && (is_array($options->data) || is_object($options->data)))
            {
                $options->data = (array)$options->data;
                $options->data = json_encode($options->data);
            }
            else $options->data = (array)$options->data;

            $curl->$method($url, $options->data);
            $result = $curl->response;

            foreach ($curl->response_headers as $header)
            {
                if (strpos($header, 'Content-Type') !== false &&
                    strpos($header, 'application/json') !== false)
                {
                    $result = json_decode($result, true);
                    break;
                }
            }
        }

        return $result;
    }
}