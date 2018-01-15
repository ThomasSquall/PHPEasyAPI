<?php

namespace PHPEasyAPI;

use PHPEasyAPI\Enrichment\Options;
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

                if (!is_null($user))
                {
                    $options->username = $user->username;
                    $options->password = $user->password;
                }

                /** @var ClientAnnotation $client */
                $client->makeRequest($property, $endpoint, $options);
            }
        }
    }
}