<?php

namespace RebelCode\Wpra\Core\Handlers\CustomFeed;

use RebelCode\Wpra\Core\Data\DataSetInterface;

/**
 * The handler that registers the WPRA custom feed with WordPress.
 *
 * @since 4.13
 */
class RegisterCustomFeedHandler
{
    /**
     * The WPRA general settings dataset.
     *
     * @since 4.13
     *
     * @var DataSetInterface
     */
    protected $settings;

    /**
     * The default custom feed URL if no URL is saved in the settings.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $defaultUrl;

    /**
     * The callback to render the custom feed.
     *
     * @since 4.13
     *
     * @var callable
     */
    protected $renderCallback;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param DataSetInterface $settings       The WPRA general settings dataset.
     * @param string           $defaultUrl     The default custom feed URL if no URL is saved in the settings.
     * @param callable         $renderCallback The callback to render the custom feed.
     */
    public function __construct(DataSetInterface $settings, $defaultUrl, callable $renderCallback)
    {
        $this->settings = $settings;
        $this->defaultUrl = $defaultUrl;
        $this->renderCallback = $renderCallback;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        // Get the custom feed URL
        $customFeedUrl = $this->getCustomFeedUrl();

        // Register the feed with WordPress
        add_feed($customFeedUrl, $this->renderCallback);

        // If the custom feed rewrite rule does not exist, flush the rewrite rules
        if (!$this->rewriteRuleExists($customFeedUrl)) {
            flush_rewrite_rules();
        }
    }

    /**
     * Retrieves the custom feed URL.
     *
     * @since 4.13
     *
     * @return string
     */
    protected function getCustomFeedUrl()
    {
        return (isset($this->settings['custom_feed_url']) && !empty($this->settings['custom_feed_url']))
            ? $this->settings['custom_feed_url']
            : $this->defaultUrl;
    }

    /**
     * Checks if the custom feed URL exists in the WordPress rewrite rules.
     *
     * @since 4.13
     *
     * @param string $customFeedUrl The custom feed URL.
     *
     * @return bool True if the rewrite rule exists, false if not.
     */
    protected function rewriteRuleExists($customFeedUrl)
    {
        // Get all registered rewrite rules
        $rules = get_option('rewrite_rules');

        // If there are existing rules
        if (is_array($rules)) {
            foreach ($rules as $key => $value) {
                if (strpos($key, $customFeedUrl) !== false && preg_match('/[?&]feed=\$matches\[\d]/', $value)) {
                    return true;
                }
            }
        }

        return false;
    }
}
