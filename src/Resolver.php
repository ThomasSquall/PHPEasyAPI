<?php

namespace PHPEasyAPI;

use PHPAnnotations\Reflection\Reflector;

/**
 * Class Resolver.
 * @package PHPEasyAPI
 */
class Resolver
{
    public function makeRequest($api, $endpoint)
    {
        $reflector = new Reflector($api);

        $client = $reflector->getClass()->getAnnotation('\PHPEasyAPI\Client');

        if (!is_null($client))
        {
            $property = $endpoint;
            $reflector = new Reflector($client->obj);
            $reflectedProp = $reflector->getProperty($property);
            $endpoint = $reflectedProp->getAnnotation('\PHPEasyAPI\Enrichment\Endpoint');

            if (!is_null($endpoint))
            {
                $options = new Options();
                $user = $reflectedProp->getAnnotation('\PHPEasyAPI\Enrichment\User');
                $json = $reflectedProp->getAnnotation('\PHPEasyAPI\Enrichment\JSON');
                $header = $reflectedProp->getAnnotation('\PHPEasyAPI\Enrichment\Header');

                $this->setUser($user, $options);
                $this->setJSON($json, $options);
                $this->setCustomHeader($header, $options);

                /** @var ClientAnnotation $client */
                $client->makeRequest($property, $endpoint, $options);
            }
        }
    }

    private function setUser($user, &$options)
    {
        if (!is_null($user))
        {
            $options->username = $user->username;
            $options->password = $user->password;
        }
    }

    private function setJSON($json, &$options)
    {
        if (!is_null($json))
        {
            $options->headers['Content-type'] = 'application/json';
        }
    }

    private function setCustomHeader($header, &$options)
    {
        if (!is_null($header))
        {
            $options->headers[$header->header] = $header->value;
        }
    }
}