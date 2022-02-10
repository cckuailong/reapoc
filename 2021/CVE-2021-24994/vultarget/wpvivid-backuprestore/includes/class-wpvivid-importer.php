<?php
/**
 * WPvivid addon: yes
 * Addon Name: wpvivid-backup-pro-all-in-one
 * Description: Pro
 * Version: 1.9.1
 * Need_init: yes
 * Interface Name: WPvivid_media_importer
 */

if ( ! class_exists( 'WP_List_Table' ) )
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPvivid_Export_List extends WP_List_Table
{
    public $list;
    public $page_num;
    public $parent;

    public function __construct( $args = array() )
    {
        global $wpdb;
        parent::__construct(
            array(
                'plural' => 'import',
                'screen' => 'import',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list, $page_num=1)
    {
        $this->list=$list;
        $this->page_num=$page_num;
    }

    public function print_column_headers( $with_id = true )
    {
        list($columns, $hidden, $sortable, $primary) = $this->get_column_info();

        if (!empty($columns['cb'])) {
            static $cb_counter = 1;
            $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __('Select All') . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox"/>';
            $cb_counter++;
        }

        foreach ($columns as $column_key => $column_display_name) {
            $class = array('manage-column', "column-$column_key");

            if (in_array($column_key, $hidden)) {
                $class[] = 'hidden';
            }

            if ('cb' === $column_key) {
                $class[] = 'check-column';
            }

            if ($column_key === $primary) {
                $class[] = 'column-primary';
            }

            $tag = ('cb' === $column_key) ? 'td' : 'th';
            $scope = ('th' === $tag) ? 'scope="col"' : '';
            $id = $with_id ? "id='$column_key'" : '';

            if (!empty($class)) {
                $class = "class='" . join(' ', $class) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }

    public function get_columns()
    {
        $posts_columns = array();

        $posts_columns['file_name'] = __( 'File Name', 'wpvivid-backuprestore' );
        $posts_columns['export_type'] = __( 'Post Types', 'wpvivid-backuprestore' );
        $posts_columns['posts_count'] = __( 'Count', 'wpvivid-backuprestore' );
        $posts_columns['media_size'] = __( 'Media Files Size', 'wpvivid-backuprestore' );
        $posts_columns['import'] = __( 'Action', 'wpvivid-backuprestore' );

        return $posts_columns;
    }

    protected function display_tablenav( $which ) {
        $total_items =sizeof($this->list);
        if($total_items > 10) {
            if ('top' === $which) {
                wp_nonce_field('bulk-' . $this->_args['plural']);
            }
            ?>
            <div class="tablenav <?php echo esc_attr($which); ?>">

                <?php if ($this->has_items()) : ?>
                    <div class="alignleft actions bulkactions">
                        <?php $this->bulk_actions($which); ?>
                    </div>
                <?php
                endif;
                $this->extra_tablenav($which);
                $this->pagination($which);
                ?>

                <br class="clear"/>
            </div>
            <?php
        }
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $total_items =sizeof($this->list);
        if($total_items > 10) {
            $this->set_pagination_args(
                array(
                    'total_items' => $total_items,
                    'per_page' => 10,
                )
            );
        }
    }

    public function has_items()
    {
        return !empty($this->list);
    }

    public function column_cb( $item )
    {
        ?>
        <input id="cb-select-<?php echo $item['id']; ?>" type="checkbox" name="export[]" value="<?php echo $item['id']; ?>"/>
        <?php
    }

    public function _column_file_name( $item, $classes, $data, $primary )
    {
        echo '<td>                 
                    <div>
                        '.$item['file_name'].'
                    </div>
                     <div style="padding-bottom: 5px;">
                        <div class="backuptime">Data Modified: ' . __(date('M-d-Y H:i', $item['time']), 'wpvivid-backuprestore') . '</div>              
                    </div>
                </td>';
    }

    public function _column_export_type( $item, $classes, $data, $primary )
    {
        $export = $item['export_type'] === 'page' ? 'Page' : 'Post';
        echo '<td style="color: #000;">              
                    <div>
                        <div style="float:left;padding:10px 10px 10px 0;">'.__('Type: ').$export.'</div>
                    </div> 
              </td>';
    }

    public function _column_posts_count( $item, $classes, $data, $primary )
    {
        echo '<td style="min-width:100px;">
                    <div style="float:left;padding:10px 10px 10px 0;">
                        '.$item['posts_count'].'
                    </div>
                </td>';
    }

    public function _column_media_size( $item, $classes, $data, $primary )
    {
        echo '<td style="min-width:100px;">
                    <div style="float:left;padding:10px 10px 10px 0;">
                        '.$item['media_size'].'
                    </div>
                </td>';
    }

    public function _column_import( $item )
    {
        echo '<td style="min-width:100px;">
                   <div class="export-list-import" style="cursor:pointer;padding:10px 0 10px 0;">
                        <img src="' . esc_url(WPVIVID_PLUGIN_URL . '/admin/partials/images/Restore.png') . '" style="vertical-align:middle;" /><span>' . __('Import', 'wpvivid-backuprestore') . '</span>
                   </div>                
               </td>';
    }

    public function display_rows()
    {
        $this->_display_rows( $this->list );
    }

    private function _display_rows($lists)
    {
        $page_lists=$lists;
        $page=$this->get_pagenum();
        $count=0;
        while ( $count<$page )
        {
            $page_lists = array_splice( $lists, 0, 10);
            $count++;
        }
        foreach ( $page_lists as $key=>$item )
        {
            $item['id']=$key;
            $this->single_row($item);
        }
        ?>
        <?php
    }

    public function get_pagenum()
    {
        if($this->page_num=='first')
        {
            $this->page_num=1;
        }
        else if($this->page_num=='last')
        {
            $this->page_num=$this->_pagination_args['total_pages'];
        }
        $pagenum = $this->page_num ? $this->page_num : 0;

        if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
        {
            $pagenum = $this->_pagination_args['total_pages'];
        }

        return max( 1, $pagenum );
    }

    public function single_row($item)
    {
        ?>
        <tr id="<?php echo $item['id'] ?>" class="wpvivid-export-list-item">
            <?php $this->single_row_columns( $item ); ?>
        </tr>
        <?php
    }

    protected function pagination( $which ) {
        if ( empty( $this->_pagination_args ) ) {
            return;
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }

        if ( 'top' === $which && $total_pages > 1 ) {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }

        $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

        $current              = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();

        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

        $current_url = remove_query_arg( $removable_query_args, $current_url );

        $page_links = array();

        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';

        $disable_first = $disable_last = $disable_prev = $disable_next = false;

        if ( $current == 1 ) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ( $current == 2 ) {
            $disable_first = true;
        }
        if ( $current == $total_pages ) {
            $disable_last = true;
            $disable_next = true;
        }
        if ( $current == $total_pages - 1 ) {
            $disable_last = true;
        }

        if ( $disable_first ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='first-page button'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                __( 'First page' ),
                '&laquo;'
            );
        }

        if ( $disable_prev ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='prev-page button' value='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                $current,
                __( 'Previous page' ),
                '&lsaquo;'
            );
        }

        if ( 'bottom' === $which ) {
            $html_current_page  = $current;
            $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf(
                "%s<input class='current-page' id='current-page-selector-import' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector-import" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                $current,
                strlen( $total_pages )
            );
        }
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
        $page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

        if ( $disable_next ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='next-page button' value='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                $current,
                __( 'Next page' ),
                '&rsaquo;'
            );
        }

        if ( $disable_last ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<div class='last-page button'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></div>",
                __( 'Last page' ),
                '&raquo;'
            );
        }

        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

        if ( $total_pages ) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        echo $this->_pagination;
    }
}

class WPvivid_Impoter_taskmanager
{
    public static function new_task($task_id,$files,$options)
    {
        $task['id']=$task_id;
        $task['status']['start_time']=time();
        $task['status']['run_time']=time();
        $task['status']['timeout']=time();
        $task['status']['str']='ready';
        $task['status']['resume_count']=0;
        $task['options']=$options;
        $task['data']['files']=$files;
        self::update_task($task_id,$task);
    }

    public static function get_files($task_id)
    {
        $task=self::get_task($task_id);
        return  $task['data']['files'];
    }

    public static function get_options($task_id)
    {
        $task=self::get_task($task_id);
        return  $task['options'];
    }

    public static function get_tasks(){
        $default = array();
        return $options = get_option('wpvivid_importer_task_list', $default);
    }

    public static function get_task($task_id)
    {
        $default = array();
        $tasks = get_option('wpvivid_importer_task_list', $default);
        if(array_key_exists ($task_id, $tasks)) {
            return $tasks[$task_id];
        }
        else {
            return false;
        }
    }

    public static function update_task($task_id, $task)
    {
        $default = array();
        $options = get_option('wpvivid_importer_task_list', $default);
        $options[$task_id]=$task;
        WPvivid_Setting::update_option('wpvivid_importer_task_list', $options);
    }

    public static function delete_task($task_id){
        $options = get_option('wpvivid_importer_task_list', array());
        unset($options[$task_id]);
        WPvivid_Setting::update_option('wpvivid_importer_task_list', $options);
    }

    public static function get_import_task_status($task_id){
        $tasks=self::get_tasks();
        if(array_key_exists ($task_id, $tasks)) {
            $task = $tasks[$task_id];
            return $task['status']['str'];
        }
        else {
            return false;
        }
    }

