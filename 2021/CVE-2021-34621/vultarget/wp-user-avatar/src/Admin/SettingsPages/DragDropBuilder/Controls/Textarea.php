<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls;


class Textarea
{
    public $args;

    public function __construct($args)
    {
        $this->args = wp_parse_args(
            $args,
            ['name' => '', 'value' => sprintf('{{{data.%s}}}', $args['name'])]
        );
    }

    public function render()
    {
        echo sprintf('<label for="%s" class="pp-label">%s</label>', $this->args['name'], $this->args['label']);

        if (isset($this->args['description'])) {
            printf('<div class="pp-form-control-description">%s</div>', $this->args['description']);
        }

        echo sprintf(
            '<textarea placeholder="%3$s" id="%1$s" name="%1$s" class="pp-form-control">%2$s</textarea>',
            $this->args['name'],
            @$this->args['value'],
            @$this->args['placeholder']
        );
    }
}