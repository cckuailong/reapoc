<?php

namespace ProfilePress\Core\Themes\DragDrop\EditProfile;

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
            $standard_fields['edit-profile-username'],
            $standard_fields['edit-profile-email'],
            $standard_fields['edit-profile-first-name'],
            $standard_fields['edit-profile-last-name'],
            $standard_fields['edit-profile-website'],
            $standard_fields['edit-profile-bio'],
        ];
    }
}