<?php

namespace RebelCode\Wpra\Core\Entities\Stores;

use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Util\SanitizerInterface;
use RebelCode\Wpra\Core\Util\Sanitizers\BoolSanitizer;
use RebelCode\Wpra\Core\Util\Sanitizers\CallbackSanitizer;
use WP_Post;

/**
 * A custom store implementation built specifically for builtin templates. This implementation intercerpts read and
 * write operations for the template's "options" key to store redundant information in the legacy display settings.
 *
 * @since 4.16
 */
class BuiltInTemplateStore extends WpPostStore
{
    /**
     * @since 4.16
     *
     * @var DataSetInterface
     */
    protected $settings;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param WP_Post          $wpPost   The post instance.
     * @param DataSetInterface $settings The settings dataset.
     */
    public function __construct(WP_Post $wpPost, DataSetInterface $settings)
    {
        parent::__construct($wpPost);

        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function get($key)
    {
        if ($key !== 'wprss_template_options') {
            return parent::get($key);
        }

        $aliases = $this->getSettingsAliases();
        $sanitizers = $this->getSettingsSanitizers();

        // Get the options from the post's meta
        $options = parent::has($key)
            ? parent::get($key)
            : [];

        foreach ($aliases as $alias => $key) {
            if (!isset($this->settings[$key])) {
                continue;
            }

            $value = $this->settings[$key];

            if (array_key_exists($key, $sanitizers)) {
                $value = $sanitizers[$key]->sanitize($value);
            }

            $options[$alias] = $value;
        }

        return $options;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function set(array $data)
    {
        if (array_key_exists('wprss_template_options', $data)) {
            $options = $data['wprss_template_options'];

            $aliases = $this->getSettingsAliases();
            foreach ($aliases as $alias => $key) {
                if (!array_key_exists($alias, $options)) {
                    continue;
                }

                $this->settings[$key] = $options[$alias];
            }
        }

        return parent::set($data);
    }

    /**
     * Retrieves the settings options aliases.
     *
     * @since 4.16
     *
     * @return string[]
     */
    protected function getSettingsAliases()
    {
        static $cache = null;

        return ($cache !== null)
            ? $cache
            : $cache = [
                'title_is_link' => 'title_link',
                'title_max_length' => 'title_limit',
                'limit' => 'feed_limit',
                'date_enabled' => 'date_enable',
                'date_prefix' => 'text_preceding_date',
                'date_format' => 'date_format',
                'date_use_time_ago' => 'time_ago_format_enable',
                'source_enabled' => 'source_enable',
                'source_prefix' => 'text_preceding_source',
                'source_is_link' => 'source_link',
                'author_enabled' => 'authors_enable',
                'pagination_type' => 'pagination',
                'links_nofollow' => 'follow_dd',
                'links_behavior' => 'open_dd',
                'links_video_embed_page' => 'video_link',
            ];
    }

    /**
     * Retrieves the sanitizers for the settings options.
     *
     * @since 4.16
     *
     * @return SanitizerInterface[]
     */
    protected function getSettingsSanitizers()
    {
        static $cache = null;

        return ($cache !== null)
            ? $cache
            : $cache = [
                'title_link' => new BoolSanitizer(),
                'date_enable' => new BoolSanitizer(),
                'time_ago_format_enable' => new BoolSanitizer(),
                'source_enable' => new BoolSanitizer(),
                'source_link' => new BoolSanitizer(),
                'authors_enable' => new BoolSanitizer(),
                'video_link' => new BoolSanitizer(),
                'follow_dd' => new CallbackSanitizer(function ($value) {
                    return $value === 'no_follow' || $value === true;
                }),
            ];
    }
}
