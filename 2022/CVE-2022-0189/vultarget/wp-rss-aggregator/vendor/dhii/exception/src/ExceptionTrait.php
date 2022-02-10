<?php

namespace Dhii\Exception;

use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\I18n\StringTranslatingTrait;

/**
 * Groups functionality for exceptions.
 */
trait ExceptionTrait
{
    /* Adds ability to initialize the exception base.
     *
     * @since [*next-version*]
     */
    use InitBaseExceptionCapableTrait;

    /* Adds ability to normalize integers.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /* Adds ability to normalize strings.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /* Adds an invalid argument exception factory.
     *
     * @since [*next-version*]
     */
    use CreateNativeInvalidArgumentExceptionCapableTrait;

    /* Adds ability to translate strings.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;
}
