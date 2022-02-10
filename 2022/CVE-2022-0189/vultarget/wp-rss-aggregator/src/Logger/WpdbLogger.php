<?php

namespace RebelCode\Wpra\Core\Logger;

use Psr\Log\AbstractLogger;
use RebelCode\Wpra\Core\Database\TableInterface;

/**
 * A PSR-3 logger that saves logs in the WordPress database.
 *
 * @since 4.13
 */
class WpdbLogger extends AbstractLogger implements ClearableLoggerInterface, LogReaderInterface
{
    /* @since 4.13 */
    use LoggerUtilsTrait;

    /**
     * The log's ID property and default column name.
     *
     * @since 4.13
     */
    const LOG_ID = 'id';

    /**
     * The log's date property and default column name.
     *
     * @since 4.13
     */
    const LOG_DATE = 'date';

    /**
     * The log's level property and default column name.
     *
     * @since 4.13
     */
    const LOG_LEVEL = 'level';

    /**
     * The log's message property and default column name.
     *
     * @since 4.13
     */
    const LOG_MESSAGE = 'message';

    /**
     * The table where logs are stored.
     *
     * @since 4.13
     *
     * @var TableInterface
     */
    protected $table;

    /**
     * A mapping of log properties to table column names.
     *
     * @since 4.13
     *
     * @see   WpdbLogger::LOG_DATE
     * @see   WpdbLogger::LOG_LEVEL
     * @see   WpdbLogger::LOG_MESSAGE
     *
     * @var string[]
     */
    protected $columns;

    /**
     * Optional mapping of additional columns and fixed data to insert into a row.
     *
     * @since 4.13
     *
     * @var string[]
     */
    protected $extra;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param TableInterface $table   The table where logs are stored.
     * @param array          $columns A mapping of log data keys to column names. The `COL_*` constants may be used to
     *                                map standard log properties and any non-standard properties may also be included
     *                                to insert fixed values with the $extra parameter.
     * @param array          $extra   A mapping of log properties and the values to insert for them. The values may be
     *                                functions, which will be invoked during insertion. The function will receive the
     *                                level, message and this $extra argument array as arguments.
     *                                Be aware that the standard log properties, "id", "level", "message" and "date",
     *                                will be overridden if those keys are given in this array.All non-standard data
     *                                is stored in the table as VARCHAR with a limit of 100 characters.
     */
    public function __construct(TableInterface $table, $columns = [], $extra = [])
    {
        $this->table = $table;
        $this->columns = array_merge($this->getDefaultColumns(), $columns);
        $this->extra = $extra;

        // Make sure the table exists
        $this->table->create();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function log($level, $message, array $context = [])
    {
        $fullMsg = $this->interpolate($message, $context);
        $rowData = $this->getLogRowData($level, $fullMsg);

        $this->table[] = $rowData;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function clearLogs()
    {
        $this->table->clear();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getLogs($num = null, $page = 1)
    {
        $table = $this->table;

        if ($num !== null) {
            // Ensure num and page are at least 1
            $num = max(1, $num);
            $page = max(1, $page);
            // Calculate the offset
            $offset = $num * ($page - 1);

            $table = $table->filter([
                TableInterface::FILTER_LIMIT => $num,
                TableInterface::FILTER_OFFSET => $offset,
            ]);
        }

        $table = $table->filter([
            TableInterface::FILTER_ORDER_BY => $this->columns[static::LOG_ID],
            TableInterface::FILTER_ORDER => 'DESC',
        ]);

        $logs = [];
        foreach ($table as $row) {
            $log = [];
            foreach ($this->columns as $prop => $col) {
                $log[$prop] = $row[$col];
            }
            $log['feed'] = get_post($log['feed_id']);

            $logs[] = $log;
        }

        return array_reverse($logs);
    }

    /**
     * Retrieves the data to insert for a single log's row.
     *
     * @since 4.13
     *
     * @param string $level   The log's level.
     * @param string $message The log's message.
     *
     * @return array An associative array containing the columns as keys mapping to their respective values.
     */
    protected function getLogRowData($level, $message)
    {
        $data = [];

        // Iterate the columns and retrieve the data to insert for each
        foreach ($this->columns as $prop => $col) {
            $value = $this->getLogPropData($prop, $level, $message);

            if ($value === null) {
                continue;
            }

            $data[$col] = $value;
        }

        return $data;
    }

    /**
     * Retrieves the data for a single log's property.
     *
     * @since 4.13
     *
     * @param string $prop    The name of the property for which to return data.
     * @param string $level   The log's level.
     * @param string $message The log's message.
     *
     * @return mixed The data to insert for the specified log property.
     */
    protected function getLogPropData($prop, $level, $message)
    {
        // Ignore the ID and date columns - they are auto populated by the DB
        if ($prop === static::LOG_ID || $prop === static::LOG_DATE) {
            return null;
        }

        if ($prop === static::LOG_LEVEL) {
            return (string) $level;
        }

        if ($prop === static::LOG_MESSAGE) {
            return $message;
        }

        if (!isset($this->extra[$prop])) {
            return null;
        }

        if (is_callable($this->extra[$prop])) {
            return call_user_func_array($this->extra[$prop], [$level, $message, $this->extra]);
        }

        return $this->extra[$prop];
    }

    /**
     * Retrieves the default columns.
     *
     * @since 4.13
     *
     * @return array A map of log property keys mapping to their respective column names.
     */
    protected function getDefaultColumns()
    {
        return [
            static::LOG_ID => static::LOG_ID,
            static::LOG_DATE => static::LOG_DATE,
            static::LOG_LEVEL => static::LOG_LEVEL,
            static::LOG_MESSAGE => static::LOG_MESSAGE,
        ];
    }
}
