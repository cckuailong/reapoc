<?php
defined("ABSPATH") or die("");

class DUP_GoDaddy_Host {
    public static function init() {
        add_filter('duplicator_defaults_settings', array('DUP_GoDaddy_Host', 'defaultsSettings'));
    }

    public static function defaultsSettings($defaults) {
        $defaults['archive_build_mode'] = DUP_Archive_Build_Mode::DupArchive;
        return $defaults;
    }
}

DUP_GoDaddy_Host::init();