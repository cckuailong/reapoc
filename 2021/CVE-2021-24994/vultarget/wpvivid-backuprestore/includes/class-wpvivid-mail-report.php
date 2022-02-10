<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
class WPvivid_mail_report
{
    public static function send_report_mail($task,$log=false)
    {
        $option=WPvivid_Setting::get_option('wpvivid_email_setting');

        $option=apply_filters('wpvivid_get_mail_option_addon', $option);
        if(empty($option))
        {
            return true;
        }

        if($option['email_enable'] == 0){
            return true;
        }

        if(empty($option['send_to']))
        {
            return true;
        }

        if($task['status']['str']=='completed'&&$option['always']==false)
        {
            return true;
        }

        $headers = array('Content-Type: text/html; charset=UTF-8');

        $subject = '';
        $subject = apply_filters('wpvivid_set_mail_subject', $subject, $task);

        $body = '';
        $body = apply_filters('wpvivid_set_mail_body', $body, $task);

        $task_log=$task['options']['log_file_name'];

        if(isset($option['email_attach_log'])){
            if($option['email_attach_log'] == '1'){
                $attach_log = true;
            }
            else{
                $attach_log = false;
            }
        }
        else{
            $attach_log = true;
        }
        if($attach_log){
            $wpvivid_log=new WPvivid_Log();
            $log_file_name= $wpvivid_log->GetSaveLogFolder().$task_log.'_log.txt';
            $attachments[] = $log_file_name;
        }
        else{
            $attachments = array();
        }

        foreach ($option['send_to'] as $send_to)
        {
            if(wp_mail( $send_to, $subject, $body,$headers,$attachments)===false)
            {
                if($log!==false)
                {
                    $message=get_error_messages('wp_mail_failed');
                    $log->WriteLog($message,'error');
                }
            }
        }

        return true;
    }



    public static function create_subject($task)
    {
        $status=$task['status']['str'];
        if($status=='completed')
        {
            $status='Succeeded';
        }
        else
        {
            $status='Failed';
        }

        $offset=get_option('gmt_offset');
        $localtime=gmdate('m-d-Y H:i:s', $task['status']['start_time']+$offset*60*60);
        $header='[Backup '.$status.']'.$localtime.' - By WPvivid Backup Plugin';
        return $header;
    }

