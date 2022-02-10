<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;

class FieldDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'after' => 'string',
        'class' => 'string',
        'id' => 'string',
        'label' => 'string',
        'name' => 'string',
        'options' => 'array',
        'text' => 'string',
        'type' => 'string',
        // 'value' => 'string', // disabled because checkbox field value can be an array
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'after' => 'text',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'class' => '',
            'id' => '',
            'label' => '',
            'name' => '',
            'options' => [],
            'text' => '',
            'type' => '',
            'value' => '',
        ];
    }

    /**
     * @return bool
     */
    protected function isMultiField(array $args)
    {
        $args = glsr()->args($args);
        if ('checkbox' === $args->type && count($args->cast('options', 'array')) > 1) {
            return true;
        }
        return Helper::ifTrue(isset($args->multiple), true, false);
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        if ($this->isMultiField($values) && !empty($values['name'])) {
            $values['name'] = Str::suffix($values['name'], '[]');
        }
        return $values;
    }
}
