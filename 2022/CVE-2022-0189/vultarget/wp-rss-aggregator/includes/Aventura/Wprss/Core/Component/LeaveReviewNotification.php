<?php

namespace Aventura\Wprss\Core\Component;

use Aventura\Wprss\Core;

/**
 * The component responsible for the "Leave a Review" notification.
 *
 * On plugin activation, records that time, if not already recorded.
 * When retrieved, looks for that time. If not recorded, deduces it from the
 * creation time of the first feed source. If no feed sources, assumes current
 * time at the moment of retrieval. This result is recorded as the first
 * activation time, and is used in subsequent requests.
 *
 * @since 4.10
 */
class LeaveReviewNotification extends Core\Plugin\ComponentAbstract
{
    const FIRST_ACTIVATION_TIME_OPTION_SUFFIX = '_first_activation_time';
    const NOTICE_ID_SUFFIX = '_leave_review';
    const REVIEW_PAGE_URL = 'https://wordpress.org/support/plugin/wp-rss-aggregator/reviews/#new-topic-0';
    const NOTICE_DELAY_PERIOD = WPRSS_LEAVE_REVIEW_NOTIFICATION_DELAY;
    const MIN_ACTIVE_FEED_SOURCE_COUNT = WPRSS_LEAVE_REVIEW_NOTIFICATION_MIN_ACTIVE_FEED_SOURCES;

    protected $firstActivationTime;
    protected $firstActivationTimeOptionName;
    protected $noticeId;

    /**
     * Runs when this component is initialized.
     *
     * @since 4.10
     */
    public function hook()
    {
        $this->init();

        $hookName = sprintf('!activate_%1$s', $this->getPlugin()->getBasename());
        $this->on($hookName, 'onPluginActivated');
    }

    /**
     * Runs when the parent plugin is activated.
     *
     * @since 4.10
     */
    public function onPluginActivated()
    {
        $this->_recordEarliestTimeIfNotPresent();
    }

    /**
     * Runs when all plugins have finished loading.
     *
     * @since 4.10
     */
    public function init()
    {
        $this->addLeaveReviewNotice();
    }

    /**
     * Adds the notification.
     *
     * @since 4.10
     *
     * @return LeaveReviewNotification This instance.
     */
    public function addLeaveReviewNotice()
    {
        $notices = $this->_getNoticesComponent();

        $noticeParams = array(
            'id'                => $this->getNoticeId(),
            'content'           => $this->getNoticeContent(),
            'condition'         => $this->getNoticeCondition()
        );
        $this->event('leave_review_notice_before_add', array('notice_params' => &$noticeParams));

        $notices->addNotice($noticeParams);
    }

    /**
     * Retrieve the ID of the "Leave a Review" admin notice.
     *
     * @since 4.10
     *
     * @return string The ID of the notice.
     */
    public function getNoticeId()
    {
        $idSuffix = static::NOTICE_ID_SUFFIX;
        if (is_null($this->noticeId)) {
            $this->noticeId = $this->getPlugin()->getCode() . $idSuffix;
        }

        $noticeId = $this->noticeId;
        $this->event('leave_review_notice_id', array(
            'notice_id'         => &$noticeId,
            'id_suffix'         => $idSuffix
        ));

        return $noticeId;
    }

    /**
     * Retrieve the content for the "Leave a Review" notice.
     *
     * @since 4.10
     *
     * @return string The content for the notice.
     */
    public function getNoticeContent()
    {
        $message = <<<'MSG'
It looks like you've been using WP RSS Aggregator for a while. Would you please consider <a href="%2$s" target="_blank">leaving a review</a>? Thank you!
MSG;
        $content = wpautop(sprintf(
            $message,
            $this->getPlugin()->getName(),
            $this->getReviewPageUrl()
        ));

        $this->event('leave_review_notification_content', array(
            'content'   => &$content
        ));

        return $content;
    }

    /**
     * Retrieve the condition for the notice to show.
     *
     * @since 4.10
     *
     * @return callable The callable that determines whether or not to display the notice.
     */
    public function getNoticeCondition()
    {
        $condition = $this->_createCommand(array(
            'function'          => array($this, 'isShowLeaveReviewNotification')
        ));

        $this->event('leave_review_notice_condition', array('condition' => &$condition));

        return $condition;
    }

    /**
     * Determines if the notice is allowed to be displayed on the current page.
     *
     * @since 4.10
     *
     * @return bool True if the notice is allowed to be displayed on the current page; false otherwise.
     */
    public function isShowLeaveReviewNotification()
    {
        $isWprssPage = $this->isWprssPage();
        $isDelayExpired = $this->isNoticeDelayPeriodExpired();
        $isMinCountReached = $this->isMinFeedSourceCountReached();
        $isShow = $isWprssPage
            && $isDelayExpired
            && $isMinCountReached;

        $this->event('leave_review_notification_is_show', array(
            'is_show'               => &$isShow,
            'is_wprss_page'         => $isWprssPage,
            'is_delay_expired'      => $isDelayExpired,
            'is_min_count_reached'  => $isMinCountReached
        ));

        return $isShow;
    }

