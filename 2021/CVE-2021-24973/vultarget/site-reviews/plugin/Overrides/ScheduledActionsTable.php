<?php

namespace GeminiLabs\SiteReviews\Overrides;

use ActionScheduler;
use ActionScheduler_Abstract_ListTable;
use ActionScheduler_HybridStore;
use ActionScheduler_LogEntry;
use ActionScheduler_LoggerSchema;
use ActionScheduler_Schedule;
use ActionScheduler_Store;
use ActionScheduler_StoreSchema;
use DateTimezone;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Queue;

class ScheduledActionsTable extends ActionScheduler_Abstract_ListTable
{
    /**
     * @var array
     */
    protected $bulk_actions = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var bool
     */
    protected static $did_notification = false;

    /**
     * @var \ActionScheduler_Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $package = 'action-scheduler';

    /**
     * @var array
     */
    protected $row_actions = [];

    /**
     * @var array
     */
    protected $row_actions_all = [];

    /**
     * @var \ActionScheduler_QueueRunner
     */
    protected $runner;

    /**
     * @var array
     */
    protected $search_by = [
        'hook',
        'args',
        'claim_id',
    ];

    /**
     * @var array
     */
    protected $sort_by = [
        'schedule',
        'hook',
    ];

    /**
     * @var \ActionScheduler_Store
     */
    protected $store;

    public function __construct()
    {
        $this->store = ActionScheduler::store();
        $this->logger = ActionScheduler::logger();
        $this->runner = ActionScheduler::runner();
        $this->bulk_actions = [
            'delete' => _x('Delete', 'admin-text', 'site-reviews'),
        ];
        $this->columns = [
            'hook' => _x('Hook', 'admin-text', 'site-reviews'),
            'status' => _x('Status', 'admin-text', 'site-reviews'),
            'recurrence' => _x('Recurrence', 'admin-text', 'site-reviews'),
            'schedule' => _x('Scheduled Date', 'admin-text', 'site-reviews'),
            'args' => _x('Args', 'admin-text', 'site-reviews'),
            'log_entries' => _x('Log', 'admin-text', 'site-reviews'),
        ];
        $request_status = $this->get_request_status();
        if (empty($request_status)) {
            $this->sort_by[] = 'schedule';
        } elseif (in_array($request_status, ['in-progress', 'failed'])) {
            $this->columns += ['claim_id' => __('Claim ID', 'site-reviews')];
            $this->sort_by[] = 'claim_id';
        }
        $this->row_actions_all = [
            'hook' => [
                'retry' => [
                    'name' => _x('Retry', 'admin-text', 'site-reviews'),
                    'desc' => _x('Retry the action now as if it were run as part of a queue', 'admin-text', 'site-reviews'),
                ],
                'run' => [
                    'name' => _x('Run Now', 'admin-text', 'site-reviews'),
                    'desc' => _x('Process the action now as if it were run as part of a queue', 'admin-text', 'site-reviews'),
                ],
                'cancel' => [
                    'name' => _x('Cancel', 'admin-text', 'site-reviews'),
                    'desc' => _x('Cancel the action now to avoid it being run in future', 'admin-text', 'site-reviews'),
                    'class' => 'cancel trash',
                ],
                'delete' => [
                    'name' => _x('Delete', 'admin-text', 'site-reviews'),
                    'desc' => _x('Delete the action now', 'admin-text', 'site-reviews'),
                    'class' => 'trash',
                ],
            ],
        ];
        parent::__construct([
            'ajax' => true,
            'plural' => 'action-scheduler',
            'singular' => 'action-scheduler',
        ]);
    }