    public static function update_import_task_status($task_id, $status, $reset_start_time=false, $reset_timeout=false, $resume_count=false, $error=''){
        $tasks=self::get_tasks();
        if(array_key_exists ($task_id, $tasks))
        {
            $task = $tasks[$task_id];
            $task['status']['run_time']=time();
            if($reset_start_time)
                $task['status']['start_time']=time();
            if(!empty($status)) {
                $task['status']['str']=$status;
            }
            if($reset_timeout)
                $task['status']['timeout']=time();
            if($resume_count!==false) {
                $task['status']['resume_count']=$resume_count;
            }

            if(!empty($error)) {
                $task['status']['error']=$error;
            }
            self::update_task($task_id, $task);
            return $task;
        }
        else {
            return false;
        }
    }
}

class WPvivid_import_data
{
    public $import_log = false;
    public $import_log_file;

    public function __construct()
    {
        $this->import_log_file = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR.DIRECTORY_SEPARATOR.'wpvivid_import_log.txt';
    }

    public function wpvivid_create_import_log()
    {
        $this->import_log=new WPvivid_Log();
        $this->import_log->CreateLogFile($this->import_log_file, 'has_folder', 'import');
    }

    public function wpvivid_write_import_log($message, $type)
    {
        if($this->import_log===false)
        {
            $this->import_log=new WPvivid_Log();
            $this->import_log->OpenLogFile($this->import_log_file,'has_folder');
        }

        clearstatcache();
        if(filesize($this->import_log_file)>4*1024*1024)
        {
            $this->import_log->CloseFile();
            unlink($this->import_log_file);
            $this->import_log=null;
            $this->import_log=new WPvivid_Log();
            $this->import_log->OpenLogFile($this->import_log_file,'has_folder');
        }
        $this->import_log->WriteLog($message, $type);
    }

    public function get_log_content()
    {
        $buffer = '';
        if(file_exists($this->import_log_file)){
            $file = fopen($this->import_log_file, 'r');

            if (!$file) {
                return '';
            }

            while (!feof($file)) {
                $buffer .= fread($file, 1024);
            }
            fclose($file);
        }
        return $buffer;
    }
}


global $xml_file_name;
class WPvivid_media_importer
{
    var $max_wxr_version = 1.2; // max. supported WXR version

    var $id; // WXR attachment ID
    var $default_user;
    // information to import from WXR file
    var $version;
    var $authors = array();
    var $posts = array();
    var $terms = array();
    var $categories = array();
    var $tags = array();
    var $base_url = '';
    var $new_site_url='';

    // mappings from old information to new
    var $processed_authors = array();
    var $author_mapping = array();
    var $processed_terms = array();
    var $processed_posts = array();
    var $post_orphans = array();
    var $processed_menu_items = array();
    var $menu_item_orphans = array();
    var $missing_menu_items = array();

    var $fetch_attachments = false;
    var $url_remap = array();
    var $featured_images = array();

    public $import_log;

    public function __construct()
    {
    }

    public function import($id)
    {
        if (!class_exists('PclZip')) include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');

        $this->import_log = new WPvivid_import_data();

        @set_time_limit(900);

        $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR;

        $files=WPvivid_Impoter_taskmanager::get_files($id);

        define(PCLZIP_TEMPORARY_DIR,dirname($path));

        global $xml_file_name;
        foreach ($files as $file)
        {
            $file_path=$path.DIRECTORY_SEPARATOR.$file;
            $this->import_log->wpvivid_write_import_log('Prepare to retrieve file info, file name: '.$file_path, 'notice');
            $archive = new PclZip($file_path);
            $ret=$this->get_file_info($file_path);
            if($ret['result']=='failed')
            {
                $this->import_log->wpvivid_write_import_log('Failed to retrieve file info, error: '.$ret['error'], 'notice');
                WPvivid_Impoter_taskmanager::update_import_task_status($id, 'error', true, false, false, $ret['error']);
                return $ret;
            }
            $this->import_log->wpvivid_write_import_log('Retrieving file info is completed.', 'notice');
            $xml_file=$ret['json_data']['xml_file'];
            $xml_file_name = $ret['json_data']['xml_file'];
            $this->import_log->wpvivid_write_import_log('Prepare to extract, file name: '.$xml_file, 'notice');
            $zip_ret = $archive->extract(PCLZIP_OPT_BY_NAME,basename($xml_file),PCLZIP_OPT_PATH,$path,PCLZIP_OPT_REPLACE_NEWER,PCLZIP_OPT_TEMP_FILE_THRESHOLD,16);
            if(!$zip_ret)
            {
                $this->import_log->wpvivid_write_import_log('Failed to extract, error: '.$archive->errorInfo(true), 'notice');
                WPvivid_Impoter_taskmanager::update_import_task_status($id, 'error', true, false, false, $archive->errorInfo(true));
                $ret['result']='failed';
                $ret['error'] = $archive->errorInfo(true);
                return $ret;
            }
            $this->import_log->wpvivid_write_import_log('The file extracton is completed, file name: '.$xml_file, 'notice');
            $this->import_log->wpvivid_write_import_log('Prepare to extract, file name: '.$file_path, 'notice');
            $zip_ret = $archive->extract(PCLZIP_OPT_PATH, WP_CONTENT_DIR, PCLZIP_OPT_REPLACE_NEWER, PCLZIP_CB_PRE_EXTRACT, 'wpvivid_function_pre_extract_import_callback', PCLZIP_OPT_TEMP_FILE_THRESHOLD,16);
            if(!$zip_ret)
            {
                $this->import_log->wpvivid_write_import_log('Failed to extract, error: '.$archive->errorInfo(true), 'notice');
                WPvivid_Impoter_taskmanager::update_import_task_status($id, 'error', true, false, false, $archive->errorInfo(true));
                $ret['result']='failed';
                $ret['error'] = $archive->errorInfo(true);
                return $ret;
            }
            $this->import_log->wpvivid_write_import_log('The file extracton is completed, file name: '.$file_path, 'notice');

            @set_time_limit(900);
            $file_path=$path.DIRECTORY_SEPARATOR.$xml_file;
            $this->import_log->wpvivid_write_import_log('Prepare import, file name: '.$file_path, 'notice');
            $ret=$this->_import($file_path, WPvivid_Impoter_taskmanager::get_options($id));
            if($ret['result']=='failed')
            {
                $this->import_log->wpvivid_write_import_log('Failed to import, error: '.$ret['error'], 'notice');
                WPvivid_Impoter_taskmanager::update_import_task_status($id, 'error', true, false, false, $ret['error']);
                return $ret;
            }
            $this->import_log->wpvivid_write_import_log('Import task is completed, file name: '.$file_path, 'notice');
            @unlink($file_path);
        }

        $this->replace_domain();

        $ret['result']='success';
        $ret['files']=$files;
        $this->import_log->wpvivid_write_import_log('Import task succeeded.', 'notice');
        WPvivid_Impoter_taskmanager::update_import_task_status($id, 'completed', false);
        return $ret;
    }

    public function get_file_info($file_name)
    {
        $zip=new WPvivid_ZipClass();
        $ret=$zip->get_json_data($file_name, 'export');
        if($ret['result'] === WPVIVID_SUCCESS)
        {
            $json=$ret['json_data'];
            $json = json_decode($json, 1);
            if (is_null($json))
            {
                return array('result'=>WPVIVID_FAILED,'error'=>'Failed to decode json');
            } else {
                return array('result'=>WPVIVID_SUCCESS,'json_data'=>$json);
            }
        }
        else {
            return $ret;
        }
    }

    public function _import($file,$options)
    {
        if(isset($options['user']))
        {
            $this->default_user=$options['user'];
        }
        else
        {
            $this->default_user=get_current_user_id();
        }

        if(isset($options['update_exist']))
        {
            $update_exist=$options['update_exist'];
        }
        else
        {
            $update_exist=false;
        }

        $ret=$this->import_start( $file );

        if($ret['result']=='failed')
        {
            return $ret;
        }

        $ret=$this->get_author_mapping();

        if($ret['result']=='failed')
        {
            return $ret;
        }

        wp_suspend_cache_invalidation( true );
        $ret=$this->process_categories();
        if($ret['result']=='failed')
        {
            return $ret;
        }
        $ret=$this->process_tags();
        if($ret['result']=='failed')
        {
            return $ret;
        }
        $ret=$this->process_terms();
        if($ret['result']=='failed')
        {
            return $ret;
        }
        $ret=$this->process_posts_ex($update_exist);
        if($ret['result']=='failed')
        {
            return $ret;
        }
        wp_suspend_cache_invalidation( false );
        $ret=$this->import_end();

        return $ret;
    }

    private function import_start( $file )
    {
        $this->import_log->wpvivid_write_import_log('Analyze the imported file, file name: '.$file, 'notice');
        $import_data = $this->parse( $file );
        if( is_wp_error( $import_data ) )
        {
            $this->import_log->wpvivid_write_import_log('Failed to analyze a file, file name: '.$file, 'notice');
            $ret['result']='failed';
            $ret['error']=$import_data->get_error_message();
            return $ret;
        }

        $this->version = $import_data['version'];
        $this->get_authors_from_import( $import_data );
        $this->posts = $import_data['posts'];
        $this->terms = $import_data['terms'];
        $this->categories = $import_data['categories'];
        $this->tags = $import_data['tags'];
        $this->base_url = esc_url( $import_data['base_url'] );
        $this->import_log->wpvivid_write_import_log('The file analysis is completed, file name: '.$file, 'notice');
        $ret['result']='success';
        return $ret;
    }