    public static function create_body($task)
    {
        $status=$task['status']['str'];
        if($status=='completed')
        {
            $status='succeeded';
        }
        else
        {
            $status='failed. '.$task['status']['error'];
        }
        $type=$task['type'];
        $offset=get_option('gmt_offset');
        $start_time=date("m-d-Y H:i:s",$task['status']['start_time']+$offset*60*60);
        $end_time=date("m-d-Y H:i:s",time()+$offset*60*60);
        $running_time=($task['status']['run_time']-$task['status']['start_time']).'s';
        $remote_options= $task['options']['remote_options'];
        if($remote_options!==false)
        {
            $remote_option=array_shift($remote_options);
            $remote=apply_filters('wpvivid_storage_provider_tran', $remote_option['type']);
        }
        else
        {
            $remote='Localhost';
        }
        $content='';

        $backup_options=$task['options']['backup_options'];
        if($backup_options!==false)
        {
            if(isset($backup_options['backup'][WPVIVID_BACKUP_TYPE_DB])&&isset($backup_options['backup'][WPVIVID_BACKUP_TYPE_THEMES]))
            {
                $content.='Entire Website';
            }
            else if(isset($backup_options['backup'][WPVIVID_BACKUP_TYPE_DB]))
            {
                $content.='Database';
            }
            else if(isset($backup_options['backup'][WPVIVID_BACKUP_TYPE_THEMES]))
            {
                $content.='All Files (Exclude Database)';
            }
        }
        else
        {
            $content='Upload';
        }

        $body='
        <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td style="padding-bottom:20px">
                <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">
                    <table align="center" style="border-spacing:0;color:#111111;Margin:0 auto;width:100%;max-width:600px" bgcolor="#F5F7F8">
                        <tbody>
				        <tr>
                            <td bgcolor="#F5F7F8" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                <table width="73%" style="border-spacing:0;color:#111111" bgcolor="#F5F7F8">
                                    <tbody>
			                        <tr>
                                        <td style="padding-top:20px;padding-bottom:0px;padding-left:10px;padding-right:40px;width:100%;text-align:center;font-size:32px;color:#2ea3f2;line-height:32px;font-weight:bold;">
                                            <span><img src="https://wpvivid.com/wp-content/uploads/2019/02/wpvivid-logo.png" title="WPvivid.com"></span>            
                                        </td>
                                    </tr>
                                    </tbody>
		                        </table>
                            </td>
                            <td width="100%" bgcolor="#F5F7F8" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                <table width="100%" style="border-spacing:0;color:#111111" bgcolor="#F5F7F8">
                                    <tbody>
                                    <tr>
                                        <td style="padding-top:10px;padding-bottom:0px;padding-left:10px;padding-right:0px;background-color:#f5f7f8;width:100%;text-align:right">
                                            <p style="Margin-top:0px;margin-bottom:0px;font-size:13px;line-height:16px"><strong><a href="https://twitter.com/wpvividcom" style="text-decoration:none;color:#111111" target="_blank">24/7 Support: <u></u>Twitter<u></u></a></strong></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top:0px;padding-bottom:0px;padding-left:10px;padding-right:0px;background-color:#f5f7f8;width:100%;text-align:right">
                                            <p class="m_764812426175198487customerinfo" style="Margin-top:5px;margin-bottom:0px;font-size:13px;line-height:16px">Or <u></u><a href="https://wpvivid.com/contact-us">Email Us</a><u></u></p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </td>
            </tr>
            </tbody>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td bgcolor="#F5F7F8" style="padding-top:0px;padding-bottom:0px">
                    <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">
                        <table bgcolor="#FFFFFF" align="center" style="border-spacing:0;color:#111111;margin:0 auto;width:100%;max-width:600px">
                            <tbody>
                            <tr>
                                <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                    <table width="100%" style="border-spacing:0;color:#111111">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:40px;padding-bottom:0px;padding-left:40px;padding-right:40px;background-color:#ffffff;width:100%;text-align:center;font-size:32px;line-height:42px;font-weight:bold;">
                                                <span>Wordpress Backup Report</span>            
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>            
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td bgcolor="#F5F7F8" style="padding-top:0px;padding-bottom:0px">
                    <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">
                        <table bgcolor="#FFFFFF" align="center" style="border-spacing:0;color:#111111;margin:0 auto;width:100%;max-width:600px">
                            <tbody>
                            <tr>
                                <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                    <table width="100%" style="border-spacing:0;color:#111111">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:20px;padding-bottom:0px;padding-left:0px;padding-right:0px;background-color:#ffffff;width:100%;text-align:left">
                                                <p style="margin-top:0px;line-height:0px;margin-bottom:0px;font-size:4px"> </p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td width="80" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                    <table width="80" style="border-spacing:0;color:#111111;border-bottom-color:#ffcca8;border-bottom-width:2px;border-bottom-style:solid">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:20px;padding-bottom:0px;padding-left:0px;padding-right:0px;background-color:#ffffff;width:100%;text-align:left">
                                                <p style="margin-top:0px;line-height:0px;margin-bottom:0px;font-size:4px"></p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                    <table width="100%" style="border-spacing:0;color:#111111">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:20px;padding-bottom:0px;padding-left:0px;padding-right:0px;background-color:#ffffff;width:100%;text-align:left">
                                                <p style="margin-top:0px;line-height:0px;margin-bottom:0px;font-size:4px"> </p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td bgcolor="#F5F7F8" style="padding-top:0px;padding-bottom:0px">
                    <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">
                        <table bgcolor="#FFFFFF" align="center" style="border-spacing:0;color:#111111;margin:0 auto;width:100%;max-width:600px">
                            <tbody>
                            <tr>
                                <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                    <table width="100%" style="border-spacing:0;color:#111111">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:40px;padding-bottom:0px;padding-left:40px;padding-right:40px;background-color:#ffffff;width:100%;text-align:left">
                                                <p style="gdsherpa-regular;margin-top:0px;font-size:14px;line-height:24px;margin-bottom:0px">
                                                    You receive this email because you have enabled the email notification feature in WPvivid plugin. Backup Details:
                                                </p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>   
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td bgcolor="#F5F7F8" style="background-color:#f5f7f8;padding-top:0;padding-right:0;padding-left:0;padding-bottom:0">
                    <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">		
                        <table bgcolor="#ffffff" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" style="color:#111111">
                            <tbody>
                            <tr>
                                <td bgcolor="#ffffff" align="left" style="padding-top:10px;padding-bottom:0;padding-right:40px;padding-left:40px;background-color:#ffffff">
                                    <table border="0" cellpadding="0" cellspacing="0" align="left" width="100%">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:10px;padding-right:0;padding-bottom:0;padding-left:20px">
                                                <table border="0" cellpadding="0" cellspacing="0" align="left">
                                                    <tbody>
                                                    <tr>
                                                        <td valign="top" align="left" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                                            <p style="text-align:left;Margin-top:0px;Margin-bottom:0px;gdsherpa-regular;font-size:14px;line-height:24px"><label>Backup:</label><label>'.$status.'</label></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" align="left" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                                            <p style="text-align:left;Margin-top:0px;Margin-bottom:0px;gdsherpa-regular;font-size:14px;line-height:24px"><label>Backup Type:</label><label>'.$type.'</label></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" align="left" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                                            <p style="text-align:left;Margin-top:0px;Margin-bottom:0px;gdsherpa-regular;font-size:14px;line-height:24px"><label>Start Time:</label><label>'.$start_time.'</label></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" align="left" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                                            <p style="text-align:left;Margin-top:0px;Margin-bottom:0px;gdsherpa-regular;font-size:14px;line-height:24px"><label>End Time:</label><label>'.$end_time.'</label></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" align="left" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                                            <p style="text-align:left;Margin-top:0px;Margin-bottom:0px;gdsherpa-regular;font-size:14px;line-height:24px"><label>Running Time:</label><label>'.$running_time.'</label></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" align="left" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                                            <p style="text-align:left;Margin-top:0px;Margin-bottom:0px;gdsherpa-regular;font-size:14px;line-height:24px"><label>Back up to:</label><label>'.$remote.'</label></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" align="left" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                                            <p style="text-align:left;Margin-top:0px;Margin-bottom:0px;gdsherpa-regular;font-size:14px;line-height:24px"><label>Backup Content:</label><label>'.$content.'</label></p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>                     
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>     
          
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td bgcolor="#F5F7F8" style="padding-top:0px;padding-bottom:0px">
                    <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">             
                        <table bgcolor="#FFFFFF" align="center" style="border-spacing:0;color:#111111;margin:0 auto;width:100%;max-width:600px">
                            <tbody>
                            <tr>
                                <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                    <table width="100%" style="border-spacing:0;color:#757575">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:20px;padding-bottom:0px;padding-left:40px;padding-right:40px;background-color:#ffffff;width:100%;text-align:left">
                                                <p style="gdsherpa-regular;margin-top:0px;font-size:14px;line-height:24px;margin-bottom:0px">
                                                    *WPvivid Backup plugin is a Wordpress plugin that it will help you back up your site to the leading cloud storage providers like Dropbox, Google Drive, Amazon S3, Microsoft OneDrive, FTP and SFTP.
                                                </p>
                                                <p style="gdsherpa-regular;margin-top:0px;font-size:14px;line-height:24px;margin-bottom:0px">
                                                    Plugin Page: <a href="https://wordpress.org/plugins/wpvivid-backuprestore/">https://wordpress.org/plugins/wpvivid-backuprestore/</a>
                                                </p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>     
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td bgcolor="#F5F7F8" style="padding-top:0px;padding-bottom:0px">
                    <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">
                        <table bgcolor="#FFFFFF" align="center" style="border-spacing:0;color:#111111;margin:0 auto;width:100%;max-width:600px">
                            <tbody>
                                <tr>
                                    <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                        <table width="100%" style="border-spacing:0;color:#111111">
                                            <tbody>
                                            <tr>
                                                <td style="padding-top:40px;padding-bottom:0px;padding-left:40px;padding-right:40px;background-color:#ffffff;width:100%;text-align:left">
                                                    <p style="margin-top:0px;line-height:0px;margin-bottom:0px;font-size:4px"></p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
                <tr>
                    <td bgcolor="#F5F7F8" style="background-color:#f5f7f8;padding-top:0;padding-right:0;padding-left:0;padding-bottom:0">
                        <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">
                            <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="color:#111111">
                                <tbody>
                                <tr>
                                    <td align="center" style="padding-top:40px;padding-bottom:0;padding-right:0px;padding-left:0px">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody>
                                            <tr>
                                                <td align="left" valign="bottom">
                                                    <img src="https://wpvivid.com/wp-content/uploads/2019/03/report-background.png" width="270" height="60" style="display:block;width:100%;max-width:270px;min-width:10px;height:60px" class="CToWUd">
                                                </td>
                                                <td width="60" valign="bottom">
                                                    <img src="https://wpvivid.com/wp-content/uploads/2019/03/female.png" width="60" height="60" style="display:block" class="CToWUd">
                                                </td>
                                                <td align="right" valign="bottom">
                                                    <img src="https://wpvivid.com/wp-content/uploads/2019/03/report-background.png" width="270" height="60" style="display:block;width:100%;max-width:270px;min-width:10px;height:60px" class="CToWUd">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>  
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table bgcolor="#FFFFFF" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" style="color:#111111">
                                <tbody>
                                <tr>
                                    <td bgcolor="#FFFFFF" align="left" style="padding-top:20px;padding-bottom:40px;padding-right:40px;padding-left:40px;background-color:#ffffff">     
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
                                            <tbody>
                                            <tr>
                                                <td align="center" style="padding-top:0px;padding-bottom:10px;padding-right:0;padding-left:0;text-align:center;font-size:18px;line-height:28px;font-weight:bold;">
                                                    <span>We\'re here to help you do your thing.</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="padding-top:0px;padding-bottom:0px;padding-right:0;padding-left:0;text-align:center">
                                                    <p style="text-align:center;margin-top:0px;margin-bottom:0px;gdsherpa-regular;;font-size:14px;line-height:24px">
                                                        <a href="https://wpvivid.com/contact-us">Contact Us</a> or <a href="https://twitter.com/wpvividcom">Twitter</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>        
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tbody>
                                    <tr>
                                        <td valign="top" style="font-size:0px;line-height:0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px">
                                            <img src="https://wpvivid.com/wp-content/uploads/2019/03/unnamed6.jpg" width="600" height="5" style="display:block;width:100%;max-width:600px;min-width:10px;height:5px">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>        
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F5F7F8">
            <tbody>
            <tr>
                <td bgcolor="#F5F7F8" style="padding-top:0px;padding-bottom:0px">
                    <div style="max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;padding-left:20px;padding-right:20px">
                        <table bgcolor="#F5F7F8" align="center" style="border-spacing:0;color:#111111;margin:0 auto;width:100%;max-width:600px">
                            <tbody>
                            <tr>
                                <td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0">
                                    <table width="100%" style="border-spacing:0;color:#111111">
                                        <tbody>
                                        <tr>
                                            <td style="padding-top:40px;padding-bottom:0px;padding-left:40px;padding-right:40px;background-color:#f5f7f8;width:100%;text-align:left">
                                                <p style="margin-top:0px;line-height:0px;margin-bottom:0px;font-size:4px">&nbsp;</p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>   
                    </div>
                </td>
            </tr>
            </tbody>
        </table>';
        return $body;
    }

