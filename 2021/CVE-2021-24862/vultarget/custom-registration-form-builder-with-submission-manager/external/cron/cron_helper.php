<?php

//Author: Terrific

function rm_add_custom_interval($schedules)
{
    //error_log("Filter called");
    $schedules['chronos_interval'] = array(
        'interval' => 600, //in seconds
        'display' => __('RegMagic Chronos Interval'),
    );
    //error_log("Schedules: " . $result);

    return $schedules;
}
add_filter('cron_schedules', 'rm_add_custom_interval');



function rm_cron_job()
{
    //error_log("in the job");
    RM_Job_Manager::do_job();
    return;
}
add_action('rm_job_hook', 'rm_cron_job');

function rm_start_cron()
{
    if (!wp_next_scheduled('rm_job_hook'))
    {
        return wp_schedule_event(time(), 'chronos_interval', 'rm_job_hook');
      //  error_log("Chronos Scheduled!");
    } else {
        return 0;
    }
}

function rm_stop_cron()
{
    if (wp_next_scheduled('rm_job_hook'))
    {
        wp_unschedule_event(wp_next_scheduled('rm_job_hook'), 'rm_job_hook');
        update_option("rm_option_jobman_job",array());
        //error_log("stopped batch!");
    }
}

//Include this file and call rm_start_cron. Use Job Manager to add - remove jobs.
//rm_start_cron();