    private function get_author_mapping()
    {
        $ret['result']='success';

        return $ret;

        /*
        $create_users = false;

        foreach ( (array) $_POST['imported_authors'] as $i => $old_login )
        {
            // Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
            $santized_old_login = sanitize_user( $old_login, true );
            $old_id = isset( $this->authors[$old_login]['author_id'] ) ? intval($this->authors[$old_login]['author_id']) : false;

            if ( ! empty( $_POST['user_map'][$i] ) )
            {
                $user = get_userdata( intval($_POST['user_map'][$i]) );
                if ( isset( $user->ID ) ) {
                    if ( $old_id )
                        $this->processed_authors[$old_id] = $user->ID;
                    $this->author_mapping[$santized_old_login] = $user->ID;
                }
            } else if ( $create_users )
            {
                if ( ! empty($_POST['user_new'][$i]) )
                {
                    $user_id = wp_create_user( $_POST['user_new'][$i], wp_generate_password() );
                } else if ( $this->version != '1.0' )
                {
                    $user_data = array(
                        'user_login' => $old_login,
                        'user_pass' => wp_generate_password(),
                        'user_email' => isset( $this->authors[$old_login]['author_email'] ) ? $this->authors[$old_login]['author_email'] : '',
                        'display_name' => $this->authors[$old_login]['author_display_name'],
                        'first_name' => isset( $this->authors[$old_login]['author_first_name'] ) ? $this->authors[$old_login]['author_first_name'] : '',
                        'last_name' => isset( $this->authors[$old_login]['author_last_name'] ) ? $this->authors[$old_login]['author_last_name'] : '',
                    );
                    $user_id = wp_insert_user( $user_data );
                }

                if ( ! is_wp_error( $user_id ) )
                {
                    if ( $old_id )
                        $this->processed_authors[$old_id] = $user_id;
                    $this->author_mapping[$santized_old_login] = $user_id;
                } else {
                    printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'wordpress-importer' ), esc_html($this->authors[$old_login]['author_display_name']) );
                    if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
                        echo ' ' . $user_id->get_error_message();
                    echo '<br />';
                }
            }

            // failsafe: if the user_id was invalid, default to the current user
            if ( ! isset( $this->author_mapping[$santized_old_login] ) )
            {
                if ( $old_id )
                    $this->processed_authors[$old_id] = (int) get_current_user_id();
                $this->author_mapping[$santized_old_login] = (int) get_current_user_id();
            }
        }
        */
    }

    private function process_categories()
    {
        $ret['result']='success';
        $this->categories = apply_filters( 'wp_import_categories', $this->categories );
        $this->import_log->wpvivid_write_import_log('Start importing categories.', 'notice');
        if ( empty( $this->categories ) ) {
            $this->import_log->wpvivid_write_import_log('Categories import is completed.', 'notice');
            return $ret;
        }

        foreach ( $this->categories as $cat )
        {
            // if the category already exists leave it alone
            $term_id = term_exists( $cat['category_nicename'], 'category' );
            if ( $term_id )
            {
                if ( is_array($term_id) ) $term_id = $term_id['term_id'];
                if ( isset($cat['term_id']) )
                    $this->processed_terms[intval($cat['term_id'])] = (int) $term_id;
                continue;
            }

            $category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
            $category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
            $catarr = array(
                'category_nicename' => $cat['category_nicename'],
                'category_parent' => $category_parent,
                'cat_name' => $cat['cat_name'],
                'category_description' => $category_description
            );
            $catarr = wp_slash( $catarr );

            $id = wp_insert_category( $catarr );
            if ( ! is_wp_error( $id ) )
            {
                if ( isset($cat['term_id']) )
                    $this->processed_terms[intval($cat['term_id'])] = $id;
            } else {

                if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
                {
                    $this->import_log->wpvivid_write_import_log('Failed to import categories, error: '.$id->get_error_message(), 'notice');
                    $ret['result']='failed';
                    $ret['error']='Failed to import category '.$cat['category_nicename'].' '.$id->get_error_message();
                    return $ret;
                }
                continue;
            }

            $this->process_termmeta( $cat, $id['term_id'] );
        }

        unset( $this->categories );
        $this->import_log->wpvivid_write_import_log('Categories import is completed.', 'notice');
        return $ret;
    }

    private function process_termmeta( $term, $term_id )
    {
        if ( ! isset( $term['termmeta'] ) )
        {
            $term['termmeta'] = array();
        }

        $term['termmeta'] = apply_filters( 'wp_import_term_meta', $term['termmeta'], $term_id, $term );

        if ( empty( $term['termmeta'] ) ) {
            return;
        }

        foreach ( $term['termmeta'] as $meta )
        {
            $key = apply_filters( 'import_term_meta_key', $meta['key'], $term_id, $term );
            if ( ! $key ) {
                continue;
            }

            // Export gets meta straight from the DB so could have a serialized string
            $value = maybe_unserialize( $meta['value'] );

            add_term_meta( $term_id, $key, $value );

            do_action( 'import_term_meta', $term_id, $key, $value );
        }
    }

    private function process_tags()
    {
        $ret['result']='success';
        $this->tags = apply_filters( 'wp_import_tags', $this->tags );
        $this->import_log->wpvivid_write_import_log('Start importing tags.', 'notice');
        if ( empty( $this->tags ) ){
            $this->import_log->wpvivid_write_import_log('Tags import is completed.', 'notice');
            return $ret;
        }

        foreach ( $this->tags as $tag )
        {
            $term_id = term_exists( $tag['tag_slug'], 'post_tag' );
            if ( $term_id )
            {
                if ( is_array($term_id) ) $term_id = $term_id['term_id'];
                if ( isset($tag['term_id']) )
                    $this->processed_terms[intval($tag['term_id'])] = (int) $term_id;
                continue;
            }

            $tag = wp_slash( $tag );
            $tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
            $tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

            $id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
            if ( ! is_wp_error( $id ) )
            {
                if ( isset($tag['term_id']) )
                    $this->processed_terms[intval($tag['term_id'])] = $id['term_id'];
            } else {
                if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
                {
                    $this->import_log->wpvivid_write_import_log('Failed to import tags, error: '.$id->get_error_message(), 'notice');
                    $ret['result']='failed';
                    $ret['error']='Failed to import post tag '.$tag['tag_name'].' '.$id->get_error_message();
                    return $ret;
                }
                continue;
            }

            $this->process_termmeta( $tag, $id['term_id'] );
        }

        unset( $this->tags );
        $this->import_log->wpvivid_write_import_log('Tags import is completed.', 'notice');
        return $ret;
    }

    private function process_terms()
    {
        $ret['result']='success';
        $this->terms = apply_filters( 'wp_import_terms', $this->terms );
        $this->import_log->wpvivid_write_import_log('Start importing terms.', 'notice');
        if ( empty( $this->terms ) ) {
            $this->import_log->wpvivid_write_import_log('Terms import is completed.', 'notice');
            return $ret;
        }

        foreach ( $this->terms as $term )
        {
            // if the term already exists in the correct taxonomy leave it alone
            $term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
            if ( $term_id ) {
                if ( is_array($term_id) ) $term_id = $term_id['term_id'];
                if ( isset($term['term_id']) )
                    $this->processed_terms[intval($term['term_id'])] = (int) $term_id;
                continue;
            }

            if ( empty( $term['term_parent'] ) ) {
                $parent = 0;
            } else {
                $parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
                if ( is_array( $parent ) ) $parent = $parent['term_id'];
            }
            $term = wp_slash( $term );
            $description = isset( $term['term_description'] ) ? $term['term_description'] : '';
            $termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );

            $id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
            if ( ! is_wp_error( $id ) ) {
                if ( isset($term['term_id']) )
                    $this->processed_terms[intval($term['term_id'])] = $id['term_id'];
            } else {
                if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
                {
                    $this->import_log->wpvivid_write_import_log('Failed to import terms, error: '.$id->get_error_message(), 'notice');
                    $ret['result']='failed';
                    $ret['error']='Failed to import '.$term['term_taxonomy'].' '.$term['term_name'].' '.$id->get_error_message();
                    return $ret;
                }

                continue;
            }

            $this->process_termmeta( $term, $id['term_id'] );
        }