    public static function wpvivid_send_debug_info($user_email,$server_type,$host_provider,$comment)
    {
        $send_to = 'support@wpvivid.com';
        $subject = 'Debug Information';
        $body = '<div>User\'s email: '.$user_email.'.</div>';
        $body .= '<div>Server type: '.$server_type.'.</div>';
        $body .= '<div>Host provider: '.$host_provider.'.</div>';
        $body .= '<div>Comment: '.$comment.'.</div>';
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $files=WPvivid_error_log::get_error_log();

        if (!class_exists('PclZip'))
            include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');

        $backup_path=WPvivid_Setting::get_backupdir();
        $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backup_path.DIRECTORY_SEPARATOR.'wpvivid_debug.zip';

        if(file_exists($path))
        {
            @unlink( $path);
        }
        $archive = new PclZip($path);

        if(!empty($files))
        {
            if(!$archive->add($files,PCLZIP_OPT_REMOVE_ALL_PATH))
            {
                echo $archive->errorInfo(true).' <a href="'.admin_url().'admin.php?page=WPvivid">retry</a>.';
                exit;
            }
        }

        global $wpvivid_plugin;
        $server_info=json_encode($wpvivid_plugin->get_website_info());
        $server_file_path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backup_path.DIRECTORY_SEPARATOR.'wpvivid_server_info.json';
        if(file_exists($server_file_path))
        {
            @unlink( $server_file_path);
        }
        $server_file = fopen($server_file_path, 'x');
        fclose($server_file);
        file_put_contents($server_file_path,$server_info);
        if(!$archive->add($server_file_path,PCLZIP_OPT_REMOVE_ALL_PATH))
        {
            echo $archive->errorInfo(true).' <a href="'.admin_url().'admin.php?page=WPvivid">retry</a>.';
            exit;
        }
        @unlink( $server_file_path);

        $attachments[] = $path;

        if(wp_mail( $send_to, $subject, $body,$headers,$attachments)===false)
        {
            $ret['result']='failed';
            $ret['error']=__('Unable to send email. Please check the configuration of email server.', 'wpvivid-backuprestore');
        }
        else
        {
            $ret['result']='success';
        }

        @unlink($path);
        return $ret;
    }
}