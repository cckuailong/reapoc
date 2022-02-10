<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Fresh_Install_Create_UI_Display_Free
{
    public function __construct()
    {

    }

    public function output_themes_plugins_info($type)
    {
        $html = '';
        if($type === 'theme')
        {
            $themes_path = get_theme_root();
            $has_themes = false;
            $themes_table = '';
            $themes_table_html = '';
            $themes_info = array();

            $themes = wp_get_themes();

            if (!empty($themes))
            {
                $has_themes = true;
            }
            foreach ($themes as $theme)
            {
                $file = $theme->get_stylesheet();
                $parent=$theme->parent();

                $themes_info[$file] = $this->get_theme_plugin_info($themes_path . DIRECTORY_SEPARATOR . $file);
                $themes_info[$file]['parent']=$parent;
                $themes_info[$file]['parent_file']=$theme->get_template();
                $themes_info[$file]['child']=array();
                $current_theme=wp_get_theme();
                if($current_theme->get_stylesheet()==$file)
                {
                    $themes_info[$file]['active'] = 1;
                }
                else
                {
                    $themes_info[$file]['active'] = 0;
                }
            }

            foreach ($themes_info as $file => $info)
            {
                if($info['active']&&$info['parent']!=false)
                {
                    $themes_info[$info['parent_file']]['active']=1;
                    $themes_info[$info['parent_file']]['child'][]=$file;
                }
            }

            $themes_all_check = 'checked';
            foreach ($themes_info as $file => $info)
            {
                $checked = '';

                if ($info['active'] == 1)
                {
                    $checked = 'checked';
                }
                if (empty($checked)) {
                    $themes_all_check = '';
                }

                $themes_table .= '<div class="wpvivid-text-line"><input type="checkbox" option="create_wp" name="Themes" value="' . esc_attr($file) . '" '. esc_html($checked) .'>' . esc_html($file) . '</div>';
            }

            if ($has_themes)
            {
                $themes_table_html = $themes_table;
            }
            $html = $themes_table_html;
        }
        else{
            $has_plugins = false;
            $plugins_table = '';
            $plugins_table_html = '';
            $path = WP_PLUGIN_DIR;
            $plugin_info = array();

            if (!function_exists('get_plugins'))
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $plugins = get_plugins();

            if (!empty($plugins))
            {
                $has_plugins = true;
            }
            foreach ($plugins as $key => $plugin)
            {
                $slug = dirname($key);
                if ($slug == '.')
                    continue;
                $plugin_info[$slug] = $this->get_theme_plugin_info($path . DIRECTORY_SEPARATOR . $slug);
                $plugin_info[$slug]['Name'] = $plugin['Name'];
                $plugin_info[$slug]['slug'] = $slug;
                if($slug=='wpvivid-staging')
                {
                    $plugin_info[$slug]['active'] = 1;
                    $plugin_info[$slug]['disable'] = 1;
                }
                else
                {
                    $plugin_info[$slug]['active'] = 0;
                    $plugin_info[$slug]['disable'] = 0;
                }

            }

            $plugins_all_check='checked';

            foreach ($plugin_info as $slug => $info)
            {
                $disable_check = '';
                if ($info['disable']==1)
                {
                    $disable_check = 'disabled';
                }
                $checked = '';

                if ($info['active'] == 1)
                {
                    $checked = 'checked';
                }

                if (empty($checked)) {
                    $plugins_all_check = '';
                }

                $plugins_table .= '<div class="wpvivid-text-line"><input type="checkbox" option="create_wp" name="Plugins" value="' . esc_attr($info['slug']) . '" '. esc_html($checked) .'>' . esc_html($info['Name']) . '</div>';
            }

            if ($has_plugins)
            {
                $plugins_table_html = $plugins_table;
            }
            $html = $plugins_table_html;
        }
        return $html;
    }

    public function get_theme_plugin_info($root)
    {
        //$theme_info['size']=$this->get_folder_size($root,0);
        $theme_info['size']=0;
        return $theme_info;
    }

    public function get_folder_size($root,$size)
    {
        $count = 0;
        if(is_dir($root))
        {
            $handler = opendir($root);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..") {
                        $count++;

                        if (is_dir($root . DIRECTORY_SEPARATOR . $filename))
                        {
                            $size=$this->get_folder_size($root . DIRECTORY_SEPARATOR . $filename,$size);
                        } else {
                            $size+=filesize($root . DIRECTORY_SEPARATOR . $filename);
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }

        }
        return $size;
    }

    public function output_create_wp_page()
    {
        $options=get_option('wpvivid_staging_options',array());
        if(isset( $options['staging_request_timeout']))
        {
            $request_timeout=$options['staging_request_timeout'];
        }
        else
        {
            $request_timeout=1500;
        }

        update_option('wpvivid_current_running_staging_task','');
        update_option('wpvivid_staging_task_cancel', false);
        $home_url   = home_url();
        $admin_url  = admin_url();
        $admin_name = basename($admin_url);
        $admin_name = trim($admin_name, '/');

        $home_path = get_home_path();
        $staging_num = 1;
        $staging_dir = 'myfreshinstall01';
        $staging_content_dir = 'myfreshinstall01';
        $default_fresh_install_site = 'myfreshinstall01';
        while(1){
            $default_fresh_install_site = 'myfreshinstall'.sprintf("%02d", $staging_num);
            $staging_dir = $home_path.$default_fresh_install_site;
            if(!file_exists($staging_dir)){
                break;
            }
            $staging_num++;
        }

        $content_dir = WP_CONTENT_DIR;
        $content_dir = str_replace('\\','/',$content_dir);
        $content_path = $content_dir.'/';
        $staging_num = 1;
        $default_content_fresh_install_site='myfreshinstall01';
        while(1){
            $default_content_fresh_install_site = 'myfreshinstall'.sprintf("%02d", $staging_num);
            $staging_dir = $content_path.$default_content_fresh_install_site;
            if(!file_exists($staging_dir)){
                break;
            }
            $staging_num++;
        }

        global $wpdb;
        $prefix='';
        $site_id=1;
        $base_prefix=$wpdb->base_prefix;
        while(1)
        {
            if($site_id<10)
            {
                $prefix='wpvividfresh0'.$site_id.'_';
            }
            else
            {
                $prefix='wpvividfresh'.$site_id.'_';
            }

            $sql=$wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($prefix) . '%');
            $result = $wpdb->get_results($sql, OBJECT_K);
            if(empty($result))
            {
                break;
            }
            $site_id++;
        }
        $themes_plugins_descript = 'The activated plugins and themes will be copied to a fresh site by default. A Child theme must be copied if it exists.';
        ?>
        <div class="postbox quickstaging">
            <div id="wpvivid_create_new_wp_content">
                <div class="wpvivid-one-coloum" style="border:1px solid #f1f1f1;padding-bottom:0em; margin-top:0em;margin-bottom:1em;">
                    <div class="wpvivid-one-coloum" style="background:#f5f5f5;padding-top:0em;padding-bottom:0em;display: none;">
                        <div class="wpvivid-two-col">
                            <p><span class="dashicons dashicons-awards wpvivid-dashicons-blue"></span><span><strong>Site Name: </strong></span><span class="wpvivid-fresh-install-staging-site-name"><?php echo $default_fresh_install_site; ?></span></p>
                            <p><span class="dashicons dashicons-admin-site-alt3 wpvivid-dashicons-blue"></span><span><strong>Database Name: </strong></span><span class="wpvivid-staging-additional-database-name-display"><?php echo DB_NAME; ?></span></p>
                            <p><span class="dashicons dashicons-list-view wpvivid-dashicons-blue"></span><span><strong>Table Prefix: </strong></span><span class="wpvivid-staging-table-prefix-display"><?php echo $prefix; ?></span></p>
                        </div>
                        <div class="wpvivid-two-col">
                            <!--<p><span class="dashicons dashicons-admin-site-alt3 wpvivid-dashicons-blue"></span><span><strong>Database Name:</strong></span><span>admin06</span></p>-->
                            <p><span class="dashicons dashicons-admin-home wpvivid-dashicons-blue"></span><span><strong>Home URL: </strong></span><span class="wpvivid-fresh-install-home-url"><?php echo $home_url; ?>/</span><span class="wpvivid-fresh-install-staging-site-name"><?php echo $default_fresh_install_site; ?></span></p>
                            <p><span class="dashicons  dashicons-rest-api wpvivid-dashicons-blue"></span><span><strong>Admin URL: </strong></span><span class="wpvivid-fresh-install-home-url"><?php echo $home_url; ?>/</span><span class="wpvivid-fresh-install-staging-site-name"><?php echo $default_fresh_install_site; ?></span><span>/<?php echo $admin_name; ?></span></p>
                        </div>
                    </div>

                    <div>
                        <div>
                            <h2 style="padding-left:1em;padding-top:0.6em; background:#f1f1f1;">
                                <span class="dashicons dashicons-portfolio wpvivid-dashicons-orange"></span>
                                <span>Directory to Install the Fresh Install</span>
                            </h2>
                            <?php
                            $server_type = $_SERVER['SERVER_SOFTWARE'];
                            if(preg_match('/nginx/i', $server_type))
                            {
                                ?>
                                <div style="border:1px solid #ccc; padding:0 1em;margin-top:1em; border-radius:0.5em;">
                                    <p>
                                        <span>We detected that your web server is Nginx, please add specific rewrite rules to the Nginx config file for the staging site working properly. <a href="https://docs.wpvivid.com/add-rewrite-rules-to-nginx.html">How to</a></span>
                                    <p>
                                    <div style="clear:both;"></div>
                                </div>
                                <?php
                            }
                            ?>
                            <p>
                                <label>
                                    <input type="radio" option="create_wp" name="choose_create_staging_dir" value="0" checked="checked">
                                    <span>website root</span>
                                </label>
                                <label>
                                    <input type="radio" option="create_wp" name="choose_create_staging_dir" value="1">
                                    <span>/wp-content/</span>
                                </label>
                                <label>
                                    <input type="radio" option="create_wp" name="choose_create_staging_dir" value="2" disabled>
                                    <span>subdomain(pro feature)</span>
                                </label>
                            </p>

                            <div id="wpvivid_fresh_install_path_part" style="border-left: 4px solid #007cba;padding-left:1em;">
                                <p>
                                    <input type="text" option="create_wp" name="path" id="wpvivid_fresh_install_staging_path" placeholder="<?php esc_attr_e($default_fresh_install_site); ?>" value="<?php esc_attr_e($default_fresh_install_site); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')"><span> Custom directory</span>
                                </p>
                                <p>
                                    <span class="dashicons dashicons-admin-home wpvivid-dashicons-blue"></span><span>Home Url: </span><span class="wpvivid-fresh-install-home-url"><?php echo $home_url; ?>/</span><span class="wpvivid-fresh-install-staging-site-name"><?php echo $default_fresh_install_site; ?></span>
                                    <span style="margin-left:1em;" class="dashicons dashicons-portfolio wpvivid-dashicons-blue"></span><span><strong>Directory:</strong></span>
                                    <span><?php echo untrailingslashit(ABSPATH); ?>/</span><span class="wpvivid-fresh-install-staging-site-name"><?php echo $default_fresh_install_site; ?></span>
                                </p>
                            </div>
                        </div>

                        <h2 style="padding-left:1em;padding-top:0.6em;background:#f1f1f1;">
                            <span class="dashicons dashicons-cloud wpvivid-dashicons-blue"></span>
                            <span>Choose Database to Install the Fresh Install</span>
                        </h2>
                        <p>
                            <input type="text" option="create_wp" name="prefix" id="wpvivid_fresh_install_staging_table_prefix" placeholder="<?php esc_attr_e($prefix); ?>" value="<?php esc_attr_e($prefix); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9-_]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9-_]/g,'')" title="Table Prefix"> Custom Table Prefix, By default: <?php echo $prefix; ?>
                        </p>

                        <p>
                            <label>
                                <input type="radio" option="create_wp" name="choose_create_staging_db" value="0" checked="">
                                <span>Install the staging site to the live site's database (recommended)</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="radio" option="create_wp" name="choose_create_staging_db" value="1">
                                <span>Install the staging site to a separate database</span>
                            </label>
                        </p>
                        <p></p>
                        <div class="" id="wpvivid_fresh_install_additional_database_account" style="display: none;">
                            <form>
                                <p><label><input type="text" option="create_wp" name="database-name" autocomplete="off" placeholder="DB Name" title="DB Name" readonly></label>
                                    <label><input type="text" option="create_wp" name="database-user" autocomplete="off" placeholder="DB Username" title="DB Username" readonly></label></p>
                                <p><label><input type="password" option="create_wp" name="database-pass" autocomplete="off" placeholder="Password" title="The Password of the Database Username" readonly></label>
                                    <label><input type="text" option="create_wp" name="database-host" autocomplete="off" placeholder="localhost" title="Database Host" readonly></label></p>
                                <p><label><input class="button-primary wpvivid_setting_general_save" name="test-fresh-install-additional-db-btn" type="button" onclick="wpvivid_additional_database_connect_test_ex();" value="Test Connection" readonly></label></p>
                            </form>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
                <div style="clear: both;"></div>

                <div style="background:#f1f1f1;">
                    <h2 style="padding-left:1em;padding-top:0.6em;">
                        <span class="dashicons dashicons-grid-view wpvivid-dashicons-blue"></span>
                        <span>Themes And Plugins</span>
                    </h2>
                </div>
                <div>
                    <div class="wpvivid-two-col" style="padding:0.2em;">
                        <div style="padding:0 0 0.5em 0.2em;">
                            <span><span>Check All </span><input type="checkbox" name="wpvivid_check_all_fresh_install_themes"></span>
                        </div>
                        <div style="padding:0.3em;height:300px;overflow-y:auto; border:1px solid #ccc;">
                            <?php echo $this->output_themes_plugins_info('theme'); ?>
                        </div>
                    </div>
                    <div class="wpvivid-two-col" style="padding:0.2em;">
                        <div style="padding:0 0 0.5em 0.2em;">
                            <span><span>Check All </span><input type="checkbox" name="wpvivid_check_all_fresh_install_plugins"></span>
                        </div>
                        <div style="padding:0.3em;height:300px;overflow-y:auto;border:1px solid #ccc;">
                            <?php echo $this->output_themes_plugins_info('plugin'); ?>
                        </div>
                    </div>
                </div>

                <div style="clear: both;"></div>
                <div style="padding:1em 1em 0 0;">
                    <input class="button-primary wpvivid_setting_general_save" id="wpvivid_create_new_wp" type="submit" value="Create Now"><span> Note: Please don't refresh the page while creating a fresh install.</span>
                </div>
                <div style="padding:1em 1em 0 0;">
                    <span>Tips: Please temporarily deactivate all cache, firewall and redirect plugins before creating a staging site to rule out possibilities of unknown failures.</span>
                </div>
            </div>

            <div id="wpvivid_create_new_wp_progress" style="display: none;">
                <div class="wpvivid-element-space-bottom">
                    <input class="button button-primary" type="button" id="wpvivid_staging_cancel" value="Cancel" />
                </div>
                <div class="postbox wpvivid-staging-log wpvivid-element-space-bottom" id="wpvivid_fresh_install_staging_log" style="margin-bottom: 0;"></div>
                <div class="action-progress-bar" style="margin: 10px 0 0 0; !important;">
                    <div class="action-progress-bar-percent" id="wpvivid_fresh_install_staging_progress_bar" style="height:24px;line-height:24px;width:0;">
                        <div style="float: left; margin-left: 4px;">0</div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
            </div>
            <script>
                var home_url="<?php echo $home_url.'/'; ?>";
                var content_url="<?php echo $home_url.'/wp-content/'; ?>";
                var staging_requet_timeout=<?php echo $request_timeout ?>;

                var default_fresh_install_site = '<?php echo $default_fresh_install_site; ?>';
                var default_content_fresh_install_site = '<?php echo $default_content_fresh_install_site; ?>';

                jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_themes]').on("click", function(){
                    if(jQuery(this).prop('checked'))
                    {
                        jQuery('input:checkbox[option=create_wp][name=Themes]').prop('checked', true);
                    }
                    else
                    {
                        jQuery('input:checkbox[option=create_wp][name=Themes]').prop('checked', false);
                    }
                });

                jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_plugins]').on("click", function(){
                    if(jQuery(this).prop('checked'))
                    {
                        jQuery('input:checkbox[option=create_wp][name=Plugins]').prop('checked', true);
                    }
                    else
                    {
                        jQuery('input:checkbox[option=create_wp][name=Plugins]').prop('checked', false);
                    }
                });

                jQuery('input:checkbox[option=create_wp][name=Themes]').on("click", function(){
                    if(jQuery(this).prop('checked'))
                    {
                        var all_check = true;
                        jQuery('input:checkbox[option=create_wp][name=Themes]').each(function(){
                            if(!jQuery(this).prop('checked')){
                                all_check = false;
                            }
                        });
                        if(all_check) {
                            jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_themes]').prop('checked', true);
                        }
                        else {
                            jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_themes]').prop('checked', false);
                        }
                    }
                    else
                    {
                        jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_themes]').prop('checked', false);
                    }
                });

                jQuery('input:checkbox[option=create_wp][name=Plugins]').on("click", function(){
                    if(jQuery(this).prop('checked'))
                    {
                        var all_check = true;
                        jQuery('input:checkbox[option=create_wp][name=Plugins]').each(function(){
                            if(!jQuery(this).prop('checked')){
                                all_check = false;
                            }
                        });
                        if(all_check) {
                            jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_plugins]').prop('checked', true);
                        }
                        else {
                            jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_plugins]').prop('checked', false);
                        }
                    }
                    else
                    {
                        jQuery('input:checkbox[name=wpvivid_check_all_fresh_install_plugins]').prop('checked', false);
                    }
                });

                jQuery('#wpvivid_create_new_wp_content').on("click", 'input:radio[name=choose_create_staging_db]', function(){
                    if(jQuery(this).prop('checked')){
                        var value = jQuery(this).val();
                        if(value === '0'){
                            jQuery('#wpvivid_fresh_install_additional_database_account').hide();
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-name]').attr('readonly', true);
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-user]').attr('readonly', true);
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-pass]').attr('readonly', true);
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-host]').attr('readonly', true);
                            jQuery('#wpvivid_create_new_wp_content').find('.wpvivid-staging-additional-database-name-display').html('<?php echo DB_NAME; ?>');
                        }
                        else{
                            jQuery('#wpvivid_fresh_install_additional_database_account').show();
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-name]').attr('readonly', false);
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-user]').attr('readonly', false);
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-pass]').attr('readonly', false);
                            jQuery('#wpvivid_fresh_install_additional_database_account').find('input[name=database-host]').attr('readonly', false);
                            var additional_db_name = jQuery('.wpvivid-additional-database-name').val();
                            if(additional_db_name !== ''){
                                jQuery('#wpvivid_create_new_wp_content').find('.wpvivid-staging-additional-database-name-display').html(additional_db_name);
                            }
                            else{
                                jQuery('#wpvivid_create_new_wp_content').find('.wpvivid-staging-additional-database-name-display').html('*');
                            }
                            wpvivid_fresh_install_additional_database_table_prefix();
                        }
                    }
                });

                jQuery('#wpvivid_create_new_wp_content').on("click", 'input:radio[name=choose_create_staging_dir]', function()
                {
                    if(jQuery(this).prop('checked'))
                    {
                        var value = jQuery(this).val();

                        if(value === '0')
                        {
                            jQuery('.wpvivid-fresh-install-home-url').show();
                            jQuery('#wpvivid_fresh_install_path_part').show();
                            jQuery('#wpvivid_fresh_install_staging_path').val(default_fresh_install_site);
                            var staging_path = jQuery('#wpvivid_fresh_install_staging_path').val();
                            if(staging_path !== '')
                            {
                                jQuery('.wpvivid-fresh-install-staging-site-name').html(staging_path);
                            }
                            else{
                                jQuery('.wpvivid-fresh-install-staging-site-name').html('*');
                            }
                        }
                        else
                        {
                            jQuery('.wpvivid-fresh-install-home-url').show();
                            jQuery('#wpvivid_fresh_install_path_part').show();
                            jQuery('#wpvivid_fresh_install_staging_path').val(default_content_fresh_install_site);
                            var staging_path = jQuery('#wpvivid_fresh_install_staging_path').val();
                            if(staging_path !== '')
                            {
                                jQuery('.wpvivid-fresh-install-staging-site-name').html('wp-content/'+staging_path);
                            }
                            else{
                                jQuery('.wpvivid-fresh-install-staging-site-name').html('wp-content/*');
                            }
                        }
                    }
                });

                jQuery('#wpvivid_create_new_wp_content').on("keyup", '#wpvivid_fresh_install_staging_table_prefix', function(){
                    wpvivid_fresh_install_additional_database_table_prefix();
                });

                jQuery('#wpvivid_create_new_wp_content').on("keyup", '#wpvivid_fresh_install_staging_path', function() {
                    var value = jQuery('input:radio[name=choose_create_staging_dir]:checked').val();
                    if(value === '0')
                    {
                        var staging_path = jQuery('#wpvivid_fresh_install_staging_path').val();
                        if(staging_path !== ''){
                            jQuery('.wpvivid-fresh-install-staging-site-name').html(staging_path);
                        }
                        else{
                            jQuery('.wpvivid-fresh-install-staging-site-name').html('*');
                        }
                    }
                    else if(value === '1'){
                        var staging_path = jQuery('#wpvivid_fresh_install_staging_path').val();
                        if(staging_path !== ''){
                            jQuery('.wpvivid-fresh-install-staging-site-name').html('wp-content/'+staging_path);
                        }
                        else{
                            jQuery('.wpvivid-fresh-install-staging-site-name').html('wp-content/*');
                        }
                    }
                });

                jQuery('#wpvivid_create_new_wp').click(function() {
                    var descript = 'Click OK to start creating fresh WordPress install.';
                    var ret = confirm(descript);
                    if(ret === true)
                    {
                        wpvivid_create_new_wp();
                    }
                });

                function wpvivid_fresh_install_additional_database_table_prefix(){
                    var additional_db_prefix = jQuery('#wpvivid_create_new_wp_content').find('#wpvivid_fresh_install_staging_table_prefix').val();
                    if(additional_db_prefix !== ''){
                        jQuery('#wpvivid_create_new_wp_content').find('.wpvivid-staging-table-prefix-display').html(additional_db_prefix);
                    }
                    else{
                        jQuery('#wpvivid_create_new_wp_content').find('.wpvivid-staging-table-prefix-display').html('*');
                    }
                }

                function wpvivid_create_new_wp() {
                    var staging_root_dir='0';
                    jQuery('input[option=create_wp][name=choose_create_staging_dir]').each(function ()
                    {
                        if (jQuery(this).prop('checked'))
                        {
                            staging_root_dir = jQuery(this).val();
                        }
                    });

                    var table_prefix=jQuery('input[option=create_wp][name=prefix]').val();

                    if(table_prefix=='')
                    {
                        alert('Table Prefix is required.');
                        return ;
                    }

                    var path='';

                    var path=jQuery('input[option=create_wp][name=path]').val();

                    if(path === '')
                    {
                        alert('A site name is required.');
                        return;
                    }

                    var additional_database_json = {};

                    var additional_database_option = '0';
                    jQuery('input[option=create_wp][name=choose_create_staging_db]').each(function ()
                    {
                        if (jQuery(this).prop('checked'))
                        {
                            additional_database_option = jQuery(this).val();
                        }
                    });

                    if (additional_database_option === '1')
                    {
                        additional_database_json['additional_database_check'] = '1';
                        additional_database_json['additional_database_info'] = {};
                        additional_database_json['additional_database_info']['db_user'] = jQuery('input[option=create_wp][name=database-user]').val();
                        additional_database_json['additional_database_info']['db_pass'] = jQuery('input[option=create_wp][name=database-pass]').val();
                        additional_database_json['additional_database_info']['db_host'] = jQuery('input[option=create_wp][name=database-host]').val();
                        additional_database_json['additional_database_info']['db_name'] = jQuery('input[option=create_wp][name=database-name]').val();
                        if (additional_database_json['additional_database_info']['db_name'] === '')
                        {
                            alert('Database Name is required.');
                            return;
                        }
                        if (additional_database_json['additional_database_info']['db_user'] === '')
                        {
                            alert('Database User is required.');
                            return;
                        }
                        if (additional_database_json['additional_database_info']['db_host'] === '')
                        {
                            alert('Database Host is required.');
                            return;
                        }
                    }
                    else {
                        additional_database_json['additional_database_check'] = '0';
                    }
                    var additional_database_info=JSON.stringify(additional_database_json);

                    var ajax_data =
                        {
                            'action': 'wpvividstg_check_staging_dir_free',
                            'root_dir':staging_root_dir,
                            'path': path,
                            'table_prefix': table_prefix,
                            'additional_db': additional_database_info
                        };
                    wpvivid_post_request(ajax_data, function (data)
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                        }
                        else
                        {
                            var ajax_data =
                                {
                                    'action': 'wpvividstg_check_filesystem_permissions_free',
                                    'root_dir':staging_root_dir,
                                    'path': path
                                };
                            wpvivid_post_request(ajax_data, function (data)
                            {
                                var jsonarray = jQuery.parseJSON(data);
                                if (jsonarray.result === 'failed')
                                {
                                    alert(jsonarray.error);
                                }
                                else
                                {
                                    var custom_dir_json = wpvivid_get_custom_create_new_wp_option();
                                    var custom_dir = JSON.stringify(custom_dir_json);

                                    var ajax_data = {
                                        'action': 'wpvividstg_start_staging_free',
                                        'create_new_wp':true,
                                        'path': path,
                                        'table_prefix': table_prefix,
                                        'custom_dir': custom_dir,
                                        'additional_db': additional_database_info,
                                        'root_dir':staging_root_dir,
                                    };


                                    jQuery('#wpvivid_create_new_wp_content').hide();
                                    jQuery('#wpvivid_create_new_wp_progress').show();

                                    wpvivid_post_request(ajax_data, function (data)
                                    {
                                        setTimeout(function ()
                                        {
                                            wpvivid_get_create_new_wp_progress();
                                        }, staging_requet_timeout);
                                    }, function (XMLHttpRequest, textStatus, errorThrown)
                                    {
                                        jQuery('#wpvivid_create_new_wp_content').hide();
                                        jQuery('#wpvivid_create_new_wp_progress').show();
                                        setTimeout(function () {
                                            wpvivid_get_create_new_wp_progress();
                                        }, staging_requet_timeout);
                                    });
                                }
                            }, function (XMLHttpRequest, textStatus, errorThrown) {
                                var error_message = wpvivid_output_ajaxerror('creating staging site', textStatus, errorThrown);
                                alert(error_message);
                            });
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown) {
                        var error_message = wpvivid_output_ajaxerror('creating staging site', textStatus, errorThrown);
                        alert(error_message);
                    });
                }

                function wpvivid_get_create_new_wp_progress() {
                    var ajax_data = {
                        'action':'wpvividstg_get_staging_progress_free',
                    };

                    wpvivid_post_request(ajax_data, function(data)
                    {
                        try
                        {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success')
                            {
                                var log_data = jsonarray.log;
                                jQuery('#wpvivid_fresh_install_staging_log').html("");
                                while (log_data.indexOf('\n') >= 0)
                                {
                                    var iLength = log_data.indexOf('\n');
                                    var log = log_data.substring(0, iLength);
                                    log_data = log_data.substring(iLength + 1);
                                    var insert_log = "<div style=\"clear:both;\">" + log + "</div>";
                                    jQuery('#wpvivid_fresh_install_staging_log').append(insert_log);
                                    var div = jQuery('#wpvivid_fresh_install_staging_log');
                                    div[0].scrollTop = div[0].scrollHeight;
                                }
                                jQuery('#wpvivid_fresh_install_staging_progress_bar').css('width', jsonarray.percent + '%');
                                jQuery('#wpvivid_fresh_install_staging_progress_bar').find('div').eq(0).html(jsonarray.percent + '%');
                                if(jsonarray.continue)
                                {
                                    if(jsonarray.need_restart)
                                    {
                                        wpvivid_restart_create_new_wp();
                                    }
                                    else
                                    {
                                        setTimeout(function()
                                        {
                                            wpvivid_get_create_new_wp_progress();
                                        }, staging_requet_timeout);
                                    }
                                }
                                else
                                {
                                    if(typeof jsonarray.completed !== 'undefined' && jsonarray.completed)
                                    {
                                        jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                                        var percent = 100;
                                        jQuery('#wpvivid_fresh_install_staging_progress_bar').css('width', percent + '%');
                                        jQuery('#wpvivid_fresh_install_staging_progress_bar').find('div').eq(0).html(percent + '%');
                                        setTimeout(function()
                                        {
                                            alert('Creating a fresh WordPress install completed successfully.');
                                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvivid-staging'; ?>';
                                        }, 1000);
                                    }
                                    else if(typeof jsonarray.error !== 'undefined' && jsonarray.error)
                                    {
                                        alert(jsonarray.error);
                                        location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvivid-staging'; ?>';
                                    }
                                    else if(typeof jsonarray.is_cancel !== 'undefined' && jsonarray.is_cancel)
                                    {
                                        var staging_site_info = {};
                                        staging_site_info['staging_path'] = jsonarray.staging_path;
                                        staging_site_info['staging_additional_db'] = jsonarray.staging_additional_db;
                                        staging_site_info['staging_additional_db_user'] = jsonarray.staging_additional_db_user;
                                        staging_site_info['staging_additional_db_pass'] = jsonarray.staging_additional_db_pass;
                                        staging_site_info['staging_additional_db_host'] = jsonarray.staging_additional_db_host;
                                        staging_site_info['staging_additional_db_name'] = jsonarray.staging_additional_db_name;
                                        staging_site_info['staging_table_prefix'] = jsonarray.staging_table_prefix;
                                        staging_site_info = JSON.stringify(staging_site_info);
                                        ajax_data = {
                                            'action': 'wpvividstg_delete_cancel_staging_site_free',
                                            'staging_site_info': staging_site_info
                                        };
                                        wpvivid_post_request(ajax_data, function (data)
                                        {
                                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvivid-staging'; ?>';
                                        }, function (XMLHttpRequest, textStatus, errorThrown)
                                        {
                                            var error_message = wpvivid_output_ajaxerror('deleting fresh site', textStatus, errorThrown);
                                            alert(error_message);
                                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvivid-staging'; ?>';
                                        });
                                    }
                                    else{
                                        location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvivid-staging'; ?>';
                                    }
                                }
                            }
                            else if (jsonarray.result === 'failed')
                            {
                                jQuery('#wpvivid_create_new_wp_content').show();
                                jQuery('#wpvivid_create_new_wp_progress').hide();
                                alert(jsonarray.error);
                            }
                        }
                        catch(err)
                        {
                            setTimeout(function()
                            {
                                wpvivid_get_create_new_wp_progress();
                            }, 3000);
                        }

                    }, function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        setTimeout(function()
                        {
                            wpvivid_get_create_new_wp_progress();
                        }, 3000);
                    });
                }

                function wpvivid_restart_create_new_wp() {
                    var ajax_data = {
                        'action':'wpvividstg_start_staging_free',
                    };

                    wpvivid_post_request(ajax_data, function(data)
                    {
                        setTimeout(function()
                        {
                            wpvivid_get_create_new_wp_progress();
                        }, staging_requet_timeout);
                    }, function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        setTimeout(function()
                        {
                            wpvivid_get_create_new_wp_progress();
                        }, staging_requet_timeout);
                    });
                }

                function wpvivid_get_custom_create_new_wp_option() {
                    var json = {};
                    json['themes_list'] = Array();
                    json['plugins_list'] = Array();
                    json['themes_check'] = '0';
                    json['plugins_check'] = '0';
                    jQuery('input:checkbox[option=create_wp][name=Themes]').each(function()
                    {
                        if(jQuery(this).prop('checked'))
                        {
                            json['themes_check'] = '1';
                        }
                        else{
                            json['themes_list'].push(jQuery(this).val());
                        }
                    });
                    jQuery('input:checkbox[option=create_wp][name=Plugins]').each(function()
                    {
                        if(jQuery(this).prop('checked'))
                        {
                            json['plugins_check'] = '1';
                        }
                        else{
                            json['plugins_list'].push(jQuery(this).val());
                        }
                    });
                    return json;
                }

                function wpvivid_additional_database_connect_test_ex()
                {
                    var db_user =jQuery('input[option=create_wp][name=database-user]').val();
                    var db_pass =jQuery('input[option=create_wp][name=database-pass]').val();
                    var db_host =jQuery('input[option=create_wp][name=database-host]').val();
                    var db_name =jQuery('input[option=create_wp][name=database-name]').val();
                    if(db_name == '')
                    {
                        alert('Database Name is required.');
                        return;
                    }

                    if(db_user == '')
                    {
                        alert('Database User is required.');
                        return;
                    }

                    if(db_pass == '')
                    {
                        alert('Database Password is required.');
                        return;
                    }

                    if(db_host == '')
                    {
                        alert('Database Host is required.');
                        return ;
                    }

                    var db_json = {};
                    db_json['db_user'] = db_user;
                    db_json['db_pass'] = db_pass;
                    db_json['db_host'] = db_host;
                    db_json['db_name'] = db_name;
                    var db_connect_info = JSON.stringify(db_json);
                    var ajax_data = {
                        'action': 'wpvividstg_test_additional_database_connect_free',
                        'database_info': db_connect_info
                    };

                    wpvivid_post_request(ajax_data, function (data)
                    {
                        try
                        {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray !== null)
                            {
                                if (jsonarray.result === 'success')
                                {
                                    alert('Connection success.')
                                }
                                else
                                {
                                    alert(jsonarray.error);
                                }
                            }
                            else
                            {
                                alert('Connection Failed. Please check the credentials you entered and try again.');
                            }
                        }
                        catch (e)
                        {
                            alert('Connection Failed. Please check the credentials you entered and try again.');
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        var error_message = wpvivid_output_ajaxerror('connecting database', textStatus, errorThrown);
                        alert(error_message);
                    });
                }
            </script>
        </div>
        <?php
    }
}