        unset( $this->terms );
        $this->import_log->wpvivid_write_import_log('Terms import is completed.', 'notice');
        return $ret;
    }

    private function process_posts_ex($update_exist=false)
    {
        $this->import_log->wpvivid_write_import_log('Start importing posts.', 'notice');
        $ret['result']='success';
        $this->posts = apply_filters( 'wp_import_posts', $this->posts );

        foreach ( $this->posts as $post )
        {
            $this->import_log->wpvivid_write_import_log('Post id: '.$post['post_id'], 'notice');
            $post = apply_filters( 'wp_import_post_data_raw', $post );
            $post_type_object = get_post_type_object( $post['post_type'] );
            $post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
            $post_exists = apply_filters( 'wp_import_existing_post', $post_exists, $post );
            if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] )
            {
                $this->import_log->wpvivid_write_import_log('The post already exists.', 'notice');
                $comment_post_ID=$post_id = $post_exists;
                $this->processed_posts[ intval( $post['post_id'] ) ] = intval( $post_exists );

                if($update_exist)
                {
                    $post_parent = (int) $post['post_parent'];
                    if ( $post_parent )
                    {
                        // if we already know the parent, map it to the new local ID
                        if ( isset( $this->processed_posts[$post_parent] ) )
                        {
                            $post_parent = $this->processed_posts[$post_parent];
                            // otherwise record the parent for later
                        } else {
                            $this->post_orphans[intval($post['post_id'])] = $post_parent;
                            $post_parent = 0;
                        }
                    }
                    $author = sanitize_user( $post['post_author'], true );
                    if ( isset( $this->author_mapping[$author] ) ) {
                        $author = $this->author_mapping[$author];
                    }
                    else {
                        $author = (int)$this->default_user;
                    }

                    $postdata = array(
                        'ID' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
                        'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
                        'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
                        'post_status' => $post['status'], 'post_name' => $post['post_name'],
                        'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
                        'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
                        'post_type' => $post['post_type'], 'post_password' => $post['post_password']
                    );

                    wp_update_post($postdata);

                    if ( ! empty( $post['postmeta'] ) )
                    {
                        foreach ( $post['postmeta'] as $meta )
                        {
                            $key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
                            $value = false;

                            if ( '_edit_last' == $key )
                            {
                                if ( isset( $this->processed_authors[intval($meta['value'])] ) )
                                    $value = $this->processed_authors[intval($meta['value'])];
                                else
                                    $key = false;
                            }

                            if ( $key )
                            {
                                // export gets meta straight from the DB so could have a serialized string
                                if ( ! $value )
                                    $value = maybe_unserialize( $meta['value'] );
                                if(metadata_exists('post', $post_id, $key))
                                {
                                    update_post_meta($post_id,$key,$value);
                                }
                                else
                                {
                                    add_post_meta( $post_id, $key, $value );
                                }


                                do_action( 'import_post_meta', $post_id, $key, $value );

                                // if the post has a featured image, take note of this in case of remap
                                if ( '_thumbnail_id' == $key )
                                    $this->featured_images[$post_id] = (int) $value;
                            }
                        }
                    }
                }

            } else {
                $post_parent = (int) $post['post_parent'];
                if ( $post_parent )
                {
                    // if we already know the parent, map it to the new local ID
                    if ( isset( $this->processed_posts[$post_parent] ) )
                    {
                        $post_parent = $this->processed_posts[$post_parent];
                        // otherwise record the parent for later
                    } else {
                        $this->post_orphans[intval($post['post_id'])] = $post_parent;
                        $post_parent = 0;
                    }
                }
                // map the post author
                $author = sanitize_user( $post['post_author'], true );
                if ( isset( $this->author_mapping[$author] ) ) {
                    $author = $this->author_mapping[$author];
                }
                else {
                    $author = (int)$this->default_user;
                }
                $postdata = array(
                    'import_id' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
                    'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
                    'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
                    'post_status' => $post['status'], 'post_name' => $post['post_name'],
                    'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
                    'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
                    'post_type' => $post['post_type'], 'post_password' => $post['post_password']
                );
                $original_post_ID = $post['post_id'];
                $postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );
                $postdata = wp_slash( $postdata );
                if ( 'attachment' == $postdata['post_type'] )
                {
                    $remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];
                    // try to use _wp_attached file for upload folder placement to ensure the same location as the export site
                    // e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
                    $postdata['upload_date'] = $post['post_date'];
                    if ( isset( $post['postmeta'] ) )
                    {
                        foreach( $post['postmeta'] as $meta )
                        {
                            if ( $meta['key'] == '_wp_attached_file' ) {
                                if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
                                    $postdata['upload_date'] = $matches[0];
                                break;
                            }
                        }
                        $postmeta=$post['postmeta'];
                    }
                    else
                    {
                        $postmeta=false;
                    }

                    $comment_post_ID = $post_id = $this->process_attachment_ex( $postdata, $remote_url ,$postmeta);
                } else {
                    $comment_post_ID =$post_id = wp_insert_post( $postdata, true );
                    do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
                }
                if ( is_wp_error( $post_id ) )
                {
                    if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
                    {
                        $ret['result']='failed';
                        $ret['error']='Failed to import '.$post_type_object->labels->singular_name.' '.$post['post_title'].' '.$post_id->get_error_message();
                        return $ret;
                    }
                    continue;
                }
                if ( $post['is_sticky'] == 1 )
                    stick_post( $post_id );
                // map pre-import ID to local ID
                $this->processed_posts[intval($post['post_id'])] = (int) $post_id;
            }

            if($post_exists)
                continue;

            if ( ! isset( $post['terms'] ) )
                $post['terms'] = array();

            $post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

            // add categories, tags and other terms
            if ( ! empty( $post['terms'] ) )
            {
                $terms_to_set = array();
                foreach ( $post['terms'] as $term )
                {
                    // back compat with WXR 1.0 map 'tag' to 'post_tag'
                    $taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
                    $term_exists = term_exists( $term['slug'], $taxonomy );
                    $term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
                    if ( ! $term_id )
                    {
                        $t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
                        if ( ! is_wp_error( $t ) )
                        {
                            $term_id = $t['term_id'];
                            do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
                        } else {
                            if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
                            {
                                $this->import_log->wpvivid_write_import_log('Failed to import post, error: '.$post_id->get_error_message(), 'notice');
                                $ret['result']='failed';
                                $ret['error']='Failed to import '.esc_html($taxonomy).' '.esc_html($term['name']).' '.$post_id->get_error_message();
                                return $ret;
                            }
                            continue;
                        }
                    }
                    $terms_to_set[$taxonomy][] = intval( $term_id );
                }

                foreach ( $terms_to_set as $tax => $ids )
                {
                    $tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
                    do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
                }
                unset( $post['terms'], $terms_to_set );
            }

            if ( ! isset( $post['comments'] ) )
                $post['comments'] = array();

            $post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

            // add/update comments
            if ( ! empty( $post['comments'] ) )
            {
                $num_comments = 0;
                $inserted_comments = array();
                foreach ( $post['comments'] as $comment )
                {
                    $comment_id	= $comment['comment_id'];
                    $newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
                    $newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
                    $newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
                    $newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
                    $newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
                    $newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
                    $newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
                    $newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
                    $newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
                    $newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
                    $newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
                    $newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
                    if ( isset( $this->processed_authors[$comment['comment_user_id']] ) )
                        $newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
                }
                ksort( $newcomments );

                foreach ( $newcomments as $key => $comment )
                {
                    // if this is a new post we can skip the comment_exists() check
                    if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) )
                    {
                        if ( isset( $inserted_comments[$comment['comment_parent']] ) )
                            $comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
                        $comment = wp_slash( $comment );
                        $comment = wp_filter_comment( $comment );
                        $inserted_comments[$key] = wp_insert_comment( $comment );
                        do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

                        foreach( $comment['commentmeta'] as $meta ) {
                            $value = maybe_unserialize( $meta['value'] );
                            add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
                        }

                        $num_comments++;
                    }
                }
                unset( $newcomments, $inserted_comments, $post['comments'] );
            }

            if ( ! isset( $post['postmeta'] ) )
                $post['postmeta'] = array();

            $post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

            // add/update post meta
            if ( ! empty( $post['postmeta'] ) )
            {
                foreach ( $post['postmeta'] as $meta )
                {
                    $key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
                    $value = false;

                    if ( '_edit_last' == $key )
                    {
                        if ( isset( $this->processed_authors[intval($meta['value'])] ) )
                            $value = $this->processed_authors[intval($meta['value'])];
                        else
                            $key = false;
                    }

                    if ( $key )
                    {
                        // export gets meta straight from the DB so could have a serialized string
                        if ( ! $value )
                            $value = maybe_unserialize( $meta['value'] );
                        if(metadata_exists('post', $post_id, $key))
                        {
                            update_post_meta($post_id,$key,$value);
                        }
                        else
                        {
                            add_post_meta( $post_id, $key, $value );
                        }


                        do_action( 'import_post_meta', $post_id, $key, $value );

                        // if the post has a featured image, take note of this in case of remap
                        if ( '_thumbnail_id' == $key )
                            $this->featured_images[$post_id] = (int) $value;
                    }
                }
            }
        }

        unset( $this->posts );
        $this->import_log->wpvivid_write_import_log('Posts import is completed.', 'notice');
        return $ret;
    }

    public function replace_domain()
    {
        $this->new_site_url= untrailingslashit(site_url());
        $this->import_log->wpvivid_write_import_log('The original domain name: '.$this->base_url, 'notice');
        $this->import_log->wpvivid_write_import_log('The current domain name: '.$this->new_site_url, 'notice');
        if(empty($this->base_url))
        {
            $this->import_log->wpvivid_write_import_log('Failed to retrieve the original domain name: '.$this->base_url, 'notice');
            return ;
        }

        if(empty($this->processed_posts))
        {
            $this->import_log->wpvivid_write_import_log('The unimported posts', 'notice');
            return ;
        }

        if($this->base_url===$this->new_site_url)
        {
            $this->import_log->wpvivid_write_import_log('Replacing domain name is not required.', 'notice');
            return ;
        }


        global $wp_query,$wpdb;
        $this->import_log->wpvivid_write_import_log('Start replacing domain name.', 'notice');
        $wp_query->in_the_loop = true;
        while ( $next_posts = array_splice( $this->processed_posts, 0, 20 ) )
        {
            $where = 'WHERE ID IN (' . join(',', $next_posts) . ')';
            $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");

            foreach ( $posts as $post )
            {
                $old_data=$post->post_content;
                $new_data=$this->replace_row_data($old_data);
                if($new_data==$old_data)
                {
                    $this->import_log->wpvivid_write_import_log('Post ID '.$post->ID.' is not changed.', 'notice');
                    continue;
                }
                else
                {
                    $this->import_log->wpvivid_write_import_log('Post ID '.$post->ID.' is changed.', 'notice');
                }
                $post->post_content=$new_data;
                wp_update_post($post);
            }
        }
    }

    private function replace_row_data($old_data)
    {
        $unserialize_data = @unserialize($old_data);
        if($unserialize_data===false)
        {
            $old_data=$this->replace_string($old_data);
        }
        else
        {
            $old_data=$this->replace_serialize_data($unserialize_data);
            $old_data=serialize($old_data);
            /*if(is_array($unserialize_data))
            {
                $temp_data = array();
                foreach ($unserialize_data as $key => $value)
                {
                    $temp_data[$key]=$this->replace_string($value);
                }

                $old_data = $temp_data;
                unset($temp_data);
                $old_data=$this->replace_serialize_data($unserialize_data);
                $old_data=serialize($old_data);
            }
            else if(is_object($unserialize_data))
            {
                $temp_data = $unserialize_data;
                $props = get_object_vars($unserialize_data);
                foreach ($props as $key => $value)
                {
                    $temp_data->$key =$this->replace_string($value);
                }
                $old_data = $temp_data;
                unset($temp_data);
                $old_data=serialize($old_data);
            }*/
        }

        return $old_data;
    }

    private function replace_serialize_data($data)
    {
        if(is_string($data))
        {
            $serialize_data =@unserialize($data);
            if($serialize_data===false)
            {
                $data=$this->replace_string($data);
            }
            else
            {
                $data=serialize($this->replace_serialize_data($serialize_data));
            }
        }
        else if(is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if(is_string($value))
                {
                    $data[$key]=$this->replace_string($value);
                }
                else if(is_array($value))
                {
                    $data[$key]=$this->replace_serialize_data($value);
                }
                else if(is_object($value))
                {
                    if (is_a($value, '__PHP_Incomplete_Class'))
                    {
                        //
                    }
                    else
                    {
                        $data[$key]=$this->replace_serialize_data($value);
                    }
                }
            }
        }
        else if(is_object($data))
        {
            $temp = $data; // new $data_class();
            if (is_a($data, '__PHP_Incomplete_Class'))
            {

            }
            else
            {
                $props = get_object_vars($data);
                foreach ($props as $key => $value)
                {
                    if(is_string($value))
                    {
                        $temp->$key =$this->replace_string($value);
                    }
                    else if(is_array($value))
                    {
                        $temp->$key=$this->replace_serialize_data($value);
                    }
                    else if(is_object($value))
                    {
                        $temp->$key=$this->replace_serialize_data($value);
                    }
                }
            }
            $data = $temp;
            unset($temp);
        }

        return $data;
    }

    private function replace_string($old_string)
    {
        if(!is_string($old_string))
        {
            return $old_string;
        }

        if($this->base_url!=$this->new_site_url)
        {
            $remove_http_link=$this->get_remove_http_link($this->base_url);
            $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
            if(strpos($new_remove_http_link,$remove_http_link)!==false)
            {
                return $this->replace_string_ex($old_string);
            }
        }

        if($this->base_url!=$this->new_site_url)
        {
            $old_string=str_replace($this->base_url,$this->new_site_url,$old_string);
            $old_mix_link=$this->get_mix_link($this->base_url);
            if($old_mix_link!==false)
            {
                $old_string=str_replace($old_mix_link,$this->new_site_url,$old_string);
            }
            $remove_http_link=$this->get_remove_http_link($this->base_url);
            if($remove_http_link!==false)
            {
                $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
                $old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
            }

            $remove_http_link=$this->get_remove_http_link_ex($this->base_url);
            if($remove_http_link!==false)
            {
                $new_remove_http_link=$this->get_remove_http_link_ex($this->new_site_url);
                $old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
            }
        }

        return $old_string;
    }

    private function replace_string_ex($old_string)
    {
        if(!is_string($old_string))
        {
            return $old_string;
        }

        if($this->base_url!=$this->new_site_url)
        {
            $remove_http_link=$this->get_remove_http_link($this->base_url);
            if($remove_http_link!==false)
            {
                $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
                $old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
            }

            $new_mix_link=$this->get_mix_link($this->new_site_url);
            if($new_mix_link!==false)
            {
                $old_string=str_replace($new_mix_link,$this->new_site_url,$old_string);
            }

            $remove_http_link=$this->get_remove_http_link_ex($this->base_url);
            if($remove_http_link!==false)
            {
                $new_remove_http_link=$this->get_remove_http_link_ex($this->new_site_url);
                $old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
            }
        }

        return $old_string;
    }

    private function get_remove_http_link($url)
    {
        if (0 === stripos($url, 'https://'))
        {
            $mix_link = '//'.substr($url, 8);
        } elseif (0 === stripos($url, 'http://')) {
            $mix_link = '//'.substr($url, 7);
        }
        else
        {
            $mix_link=false;
        }
        return $mix_link;
    }

    private function get_remove_http_link_ex($url)
    {
        if (0 === stripos($url, 'https://'))
        {
            $mix_link = '\/\/'.substr($url, 8);
        } elseif (0 === stripos($url, 'http://')) {
            $mix_link = '\/\/'.substr($url, 7);
        }
        else
        {
            $mix_link=false;
        }
        return $mix_link;
    }

    private function get_mix_link($url)
    {
        if (0 === stripos($url, 'https://'))
        {
            $mix_link = 'http://'.substr($url, 8);
        } elseif (0 === stripos($url, 'http://')) {
            $mix_link = 'https://'.substr($url, 7);
        }
        else
        {
            $mix_link=false;
        }
        return $mix_link;
    }

    function process_attachment_ex( $post, $url,$postmeta )
    {
        // if the URL is absolute, but does not contain address, then upload it assuming base_site_url
        if ( preg_match( '|^/[\w\W]+$|', $url ) )
            $url = rtrim( $this->base_url, '/' ) . $url;

        $upload = $this->fetch_local_file_ex( $url, $post ,$postmeta);
        if ( is_wp_error( $upload ) )
            return $upload;
        $post['post_mime_type']=$upload['type'];

        $post['guid'] = $upload['url'];

        // as per wp-admin/includes/upload.php
        $post_id = wp_insert_attachment( $post, $upload['file'] );

        if ( is_wp_error( $post_id ) )
        {
            return $post_id;
        }

        if ( preg_match( '!^image/!',$upload['type'] ) )
        {
            $parts = pathinfo( $url );
            $name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

            $parts_new = pathinfo( $upload['url'] );
            $name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

            $this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
        }

        return $post_id;
    }

    function fetch_local_file_ex($url,$post,$postmeta)
    {
        $file_name = basename( $url );

        $upload = wp_upload_dir( $post['upload_date'] );

        $new_file='';
        if($postmeta!==false)
        {
            foreach( $postmeta as $meta )
            {
                if ( $meta['key'] == '_wp_attached_file' )
                {
                    $new_file=$upload['basedir'].'/'.$meta['meta_value'];
                    $url = $upload['baseurl'].'/'.$meta['meta_value'];
                }
            }
        }

        if(empty($new_file))
        {
            $new_file = $upload['path'] . "/$file_name";
            $url = $upload['url'] . "/$file_name";
        }

        if(!file_exists($new_file))
        {
            return new WP_Error( 'import_file_error', __('File not exist, file:'.$new_file, 'wpvivid-backuprestore') );
        }

        $wp_filetype = wp_check_filetype( $file_name );

        if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) )
        {
            return new WP_Error( 'import_file_error', __( 'Sorry, this file type is not permitted for security reasons.' ) );
        }

        return apply_filters(
            'wp_handle_upload',
            array(
                'file'  => $new_file,
                'url'   => $url,
                'type'  => $wp_filetype['type'],
                'error' => false,
            ),
            'sideload'
        );
    }

    function parse( $file ) {
        $parser = new WPvivid_WXR_Parser();
        return $parser->parse( $file );
    }

    function get_authors_from_import( $import_data )
    {
        if ( ! empty( $import_data['authors'] ) )
        {
            $this->authors = $import_data['authors'];
            // no author information, grab it from the posts
        } else {
            foreach ( $import_data['posts'] as $post )
            {
                $login = sanitize_user( $post['post_author'], true );
                if ( empty( $login ) )
                {
                    continue;
                }

                if ( ! isset($this->authors[$login]) )
                    $this->authors[$login] = array(
                        'author_login' => $login,
                        'author_display_name' => $post['post_author']
                    );
            }
        }
    }

    function process_attachment( $post, $url )
    {
        if ( ! $this->fetch_attachments )
            return new WP_Error( 'attachment_processing_error',
                __( 'Fetching attachments is not enabled', 'wpvivid-backuprestore' ) );

        // if the URL is absolute, but does not contain address, then upload it assuming base_site_url
        if ( preg_match( '|^/[\w\W]+$|', $url ) )
            $url = rtrim( $this->base_url, '/' ) . $url;

        $upload = $this->fetch_local_file( $url, $post );
        if ( is_wp_error( $upload ) )
            return $upload;

        if ( $info = wp_check_filetype( $upload['file'] ) )
            $post['post_mime_type'] = $info['type'];
        else
            return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'wpvivid-backuprestore') );

        $post['guid'] = $upload['url'];

        // as per wp-admin/includes/upload.php
        $post_id = wp_insert_attachment( $post, $upload['file'] );

        if ( is_wp_error( $post_id ) )
        {
            echo 'error file:'.$upload['file'];
        }

        //$metadata=wp_generate_attachment_metadata( $post_id, $upload['file'] );
        //wp_update_attachment_metadata( $post_id,$metadata  );

        // remap resized image URLs, works by stripping the extension and remapping the URL stub.
        if ( preg_match( '!^image/!', $info['type'] ) ) {
            $parts = pathinfo( $url );
            $name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

            $parts_new = pathinfo( $upload['url'] );
            $name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

            $this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
        }

        return $post_id;
    }

    function fetch_local_file($url,$post)
    {
        $file_name = basename( $url );

        $upload = wp_upload_dir( $post['upload_date'] );
        $new_file = $upload['path'] . "/$file_name";
        $url = $upload['url'] . "/$file_name";

        $wp_filetype = wp_check_filetype( $file_name );

        if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) ) {
            return array( 'error' => __( 'Sorry, this file type is not permitted for security reasons.' ) );
        }

        if(!file_exists($new_file))
        {
            return new WP_Error( 'import_file_error', __('File not exist file:'.$new_file, 'wpvivid-backuprestore') );
        }

        return apply_filters(
            'wp_handle_upload',
            array(
                'file'  => $new_file,
                'url'   => $url,
                'type'  => $wp_filetype['type'],
                'error' => false,
            ),
            'sideload'
        );
    }

    /**
     * Performs post-import cleanup of files and the cache
     */
    function import_end()
    {
        wp_import_cleanup( $this->id );

        wp_cache_flush();
        foreach ( get_taxonomies() as $tax ) {
            delete_option( "{$tax}_children" );
            _get_term_hierarchy( $tax );
        }

        wp_defer_term_counting( false );
        wp_defer_comment_counting( false );

        $ret['result']='success';

        do_action( 'import_end' );
        return $ret;
    }
}