    /**
     * Determines whether a certain amount of time has passed since first activation of the plugin.
     *
     * @since 4.10
     *
     * @see WPRSS_LEAVE_REVIEW_NOTIFICATION_DELAY
     *
     * @return bool True if the pre-configured amount of time since first activation has passed; false otherwise.
     */
    public function isNoticeDelayPeriodExpired()
    {
        $now = $this->_getCurrentTimestampGmt();
        $delay = $this->getNoticeDelayPeriod();
        $firstActivation = $this->getFirstActivationTime();
        $isExpired = $now - $firstActivation >= $delay;

        $this->event('leave_review_notification_delay_period_is_expired', array(
            'is_expired'            => &$isExpired,
            'now'                   => $now,
            'delay'                 => $delay,
            'first_activation'      => $firstActivation
        ));

        return $isExpired;
    }

    /**
     * Determines if the minimal amount of sources required to display the notification exists.
     *
     * @since 4.10
     *
     * @return bool True if the minimal amount of feed sources exists; false otherwise.
     */
    public function isMinFeedSourceCountReached()
    {
        $minCount = $this->getMinFeedSourceCount();
        $activeSourceIds = $this->_getActiveFeedSourceIds($minCount);
        $isReached = count($activeSourceIds) >= $minCount;

        $this->event('leave_review_notification_min_feed_source_count_reached', array(
            'is_reached'            => &$isReached,
            'min_count'             => $minCount,
            'active_source_ids'     => $activeSourceIds
        ));

        return $isReached;
    }

    /**
     * Retrieve the minimal amount of active feed source required for the notification to be displayed.
     *
     * @since 4.10
     *
     * @return int The amount of feed sources required.
     */
    public function getMinFeedSourceCount()
    {
        $minCount = intval(static::MIN_ACTIVE_FEED_SOURCE_COUNT);
        $this->event('leave_review_notice_min_active_feed_source_count', array('min_count' => &$minCount));

        return $minCount;
    }

    /**
     * Retrieve IDs of feed sources that are active.
     *
     * @since 4.10
     *
     * @param array|int $args If array, will be merged with default query params.
     *  Otherwise, treated as the maximal number of IDs to return.
     *
     * @return array A numeric array containing IDs of feed sources that are active
     */
    protected function _getActiveFeedSourceIds($args = null)
    {
        if (!is_array($args)) {
            $args = is_null($args)
                    ? -1
                    : intval($args);
            $args = array('posts_per_page' => $args);
        }

        $defaults = array(
            'per_page'          => -1,
            'fields'            => 'ids',
            'meta_query'        => array(
                array(
                    'key'               => 'wprss_state',
                    'value'             => 'active'
                )
            )
        );
        $args = array_merge_recursive_distinct($defaults, $args);

        $ids = $this->_getFeedSources($args);

        return $ids;
    }

    /**
     * Get the delay before notice gets displayed.
     *
     * @since 4.10
     *
     * @return int The amount of seconds, by which to delay displaying the notice.
     */
    public function getNoticeDelayPeriod()
    {
        return intval(static::NOTICE_DELAY_PERIOD);
    }

    /**
     * Determines if the curren page is related to WPRSS.
     *
     * @since 4.10
     *
     * @return bool True if the current page is related to WPRSS; false otherwise.
     */
    public function isWprssPage()
    {
        return $this->getAdminHelper()->isWprssPage();
    }

    /**
     * Retrieves the URL of the page where to send visitors to leave a review.
     *
     * @since 4.10
     *
     * @return string The page URL.
     */
    public function getReviewPageUrl()
    {
        $url = static::REVIEW_PAGE_URL;
        $this->event('leave_review_page_url', array('url' => &$url));

        return $url;
    }

    /**
     * Retrieve the time of the first activation of the plugin.
     *
     * @since 4.10
     *
     * @return int|string The first activation time in GMT as Unix timestamp.
     *  If `$format` is not `null`, formats the timestamp using the specified format string.
     */
    public function getFirstActivationTime($format = null)
    {
        $this->_recordEarliestTimeIfNotPresent();
        $time = $this->_getFirstActivationTimeDb();

        $formatted = is_null($format)
                ? $time
                : date($format, $time);

        $this->event('leave_review_first_activation_time', array(
            'time'              => $time,
            'format'            => $format,
            'time_formatted'    => &$formatted
        ));

        return $formatted;
    }

    /**
     * Deduces what the first activation time is, if it is not recorded.
     *
     * @since 4.10
     *
     * @return int The first activation time, calculated, in the GMT zone.
     */
    protected function _calculateFirstActivationTime()
    {
        $feedSources = $this->_getFeedSources(1);
        if (!count($feedSources)) {
            return $this->_getCurrentTimestampGmt();
        }

        $firstFeedSource = $feedSources[0];
        /* @var $firstFeedSource \WP_Post */
        $firstFeedSourceCreated = $firstFeedSource->post_date_gmt;
        $firstFeedSourceCreated = $this->_dateToTimestamp($firstFeedSourceCreated);

        return $firstFeedSourceCreated;
    }

