<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Staging_Log_List_Free extends WP_List_Table
{
    public $page_num;
    public $log_list;

    public function __construct( $args = array() )
    {
        parent::__construct(
            array(
                'plural' => 'log',
                'screen' => 'log'
            )
        );
    }

    public function print_column_headers( $with_id = true )
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        foreach ( $columns as $column_key => $column_display_name ) {
            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) ) {
                $class[] = 'hidden';
            }

            if ( $column_key === $primary )
            {
                $class[] = 'column-primary';
            }

            $tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
            $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
            $id    = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) ) {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }

    public function get_columns()
    {
        $columns = array();
        $columns['wpvivid_date'] = 'Date';
        $columns['wpvivid_log_type'] = __( 'Log Type', 'wpvivid' );
        $columns['wpvivid_log_file_name'] =__( 'Log File Name	', 'wpvivid'  );
        $columns['wpvivid_log_action'] = __( 'Action	', 'wpvivid'  );
        $columns['wpvivid_download'] = __( 'Download', 'wpvivid'  );

        return $columns;
    }

    public function set_log_list($log_list,$page_num=1)
    {
        $this->log_list=$log_list;
        $this->page_num=$page_num;
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

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $total_items =sizeof($this->log_list);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => 10,
            )
        );
    }

    public function has_items()
    {
        return !empty($this->log_list);
    }

    public function _column_wpvivid_date( $log )
    {
        $offset=get_option('gmt_offset');
        $localtime = strtotime($log['time']) + $offset * 60 * 60;
        echo '<td><label for="tablecell">'.date('F-d-Y H:i:s',$localtime).'</label></td>';
    }

    protected function column_wpvivid_log_type($log)
    {
        if($log['error'])
        {
            echo '<span>Error</span>';
        }
        else
        {
            echo '<span>'.$log['des'].'</span>';
        }
    }

    public function column_wpvivid_log_file_name( $log )
    {
        echo '<span>'.$log['file_name'].'</span>';
    }

    public function column_wpvivid_log_action( $log )
    {
        $html='<a class="open-log" log="'.$log['file_name'].'" style="cursor:pointer;">
                  <img src="'.esc_url(WPVIVID_PLUGIN_IMAGES_URL.'Log.png').'" style="vertical-align:middle;">Log
               </a>';
        echo $html;
    }

    public function column_wpvivid_download( $log )
    {
        $html='<a class="download-log" log="'.$log['file_name'].'" style="cursor:pointer;">
                    <img src="' . esc_url(WPVIVID_PLUGIN_IMAGES_URL . 'staging/download.png') . '" style="vertical-align:middle;" />Download
               </a>';
        echo $html;
    }

    public function display_rows()
    {
        $this->_display_rows( $this->log_list );
    }

    private function _display_rows($log_list)
    {
        $page=$this->get_pagenum();

        $page_log_list=array();
        $count=0;
        while ( $count<$page )
        {
            $page_log_list = array_splice( $log_list, 0, 10);
            $count++;
        }
        foreach ( $page_log_list as $log)
        {
            $this->single_row($log);
        }
    }

    public function single_row($log)
    {
        ?>
        <tr>
            <?php $this->single_row_columns( $log ); ?>
        </tr>
        <?php
    }

    protected function pagination( $which )
    {
        if ( empty( $this->_pagination_args ) )
        {
            return;
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        if ( isset( $this->_pagination_args['infinite_scroll'] ) )
        {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }

        if ( 'top' === $which && $total_pages > 1 )
        {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }

        $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

        $current              = $this->get_pagenum();

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
                "%s<input class='current-page' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
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

    protected function display_tablenav( $which ) {
        $css_type = '';
        if ( 'top' === $which ) {
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
            $css_type = 'margin: 0 0 10px 0';
        }
        else if( 'bottom' === $which ) {
            $css_type = 'margin: 10px 0 0 0';
        }
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>

            <br class="clear" />
        </div>
        <?php
    }
}

class WPvivid_Staging_Log_Page_Free
{
    public $main_tab;
    public function __construct()
    {
        add_action('wp_ajax_wpvividstg_get_log_list_page', array($this, 'get_log_list_page'));
        add_action('wp_ajax_wpvividstg_view_log_ex', array($this, 'view_log_ex'));
        add_action('wp_ajax_wpvividstg_download_log', array($this, 'download_log'));

        //add_filter('wpvivid_get_staging_admin_menus', array($this, 'get_menu'), 10);
        add_filter('wpvivid_add_log_tab_page', array($this, 'add_log_tab_page'), 11);
        add_filter('wpvivid_get_staging_log_list', array($this, 'get_staging_log_list'), 10);
    }

    public function add_log_tab_page($setting_array)
    {
        $setting_array['staging_log_page'] = array('index' => '2', 'tab_func' =>  array($this, 'add_tab_log'), 'page_func' => array($this, 'add_page_log'));
        return $setting_array;
    }

    public function add_tab_log(){
        ?>
        <a href="#" id="wpvivid_tab_staging_log" class="nav-tab log-nav-tab" onclick="switchlogTabs(event,'staging-logs-page')"><?php _e('Staging Logs', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function add_page_log()
    {
        global $wpvivid_plugin;
        $display_log_count=array(0=>"10",1=>"20",2=>"30",3=>"40",4=>"50");
        $max_log_diaplay=20;
        $loglist=$this->get_log_list('staging');
        ?>
        <div id="staging-logs-page" class="log-tab-content wpvivid_tab_log" name="tab-logs" style="display: none">
            <table class="wp-list-table widefat plugins">
                <thead class="log-head">
                <tr>
                    <th class="row-title"><?php _e( 'Date', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Log Type', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Log File Name', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Action', 'wpvivid-backuprestore' ); ?></th>
                </tr>
                </thead>
                <tbody class="wpvivid-loglist" id="wpvivid_staging_loglist">
                <?php
                $ret['html']='';
                $ret = apply_filters('wpvivid_get_staging_log_list', $ret);
                echo $ret['html'];
                ?>
                </tbody>
            </table>
            <div style="padding-top: 10px; text-align: center;">
                <input class="button-secondary log-page" id="wpvivid_staging_pre_log_page" type="submit" value="<?php esc_attr_e( ' < Pre page ', 'wpvivid-backuprestore' ); ?>" />
                <div style="font-size: 12px; display: inline-block; padding-left: 10px;">
                                <span id="wpvivid_staging_log_page_info" style="line-height: 35px;">
                                    <?php
                                    $current_page=1;
                                    $max_page=ceil(sizeof($loglist['log_list']['file'])/$max_log_diaplay);
                                    if($max_page == 0) $max_page = 1;
                                    echo $current_page.' / '.$max_page;
                                    ?>
                                </span>
                </div>
                <input class="button-secondary log-page" id="wpvivid_staging_next_log_page" type="submit" value="<?php esc_attr_e( ' Next page > ', 'wpvivid-backuprestore' ); ?>" />
                <div style="float: right;">
                    <select name="" id="wpvivid_staging_display_log_count">
                        <?php
                        foreach ($display_log_count as $value){
                            if($value == $max_log_diaplay){
                                echo '<option selected="selected" value="' . $value . '">' . $value . '</option>';
                            }
                            else {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <script>
            var wpvivid_cur_staging_log_page = 1;
            var wpvivid_staging_log_count = '<?php
                _e(sizeof($loglist['log_list']['file']), 'wpvivid-backuprestore');
                ?>';
            jQuery('#wpvivid_staging_display_log_count').on("change", function(){
                wpvivid_staging_display_log_page();
            });

            jQuery('#wpvivid_staging_pre_log_page').click(function(){
                wpvivid_staging_pre_log_page();
            });

            jQuery('#wpvivid_staging_next_log_page').click(function(){
                wpvivid_staging_next_log_page();
            });

            function wpvivid_staging_pre_log_page(){
                if(wpvivid_cur_staging_log_page > 1){
                    wpvivid_cur_staging_log_page--;
                }
                wpvivid_staging_display_log_page();
            }

            function wpvivid_staging_next_log_page(){
                var display_count = jQuery("#wpvivid_staging_display_log_count option:selected").val();
                var max_pages=Math.ceil(wpvivid_staging_log_count/display_count);
                if(wpvivid_cur_staging_log_page < max_pages){
                    wpvivid_cur_staging_log_page++;
                }
                wpvivid_staging_display_log_page();
            }

            function wpvivid_staging_display_log_page(){
                var display_count = jQuery("#wpvivid_staging_display_log_count option:selected").val();
                var max_pages=Math.ceil(wpvivid_staging_log_count/display_count);
                if(max_pages == 0) max_pages = 1;
                jQuery('#wpvivid_staging_log_page_info').html(wpvivid_cur_staging_log_page+ " / "+max_pages);

                var begin = (wpvivid_cur_staging_log_page - 1) * display_count;
                var end = parseInt(begin) + parseInt(display_count);
                jQuery("#wpvivid_staging_loglist tr").hide();
                jQuery('#wpvivid_staging_loglist tr').each(function(i){
                    if (i >= begin && i < end)
                    {
                        jQuery(this).show();
                    }
                });
            }
        </script>
        <?php
    }

    public function get_staging_log_list($ret)
    {
        $html=$ret['html'];
        $loglist=$this->get_log_list('staging');
        $current_num=1;
        $max_log_diaplay=20;
        $log_index=0;
        $pic_log='/admin/partials/images/Log.png';
        if(!empty($loglist['log_list']['file']))
        {
            foreach ($loglist['log_list']['file'] as $value)
            {
                if ($current_num <= $max_log_diaplay) {
                    $log_tr_display = '';
                } else {
                    $log_tr_display = 'display: none;';
                }
                if (empty($value['time'])) {
                    $value['time'] = 'N/A';
                }
                else{
                    $offset=get_option('gmt_offset');
                    $localtime = strtotime($value['time']) + $offset * 60 * 60;
                    $value['time'] = date('F-d-Y H:i:s',$localtime);
                }
                if (empty($value['des'])) {
                    $value['des'] = 'N/A';
                }
                $value['path'] = str_replace('\\', '/', $value['path']);
                $html .= '<tr style="'.esc_attr($log_tr_display, 'wpvivid-backuprestore').'">
                <td class="row-title"><label for="tablecell">'.__($value['time'], 'wpvivid-backuprestore').'</label>
                </td>
                <td>'.__($value['des'], 'wpvivid-backuprestore').'</td>
                <td>'.__($value['file_name'], 'wpvivid-backuprestore').'</td>
                <td>
                    <a onclick="wpvivid_read_log(\''.'wpvivid_view_log'.'\', \''.$value['path'].'\')" style="cursor:pointer;">
                    <img src="'.esc_url(WPVIVID_PLUGIN_URL.$pic_log).'" style="vertical-align:middle;">Log
                    </a>
                </td>
                </tr>';
                $log_index++;
                $current_num++;
            }
        }
        $ret['log_count']=$log_index;
        $ret['html']=$html;
        return $ret;
    }

    public function output_log()
    {
        ?>
        <div class="postbox restore_log" id="wpvivid_read_log_content">

        </div>
        <?php
    }

    public function output_staging_log_list()
    {
        $this->output_log_list('staging','staging_log','wpvivid_staging_log_list');
    }

    public function output_log_list($type,$slug,$id)
    {
        ?>
        <div class="wpvivid-log-list" id="<?php echo $id?>">
            <?php
            $loglist=$this->get_log_list($type);
            $table = new WPvivid_Staging_Log_List_Free();
            $table->set_log_list($loglist['log_list']['file']);
            $table->prepare_items();
            $table->display();
            ?>
        </div>
        <script>
            jQuery('#<?php echo $id?>').on("click",'.first-page',function()
            {
                wpvivid_log_change_page('first','<?php echo $type;?>','<?php echo $id;?>');
            });

            jQuery('#<?php echo $id?>').on("click",'.prev-page',function()
            {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_log_change_page(page-1,'<?php echo $type;?>','<?php echo $id?>');
            });

            jQuery('#<?php echo $id?>').on("click",'.next-page',function()
            {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_log_change_page(page+1,'<?php echo $type;?>','<?php echo $id?>');
            });

            jQuery('#<?php echo $id?>').on("click",'.last-page',function()
            {
                wpvivid_log_change_page('last','<?php echo $type;?>','<?php echo $id?>');
            });

            jQuery('#<?php echo $id?>').on("keypress", '.current-page', function()
            {
                if(event.keyCode === 13)
                {
                    var page = jQuery(this).val();
                    wpvivid_log_change_page(page,'<?php echo $type;?>','<?php echo $id?>');
                }
            });

            jQuery('#<?php echo $id?>').on("click",'.open-log',function()
            {
                var log=jQuery(this).attr("log");
                wpvivid_open_log(log,'<?php echo $slug;?>');
            });

            jQuery('#<?php echo $id?>').on("click",'.download-log',function()
            {
                var log=jQuery(this).attr("log");
                wpvivid_download_log(log);
            });

        </script>
        <?php
    }

    public function get_log_list($type='backup')
    {
        $ret['log_list']['file']=array();
        $log=new WPvivid_Staging_Log_Free();
        $dir=$log->GetSaveLogFolder();
        $files=array();
        $error_files=array();
        $handler=opendir($dir);
        $regex='#^wpvivid.*_log.txt#';
        if($handler!==false)
        {
            while(($filename=readdir($handler))!==false)
            {
                if($filename != "." && $filename != "..")
                {
                    if(is_dir($dir.$filename))
                    {
                        continue;
                    }else{
                        if(preg_match($regex,$filename))
                        {
                            $files[$filename] = $dir.$filename;
                        }
                    }
                }
            }
            if($handler)
                @closedir($handler);
        }

        $dir.='error'.DIRECTORY_SEPARATOR;
        if(file_exists($dir))
        {
            $handler=opendir($dir);
            if($handler!==false)
            {
                while(($filename=readdir($handler))!==false)
                {
                    if($filename != "." && $filename != "..")
                    {
                        if(is_dir($dir.$filename))
                        {
                            continue;
                        }else{
                            if(preg_match($regex,$filename))
                            {
                                $error_files[$filename] = $dir.$filename;
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }


        foreach ($files as $file)
        {
            $handle = @fopen($file, "r");
            if ($handle)
            {
                $log_file=array();
                $log_file['file_name']=basename($file);
                $log_file['path']=$file;
                $log_file['des']='';
                $log_file['time']='';
                $log_file['error']=false;
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Log created: ');
                    if($pos!==false)
                    {
                        $log_file['time']=substr ($line,$pos+strlen('Log created: '));
                    }
                }
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Type: ');
                    if($pos!==false)
                    {
                        $log_file['des']=substr ($line,$pos+strlen('Type: '));
                    }
                    else
                    {
                        $log_file['des']='other';
                    }
                }
                fclose($handle);
                if(preg_match('#'.$type.'#',$log_file['des']))
                {
                    $ret['log_list']['file'][basename($file)]=$log_file;
                }
                else if($type=='other')
                {
                    if(preg_match('#scan#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#export#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#Add Remote Test Connection	#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#upload#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#import#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#transfer#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#other#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                }
            }
        }

        foreach ($error_files as $file)
        {
            $handle = @fopen($file, "r");
            if ($handle)
            {
                $log_file=array();
                $log_file['file_name']=basename($file);
                $log_file['path']=$file;
                $log_file['des']='';
                $log_file['time']='';
                $log_file['error']=true;
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Log created: ');
                    if($pos!==false)
                    {
                        $log_file['time']=substr ($line,$pos+strlen('Log created: '));
                    }
                }
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Type: ');
                    if($pos!==false)
                    {
                        $log_file['des']=substr ($line,$pos+strlen('Type: '));
                    }
                    else
                    {
                        $log_file['des']='other';
                    }
                }
                fclose($handle);
                if(preg_match('#'.$type.'#',$log_file['des']))
                {
                    $ret['log_list']['file'][basename($file)]=$log_file;
                }
                else if($type=='other')
                {
                    if(preg_match('#scan#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#export#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#Add Remote Test Connection	#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#upload#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#import#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#transfer#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                    else if(preg_match('#other#',$log_file['des']))
                    {
                        $ret['log_list']['file'][basename($file)]=$log_file;
                    }
                }
            }
        }

        $ret['log_list']['file'] =$this->sort_list($ret['log_list']['file']);

        return $ret;
    }

    public function sort_list($list)
    {
        uasort ($list,function($a, $b)
        {
            if($a['error']>$b['error'])
            {
                return -1;
            }
            else if($a['error']<$b['error'])
            {
                return 1;
            }

            if($a['time']>$b['time'])
            {
                return -1;
            }
            else if($a['time']===$b['time'])
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

    public function get_log_list_page()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $page = $_POST['page'];
            $type=$_POST['type'];
            $loglist = $this->get_log_list($type);
            $table = new WPvivid_Staging_Log_List_Free();
            $table->set_log_list($loglist['log_list']['file'], $page);
            $table->prepare_items();
            ob_start();
            $table->display();
            $rows = ob_get_clean();

            $ret['result'] = 'success';
            $ret['rows'] = $rows;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function view_log_ex()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            if (isset($_POST['log']) && !empty($_POST['log']) && is_string($_POST['log']))
            {
                $log = sanitize_text_field($_POST['log']);
                $loglist=$this->get_log_list_ex();

                if(isset($loglist['log_list']['file'][$log]))
                {
                    $log=$loglist['log_list']['file'][$log];
                }
                else
                {
                    $json['result'] = 'failed';
                    $json['error'] = __('The log not found.', 'wpvivid');
                    echo json_encode($json);
                    die();
                }

                $path=$log['path'];

                if (!file_exists($path))
                {
                    $json['result'] = 'failed';
                    $json['error'] = __('The log not found.', 'wpvivid');
                    echo json_encode($json);
                    die();
                }

                $file = fopen($path, 'r');

                if (!$file) {
                    $json['result'] = 'failed';
                    $json['error'] = __('Unable to open the log file.', 'wpvivid');
                    echo json_encode($json);
                    die();
                }

                $buffer = '';
                while (!feof($file)) {
                    $buffer .= fread($file, 1024);
                }
                fclose($file);

                $json['result'] = 'success';
                $json['data'] = $buffer;
                echo json_encode($json);
            } else {
                $json['result'] = 'failed';
                $json['error'] = __('Reading the log failed. Please try again.', 'wpvivid');
                echo json_encode($json);
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_log_list_ex()
    {
        $ret['log_list']['file']=array();
        $log=new WPvivid_Staging_Log_Free();
        $dir=$log->GetSaveLogFolder();
        $files=array();
        $error_files=array();
        $handler=opendir($dir);
        $regex='#^wpvivid.*_log.txt#';
        if($handler!==false)
        {
            while(($filename=readdir($handler))!==false)
            {
                if($filename != "." && $filename != "..")
                {
                    if(is_dir($dir.$filename))
                    {
                        continue;
                    }else{
                        if(preg_match($regex,$filename))
                        {
                            $files[$filename] = $dir.$filename;
                        }
                    }
                }
            }
            if($handler)
                @closedir($handler);
        }

        $dir.='error'.DIRECTORY_SEPARATOR;
        if(file_exists($dir))
        {
            $handler=opendir($dir);
            if($handler!==false)
            {
                while(($filename=readdir($handler))!==false)
                {
                    if($filename != "." && $filename != "..")
                    {
                        if(is_dir($dir.$filename))
                        {
                            continue;
                        }else{
                            if(preg_match($regex,$filename))
                            {
                                $error_files[$filename] = $dir.$filename;
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }


        foreach ($files as $file)
        {
            $handle = @fopen($file, "r");
            if ($handle)
            {
                $log_file=array();
                $log_file['file_name']=basename($file);
                $log_file['path']=$file;
                $log_file['des']='';
                $log_file['time']='';
                $log_file['error']=false;
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Log created: ');
                    if($pos!==false)
                    {
                        $log_file['time']=substr ($line,$pos+strlen('Log created: '));
                    }
                }
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Type: ');
                    if($pos!==false)
                    {
                        $log_file['des']=substr ($line,$pos+strlen('Type: '));
                    }
                    else
                    {
                        $log_file['des']='other';
                    }
                }
                $ret['log_list']['file'][basename($file)]=$log_file;
                fclose($handle);
            }
        }

        foreach ($error_files as $file)
        {
            $handle = @fopen($file, "r");
            if ($handle)
            {
                $log_file=array();
                $log_file['file_name']=basename($file);
                $log_file['path']=$file;
                $log_file['des']='';
                $log_file['time']='';
                $log_file['error']=true;
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Log created: ');
                    if($pos!==false)
                    {
                        $log_file['time']=substr ($line,$pos+strlen('Log created: '));
                    }
                }
                $line = fgets($handle);
                if($line!==false)
                {
                    $pos=strpos($line,'Type: ');
                    if($pos!==false)
                    {
                        $log_file['des']=substr ($line,$pos+strlen('Type: '));
                    }
                    else
                    {
                        $log_file['des']='other';
                    }
                }
                $ret['log_list']['file'][basename($file)]=$log_file;
                fclose($handle);
            }
        }

        $ret['log_list']['file'] =$this->sort_list($ret['log_list']['file']);

        return $ret;
    }

    public function download_log()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $admin_url=apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-log';
        try
        {
            if (isset($_REQUEST['log']))
            {
                $log = sanitize_text_field($_REQUEST['log']);
                $loglist=$this->get_log_list_ex();

                if(isset($loglist['log_list']['file'][$log]))
                {
                    $log=$loglist['log_list']['file'][$log];
                }
                else
                {
                    $message= __('The log not found.', 'wpvivid');
                    echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
                    die();
                }

                $path=$log['path'];

                if (!file_exists($path))
                {
                    $message= __('The log not found.', 'wpvivid');
                    echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
                    die();
                }

                if (file_exists($path))
                {
                    if (session_id())
                        session_write_close();

                    $size = filesize($path);
                    if (!headers_sent())
                    {
                        header('Content-Description: File Transfer');
                        header('Content-Type: text');
                        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
                        header('Cache-Control: must-revalidate');
                        header('Content-Length: ' . $size);
                        header('Content-Transfer-Encoding: binary');
                    }

                    if ($size < 1024 * 1024 * 60) {
                        ob_end_clean();
                        readfile($path);
                        exit;
                    } else {
                        ob_end_clean();
                        $download_rate = 1024 * 10;
                        $file = fopen($path, "r");
                        while (!feof($file)) {
                            @set_time_limit(20);
                            // send the current file part to the browser
                            print fread($file, round($download_rate * 1024));
                            // flush the content to the browser
                            flush();
                            if (ob_get_level())
                            {
                                ob_end_clean();
                            }
                            // sleep one second
                            sleep(1);
                        }
                        fclose($file);
                        exit;
                    }
                }
                else
                {
                    echo __(' file not found. please <a href="'.$admin_url.'">retry</a> again.');
                    die();
                }

            } else {
                $message = __('Reading the log failed. Please try again.', 'wpvivid');
                echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
                die();
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
            die();
        }
    }
}