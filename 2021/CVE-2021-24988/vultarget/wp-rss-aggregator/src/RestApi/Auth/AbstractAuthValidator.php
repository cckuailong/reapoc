<?php

namespace RebelCode\Wpra\Core\RestApi\Auth;

use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Validation\AbstractValidatorBase;

/**
 * Abstract functionality for authorization validators.
 *
 * @since 4.13
 */
abstract class AbstractAuthValidator extends AbstractValidatorBase
{
    /* @since 4.13 */
    use NormalizeIterableCapableTrait;
}
