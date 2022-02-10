<?php

namespace NotificationX\Core;

use NotificationX\Admin\Entries;
use NotificationX\Admin\Settings;
use NotificationX\Core\Database;
use NotificationX\GetInstance;


class Limiter {
    use GetInstance;

    /**
     * Initial Invoked
     */
    public function __construct() {
    }

    public function remove($nx_id, $new) {
        $count = Entries::get_instance()->count($nx_id, 'nx_id');
        $limit = Settings::get_instance()->get('settings.cache_limit', 100);
        if ($limit <= 0) {
            $limit = 100;
        }

        if ($new + $count > $limit) {
            $overflow = ($new + $count) - $limit;
            Entries::get_instance()->delete_entries($nx_id, $overflow);
        }
    }
}
