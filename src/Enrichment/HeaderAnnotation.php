<?php

namespace PHPEasyAPI\Enrichment;

use PHPAnnotations\Annotations\Annotation;

/**
 * Class HeaderAnnotation.
 * @package PHPEasyAPI\Enrichment
 */
class HeaderAnnotation extends Annotation
{
    protected $header;
    protected $value;

    /**
     * HeaderAnnotation constructor.
     * @param string $header
     * @param string $value
     */
    public function __construct($header, $value)
    {
        $this->header = $header;
        $this->value = $value;
    }
}