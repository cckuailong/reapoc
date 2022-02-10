<?php

namespace MercadoPago\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class RestMethod extends Annotation
{
    /**
     * @var
     */
    public $resource;

    /**
     * @var
     */
    public $method;
    public $idempotency;
}