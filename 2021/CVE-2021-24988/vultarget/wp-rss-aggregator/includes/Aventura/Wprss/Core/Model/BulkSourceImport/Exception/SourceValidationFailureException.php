<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport\Exception;

/**
 * An exception that occurs if a feed source representation is invalid.
 *
 * @since 4.11
 */
class SourceValidationFailureException extends AbstractSourceValidationFailureException implements SourceValidationFailureExceptionInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.11
     *
     * @param string $message {@inheritdoc}
     * @param int $code {@inheritdoc}
     * @param \Exception $previous {@inheritdoc}
     * @param array|\Traversable $validationErrors The list of validation errors.
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null, $validationErrors = array())
    {
        parent::__construct($message, $code, $previous);

        $this->_setValidationErrors($validationErrors);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getValidationErrors()
    {
        return $this->_getValidationErrors();
    }
}
