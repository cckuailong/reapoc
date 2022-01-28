<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls;


class Select
{
    public $args;

    public function __construct($args)
    {
        $this->args = wp_parse_args(
            $args,
            ['name' => '', 'value' => '', 'options' => []]
        );
    }

    public function render()
    {
        echo sprintf('<label for="%s" class="pp-label">%s</label>', $this->args['name'], $this->args['label']);

        echo sprintf('<select class="pp-form-control" id="%1$s" name="%1$s">', $this->args['name']);

        foreach ($this->args['options'] as $key => $value) {

            $selected = sprintf(
                "<# if(data.%s ==  '%s') { #> selected <# } #>",
                $this->args['name'],
                $key
            );

            if (is_array($value)) {
                echo "<optgroup label='$key'>";
                foreach ($value as $key2 => $value2) {
                    echo sprintf('<option value="%s" %s>%s</option>', $key2, $selected, $value2);
                }
                echo '</optgroup>';
            } else {
                echo sprintf('<option value="%s" %s>%s</option>', $key, $selected, $value);
            }
        }

        echo '</select>';

        if (isset($this->args['description'])) {
            printf('<div class="pp-form-control-description">%s</div>', $this->args['description']);
        }
    }
}