function wpvivid_function_pre_extract_import_callback($p_event, &$p_header)
{
    global $xml_file_name;

    if(strpos($p_header['filename'],$xml_file_name)!==false)
    {
        return 0;
    }

    if(strpos($p_header['filename'],'wpvivid_export_package_info.json')!==false)
    {
        return 0;
    }

    return 1;
}

/**
 * WordPress Importer class for managing parsing of WXR files.
 */
class WPvivid_WXR_Parser
{
    function parse( $file )
    {
        // Attempt to use proper XML parsers first
        if ( extension_loaded( 'simplexml' ) )
        {
            $parser = new WPvivid_WXR_Parser_SimpleXML;
            $result = $parser->parse( $file );

            // If SimpleXML succeeds or this is an invalid WXR file then return the results
            if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() )
                return $result;
        }
        else if ( extension_loaded( 'xml' ) )
        {
            $parser = new WPvivid_WXR_Parser_XML;
            $result = $parser->parse( $file );

            // If XMLParser succeeds or this is an invalid WXR file then return the results
            if ( ! is_wp_error( $result ) || 'XML_parse_error' != $result->get_error_code() )
                return $result;
        }

        // We have a malformed XML file, so display the error and fallthrough to regex
        if ( isset($result) && defined('IMPORT_DEBUG') && IMPORT_DEBUG )
        {
            $msg='';
            if ( 'SimpleXML_parse_error' == $result->get_error_code() )
            {
                foreach  ( $result->get_error_data() as $error )
                    $msg.= $error->line . ':' . $error->column . ' ' . esc_html( $error->message ) . "\n";
            } else if ( 'XML_parse_error' == $result->get_error_code() )
            {
                $error = $result->get_error_data();
                $msg.= $error[0] . ':' . $error[1] . ' ' . esc_html( $error[2] );
            }
            $msg.=__( 'There was an error when reading this WXR file', 'wpvivid-backuprestore' ) ;
            $msg.=__( 'Details are shown above. The importer will now try again with a different parser...', 'wpvivid-backuprestore' );

            return new WP_Error( 'WXR_Parser_error', $msg,'' );
        }

