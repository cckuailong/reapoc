<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Checkbox extends Field
{
    /**
     * @param string $fieldLocation
     * @return array
     */
    public static function merge(array $args, $fieldLocation = null)
    {
        $field = glsr()->args(parent::merge($args, $fieldLocation));
        if (empty($field->options)) {
            $label = $field->get('label', $field->text);
            $value = $field->get('value', 1);
            $field->options = [$value => $label];
            $field->label = '';
            $field->value = '';
        }
        return $field->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public static function required($fieldLocation = null)
    {
        return [
            'is_multi' => true,
            'type' => 'checkbox',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function tag()
    {
        return 'input';
    }
}
