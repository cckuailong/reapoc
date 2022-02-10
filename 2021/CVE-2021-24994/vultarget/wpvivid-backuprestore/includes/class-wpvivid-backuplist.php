<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
class WPvivid_Backuplist
{
    public static function get_backup_by_id($id)
    {
        $lists[]='wpvivid_backup_list';
        $lists=apply_filters('wpvivid_get_backuplist_name',$lists);
        foreach ($lists as $list_name)
        {
            $list = WPvivid_Setting::get_option($list_name);
            foreach ($list as $k=>$backup)
            {
                if ($id == $k)
                {
                    return $backup;
                }
            }
        }
        return false;
    }

    public static function update_backup_option($backup_id,$backup_new)
    {
        $lists[]='wpvivid_backup_list';
        $lists=apply_filters('wpvivid_get_backuplist_name',$lists);
        foreach ($lists as $list_name)
        {
            $list = WPvivid_Setting::get_option($list_name);
            foreach ($list as $k=>$backup)
            {
                if ($backup_id == $k)
                {
                    $list[$backup_id]=$backup_new;
                    WPvivid_Setting::update_option($list_name,$list);
                    return ;
                }
            }
        }
    }

    public static function get_backuplist($list_name='')
    {
        $list=array();
        add_filter('wpvivid_get_backuplist',array('WPvivid_Backuplist','get_backup_list'),10,2);
        $list=apply_filters('wpvivid_get_backuplist',$list,$list_name);
        return $list;
    }

    public static function get_backup_list($list,$list_name)
    {
        $list =  WPvivid_Setting::get_option('wpvivid_backup_list');
        $list =self::sort_list($list);

        return $list;
    }

    public static function get_backuplist_by_id($id){
        $list = array();
        add_filter('wpvivid_get_backuplist_by_id',array('WPvivid_Backuplist','get_backup_list_by_id'), 10 , 2);
        $ret=apply_filters('wpvivid_get_backuplist_by_id',$list,$id);
        return $ret;
    }

    public static function get_backup_list_by_id($list, $id)
    {
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        foreach ($list as $k=>$backup)
        {
            if ($id == $k)
            {
                $ret['list_name'] = 'wpvivid_backup_list';
                $ret['list_data'] = $list;
                return $ret;
            }
        }
        return false;
    }

    public static function get_backuplist_by_key($key)
    {
        add_filter('wpvivid_get_backuplist_item',array('WPvivid_Backuplist','get_backuplist_item'),10,2);
        $backup=false;
        $backup=apply_filters('wpvivid_get_backuplist_item',$backup,$key);
        return $backup;
    }

    public static function get_backuplist_item($backup,$key)
    {
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        foreach ($list as $k=>$backup)
        {
            if ($key == $k)
            {
                return $backup;
            }
        }
        return false;
    }

    public static function update_backup($id,$key,$data)
    {
        add_action('wpvivid_update_backup',array('WPvivid_Backuplist', 'update_backup_item'),10,3);
        do_action('wpvivid_update_backup',$id,$key,$data);
    }

    public static function update_backup_item($id,$key,$data)
    {
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        if(array_key_exists($id,$list))
        {
            $list[$id][$key]=$data;
            WPvivid_Setting::update_option('wpvivid_backup_list',$list);
        }
    }

    public static function add_new_upload_backup($task_id,$backup,$time,$log='')
    {
        $backup_data=array();
        $backup_data['type']='Upload';
        $backup_data['create_time']=$time;
        $backup_data['manual_delete']=0;
        $backup_data['local']['path']=WPvivid_Setting::get_backupdir();
        $backup_data['compress']['compress_type']='zip';
        $backup_data['save_local']=1;
        $backup_data['log']=$log;

        $backup_data['backup']=$backup;
        $backup_data['remote']=array();
        $backup_data['lock']=0;
        $backup_list='wpvivid_backup_list';

        $backup_list=apply_filters('get_wpvivid_backup_list_name',$backup_list,$task_id,$backup_data);

        $list = WPvivid_Setting::get_option($backup_list);
        $list[$task_id]=$backup_data;
        WPvivid_Setting::update_option($backup_list,$list);
    }

    public static function delete_backup($key)
    {
        $lists[]='wpvivid_backup_list';
        $lists=apply_filters('wpvivid_get_backuplist_name',$lists);
        foreach ($lists as $list_name)
        {
            $list = WPvivid_Setting::get_option($list_name);
            foreach ($list as $k=>$backup)
            {
                if ($key == $k)
                {
                    unset($list[$key]);
                    WPvivid_Setting::update_option($list_name, $list);
                    return;
                }
            }
        }
    }

    public static function sort_list($list)
    {
        uasort ($list,function($a, $b)
        {
            if($a['create_time']>$b['create_time'])
            {
                return -1;
            }
            else if($a['create_time']===$b['create_time'])
            {
                return 0;
            }
            else
            {
                return 1;
            }
        });

        return $list;
    }

    public static function get_oldest_backup_id($list)
    {
        $oldest_id='';
        $oldest=0;
        foreach ($list as $k=>$backup)
        {
            if(!array_key_exists('lock',$backup))
            {
                if ($oldest == 0)
                {
                    $oldest = $backup['create_time'];
                    $oldest_id = $k;
                } else {
                    if ($oldest > $backup['create_time'])
                    {
                        $oldest_id = $k;
                    }
                }
            }
        }
        return $oldest_id;
    }