        // use regular expressions if nothing else available or this is bad XML
        $parser = new WPvivid_WXR_Parser_Regex;
        return $parser->parse( $file );
    }
}

/**
 * WXR Parser that makes use of the SimpleXML PHP extension.
 */
class WPvivid_WXR_Parser_SimpleXML
{
    function parse( $file )
    {
        $authors = $posts = $categories = $tags = $terms = array();

        $internal_errors = libxml_use_internal_errors(true);

        $dom = new DOMDocument;
        $old_value = null;
        if ( function_exists( 'libxml_disable_entity_loader' ) ) {
            $old_value = libxml_disable_entity_loader( true );
        }
        $success = $dom->loadXML( file_get_contents( $file ) );
        if ( ! is_null( $old_value ) )
        {
            libxml_disable_entity_loader( $old_value );
        }

        if ( ! $success || isset( $dom->doctype ) )
        {
            return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wpvivid-backuprestore' ), libxml_get_errors() );
        }

        $xml = simplexml_import_dom( $dom );
        unset( $dom );

        // halt if loading produces an error
        if ( ! $xml )
            return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wpvivid-backuprestore' ), libxml_get_errors() );

        $wxr_version = $xml->xpath('/rss/channel/wp:wxr_version');
        if ( ! $wxr_version )
            return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wpvivid-backuprestore' ) );

        $wxr_version = (string) trim( $wxr_version[0] );
        // confirm that we are dealing with the correct file format
        if ( ! preg_match( '/^\d+\.\d+$/', $wxr_version ) )
            return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wpvivid-backuprestore' ) );

        $base_url = $xml->xpath('/rss/channel/wp:base_site_url');
        $base_url = (string) trim( $base_url[0] );

        $namespaces = $xml->getDocNamespaces();
        if ( ! isset( $namespaces['wp'] ) )
            $namespaces['wp'] = 'http://wordpress.org/export/1.1/';
        if ( ! isset( $namespaces['excerpt'] ) )
            $namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';

        // grab authors
        foreach ( $xml->xpath('/rss/channel/wp:author') as $author_arr )
        {
            $a = $author_arr->children( $namespaces['wp'] );
            $login = (string) $a->author_login;
            $authors[$login] = array(
                'author_id' => (int) $a->author_id,
                'author_login' => $login,
                'author_email' => (string) $a->author_email,
                'author_display_name' => (string) $a->author_display_name,
                'author_first_name' => (string) $a->author_first_name,
                'author_last_name' => (string) $a->author_last_name
            );
        }

        // grab cats, tags and terms
        foreach ( $xml->xpath('/rss/channel/wp:category') as $term_arr )
        {
            $t = $term_arr->children( $namespaces['wp'] );
            $category = array(
                'term_id' => (int) $t->term_id,
                'category_nicename' => (string) $t->category_nicename,
                'category_parent' => (string) $t->category_parent,
                'cat_name' => (string) $t->cat_name,
                'category_description' => (string) $t->category_description
            );

            foreach ( $t->termmeta as $meta ) {
                $category['termmeta'][] = array(
                    'key' => (string) $meta->meta_key,
                    'value' => (string) $meta->meta_value
                );
            }

            $categories[] = $category;
        }

        foreach ( $xml->xpath('/rss/channel/wp:tag') as $term_arr )
        {
            $t = $term_arr->children( $namespaces['wp'] );
            $tag = array(
                'term_id' => (int) $t->term_id,
                'tag_slug' => (string) $t->tag_slug,
                'tag_name' => (string) $t->tag_name,
                'tag_description' => (string) $t->tag_description
            );

            foreach ( $t->termmeta as $meta ) {
                $tag['termmeta'][] = array(
                    'key' => (string) $meta->meta_key,
                    'value' => (string) $meta->meta_value
                );
            }

            $tags[] = $tag;
        }

        foreach ( $xml->xpath('/rss/channel/wp:term') as $term_arr )
        {
            $t = $term_arr->children( $namespaces['wp'] );
            $term = array(
                'term_id' => (int) $t->term_id,
                'term_taxonomy' => (string) $t->term_taxonomy,
                'slug' => (string) $t->term_slug,
                'term_parent' => (string) $t->term_parent,
                'term_name' => (string) $t->term_name,
                'term_description' => (string) $t->term_description
            );

            foreach ( $t->termmeta as $meta ) {
                $term['termmeta'][] = array(
                    'key' => (string) $meta->meta_key,
                    'value' => (string) $meta->meta_value
                );
            }

            $terms[] = $term;
        }

        // grab posts
        foreach ( $xml->channel->item as $item )
        {
            $post = array(
                'post_title' => (string) $item->title,
                'guid' => (string) $item->guid,
            );

            $dc = $item->children( 'http://purl.org/dc/elements/1.1/' );
            $post['post_author'] = (string) $dc->creator;

            $content = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
            $excerpt = $item->children( $namespaces['excerpt'] );
            $post['post_content'] = (string) $content->encoded;
            $post['post_excerpt'] = (string) $excerpt->encoded;

            $wp = $item->children( $namespaces['wp'] );
            $post['post_id'] = (int) $wp->post_id;
            $post['post_date'] = (string) $wp->post_date;
            $post['post_date_gmt'] = (string) $wp->post_date_gmt;
            $post['comment_status'] = (string) $wp->comment_status;
            $post['ping_status'] = (string) $wp->ping_status;
            $post['post_name'] = (string) $wp->post_name;
            $post['status'] = (string) $wp->status;
            $post['post_parent'] = (int) $wp->post_parent;
            $post['menu_order'] = (int) $wp->menu_order;
            $post['post_type'] = (string) $wp->post_type;
            $post['post_password'] = (string) $wp->post_password;
            $post['is_sticky'] = (int) $wp->is_sticky;

            if ( isset($wp->attachment_url) )
                $post['attachment_url'] = (string) $wp->attachment_url;

            foreach ( $item->category as $c )
            {
                $att = $c->attributes();
                if ( isset( $att['nicename'] ) )
                    $post['terms'][] = array(
                        'name' => (string) $c,
                        'slug' => (string) $att['nicename'],
                        'domain' => (string) $att['domain']
                    );
            }

            foreach ( $wp->postmeta as $meta )
            {
                $post['postmeta'][] = array(
                    'key' => (string) $meta->meta_key,
                    'value' => (string) $meta->meta_value
                );
            }

            foreach ( $wp->comment as $comment )
            {
                $meta = array();
                if ( isset( $comment->commentmeta ) ) {
                    foreach ( $comment->commentmeta as $m ) {
                        $meta[] = array(
                            'key' => (string) $m->meta_key,
                            'value' => (string) $m->meta_value
                        );
                    }
                }

                $post['comments'][] = array(
                    'comment_id' => (int) $comment->comment_id,
                    'comment_author' => (string) $comment->comment_author,
                    'comment_author_email' => (string) $comment->comment_author_email,
                    'comment_author_IP' => (string) $comment->comment_author_IP,
                    'comment_author_url' => (string) $comment->comment_author_url,
                    'comment_date' => (string) $comment->comment_date,
                    'comment_date_gmt' => (string) $comment->comment_date_gmt,
                    'comment_content' => (string) $comment->comment_content,
                    'comment_approved' => (string) $comment->comment_approved,
                    'comment_type' => (string) $comment->comment_type,
                    'comment_parent' => (string) $comment->comment_parent,
                    'comment_user_id' => (int) $comment->comment_user_id,
                    'commentmeta' => $meta,
                );
            }

            $posts[] = $post;
        }

        return array(
            'authors' => $authors,
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'terms' => $terms,
            'base_url' => $base_url,
            'version' => $wxr_version
        );
    }
}

