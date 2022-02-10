<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

class WPvivid_Schedule
{
    protected $schedule_type = array(
        'wpvivid_12hours'       =>  '12Hours',
        'twicedaily'             =>  '12Hours',
        'wpvivid_daily'         =>   'Daily',
        'daily'                  =>   'Daily',
        'onceday'                =>   'Daily',
        'wpvivid_weekly'        =>   'Weekly',
        'weekly'                 =>   'Weekly',
        'wpvivid_fortnightly'  =>   'Fortnightly',
        'fortnightly'           =>   'Fortnightly',
        'wpvivid_monthly'      =>   'Monthly',
        'monthly'               =>    'Monthly',
        'montly'                =>    'Monthly'
    );

    public function __construct()
    {
        add_action('wpvivid_reset_schedule', array($this, 'wpvivid_reset_schedule'), 10);
    }

    public function wpvivid_cron_schedules($schedules)
    {
        if(!isset($schedules["wpvivid_12hours"])){
            $schedules["wpvivid_12hours"] = array(
                'interval' => 3600*12,
                'display' => __('12 Hours'));
        }

        if(!isset($schedules["wpvivid_daily"])){
            $schedules["wpvivid_daily"] = array(
                'interval' => 86400 ,
                'display' => __('Daily'));
        }

        if(!isset($schedules["wpvivid_weekly"])){
            $schedules["wpvivid_weekly"] = array(
                'interval' => 604800 ,
                'display' => __('Weekly'));
        }

        if(!isset($schedules["wpvivid_fortnightly"])){
            $schedules["wpvivid_fortnightly"] = array(
                'interval' => 604800*2 ,
                'display' => __('Fortnightly'));
        }

        if(!isset($schedules["wpvivid_monthly"])){
            $schedules["wpvivid_monthly"] = array(
                'interval' => 2592000 ,
                'display' => __('Monthly'));
        }

        return $schedules;
    }

    public function check_schedule_type($display){
        $schedules = wp_get_schedules();
        $check_res = false;
        $ret = array();
        foreach ($this->schedule_type as $key => $value){
            if($value == $display){
                if(isset($schedules[$key])){
                    $check_res = true;
                    $ret['type']=$key;
                    break;
                }
            }
        }
        $ret['result']=$check_res;
        return $ret;
    }

    public function output($html)
    {
        $html='';

        $display_array = array("12Hours", "Daily", "Weekly", "Fortnightly", "Monthly");
        foreach($display_array as $display){
            $schedule_check = $this->check_schedule_type($display);
            if($schedule_check['result']){
                $html.=' <label><input type="radio" option="schedule" name="recurrence" value="'.$schedule_check['type'].'" />';
                if($display === '12Hours'){
                    $html.='<span>'.__('12Hours', 'wpvivid-backuprestore').'</span></label><br>';
                }
                if($display === 'Daily'){
                    $html.='<span>'.__('Daily', 'wpvivid-backuprestore').'</span></label><br>';
                }
                if($display === 'Weekly'){
                    $html.='<span>'.__('Weekly', 'wpvivid-backuprestore').'</span></label><br>';
                }
                if($display === 'Fortnightly'){
                    $html.='<span>'.__('Fortnightly', 'wpvivid-backuprestore').'</span></label><br>';
                }
                if($display === 'Monthly'){
                    $html.='<span>'.__('Monthly', 'wpvivid-backuprestore').'</span></label><br>';
                }
            }
            else{
                $html.='<p>Warning: Unable to set '.$display.' backup schedule</p>';
            }
        }
        $html.='<label>';
        $html.='<div style="float: left;">';
        $html.='<input type="radio" disabled />';
        $html.='<span class="wpvivid-element-space-right" style="color: #ddd;">'.__('Custom', 'wpvivid-backuprestore').'</span>';
        $html.='</div>';
        $html.='<div style="float: left; height: 32px; line-height: 32px;">';
        $html.='<span class="wpvivid-feature-pro">';
        $html.='<a href="https://docs.wpvivid.com/wpvivid-backup-pro-customize-start-time.html" style="text-decoration: none; margin-top: 10px;">'.__('Pro feature: learn more', 'wpvivid-backuprestore').'</a>';
        $html.='</span>';
        $html.='</div>';
        $html.='</label><br>';
        return $html;
    }

