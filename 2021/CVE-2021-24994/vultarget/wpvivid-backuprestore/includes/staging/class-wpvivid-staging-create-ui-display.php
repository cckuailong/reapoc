<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Staging_Create_UI_Display_Free
{
    public function __construct()
    {

    }

    public function get_database_home_url()
    {
        $home_url = home_url();
        global $wpdb;
        $home_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'home' ) );
        foreach ( $home_url_sql as $home ){
            $home_url = $home->option_value;
        }
        return untrailingslashit($home_url);
    }

    public function output_create_staging_site_page()
    {
        update_option('wpvivid_current_running_staging_task','');
        update_option('wpvivid_staging_task_cancel', false);
        $home_url   = $this->get_database_home_url();
        $admin_url  = admin_url();
        $admin_name = basename($admin_url);
        $admin_name = trim($admin_name, '/');

        $home_path = get_home_path();
        $staging_num = 1;
        $staging_dir = 'mystaging01';
        $staging_content_dir = 'mystaging01';
        $default_staging_site = 'mystaging01';
        while(1){
            $default_staging_site = 'mystaging'.sprintf("%02d", $staging_num);
            $staging_dir = $home_path.$default_staging_site;
            if(!file_exists($staging_dir)){
                break;
            }
            $staging_num++;
        }

        $content_dir = WP_CONTENT_DIR;
        $content_dir = str_replace('\\','/',$content_dir);
        $content_path = $content_dir.'/';
        $staging_num = 1;
        $default_content_staging_site='mystaging01';
        while(1){
            $default_content_staging_site = 'mystaging'.sprintf("%02d", $staging_num);
            $staging_dir = $content_path.$default_content_staging_site;
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
                $prefix='wpvividstg0'.$site_id.'_';
            }
            else
            {
                $prefix='wpvividstg'.$site_id.'_';
            }

            $sql=$wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($prefix) . '%');
            $result = $wpdb->get_results($sql, OBJECT_K);
            if(empty($result))
            {
                break;
            }
            $site_id++;
        }
        ?>
        <div class="postbox quickstaging">
            <div class="wpvivid-one-coloum" id="wpvivid_create_staging_step1" style="border:1px solid #f1f1f1;padding-bottom:0em; margin-top:0em;margin-bottom:1em;">
                <div class="wpvivid-one-coloum" style="background:#f5f5f5;padding-top:0em;padding-bottom:0em;display: none;">
                    <div class="wpvivid-two-col">
                        <p><span class="dashicons dashicons-awards wpvivid-dashicons-blue"></span><span><strong>Site Name: </strong></span><span class="wpvivid-staging-site-name"><?php echo $default_staging_site; ?></span></p>
                        <p><span class="dashicons dashicons-admin-site-alt3 wpvivid-dashicons-blue"></span><span><strong>Database Name: </strong></span><span class="wpvivid-staging-additional-database-name-display"><?php echo DB_NAME; ?></span></p>
                        <p><span class="dashicons dashicons-list-view wpvivid-dashicons-blue"></span><span><strong>Table Prefix: </strong></span><span class="wpvivid-staging-table-prefix-display"><?php echo $prefix; ?></span></p>
                    </div>
                    <div class="wpvivid-two-col">
                        <!--<p><span class="dashicons dashicons-admin-site-alt3 wpvivid-dashicons-blue"></span><span><strong>Database Name:</strong></span><span>admin06</span></p>-->
                        <p><span class="dashicons dashicons-admin-home wpvivid-dashicons-blue"></span><span><strong>Home URL: </strong></span><span class="wpvivid-staging-home-url"><?php echo $home_url; ?>/</span><span class="wpvivid-staging-site-name"><?php echo $default_staging_site; ?></span></p>
                        <p><span class="dashicons  dashicons-rest-api wpvivid-dashicons-blue"></span><span><strong>Admin URL: </strong></span><span class="wpvivid-staging-home-url"><?php echo $home_url; ?>/</span><span class="wpvivid-staging-site-name"><?php echo $default_staging_site; ?></span><span>/<?php echo $admin_name; ?></span></p>
                    </div>
                </div>

                <div>
                    <div>
                        <h2 style="padding-left:1em;padding-top:0.6em; background:#f1f1f1;">
                            <span class="dashicons dashicons-portfolio wpvivid-dashicons-orange"></span>
                            <span>Directory to Install the Staging Site</span>
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
                                <input type="radio" name="choose_staging_dir" value="0" checked="checked">
                                <span>website root</span>
                            </label>
                            <label>
                                <input type="radio" name="choose_staging_dir" value="1">
                                <span>/wp-content/</span>
                            </label>
                            <label>
                                <input type="radio" name="choose_staging_dir" value="2" disabled>
                                <span>subdomain(pro feature)</span>
                            </label>
                        </p>

                        <div id="wpvivid_staging_part" style="border-left: 4px solid #007cba;padding-left:1em;">
                            <p>
                                <input type="text" id="wpvivid_staging_path" placeholder="<?php esc_attr_e($default_staging_site); ?>" value="<?php esc_attr_e($default_staging_site); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')"><span> Custom directory</span>
                            </p>
                            <p>
                                <span class="dashicons dashicons-admin-home wpvivid-dashicons-blue"></span><span>Home Url: </span><span class="wpvivid-staging-home-url"><?php echo $home_url; ?>/</span><span class="wpvivid-staging-site-name"><?php echo $default_staging_site; ?></span>
                                <span style="margin-left:1em;" class="dashicons dashicons-portfolio wpvivid-dashicons-blue"></span><span><strong>Directory:</strong></span>
                                <span><?php echo untrailingslashit(ABSPATH); ?>/</span><span class="wpvivid-staging-site-name"><?php echo $default_staging_site; ?></span>
                            </p>
                        </div>
                    </div>

                    <h2 style="padding-left:1em;padding-top:0.6em;background:#f1f1f1;">
                        <span class="dashicons dashicons-cloud wpvivid-dashicons-blue"></span>
                        <span>Choose Database to Install the Staging Site</span>
                    </h2>
                    <p>
                        <input type="text" id="wpvivid_staging_table_prefix" placeholder="<?php esc_attr_e($prefix); ?>" value="<?php esc_attr_e($prefix); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9-_]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9-_]/g,'')" title="Table Prefix"> Custom Table Prefix, By default: <?php echo $prefix; ?>
                    </p>

                    <p>
                        <label>
                            <input type="radio" name="choose_staging_db" value="0" checked="">
                            <span>Install the staging site to the live site's database (recommended)</span>
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="radio" name="choose_staging_db" value="1">
                            <span>Install the staging site to a separate database</span>
                        </label>
                    </p>
                    <p></p>
                    <div class="" id="wpvivid_additional_database_account" style="display: none;">
                        <form>
                            <p><label><input type="text" class="wpvivid-additional-database-name" autocomplete="off" placeholder="DB Name" title="DB Name" readonly></label>
                                <label><input type="text" class="wpvivid-additional-database-user" autocomplete="off" placeholder="DB Username" title="DB Username" readonly></label></p>
                            <p><label><input type="password" class="wpvivid-additional-database-pass" autocomplete="off" placeholder="Password" title="The Password of the Database Username" readonly></label>
                                <label><input type="text" class="wpvivid-additional-database-host" autocomplete="off" placeholder="localhost" title="Database Host" readonly></label></p>
                            <p><label><input class="button-primary wpvivid_setting_general_save" type="button" id="wpvivid_connect_additional_database" onclick="wpvivid_additional_database_connect_test();" value="Test Connection" readonly></label></p>
                        </form>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>

            <div id="wpvivid_choose_staging_content" class="wpvivid-one-coloum" style="border:1px solid #f1f1f1;padding-bottom:1em; margin-top:1em;margin-bottom:1em;">
                <h2 style="padding-left:0em;">
                    <span class="dashicons dashicons-admin-page wpvivid-dashicons-orange"></span>
                    <span>Choose What to Copy to The Staging Site</span>
                </h2>
                <p></p>
                <div>
                    <div id="wpvividstg_custom_backup_content">
                        <div id="wpvivid_custom_staging_list">
                            <?php
                            $custom_staging_list = new WPvivid_Staging_Custom_Select_List_Free();
                            $custom_staging_list ->set_parent_id('wpvivid_custom_staging_list');
                            $custom_staging_list ->set_staging_home_path();
                            $custom_staging_list ->display_rows();
                            $custom_staging_list ->load_js();
                            ?>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>

            <div id="wpvivid_create_btn" style="padding:1em 1em 0 0;">
                <div id="wpvivid_create_staging_content">
                    <input class="button-primary wpvivid_setting_general_save" id="wpvivid_create_staging" type="submit" value="Create Now" /><span> Note: Please don't refresh the page while creating a staging site.</span>
                </div>
                <div style="padding:1em 1em 0 0;">
                    <span>Tips: Please temporarily deactivate all cache, firewall and redirect plugins before creating a staging site to rule out possibilities of unknown failures.</span>
                </div>
            </div>

            <div id="wpvivid_create_staging_step2" style="display: none;">
                <div class="wpvivid-element-space-bottom">
                    <input class="button button-primary" type="button" id="wpvivid_staging_cancel" value="Cancel" />
                </div>
                <div class="postbox wpvivid-staging-log wpvivid-element-space-bottom" id="wpvivid_staging_log" style="margin-bottom: 0;"></div>
                <div class="action-progress-bar" style="margin: 10px 0 0 0; !important;">
                    <div class="action-progress-bar-percent" id="wpvivid_staging_progress_bar" style="height:24px;line-height:24px;width:0;">
                        <div style="float: left; margin-left: 4px;">0</div>
                        <div style="clear: both;"></div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            <?php
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'];
            $upload_path = str_replace('\\','/',$upload_path);
            $upload_path = $upload_path.'/';
            $content_dir = WP_CONTENT_DIR;
            $content_path = str_replace('\\','/',$content_dir);
            $content_path = $content_path.'/';
            $home_path = str_replace('\\','/', get_home_path());
            $theme_path = str_replace('\\','/', get_theme_root());
            $theme_path = $theme_path.'/';
            $plugin_path = str_replace('\\','/', WP_PLUGIN_DIR);
            $plugin_path = $plugin_path.'/';
            ?>

            jQuery('#wpvivid_create_staging_step1').on("keyup", '#wpvivid_staging_path', function()
            {
                var value = jQuery('#wpvivid_create_staging_step1').find('input:radio[name=choose_staging_dir]:checked').val();
                if(value === '0')
                {
                    var staging_path = jQuery('#wpvivid_staging_path').val();
                    if(staging_path !== ''){
                        jQuery('.wpvivid-staging-site-name').html(staging_path);
                    }
                    else{
                        jQuery('.wpvivid-staging-site-name').html('*');
                    }
                }
                else if(value === '1')
                {
                    var staging_path = jQuery('#wpvivid_staging_path').val();
                    if(staging_path !== '')
                    {
                        jQuery('.wpvivid-staging-site-name').html('wp-content/'+staging_path);
                    }
                    else{
                        jQuery('.wpvivid-staging-site-name').html('wp-content/*');
                    }
                }
            });


            jQuery('#wpvivid_create_staging_step1').on("click", 'input:radio[name=choose_staging_db]', function(){
                if(jQuery(this).prop('checked')){
                    var value = jQuery(this).val();
                    if(value === '0'){
                        jQuery('#wpvivid_additional_database_account').hide();
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-name').attr('readonly', true);
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-user').attr('readonly', true);
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-pass').attr('readonly', true);
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-host').attr('readonly', true);
                        jQuery('.wpvivid-staging-additional-database-name-display').html('<?php echo DB_NAME; ?>');
                    }
                    else{
                        jQuery('#wpvivid_additional_database_account').show();
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-name').attr('readonly', false);
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-user').attr('readonly', false);
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-pass').attr('readonly', false);
                        jQuery('#wpvivid_additional_database_account').find('.wpvivid-additional-database-host').attr('readonly', false);
                        var additional_db_name = jQuery('.wpvivid-additional-database-name').val();
                        if(additional_db_name !== ''){
                            jQuery('.wpvivid-staging-additional-database-name-display').html(additional_db_name);
                        }
                        else{
                            jQuery('.wpvivid-staging-additional-database-name-display').html('*');
                        }
                        wpvivid_additional_database_table_prefix();
                    }
                }
            });

            var default_staging_site = '<?php echo $default_staging_site; ?>';
            var default_content_staging_site = '<?php echo $default_content_staging_site; ?>';
            var is_mu='<?php echo is_multisite(); ?>';
            jQuery('#wpvivid_create_staging_step1').on("click", 'input:radio[name=choose_staging_dir]', function() {
                if(jQuery(this).prop('checked'))
                {
                    var value = jQuery(this).val();

                    if(value === '0')
                    {
                        jQuery('.wpvivid-staging-home-url').show();
                        jQuery('#wpvivid_staging_path_part').show();
                        jQuery('#wpvivid_staging_path').val(default_staging_site);
                        var staging_path = jQuery('#wpvivid_staging_path').val();
                        if(staging_path !== '')
                        {
                            jQuery('.wpvivid-staging-site-name').html(staging_path);
                        }
                        else{
                            jQuery('.wpvivid-staging-site-name').html('*');
                        }
                    }
                    else
                    {
                        jQuery('.wpvivid-staging-home-url').show();
                        jQuery('#wpvivid_staging_path_part').show();
                        jQuery('#wpvivid_staging_path').val(default_content_staging_site);
                        var staging_path = jQuery('#wpvivid_staging_path').val();
                        if(staging_path !== '')
                        {
                            jQuery('.wpvivid-staging-site-name').html('wp-content/'+staging_path);
                        }
                        else{
                            jQuery('.wpvivid-staging-site-name').html('wp-content/*');
                        }
                    }
                }
            });

            jQuery('#wpvivid_create_staging_step1').on("keyup", '.wpvivid-additional-database-name', function(){
                var additional_db_name = jQuery(this).val();
                if(additional_db_name !== ''){
                    jQuery('.wpvivid-staging-additional-database-name-display').html(additional_db_name);
                }
                else{
                    jQuery('.wpvivid-staging-additional-database-name-display').html('*');
                }
            });

            jQuery('#wpvivid_create_staging_step1').on("keyup", '#wpvivid_staging_table_prefix', function(){
                wpvivid_additional_database_table_prefix();
            });

            function wpvivid_additional_database_table_prefix(){
                var additional_db_prefix = jQuery('#wpvivid_create_staging_step1').find('#wpvivid_staging_table_prefix').val();
                if(additional_db_prefix !== ''){
                    jQuery('#wpvivid_create_staging_step1').find('.wpvivid-staging-table-prefix-display').html(additional_db_prefix);
                }
                else{
                    jQuery('#wpvivid_create_staging_step1').find('.wpvivid-staging-table-prefix-display').html('*');
                }
            }

            jQuery('#wpvivid_create_staging_step2').on("click", '#wpvivid_staging_cancel', function(){
                wpvivid_staging_cancel();
            });

            function wpvivid_staging_cancel(){
                var ajax_data = {
                    'action': 'wpvividstg_cancel_staging_free'
                };
                jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function(data){

                }, function(XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('cancelling the staging', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#wpvivid_create_staging').click(function() {
                var descript = 'Click OK to start creating the staging site.';
                var ret = confirm(descript);
                if(ret === true){
                    jQuery('#wpvivid_staging_notice').hide();
                    wpvivid_start_staging();
                }
            });

            jQuery('#wpvivid_mu_create_staging').click(function() {
                var descript = 'Click OK to start creating the staging site.';
                var ret = confirm(descript);
                if(ret === true){
                    jQuery('#wpvivid_staging_notice').hide();
                    wpvivid_start_staging();
                }
            });

            jQuery('#wpvivid_mu_single_create_staging').click(function() {
                var descript = 'Click OK to start creating the staging site.';
                var ret = confirm(descript);
                if(ret === true){
                    jQuery('#wpvivid_staging_notice').hide();
                    wpvivid_start_staging();
                }
            });

            function wpvivid_recreate_staging(){
                jQuery('#wpvivid_choose_staging_content').show();
                jQuery('#wpvivid_create_btn').show();
                jQuery('#wpvivid_create_staging_step2').hide();
            }

            function wpvivid_create_custom_json(parent_id){
                var json = {};
                //exclude
                json['exclude_custom'] = '0';
                json['folder_check_ex'] = '0';
                //core
                json['core_check'] = '1';
                json['core_list'] = Array();

                //themes
                json['themes_check'] = '1';
                json['themes_list'] = {};
                json['themes_extension'] = '';

                //plugins
                json['plugins_check'] = '1';
                json['plugins_list'] = {};
                json['plugins_extension'] = '';

                //content
                json['content_check'] = '1';
                json['content_list'] = {};
                json['content_extension'] = '';

                //uploads
                json['uploads_check'] = '1';
                json['uploads_list'] = {};
                json['upload_extension'] = '';

                //additional folders/files
                json['additional_file_check'] = '0';
                json['additional_file_list'] = {};

                //database
                json['database_list'] = Array();
                json['database_check'] = '1';

                return json;
            }

            function wpvivid_create_staging_lock_unlock(action){
                if(action === 'lock'){
                    jQuery('#wpvivid_create_staging_step1').find('input').attr('disabled', true);
                    jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'none', 'opacity': '0.4'});
                }
                else{
                    jQuery('#wpvivid_create_staging_step1').find('input').attr('disabled', false);
                    jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            }

            function wpvivid_check_staging_additional_folder_valid(parent_id){
                var check_status = false;
                if(jQuery('#'+parent_id).find('.wpvivid-custom-additional-file-check').prop('checked')){
                    jQuery('#'+parent_id).find('.wpvivid-custom-include-additional-file-list ul').find('li div:eq(1)').each(function () {
                        check_status = true;
                    });
                    if(check_status === false){
                        alert('Please select at least one item under the additional files/folder option, or deselect the option.');
                    }
                }
                else{
                    check_status = true;
                }
                return check_status;
            }

            function wpvivid_check_backup_option_avail(parent_id, check_database_item)
            {
                var check_status = true;

                //check is backup db or files
                var has_select_db_file = false;
                if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                    has_select_db_file = true;
                    var has_db_item = false;
                    if(jQuery('#'+parent_id).find('.wpvivid-custom-database-check').prop('checked')){
                        has_db_item = true;
                        var has_local_table_item = false;
                        if(!check_database_item){
                            has_local_table_item = true;
                        }
                        jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(index, value){
                            if(jQuery(this).prop('checked')){
                                has_local_table_item = true;
                            }
                        });
                        if(!has_local_table_item){
                            check_status = false;
                            alert('Please select at least one table to back up. Or, deselect the option \'Tables In The Wordpress Database\' under the option \'Databases Will Be Backed up\'.');
                            return check_status;
                        }
                    }
                    if(!has_db_item){
                        check_status = false;
                        alert('Please select at least one option from \'Tables In The Wordpress Database\' and \'Additional Databases\' under the option \'Databases Will Be Backed up\'. Or, deselect the option \'Databases Will Be Backed up\'.');
                        return check_status;
                    }
                }
                if(jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked')){
                    has_select_db_file = true;
                    var has_file_item = false;
                    if(jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked')){
                        has_file_item = true;
                    }
                    if(jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked')){
                        has_file_item = true;
                    }
                    if(jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked')){
                        has_file_item = true;
                    }
                    if(jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked')){
                        has_file_item = true;
                    }
                    if(jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked')){
                        has_file_item = true;
                    }
                    if(jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                        has_file_item = true;
                        var has_additional_folder = false;
                        jQuery('#'+parent_id).find('.wpvivid-custom-include-additional-folder-list div').find('span:eq(2)').each(function(){
                            has_additional_folder = true;
                        });
                        if(!has_additional_folder){
                            check_status = false;
                            alert('Please select at least one additional file or folder under the option \'Files/Folders Will Be Backed up\', Or, deselect the option \'Additional Files/Folders\'.');
                            return check_status;
                        }
                    }
                    if(!has_file_item){
                        check_status = false;
                        alert('Please select at least one option under the option \'Files/Folders Will Be Backed up\'. Or, deselect the option \'Files/Folders Will Be Backed up\'.');
                        return check_status;
                    }
                }
                if(!has_select_db_file){
                    check_status = false;
                    alert('Please select at least one option from \'Databases Will Be Backed up\' and \'Files/Folders Will Be Backed up\'.');
                    return check_status;
                }

                return check_status;
            }

            function wpvivid_start_staging()
            {
                var staging_root_dir='0';
                jQuery('#wpvivid_create_staging_step1').find('input:radio[name=choose_staging_dir]').each(function ()
                {
                    if (jQuery(this).prop('checked'))
                    {
                        staging_root_dir = jQuery(this).val();
                    }
                });

                var path='';

                path=jQuery('#wpvivid_staging_path').val();

                if(path === '')
                {
                    alert('A site name is required.');
                    return;
                }

                var table_prefix=jQuery('#wpvivid_staging_table_prefix').val();

                if(table_prefix === '')
                {
                    alert('Table Prefix is required.');
                    return;
                }

                var additional_database_json = {};

                var additional_database_option = '0';
                jQuery('#wpvivid_create_staging_step1').find('input:radio[name=choose_staging_db]').each(function ()
                {
                    if (jQuery(this).prop('checked')) {
                        additional_database_option = jQuery(this).val();
                    }
                });

                if (additional_database_option === '1')
                {
                    additional_database_json['additional_database_check'] = '1';
                    additional_database_json['additional_database_info'] = {};
                    additional_database_json['additional_database_info']['db_user'] = jQuery('.wpvivid-additional-database-user').val();
                    additional_database_json['additional_database_info']['db_pass'] = jQuery('.wpvivid-additional-database-pass').val();
                    additional_database_json['additional_database_info']['db_host'] = jQuery('.wpvivid-additional-database-host').val();
                    additional_database_json['additional_database_info']['db_name'] = jQuery('.wpvivid-additional-database-name').val();
                    if (additional_database_json['additional_database_info']['db_name'] === '') {
                        alert('Database Name is required.');
                        return;
                    }
                    if (additional_database_json['additional_database_info']['db_user'] === '') {
                        alert('Database User is required.');
                        return;
                    }
                    if (additional_database_json['additional_database_info']['db_host'] === '') {
                        alert('Database Host is required.');
                        return;
                    }
                }
                else {
                    additional_database_json['additional_database_check'] = '0';
                }
                var additional_database_info = JSON.stringify(additional_database_json);

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
                                jQuery('#wpvivid_staging_log').html("");
                                jQuery('#wpvivid_staging_progress_bar').css('width', '0%');
                                jQuery('#wpvivid_staging_progress_bar').find('div').eq(0).html('0%');
                                var custom_dir_json = wpvivid_create_custom_json('wpvivid_custom_staging_list');
                                var custom_dir = JSON.stringify(custom_dir_json);
                                var check_select = true;

                                wpvivid_create_staging_lock_unlock('lock');

                                var ajax_data = {
                                    'action': 'wpvividstg_start_staging_free',
                                    'path': path,
                                    'table_prefix': table_prefix,
                                    'custom_dir': custom_dir,
                                    'additional_db': additional_database_info,
                                    'root_dir':staging_root_dir
                                };

                                jQuery('#wpvivid_choose_staging_content').hide();
                                jQuery('#wpvivid_create_btn').hide();
                                jQuery('#wpvivid_create_staging_step2').show();
                                wpvivid_post_request(ajax_data, function (data)
                                {
                                    setTimeout(function () {
                                        wpvivid_get_staging_progress();
                                    }, staging_requet_timeout);
                                }, function (XMLHttpRequest, textStatus, errorThrown)
                                {
                                    jQuery('#wpvivid_choose_staging_content').hide();
                                    jQuery('#wpvivid_create_btn').hide();
                                    jQuery('#wpvivid_create_staging_step2').show();
                                    setTimeout(function () {
                                        wpvivid_get_staging_progress();
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

            function wpvivid_restart_staging() {
                var ajax_data = {
                    'action':'wpvividstg_start_staging_free',
                };

                wpvivid_post_request(ajax_data, function(data)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_staging_progress();
                    }, staging_requet_timeout);
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_staging_progress();
                    }, staging_requet_timeout);
                });
            }

            function wpvivid_get_staging_progress() {
                console.log(staging_requet_timeout);
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
                            jQuery('#wpvivid_staging_log').html("");
                            while (log_data.indexOf('\n') >= 0)
                            {
                                var iLength = log_data.indexOf('\n');
                                var log = log_data.substring(0, iLength);
                                log_data = log_data.substring(iLength + 1);
                                var insert_log = "<div style=\"clear:both;\">" + log + "</div>";
                                jQuery('#wpvivid_staging_log').append(insert_log);
                                var div = jQuery('#wpvivid_staging_log');
                                div[0].scrollTop = div[0].scrollHeight;
                            }
                            jQuery('#wpvivid_staging_progress_bar').css('width', jsonarray.percent + '%');
                            jQuery('#wpvivid_staging_progress_bar').find('div').eq(0).html(jsonarray.percent + '%');
                            if(jsonarray.continue)
                            {
                                if(jsonarray.need_restart)
                                {
                                    wpvivid_restart_staging();
                                }
                                else
                                {
                                    setTimeout(function()
                                    {
                                        wpvivid_get_staging_progress();
                                    }, staging_requet_timeout);
                                }
                            }
                            else{
                                if(typeof jsonarray.completed !== 'undefined' && jsonarray.completed){
                                    jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                                    wpvivid_create_staging_lock_unlock('unlock');
                                    jQuery('#wpvivid_create_staging_step2').hide();
                                    location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvivid-staging'; ?>';
                                }
                                else if(typeof jsonarray.error !== 'undefined' && jsonarray.error){
                                    wpvivid_create_staging_lock_unlock('unlock');
                                    var insert_log = "<div style=\"clear:both;\"><a style=\"cursor: pointer;\" onclick=\"wpvivid_recreate_staging();\">Create a staging site</a></div>";
                                    jQuery('#wpvivid_staging_log').append(insert_log);
                                    var div = jQuery('#wpvivid_staging_log');
                                    div[0].scrollTop = div[0].scrollHeight;
                                }
                                else if(typeof jsonarray.is_cancel !== 'undefined' && jsonarray.is_cancel){
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
                                    wpvivid_post_request(ajax_data, function (data) {
                                        jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                                        wpvivid_create_staging_lock_unlock('unlock');
                                        jQuery('#wpvivid_choose_staging_content').show();
                                        jQuery('#wpvivid_create_btn').show();
                                        jQuery('#wpvivid_create_staging_step2').hide();
                                        try {
                                            var jsonarray = jQuery.parseJSON(data);
                                            if (jsonarray !== null) {
                                                if (jsonarray.result === 'success') {
                                                }
                                                else {
                                                    alert(jsonarray.error);
                                                }
                                            }
                                            else {
                                            }
                                        }
                                        catch (e) {
                                        }
                                    }, function (XMLHttpRequest, textStatus, errorThrown) {
                                        wpvivid_create_staging_lock_unlock('unlock');
                                        jQuery('#wpvivid_choose_staging_content').show();
                                        jQuery('#wpvivid_create_btn').show();
                                        jQuery('#wpvivid_create_staging_step2').hide();
                                        var error_message = wpvivid_output_ajaxerror('deleting staging site', textStatus, errorThrown);
                                        alert(error_message);
                                    });
                                }
                                else{
                                    jQuery('#wpvivid_staging_cancel').css({'pointer-events': 'auto', 'opacity': '1'});
                                    wpvivid_create_staging_lock_unlock('unlock');
                                    jQuery('#wpvivid_choose_staging_content').show();
                                    jQuery('#wpvivid_create_btn').show();
                                    jQuery('#wpvivid_create_staging_step2').hide();
                                }
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            wpvivid_create_staging_lock_unlock('unlock');
                            jQuery('#wpvivid_choose_staging_content').show();
                            jQuery('#wpvivid_create_btn').show();
                            jQuery('#wpvivid_create_staging_step2').hide();
                            alert(jsonarray.error);
                        }
                    }
                    catch(err){
                        setTimeout(function()
                        {
                            wpvivid_get_staging_progress();
                        }, 3000);
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    setTimeout(function()
                    {
                        wpvivid_get_staging_progress();
                    }, 3000);
                });
            }

            function wpvivid_additional_database_connect_test(){
                var db_user = jQuery('.wpvivid-additional-database-user').val();
                var db_pass = jQuery('.wpvivid-additional-database-pass').val();
                var db_host = jQuery('.wpvivid-additional-database-host').val();
                var db_name = jQuery('.wpvivid-additional-database-name').val();
                if(db_name !== ''){
                    if(db_user !== ''){
                        if(db_host !== ''){
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
                            jQuery('#wpvivid_connect_additional_database').css({
                                'pointer-events': 'none',
                                'opacity': '0.4'
                            });
                            wpvivid_post_request(ajax_data, function (data) {
                                jQuery('#wpvivid_connect_additional_database').css({
                                    'pointer-events': 'auto',
                                    'opacity': '1'
                                });
                                try {
                                    var jsonarray = jQuery.parseJSON(data);
                                    if (jsonarray !== null) {
                                        if (jsonarray.result === 'success') {
                                            alert('Connection success.')
                                        }
                                        else {
                                            alert(jsonarray.error);
                                        }
                                    }
                                    else {
                                        alert('Connection Failed. Please check the credentials you entered and try again.');
                                    }
                                }
                                catch (e) {
                                    alert('Connection Failed. Please check the credentials you entered and try again.');
                                }
                            }, function (XMLHttpRequest, textStatus, errorThrown) {
                                jQuery('#wpvivid_connect_additional_database').css({
                                    'pointer-events': 'auto',
                                    'opacity': '1'
                                });
                                jQuery(obj).css({'pointer-events': 'auto', 'opacity': '1'});
                                var error_message = wpvivid_output_ajaxerror('connecting database', textStatus, errorThrown);
                                alert(error_message);
                            });
                        }
                        else{
                            alert('Database Host is required.');
                        }
                    }
                    else{
                        alert('Database User is required.');
                    }
                }
                else{
                    alert('Database Name is required.');
                }
            }
        </script>
        <?php
    }
}