    /**
     * Converts a date string into a Unix timestamp.
     *
     * @since 4.10
     *
     * @see \DateTime::createFromFormat()
     *
     * @param string $date The string representation of the date.
     * @param string $format The format in which the date is specified.
     * @param string $timezone The string representation of the timezone, in which the date is specified.
     *
     * @return type
     */
    protected function _dateToTimestamp($date, $format = 'Y-m-d H:i:s', $timezone = 'UTC')
    {
        $date = \DateTime::createFromFormat(
            $format,
            $date,
            new \DateTimeZone($timezone)
        );
        $timestamp = $date->getTimestamp();

        return $timestamp;
    }

    /**
     * Retrieve feed sources for specific conditions.
     *
     * @since 4.10
     *
     * @param array|int|null $args The maximal amount of feed sources to retrieve.
     *  If array, will be treated as query args, and merged with defaults.
     * @return WP_Post[]|\Traversable A list of feed sources that obey the specified conditions.
     */
    protected function _getFeedSources($args = null)
    {
        if (!is_array($args)) {
            $args = is_null($args)
                    ? -1
                    : intval($args);
            $args = array('posts_per_page' => $args);
        }

        $defaults = array(
            'post_type'         => $this->_getFeedSourcePostType(),
            'posts_per_page'    => -1,
            'orderby'           => 'date',
            'order'             => 'ASC',
        );
        $args = array_merge_recursive_distinct($defaults, $args);

        $query = new \WP_Query($args);

        return $query->posts;
    }

    /**
     * Retrieves the first activation time value from the database.
     *
     * @since 4.10
     *
     * @return int|null
     */
    protected function _getFirstActivationTimeDb()
    {
        $optionName =  $this->_getFirstActivationTimeOptionName();
        $value = get_option($optionName, null);

        if (!is_null($value )){
            $value = intval($value);
        }

        return $value;
    }

    /**
     * Sets the value of the option, which stores the first activation time in the database.
     *
     * @since 4.10
     *
     * @param int $time The GMT time as a Unix timestamp.
     * @return bool True if time set successfully, false otherwise.
     */
    protected function _setFirstActivationTimeDb($time)
    {
        $optionName =  $this->_getFirstActivationTimeOptionName();

        return update_option($optionName, $time);
    }

    /**
     * Retrieves the name of the option, which stores the first activation time.
     *
     * @since 4.10
     *
     * @return string Name of the option.
     */
    protected function _getFirstActivationTimeOptionName()
    {
        if (is_null($this->firstActivationTimeOptionName)) {
            $this->firstActivationTimeOptionName =
                    $this->getPlugin()->getCode() . static::FIRST_ACTIVATION_TIME_OPTION_SUFFIX;
        }

        return $this->firstActivationTimeOptionName;
    }

    /**
     * Records the current time as first activation time, if not already recorded.
     *
     * @since 4.10
     *
     * @return LeaveReviewNotification This instance.
     */
    protected function _recordEarliestTimeIfNotPresent()
    {
        $time = $this->_getFirstActivationTimeDb();

        if (empty($time)) {
            $time = $this->_calculateFirstActivationTime();
            $this->_setFirstActivationTimeDb($time);
        }

        return $this;
    }

    /**
     * Retrieves the current timestamp in the GMT zone.
     *
     * @since 4.10
     *
     * @return int The number of seconds from the start of the Unix epoch, GMT.
     */
    protected function _getCurrentTimestampGmt()
    {
        return intval(current_time('timestamp', true));
    }

    /**
     * Retrieves the name of the feed source post type.
     *
     * @since 4.10
     *
     * @return string The post type name of the feed source post type.
     */
    protected function _getFeedSourcePostType()
    {
        return $this->getPlugin()->getFeedSourcePostType();
    }

    /**
     * Retrieve the component responsible for admin AJAX notices.
     *
     * @since 4.10
     *
     * @return AdminAjaxNotices The component instance.
     */
    protected function _getNoticesComponent()
    {
        return $this->getPlugin()->getAdminAjaxNotices();
    }

    /**
     * Retrieve the admin helper singleton.
     *
     * @since 4.10
     *
     * @return AdminHelper The helper singleton instance.
     */
    public function getAdminHelper()
    {
        return $this->getPlugin()->getAdminHelper();
    }

    /**
     * Creates a callable command instance.
     *
     * @since 4.10
     *
     * @param array|callable $data See {@see Core\Model\Command}.
     * @return Core\Model\Command|callable See {@see Core\Model\Command}.
     */
    protected function _createCommand($data)
    {
        return $this->getAdminHelper()->createCommand($data);
    }
}
