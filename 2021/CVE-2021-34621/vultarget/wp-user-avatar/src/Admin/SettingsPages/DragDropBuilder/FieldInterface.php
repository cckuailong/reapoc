<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder;


interface FieldInterface
{
    public function field_type();

    public static function field_icon();

    public function field_title();

    public function field_bar_title();

    public function field_settings();

    public function field_settings_tabs();

    public function category();
}