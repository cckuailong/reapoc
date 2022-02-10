<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport;

use Aventura\Wprss\Core\Model\AbstractTranslatingModel;

/**
 * Common functionality for feed source importers.
 *
 * @since 4.11
 */
abstract class AbstractImporter extends AbstractTranslatingModel
{
    /**
     * Transforms import input into feed source data.
     *
     * @since 4.11
     *
     * @return array[] A list of data maps.
     */
    abstract protected function _inputToSourcesList($input);

    /**
     * Imports feed sources from a list.
     *
     * @since 4.11
     *
     * @param array|\Traversable $list
     *
     * @return array|\Traversable A list of results.
     * For each item in order, the value will be either an integer representing the created resource ID,
     * or an instance of {@see \Exception} if something went wrong.
     */
    protected function _importFromSourcesList($list)
    {
        $results = array();
        foreach ($list as $_idx => $_source) {
            try {
                $results[$_idx] = $this->_importSource($_source);
            } catch (\Exception $e) {
                $results[$_idx] = new Exception\ImportException($this->__(array('Could not import item #%1$s', $_idx)), 0, $e);
                continue;
            }
        }

        return $results;
    }

    /**
     * Imports a feed source.
     *
     * @since 4.11
     *
     * @param array|\ArrayAccess $source The source to import.
     * @return int The ID of the imported resource. See {@see _createLocalResource()}.
     */
    protected function _importSource($source)
    {
        $this->_assertSourceValid($source);
        $data = $this->_prepareInsertionData($source);
        $id = $this->_createLocalResource($data);

        return $id;
    }

    /**
     * Prepares data using a feed source representation, ready to be converted into a local resource.
     *
     * @since 4.11
     *
     * @param array|\ArrayAccess $source The feed source representation.
     * @return array Data ready for insertion that can represent a feed source.
     */
    protected function _prepareInsertionData($source)
    {
        return $this->_getPostDataDefaults($source);
    }

    /**
     * Creates a local representation of a feed source locally.
     *
     * @since [*next-version]
     *
     * @param mixed $data The data ready to be converted to a local resoure.
     *
     * @return int The ID of the new resource.
     */
    abstract protected function _createLocalResource($data);

    /**
     * Merges two arrays recursively.
     *
     * @since 4.11
     *
     * @param array $default
     * @param array $additional
     * @return array The product of two arrays.
     */
    protected function _mergeArrays($default, $additional = array())
    {
        return \array_merge_recursive_distinct($default, $additional);
    }

    /**
     * Determines whether a source representation is valid for import.
     *
     * If resulting list is empty, the source is valid.
     *
     * @since 4.11
     *
     * @param array|\ArrayAccess $source The feed source representation.
     *
     * @return string[]|\Exception[]|\Traversable A list of validation errors.
     */
    protected function _validateSource($source)
    {
        $errors = array();
        if (\count($source) < 2) {
            $errors[] = $this->__('A source must have at least two data members');
        }

        if (!isset($source[ImporterInterface::SK_TITLE])) {
            $errors[] = $this->__('Source title is missing');
        }

        if (isset($source[ImporterInterface::SK_TITLE]) && !strlen($source[ImporterInterface::SK_TITLE])) {
            $errors[] = $this->__('Source title must not be empty');
        }

        if (!isset($source[ImporterInterface::SK_URL])) {
            $errors[] = $this->__('Source URL is missing');
        }

        if (isset($source[ImporterInterface::SK_URL]) && !strlen($source[ImporterInterface::SK_URL])) {
            $errors[] = $this->__('Source URL must not be empty');
        }

        return $errors;
    }

    /**
     * Throws an exception if specified source is not valid.
     *
     * @since 4.11
     *
     * @param array|\ArrayAccess $source The source that must be valid.
     * @return AbstractImporter This instance.
     * @throws Exception\SourceValidationFailureExceptionInterface If feed source is not valid.
     */
    protected function _assertSourceValid($source)
    {
        if (\count($errors = $this->_validateSource($source))) {
            throw new Exception\SourceValidationFailureException($this->__('Feed source representation is invalid'), 0, null, $errors);
        }

        return $this;
    }
}