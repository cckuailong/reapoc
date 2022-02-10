<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @package thephpleague/csv v9.6.1 (modified for PHP 5.6 compatibility)
 */

namespace GeminiLabs\League\Csv;

use CallbackFilterIterator;
use Iterator;
use JsonSerializable;
use GeminiLabs\League\Csv\Polyfill\EmptyEscapeParser;
use SplFileObject;
use function array_combine;
use function array_filter;
use function array_pad;
use function array_slice;
use function array_unique;
use function count;
use function is_array;
use function iterator_count;
use function iterator_to_array;
use function mb_strlen;
use function mb_substr;
use function sprintf;
use function strlen;
use function substr;
use const PHP_VERSION_ID;
use const STREAM_FILTER_READ;

/**
 * A class to parse and read records from a CSV document.
 */
class Reader extends AbstractCsv implements TabularDataReader, JsonSerializable
{
    /**
     * header offset.
     *
     * @var int|null
     */
    protected $header_offset;

    /**
     * header record.
     *
     * @var string[]
     */
    protected $header = [];

    /**
     * records count.
     *
     * @var int
     */
    protected $nb_records = -1;

    /**
     * {@inheritdoc}
     */
    protected $stream_filter_mode = STREAM_FILTER_READ;

    /**
     * @var bool
     */
    protected $is_empty_records_included = false;

    /**
     * {@inheritdoc}
     */
    public static function createFromPath($path, $open_mode = 'r', $context = null)
    {
        return parent::createFromPath($path, $open_mode, $context);
    }

    /**
     * {@inheritdoc}
     */
    protected function resetProperties()
    {
        parent::resetProperties();
        $this->nb_records = -1;
        $this->header = [];
    }

    /**
     * Returns the header offset.
     *
     * If no CSV header offset is set this method MUST return null
     *
     * @return ?int
     */
    public function getHeaderOffset()
    {
        return $this->header_offset;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader()
    {
        if (null === $this->header_offset) {
            return $this->header;
        }

        if ([] !== $this->header) {
            return $this->header;
        }

        $this->header = $this->setHeader($this->header_offset);

        return $this->header;
    }

    /**
     * Determine the CSV record header.
     *
     * @param int $offset
     * @return string[]
     * @throws Exception If the header offset is set and no record is found or is the empty array
     */
    protected function setHeader($offset)
    {
        $header = $this->seekRow($offset);
        if (in_array($header, [[], [null]], true)) {
            throw new SyntaxError(sprintf('The header record does not exist or is empty at offset: `%s`', $offset));
        }

        if (0 !== $offset) {
            return $header;
        }

        $header = $this->removeBOM($header, mb_strlen($this->getInputBOM()), $this->enclosure);
        if ([''] === $header) {
            throw new SyntaxError(sprintf('The header record does not exist or is empty at offset: `%s`', $offset));
        }

        return $header;
    }

    /**
     * Returns the row at a given offset.
     * 
     * @param int $offset
     * @return array
     */
    protected function seekRow($offset)
    {
        foreach ($this->getDocument() as $index => $record) {
            if ($offset === $index) {
                return $record;
            }
        }

        return [];
    }

    /**
     * Returns the document as an Iterator.
     * 
     * @return Iterator
     */
    protected function getDocument()
    {
        if (70400 > PHP_VERSION_ID && '' === $this->escape) {
            $this->document->setCsvControl($this->delimiter, $this->enclosure);

            return EmptyEscapeParser::parse($this->document);
        }

        $this->document->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD);
        $this->document->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        $this->document->rewind();

        return $this->document;
    }

    /**
     * Strip the BOM sequence from a record.
     *
     * @param string[] $record
     * @param int $bom_length
     * @param string $enclosure
     * @return string[]
     */
    protected function removeBOM(array $record, $bom_length, $enclosure)
    {
        if (0 === $bom_length) {
            return $record;
        }

        $record[0] = mb_substr($record[0], $bom_length);
        if ($enclosure.$enclosure != substr($record[0].$record[0], strlen($record[0]) - 1, 2)) {
            return $record;
        }

        $record[0] = substr($record[0], 1, -1);

        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($index = 0)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchColumn($index);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchOne($nth_record = 0)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchOne($nth_record);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPairs($offset_index = 0, $value_index = 1)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchPairs($offset_index, $value_index);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (-1 === $this->nb_records) {
            $this->nb_records = iterator_count($this->getRecords());
        }

        return $this->nb_records;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->getRecords();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return iterator_to_array($this->getRecords(), false);
    }

