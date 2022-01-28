<?php

namespace ProfilePress\Core\NavigationMenuLinks;


if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


class PP_Nav_Items
{
    public $db_id = 0;
    public $object = 'ppnavlog';
    public $object_id;
    public $menu_item_parent = 0;
    public $type = 'custom';
    public $title;
    public $url;
    public $target = '';
    public $attr_title = '';
    public $classes = array();
    public $xfn = '';
}