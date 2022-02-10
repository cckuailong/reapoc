<?php

namespace Dhii\Validation;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Iterator\CountIterableCapableTrait;
use Dhii\Iterator\ResolveIteratorCapableTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;

/**
 * Common validator dependencies.
 *
 * @since [*next-version*]
 */
trait ValidatorTrait
{
    /* Functionality for validation.
     *
     * @since [*next-version*]
     */
    use ValidateCapableTrait;

    /* Dummy string translation functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /* Functionality for counting of iterables.
     *
     * @since [*next-version*]
     */
    use CountIterableCapableTrait;

    /* Functionality for iterator resolution.
     *
     * @since [*next-version*]
     */
    use ResolveIteratorCapableTrait;

    /* Functionality for integer normalization.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /* Functionality for string normalization.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /* Out of Range exception factory.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /* Invalid Argument exception factory.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* Validation exception factory.
     *
     * @since [*next-version*]
     */
    use CreateValidationExceptionCapableTrait;

    /* Validation Failed exception factory.
     *
     * @since [*next-version*]
     */
    use CreateValidationFailedExceptionCapableTrait;
}
