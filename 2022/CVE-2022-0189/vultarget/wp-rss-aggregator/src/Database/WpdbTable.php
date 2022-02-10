<?php

namespace RebelCode\Wpra\Core\Database;

use ArrayAccess;
use ArrayIterator;
use OutOfRangeException;
use RebelCode\Wpra\Core\Data\ArrayDataSet;
use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Util\IteratorDelegateTrait;
use RuntimeException;
use Traversable;
use wpdb;

/**
 * An implementation of a WPDB table.
 *
 * @since 4.13
 */
class WpdbTable implements TableInterface
{
    /* @since 4.13 */
    use IteratorDelegateTrait;

    /**
     * The WordPress database driver.
     *
     * @since 4.13
     *
     * @var wpdb
     */
    protected $wpdb;

    /**
     * The table name.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $name;

    /**
     * The full table name, prefixed with the wpdb prefix.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $fullName;

    /**
     * The schema as a map of column names and their respective declarations.
     *
     * @since 4.13
     *
     * @var string[]|Traversable
     */
    protected $schema;

    /**
     * The name of the column to be used as the primary key.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * The current filters.
     *
     * @since 4.13
     *
     * @var array
     */
    protected $filters;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param wpdb                 $wpdb       The WordPress database driver.
     * @param string               $name       The table name.
     * @param string[]|Traversable $schema     The schema as a map of column names and their respective declarations.
     * @param string               $primaryKey The name of the column to be used as the primary key.
     * @param array                $filters    The current filters.
     */
    public function __construct(wpdb $wpdb, $name, $schema, $primaryKey, $filters = [])
    {
        $this->wpdb = $wpdb;
        $this->name = $name;
        $this->fullName = $wpdb->prefix . $name;
        $this->schema = $schema;
        $this->primaryKey = $primaryKey;
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetGet($key)
    {
        $filters = array_merge($this->filters, [$this->primaryKey => $key]);
        $parse = $this->parseFilters($filters);
        $query = $this->buildSelectQuery($parse);
        $result = $this->wpdb->get_row($query, ARRAY_A);

        if ($result === null) {
            throw new OutOfRangeException(sprintf(__('Cannot find record with key "%s"', 'wprss'), $key));
        }

        return $this->createModel($result);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetExists($key)
    {
        try {
            $this->offsetGet($key);

            return true;
        } catch (OutOfRangeException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetSet($key, $value)
    {
        if ($key === null || !$this->offsetExists($key)) {
            $this->wpdb->insert($this->fullName, $value);

            return;
        }

        $filters = array_merge($this->filters, [$this->primaryKey => $key]);
        $parse = $this->parseFilters($filters);
        $query = $this->buildUpdateQuery($value, $parse);
        $this->wpdb->query($query);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetUnset($key)
    {
        $filters = array_merge($this->filters, [$this->primaryKey => $key]);
        $parse = $this->parseFilters($filters);
        $query = $this->buildDeleteQuery($parse);

        $this->wpdb->query($query);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getCount()
    {
        $parse = $this->parseFilters($this->filters);
        $query = $this->buildCountQuery($parse);
        $result = $this->wpdb->get_row($query, ARRAY_N);

        return (int) $result[0];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function filter($filters)
    {
        $newFilters = array_merge($this->filters, $filters);

        return new static($this->wpdb, $this->name, $this->schema, $this->primaryKey, $newFilters);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function clear()
    {
        $parse = $this->parseFilters($this->filters);
        $query = $this->buildDeleteQuery($parse);

        $this->wpdb->query($query);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function create()
    {
        $schema = [];
        foreach ($this->schema as $col => $decl) {
            if (empty($col) || empty($decl)) {
                continue;
            }

            $schema[] = sprintf('`%s` %s', $col, $decl);
        }

        $schema[] = sprintf('PRIMARY KEY (`%s`)', $this->primaryKey);
        $schemaStr = implode(', ', $schema);
        $charset = $this->wpdb->get_charset_collate();

        $query = sprintf('CREATE TABLE IF NOT EXISTS `%s` ( %s ) %s;', $this->fullName, $schemaStr, $charset);

        $success = $this->wpdb->query($query);

        if (!$success) {
            throw new RuntimeException(
                sprintf(
                    __('Failed to create the "%s" database table. Reason: %s', 'wprss'),
                    $this->fullName,
                    $this->wpdb->last_error
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function drop()
    {
        $this->wpdb->query(sprintf('DROP TABLE IF EXISTS %s', $this->fullName));
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function getIterator()
    {
        $query = $this->buildSelectQuery($this->parseFilters($this->filters));
        $results = $this->wpdb->get_results($query, ARRAY_A);

        return new ArrayIterator($results);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function createIterationValue($value)
    {
        return $this->createModel($value);
    }

    /**
     * Creates a data set model instance for a table record.
     *
     * @since 4.13
     *
     * @param array $data The record data.
     *
     * @return DataSetInterface The created model.
     */
    protected function createModel($data)
    {
        return new ArrayDataSet($data);
    }

    /**
     * Parses a given list of collection filters into query parts.
     *
     * @since 4.13
     *
     * @param array $filters The filters to parse.
     *
     * @return array An array containing the keys: "orderBy", "order", "limit", "offset" and "conditions".
     */
    protected function parseFilters($filters)
    {
        $queryParts = [
            'conditions' => [],
            'orderBy' => '',
            'order' => 'ASC',
            'limit' => null,
            'offset' => 0,
        ];

        foreach ($filters as $filter => $value) {
            $this->handleFilter($filter, $value, $queryParts);
        }

        $queryParts['where'] = implode(' AND ', $queryParts['conditions']);
        unset($queryParts['conditions']);

        return $queryParts;
    }

    /**
     * Processes a single filter and modifies the given query parts.
     *
     * @since 4.13
     *
     * @param string $filter     The filter key.
     * @param mixed  $value      The filter value.
     * @param array  $queryParts The query parts to modify.
     */
    protected function handleFilter($filter, $value, &$queryParts)
    {
        if ($filter === static::FILTER_ORDER_BY) {
            $queryParts['orderBy'] = $value;

            return;
        }

        if ($filter === static::FILTER_ORDER) {
            $queryParts['order'] = $value;

            return;
        }

        if ($filter === static::FILTER_LIMIT) {
            $queryParts['limit'] = $value;

            return;
        }

        if ($filter === static::FILTER_OFFSET) {
            $queryParts['offset'] = $value;

            return;
        }

        if ($filter === static::FILTER_WHERE) {
            $queryParts['conditions'][] = $value;

            return;
        }

        $normValue = $this->normalizeSqlValue($value);
        $condition = sprintf('`%s` = %s', $filter, $normValue);

        $queryParts['conditions'][] = $condition;
    }

    /**
     * Builds a SELECT query from a given set of query parts.
     *
     * @since 4.13
     *
     * @param array $parts The query parts. See {@link parseFilters()}.
     *
     * @return string The built query.
     */
    protected function buildSelectQuery($parts)
    {
        return sprintf(
            'SELECT * FROM %s %s %s %s',
            $this->fullName,
            $this->buildWhere($parts['where']),
            $this->buildOrder($parts['orderBy'], $parts['order']),
            $this->buildLimit($parts['limit'], $parts['offset'])
        );
    }

    /**
     * Builds a SELECT COUNT query from a given set of query parts.
     *
     * @since 4.13
     *
     * @param array $parts The query parts. See {@link parseFilters()}.
     *
     * @return string The built query.
     */
    protected function buildCountQuery($parts)
    {
        return sprintf(
            'SELECT COUNT(`%s`) FROM %s %s %s %s',
            $this->primaryKey,
            $this->fullName,
            $this->buildWhere($parts['where']),
            $this->buildOrder($parts['orderBy'], $parts['order']),
            $this->buildLimit($parts['limit'], $parts['offset'])
        );
    }

    /**
     * Builds a DELETE query from a given set of query parts.
     *
     * @since 4.13
     *
     * @param array $parts The query parts. See {@link parseFilters()}.
     *
     * @return string The built query.
     */
    protected function buildDeleteQuery($parts)
    {
        return sprintf(
            'DELETE FROM %s %s %s %s',
            $this->fullName,
            $this->buildWhere($parts['where']),
            $this->buildOrder($parts['orderBy'], $parts['order']),
            $this->buildLimit($parts['limit'], $parts['offset'])
        );
    }

    /**
     * Builds an INSERT query for a given list of records to be inserted.
     *
     * @since 4.13
     *
     * @param array|ArrayAccess[]|Traversable $records A list of records, each as an assoc. array or data set object.
     *
     * @return string The built query.
     */
    protected function buildInsertQuery($records)
    {
        return sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $this->fullName,
            $this->getColumnNames(),
            $this->buildInsertValues($records)
        );
    }

    /**
     * Builds a DELETE query from a given set of query parts.
     *
     * @since 4.13
     *
     * @param array $parts The query parts. See {@link parseFilters()}.
     *
     * @return string The built query.
     */
    protected function buildUpdateQuery($values, $parts)
    {
        return sprintf(
            'UPDATE %s SET %s %s',
            $this->fullName,
            $this->buildUpdateValues($values),
            $this->buildWhere($parts['where'])
        );
    }

    /**
     * Builds the WHERE clause of an SQL query.
     *
     * @since 4.13
     *
     * @param string $where The WHERE condition.
     *
     * @return string The built WHERE clause.
     */
    protected function buildWhere($where)
    {
        if (empty($where)) {
            return '';
        }

        return sprintf('WHERE %s', $where);
    }

    /**
     * Builds the ORDER clause of an SQL query.
     *
     * @since 4.13
     *
     * @param string $orderBy The field to order by.
     * @param string $order   The ordering mode.
     *
     * @return string The built ORDER clause.
     */
    protected function buildOrder($orderBy, $order)
    {
        if (empty($orderBy)) {
            return '';
        }

        return sprintf('ORDER BY %s %s', $orderBy, $order);
    }

    /**
     * Builds the LIMIT clause of an SQL query.
     *
     * @since 4.13
     *
     * @param int|null $limit  The limit.
     * @param int|null $offset The offset.
     *
     * @return string The build LIMIT clause.
     */
    protected function buildLimit($limit, $offset)
    {
        if (empty($limit)) {
            return '';
        }

        if (empty($offset)) {
            $offset = 0;
        }

        return sprintf('LIMIT %d OFFSET %d', $limit, $offset);
    }

    /**
     * Builds the values of an INSERT query for a given list of records.
     *
     * @since 4.13
     *
     * @param array|ArrayAccess[]|Traversable $records A list of records, each as an assoc. arrays or data set object.
     *
     * @return string The built VALUES portion of an INSERT query, without the "VALUES" keyword.
     */
    protected function buildInsertValues($records)
    {
        $rows = [];
        $cols = $this->getColumnNames();

        foreach ($records as $record) {
            $array = [];
            foreach ($cols as $col) {
                $array[] = isset($record[$col])
                    ? $this->normalizeSqlValue($record[$col])
                    : 'NULL';
            }
            $rows[] = sprintf('(%s)', implode(',', $array));
        }

        return implode(', ', $rows);
    }

    /**
     * Builds the "SET" SQL portion for an UPDATE query, without the "SET" keyword, for a given value map.
     *
     * @since 4.13
     *
     * @param array $values A map of column names to values.
     *
     * @return string The SET portion of an UPDATE query without the "SET" keyword.
     */
    protected function buildUpdateValues($values)
    {
        $setList = [];

        foreach ($values as $col => $val) {
            $setList[] = sprintf('`%s` = %s', $col, $this->normalizeSqlValue($val));
        }

        return implode(', ', $setList);
    }

    /**
     * Normalizes an SQL value.
     *
     * If the value is a string and is not an SQL function, it is quoted.
     * If the value is an array, it is turned into a set: ex. "(1, 2, 3)".
     *
     * @since 4.13
     *
     * @param mixed $value The value to normalize.
     *
     * @return string The normalized value.
     */
    protected function normalizeSqlValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        if (is_array($value)) {
            $normalized = array_map([$this, 'normalizeSqlValue'], $value);
            $imploded = implode(', ', $normalized);

            return sprintf('(%s)', $imploded);
        }

        $isUpper = is_string($value) && strtoupper($value) === $value;
        $openParen = strpos($value, '(');
        $closeParen = strpos($value, ')');
        $hasParens = ($openParen !== false && $closeParen !== false) && $openParen < $closeParen;

        if ($isUpper && $hasParens) {
            return $value;
        }

        return sprintf('"%s"', $value);
    }

    /**
     * Retrieves the column names.
     *
     * @since 4.13
     *
     * @return string[]
     */
    protected function getColumnNames()
    {
        return array_filter(array_keys($this->schema), 'strlen');
    }
}
