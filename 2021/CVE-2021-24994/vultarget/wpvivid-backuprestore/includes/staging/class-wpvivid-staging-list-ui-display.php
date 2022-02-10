<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Staging_List_UI_Display_Free
{
    public function __construct()
    {

    }

    public function get_staging_site_data()
    {
        if(is_multisite())
        {
            switch_to_blog(get_main_network_id());
            $staging=get_option('wpvivid_staging_data',false);
            restore_current_blog();
        }
        else
        {
            $staging=get_option('wpvivid_staging_data',false);
        }

        return $staging;
    }

    public function output_staging_sites_list_page()
    {
        ?>
        <div class="postbox quickstaging">
            <div class="wpvivid-one-coloum" style="border:1px solid #f1f1f1;padding-top:0em;padding-bottom:0em;">
                <div class="wpvivid-two-col">
                    <ul class="">
                        <li>
                            <input type="button" class="button button-primary" id="wpvivid_switch_create_staging_page" value="Create A Staging Site">
                            <p>Click to start creating a staging site.
                        </li>
                    </ul>
                </div>

                <?php
                if(!is_multisite()){
                    ?>
                    <div class="wpvivid-two-col">
                        <ul class="">
                            <li>
                                <input type="button" class="button button-primary" id="wpvivid_switch_create_fresh_install_page" value="Create A Fresh WP Site">
                                <p>Click to start creating a fresh WP install.
                            </li>
                        </ul>
                    </div>
                    <?php
                }
                ?>
                <div style="clear: both;"></div>
            </div>


            <div id="wpvivid_staging_list">
                <?php
                $list = get_option('wpvivid_staging_task_list',array());
                if(!empty($list))
                {
                    foreach ($list as $id => $staging)
                    {
                        if(isset($staging['site']['path']) && !empty($staging['site']['path']))
                        {
                            $staging_site_name = basename($staging['site']['path']);
                        }
                        else{
                            $staging_site_name = 'N/A';
                        }

                        $home_url = home_url();
                        global $wpdb;
                        $home_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'home' ) );
                        foreach ( $home_url_sql as $home ){
                            $home_url = $home->option_value;
                        }
                        $home_url = untrailingslashit($home_url);
                        $admin_url  = apply_filters('wpvividstg_get_admin_url', '');

                        //$admin_name = str_replace($home_url, '', $admin_url);
                        //$admin_name = trim($admin_name, '/');

                        if(!isset($staging['login_url']))
                        {
                            $admin_name = str_replace($home_url, '', $admin_url);
                            $admin_name = trim($admin_name, '/');
                            $admin_url_descript = 'Admin URL';
                        }
                        else
                        {
                            $login_url = $staging['login_url'];
                            $login_name = str_replace($home_url, '', $login_url);
                            $login_name = trim($login_name, '/');
                            if($login_name !== 'wp-login.php' && !isset($staging['site']['fresh_install']))
                            {
                                $admin_name = $login_name;
                                $admin_url_descript = 'Login URL';
                            }
                            else
                            {
                                $admin_name = str_replace($home_url, '', $admin_url);
                                $admin_name = trim($admin_name, '/');
                                $admin_url_descript = 'Admin URL';
                            }
                        }

                        if(isset($staging['site']['home_url']) && !empty($staging['site']['home_url']))
                        {
                            $site_url = esc_url($staging['site']['home_url']);
                            $admin_url = esc_url($staging['site']['home_url'].'/'.$admin_name.'/');
                        }
                        else{
                            $site_url = 'N/A';
                            $admin_url = 'N/A';
                        }

                        if(isset($staging['site']['prefix']) && !empty($staging['site']['prefix']))
                        {
                            $prefix = $staging['site']['prefix'];
                            if(isset($staging['site']['db_connect']['dbname']) && !empty($staging['site']['db_connect']['dbname'])){
                                $db_name = $staging['site']['db_connect']['dbname'];
                            }
                            else{
                                $db_name = DB_NAME;
                            }
                        }
                        else{
                            $prefix = 'N/A';
                            $db_name = 'N/A';
                        }
                        if(isset($staging['site']['path']) && !empty($staging['site']['path'])){
                            $site_dir = $staging['site']['path'];
                        }
                        else{
                            $site_dir = 'N/A';
                        }

                        if(isset($staging['site']['fresh_install']))
                        {
                            $copy_btn='Copy the Fresh Install to Live(pro feature)';
                            $update_btn='Update the Fresh Install(pro feature)';
                            $class_btn='fresh-install';
                        }
                        else
                        {
                            $copy_btn='Copy the Staging Site to Live(pro feature)';
                            $update_btn='Update the Staging Site(pro feature)';
                            $class_btn='staging-site';
                        }

                        if(isset($staging['create_time']))
                        {
                            $staging_create_time = $staging['create_time'];
                            $offset=get_option('gmt_offset');
                            $utc_time = $staging_create_time - $offset * 60 * 60;
                            $staging_create_time = date('M-d-Y H:i', $utc_time);
                        }
                        else
                        {
                            $staging_create_time = 'N/A';
                        }

                        if(isset($staging['copy_time']))
                        {
                            $staging_copy_time = $staging['copy_time'];
                            $offset=get_option('gmt_offset');
                            $utc_time = $staging_copy_time - $offset * 60 * 60;
                            $staging_copy_time = date('M-d-Y H:i', $utc_time);
                        }
                        else
                        {
                            $staging_copy_time = 'N/A';
                        }
                        ?>
                        <div class="wpvivid-one-coloum" style="border:1px solid #f1f1f1;padding-top:0em; margin-top:1em;" id="<?php echo esc_attr($id); ?>">
                            <div class="wpvivid-two-col">
                                <p><span class="dashicons dashicons-awards wpvivid-dashicons-blue"></span><span><strong>Site Name: </strong></span><span><?php echo $staging_site_name; ?></span></p>
                                <p><span class="dashicons dashicons-admin-home wpvivid-dashicons-blue"></span><span><strong>Home URL: </strong></span><span><a href="<?php echo esc_url($site_url); ?>"><?php echo $site_url; ?></a></span></p>
                                <p><span class="dashicons dashicons-rest-api wpvivid-dashicons-blue"></span><span><strong><?php echo $admin_url_descript; ?>: </strong></span><span><a href="<?php echo esc_url($admin_url); ?>"><?php echo $admin_url; ?></a></span></p>
                                <p><span class="dashicons dashicons-clock wpvivid-dashicons-blue"></span><span><strong>Create Time: </strong></span><span><?php echo $staging_create_time; ?></span></p>
                            </div>

                            <div class="wpvivid-two-col">
                                <p><span class="dashicons dashicons-admin-site-alt3 wpvivid-dashicons-blue"></span><span><strong>Database Name: </strong></span><span><?php echo $db_name; ?></span></p>
                                <p><span class="dashicons dashicons-list-view wpvivid-dashicons-blue"></span><span><strong>Table Prefix: </strong></span><span><?php echo $prefix; ?></span></p>
                                <p><span class="dashicons dashicons-portfolio wpvivid-dashicons-blue"></span><span><strong>Directory: </strong></span><span><?php echo $site_dir; ?></span></p>
                                <p><span class="dashicons dashicons-clock wpvivid-dashicons-blue"></span><span><strong>Update Time: </strong></span><span><?php echo $staging_copy_time; ?></span></p>
                            </div>

                            <div class="wpvivid-copy-staging-to-live-block <?php echo esc_attr($class_btn); ?>" name="<?php echo esc_attr($id); ?>" style="padding:1em 1em 0 0;">
                                <?php
                                if($staging['status']['str'] === 'completed')
                                {
                                    ?>
                                    <span class="wpvivid-rectangle wpvivid-grey-light wpvivid-hover-blue wpvivid-staging-operate wpvivid-update-live-to-staging"><?php echo $update_btn; ?></span>
                                    <span class="wpvivid-rectangle wpvivid-grey-light wpvivid-hover-blue wpvivid-staging-operate wpvivid-copy-staging-to-live"><?php echo $copy_btn; ?></span>
                                    <?php
                                }
                                ?>
                                <span class="wpvivid-rectangle wpvivid-grey-light wpvivid-hover-blue wpvivid-staging-operate wpvivid-delete-staging-site">Delete</span>
                                <?php
                                if($staging['status']['str'] === 'ready')
                                {
                                    ?>
                                    <span class="wpvivid-rectangle wpvivid-grey-light wpvivid-hover-blue wpvivid-staging-operate wpvivid-restart-staging-site">Resume</span>
                                    <span class="dashicons dashicons-editor-help wpvivid-dashicons-editor-help wpvivid-tooltip" style="margin-top: 4px;">
                                    <div class="wpvivid-bottom">
                                        <!-- The content you need -->
                                        <p>The staging site is not fully created yet due to an interruption. Click the Resume button to continue the creation.</p>
                                        <i></i> <!-- do not delete this line -->
                                    </div>
                                </span>
                                    <?php
                                }
                                ?>
                            </div>
                            <div style="clear: both;"></div>
                        </div>
                        <?php
                    }
                }
                ?>
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
                var path_arr = {};
                path_arr['core'] = '<?php echo $home_path; ?>';
                path_arr['content'] = '<?php echo $content_path; ?>';
                path_arr['uploads'] = '<?php echo $upload_path; ?>';
                path_arr['themes'] = '<?php echo $theme_path; ?>';
                path_arr['plugins'] = '<?php echo $plugin_path; ?>';

                var push_staging_site_id='';
                var wpvivid_ajax_lock=false;

                function wpvivid_create_standard_json(){
                    var json = {};
                    json['database_check_ex'] = '1';
                    json['folder_check_ex'] = '1';
                    json['exclude_custom'] = '0';
                    json['core_list'] = Array();
                    json['core_check'] = '0';
                    json['database_list'] = Array();
                    json['database_check'] = '1';
                    json['themes_list'] = {};
                    json['themes_check'] = '0';
                    json['themes_extension']= Array();
                    json['plugins_list'] = {};
                    json['plugins_check'] = '0';
                    json['plugins_extension']= Array();
                    json['uploads_list'] = {};
                    json['uploads_check'] = '1';
                    json['upload_extension']= Array();
                    json['content_list'] = {};
                    json['content_check'] = '0';
                    json['content_extension']= Array();
                    json['additional_file_list'] = {};
                    json['additional_file_check'] = '0';
                    json['additional_file_extension']= Array();
                    return json;
                }

                function wpvivid_lock_unlock_push_ui(action){
                    if(action === 'lock'){
                        jQuery('#wpvivid_staging_list').find('a').css({'pointer-events': 'none', 'opacity': '0.4'});
                        jQuery('#wpvivid_staging_list').find('input').attr('disabled', true);
                        jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'none', 'opacity': '0.4'});
                        jQuery('#wpvivid_staging_list').find('div#wpvivid_custom_staging_site').css({'pointer-events': 'none', 'opacity': '0.4'});
                    }
                    else{
                        jQuery('#wpvivid_staging_list').find('a').css({'pointer-events': 'auto', 'opacity': '1'});
                        jQuery('#wpvivid_staging_list').find('input').attr('disabled', false);
                        jQuery('#wpvivid_staging_list').find('div.wpvivid-delete-staging-site').css({'pointer-events': 'auto', 'opacity': '1'});
                        jQuery('#wpvivid_staging_list').find('div#wpvivid_custom_staging_site').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }

                function wpvivid_delete_staging_site_lock_unlock(id, action){
                    if(action === 'lock'){
                        jQuery('#wpvivid_staging_list').css({'pointer-events': 'none', 'opacity': '0.4'});
                    }
                    else{
                        jQuery('#wpvivid_staging_list').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }

                function wpvivid_staging_js_fix(parent_id, is_staging, themes_path, plugins_path, uploads_path, content_path, home_path, staging_site_id){
                    var tree_path = themes_path;

                    var path_arr = {};
                    path_arr['core'] = home_path;
                    path_arr['content'] = content_path;
                    path_arr['uploads'] = uploads_path;
                    path_arr['themes'] = themes_path;
                    path_arr['plugins'] = plugins_path;

                    jQuery('#'+parent_id).on('click', '.wpvivid-handle-additional-folder-detail', function(){
                        wpvivid_init_custom_include_tree(home_path, is_staging, parent_id);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-refresh-include-tree', function(){
                        wpvivid_init_custom_include_tree(home_path, is_staging, parent_id, 1);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-handle-tree-detail', function(){
                        var value = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        if(value === 'themes'){
                            tree_path = themes_path;
                        }
                        else if(value === 'plugins'){
                            tree_path = plugins_path;
                        }
                        else if(value === 'content'){
                            tree_path = content_path;
                        }
                        else if(value === 'uploads'){
                            tree_path = uploads_path;
                        }
                        wpvivid_init_custom_exclude_tree(tree_path, is_staging, parent_id);
                    });

                    jQuery('#'+parent_id).on('change', '.wpvivid-custom-tree-selector', function(){
                        var value = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        if(value === 'themes'){
                            tree_path = themes_path;
                        }
                        else if(value === 'plugins'){
                            tree_path = plugins_path;
                        }
                        else if(value === 'content'){
                            tree_path = content_path;
                        }
                        else if(value === 'uploads'){
                            tree_path = uploads_path;
                        }
                        jQuery('#'+parent_id).find('.wpvivid-custom-exclude-tree-info').jstree("destroy").empty();
                        wpvivid_init_custom_exclude_tree(tree_path, is_staging, parent_id);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-refresh-exclude-tree', function(){
                        var value = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        if(value === 'themes'){
                            tree_path = themes_path;
                        }
                        else if(value === 'plugins'){
                            tree_path = plugins_path;
                        }
                        else if(value === 'content'){
                            tree_path = content_path;
                        }
                        else if(value === 'uploads'){
                            tree_path = uploads_path;
                        }
                        wpvivid_init_custom_exclude_tree(tree_path, is_staging, parent_id, 1);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-custom-tree-exclude-btn', function(){
                        var select_folders = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-tree-info').jstree(true).get_selected(true);
                        var tree_type = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        var tree_path = path_arr[tree_type];
                        if(tree_type === 'themes'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-themes-list');
                        }
                        else if(tree_type === 'plugins'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-plugins-list');
                        }
                        else if(tree_type === 'content'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-content-list');
                        }
                        else if(tree_type === 'uploads'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-uploads-list');
                        }

                        jQuery.each(select_folders, function (index, select_item) {
                            if (select_item.id !== tree_path) {
                                var value = select_item.id;
                                value = value.replace(tree_path, '');
                                if (!wpvivid_check_tree_repeat(tree_type, value, parent_id)) {
                                    var class_name = select_item.icon;
                                    if(class_name === 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer'){
                                        var type = 'folder';
                                    }
                                    else{
                                        var type = 'file';
                                    }
                                    var tr = "<div class='wpvivid-text-line' type='"+type+"'>" +
                                        "<span class='dashicons dashicons-trash wpvivid-icon-16px wpvivid-remove-custom-exlcude-tree'></span>" +
                                        "<span class='"+class_name+"'></span>" +
                                        "<span class='wpvivid-text-line'>" + value + "</span>" +
                                        "</div>";
                                    list_obj.append(tr);
                                }
                            }
                        });
                    });

                    if(is_staging){
                        is_staging = '1';
                    }
                    else{
                        is_staging = '0';
                    }
                    wpvivid_get_custom_database_tables_info(parent_id, is_staging, staging_site_id);
                }

                function wpvivid_load_mu_staging_js(parent_id){
                    function wpvivid_handle_custom_open_close_ex(handle_obj, obj, parent_id){
                        if(obj.is(":hidden")) {
                            handle_obj.each(function(){
                                if(jQuery(this).hasClass('dashicons-arrow-down-alt2')){
                                    jQuery(this).removeClass('dashicons-arrow-down-alt2');
                                    jQuery(this).addClass('dashicons-arrow-up-alt2');
                                }
                            });
                            obj.show();
                        }
                        else{
                            handle_obj.each(function(){
                                if(jQuery(this).hasClass('dashicons-arrow-up-alt2')){
                                    jQuery(this).removeClass('dashicons-arrow-up-alt2');
                                    jQuery(this).addClass('dashicons-arrow-down-alt2');
                                }
                            });
                            obj.hide();
                        }
                    }

                    function wpvivid_change_custom_exclude_info(type, parent_id){
                        jQuery('#'+parent_id).find('.wpvivid-custom-exclude-module').hide();
                        if(type === 'themes'){
                            jQuery('#'+parent_id).find('.wpvivid-custom-exclude-themes-module').show();
                        }
                        else if(type === 'plugins'){
                            jQuery('#'+parent_id).find('.wpvivid-custom-exclude-plugins-module').show();
                        }
                        else if(type === 'content'){
                            jQuery('#'+parent_id).find('.wpvivid-custom-exclude-content-module').show();
                        }
                        else if(type === 'uploads'){
                            jQuery('#'+parent_id).find('.wpvivid-custom-exclude-uploads-module').show();
                        }
                    }

                    function wpvivid_check_tree_repeat(tree_type, value, parent_id) {
                        if(tree_type === 'themes'){
                            var list = 'wpvivid-custom-exclude-themes-list';
                        }
                        else if(tree_type === 'plugins'){
                            var list = 'wpvivid-custom-exclude-plugins-list';
                        }
                        else if(tree_type === 'content'){
                            var list = 'wpvivid-custom-exclude-content-list';
                        }
                        else if(tree_type === 'uploads'){
                            var list = 'wpvivid-custom-exclude-uploads-list';
                        }
                        else if(tree_type === 'additional-folder'){
                            var list = 'wpvivid-custom-include-additional-folder-list';
                        }

                        var brepeat = false;
                        jQuery('#'+parent_id).find('.'+list+' div').find('span:eq(2)').each(function (){
                            if (value === this.innerHTML) {
                                brepeat = true;
                            }
                        });
                        return brepeat;
                    }

                    jQuery('#'+parent_id).on('click', '.wpvivid-handle-database-detail', function(){
                        var handle_obj = jQuery('#'+parent_id).find('.wpvivid-handle-database-detail');
                        var obj = jQuery('#'+parent_id).find('.wpvivid-database-detail');
                        wpvivid_handle_custom_open_close_ex(handle_obj, obj, parent_id);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-handle-base-database-detail', function(){
                        var handle_obj = jQuery('#'+parent_id).find('.wpvivid-handle-base-database-detail');
                        var obj = jQuery('#'+parent_id).find('.wpvivid-base-database-detail');
                        wpvivid_handle_custom_open_close_ex(handle_obj, obj, parent_id);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-handle-file-detail', function(){
                        var handle_obj = jQuery('#'+parent_id).find('.wpvivid-handle-file-detail');
                        var obj = jQuery('#'+parent_id).find('.wpvivid-file-detail');
                        wpvivid_handle_custom_open_close_ex(handle_obj, obj, parent_id);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-handle-additional-folder-detail', function(){
                        var handle_obj = jQuery('#'+parent_id).find('.wpvivid-handle-additional-folder-detail');
                        var obj = jQuery('#'+parent_id).find('.wpvivid-additional-folder-detail');
                        wpvivid_handle_custom_open_close_ex(handle_obj, obj, parent_id);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-handle-tree-detail', function(){
                        var handle_obj = jQuery('#'+parent_id).find('.wpvivid-handle-tree-detail');
                        var obj = jQuery('#'+parent_id).find('.wpvivid-tree-detail');
                        var value = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        wpvivid_handle_custom_open_close_ex(handle_obj, obj, parent_id);
                    });

                    jQuery('#'+parent_id).on('change', '.wpvivid-custom-tree-selector', function(){
                        var value = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        jQuery('#'+parent_id).find('.wpvivid-custom-exclude-tree-info').jstree("destroy").empty();
                        wpvivid_change_custom_exclude_info(value, parent_id);
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-include-additional-folder-btn', function(){
                        var select_folders = jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-tree-info').jstree(true).get_selected(true);
                        var tree_path = '<?php echo $home_path; ?>';
                        var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-include-additional-folder-list');
                        var tree_type = 'additional-folder';

                        jQuery.each(select_folders, function (index, select_item) {
                            if (select_item.id !== tree_path) {
                                var value = select_item.id;
                                value = value.replace(tree_path, '');
                                if (!wpvivid_check_tree_repeat(tree_type, value, parent_id)) {
                                    var class_name = select_item.icon;
                                    if(class_name === 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer'){
                                        var type = 'folder';
                                    }
                                    else{
                                        var type = 'file';
                                    }
                                    var tr = "<div class='wpvivid-text-line' type='"+type+"'>" +
                                        "<span class='dashicons dashicons-trash wpvivid-icon-16px wpvivid-remove-custom-exlcude-tree'></span>" +
                                        "<span class='"+class_name+"'></span>" +
                                        "<span class='wpvivid-text-line'>" + value + "</span>" +
                                        "</div>";
                                    list_obj.append(tr);
                                }
                            }
                        });
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-custom-tree-exclude-btn', function(){
                        var select_folders = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-tree-info').jstree(true).get_selected(true);
                        var tree_type = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        var tree_path = path_arr[tree_type];
                        if(tree_type === 'themes'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-themes-list');
                        }
                        else if(tree_type === 'plugins'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-plugins-list');
                        }
                        else if(tree_type === 'content'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-content-list');
                        }
                        else if(tree_type === 'uploads'){
                            var list_obj = jQuery('#'+parent_id).find('.wpvivid-custom-exclude-uploads-list');
                        }

                        jQuery.each(select_folders, function (index, select_item) {
                            if (select_item.id !== tree_path) {
                                var value = select_item.id;
                                value = value.replace(tree_path, '');
                                if (!wpvivid_check_tree_repeat(tree_type, value, parent_id)) {
                                    var class_name = select_item.icon;
                                    if(class_name === 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer'){
                                        var type = 'folder';
                                    }
                                    else{
                                        var type = 'file';
                                    }
                                    var tr = "<div class='wpvivid-text-line' type='"+type+"'>" +
                                        "<span class='dashicons dashicons-trash wpvivid-icon-16px wpvivid-remove-custom-exlcude-tree'></span>" +
                                        "<span class='"+class_name+"'></span>" +
                                        "<span class='wpvivid-text-line'>" + value + "</span>" +
                                        "</div>";
                                    list_obj.append(tr);
                                }
                            }
                        });
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-remove-custom-exlcude-tree', function(){
                        jQuery(this).parent().remove();
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-clear-custom-include-list', function(){
                        jQuery('#'+parent_id).find('.wpvivid-custom-include-additional-folder-list').html('');
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-clear-custom-exclude-list', function(){
                        var tree_type = jQuery('#'+parent_id).find('.wpvivid-custom-tree-selector').val();
                        if(tree_type === 'themes'){
                            var list = 'wpvivid-custom-exclude-themes-list';
                        }
                        else if(tree_type === 'plugins'){
                            var list = 'wpvivid-custom-exclude-plugins-list';
                        }
                        else if(tree_type === 'content'){
                            var list = 'wpvivid-custom-exclude-content-list';
                        }
                        else if(tree_type === 'uploads'){
                            var list = 'wpvivid-custom-exclude-uploads-list';
                        }
                        jQuery('#'+parent_id).find('.'+list).html('');
                    });

                    jQuery('#'+parent_id).on('click', '.wpvivid-database-table-check', function(){
                        if(jQuery(this).prop('checked')){
                            if(jQuery(this).hasClass('wpvivid-database-base-table-check')){
                                jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').prop('checked', true);
                            }
                            else if(jQuery(this).hasClass('wpvivid-database-other-table-check')){
                                jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').prop('checked', true);
                            }
                            else if(jQuery(this).hasClass('wpvivid-database-diff-prefix-table-check')){
                                jQuery('#'+parent_id).find('input:checkbox[option=diff_prefix_db][name=Database]').prop('checked', true);
                            }
                        }
                        else{
                            var check_status = false;
                            if (jQuery(this).hasClass('wpvivid-database-base-table-check')) {
                                jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').each(function(){
                                    if(jQuery(this).prop('checked')){
                                        check_status = true;
                                    }
                                });
                                jQuery('#'+parent_id).find('input:checkbox[option=diff_prefix_db][name=Database]').each(function(){
                                    if(jQuery(this).prop('checked')){
                                        check_status = true;
                                    }
                                });
                                if(check_status) {
                                    jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').prop('checked', false);
                                }
                                else{
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one table type under the Database option, or deselect the option.');
                                }
                            }
                            else if (jQuery(this).hasClass('wpvivid-database-other-table-check')) {
                                jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').each(function(){
                                    if(jQuery(this).prop('checked')){
                                        check_status = true;
                                    }
                                });
                                jQuery('#'+parent_id).find('input:checkbox[option=diff_prefix_db][name=Database]').each(function(){
                                    if(jQuery(this).prop('checked')){
                                        check_status = true;
                                    }
                                });
                                if(check_status) {
                                    jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').prop('checked', false);
                                }
                                else{
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one table type under the Database option, or deselect the option.');
                                }
                            }
                            else if (jQuery(this).hasClass('wpvivid-database-diff-prefix-table-check')) {
                                jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').each(function(){
                                    if(jQuery(this).prop('checked')){
                                        check_status = true;
                                    }
                                });
                                jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').each(function(){
                                    if(jQuery(this).prop('checked')){
                                        check_status = true;
                                    }
                                });
                                if(check_status) {
                                    jQuery('#'+parent_id).find('input:checkbox[option=diff_prefix_db][name=Database]').prop('checked', false);
                                }
                                else{
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one table type under the Database option, or deselect the option.');
                                }
                            }
                        }
                    });

                    jQuery('#'+parent_id).on("click", 'input:checkbox[option=base_db][name=Database]', function(){
                        if(jQuery(this).prop('checked')){
                            var all_check = true;
                            jQuery('#'+parent_id).find('input:checkbox[option=base_db][name=Database]').each(function(){
                                if(!jQuery(this).prop('checked')){
                                    all_check = false;
                                }
                            });
                            if(all_check){
                                jQuery('#'+parent_id).find('.wpvivid-database-base-table-check').prop('checked', true);
                            }
                        }
                        else{
                            var check_status = false;
                            jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status){
                                jQuery('#'+parent_id).find('.wpvivid-database-base-table-check').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one table type under the Database option, or deselect the option.');
                            }
                        }
                    });

                    jQuery('#'+parent_id).on("click", 'input:checkbox[option=other_db][name=Database]', function(){
                        if(jQuery(this).prop('checked')){
                            var all_check = true;
                            jQuery('#'+parent_id).find('input:checkbox[option=other_db][name=Database]').each(function(){
                                if(!jQuery(this).prop('checked')){
                                    all_check = false;
                                }
                            });
                            if(all_check){
                                jQuery('#'+parent_id).find('.wpvivid-database-other-table-check').prop('checked', true);
                            }
                        }
                        else{
                            var check_status = false;
                            jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status){
                                jQuery('#'+parent_id).find('.wpvivid-database-other-table-check').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one table type under the Database option, or deselect the option.');
                            }
                        }
                    });

                    jQuery('#'+parent_id).on("click", 'input:checkbox[option=diff_prefix_db][name=Database]', function(){
                        if(jQuery(this).prop('checked')){
                            var all_check = true;
                            jQuery('#'+parent_id).find('input:checkbox[option=diff_prefix_db][name=Database]').each(function(){
                                if(!jQuery(this).prop('checked')){
                                    all_check = false;
                                }
                            });
                            if(all_check){
                                jQuery('#'+parent_id).find('.wpvivid-database-diff-prefix-table-check').prop('checked', true);
                            }
                        }
                        else{
                            var check_status = false;
                            jQuery('#'+parent_id).find('input:checkbox[name=Database]').each(function(){
                                if(jQuery(this).prop('checked')){
                                    check_status = true;
                                }
                            });
                            if(check_status){
                                jQuery('#'+parent_id).find('.wpvivid-database-diff-prefix-table-check').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one table type under the Database option, or deselect the option.');
                            }
                        }
                    });

                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-database-part', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-database-check').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                jQuery('#'+parent_id).find('.wpvivid-custom-database-check').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one item under Custom Backup option.');
                            }
                        }
                    });

                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-database-check', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one item under Custom Backup option.');
                            }
                        }
                    });

                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-file-part', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked', true);
                            jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked', true);
                            jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked', true);
                            jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked', true);
                            jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked', true);
                            jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked', false);
                                jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked', false);
                                jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked', false);
                                jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked', false);
                                jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked', false);
                                jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked', false);
                            }
                            else{
                                jQuery(this).prop('checked', true);
                                alert('Please select at least one item under Custom Backup option.');
                            }
                        }
                    });

                    //core
                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-core-check', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', false);
                                }
                            }
                            else{
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one item under Custom Backup option.');
                                }
                            }
                        }
                    });

                    //themes
                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-themes-check', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', false);
                                }
                            }
                            else{
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one item under Custom Backup option.');
                                }
                            }
                        }
                    });

                    //plugins
                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-plugins-check', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', false);
                                }
                            }
                            else{
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one item under Custom Backup option.');
                                }
                            }
                        }
                    });

                    //content
                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-content-check', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', false);
                                }
                            }
                            else{
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one item under Custom Backup option.');
                                }
                            }
                        }
                    });

                    //uploads
                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-uploads-check', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', false);
                                }
                            }
                            else{
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-additional-folder-check').prop('checked')){
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one item under Custom Backup option.');
                                }
                            }
                        }
                    });

                    //additional_folder
                    jQuery('#'+parent_id).on("click", '.wpvivid-custom-additional-folder-check', function(){
                        if(jQuery(this).prop('checked')){
                            jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', true);
                        }
                        else{
                            var check_status = false;
                            if(jQuery('#'+parent_id).find('.wpvivid-custom-database-part').prop('checked')){
                                check_status = true;
                            }
                            if(check_status){
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked')){
                                    jQuery('#'+parent_id).find('.wpvivid-custom-file-part').prop('checked', false);
                                }
                            }
                            else{
                                if(!jQuery('#'+parent_id).find('.wpvivid-custom-themes-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-plugins-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-content-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-uploads-check').prop('checked') &&
                                    !jQuery('#'+parent_id).find('.wpvivid-custom-core-check').prop('checked')){
                                    jQuery(this).prop('checked', true);
                                    alert('Please select at least one item under Custom Backup option.');
                                }
                            }
                        }
                    });
                }

                function wpvivid_get_mu_site_info(id,copy){
                    var ajax_data = {
                        'action':'wpvividstg_get_mu_site_info_free',
                        'id': id,
                        'copy':copy
                    };
                    wpvivid_lock_unlock_push_ui('lock');
                    wpvivid_post_request(ajax_data, function(data){
                        wpvivid_lock_unlock_push_ui('unlock');
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                            push_staging_site_id=id;
                            jQuery('#wpvividstg_select_mu_staging_site').html(jsonarray.html);
                            jQuery('#'+id).find('.wpvivid-push-content').after(jQuery('#wpvividstg_select_mu_staging_site'));
                            jQuery('#wpvividstg_select_mu_staging_site').show();
                            wpvivid_load_mu_staging_js('wpvivid_custom_mu_staging_site');
                            if(copy == 'true' || copy == true){
                                //wpvivid_load_staging_tree('wpvivid_custom_mu_staging_site', true);
                                wpvivid_staging_js_fix('wpvivid_custom_mu_staging_site', true, jsonarray.theme_path, jsonarray.plugin_path, jsonarray.uploads_path, jsonarray.content_path, jsonarray.home_path, id);
                            }
                            else{
                                //wpvivid_load_staging_tree('wpvivid_custom_mu_staging_site', false);
                                wpvivid_staging_js_fix('wpvivid_custom_mu_staging_site', false, jsonarray.theme_path, jsonarray.plugin_path, jsonarray.uploads_path, jsonarray.content_path, jsonarray.home_path, id);
                            }
                            jQuery('#wpvivid_mu_copy_staging_site_list').find('input:checkbox').each(function(){
                                jQuery(this).prop('checked', true);
                            });
                        }
                        else if (jsonarray.result === 'failed') {
                            alert(jsonarray.error);
                        }

                        jQuery('#wpvivid_staging_list').find('.wpvivid-copy-staging-to-live-block').each(function() {
                            var tmp_id = jQuery(this).attr('name');
                            if(id !== tmp_id) {
                                if(jQuery(this).hasClass('staging-site')){
                                    var class_btn = 'staging-site';
                                    var copy_btn = 'Copy the Staging Site to Live(pro feature)';
                                    var update_btn = 'Update the Staging Site(pro feature)';
                                    var tip_text = 'Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
                                }
                                else{
                                    var class_btn = 'fresh-install';
                                    var copy_btn = 'Copy the Fresh Install to Live(pro feature)';
                                    var update_btn = 'Update the Fresh Install(pro feature)';
                                    var tip_text = 'Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
                                }

                                if(jQuery(this).hasClass('mu-single')){
                                    var mu_single_class = 'mu-single';
                                }
                                else{
                                    var mu_single_class = '';
                                }

                                var tmp_html = '<span class="wpvivid-rectangle wpvivid-grey-light wpvivid-hover-blue wpvivid-staging-operate wpvivid-update-live-to-staging">Update the Staging Site(pro feature)</span>' +
                                    '<span class="wpvivid-rectangle wpvivid-grey-light wpvivid-hover-blue wpvivid-staging-operate wpvivid-copy-staging-to-live">Copy the Staging Site to Live(pro feature)</span>' +
                                    '<span class="wpvivid-rectangle wpvivid-grey-light wpvivid-hover-blue wpvivid-staging-operate wpvivid-delete-staging-site">Delete</span>';
                                jQuery(this).html(tmp_html);
                            }
                        });
                    }, function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        wpvivid_lock_unlock_push_ui('unlock');
                        var error_message = wpvivid_output_ajaxerror('export the previously-exported settings', textStatus, errorThrown);
                        alert(error_message);
                    });
                }

                jQuery('#wpvivid_switch_create_staging_page').click(function(){
                    switch_staging_tab('create_staging');
                });

                jQuery('#wpvivid_switch_create_fresh_install_page').click(function(){
                    switch_staging_tab('create_fresh_install');
                });

                jQuery('#wpvivid_staging_list').on("click", '.wpvivid-delete-staging-site', function(){
                    var descript = 'Are you sure to delete this staging site?';
                    var ret = confirm(descript);
                    if (ret === true) {
                        var id = jQuery(this).parent().attr('name');
                        var ajax_data = {
                            'action': 'wpvividstg_delete_site_free',
                            'id': id
                        };
                        wpvivid_delete_staging_site_lock_unlock(id, 'lock');
                        wpvivid_post_request(ajax_data, function (data) {
                            wpvivid_delete_staging_site_lock_unlock(id, 'unlock');
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success') {
                                location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvivid-staging'; ?>';
                            }
                            else if (jsonarray.result === 'failed') {
                                alert(jsonarray.error);
                            }
                        }, function (XMLHttpRequest, textStatus, errorThrown) {
                            wpvivid_delete_staging_site_lock_unlock(id, 'unlock');
                            var error_message = wpvivid_output_ajaxerror('export the previously-exported settings', textStatus, errorThrown);
                            alert(error_message);
                        });
                    }
                });

                jQuery('#wpvivid_staging_list').on("click", '.wpvivid-restart-staging-site', function(){
                    var descript = 'Are you sure to restart this staging site?';
                    var ret = confirm(descript);
                    if (ret === true) {
                        var id = jQuery(this).parent().attr('name');
                        var ajax_data = {
                            'action':'wpvividstg_set_restart_staging_id_free',
                            'id': id
                        };
                        wpvivid_post_request(ajax_data, function (data) {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success') {
                                jQuery('#wpvivid_choose_staging_content').hide();
                                jQuery('#wpvivid_create_btn').hide();
                                jQuery('#wpvivid_create_staging_step2').show();
                                switch_staging_tab('create_staging');
                                wpvivid_restart_staging();
                            }
                            else if (jsonarray.result === 'failed') {
                                alert(jsonarray.error);
                            }
                        }, function (XMLHttpRequest, textStatus, errorThrown) {
                            var error_message = wpvivid_output_ajaxerror('setting restart staging id', textStatus, errorThrown);
                            alert(error_message);
                        });
                    }
                });
            </script>
        </div>
        <?php
    }

    public function output_staging(){
        $data=$this->get_staging_site_data();
        $data['live_site_staging_url'] = str_replace('wpvivid-staging', 'WPvivid_Staging', $data['live_site_staging_url']);
        $live_site_url = $data['live_site_url'];
        $push_site_url = $data['live_site_staging_url'];
        ?>
        <div class="wpvivid-one-coloum" style="border:1px solid #f1f1f1;padding-top:0em;">
            <div class="wpvivid-two-col">
                <p><span class="dashicons dashicons-awards wpvivid-dashicons-blue"></span><span><strong>Site Name: </strong></span><span><?php echo _e(basename(get_home_path())); ?></span></p>
                <p><span class="dashicons dashicons-admin-home wpvivid-dashicons-blue"></span><span><strong>Live Site URL: </strong></span><span><?php echo esc_url($live_site_url); ?></span></p>
                <p><span class="dashicons dashicons-rest-api wpvivid-dashicons-blue"></span><span><strong>Live Site Staging: </strong></span><span><?php echo esc_url($push_site_url); ?></span></p>
            </div>

            <div class="wpvivid-two-col">
                <p><span class="dashicons dashicons-admin-site-alt3 wpvivid-dashicons-blue"></span><span><strong>Database Name: </strong></span><span><?php echo _e(DB_NAME); ?></span></p>
                <p><span class="dashicons dashicons-list-view wpvivid-dashicons-blue"></span><span><strong>Table Prefix: </strong></span><span><?php echo _e($data['prefix']); ?></span></p>
                <p><span class="dashicons dashicons-portfolio wpvivid-dashicons-blue"></span><span><strong>Directory: </strong></span><span><?php echo _e(get_home_path()); ?></span></p>
            </div>
            <div style="clear: both;"></div>
        </div>
        <?php
    }
}