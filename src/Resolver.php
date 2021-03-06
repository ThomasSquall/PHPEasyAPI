<?php

namespace PHPEasyAPI;

use PHPAnnotations\Reflection\Reflector;

/**
 * Class Resolver.
 * @package PHPEasyAPI
 */
class Resolver
{
    /**
     * @var Reflector[] $listeners
     */
    private $listeners = [];

    /**
     * @var string $baseUrl
     */
    private $baseUrl = '';

    /**
     * Resolver constructor.
     * @param string $baseUrl
     */
    public function __construct($baseUrl = '') { $this->setBaseUrl($baseUrl); }

    /**
     * Sets the base url used to handle requests.
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl = '')
    {
        if (substr($baseUrl, strlen($baseUrl) - 1, 1) !== '/') $baseUrl .= '/';
        $this->baseUrl = $baseUrl;
    }

    /**
     * Makes a Request to an external API.
     * @param object $apiClient
     * @param string $endpoint
     * @param array $data
     * @throws \Exception
     */
    public function makeRequest($apiClient, $endpoint, $data = [])
    {
        $reflector = new Reflector($apiClient);

        $client = $reflector->getClass()->getAnnotation('\PHPEasyAPI\Client');

        if (is_null($client)) throw new \Exception('No \PHPEasyAPI\Client annotation found in class ' . get_class($apiClient));

        $property = $endpoint;
        $reflector = new Reflector($client->obj);
        $reflectedProp = $reflector->getProperty($property);
        $endpoint = $reflectedProp->getAnnotation('\PHPEasyAPI\Enrichment\Endpoint');

        if (!is_null($endpoint))
        {
            $options = new Options();
            $options->data = $data;
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

    /**
     * Binds a server listener to an endpoint.
     * @param object $apiServer
     * @throws \Exception
     */
    public function bindListener($apiServer)
    {
        $reflector = new Reflector($apiServer);
        $server = $reflector->getClass()->getAnnotation('\PHPEasyAPI\Server');

        $endpoint = $server->endpoint;

        if (is_null($server)) throw new \Exception('No \PHPEasyAPI\Server annotation found in class ' . get_class($apiServer));

        if (substr($endpoint, 0, 1) === '/') $endpoint = substr($endpoint, 1, strlen($endpoint) - 1);
        if (strpos($endpoint, '/') !== false) throw new \Exception("String $endpoint is not accepted while binding listener!");

        $this->listeners[$endpoint] = $reflector;
    }

    /**
     * Resolve an url and returns the API call result.
     *
     * @param callback $controlCallback     If not null will be called before resolving the url.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function resolve($controlCallback = null)
    {
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (substr($url, 0, 1) === '/') $url = substr($url, 1, strlen($url) - 1);
        if ($this->baseUrl === '') throw new \Exception('Please set a base url before handling API requests');

        if (strpos($url, $this->baseUrl) !== false) $endpoint = explode($this->baseUrl, $url)[1];
        else $endpoint = $url;

        $request = explode('/', $endpoint);

        if (strpos($request[0], '?') !== false)
        {
            $request = explode('?', $request[0]);
            $request[1] = '?' . $request[1];
        }

        $endpoint = $request[0];

        if (!is_null($controlCallback) && is_callable($controlCallback)) $controlCallback($endpoint, $request);

        if (!isset($this->listeners[$endpoint])) $this->notFoundResponse();

        unset($request[0]);
        list($method, $args) = $this->findHandlerMethod($request, $endpoint);

        if ($method === '') $this->notFoundResponse();

        $methodAnnotation = $this->listeners[$endpoint]->getMethod($method);

        if ($methodAnnotation->hasAnnotation('\PHPEasyAPI\Enrichment\JSON'))
        {
            header('Content-Type: application/json');
        }

        $server = $this->listeners[$endpoint]->getClass()->getAnnotation('\PHPEasyAPI\Server');
        $method = new \ReflectionMethod(get_class($server->obj), $method);
        $method->invokeArgs($server->obj, [new Request($url, $args)]);
    }

    private function findHandlerMethod($request, $endpoint)
    {
        $result = '';
        $args = [];

        $request = array_values($request);
        $requestCount = count($request);

        if ($requestCount == 0 ||
            (strpos($request[0], '?') !== false && substr($request[0], 0, 1) === '?'))
        {
            $request = ["index"];
            $requestCount = 1;
        }

        if ($request[$requestCount - 1] === "") $request[$requestCount - 1] = "index";
        if (strpos($request[$requestCount - 1], '?') !== false)
            $request[$requestCount - 1] = explode('?', $request[$requestCount - 1])[0];

        $reflector = $this->listeners[$endpoint];
        $methods = $reflector->getMethods();

        foreach ($methods as $name => $method)
        {
            /** @var \PHPAnnotations\Reflection\ReflectionMethod $method */
            $endpoint = $method->getAnnotation('\PHPEasyAPI\Enrichment\Endpoint');

            if (is_null($endpoint)) continue;
            if (strtolower($endpoint->method) !== strtolower($_SERVER['REQUEST_METHOD'])) continue;

            $url = $endpoint->url;

            if (substr($url, 0, 1) === '/') $url = substr($url, 1, strlen($url) - 1);
            if ($url === "") $url = "index";

            $url = explode('/', $url);

            if (count($url) != count($request)) continue;

            foreach ($url as $key => $value)
            {
                if (substr($value, 0, 1) === ':')
                {
                    $args[substr($value, 1, strlen($value) - 1)] =
                        isset($request[$key]) ? $request[$key] : null;

                    continue;
                }

                if (!isset($request[$key]) || $value !== $request[$key])
                {
                    $result = '';
                    $args = [];

                    break;
                }

                $result = $name;
            }

            if ($result !== "") break;
        }

        return [$result, $args];
    }

    private function notFoundResponse()
    {
        header('HTTP/1.1 404 Not Found', true, 404);
        exit;
    }

    private function setUser($user, &$options)
    {
        if (!is_null($user))
        {
            $options->username = $user->username;
            $options->password = $user->password;
        }
    }

    private function setJSON($json, Options &$options)
    {
        if (!is_null($json))
        {
            $options->headers['Content-type'] = 'application/json';
            $options->json = true;
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