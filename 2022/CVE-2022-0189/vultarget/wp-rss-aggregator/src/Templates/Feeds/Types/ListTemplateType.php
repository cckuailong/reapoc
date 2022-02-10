<?php

namespace RebelCode\Wpra\Core\Templates\Feeds\Types;

/**
 * An implementation for the list template type.
 *
 * @since 4.13
 */
class ListTemplateType extends AbstractWpraFeedTemplateType
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getKey()
    {
        return 'list';
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getName()
    {
        return __('List', 'wprss');
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function enqueueAssets()
    {
        $general_settings = get_option('wprss_settings_general');

        // Enqueue scripts
        wp_enqueue_script('jquery.colorbox-min', WPRSS_JS . 'jquery.colorbox-min.js', ['jquery']);
        wp_enqueue_script('wprss_custom', WPRSS_JS . 'custom.js', ['jquery', 'jquery.colorbox-min']);

        wp_enqueue_script('wpra-manifest', WPRSS_APP_JS . 'wpra-manifest.min.js', ['jquery'], WPRSS_VERSION);
        wp_enqueue_script('wpra-pagination', WPRSS_APP_JS . 'pagination.min.js', ['wpra-manifest'], WPRSS_VERSION);

        wp_localize_script('wpra-pagination', 'WpraPagination', [
            'baseUri' => rest_url('/wpra/v1/templates/%s/render/'),
        ]);

        if (empty($general_settings['styles_disable'])) {
            wp_enqueue_style('colorbox', WPRSS_CSS . 'colorbox.css', [], '1.4.33');
            wp_enqueue_style('wpra-list-template-styles', WPRSS_CSS . 'templates/list/styles.css', [], WPRSS_VERSION);
            wp_enqueue_style('wpra-pagination', WPRSS_APP_CSS . 'pagination.min.css', [], WPRSS_VERSION);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getOptions()
    {
        // Add the "limit" std option to save it in template models
        $stdOpts = $this->getStandardOptions();
        $limitOpt = $stdOpts['limit'];
        unset($limitOpt['key']);

        return [
            'limit' => $limitOpt,
            'title_max_length' => [
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
                'default' => 0,
            ],
            'title_is_link' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'flags' => [],
                'default' => true,
            ],
            'pagination' => [
                'key' => 'pagination_enabled',
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'pagination_type' => [
                'filter' => 'enum',
                'options' => ['default', 'numbered'],
                'default' => 'default',
            ],
            'source_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'source_prefix' => [
                'filter' => FILTER_DEFAULT,
                'default' => __('Source:', 'wprss'),
            ],
            'source_is_link' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'author_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'author_prefix' => [
                'filter' => FILTER_DEFAULT,
                'default' => __('By', 'wprss'),
            ],
            'date_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'date_prefix' => [
                'filter' => FILTER_DEFAULT,
                'default' => __('Published on:', 'wprss'),
            ],
            'date_format' => [
                'filter' => FILTER_DEFAULT,
                'default' => 'Y-m-d',
            ],
            'date_use_time_ago' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'bullets_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => true,
            ],
            'bullet_type' => [
                'filter' => 'enum',
                'options' => ['default', 'numbers'],
                'default' => 'default',
            ],
            'audio_player_enabled' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ]
        ];
    }
}
