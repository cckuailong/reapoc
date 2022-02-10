<?php

namespace GeminiLabs\SiteReviews\Modules;

use ActionScheduler;
use ActionScheduler_Store;
use GeminiLabs\SiteReviews\Contracts\QueueContract;
use GeminiLabs\SiteReviews\Helpers\Str;

class Queue implements QueueContract
{
    const STATUS_CANCELED = ActionScheduler_Store::STATUS_CANCELED;
    const STATUS_COMPLETE = ActionScheduler_Store::STATUS_COMPLETE;
    const STATUS_FAILED = ActionScheduler_Store::STATUS_FAILED;
    const STATUS_PENDING = ActionScheduler_Store::STATUS_PENDING;
    const STATUS_RUNNING = ActionScheduler_Store::STATUS_RUNNING;

    /**
     * @var bool
     */
    protected $isTesting;

    public function __construct()
    {
        $this->isTesting = defined('GLSR_UNIT_TESTS');
    }

    /**
     * @return \GeminiLabs\SiteReviews\Application|\GeminiLabs\SiteReviews\Addons\Addon
     */
    public function app()
    {
        return glsr();
    }

    /**
     * {@inheritdoc}
     */
    public function async($hook, $args = [])
    {
        if (!function_exists('as_enqueue_async_action') || $this->isTesting) {
            return 0;
        }
        return as_enqueue_async_action($this->hook($hook), $args, glsr()->id);
    }

    /**
     * {@inheritdoc}
     */
    public function cancel($hook, $args = [])
    {
        if (!function_exists('as_unschedule_action') || $this->isTesting) {
            return null;
        }
        return as_unschedule_action($this->hook($hook), $args, glsr()->id);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelAll($hook, $args = [])
    {
        if (!ActionScheduler::is_initialized('Queue::cancelAll') || $this->isTesting) {
            return;
        }
        if (empty($args)) {
            ActionScheduler_Store::instance()->cancel_actions_by_hook($this->hook($hook));
        } elseif (function_exists('as_unschedule_all_actions')) {
            as_unschedule_all_actions($this->hook($hook), $args, glsr()->id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cron($timestamp, $cron, $hook, $args = [])
    {
        if (!function_exists('as_schedule_cron_action') || $this->isTesting) {
            return 0;
        }
        return as_schedule_cron_action($timestamp, $cron, $this->hook($hook), $args, glsr()->id);
    }

    /**
     * {@inheritdoc}
     */
    public function isPending($hook, $args = [])
    {
        if (!ActionScheduler::is_initialized('Queue::isPending') || !function_exists('as_has_scheduled_action') || $this->isTesting) {
            return false;
        }
        if (empty($args)) {
            return !empty(
                glsr(Queue::class)->search([
                    'hook' => $this->hook($hook),
                    'status' => static::STATUS_PENDING,
                ])
            );
        }
        return as_has_scheduled_action($this->hook($hook), $args, glsr()->id);
    }

    /**
     * {@inheritdoc}
     */
    public function next($hook, $args = null)
    {
        if (!function_exists('as_next_scheduled_action') || $this->isTesting) {
            return false;
        }
        $next = as_next_scheduled_action($this->hook($hook), $args, glsr()->id);
        if (is_numeric($next)) {
            return new \DateTime('@'.$next, new \DateTimeZone('UTC'));
        }
        return $next;
    }

    /**
     * {@inheritdoc}
     */
    public function once($timestamp, $hook, $args = [])
    {
        if (!function_exists('as_schedule_single_action') || $this->isTesting) {
            return 0;
        }
        return as_schedule_single_action($timestamp, $this->hook($hook), $args, glsr()->id);
    }

    /**
     * {@inheritdoc}
     */
    public function recurring($timestamp, $intervalInSeconds, $hook, $args = [])
    {
        if (!function_exists('as_schedule_recurring_action') || $this->isTesting) {
            return 0;
        }
        return as_schedule_recurring_action($timestamp, $intervalInSeconds, $this->hook($hook), $args, glsr()->id);
    }

    /**
     * {@inheritdoc}
     */
    public function search($args = [], $returnFormat = OBJECT)
    {
        if (!function_exists('as_get_scheduled_actions') || $this->isTesting) {
            return [];
        }
        if (isset($args['hook'])) {
            $args['hook'] = $this->hook($args['hook']);
        }
        $args = wp_parse_args($args, ['group' => glsr()->id]);
        return as_get_scheduled_actions($args, $returnFormat);
    }

    /**
     * @param string $hook
     * @return string
     */
    protected function hook($hook)
    {
        return Str::prefix($hook, $this->app()->id.'/');
    }
}
