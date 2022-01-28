<?php

namespace ProfilePress\Core\Themes\DragDrop\Registration;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

class BuildScratch extends AbstractBuildScratch
{
    public static function default_field_listing()
    {
        Fields\Init::init();
        $standard_fields = DragDropBuilder::get_instance()->standard_fields();

        return [
            $standard_fields['reg-username'],
            $standard_fields['reg-email'],
            $standard_fields['reg-password'],
            $standard_fields['reg-first-name'],
            $standard_fields['reg-last-name'],
        ];
    }
}