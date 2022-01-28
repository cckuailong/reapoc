<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC schedule class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_schedule extends MEC_base
{
    private $db;
    private $main;
    private $render;

    public function __construct()
    {
        $this->db = $this->getDB();
        $this->main = $this->getMain();
        $this->render = $this->getRender();
    }

    public function cron()
    {
        // Get All Events
        $events = $this->main->get_events();

        // Append Schedule for Events
        foreach($events as $event)
        {
            $maximum = 50;
            $repeat_type = get_post_meta($event->ID, 'mec_repeat_type', true);

            // Reschedule Schedule for Custom Days Events
            if($repeat_type === 'custom_days') $this->reschedule($event->ID, 200);
            else $this->append($event->ID, $maximum);
        }
    }

    public function reschedule($event_id, $maximum = 200)
    {
        // Clean Current Schedule
        $this->clean($event_id);

        // Event Start Date
        $start = get_post_meta($event_id, 'mec_start_date', true);

        if(trim($start) == '' or $start == '0000-00-00') $start = date('Y-m-d', strtotime('-1 Year'));
        else $start = date('Y-m-d', strtotime('-1 Day', strtotime($start)));

        // New Schedule
        $this->schedule($event_id, $start, $maximum);
    }

    public function append($event_id, $maximum = 25)
    {
        // Get Start Date
        $start = $this->time($event_id, 'max', 'Y-m-d');

        // Don't create dates more than next 7 years!
        if(strtotime($start) > strtotime('+7 years', current_time('timestamp', 0))) return;

        // Append Schedule
        $this->schedule($event_id, $start, $maximum);
    }

    public function schedule($event_id, $start, $maximum = 100)
    {
        // Get event dates
        $dates = $this->render->dates($event_id, NULL, $maximum, $start);

        // No new date found!
        if(!is_array($dates) or (is_array($dates) and !count($dates))) return false;

        // All Day Event
        $allday = get_post_meta($event_id, 'mec_allday', true);

        // Public Event
        $public = get_post_meta($event_id, 'mec_public', true);
        if(trim($public) === '') $public = 1;

        // Create Public Column If Not Exists
        if(!$this->db->columns('mec_dates', 'public')) $this->db->q("ALTER TABLE `#__mec_dates` ADD `public` INT(4) UNSIGNED NOT NULL DEFAULT 1 AFTER `tend`;");

        foreach($dates as $date)
        {
            $sd = $date['start']['date'];
            $ed = $date['end']['date'];

            $start_hour = isset($date['start']['hour']) ? sprintf("%02d", $date['start']['hour']) : '08';
            $start_minute = isset($date['start']['minutes']) ? sprintf("%02d", $date['start']['minutes']) : '00';
            $start_ampm = isset($date['start']['ampm']) ? $date['start']['ampm'] : 'AM';

            if($start_hour == '00')
            {
                $start_hour = '';
                $start_minute = '';
                $start_ampm = '';
            }

            $start_time = $start_hour.':'.$start_minute.' '.$start_ampm;

            $end_hour = isset($date['end']['hour']) ? sprintf("%02d", $date['end']['hour']) : '06';
            $end_minute = isset($date['end']['minutes']) ? sprintf("%02d", $date['end']['minutes']) : '00';
            $end_ampm = isset($date['end']['ampm']) ? $date['end']['ampm'] : 'PM';

            if($end_hour == '00')
            {
                $end_hour = '';
                $end_minute = '';
                $end_ampm = '';
            }

            $end_time = $end_hour.':'.$end_minute.' '.$end_ampm;

            // All Day Event
            if($allday)
            {
                $start_time = '12:01 AM';
                $end_time = '11:59 PM';
            }

            $st = strtotime(trim($date['start']['date'].' '.$start_time, ' :'));
            $et = strtotime(trim($date['end']['date'].' '.$end_time, ' :'));

            $date_id = $this->db->select("SELECT `id` FROM `#__mec_dates` WHERE `post_id`='$event_id' AND `tstart`='$st' AND `tend`='$et'", 'loadResult');

            // Add new Date
            if(!$date_id) $this->db->q("INSERT INTO `#__mec_dates` (`post_id`,`dstart`,`dend`,`tstart`,`tend`,`public`) VALUES ('$event_id','$sd','$ed','$st','$et','$public');");
            // Update Existing Record
            else $this->db->q("UPDATE `#__mec_dates` SET `tstart`='$st', `tend`='$et', `public`='$public' WHERE `id`='$date_id';");
        }

        return true;
    }

    public function clean($event_id)
    {
        // Remove All Scheduled Dates
        return $this->db->q("DELETE FROM `#__mec_dates` WHERE `post_id`='$event_id'");
    }

    public function time($event_id, $type = 'max', $format = 'Y-m-d')
    {
        $time = $this->db->select("SELECT ".(strtolower($type) == 'min' ? 'MIN' : 'MAX')."(`tstart`) FROM `#__mec_dates` WHERE `post_id`='$event_id'", 'loadResult');
        if(!$time) $time = time();

        return date($format, $time);
    }

    public function get_reschedule_maximum($repeat_type)
    {
        if($repeat_type == 'daily') return 370;
        elseif($repeat_type == 'weekday') return 270;
        elseif($repeat_type == 'advanced') return 250;
        elseif($repeat_type == 'weekend') return 200;
        elseif($repeat_type == 'certain_weekdays') return 200;
        elseif($repeat_type == 'weekly') return 100;
        elseif($repeat_type == 'monthly') return 50;
        elseif($repeat_type == 'yearly') return 50;
        else return 50;
    }
}