    /**
     * Serializes the argument of an action to render it in a human friendly format.
     *
     * @param array $row The array representation of the current row of the table
     * @return string
     */
    public function column_args(array $row)
    {
        if (empty($row['args'])) {
            return apply_filters('action_scheduler_list_table_column_args', '', $row);
        }
        $tooltip = var_export($row['args'], true);
        $patterns = [
            "/array \(/" => '[',
            "/^(\s*)\)(,?)$/m" => '$1]$2',
            "/=>\s?\\n\s+\[/" => '=> [',
            "/(\s*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
            "/\[\\n\s+\]/" => '[]',
            "/\s+\d+\s=>\s(\d+,)\\n/" => '$1',
            "/(\d+),\s+(\],\\n)/" => '$1$2',
        ];
        $tooltip = preg_replace(array_keys($patterns), array_values($patterns), $tooltip);
        $row_html = sprintf('<span class="glsr-tooltip dashicons-before dashicons-format-aside" data-syntax="php" data-tippy-allowHTML="1" data-tippy-content="%s" data-tippy-delay="0" data-tippy-interactive="1" data-tippy-maxWidth="640" data-tippy-trigger="click"></span>',
            esc_html($tooltip)
        );
        return apply_filters('action_scheduler_list_table_column_args', $row_html, $row);
    }

    /**
     * Default column formatting, it will escape everythig for security.
     */
    public function column_hook(array $row)
    {
        $column_html = sprintf('<span class="row-title">%s</span>', esc_html($row['hook']));
        $column_html .= $this->maybe_render_actions($row, 'hook');
        return $column_html;
    }

    /**
     * Prints the logs entries inline. We do so to avoid loading Javascript and other hacks to show it in a modal.
     *
     * @param array $row action array
     * @return string
     */
    public function column_log_entries(array $row)
    {
        $log_entries_html = '<ol>';
        $timezone = new \DateTimezone('UTC');
        foreach ($row['log_entries'] as $log_entry) {
            $log_entries_html .= $this->get_log_entry_html($log_entry, $timezone);
        }
        $log_entries_html .= '</ol>';
        return sprintf('<span class="glsr-tooltip dashicons-before dashicons-format-aside" data-tippy-allowHTML="1" data-tippy-content="%s" data-tippy-delay="0" data-tippy-interactive="1" data-tippy-trigger="click"></span>',
            esc_html($log_entries_html)
        );
    }

    /**
     * Prints the scheduled date in a human friendly format.
     *
     * @param array $row The array representation of the current row of the table
     * @return string
     */
    public function column_schedule($row)
    {
        return $this->get_schedule_display_string($row['schedule']);
    }

