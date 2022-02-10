<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\WidgetBuilder;
use WP_Widget;

abstract class Widget extends WP_Widget
{
    /**
     * @var array
     */
    protected $mapped = [ // @compat 4.0
        'assign_to' => 'assigned_posts',
        'assigned_to' => 'assigned_posts',
        'category' => 'assigned_terms',
        'per_page' => 'display',
        'user' => 'assigned_users',
    ];

    /**
     * @var array
     */
    protected $widgetArgs;

    public function __construct()
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $className = str_replace('Widget', '', $className);
        $baseId = glsr()->prefix.Str::dashCase($className);
        parent::__construct($baseId, $this->widgetName(), $this->widgetOptions());
    }

    /**
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget($args, $instance)
    {
        echo $this->shortcode()->build($instance, $args, 'widget');
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function mapped($key)
    {
        $key = Arr::get($this->mapped, $key, $key);
        return Arr::get($this->widgetArgs, $key);
    }

    /**
     * @param string $tag
     * @return array
     */
    protected function normalizeFieldAttributes($tag, array $args)
    {
        if (empty($args['value'])) {
            $args['value'] = $this->mapped($args['name']);
        }
        if (empty($this->mapped('options')) && in_array($tag, ['checkbox', 'radio'])) {
            $args['checked'] = in_array($args['value'], (array) $this->mapped($args['name']));
        }
        $args['id'] = $this->get_field_id($args['name']);
        $args['name'] = $this->get_field_name($args['name']);
        return $args;
    }

    /**
     * @param string $tag
     * @return void
     */
    protected function renderField($tag, array $args = [])
    {
        $args = $this->normalizeFieldAttributes($tag, $args);
        echo glsr(WidgetBuilder::class)->p([
            'text' => glsr(WidgetBuilder::class)->$tag($args),
        ]);
    }

    /**
     * @return \GeminiLabs\SiteReviews\Shortcodes\Shortcode
     */
    abstract protected function shortcode();

    /**
     * @return string
     */
    protected function widgetDescription()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function widgetName()
    {
        return _x('Site Reviews: Unknown Widget', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    protected function widgetOptions()
    {
        return [
            'description' => $this->widgetDescription(),
            'name' => $this->widgetName(),
            'show_instance_in_rest' => true,
        ];
    }
}
