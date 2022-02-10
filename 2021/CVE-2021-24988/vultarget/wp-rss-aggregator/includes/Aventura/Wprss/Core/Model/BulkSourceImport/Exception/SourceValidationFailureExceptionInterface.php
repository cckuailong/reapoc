<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport\Exception;

/**
 * Something that can represent an exception that occurs if a feed source is invalid.
 *
 * @since 4.11
 */
interface SourceValidationFailureExceptionInterface extends ImportExceptionInterface
{
    /**
     * Retrieves a list of problems with a feed source.
     *
     * @since 4.11
     *
     * @return array|\Traversable
     */
    public function getValidationErrors();
}