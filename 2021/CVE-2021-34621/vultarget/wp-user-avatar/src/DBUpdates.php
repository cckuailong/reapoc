<?php

namespace ProfilePress\Core;

class DBUpdates
{
    public static $instance;

    const DB_VER = 0;

    public function init_options()
    {
        get_option('ppress_db_ver', 0);
    }

    public function maybe_update()
    {
        $this->init_options();

        if (get_option('ppress_db_ver') >= self::DB_VER) {
            return;
        }

        // update plugin
        $this->update();
    }

    public function update()
    {
        // no PHP timeout for running updates
        set_time_limit(0);

        // this is the current database schema version number
        $current_db_ver = get_option('ppress_db_ver', 0);

        // this is the target version that we need to reach
        $target_db_ver = self::DB_VER;

        // run update routines one by one until the current version number
        // reaches the target version number
        while ($current_db_ver < $target_db_ver) {
            // increment the current db_ver by one
            $current_db_ver++;

            // each db version will require a separate update function
            $update_method = "ppress_update_routine_{$current_db_ver}";

            if (method_exists($this, $update_method)) {
                call_user_func(array($this, $update_method));
            }

            // update the option in the database, so that this process can always
            // pick up where it left off
            update_option('ppress_db_ver', $current_db_ver);
        }
    }

    public static function get_instance()
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}