<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Application;

interface QueueContract
{
    /**
     * Enqueue an action to run one time, as soon as possible.
     *
     * @param string $hook the hook to trigger
     * @param array $args arguments to pass when the hook triggers
     * @return int the action ID
     */
    public function async($hook, $args = []);

    /**
     * Cancel the next occurrence of a scheduled action.
     *
     * While only the next instance of a recurring or cron action is unscheduled by this method, that will also prevent
     * all future instances of that recurring or cron action from being run. Recurring and cron actions are scheduled in
     * a sequence instead of all being scheduled at once. Each successive occurrence of a recurring action is scheduled
     * only after the former action is run. If the next instance is never run, because it's unscheduled by this function,
     * then the following instance will never be scheduled (or exist), which is effectively the same as being unscheduled
     * by this method also.
     *
     * @param string $hook the hook that the job will trigger
     * @param array $args args that would have been passed to the job
     * @return string|null the scheduled action ID if a scheduled action was found, or null if no matching action found
     */
    public function cancel($hook, $args = []);

    /**
     * Cancel all occurrences of a scheduled action.
     *
     * @param string $hook the hook that the job will trigger
     * @param array $args args that would have been passed to the job
     */
    public function cancelAll($hook, $args = []);

    /**
     * Schedule an action that recurs on a cron-like schedule.
     *
     * @param int $timestamp The first instance of the action will be scheduled
     *        to run at a time calculated after this timestamp matching the cron
     *        expression. This can be used to delay the first instance of the action.
     * @param string $schedule A cron-link schedule string
     * @see http://en.wikipedia.org/wiki/Cron
     *   *    *    *    *    *    *
     *   ┬    ┬    ┬    ┬    ┬    ┬
     *   |    |    |    |    |    |
     *   |    |    |    |    |    + year [optional]
     *   |    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
     *   |    |    |    +---------- month (1 - 12)
     *   |    |    +--------------- day of month (1 - 31)
     *   |    +-------------------- hour (0 - 23)
     *   +------------------------- min (0 - 59)
     * @param string $hook the hook to trigger
     * @param array $args arguments to pass when the hook triggers
     * @return int the action ID
     */
    public function cron($timestamp, $schedule, $hook, $args = []);

    /**
     * Check if there is a scheduled action in the queue but more efficiently than as_next_scheduled_action().
     *
     * It's recommended to use this function when you need to know whether a specific action is currently scheduled (pending or in-progress).
     *
     * @param string $hook  the hook of the action
     * @param array  $args  Args that have been passed to the action. Null will matches any args.
     * @return bool true if a matching action is pending or in-progress, false otherwise
     */
    public function isPending($hook, $args = []);

    /**
     * Check if there is an existing action in the queue with a given hook, args and group combination.
     *
     * An action in the queue could be pending, in-progress or async. If the is pending for a time in
     * future, its scheduled date will be returned as a timestamp. If it is currently being run, or an
     * async action sitting in the queue waiting to be processed, in which case boolean true will be
     * returned. Or there may be no async, in-progress or pending action for this hook, in which case,
     * boolean false will be the return value.
     *
     * @param string $hook
     * @param array $args
     * @return \DateTime|bool the timestamp for the next occurrence of a pending scheduled action, true for an async or in-progress action or false if there is no matching action
     */
    public function next($hook, $args = null);

    /**
     * Schedule an action to run one time.
     *
     * @param int $timestamp when the job will run
     * @param string $hook the hook to trigger
     * @param array $args arguments to pass when the hook triggers
     * @return int the action ID
     */
    public function once($timestamp, $hook, $args = []);

    /**
     * Schedule a recurring action.
     *
     * @param int $timestamp when the first instance of the job will run
     * @param int $intervalInSeconds how long to wait between runs
     * @param string $hook the hook to trigger
     * @param array $args arguments to pass when the hook triggers
     * @return int the action ID
     */
    public function recurring($timestamp, $intervalInSeconds, $hook, $args = []);

    /**
     * Find scheduled actions.
     *
     * @param array $args Possible arguments, with their default values:
     *        'hook' => '' - the name of the action that will be triggered
     *        'args' => NULL - the args array that will be passed with the action
     *        'date' => NULL - the scheduled date of the action. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime(). Used in UTC timezone.
     *        'date_compare' => '<=' - operator for testing "date". accepted values are '!=', '>', '>=', '<', '<=', '='
     *        'modified' => NULL - the date the action was last updated. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime(). Used in UTC timezone.
     *        'modified_compare' => '<=' - operator for testing "modified". accepted values are '!=', '>', '>=', '<', '<=', '='
     *        'group' => '' - the group the action belongs to
     *        'status' => '' - ActionScheduler_Store::STATUS_COMPLETE or ActionScheduler_Store::STATUS_PENDING
     *        'claimed' => NULL - TRUE to find claimed actions, FALSE to find unclaimed actions, a string to find a specific claim ID
     *        'per_page' => 5 - Number of results to return
     *        'offset' => 0
     *        'orderby' => 'date' - accepted values are 'hook', 'group', 'modified', 'date' or 'none'
     *        'order' => 'ASC'
     *
     * @param string $returnFormat OBJECT, ARRAY_A, or ids
     * @return array
     */
    public function search($args = [], $returnFormat = OBJECT);
}
