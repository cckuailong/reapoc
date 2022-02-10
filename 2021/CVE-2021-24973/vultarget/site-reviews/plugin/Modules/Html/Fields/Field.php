<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

abstract class Field
{
    /**
     * @var \GeminiLabs\SiteReviews\Modules\Html\Builder
     */
    protected $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return \GeminiLabs\SiteReviews\Arguments
     */
    public function args()
    {
        return $this->builder->args;
    }

    /**
     * This is used to build a custom Field type.
     * @return string|void
     */
    public function build()
    {
        return $this->builder->build($this->tag(), $this->args()->toArray());
    }

    /**
     * @param string $fieldLocation
     * @return array
     */
    public static function defaults($fieldLocation = null)
    {
        return [];
    }

    /**
     * @param string $fieldLocation
     * @return array
     */
    public static function merge(array $args, $fieldLocation = null)
    {
        $merged = array_merge(
            wp_parse_args($args, static::defaults($fieldLocation)),
            static::required($fieldLocation)
        );
        $merged['class'] = implode(' ', static::mergedAttribute('class', ' ', $args, $fieldLocation));
        $merged['style'] = implode(';', static::mergedAttribute('style', ';', $args, $fieldLocation));
        return $merged;
    }

    /**
     * @param string $key
     * @param string $delimiter
     * @param string $fieldLocation
     * @return array
     */
    public static function mergedAttribute($key, $delimiter, array $args, $fieldLocation)
    {
        return Arr::unique(array_merge(
            explode($delimiter, Arr::get($args, $key)),
            explode($delimiter, Arr::get(static::defaults($fieldLocation), $key)),
            explode($delimiter, Arr::get(static::required($fieldLocation), $key))
        ));
    }

    /**
     * @param string $fieldLocation
     * @return array
     */
    public static function required($fieldLocation = null)
    {
        return [];
    }

    /**
     * @return string
     */
    public function tag()
    {
        return $this->builder->tag;
    }
}