    /**
     * {@inheritDoc}
     */
    public function getRecords(array $header = [])
    {
        $header = $this->computeHeader($header);
        $normalized = function ($record) {
            return is_array($record) && ($this->is_empty_records_included || $record != [null]);
        };

        $bom = '';
        if (!$this->is_input_bom_included) {
            $bom = $this->getInputBOM();
        }

        $document = $this->getDocument();
        $records = $this->stripBOM(new CallbackFilterIterator($document, $normalized), $bom);
        if (null !== $this->header_offset) {
            $records = new CallbackFilterIterator($records, function (array $record, $offset) {
                return $offset !== $this->header_offset;
            });
        }

        if ($this->is_empty_records_included) {
            $normalized_empty_records = static function (array $record) {
                if ([null] === $record) {
                    return [];
                }

                return $record;
            };

            return $this->combineHeader(new MapIterator($records, $normalized_empty_records), $header);
        }

        return $this->combineHeader($records, $header);
    }

    /**
     * Returns the header to be used for iteration.
     *
     * @param string[] $header
     * @return string[]
     * @throws Exception If the header contains non unique column name
     */
    protected function computeHeader(array $header)
    {
        if ([] === $header) {
            $header = $this->getHeader();
        }

        if ($header === array_unique(array_filter($header, 'is_string'))) {
            return $header;
        }

        throw new SyntaxError('The header record must be an empty or a flat array with unique string values.');
    }

    /**
     * Combine the CSV header to each record if present.
     *
     * @param string[] $header
     * @return Iterator
     */
    protected function combineHeader(Iterator $iterator, array $header)
    {
        if ([] === $header) {
            return $iterator;
        }

        $field_count = count($header);
        $mapper = static function (array $record) use ($header, $field_count) {
            if (count($record) != $field_count) {
                $record = array_slice(array_pad($record, $field_count, null), 0, $field_count);
            }

            /** @var array<string|null> $assocRecord */
            $assocRecord = array_combine($header, $record);

            return $assocRecord;
        };

        return new MapIterator($iterator, $mapper);
    }

    /**
     * Strip the BOM sequence from the returned records if necessary.
     * 
     * @param Iterator $iterator
     * @param string $bom
     * @return Iterator
     */
    protected function stripBOM($iterator, $bom)
    {
        if ('' === $bom) {
            return $iterator;
        }

        $bom_length = mb_strlen($bom);
        $mapper = function (array $record, $index) use ($bom_length) {
            if (0 !== $index) {
                return $record;
            }

            $record = $this->removeBOM($record, $bom_length, $this->enclosure);
            if ([''] === $record) {
                return [null];
            }

            return $record;
        };

        $filter = function (array $record) {
            return $this->is_empty_records_included || $record != [null];
        };

        return new CallbackFilterIterator(new MapIterator($iterator, $mapper), $filter);
    }

    /**
     * Selects the record to be used as the CSV header.
     *
     * Because the header is represented as an array, to be valid
     * a header MUST contain only unique string value.
     *
     * @param int|null $offset the header record offset
     * @throws Exception if the offset is a negative integer
     * @return static
     */
    public function setHeaderOffset($offset)
    {
        if ($offset === $this->header_offset) {
            return $this;
        }

        if (null !== $offset && 0 > $offset) {
            throw new InvalidArgument(__METHOD__.'() expects 1 Argument to be greater or equal to 0');
        }

        $this->header_offset = $offset;
        $this->resetProperties();

        return $this;
    }

    /**
     * Enable skipping empty records.
     * 
     * @return static
     */
    public function skipEmptyRecords()
    {
        if ($this->is_empty_records_included) {
            $this->is_empty_records_included = false;
            $this->nb_records = -1;
        }

        return $this;
    }

    /**
     * Disable skipping empty records.
     * 
     * @return static
     */
    public function includeEmptyRecords()
    {
        if (!$this->is_empty_records_included) {
            $this->is_empty_records_included = true;
            $this->nb_records = -1;
        }

        return $this;
    }

    /**
     * Tells whether empty records are skipped by the instance.
     * 
     * @return bool
     */
    public function isEmptyRecordsIncluded()
    {
        return $this->is_empty_records_included;
    }
}
