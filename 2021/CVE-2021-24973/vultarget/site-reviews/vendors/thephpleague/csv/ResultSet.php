<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GeminiLabs\League\Csv;

use CallbackFilterIterator;
use Iterator;
use JsonSerializable;
use LimitIterator;
use function array_flip;
use function array_search;
use function is_string;
use function iterator_count;
use function iterator_to_array;
use function sprintf;

/**
 * Represents the result set of a {@link Reader} processed by a {@link Statement}.
 */
class ResultSet implements TabularDataReader, JsonSerializable
{
    /**
     * The CSV records collection.
     *
     * @var Iterator
     */
    protected $records;

    /**
     * The CSV records collection header.
     *
     * @var array
     */
    protected $header = [];

    /**
     * New instance.
     */
    public function __construct(Iterator $records, array $header)
    {
        $this->validateHeader($header);

        $this->records = $records;
        $this->header = $header;
    }

    /**
     * @return void
     * @throws SyntaxError if the header syntax is invalid
     */
    protected function validateHeader(array $header)
    {
        if ($header !== array_unique(array_filter($header, 'is_string'))) {
            throw new SyntaxError('The header record must be an empty or a flat array with unique string values.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        unset($this->records);
    }

    /**
     * Returns a new instance from a League\Csv\Reader object.
     * 
     * @return static
     */
    public static function createFromTabularDataReader(TabularDataReader $reader)
    {
        return new self($reader->getRecords(), $reader->getHeader());
    }

    /**
     * Returns the header associated with the result set.
     *
     * @return string[]
     */
    public function getHeader()
    {
        return $this->header;
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
    public function getRecords(array $header = [])
    {
        $this->validateHeader($header);
        $records = $this->combineHeader($header);
        foreach ($records as $offset => $value) {
            yield $offset => $value;
        }
    }

    /**
     * Combine the header to each record if present.
     * 
     * @return Iterator
     */
    protected function combineHeader(array $header)
    {
        if ($header === $this->header || [] === $header) {
            return $this->records;
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

        return new MapIterator($this->records, $mapper);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return iterator_count($this->records);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return iterator_to_array($this->records, false);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchOne($nth_record = 0)
    {
        if ($nth_record < 0) {
            throw new InvalidArgument(sprintf('%s() expects the submitted offset to be a positive integer or 0, %s given', __METHOD__, $nth_record));
        }

        $iterator = new LimitIterator($this->records, $nth_record, 1);
        $iterator->rewind();

        return (array) $iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($index = 0)
    {
        $offset = $this->getColumnIndex($index, __METHOD__.'() expects the column index to be a valid string or integer, `%s` given');
        $filter = static function (array $record) use ($offset) {
            return isset($record[$offset]);
        };

        $select = static function (array $record) use ($offset) {
            return $record[$offset];
        };

        $iterator = new MapIterator(new CallbackFilterIterator($this->records, $filter), $select);
        foreach ($iterator as $tKey => $tValue) {
            yield $tKey => $tValue;
        }
    }

    /**
     * Filter a column name against the header if any.
     *
     * @param string|int $field         the field name or the field index
     * @param string     $error_message the associated error message
     *
     * @return string|int
     */
    protected function getColumnIndex($field, $error_message)
    {
        if (is_string($field)) {
            return $this->getColumnIndexByValue($field, $error_message);
        }

        return $this->getColumnIndexByKey($field, $error_message);
    }

    /**
     * Returns the selected column name.
     *
     * @param string $value
     * @param string $error_message
     * @return string
     * @throws Exception if the column is not found
     */
    protected function getColumnIndexByValue($value, $error_message)
    {
        if (false !== array_search($value, $this->header, true)) {
            return $value;
        }

        throw new InvalidArgument(sprintf($error_message, $value));
    }

    /**
     * Returns the selected column name according to its offset.
     *
     * @param int $index
     * @param string $error_message
     * @return int|string
     * @throws Exception if the field is invalid or not found
     */
    protected function getColumnIndexByKey($index, $error_message)
    {
        if ($index < 0) {
            throw new InvalidArgument($error_message);
        }

        if ([] === $this->header) {
            return $index;
        }

        $value = array_search($index, array_flip($this->header), true);
        if (false !== $value) {
            return $value;
        }

        throw new InvalidArgument(sprintf($error_message, $index));
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPairs($offset_index = 0, $value_index = 1)
    {
        $offset = $this->getColumnIndex($offset_index, __METHOD__.'() expects the offset index value to be a valid string or integer, `%s` given');
        $value = $this->getColumnIndex($value_index, __METHOD__.'() expects the value index value to be a valid string or integer, `%s` given');

        $filter = static function (array $record) use ($offset) {
            return isset($record[$offset]);
        };

        $select = static function (array $record) use ($offset, $value) {
            return [$record[$offset], isset($record[$value]) ? $record[$value] : null];
        };

        $iterator = new MapIterator(new CallbackFilterIterator($this->records, $filter), $select);
        foreach ($iterator as $pair) {
            yield $pair[0] => $pair[1];
        }
    }
}
