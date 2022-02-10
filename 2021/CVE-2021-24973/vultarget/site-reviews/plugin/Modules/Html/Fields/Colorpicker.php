<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Colorpicker extends Field
{
    /**
     * @return \GeminiLabs\SiteReviews\Arguments
     */
    public function args()
    {
        // this is needed because merging field defaults filters unique array values
        $value = glsr_get_option($this->builder->args->path, $this->builder->args->default);
        $this->builder->args->set('value', $value);
        return $this->builder->args;
    }

    /**
     * This is used to build the custom Field type.
     * @return string|void
     */
    public function build()
    {
        if (!is_array($this->args()->default)) {
            return $this->builder->build($this->tag(), $this->args()->toArray());
        }
        $colours = [];
        foreach ($this->args()->default as $index => $default) {
            $colours[] = $this->buildColorField($index);
        }
        return $this->builder->div([
            'class' => 'glsr-color-pickers',
            'text' => implode('', $colours),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function required($fieldLocation = null)
    {
        return [
            'class' => 'glsr-color-picker color-picker-hex',
        ];
    }

    /**
     * @inheritDoc
     */
    public function tag()
    {
        return 'input';
    }

    /**
     * @param int $index
     * @return string
     */
    protected function buildColorField($index)
    {
        $args = Arr::consolidate(Arr::get($this->args()->repeat, (string) $index));
        $args = wp_parse_args($args, $this->args()->toArray());
        $args['default'] = Arr::get($this->args()->default, $index);
        $args['id'] = Str::suffix($this->args()->id, (string) $index);
        $args['name'] = Str::suffix($this->args()->name, '[]');
        $args['value'] = Arr::get($this->args()->value, $index);
        return $this->builder->input($args);
    }
}
