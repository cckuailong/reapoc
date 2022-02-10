<?php

namespace Aventura\Wprss\Core\Component;

use Aventura\Wprss\Core\Plugin\ComponentAbstract;
use Aventura\Wprss\Core\Model\BulkSourceImport\ImporterInterface;

/**
 * A component that is responsible for importing feed sources in bulk.
 *
 * @since 4.11
 */
class BulkSourceImport extends ComponentAbstract
{
    const K_IMPORTED_SOURCES_COUNT = 'imported_sources_count';

    /**
     * @since 4.11
     * @var ImporterInterface
     */
    protected $importer;

    /**
     * @since 4.11
     *
     * @param array $data The data members map.
     * @param ImporterInterface $importer The importer that this instance should use to import sources.
     */
    public function __construct($data, ImporterInterface $importer)
    {
        parent::__construct($data);

        $this->_setImporter($importer);
    }

    /**
     * Resets data about the last import.
     *
     * @since 4.11
     */
    public function resetStats()
    {
        $this->unsetData(static::K_IMPORTED_SOURCES_COUNT);

        return $this;
    }

    /**
     * Imports a text representation of a feed source list.
     *
     * @since 4.11
     *
     * @param string $input The input string containing feed source representations.
     *  {@see \Aventura\Wprss\Core\Model\BulkSourceImport\PlainTextImporter::import()}
     * @return array Array of results. See {@see ImporterInterface::import()}
     */
    public function import($input)
    {
        $this->resetStats();
        $results = $this->_import($input);
        $successCount = $this->_countSuccessfulResults($results);
        $this->_setImportedSourcesCount($successCount);

        return $results;
    }

    /**
     *
     * @param type $input
     * @return array Array of results. See {@see ImporterInterface::import()}
     */
    protected function _import($input)
    {
        $importer = $this->_getImporter();
        $results = $importer->import($input);

        return $results;
    }

    /**
     * Counts successful import results.
     *
     * @since 4.11
     *
     * @param array $results A list of import results.
     *  See {@see ImporterInterface::import()}.
     * @return int The amount of successful results.
     */
    protected function _countSuccessfulResults($results)
    {
        $count = 0;
        foreach ($results as $_idx => $_result) {
            if (is_numeric($_result)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Retrieves errors that occurred during an import.
     *
     * @since 4.11
     *
     * @param array $results A list of import results.
     *  See {@see ImporterInterface::import()}.
     * @return \Exception[] A list of exceptions that occurred during an import.
     */
    protected function _getImportFailures($results)
    {
        $failures = array();
        foreach ($results as $_idx => $_result) {
            if ($_result instanceof \Exception) {
                $failures[$_idx] = $_result;
            }
        }

        return $failures;
    }

    /**
     * Retrieves the amount of feed sources successfully imported during the last import.
     *
     * @since 4.11
     *
     * @return int The amount of sources.
     */
    public function getImportedSourcesCount()
    {
        return $this->_getImportedSourcesCount();
    }

    /**
     * Retrieves the imported sources count data member.
     *
     * @since 4.11
     *
     * @return int The amount of sources.
     */
    protected function _getImportedSourcesCount()
    {
        return (int) $this->_getData(self::K_IMPORTED_SOURCES_COUNT);
    }

    /**
     * Sets the imported sources count data member.
     *
     * @since 4.11
     *
     * @param int $count The new count.
     * @return BulkSourceImport This instance.
     */
    protected function _setImportedSourcesCount($count)
    {
        $this->setData(self::K_IMPORTED_SOURCES_COUNT, (int) $count);

        return $this;
    }

    /**
     * Assigns the importer that this instance should use.
     *
     * @since 4.11
     *
     * @param ImporterInterface $importer The importer.
     * @return BulkSourceImport This instance.
     */
    protected function _setImporter(ImporterInterface $importer)
    {
        $this->importer = $importer;

        return $this;
    }

    /**
     * Retrieves the importer used by this instance.
     *
     * @since 4.11
     *
     * @return ImporterInterface
     */
    protected function _getImporter()
    {
        return $this->importer;
    }
}