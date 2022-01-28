<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls;


class IconPicker
{
    public $args;

    public function __construct($args)
    {
        $this->args = wp_parse_args(
            $args,
            ['value' => sprintf('{{data.%s}}', $args['name'])]
        );
    }

    public function render()
    {
        printf('<label for="%s" class="pp-label">%s</label>', $this->args['name'], $this->args['label']);

        if (isset($this->args['description'])) {
            printf('<div class="pp-form-control-description">%s</div>', $this->args['description']);
        }

        echo '<div class="pp-form-control-icon-picker-wrap">';

        printf(
            '<input style="display: none" class="pp-form-control" type="hidden" id="%1$s" name="%1$s" value="%2$s">',
            $this->args['name'],
            @$this->args['value']
        );

        echo '<div class="pp-form-control pp-form-control-icon-picker">';
        printf(
            '<# var material_icon = wp.template("pp-form-builder-material-icon")({icon:data.%1$s}); #> {{{ material_icon }}}',
            @$this->args['name']
        );
        echo '</div>';

        echo '</div>';
    }
}