<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Staging_Setting_Free
{
    public $main_tab;

    public function __construct()
    {
        add_action('wp_ajax_wpvividstg_save_setting', array($this, 'save_setting'));
        add_filter('wpvivid_add_setting_tab_page', array($this, 'add_setting_tab_page'), 10);
        add_action('wpvivid_setting_add_staging_cell',array($this, 'output_staging_setting_cell'),10);
        add_filter('wpvivid_set_general_setting', array($this, 'set_general_setting'), 12, 3);
    }

    public function add_setting_tab_page($setting_array)
    {
        $setting_array['staging_setting'] = array('index' => '3', 'tab_func' =>  array($this, 'add_tab_staging'), 'page_func' => array($this, 'add_page_staging'));
        return $setting_array;
    }

    public function add_tab_staging()
    {
        ?>
        <a href="#" id="wpvivid_tab_staging_setting" class="nav-tab setting-nav-tab" onclick="switchsettingTabs(event,'page-staging-setting')"><?php _e('Staging Settings', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function add_page_staging()
    {
        ?>
        <div class="setting-tab-content wpvivid_tab_staging_setting" id="page-staging-setting" style="margin-top: 10px; display: none;">
            <?php do_action('wpvivid_setting_add_staging_cell'); ?>
        </div>
        <?php
    }

    public function output_staging_setting_cell()
    {
        $options=get_option('wpvivid_staging_options',array());

        $staging_db_insert_count   = isset($options['staging_db_insert_count']) ? $options['staging_db_insert_count'] : 10000;
        $staging_db_replace_count  = isset($options['staging_db_replace_count']) ? $options['staging_db_replace_count'] : 5000;
        $staging_file_copy_count   = isset($options['staging_file_copy_count']) ? $options['staging_file_copy_count'] : 500;
        $staging_exclude_file_size = isset($options['staging_exclude_file_size']) ? $options['staging_exclude_file_size'] : 30;
        $staging_memory_limit      = isset($options['staging_memory_limit']) ? $options['staging_memory_limit'] : '256M';
        $staging_memory_limit      = str_replace('M', '', $staging_memory_limit);
        $staging_max_execution_time= isset($options['staging_max_execution_time']) ? $options['staging_max_execution_time'] : 900;
        $staging_resume_count      = isset($options['staging_resume_count']) ? $options['staging_resume_count'] : 6;
        $staging_request_timeout      = isset($options['staging_request_timeout']) ? $options['staging_request_timeout'] : 1500;

        $staging_keep_setting      = isset($options['staging_keep_setting']) ? $options['staging_keep_setting'] : true;


        $staging_not_need_login=isset($options['not_need_login']) ? $options['not_need_login'] : true;
        if($staging_not_need_login)
        {
            $staging_not_need_login_check='checked';
        }
        else
        {
            $staging_not_need_login_check='';
        }
        $staging_overwrite_permalink = isset($options['staging_overwrite_permalink']) ? $options['staging_overwrite_permalink'] : true;
        if($staging_overwrite_permalink){
            $staging_overwrite_permalink_check = 'checked';
        }
        else{
            $staging_overwrite_permalink_check = '';
        }

        if($staging_keep_setting)
        {
            $staging_keep_setting='checked';
        }
        else
        {
            $staging_keep_setting='';
        }
        ?>
        <div style="margin-top: 10px;">
            <div class="postbox schedule-tab-block wpvivid-setting-addon" style="margin-bottom: 10px; padding-bottom: 0;">
                <div class="wpvivid-element-space-bottom"><strong><?php _e('DB Copy Count', 'wpvivid'); ?></strong></div>
                <div class="wpvivid-element-space-bottom">
                    <input type="text" class="all-options" option="setting" name="staging_db_insert_count" value="<?php esc_attr_e($staging_db_insert_count); ?>"
                           placeholder="10000" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="wpvivid-element-space-bottom">
                    <?php _e( 'Number of DB rows, that are copied within one ajax query. The higher value makes the database copy process faster. 
                Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until no 
                more errors occur.', 'wpvivid' ); ?>
                </div>

                <div class="wpvivid-element-space-bottom"><strong><?php _e('DB Replace Count', 'wpvivid'); ?></strong></div>
                <div class="wpvivid-element-space-bottom">
                    <input type="text" class="all-options" option="setting" name="staging_db_replace_count" value="<?php esc_attr_e($staging_db_replace_count); ?>"
                           placeholder="5000" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="wpvivid-element-space-bottom">
                    <?php _e( 'Number of DB rows, that are processed within one ajax query. The higher value makes the DB replacement process faster. 
                If timeout erros occur, decrease the value because this process consumes a lot of memory.', 'wpvivid' ); ?>
                </div>

                <div class="wpvivid-element-space-bottom"><strong><?php _e('File Copy Count', 'wpvivid'); ?></strong></div>
                <div class="wpvivid-element-space-bottom">
                    <input type="text" class="all-options" option="setting" name="staging_file_copy_count" value="<?php esc_attr_e($staging_file_copy_count); ?>"
                           placeholder="500" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="wpvivid-element-space-bottom">
                    <?php _e( 'Number of files to copy that will be copied within one ajax request. The higher value makes the file file copy process faster. 
                Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until no more errors occur.', 'wpvivid' ); ?>
                </div>

                <div class="wpvivid-element-space-bottom"><strong><?php _e('Max File Size', 'wpvivid'); ?></strong></div>
                <div class="wpvivid-element-space-bottom">
                    <input type="text" class="all-options" option="setting" name="staging_exclude_file_size" value="<?php esc_attr_e($staging_exclude_file_size); ?>"
                           placeholder="30" onkeyup="value=value.replace(/\D/g,'')" />MB
                </div>
                <div class="wpvivid-element-space-bottom">
                    <?php _e( 'Maximum size of the files copied to a staging site. All files larger than this value will be ignored. If you set the value of 0 MB, all files will be copied to a staging site.', 'wpvivid' ); ?>
                </div>

                <div class="wpvivid-element-space-bottom"><strong><?php _e('Staging Memory Limit', 'wpvivid'); ?></strong></div>
                <div class="wpvivid-element-space-bottom">
                    <input type="text" class="all-options" option="setting" name="staging_memory_limit" value="<?php esc_attr_e($staging_memory_limit); ?>"
                           placeholder="256" onkeyup="value=value.replace(/\D/g,'')" />MB
                </div>
                <div class="wpvivid-element-space-bottom">
                    <?php _e('Adjust this value to apply for a temporary PHP memory limit for the plugin to create a staging site. 
                We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some 
                web hosting providers may not support this.', 'wpvivid'); ?>
                </div>

                <div class="wpvivid-element-space-bottom"><strong><?php _e('PHP Script Execution Timeout', 'wpvivid'); ?></strong></div>
                <div class="wpvivid-element-space-bottom">
                    <input type="text" class="all-options" option="setting" name="staging_max_execution_time" value="<?php esc_attr_e($staging_max_execution_time); ?>"
                           placeholder="900" onkeyup="value=value.replace(/\D/g,'')" />
                </div>
                <div class="wpvivid-element-space-bottom">
                    <?php _e( 'The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut down the progress of 
                creating a staging site. If the progress  encounters a time-out, that means you have a medium or large sized website. Please try to 
                scale the value bigger.', 'wpvivid' ); ?>
                </div>

                <div class="wpvivid-element-space-bottom"><strong><?php _e('Delay Between Requests', 'wpvivid'); ?></strong></div>
                <div class="wpvivid-element-space-bottom">
                    <input type="text" class="all-options" option="setting" name="staging_request_timeout" value="<?php esc_attr_e($staging_request_timeout); ?>"
                           placeholder="1000" onkeyup="value=value.replace(/\D/g,'')" />ms
                </div>
                <div class="wpvivid-element-space-bottom">
                    <?php _e( 'A lower value will help speed up the process of creating a staging site. However, if your server has a limit on the number of requests, a higher value is recommended.', 'wpvivid' ); ?>
                </div>

                <div class="wpvivid-element-space-bottom">
                    <strong>Retrying </strong>
                    <select option="setting" name="staging_resume_count">
                        <?php
                        for($resume_count=3; $resume_count<10; $resume_count++){
                            if($resume_count === $staging_resume_count){
                                _e('<option selected="selected" value="'.$resume_count.'">'.$resume_count.'</option>');
                            }
                            else{
                                _e('<option value="'.$resume_count.'">'.$resume_count.'</option>');
                            }
                        }
                        ?>
                    </select><strong><?php _e(' times when encountering a time-out error', 'wpvivid'); ?></strong>
                </div>

                <div class="wpvivid-element-space-bottom">
                    <label>
                        <input type="checkbox" option="setting" name="not_need_login" <?php esc_attr_e($staging_not_need_login_check); ?> />
                        <span><strong><?php _e('Anyone can visit the staging site', 'wpvivid'); ?></strong></span>
                    </label>
                </div>

                <div class="wpvivid-element-space-bottom">
                    <span>When the option is checked, anyone will be able to visit the staging site without the need to login. Uncheck it to request a login to visit the staging site.</span>
                </div>

                <div class="wpvivid-element-space-bottom">
                    <label>
                        <input type="checkbox" option="setting" name="staging_overwrite_permalink" <?php esc_attr_e($staging_overwrite_permalink_check); ?> />
                        <span><strong><?php _e('Keep permalink when transferring website', 'wpvivid'); ?></strong></span>
                    </label>
                </div>

                <div class="wpvivid-element-space-bottom">
                    <span>When checked, this option allows you to keep the current permalink structure when you create a staging site or push a staging site to live.</span>
                </div>

                <div class="wpvivid-element-space-bottom">
                    <label>
                        <input type="checkbox" option="setting" name="staging_keep_setting" <?php esc_attr_e($staging_keep_setting); ?> />
                        <span><strong><?php _e('Keep staging sites when deleting the plugin', 'wpvivid'); ?></strong></span>
                    </label>
                </div>

                <div class="wpvivid-element-space-bottom">
                    <span>With this option checked, all staging sites you have created will be retained when the plugin is deleted, just in case you still need them later. The sites will show up again after the plugin is reinstalled.</span>
                </div>
            </div>
        </div>
        <?php
    }

    public function output_staging_setting()
    {
        ?>
        <div style="margin-top: 10px;">
            <?php
            $this->wpvivid_setting_add_staging_cell_addon();
            ?>
        </div>
        <?php
    }

    public function wpvivid_setting_add_staging_cell_addon()
    {
        $options=get_option('wpvivid_staging_options',array());

        $staging_db_insert_count   = isset($options['staging_db_insert_count']) ? $options['staging_db_insert_count'] : 10000;
        $staging_db_replace_count  = isset($options['staging_db_replace_count']) ? $options['staging_db_replace_count'] : 5000;
        $staging_file_copy_count   = isset($options['staging_file_copy_count']) ? $options['staging_file_copy_count'] : 500;
        $staging_exclude_file_size = isset($options['staging_exclude_file_size']) ? $options['staging_exclude_file_size'] : 30;
        $staging_memory_limit      = isset($options['staging_memory_limit']) ? $options['staging_memory_limit'] : '256M';
        $staging_memory_limit      = str_replace('M', '', $staging_memory_limit);
        $staging_max_execution_time= isset($options['staging_max_execution_time']) ? $options['staging_max_execution_time'] : 900;
        $staging_resume_count      = isset($options['staging_resume_count']) ? $options['staging_resume_count'] : 6;
        $staging_request_timeout      = isset($options['staging_request_timeout']) ? $options['staging_request_timeout'] : 1500;

        $staging_keep_setting      = isset($options['staging_keep_setting']) ? $options['staging_keep_setting'] : true;


        $staging_not_need_login=isset($options['not_need_login']) ? $options['not_need_login'] : true;
        if($staging_not_need_login)
        {
            $staging_not_need_login_check='checked';
        }
        else
        {
            $staging_not_need_login_check='';
        }
        $staging_overwrite_permalink = isset($options['staging_overwrite_permalink']) ? $options['staging_overwrite_permalink'] : true;
        if($staging_overwrite_permalink){
            $staging_overwrite_permalink_check = 'checked';
        }
        else{
            $staging_overwrite_permalink_check = '';
        }

        if($staging_keep_setting)
        {
            $staging_keep_setting='checked';
        }
        else
        {
            $staging_keep_setting='';
        }
        ?>
        <div class="postbox schedule-tab-block wpvivid-setting-addon" style="margin-bottom: 10px; padding-bottom: 0;">
            <div class="wpvivid-element-space-bottom"><strong><?php _e('DB Copy Count', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_db_insert_count" value="<?php esc_attr_e($staging_db_insert_count); ?>"
                       placeholder="10000" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Number of DB rows, that are copied within one ajax query. The higher value makes the database copy process faster. 
                Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until no 
                more errors occur.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('DB Replace Count', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_db_replace_count" value="<?php esc_attr_e($staging_db_replace_count); ?>"
                       placeholder="5000" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Number of DB rows, that are processed within one ajax query. The higher value makes the DB replacement process faster. 
                If timeout erros occur, decrease the value because this process consumes a lot of memory.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('File Copy Count', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_file_copy_count" value="<?php esc_attr_e($staging_file_copy_count); ?>"
                       placeholder="500" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Number of files to copy that will be copied within one ajax request. The higher value makes the file file copy process faster. 
                Please try a high value to find out the highest possible value. If you encounter timeout errors, try lower values until no more errors occur.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('Max File Size', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_exclude_file_size" value="<?php esc_attr_e($staging_exclude_file_size); ?>"
                       placeholder="30" onkeyup="value=value.replace(/\D/g,'')" />MB
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Maximum size of the files copied to a staging site. All files larger than this value will be ignored. If you set the value of 0 MB, all files will be copied to a staging site.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('Staging Memory Limit', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_memory_limit" value="<?php esc_attr_e($staging_memory_limit); ?>"
                       placeholder="256" onkeyup="value=value.replace(/\D/g,'')" />MB
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e('Adjust this value to apply for a temporary PHP memory limit for the plugin to create a staging site. 
                We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some 
                web hosting providers may not support this.', 'wpvivid'); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('PHP Script Execution Timeout', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_max_execution_time" value="<?php esc_attr_e($staging_max_execution_time); ?>"
                       placeholder="900" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut down the progress of 
                creating a staging site. If the progress  encounters a time-out, that means you have a medium or large sized website. Please try to 
                scale the value bigger.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom"><strong><?php _e('Delay Between Requests', 'wpvivid'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input type="text" class="all-options" option="setting" name="staging_request_timeout" value="<?php esc_attr_e($staging_request_timeout); ?>"
                       placeholder="1000" onkeyup="value=value.replace(/\D/g,'')" />ms
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'A lower value will help speed up the process of creating a staging site. However, if your server has a limit on the number of requests, a higher value is recommended.', 'wpvivid' ); ?>
            </div>

            <div class="wpvivid-element-space-bottom">
                <strong>Retrying </strong>
                <select option="setting" name="staging_resume_count">
                    <?php
                    for($resume_count=3; $resume_count<10; $resume_count++){
                        if($resume_count === $staging_resume_count){
                            _e('<option selected="selected" value="'.$resume_count.'">'.$resume_count.'</option>');
                        }
                        else{
                            _e('<option value="'.$resume_count.'">'.$resume_count.'</option>');
                        }
                    }
                    ?>
                </select><strong><?php _e(' times when encountering a time-out error', 'wpvivid'); ?></strong>
            </div>

            <div class="wpvivid-element-space-bottom">
                <label>
                    <input type="checkbox" option="setting" name="not_need_login" <?php esc_attr_e($staging_not_need_login_check); ?> />
                    <span><strong><?php _e('Anyone can visit the staging site', 'wpvivid'); ?></strong></span>
                </label>
            </div>

            <div class="wpvivid-element-space-bottom">
                <span>When the option is checked, anyone will be able to visit the staging site without the need to login. Uncheck it to request a login to visit the staging site.</span>
            </div>

            <div class="wpvivid-element-space-bottom">
                <label>
                    <input type="checkbox" option="setting" name="staging_overwrite_permalink" <?php esc_attr_e($staging_overwrite_permalink_check); ?> />
                    <span><strong><?php _e('Keep permalink when transferring website', 'wpvivid'); ?></strong></span>
                </label>
            </div>

            <div class="wpvivid-element-space-bottom">
                <span>When checked, this option allows you to keep the current permalink structure when you create a staging site or push a staging site to live.</span>
            </div>

            <div class="wpvivid-element-space-bottom">
                <label>
                    <input type="checkbox" option="setting" name="staging_keep_setting" <?php esc_attr_e($staging_keep_setting); ?> />
                    <span><strong><?php _e('Keep staging sites when deleting the plugin', 'wpvivid'); ?></strong></span>
                </label>
            </div>

            <div class="wpvivid-element-space-bottom">
                <span>With this option checked, all staging sites you have created will be retained when the plugin is deleted, just in case you still need them later. The sites will show up again after the plugin is reinstalled.</span>
            </div>
        </div>
        <div><input class="button-primary wpvividstg_save_setting" type="submit" value="<?php esc_attr_e( 'Save Changes', 'wpvivid' ); ?>" /></div>
        <script>
            jQuery('.wpvividstg_save_setting').click(function()
            {
                wpvividstg_save_setting();
            });

            function wpvividstg_save_setting()
            {
                var setting_data = wpvivid_ajax_data_transfer('setting');
                var ajax_data = {
                    'action': 'wpvividstg_save_setting',
                    'setting': setting_data,
                };
                jQuery('.wpvividstg_save_setting').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);

                        jQuery('.wpvividstg_save_setting').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success')
                        {
                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-setting'; ?>';
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err)
                    {
                        alert(err);
                        jQuery('.wpvividstg_save_setting').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('.wpvividstg_save_setting').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function set_general_setting($setting_data, $setting, $options)
    {
        $options=get_option('wpvivid_staging_options',array());

        if(isset($setting['staging_db_insert_count']))
            $options['staging_db_insert_count'] = intval($setting['staging_db_insert_count']);
        if(isset($setting['staging_db_replace_count']))
            $options['staging_db_replace_count'] = intval($setting['staging_db_replace_count']);
        if(isset($setting['staging_file_copy_count']))
            $options['staging_file_copy_count'] = intval($setting['staging_file_copy_count']);
        if(isset($setting['staging_exclude_file_size']))
        $options['staging_exclude_file_size'] = intval($setting['staging_exclude_file_size']);
        if(isset($setting['staging_memory_limit']))
            $options['staging_memory_limit'] = $setting['staging_memory_limit'].'M';
        if(isset($setting['staging_max_execution_time']))
            $options['staging_max_execution_time'] = intval($setting['staging_max_execution_time']);
        if(isset($setting['staging_resume_count']))
            $options['staging_resume_count'] = intval($setting['staging_resume_count']);
        if(isset($setting['not_need_login']))
            $options['not_need_login']= intval($setting['not_need_login']);
        if(isset($setting['staging_overwrite_permalink']))
            $options['staging_overwrite_permalink'] = intval($setting['staging_overwrite_permalink']);
        if(isset($setting['staging_request_timeout']))
            $options['staging_request_timeout']= intval($setting['staging_request_timeout']);
        if(isset($setting['staging_keep_setting']))
            $options['staging_keep_setting']= intval($setting['staging_keep_setting']);

        update_option('wpvivid_staging_options',$options);

        return $setting_data;
    }

    public function save_setting()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security('manage_options');
        $ret=array();
        try
        {
            if(isset($_POST['setting'])&&!empty($_POST['setting']))
            {
                $json_setting = $_POST['setting'];
                $json_setting = stripslashes($json_setting);
                $setting = json_decode($json_setting, true);
                if (is_null($setting))
                {
                    echo 'json decode failed';
                    die();
                }

                $options=get_option('wpvivid_staging_options',array());

                $options['staging_db_insert_count'] = intval($setting['staging_db_insert_count']);
                $options['staging_db_replace_count'] = intval($setting['staging_db_replace_count']);
                $options['staging_file_copy_count'] = intval($setting['staging_file_copy_count']);
                $options['staging_exclude_file_size'] = intval($setting['staging_exclude_file_size']);
                $options['staging_memory_limit'] = $setting['staging_memory_limit'].'M';
                $options['staging_max_execution_time'] = intval($setting['staging_max_execution_time']);
                $options['staging_resume_count'] = intval($setting['staging_resume_count']);
                $options['not_need_login']= intval($setting['not_need_login']);
                $options['staging_overwrite_permalink'] = intval($setting['staging_overwrite_permalink']);

                $options['staging_request_timeout']= intval($setting['staging_request_timeout']);
                $options['staging_keep_setting']= intval($setting['staging_keep_setting']);

                update_option('wpvivid_staging_options',$options);

                $ret['result']='success';
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }
}