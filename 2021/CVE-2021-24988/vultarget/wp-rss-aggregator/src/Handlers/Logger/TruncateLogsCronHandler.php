<?php

namespace RebelCode\Wpra\Core\Handlers\Logger;

use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Database\TableInterface;

/**
 * The handler for the log truncation cron job.
 *
 * @since 4.13
 */
class TruncateLogsCronHandler
{
    /**
     * The log table to truncate.
     *
     * @since 4.13
     *
     * @var TableInterface
     */
    protected $table;

    /**
     * The logging config data set.
     *
     * @since 4.14
     *
     * @var DataSetInterface
     */
    protected $config;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param TableInterface   $table  The log table.
     * @param DataSetInterface $config The logging config data set.
     */
    public function __construct(TableInterface $table, DataSetInterface $config)
    {
        $this->table = $table;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        $daysLimit = $this->config['logging/limit_days'];

        if (empty($daysLimit) || $daysLimit <= 0) {
            return;
        }

        // Filter to retrieve logs older than 60 days
        $table = $this->table->filter([
            'where' => sprintf('DATEDIFF(CURDATE(), `date`) > %d', $daysLimit),
        ]);

        // Clear the filtered table
        $table->clear();
    }
}
