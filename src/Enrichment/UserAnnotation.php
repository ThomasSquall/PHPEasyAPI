<?php

namespace PHPEasyAPI\Enrichment;

use PHPAnnotations\Annotations\Annotation;

/**
 * Use this class to set API user and password.
 * Class UserAnnotation.
 * @package PHPEasyAPI\Enrichment
 */
class UserAnnotation extends Annotation
{
    protected $username;
    protected $password;

    /**
     * APIUserAnnotation constructor.
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * __get magic method used to retrieve the name.
     * @param $param
     * @return null
     */
    public function __get($param)
    {
        $result = null;

        if (property_exists($this, $param)) $result = $this->$param;

        return $result;
    }
}