<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport\Exception;

/**
 * Common functionality for exeptions that occur if a source representation is invalid.
 *
 * @since 4.11
 */
class AbstractSourceValidationFailureException extends AbstractImportException
{
    /**
     * @since 4.11
     * @var array|\Traversable
     */
    protected $validationErrors;

    /**
     * Retrieve the list of validation errors that this instance represents.
     *
     * @since 4.11
     *
     * @return array|\Traversable The error list.
     */
    protected function _getValidationErrors()
    {
        if (!$this->_isValidList($this->validationErrors)) {
            $this->validationErrors = array();
        }

        return $this->validationErrors;
    }

    /**
     * Sets the list of validation errors that this intance should represent.
     *
     * @since 4.11
     *
     * @param array|\Traversable $errorList The list of errors.
     * @return AbstractSourceValidationFailureException This instance.
     */
    protected function _setValidationErrors($errorList)
    {
        $this->_assertList($errorList);
        $this->validationErrors = $errorList;

        return $this;
    }

    /**
     * Throws an exception if the given list is invalid.
     *
     * @since 4.11
     *
     * @param \Traversable $list
     * @return AbstractSourceValidationFailureException This instance.
     * @throws ImportException If list is invalid.
     */
    protected function _assertList($list)
    {
        if (!$this->_isValidList($list)) {
            throw new ImportException('The list is invalid');
        }

        return $this;
    }

    /**
     * Determines if the given list is valid.
     *
     * @since 4.11
     *
     * @param array|\Traversable $list The list to validate.
     * @return boolean True if the given list is valid; false otherwise.
     */
    protected function _isValidList($list)
    {
        if (!is_array($list) && !($list instanceof \Traversable)) {
            return false;
        }

        return true;
    }
}