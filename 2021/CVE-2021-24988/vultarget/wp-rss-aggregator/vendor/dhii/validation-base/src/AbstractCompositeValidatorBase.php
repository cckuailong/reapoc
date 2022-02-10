<?php

namespace Dhii\Validation;

use AppendIterator;
use ArrayIterator;
use Dhii\Iterator\NormalizeIteratorCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Iterator;
use IteratorIterator;
use Traversable;

/**
 * Base functionality for validators.
 * 
 * Currently, allows creation of concrete exceptions.
 *
 * @since [*next-version*]
 */
abstract class AbstractCompositeValidatorBase extends AbstractValidatorBase
{
    /*
     * Adds iterator normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIteratorCapableTrait;

    /* Functionality for composite validation.
     *
     * @since [*next-version*]
     */
    use GetValidationErrorsCapableCompositeTrait;

    /* Awareness of child validators.
     *
     * @since [*next-version*]
     */
    use ChildValidatorsAwareTrait;

    /* Normalization for iterables.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _normalizeErrorList($errorList)
    {
        $listIterator = new AppendIterator();
        foreach ($errorList as $_error) {
            $listIterator->append($this->_normalizeIterator($_error));
        }

        return $listIterator;
    }

    /**
     * Creates an iterator that will iterate over the given array.
     *
     * @param array $array The array to create an iterator for.
     *
     * @since [*next-version*]
     *
     * @return Iterator The iterator that will iterate over the array.
     */
    protected function _createArrayIterator($array)
    {
        return new ArrayIterator($array);
    }

    /**
     * Creates an iterator that will iterate over the given traversable.
     *
     * @param Traversable $traversable The traversable to create an iterator for.
     *
     * @since [*next-version*]
     *
     * @return Iterator The iterator that will iterate over the traversable.
     */
    protected function _createTraversableIterator($traversable)
    {
        return new IteratorIterator($traversable);
    }
}