    public static function check_backuplist_limit($max_count)
    {
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        $size=sizeof($list);
        if($size>=$max_count)
        {
            $oldest_id=self::get_oldest_backup_id($list);
            if(empty($oldest_id))
            {
                return false;
            }
            else
            {
                return $oldest_id;
            }
        }
        else
        {
           return false;
        }
    }

    public static function get_out_of_date_backuplist($max_count)
    {
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        $size=sizeof($list);
        $out_of_date_list=array();

        if($max_count==0)
            return $out_of_date_list;

        while($size>$max_count)
        {
            $oldest_id=self::get_oldest_backup_id($list);

            if(!empty($oldest_id))
            {
                $out_of_date_list[]=$oldest_id;
                unset($list[$oldest_id]);
            }
            $new_size=sizeof($list);
            if($new_size==$size)
            {
                break;
            }
            else
            {
                $size=$new_size;
            }
        }

        return $out_of_date_list;
    }

    public static function get_out_of_date_backuplist_info($max_count)
    {
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        $size=sizeof($list);
        $out_of_date_list['size']=0;
        $out_of_date_list['count']=0;

        if($max_count==0)
            return $out_of_date_list;

        while($size>$max_count)
        {
            $oldest_id=self::get_oldest_backup_id($list);

            if(!empty($oldest_id))
            {
                $out_of_date_list['size']+=self::get_size($oldest_id);
                $out_of_date_list['count']++;
                unset($list[$oldest_id]);
            }
            $new_size=sizeof($list);
            if($new_size==$size)
            {
                break;
            }
            else
            {
                $size=$new_size;
            }
        }

        return $out_of_date_list;
    }

    public static function get_size($backup_id)
    {
        $size=0;
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        $backup=$list[$backup_id];
        if(isset($backup['backup']['files'])){
            foreach ($backup['backup']['files'] as $file) {
                $size+=$file['size'];
            }
        }
        else{
            if(isset($backup['backup']['data']['type'])){
                foreach ($backup['backup']['data']['type'] as $type) {
                    foreach ($type['files'] as $file) {
                        $size+=$file['size'];
                    }
                }
            }
        }

        return $size;
    }
    public static function set_security_lock($backup_id,$lock)
    {
        //$list = WPvivid_Setting::get_option('wpvivid_backup_list');
        $ret = self::get_backuplist_by_id($backup_id);
        if($ret !== false) {
            $list = $ret['list_data'];
            if (array_key_exists($backup_id, $list)) {
                if ($lock == 1) {
                    $list[$backup_id]['lock'] = 1;
                }
                else {
                    if (array_key_exists('lock', $list[$backup_id])) {
                        unset($list[$backup_id]['lock']);
                    }
                }
            }
            WPvivid_Setting::update_option($ret['list_name'], $list);
        }

        $ret['result'] = 'success';
        $list = WPvivid_Setting::get_option($ret['list_name']);
        if (array_key_exists($backup_id, $list)) {
            if (isset($list[$backup_id]['lock'])) {
                if ($list[$backup_id]['lock'] == 1) {
                    $backup_lock = '/admin/partials/images/locked.png';
                    $lock_status = 'lock';
                    $ret['html'] = '<img src="' . esc_url(WPVIVID_PLUGIN_URL . $backup_lock) . '" name="' . esc_attr($lock_status, 'wpvivid-backuprestore') . '" onclick="wpvivid_set_backup_lock(\''.$backup_id.'\', \''.$lock_status.'\');" style="vertical-align:middle; cursor:pointer;"/>';
                } else {
                    $backup_lock = '/admin/partials/images/unlocked.png';
                    $lock_status = 'unlock';
                    $ret['html'] = '<img src="' . esc_url(WPVIVID_PLUGIN_URL . $backup_lock) . '" name="' . esc_attr($lock_status, 'wpvivid-backuprestore') . '" onclick="wpvivid_set_backup_lock(\''.$backup_id.'\', \''.$lock_status.'\');" style="vertical-align:middle; cursor:pointer;"/>';
                }
            } else {
                $backup_lock = '/admin/partials/images/unlocked.png';
                $lock_status = 'unlock';
                $ret['html'] = '<img src="' . esc_url(WPVIVID_PLUGIN_URL . $backup_lock) . '" name="' . esc_attr($lock_status, 'wpvivid-backuprestore') . '" onclick="wpvivid_set_backup_lock(\''.$backup_id.'\', \''.$lock_status.'\');" style="vertical-align:middle; cursor:pointer;"/>';
            }
        } else {
            $backup_lock = '/admin/partials/images/unlocked.png';
            $lock_status = 'unlock';
            $ret['html'] = '<img src="' . esc_url(WPVIVID_PLUGIN_URL . $backup_lock) . '" name="' . esc_attr($lock_status, 'wpvivid-backuprestore') . '" onclick="wpvivid_set_backup_lock(\''.$backup_id.'\', \''.$lock_status.'\');" style="vertical-align:middle; cursor:pointer;"/>';
        }
        return $ret;
    }

    public static function get_has_remote_backuplist()
    {
        $backup_id_list=array();
        $list = WPvivid_Setting::get_option('wpvivid_backup_list');
        foreach ($list as $k=>$backup)
        {
            if(!empty($backup['remote']))
            {
                $backup_id_list[]=$k;
            }
        }
        return $backup_id_list;
    }
}