/**
 * WXR Parser that makes use of the XML Parser PHP extension.
 */
class WPvivid_WXR_Parser_XML {
    var $wp_tags = array(
        'wp:post_id', 'wp:post_date', 'wp:post_date_gmt', 'wp:comment_status', 'wp:ping_status', 'wp:attachment_url',
        'wp:status', 'wp:post_name', 'wp:post_parent', 'wp:menu_order', 'wp:post_type', 'wp:post_password',
        'wp:is_sticky', 'wp:term_id', 'wp:category_nicename', 'wp:category_parent', 'wp:cat_name', 'wp:category_description',
        'wp:tag_slug', 'wp:tag_name', 'wp:tag_description', 'wp:term_taxonomy', 'wp:term_parent',
        'wp:term_name', 'wp:term_description', 'wp:author_id', 'wp:author_login', 'wp:author_email', 'wp:author_display_name',
        'wp:author_first_name', 'wp:author_last_name',
    );
    var $wp_sub_tags = array(
        'wp:comment_id', 'wp:comment_author', 'wp:comment_author_email', 'wp:comment_author_url',
        'wp:comment_author_IP',	'wp:comment_date', 'wp:comment_date_gmt', 'wp:comment_content',
        'wp:comment_approved', 'wp:comment_type', 'wp:comment_parent', 'wp:comment_user_id',
    );

    function parse( $file ) {
        $this->wxr_version = $this->in_post = $this->cdata = $this->data = $this->sub_data = $this->in_tag = $this->in_sub_tag = false;
        $this->authors = $this->posts = $this->term = $this->category = $this->tag = array();

        $xml = xml_parser_create( 'UTF-8' );
        xml_parser_set_option( $xml, XML_OPTION_SKIP_WHITE, 1 );
        xml_parser_set_option( $xml, XML_OPTION_CASE_FOLDING, 0 );
        xml_set_object( $xml, $this );
        xml_set_character_data_handler( $xml, 'cdata' );
        xml_set_element_handler( $xml, 'tag_open', 'tag_close' );

        if ( ! xml_parse( $xml, file_get_contents( $file ), true ) ) {
            $current_line = xml_get_current_line_number( $xml );
            $current_column = xml_get_current_column_number( $xml );
            $error_code = xml_get_error_code( $xml );
            $error_string = xml_error_string( $error_code );
            return new WP_Error( 'XML_parse_error', 'There was an error when reading this WXR file', array( $current_line, $current_column, $error_string ) );
        }
        xml_parser_free( $xml );

        if ( ! preg_match( '/^\d+\.\d+$/', $this->wxr_version ) )
            return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wpvivid-backuprestore' ) );

        return array(
            'authors' => $this->authors,
            'posts' => $this->posts,
            'categories' => $this->category,
            'tags' => $this->tag,
            'terms' => $this->term,
            'base_url' => $this->base_url,
            'version' => $this->wxr_version
        );
    }

    function tag_open( $parse, $tag, $attr ) {
        if ( in_array( $tag, $this->wp_tags ) ) {
            $this->in_tag = substr( $tag, 3 );
            return;
        }

        if ( in_array( $tag, $this->wp_sub_tags ) ) {
            $this->in_sub_tag = substr( $tag, 3 );
            return;
        }

        switch ( $tag ) {
            case 'category':
                if ( isset($attr['domain'], $attr['nicename']) ) {
                    $this->sub_data['domain'] = $attr['domain'];
                    $this->sub_data['slug'] = $attr['nicename'];
                }
                break;
            case 'item': $this->in_post = true;
            case 'title': if ( $this->in_post ) $this->in_tag = 'post_title'; break;
            case 'guid': $this->in_tag = 'guid'; break;
            case 'dc:creator': $this->in_tag = 'post_author'; break;
            case 'content:encoded': $this->in_tag = 'post_content'; break;
            case 'excerpt:encoded': $this->in_tag = 'post_excerpt'; break;

            case 'wp:term_slug': $this->in_tag = 'slug'; break;
            case 'wp:meta_key': $this->in_sub_tag = 'key'; break;
            case 'wp:meta_value': $this->in_sub_tag = 'value'; break;
        }
    }

    function cdata( $parser, $cdata ) {
        if ( ! trim( $cdata ) )
            return;

        if ( false !== $this->in_tag || false !== $this->in_sub_tag ) {
            $this->cdata .= $cdata;
        } else {
            $this->cdata .= trim( $cdata );
        }
    }

    function tag_close( $parser, $tag ) {
        switch ( $tag ) {
            case 'wp:comment':
                unset( $this->sub_data['key'], $this->sub_data['value'] ); // remove meta sub_data
                if ( ! empty( $this->sub_data ) )
                    $this->data['comments'][] = $this->sub_data;
                $this->sub_data = false;
                break;
            case 'wp:commentmeta':
                $this->sub_data['commentmeta'][] = array(
                    'key' => $this->sub_data['key'],
                    'value' => $this->sub_data['value']
                );
                break;
            case 'category':
                if ( ! empty( $this->sub_data ) ) {
                    $this->sub_data['name'] = $this->cdata;
                    $this->data['terms'][] = $this->sub_data;
                }
                $this->sub_data = false;
                break;
            case 'wp:postmeta':
                if ( ! empty( $this->sub_data ) )
                    $this->data['postmeta'][] = $this->sub_data;
                $this->sub_data = false;
                break;
            case 'item':
                $this->posts[] = $this->data;
                $this->data = false;
                break;
            case 'wp:category':
            case 'wp:tag':
            case 'wp:term':
                $n = substr( $tag, 3 );
                array_push( $this->$n, $this->data );
                $this->data = false;
                break;
            case 'wp:author':
                if ( ! empty($this->data['author_login']) )
                    $this->authors[$this->data['author_login']] = $this->data;
                $this->data = false;
                break;
            case 'wp:base_site_url':
                $this->base_url = $this->cdata;
                break;
            case 'wp:wxr_version':
                $this->wxr_version = $this->cdata;
                break;

            default:
                if ( $this->in_sub_tag ) {
                    $this->sub_data[$this->in_sub_tag] = ! empty( $this->cdata ) ? $this->cdata : '';
                    $this->in_sub_tag = false;
                } else if ( $this->in_tag ) {
                    $this->data[$this->in_tag] = ! empty( $this->cdata ) ? $this->cdata : '';
                    $this->in_tag = false;
                }
        }

        $this->cdata = false;
    }
}

/**
 * WXR Parser that uses regular expressions. Fallback for installs without an XML parser.
 */
class WPvivid_WXR_Parser_Regex {
    var $authors = array();
    var $posts = array();
    var $categories = array();
    var $tags = array();
    var $terms = array();
    var $base_url = '';

    function __construct() {
        $this->has_gzip = is_callable( 'gzopen' );
    }