    public static function get_start_time($time)
    {
        if(!is_array( $time ) )
        {
            return false;
        }

        if(!isset($time['type']))
        {
            return false;
        }

        $week=$time['start_time']['week'];
        $day=$time['start_time']['day'];
        $current_day=$time['start_time']['current_day'];

        if(strtotime('now')>strtotime($current_day)){
            $daily_start_time = $current_day.' +1 day';
        }
        else{
            $daily_start_time = $current_day;
        }

        $weekly_tmp = $week.' '.$current_day;
        if(strtotime('now')>strtotime($weekly_tmp)) {
            $weekly_start_time = $week.' '.$weekly_tmp.' next week';
        }
        else{
            $weekly_start_time = $weekly_tmp;
        }

        $date_now = date("Y-m-",time());
        $monthly_tmp = $date_now.$day.' '.$current_day;
        if(strtotime('now')>strtotime($monthly_tmp)){
            $date_now = date("Y-m-",strtotime('+1 month'));
            $monthly_start_time = $date_now.$day.' '.$current_day;
        }
        else{
            $monthly_start_time = $monthly_tmp;
        }

        $schedule_type_ex = array(
            'wpvivid_12hours'       =>  '12Hours',
            'twicedaily'             =>  '12Hours',
            'wpvivid_daily'         =>   'Daily',
            'daily'                  =>   'Daily',
            'onceday'                =>   'Daily',
            'wpvivid_weekly'        =>   'Weekly',
            'weekly'                 =>   'Weekly',
            'wpvivid_fortnightly'  =>   'Fortnightly',
            'fortnightly'           =>   'Fortnightly',
            'wpvivid_monthly'      =>   'Monthly',
            'monthly'               =>    'Monthly',
            'montly'                =>    'Monthly'
        );

        $display_array = array(
            "12Hours"       =>  $daily_start_time,
            "Daily"         =>  $daily_start_time,
            "Weekly"        =>  $weekly_start_time,
            "Fortnightly"   =>  $weekly_start_time,
            "Monthly"       =>  $monthly_start_time
        );
        foreach ($schedule_type_ex as $key => $value){
            if($key == $time['type']){
                foreach ($display_array as $display_key => $display_value){
                    if($value == $display_key){
                        return strtotime($display_value);
                    }
                }
            }
        }
        return false;
    }

    public static function get_schedule($schedule_id = '')
    {
        add_filter('wpvivid_get_schedule', array('WPvivid_Schedule', 'get_schedule_ex'),10,2);
        $schedule=array();
        $schedule=apply_filters('wpvivid_get_schedule',$schedule,$schedule_id);
        return $schedule;
    }

    public static function get_schedule_ex($schedule,$schedule_id)
    {
        $schedule=WPvivid_Setting::get_option('wpvivid_schedule_setting');

        if(empty($schedule['backup']))
        {
            $schedule['backup']['backup_files']='files+db';
            $schedule['backup']['local']=1;
            $schedule['backup']['remote']=0;
            $schedule['backup']['ismerge']=1;
            $schedule['backup']['lock']=0;
        }

        $recurrence = wp_get_schedule(WPVIVID_MAIN_SCHEDULE_EVENT);

        if(!wp_get_schedule(WPVIVID_MAIN_SCHEDULE_EVENT))
        {
            $schedule['enable']=false;
            return $schedule;
        }

        $schedule['enable']=true;
        $schedule['recurrence']=$recurrence;
        $timestamp=wp_next_scheduled(WPVIVID_MAIN_SCHEDULE_EVENT);

        $schedule['next_start']=$timestamp;
        return $schedule;
    }

