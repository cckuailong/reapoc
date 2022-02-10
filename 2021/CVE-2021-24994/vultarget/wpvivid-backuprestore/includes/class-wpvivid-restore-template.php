<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
return array(
    'task_id' => '',
    'data' => array(
        WPVIVID_BACKUP_TYPE_CORE => array(
            'state' => WPVIVID_RESTORE_WAIT,
            'time' => array(
                'start' => 0,
                'end' => 0,
            ),
            'return' => array(
                'result' => '',
                'error' => '',
            ),
            'table' => array (
                'succeed' => 0,
                'failed' => 0,
                'unfinished' => 0,
            ),
        ),

        WPVIVID_BACKUP_TYPE_DB => array(
            'state' => WPVIVID_RESTORE_WAIT,
            'time' => array(
                'start' => 0,
                'end' => 0,
            ),
            'return' => array(
                'result' => '',
                'error' => '',
            ),
            'table' => array (
                'succeed' => 0,
                'failed' => 0,
                'unfinished' => 0,
            ),
        ),

        WPVIVID_BACKUP_TYPE_CONTENT => array(
            'state' => WPVIVID_RESTORE_WAIT,
            'time' => array(
                'start' => 0,
                'end' => 0,
            ),
            'return' => array(
                'result' => '',
                'error' => '',
            ),
            'table' => array (
                'succeed' => 0,
                'failed' => 0,
                'unfinished' => 0,
            ),
        ),

        WPVIVID_BACKUP_TYPE_PLUGIN => array(
            'state' => WPVIVID_RESTORE_WAIT,
            'time' => array(
                'start' => 0,
                'end' => 0,
            ),
            'return' => array(
                'result' => '',
                'error' => '',
            ),
            'table' => array (
                'succeed' => 0,
                'failed' => 0,
                'unfinished' => 0,
            ),
        ),

        WPVIVID_BACKUP_TYPE_UPLOADS => array(
            'state' => WPVIVID_RESTORE_WAIT,
            'time' => array(
                'start' => 0,
                'end' => 0,
            ),
            'return' => array(
                'result' => '',
                'error' => '',
            ),
            'table' => array (
                'succeed' => 0,
                'failed' => 0,
                'unfinished' => 0,
            ),
        ),

        WPVIVID_BACKUP_TYPE_THEMES => array(
            'state' => WPVIVID_RESTORE_WAIT,
            'time' => array(
                'start' => 0,
                'end' => 0,
            ),
            'return' => array(
                'result' => '',
                'error' => '',
            ),
            'table' => array (
                'succeed' => 0,
                'failed' => 0,
                'unfinished' => 0,
            ),
        ),
    ),
    'state' => WPVIVID_RESTORE_INIT,
    'error' => '',
    'error_task' => '',
    'backup_data' => '',
);