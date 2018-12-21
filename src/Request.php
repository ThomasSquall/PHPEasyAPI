<?php

namespace PHPEasyAPI;

/**
 * Class Request.
 * @package PHPEasyAPI
 */
class Request implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @var array $args
     */
    private $args = [];

    /**
     * @var string
     */
    private $url;

    /**
     * Request constructor.
     * @param string $url
     * @param array $args
     */
    public function __construct($url, $args = [])
    {
        $this->url = $url;
        $this->args = $args;
    }

    /**
     * Sends a response to the client.
     * @param $code
     * @param string $result
     */
    public function send($code, $result = '')
    {
        $text = "HTTP/1.1 $code ";

        switch ($code)
        {
            case 100: $text .= 'Continue'; break;
            case 101: $text .= 'Switching Protocols'; break;
            case 200: $text .= 'OK'; break;
            case 201: $text .= 'Created'; break;
            case 202: $text .= 'Accepted'; break;
            case 203: $text .= 'Non-Authoritative Information'; break;
            case 204: $text .= 'No Content'; break;
            case 205: $text .= 'Reset Content'; break;
            case 206: $text .= 'Partial Content'; break;
            case 300: $text .= 'Multiple Choices'; break;
            case 301: $text .= 'Moved Permanently'; break;
            case 302: $text .= 'Moved Temporarily'; break;
            case 303: $text .= 'See Other'; break;
            case 304: $text .= 'Not Modified'; break;
            case 305: $text .= 'Use Proxy'; break;
            case 400: $text .= 'Bad Request'; break;
            case 401: $text .= 'Unauthorized'; break;
            case 402: $text .= 'Payment Required'; break;
            case 403: $text .= 'Forbidden'; break;
            case 404: $text .= 'Not Found'; break;
            case 405: $text .= 'Method Not Allowed'; break;
            case 406: $text .= 'Not Acceptable'; break;
            case 407: $text .= 'Proxy Authentication Required'; break;
            case 408: $text .= 'Request Time-out'; break;
            case 409: $text .= 'Conflict'; break;
            case 410: $text .= 'Gone'; break;
            case 411: $text .= 'Length Required'; break;
            case 412: $text .= 'Precondition Failed'; break;
            case 413: $text .= 'Request Entity Too Large'; break;
            case 414: $text .= 'Request-URI Too Large'; break;
            case 415: $text .= 'Unsupported Media Type'; break;
            case 500: $text .= 'Internal Server Error'; break;
            case 501: $text .= 'Not Implemented'; break;
            case 502: $text .= 'Bad Gateway'; break;
            case 503: $text .= 'Service Unavailable'; break;
            case 504: $text .= 'Gateway Time-out'; break;
            case 505: $text .= 'HTTP Version not supported'; break;
        }

        header($text);

        foreach (headers_list() as $header)
        {
            if (stripos($header,'Content-Type') !== FALSE)
            {
                $headerParts = explode(':', $header);
                $headerValue = trim($headerParts[1]);

                if ($headerValue === "application/json" && (is_array($result) || is_object($result)))
                    $result = json_encode($result);
            }
        }

        exit($result);
    }

    /**
     * Sends the 200 response to the client.
     * @param string $result
     */
    public function send200($result = '') { $this->send(200, $result); }

    /**
     * Sends the 404 response to the client.
     * @param string $result
     */
    public function send404($result = '') { $this->send(404, $result); }

    /**
     * Sends the 500 response to the client.
     * @param string $result
     */
    public function send500($result = '') { $this->send(500, $result); }

    /**
     * Returns the requested url.
     * @return string
     */
    public function getUrl() { return $this->url; }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() { return new \ArrayIterator($this->args); }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) { return isset($this->args[$offset]); }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) { return isset($this->args[$offset]) ? $this->args[$offset] : null; }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) $this->args[] = $value;
        else $this->args[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) { unset($this->args[$offset]); }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count() { return count($this->args); }
}