    public static function set_schedule($schedule_data,$schedule)
    {
        if($schedule['enable']==1)
        {
            $schedule_data['enable']=$schedule['enable'];

            $schedule_data['type']=$schedule['recurrence'];
            $schedule_data['event']=WPVIVID_MAIN_SCHEDULE_EVENT;
            $time['type']=$schedule['recurrence'];
            $time['start_time']['week']='mon';
            $time['start_time']['day']='01';
            $time['start_time']['current_day']="00:00";
            $timestamp=WPvivid_Schedule::get_start_time($time);
            $schedule_data['start_time']=$timestamp;

            $schedule_data['backup']['backup_files']=$schedule['backup_type'];
            if($schedule['save_local_remote']=='remote')
            {
                $schedule_data['backup']['local']=0;
                $schedule_data['backup']['remote']=1;
            }
            else
            {
                $schedule_data['backup']['local']=1;
                $schedule_data['backup']['remote']=0;
            }
            $schedule_data['backup']['ismerge']=1;
            $schedule_data['backup']['lock']=$schedule['lock'];
        }
        else
        {
            $schedule_data['enable']=$schedule['enable'];
        }

        return $schedule_data;
    }

    public static function set_schedule_ex($schedule)
    {
        add_filter('wpvivid_set_schedule', array('WPvivid_Schedule', 'set_schedule'),10,2);
        $schedule_data=array();
        $schedule_data= apply_filters('wpvivid_set_schedule',$schedule_data, $schedule);
        WPvivid_Setting::update_option('wpvivid_schedule_setting',$schedule_data);
        if($schedule_data===false)
        {
            $ret['result']='failed';
            $ret['error']=__('Creating scheduled tasks failed. Please try again later.', 'wpvivid-backuprestore');
            return $ret;
        }

        if($schedule_data['enable']==1)
        {
            if(wp_get_schedule($schedule_data['event']))
            {
                $timestamp = wp_next_scheduled($schedule_data['event']);
                wp_unschedule_event($timestamp,$schedule_data['event']);
            }
            if(wp_schedule_event($schedule_data['start_time'], $schedule_data['type'], $schedule_data['event'])===false)
            {
                $ret['result']='failed';
                $ret['error']=__('Creating scheduled tasks failed. Please try again later.', 'wpvivid-backuprestore');
                $ret['data']=$schedule_data;
                return $ret;
            }
            else
            {
                $ret['result']='success';
                $ret['data']=$schedule_data;
                return $ret;
            }
        }
        else
        {
            if(wp_get_schedule(WPVIVID_MAIN_SCHEDULE_EVENT))
            {
                wp_clear_scheduled_hook(WPVIVID_MAIN_SCHEDULE_EVENT);
                $timestamp = wp_next_scheduled(WPVIVID_MAIN_SCHEDULE_EVENT);
                wp_unschedule_event($timestamp,WPVIVID_MAIN_SCHEDULE_EVENT);
            }
            $ret['result']='success';
            $ret['data']=$schedule_data;
            return $ret;
        }

    }

    public function wpvivid_reset_schedule()
    {
        self::reset_schedule();
        return true;
    }

    public static function reset_schedule()
    {
        $schedule=WPvivid_Setting::get_option('wpvivid_schedule_setting');
        if(!empty($schedule))
        {
            if($schedule['enable'])
            {
                self::set_schedule_ex($schedule);
            }
            else
            {
                self::disable_schedule();
            }
        }
        else
        {
            self::disable_schedule();
        }

        return true;
    }

    public static function disable_schedule()
    {
        $schedule=WPvivid_Setting::get_option('wpvivid_schedule_setting');
        $schedule['enable']=false;
        WPvivid_Setting::update_option('wpvivid_schedule_setting',$schedule);
        if(wp_get_schedule(WPVIVID_MAIN_SCHEDULE_EVENT))
        {
            wp_clear_scheduled_hook(WPVIVID_MAIN_SCHEDULE_EVENT);
            $timestamp = wp_next_scheduled(WPVIVID_MAIN_SCHEDULE_EVENT);
            wp_unschedule_event($timestamp,WPVIVID_MAIN_SCHEDULE_EVENT);
        }
    }

    public static function clear_monitor_schedule($id)
    {
        $timestamp =wp_next_scheduled(WPVIVID_TASK_MONITOR_EVENT,array($id));
        if($timestamp!==false)
        {
            wp_unschedule_event($timestamp,WPVIVID_TASK_MONITOR_EVENT,array($id));
        }
    }

    public static function get_next_resume_time($id)
    {
        $timestamp=wp_next_scheduled(WPVIVID_RESUME_SCHEDULE_EVENT,array($id));
        if($timestamp!==false)
        {
            return $timestamp-time();
        }
        else
        {
            return false;
        }
    }
}
