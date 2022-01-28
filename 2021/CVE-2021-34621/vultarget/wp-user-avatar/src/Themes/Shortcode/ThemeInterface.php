<?php

namespace ProfilePress\Core\Themes\Shortcode;


interface ThemeInterface
{
    public function get_name();

    public function get_structure();

    public function get_css();
}