    function parse( $file ) {
        $wxr_version = $in_multiline = false;

        $multiline_content = '';

        $multiline_tags = array(
            'item'        => array( 'posts', array( $this, 'process_post' ) ),
            'wp:category' => array( 'categories', array( $this, 'process_category' ) ),
            'wp:tag'      => array( 'tags', array( $this, 'process_tag' ) ),
            'wp:term'     => array( 'terms', array( $this, 'process_term' ) ),
        );

        $fp = $this->fopen( $file, 'r' );
        if ( $fp ) {
            while ( ! $this->feof( $fp ) ) {
                $importline = rtrim( $this->fgets( $fp ) );

                if ( ! $wxr_version && preg_match( '|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version ) )
                    $wxr_version = $version[1];

                if ( false !== strpos( $importline, '<wp:base_site_url>' ) ) {
                    preg_match( '|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url );
                    $this->base_url = $url[1];
                    continue;
                }

                if ( false !== strpos( $importline, '<wp:author>' ) ) {
                    preg_match( '|<wp:author>(.*?)</wp:author>|is', $importline, $author );
                    $a = $this->process_author( $author[1] );
                    $this->authors[$a['author_login']] = $a;
                    continue;
                }

                foreach ( $multiline_tags as $tag => $handler ) {
                    // Handle multi-line tags on a singular line
                    if ( preg_match( '|<' . $tag . '>(.*?)</' . $tag . '>|is', $importline, $matches ) ) {
                        $this->{$handler[0]}[] = call_user_func( $handler[1], $matches[1] );

                    } elseif ( false !== ( $pos = strpos( $importline, "<$tag>" ) ) ) {
                        // Take note of any content after the opening tag
                        $multiline_content = trim( substr( $importline, $pos + strlen( $tag ) + 2 ) );

                        // We don't want to have this line added to `$is_multiline` below.
                        $importline        = '';
                        $in_multiline      = $tag;

                    } elseif ( false !== ( $pos = strpos( $importline, "</$tag>" ) ) ) {
                        $in_multiline          = false;
                        $multiline_content    .= trim( substr( $importline, 0, $pos ) );

                        $this->{$handler[0]}[] = call_user_func( $handler[1], $multiline_content );
                    }
                }

                if ( $in_multiline && $importline ) {
                    $multiline_content .= $importline . "\n";
                }
            }

            $this->fclose($fp);
        }

        if ( ! $wxr_version )
            return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wpvivid-backuprestore' ) );

        return array(
            'authors' => $this->authors,
            'posts' => $this->posts,
            'categories' => $this->categories,
            'tags' => $this->tags,
            'terms' => $this->terms,
            'base_url' => $this->base_url,
            'version' => $wxr_version
        );
    }

    function get_tag( $string, $tag ) {
        preg_match( "|<$tag.*?>(.*?)</$tag>|is", $string, $return );
        if ( isset( $return[1] ) ) {
            if ( substr( $return[1], 0, 9 ) == '<![CDATA[' ) {
                if ( strpos( $return[1], ']]]]><![CDATA[>' ) !== false ) {
                    preg_match_all( '|<!\[CDATA\[(.*?)\]\]>|s', $return[1], $matches );
                    $return = '';
                    foreach( $matches[1] as $match )
                        $return .= $match;
                } else {
                    $return = preg_replace( '|^<!\[CDATA\[(.*)\]\]>$|s', '$1', $return[1] );
                }
            } else {
                $return = $return[1];
            }
        } else {
            $return = '';
        }
        return $return;
    }

    function process_category( $c ) {
        return array(
            'term_id' => $this->get_tag( $c, 'wp:term_id' ),
            'cat_name' => $this->get_tag( $c, 'wp:cat_name' ),
            'category_nicename'	=> $this->get_tag( $c, 'wp:category_nicename' ),
            'category_parent' => $this->get_tag( $c, 'wp:category_parent' ),
            'category_description' => $this->get_tag( $c, 'wp:category_description' ),
        );
    }

    function process_tag( $t ) {
        return array(
            'term_id' => $this->get_tag( $t, 'wp:term_id' ),
            'tag_name' => $this->get_tag( $t, 'wp:tag_name' ),
            'tag_slug' => $this->get_tag( $t, 'wp:tag_slug' ),
            'tag_description' => $this->get_tag( $t, 'wp:tag_description' ),
        );
    }

    function process_term( $t ) {
        return array(
            'term_id' => $this->get_tag( $t, 'wp:term_id' ),
            'term_taxonomy' => $this->get_tag( $t, 'wp:term_taxonomy' ),
            'slug' => $this->get_tag( $t, 'wp:term_slug' ),
            'term_parent' => $this->get_tag( $t, 'wp:term_parent' ),
            'term_name' => $this->get_tag( $t, 'wp:term_name' ),
            'term_description' => $this->get_tag( $t, 'wp:term_description' ),
        );
    }

    function process_author( $a ) {
        return array(
            'author_id' => $this->get_tag( $a, 'wp:author_id' ),
            'author_login' => $this->get_tag( $a, 'wp:author_login' ),
            'author_email' => $this->get_tag( $a, 'wp:author_email' ),
            'author_display_name' => $this->get_tag( $a, 'wp:author_display_name' ),
            'author_first_name' => $this->get_tag( $a, 'wp:author_first_name' ),
            'author_last_name' => $this->get_tag( $a, 'wp:author_last_name' ),
        );
    }

    function process_post( $post ) {
        $post_id        = $this->get_tag( $post, 'wp:post_id' );
        $post_title     = $this->get_tag( $post, 'title' );
        $post_date      = $this->get_tag( $post, 'wp:post_date' );
        $post_date_gmt  = $this->get_tag( $post, 'wp:post_date_gmt' );
        $comment_status = $this->get_tag( $post, 'wp:comment_status' );
        $ping_status    = $this->get_tag( $post, 'wp:ping_status' );
        $status         = $this->get_tag( $post, 'wp:status' );
        $post_name      = $this->get_tag( $post, 'wp:post_name' );
        $post_parent    = $this->get_tag( $post, 'wp:post_parent' );
        $menu_order     = $this->get_tag( $post, 'wp:menu_order' );
        $post_type      = $this->get_tag( $post, 'wp:post_type' );
        $post_password  = $this->get_tag( $post, 'wp:post_password' );
        $is_sticky      = $this->get_tag( $post, 'wp:is_sticky' );
        $guid           = $this->get_tag( $post, 'guid' );
        $post_author    = $this->get_tag( $post, 'dc:creator' );

        $post_excerpt = $this->get_tag( $post, 'excerpt:encoded' );
        $post_excerpt = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $post_excerpt );
        $post_excerpt = str_replace( '<br>', '<br />', $post_excerpt );
        $post_excerpt = str_replace( '<hr>', '<hr />', $post_excerpt );

        $post_content = $this->get_tag( $post, 'content:encoded' );
        $post_content = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $post_content );
        $post_content = str_replace( '<br>', '<br />', $post_content );
        $post_content = str_replace( '<hr>', '<hr />', $post_content );

        $postdata = compact( 'post_id', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_excerpt',
            'post_title', 'status', 'post_name', 'comment_status', 'ping_status', 'guid', 'post_parent',
            'menu_order', 'post_type', 'post_password', 'is_sticky'
        );

        $attachment_url = $this->get_tag( $post, 'wp:attachment_url' );
        if ( $attachment_url )
            $postdata['attachment_url'] = $attachment_url;

        preg_match_all( '|<category domain="([^"]+?)" nicename="([^"]+?)">(.+?)</category>|is', $post, $terms, PREG_SET_ORDER );
        foreach ( $terms as $t ) {
            $post_terms[] = array(
                'slug' => $t[2],
                'domain' => $t[1],
                'name' => str_replace( array( '<![CDATA[', ']]>' ), '', $t[3] ),
            );
        }
        if ( ! empty( $post_terms ) ) $postdata['terms'] = $post_terms;

        preg_match_all( '|<wp:comment>(.+?)</wp:comment>|is', $post, $comments );
        $comments = $comments[1];
        if ( $comments ) {
            foreach ( $comments as $comment ) {
                preg_match_all( '|<wp:commentmeta>(.+?)</wp:commentmeta>|is', $comment, $commentmeta );
                $commentmeta = $commentmeta[1];
                $c_meta = array();
                foreach ( $commentmeta as $m ) {
                    $c_meta[] = array(
                        'key' => $this->get_tag( $m, 'wp:meta_key' ),
                        'value' => $this->get_tag( $m, 'wp:meta_value' ),
                    );
                }

                $post_comments[] = array(
                    'comment_id' => $this->get_tag( $comment, 'wp:comment_id' ),
                    'comment_author' => $this->get_tag( $comment, 'wp:comment_author' ),
                    'comment_author_email' => $this->get_tag( $comment, 'wp:comment_author_email' ),
                    'comment_author_IP' => $this->get_tag( $comment, 'wp:comment_author_IP' ),
                    'comment_author_url' => $this->get_tag( $comment, 'wp:comment_author_url' ),
                    'comment_date' => $this->get_tag( $comment, 'wp:comment_date' ),
                    'comment_date_gmt' => $this->get_tag( $comment, 'wp:comment_date_gmt' ),
                    'comment_content' => $this->get_tag( $comment, 'wp:comment_content' ),
                    'comment_approved' => $this->get_tag( $comment, 'wp:comment_approved' ),
                    'comment_type' => $this->get_tag( $comment, 'wp:comment_type' ),
                    'comment_parent' => $this->get_tag( $comment, 'wp:comment_parent' ),
                    'comment_user_id' => $this->get_tag( $comment, 'wp:comment_user_id' ),
                    'commentmeta' => $c_meta,
                );
            }
        }
        if ( ! empty( $post_comments ) ) $postdata['comments'] = $post_comments;

        preg_match_all( '|<wp:postmeta>(.+?)</wp:postmeta>|is', $post, $postmeta );
        $postmeta = $postmeta[1];
        if ( $postmeta ) {
            foreach ( $postmeta as $p ) {
                $post_postmeta[] = array(
                    'key' => $this->get_tag( $p, 'wp:meta_key' ),
                    'value' => $this->get_tag( $p, 'wp:meta_value' ),
                );
            }
        }
        if ( ! empty( $post_postmeta ) ) $postdata['postmeta'] = $post_postmeta;

        return $postdata;
    }

    function _normalize_tag( $matches ) {
        return '<' . strtolower( $matches[1] );
    }

    function fopen( $filename, $mode = 'r' ) {
        if ( $this->has_gzip )
            return gzopen( $filename, $mode );
        return fopen( $filename, $mode );
    }

    function feof( $fp ) {
        if ( $this->has_gzip )
            return gzeof( $fp );
        return feof( $fp );
    }

    function fgets( $fp, $len = 8192 ) {
        if ( $this->has_gzip )
            return gzgets( $fp, $len );
        return fgets( $fp, $len );
    }

    function fclose( $fp ) {
        if ( $this->has_gzip )
            return gzclose( $fp );
        return fclose( $fp );
    }
}