    /**
     * Renders admin notifications.
     *
     * Notifications:
     *  1. When the maximum number of tasks are being executed simultaneously.
     *  2. Notifications when a task is manually executed.
     *  3. Tables are missing.
     */
    public function display_admin_notices()
    {
        global $wpdb;
        if ((is_a($this->store, 'ActionScheduler_HybridStore') || is_a($this->store, 'ActionScheduler_DBStore')) && apply_filters('action_scheduler_enable_recreate_data_store', true)) {
            $table_list = [
                'actionscheduler_actions',
                'actionscheduler_logs',
                'actionscheduler_groups',
                'actionscheduler_claims',
            ];
            $found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}actionscheduler%'"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            foreach ($table_list as $table_name) {
                if (!in_array($wpdb->prefix.$table_name, $found_tables)) {
                    $this->admin_notices[] = [
                        'class' => 'error',
                        'message' => __('It appears one or more database tables were missing. Attempting to re-create the missing table(s).', 'site-reviews'),
                    ];
                    $this->recreate_tables();
                    parent::display_admin_notices();
                    return;
                }
            }
        }
        if ($this->runner->has_maximum_concurrent_batches()) {
            $claim_count = $this->store->get_claim_count();
            $this->admin_notices[] = [
                'class' => 'updated',
                'message' => sprintf(
                    /* translators: %s: amount of claims */
                    _n(
                        'Maximum simultaneous queues already in progress (%s queue). No additional queues will begin processing until the current queues are complete.',
                        'Maximum simultaneous queues already in progress (%s queues). No additional queues will begin processing until the current queues are complete.',
                        $claim_count,
                        'site-reviews'
                    ),
                    $claim_count
                ),
            ];
        } elseif ($this->store->has_pending_actions_due()) {
            $async_request_lock_expiration = ActionScheduler::lock()->get_expiration('async-request-runner');
            // No lock set or lock expired
            if (false === $async_request_lock_expiration || $async_request_lock_expiration < time()) {
                $in_progress_url = add_query_arg('status', 'in-progress', remove_query_arg('status'));
                /* translators: %s: process URL */
                $async_request_message = sprintf(__('A new queue has begun processing. <a href="%s">View actions in-progress &raquo;</a>', 'site-reviews'), esc_url($in_progress_url));
            } else {
                /* translators: %d: seconds */
                $async_request_message = sprintf(__('The next queue will begin processing in approximately %d seconds.', 'site-reviews'), $async_request_lock_expiration - time());
            }
            $this->admin_notices[] = [
                'class' => 'notice notice-info is-dismissible',
                'message' => $async_request_message,
            ];
        }
        $notification = get_transient('action_scheduler_admin_notice');
        if (is_array($notification)) {
            delete_transient('action_scheduler_admin_notice');
            $action = $this->store->fetch_action($notification['action_id']);
            $action_hook_html = '<strong><code>'.$action->get_hook().'</code></strong>';
            if (1 == $notification['success']) {
                $class = 'success';
                switch ($notification['row_action_type']) {
                    case 'run':
                        /* translators: %s: action HTML */
                        $action_message_html = sprintf(__('Successfully executed action: %s', 'site-reviews'), $action_hook_html);
                        break;
                    case 'cancel':
                        /* translators: %s: action HTML */
                        $action_message_html = sprintf(__('Successfully canceled action: %s', 'site-reviews'), $action_hook_html);
                        break;
                    case 'delete':
                        $action_message_html = __('Successfully deleted action', 'site-reviews');
                        break;
                    default:
                        /* translators: %s: action HTML */
                        $action_message_html = sprintf(__('Successfully processed change for action: %s', 'site-reviews'), $action_hook_html);
                        break;
                }
            } else {
                $class = 'error';
                /* translators: 1: action HTML 2: action ID 3: error message */
                $action_message_html = sprintf(__('Could not process change for action: "%1$s" (ID: %2$d). Error: %3$s', 'site-reviews'), $action_hook_html, esc_html($notification['action_id']), esc_html($notification['error_message']));
            }
            $action_message_html = apply_filters('action_scheduler_admin_notice_html', $action_message_html, $action, $notification);
            $this->admin_notices[] = [
                'class' => sprintf('notice notice-%s is-dismissible', $class),
                'message' => $action_message_html,
            ];
        }
        parent::display_admin_notices();
    }

    /**
     * Render the list table page, including header, notices, status filters and table.
     */
    public function display_page()
    {
        $this->process_bulk_action();
        $this->process_row_actions();
        $this->prepare_items();
        $this->display_admin_notices();
        $this->display_filter_by_status();
        $this->display_table();
    }

    /**
     * {@inheritDoc}
     */
    public function prepare_items()
    {
        $this->prepare_column_headers();
        $per_page = $this->get_items_per_page($this->package.'_items_per_page', $this->items_per_page);
        $query = [
            'group' => glsr()->id,
            'per_page' => $per_page,
            'offset' => $this->get_items_offset(),
            'status' => $this->get_request_status(),
            'orderby' => $this->get_request_orderby(),
            'order' => $this->get_request_order(),
            'search' => $this->get_request_search_query(),
        ];
        $this->items = [];
        $total_items = $this->store->query_actions($query, 'count');
        $status_labels = $this->store->get_status_labels();
        foreach ($this->store->query_actions($query) as $action_id) {
            try {
                $action = $this->store->fetch_action($action_id);
            } catch (\Exception $e) {
                continue;
            }
            if (is_a($action, 'ActionScheduler_NullAction')) {
                continue;
            }
            $this->items[$action_id] = [
                'ID' => $action_id,
                'hook' => $action->get_hook(),
                'status_name' => $this->store->get_status($action_id),
                'status' => $status_labels[$this->store->get_status($action_id)],
                'args' => $action->get_args(),
                'group' => $action->get_group(),
                'log_entries' => $this->logger->get_logs($action_id),
                'claim_id' => $this->store->get_claim_id($action_id),
                'recurrence' => $this->get_recurrence($action),
                'schedule' => $action->get_schedule(),
            ];
        }
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }

    /**
     * Displays the search box.
     *
     * @param string $text     the 'submit' button label
     * @param string $input_id ID attribute value for the search input field
     */
    public function search_box($text, $input_id)
    {
        return; // hide the search box
    }

    /**
     * Generates content for a single row of the table.
     *
     * @param object|array $item The current item
     */
    public function single_row($item)
    {
        printf('<tr class="action-%s">', $item['status_name']);
        $this->single_row_columns($item);
        echo '</tr>';
    }

    /**
     * Bulk delete.
     *
     * Deletes actions based on their ID. This is the handler for the bulk delete. It assumes the data
     * properly validated by the callee and it will delete the actions without any extra validation.
     *
     * @param array $ids
     * @param string $ids_sql Inherited and unused
     */
    protected function bulk_delete(array $ids, $ids_sql)
    {
        foreach ($ids as $action_id) {
            try {
                $this->store->delete_action($action_id);
            } catch (\Exception $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    /**
     * Prints the available statuses so the user can click to filter.
     */
    protected function display_filter_by_status()
    {
        global $wpdb;
        $sql = "
            SELECT a.status, count(a.status) as 'count'
            FROM {$wpdb->actionscheduler_actions} a 
            INNER JOIN {$wpdb->actionscheduler_groups} g ON a.group_id = g.group_id
            WHERE g.slug = %s
            GROUP BY a.status
        ";
        $this->status_counts = [];
        $action_stati_and_labels = $this->store->get_status_labels();
        $results = $wpdb->get_results($wpdb->prepare($sql, glsr()->id));
        foreach ($results as $action_data) {
            if (array_key_exists($action_data->status, $action_stati_and_labels)) {
                $this->status_counts[$action_data->status] = $action_data->count;
            }
        }
        parent::display_filter_by_status();
    }

    /**
     * Prints the logs entries inline. We do so to avoid loading Javascript and other hacks to show it in a modal.
     *
     * @return string
     */
    protected function get_log_entry_html(ActionScheduler_LogEntry $log_entry, DateTimezone $timezone)
    {
        $date = $log_entry->get_date();
        $date->setTimezone($timezone);
        return sprintf('<li><strong>%s</strong><br/>%s</li>', esc_html($date->format('Y-m-d H:i:s O')), esc_html($log_entry->get_message()));
    }

    /**
     * Returns the recurrence of an action or 'Non-repeating'. The output is human readable.
     *
     * @param \ActionScheduler_Action $action
     * @return string
     */
    protected function get_recurrence($action)
    {
        $schedule = $action->get_schedule();
        if (!$schedule->is_recurring()) {
            return _x('Non-repeating', 'admin-text', 'site-reviews');
        }
        $recurrence = $schedule->get_recurrence();
        if (is_numeric($recurrence)) {
            return sprintf(_x('Every %s', '%s: time interval (admin-text)', 'site-reviews'), glsr(Date::class)->interval($recurrence));
        }
        return $recurrence;
    }

    /**
     * Return the sortable column order specified for this request.
     *
     * @return string
     */
    protected function get_request_order()
    {
        $order = strtolower(filter_input(INPUT_GET, 'order'));
        if ('desc' === $order) {
            return 'DESC';
        }
        if (empty($order) && 'schedule' === $this->get_request_orderby()) {
            return 'DESC';
        }
        return 'ASC';
    }

    /**
     * Get the scheduled date in a human friendly format.
     *
     * @param ActionScheduler_Schedule $schedule
     * @return string
     */
    protected function get_schedule_display_string(ActionScheduler_Schedule $schedule)
    {
        $schedule_display_string = '';
        if (!$schedule->get_date()) {
            return '0000-00-00 00:00:00';
        }
        $next_timestamp = $schedule->get_date()->getTimestamp();
        $schedule_display_string .= $schedule->get_date()->format('Y-m-d H:i:s O');
        $schedule_display_string .= '<br/>';
        if (gmdate('U') > $next_timestamp) {
            $schedule_display_string .= sprintf(' (%s)', glsr(Date::class)->interval(gmdate('U') - $next_timestamp, 'past'));
        } else {
            $schedule_display_string .= sprintf(' (%s)', glsr(Date::class)->interval($next_timestamp - gmdate('U')));
        }
        return $schedule_display_string;
    }

    /**
     * Get the text to display in the search box on the list table.
     */
    protected function get_search_box_button_text()
    {
        return ''; // we are not displaying the search box
    }

    /**
     * Only display row actions for pending actions.
     *
     * @param array  $row         Row to render
     * @param string $column_name Current row
     * @return string
     */
    protected function maybe_render_actions($row, $column_name)
    {
        $actions = $this->row_actions_all;
        $status = strtolower($row['status_name']);
        switch ($status) {
            case ActionScheduler_Store::STATUS_CANCELED:
            case ActionScheduler_Store::STATUS_RUNNING:
                return '';
            case ActionScheduler_Store::STATUS_COMPLETE:
                unset($actions['hook']['cancel']);
                unset($actions['hook']['retry']);
                unset($actions['hook']['run']);
                break;
            case ActionScheduler_Store::STATUS_FAILED:
                unset($actions['hook']['cancel']);
                unset($actions['hook']['run']);
                break;
            case ActionScheduler_Store::STATUS_PENDING:
                unset($actions['hook']['delete']);
                unset($actions['hook']['retry']);
                break;
        }
        $this->row_actions = $actions;
        return parent::maybe_render_actions($row, $column_name);
    }

    /**
     * Implements the logic behind processing an action once an action link is clicked on the list table.
     *
     * @param int $action_id
     * @param string $row_action_type the type of action to perform on the action
     */
    protected function process_row_action($action_id, $row_action_type)
    {
        try {
            switch ($row_action_type) {
                case 'cancel':
                    $this->store->cancel_action((string) $action_id);
                    break;
                case 'delete':
                    $this->store->delete_action((string) $action_id);
                    break;
                case 'retry':
                    $action = $this->store->fetch_action((string) $action_id);
                    // don't use Queue because we want to keep the original hook
                    as_schedule_single_action(time(), $action->get_hook(), $action->get_args(), glsr()->id);
                    $this->store->delete_action((string) $action_id);
                    break;
                case 'run':
                    $this->runner->process_action($action_id, 'Admin List Table');
                    break;
            }
            $success = 1;
            $error_message = '';
        } catch (\Exception $e) {
            $success = 0;
            $error_message = $e->getMessage();
        }
        set_transient('action_scheduler_admin_notice', compact('action_id', 'success', 'error_message', 'row_action_type'), 30);
    }

    /**
     * Force the data store schema updates.
     */
    protected function recreate_tables()
    {
        if (is_a($this->store, 'ActionScheduler_HybridStore')) {
            $store = $this->store;
        } else {
            $store = new ActionScheduler_HybridStore();
        }
        add_action('action_scheduler/created_table', [$store, 'set_autoincrement'], 10, 2);
        $store_schema = new ActionScheduler_StoreSchema();
        $logger_schema = new ActionScheduler_LoggerSchema();
        $store_schema->register_tables(true);
        $logger_schema->register_tables(true);
        remove_action('action_scheduler/created_table', [$store, 'set_autoincrement'], 10);
    }

    /**
     * Implements the logic behind running an action. ActionScheduler_Abstract_ListTable validates the request and their
     * parameters are valid.
     *
     * @param int $action_id
     */
    protected function row_action_cancel($action_id)
    {
        $this->process_row_action($action_id, 'cancel');
    }

    /**
     * @param int $action_id
     */
    protected function row_action_delete($action_id)
    {
        $this->process_row_action($action_id, 'delete');
    }

    /**
     * @param int $action_id
     */
    protected function row_action_retry($action_id)
    {
        $this->process_row_action($action_id, 'retry');
    }

    /**
     * Implements the logic behind running an action. ActionScheduler_Abstract_ListTable validates the request and their
     * parameters are valid.
     *
     * @param int $action_id
     */
    protected function row_action_run($action_id)
    {
        $this->process_row_action($action_id, 'run');
    }
}
