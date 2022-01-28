<?php

namespace ProfilePress\Core\Themes\DragDrop\PasswordReset;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Themes\DragDrop\AbstractBuildScratch;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

class Tulip extends AbstractBuildScratch
{
    public static function default_field_listing()
    {
        Fields\Init::init();
        $standard_fields = DragDropBuilder::get_instance()->standard_fields();

        return [
            array_merge(
                $standard_fields['user-login'],
                ['icon' => 'face']
            ),
        ];
    }

    public function default_metabox_settings()
    {
        $data                                      = parent::default_metabox_settings();
        $data['buildscratch_field_layout']         = 'pill';
        $data['buildscratch_label_field_size']     = 'medium';
        $data['buildscratch_submit_button_layout'] = 'pill';
        $data['buildscratch_submit_button_width']  = 'full-width';

        return $data;
    }
}