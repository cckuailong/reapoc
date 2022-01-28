<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls;


class Input
{
    public $args;

    public function __construct($args)
    {
        $this->args = wp_parse_args(
            $args,
            ['type' => 'text', 'name' => '', 'value' => sprintf('{{{data.%s}}}', $args['name'])]
        );
    }

    public function render()
    {
        if ($this->args['type'] != 'checkbox') {
            printf('<label for="%s" class="pp-label">%s</label>', $this->args['name'], $this->args['label']);
        }

        if (isset($this->args['description']) && $this->args['type'] != 'checkbox') {
            printf('<div class="pp-form-control-description">%s</div>', $this->args['description']);
        }

        printf(
            '<input class="pp-form-control" type="%1$s" placeholder="%2$s" id="%3$s" name="%3$s" value="%4$s" %5$s>',
            $this->args['type'],
            @$this->args['placeholder'],
            $this->args['name'],
            @$this->args['value'],
            $this->args['type'] == 'checkbox' ? sprintf('<# if(data.%s === true) { #> checked <# } #>', $this->args['name']) : ''
        );

        if (isset($this->args['description']) && $this->args['type'] == 'checkbox') {
            printf('<label style="display: inline-block;margin-left: 5px" for="%s" class="pp-label">%s</label>', $this->args['name'], $this->args['label']);
            printf('<div class="pp-form-control-description">%s</div>', $this->args['description']);
        }
    }
}