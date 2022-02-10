<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

if ( ! class_exists( 'WP_List_Table' ) )
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPvivid_Exclude_Files_List extends WP_List_Table
{
    public $list;
    public $type;
    public $page_num;
    public $parent;

    public function __construct( $args = array() )
    {
        global $wpdb;
        parent::__construct(
            array(
                'plural' => 'upload_files',
                'screen' => 'upload_files',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list,$page_num=1)
    {
        $this->list=$list;
        $this->page_num=$page_num;
    }

    protected function get_table_classes()
    {
        return array( 'widefat striped' );
    }

    public function print_column_headers( $with_id = true )
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        if (!empty($columns['cb']))
        {
            static $cb_counter = 1;
            $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __('Select All') . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox"/>';
            $cb_counter++;
        }

        foreach ( $columns as $column_key => $column_display_name )
        {

            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) )
            {
                $class[] = 'hidden';
            }


            if ( $column_key === $primary )
            {
                $class[] = 'column-primary';
            }

            if ( $column_key === 'cb' )
            {
                $class[] = 'check-column';
            }
            $tag='th';
            $tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
            $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
            $id    = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) )
            {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }

    public function get_columns()
    {
        $sites_columns = array(
            'cb'          => __( ' ' ),
            'file_regex'    => __( 'File Regex' )
        );

        return $sites_columns;
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

    public function column_cb( $item )
    {
        $html='<input type="checkbox" name="regex_list" />';
        echo $html;
    }

    public function column_file_regex( $item )
    {
        echo $item;
    }

    public function has_items()
    {
        return !empty($this->list);
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $total_items =sizeof($this->list);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => 10,
            )
        );
    }

    public function display_rows()
    {
        $this->_display_rows( $this->list );
    }

    private function _display_rows( $list )
    {
        $page=$this->get_pagenum();

        $page_list=$list;
        $temp_page_list=array();

        $count=0;
        while ( $count<$page )
        {
            $temp_page_list = array_splice( $page_list, 0, 10);
            $count++;
        }

        foreach ( $temp_page_list as $key=>$item)
        {
            $this->single_row($item);
        }
    }

    public function single_row($item)
    {
        ?>
        <tr file_regex="<?php echo $item?>">
            <?php $this->single_row_columns( $item ); ?>
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
                "%s<input class='current-page'  type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label  class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
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

        $total_pages     = $this->_pagination_args['total_pages'];
        if ( $total_pages >1)
        {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
                <div class="alignleft actions bulkactions">
                    <label for="wpvivid_uc_exclude_regex_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_exclude_regex_bulk_action">
                        <option value="remove_exclude_regex">Remove</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                ?>
                <br class="clear" />
            </div>
            <?php
        }
        else
        {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
                <div class="alignleft actions bulkactions">
                    <label for="wpvivid_uc_exclude_regex_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_exclude_regex_bulk_action">
                        <option value="remove_exclude_regex">Remove</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <br class="clear" />
            </div>
            <?php
        }
    }

    public function display()
    {
        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" >
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                }
                ?>
            >
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
                <?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>

        </table>
        <?php
    }
}

class WPvivid_Post_Type_List extends WP_List_Table
{
    public $list;
    public $type;
    public $page_num;
    public $parent;

    public function __construct( $args = array() )
    {
        global $wpdb;
        parent::__construct(
            array(
                'plural' => 'upload_files',
                'screen' => 'upload_files',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list,$page_num=1)
    {
        $this->list=$list;
        $this->page_num=$page_num;
    }

    protected function get_table_classes()
    {
        return array( 'widefat striped' );
    }

    public function print_column_headers( $with_id = true )
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        if (!empty($columns['cb']))
        {
            static $cb_counter = 1;
            $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __('Select All') . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox"/>';
            $cb_counter++;
        }

        foreach ( $columns as $column_key => $column_display_name )
        {

            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) )
            {
                $class[] = 'hidden';
            }


            if ( $column_key === $primary )
            {
                $class[] = 'column-primary';
            }

            if ( $column_key === 'cb' )
            {
                $class[] = 'check-column';
            }
            $tag='th';
            $tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
            $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
            $id    = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) )
            {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }

    public function get_columns()
    {
        $sites_columns = array(
            'cb'          => __( ' ' ),
            'post_type'    => __( 'Post Type' )
        );

        return $sites_columns;
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

    public function column_cb( $item )
    {
        $html='<input type="checkbox" name="post_type" />';
        echo $html;
    }

    public function column_post_type( $item )
    {
        echo $item;
    }

    public function has_items()
    {
        return !empty($this->list);
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $total_items =sizeof($this->list);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => 10,
            )
        );
    }

    public function display_rows()
    {
        $this->_display_rows( $this->list );
    }

    private function _display_rows( $list )
    {
        $page=$this->get_pagenum();

        $page_list=$list;
        $temp_page_list=array();

        $count=0;
        while ( $count<$page )
        {
            $temp_page_list = array_splice( $page_list, 0, 10);
            $count++;
        }

        foreach ( $temp_page_list as $key=>$item)
        {
            $this->single_row($item);
        }
    }

    public function single_row($item)
    {
        ?>
        <tr post_type="<?php echo $item?>">
            <?php $this->single_row_columns( $item ); ?>
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
                "%s<input class='current-page'  type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label  class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
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

        $total_pages     = $this->_pagination_args['total_pages'];
        if ( $total_pages >1)
        {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
                <div class="alignleft actions bulkactions">
                    <label for="wpvivid_uc_post_type_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_post_type_bulk_action">
                        <option value="remove_post_type">Remove</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                ?>
                <br class="clear" />
            </div>
            <?php
        }
        else
        {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
                <div class="alignleft actions bulkactions">
                    <label for="wpvivid_uc_post_type_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_post_type_bulk_action">
                        <option value="remove_post_type">Remove</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <br class="clear" />
            </div>
            <?php
        }
    }

    public function display()
    {
        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" >
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                }
                ?>
            >
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
                <?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>

        </table>
        <?php
    }
}

class WPvivid_Uploads_Cleaner_Setting
{
    public function __construct()
    {
        add_filter('wpvivid_add_setting_tab_page', array($this, 'add_setting_tab_page'), 10);
        add_action('wpvivid_setting_add_uc_cell',array($this, 'add_uc_cell'),13);
        add_filter('wpvivid_set_general_setting', array($this, 'set_general_setting'), 11, 3);

        add_filter('wpvivid_pro_setting_tab', array($this, 'setting_tab'), 13);

        add_action('wp_ajax_wpvivid_get_exclude_files_list',array($this, 'get_exclude_files_list'));
        add_action('wp_ajax_wpvivid_delete_exclude_files',array($this, 'delete_exclude_files'));

        add_action('wp_ajax_wpvivid_get_post_type_list',array($this, 'get_post_type_list'));
        add_action('wp_ajax_wpvivid_delete_post_type',array($this, 'delete_post_type'));
    }

    public function setting_tab($tabs)
    {
        if(current_user_can('administrator'))
        {
            $tab['title']='Media Cleaner Settings';
            $tab['slug']='upload_cleaner';
            $tab['callback']= array($this, 'output_setting');
            $args['is_parent_tab']=0;
            $args['transparency']=1;
            $tab['args']=$args;
            $tabs[]=$tab;
        }
        return $tabs;
    }

    public function set_general_setting($setting_data, $setting, $options)
    {
        if(isset($setting['wpvivid_uc_scan_limit']))
            $setting_data['wpvivid_uc_scan_limit'] = intval($setting['wpvivid_uc_scan_limit']);

        if(isset($setting['wpvivid_uc_files_limit']))
            $setting_data['wpvivid_uc_files_limit'] = intval($setting['wpvivid_uc_files_limit']);

        if(isset($setting['wpvivid_uc_scan_file_types'])&&is_array($setting['wpvivid_uc_scan_file_types']))
            $setting_data['wpvivid_uc_scan_file_types'] = $setting['wpvivid_uc_scan_file_types'];

        if(isset($setting['wpvivid_uc_post_types'])&&is_array($setting['wpvivid_uc_post_types']))
            $setting_data['wpvivid_uc_post_types'] = $setting['wpvivid_uc_post_types'];

        if(isset($setting['wpvivid_uc_quick_scan']))
            $setting_data['wpvivid_uc_quick_scan'] = boolval($setting['wpvivid_uc_quick_scan']);

        if(isset($setting['wpvivid_uc_delete_media_when_delete_file']))
            $setting_data['wpvivid_uc_delete_media_when_delete_file'] = boolval($setting['wpvivid_uc_delete_media_when_delete_file']);

        if(isset($setting['wpvivid_uc_exclude_files_regex'])&&is_array($setting['wpvivid_uc_exclude_files_regex']))
            $setting_data['wpvivid_uc_exclude_files_regex'] = $setting['wpvivid_uc_exclude_files_regex'];

        return $setting_data;
    }

    public function add_setting_tab_page($setting_array)
    {
        $setting_array['uc_setting'] = array('index' => '3', 'tab_func' =>  array($this, 'wpvivid_settingpage_add_tab_uc'), 'page_func' => array($this, 'wpvivid_settingpage_add_page_uc'));
        return $setting_array;
    }

    public function wpvivid_settingpage_add_tab_uc()
    {
        ?>
        <a href="#" id="wpvivid_tab_uc_setting" class="nav-tab setting-nav-tab" onclick="switchsettingTabs(event,'page-uc-setting')"><?php _e('Media Cleaner Settings', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_settingpage_add_page_uc()
    {
        ?>
        <div class="setting-tab-content wpvivid_tab_uc_setting" id="page-uc-setting" style="margin-top: 10px; display: none;">
            <?php do_action('wpvivid_setting_add_uc_cell'); ?>
        </div>
        <?php
    }

    public function output_setting()
    {
        ?>
        <div style="margin-top: 10px;">
            <?php
            $this->add_uc_cell();
            ?>
            <div><input class="button-primary wpvivid_setting_general_save" type="submit" value="<?php esc_attr_e( 'Save Changes', 'wpvivid' ); ?>" /></div>
        </div>
        <?php
    }

    public function add_uc_cell()
    {
        $scan_limit=get_option('wpvivid_uc_scan_limit',20);
        $files_limit=get_option('wpvivid_uc_files_limit',100);

        $default_file_types=array();
        $default_file_types[]='png';
        $default_file_types[]='jpg';
        $default_file_types[]='jpeg';
        $scan_file_types=get_option('wpvivid_uc_scan_file_types',$default_file_types);

        $quick_scan=get_option('wpvivid_uc_quick_scan',false);

        if($quick_scan)
        {
            $quick_scan='checked';
        }
        else
        {
            $quick_scan='';
        }

        //$default_post_types=array();
        //$default_post_types[]='attachment';
        //$default_post_types[]='revision';
        //$default_post_types[]='auto-draft';
        //$default_post_types[]='nav_menu_item';
        //$default_post_types[]='shop_order';
        //$default_post_types[]='shop_order_refund';
        //$default_post_types[]='oembed_cache';
        //$post_types=get_option('wpvivid_uc_post_types',$default_post_types);

        $delete_media_when_delete_file=get_option('wpvivid_uc_delete_media_when_delete_file',false);

        if($delete_media_when_delete_file)
        {
            $delete_media_when_delete_file='checked';
        }
        else
        {
            $delete_media_when_delete_file='';
        }

        //$white_list=get_option('wpvivid_uc_exclude_files_regex',array());
        ?>
        <div class="postbox schedule-tab-block setting-page-content">
            <div class="wpvivid-element-space-bottom">
                <label for="wpvivid_uc_scan_file_types">
                    <input style="margin: 4px;" id="wpvivid_uc_quick_scan" type="checkbox" option="setting" name="wpvivid_uc_quick_scan" <?php esc_attr_e($quick_scan); ?> />
                    <span><strong><?php _e('Enable Quick Scan', 'wpvivid-backuprestore'); ?></strong></span>
                </label>
            </div>
            <div class="wpvivid-element-space-bottom">
                <span><?php _e('Checking this option will speed up your scans but may produce lower accuracy.', 'wpvivid-backuprestore'); ?></span>
            </div>
            <div class="wpvivid-element-space-bottom">
                <label for="wpvivid_uc_delete_media_when_delete_file">
                    <input style="margin: 4px;" id="wpvivid_uc_delete_media_when_delete_file" style="margin-right: 4px;" type="checkbox" option="setting" name="wpvivid_uc_delete_media_when_delete_file" <?php esc_attr_e($delete_media_when_delete_file); ?> />
                    <span><strong><?php _e('Delete Image URL', 'wpvivid-backuprestore'); ?></strong></span>
                </label>
            </div>
            <div class="wpvivid-element-space-bottom">
                <span><?php _e('With this option checked, when the image is deleted, the corresponding image url in the database that is not used anywhere on your website will also be deleted.', 'wpvivid-backuprestore'); ?></span>
            </div>
        </div>

        <div class="postbox schedule-tab-block setting-page-content">
            <div class="wpvivid-element-space-bottom"><strong><?php _e('Posts Quantity Processed Per Request', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input style="margin: 0px;" type="text" placeholder="20" option="setting" name="wpvivid_uc_scan_limit" id="wpvivid_uc_scan_limit" class="all-options" value="<?php esc_attr_e($scan_limit, 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Set how many posts to process per request. The value should be set depending on your server performance and the recommended value is 20.', 'wpvivid-backuprestore' ); ?>
            </div>
            <div class="wpvivid-element-space-bottom"><strong><?php _e('Media Files Quantity Processed Per Request', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="wpvivid-element-space-bottom">
                <input style="margin: 0px;" type="text" placeholder="100" option="setting" name="wpvivid_uc_files_limit" id="wpvivid_uc_files_limit" class="all-options" value="<?php esc_attr_e($files_limit, 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />
            </div>
            <div class="wpvivid-element-space-bottom">
                <?php _e( 'Set how many media files to process per request. The value should be set depending on your server performance and the recommended value is 100.', 'wpvivid-backuprestore' ); ?>
            </div>
        </div>
        <?php
        /*
        <div class="postbox schedule-tab-block wpvivid-setting-addon" style="margin-bottom: 10px; padding-bottom: 0;">
            <div style="margin-bottom: 20px;"><strong><?php _e('Files Filter ', 'wpvivid'); ?></strong></div>
            <div>
                <div class="wpvivid-element-space-bottom" style="float: left;">
                    <input type="text" option="setting" id="wpvivid_uc_exclude_files_regex" class="regular-text" />
                </div>
                <div class="wpvivid-element-space-bottom" style="float: left;">
                    <input class="button-secondary" id="wpvivid_uc_add_exclude_files_regex" type="submit" value="<?php esc_attr_e( 'Add File Regex', 'wpvivid' ); ?>"/>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div class="wpvivid-element-space-bottom">
                <div id="wpvivid_uc_add_exclude_files_regex_list">
                    <?php
                    $list = new WPvivid_Exclude_Files_List();

                    $list->set_list($white_list);
                    $list->prepare_items();
                    $list ->display();
                    ?>
                </div>
            </div>
            <script>
                jQuery('#wpvivid_uc_add_exclude_files_regex').click(function()
                {
                    var file_exclude=jQuery('#wpvivid_uc_exclude_files_regex').val();
                    wpvivid_get_exclude_files_list('first',file_exclude);
                });

                jQuery('#wpvivid_uc_add_exclude_files_regex_list').on("click",'.first-page',function()
                {
                    wpvivid_get_exclude_files_list('first');
                });

                jQuery('#wpvivid_uc_add_exclude_files_regex_list').on("click",'.prev-page',function()
                {
                    var page=parseInt(jQuery(this).attr('value'));
                    wpvivid_get_exclude_files_list(page-1);
                });

                jQuery('#wpvivid_uc_add_exclude_files_regex_list').on("click",'.next-page',function()
                {
                    var page=parseInt(jQuery(this).attr('value'));
                    wpvivid_get_exclude_files_list(page+1);
                });

                jQuery('#wpvivid_uc_add_exclude_files_regex_list').on("click",'.last-page',function()
                {
                    wpvivid_get_exclude_files_list('last');
                });

                jQuery('#wpvivid_uc_add_exclude_files_regex_list').on("keypress", '.current-page', function()
                {
                    if(event.keyCode === 13)
                    {
                        var page = jQuery(this).val();
                        wpvivid_get_exclude_files_list(page);
                    }
                });

                jQuery('#wpvivid_uc_add_exclude_files_regex_list').on("click",'.action',function()
                {
                    var selected=jQuery('#wpvivid_uc_exclude_regex_bulk_action').val();

                    if(selected=='remove_exclude_regex')
                    {
                        wpvivid_delete_exclude_files();
                    }
                });

                function wpvivid_get_exclude_files_list(page,file_exclude='')
                {
                    var ajax_data = {
                        'action': 'wpvivid_get_exclude_files_list',
                        'page':page,
                        'file_exclude':file_exclude
                    };

                    wpvivid_post_request(ajax_data, function (data)
                    {
                        jQuery('#wpvivid_uc_add_exclude_files_regex_list').html('');
                        try
                        {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success')
                            {
                                jQuery('#wpvivid_uc_add_exclude_files_regex_list').html(jsonarray.html);
                            }
                            else
                            {
                                alert(jsonarray.error);
                            }
                        }
                        catch (err)
                        {
                            alert(err);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                        alert(error_message);
                    });
                }

                function wpvivid_delete_exclude_files()
                {
                    var json = {};
                    json['selected']=Array();
                    jQuery('input[name=regex_list][type=checkbox]').each(function(index, value)
                    {
                        if(jQuery(value).prop('checked'))
                        {
                            jQuery(value).closest('tr');
                            var path = jQuery(this).closest('tr').attr('file_regex');
                            json['selected'].push(path)
                        }
                    });
                    var selected= JSON.stringify(json);
                    jQuery('#wpvivid_uc_add_exclude_files_regex_list').find('.action').prop('disabled', true);

                    var ajax_data = {
                        'action': 'wpvivid_delete_exclude_files',
                        'selected':selected
                    };
                    wpvivid_post_request(ajax_data, function (data)
                    {
                        jQuery('#wpvivid_uc_add_exclude_files_regex_list').find('.action').prop('disabled', false);
                        jQuery('#wpvivid_uc_add_exclude_files_regex_list').html('');
                        try
                        {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success')
                            {
                                alert('success');
                                jQuery('#wpvivid_uc_add_exclude_files_regex_list').html(jsonarray.html);
                            }
                            else
                            {
                                alert(jsonarray.error);
                            }
                        }
                        catch (err)
                        {
                            alert(err);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        jQuery('#wpvivid_uc_add_exclude_files_regex_list').find('.action').prop('disabled', false);

                        var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                        alert(error_message);
                    });
                }
            </script>
        </div>
        <div class="postbox schedule-tab-block wpvivid-setting-addon" style="margin-bottom: 10px; padding-bottom: 0;">
            <div style="margin-bottom: 20px;"><strong><?php _e('Post type', 'wpvivid'); ?></strong></div>
            <div>
                <div class="wpvivid-element-space-bottom" style="float: left;">
                    <input type="text" option="setting" id="wpvivid_uc_post_type" class="regular-text" />
                </div>
                <div class="wpvivid-element-space-bottom" style="float: left;">
                    <input class="button-secondary" id="wpvivid_uc_add_post_type" type="submit" value="<?php esc_attr_e( 'Add Post type', 'wpvivid' ); ?>"/>
                </div>
                <div style="clear: both;"></div>
            </div>
            <div class="wpvivid-element-space-bottom">
                <div id="wpvivid_uc_post_type_list">
                    <?php
                    $list = new WPvivid_Post_Type_List();

                    $list->set_list($post_types);
                    $list->prepare_items();
                    $list ->display();
                    ?>
                </div>
            </div>
            <script>
                jQuery('#wpvivid_uc_add_post_type').click(function()
                {
                    var post_type=jQuery('#wpvivid_uc_post_type').val();
                    wpvivid_get_post_type_list('first',post_type);
                });

                jQuery('#wpvivid_uc_post_type_list').on("click",'.first-page',function()
                {
                    wpvivid_get_post_type_list('first');
                });

                jQuery('#wpvivid_uc_post_type_list').on("click",'.prev-page',function()
                {
                    var page=parseInt(jQuery(this).attr('value'));
                    wpvivid_get_post_type_list(page-1);
                });

                jQuery('#wpvivid_uc_post_type_list').on("click",'.next-page',function()
                {
                    var page=parseInt(jQuery(this).attr('value'));
                    wpvivid_get_post_type_list(page+1);
                });

                jQuery('#wpvivid_uc_post_type_list').on("click",'.last-page',function()
                {
                    wpvivid_get_post_type_list('last');
                });

                jQuery('#wpvivid_uc_post_type_list').on("keypress", '.current-page', function()
                {
                    if(event.keyCode === 13)
                    {
                        var page = jQuery(this).val();
                        wpvivid_get_post_type_list(page);
                    }
                });

                jQuery('#wpvivid_uc_post_type_list').on("click",'.action',function()
                {
                    var selected=jQuery('#wpvivid_uc_post_type_bulk_action').val();

                    if(selected=='remove_post_type')
                    {
                        wpvivid_delete_post_type();
                    }
                });

                function wpvivid_get_post_type_list(page,post_type='')
                {
                    var ajax_data = {
                        'action': 'wpvivid_get_post_type_list',
                        'page':page,
                        'post_type':post_type
                    };

                    wpvivid_post_request(ajax_data, function (data)
                    {
                        jQuery('#wpvivid_uc_post_type_list').html('');
                        try
                        {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success')
                            {
                                jQuery('#wpvivid_uc_post_type_list').html(jsonarray.html);
                            }
                            else
                            {
                                alert(jsonarray.error);
                            }
                        }
                        catch (err)
                        {
                            alert(err);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                        alert(error_message);
                    });
                }

                function wpvivid_delete_post_type()
                {
                    var json = {};
                    json['selected']=Array();
                    jQuery('input[name=post_type][type=checkbox]').each(function(index, value)
                    {
                        if(jQuery(value).prop('checked'))
                        {
                            jQuery(value).closest('tr');
                            var path = jQuery(this).closest('tr').attr('post_type');
                            json['selected'].push(path)
                        }
                    });
                    var selected= JSON.stringify(json);
                    jQuery('#wpvivid_uc_post_type_list').find('.action').prop('disabled', true);

                    var ajax_data = {
                        'action': 'wpvivid_delete_post_type',
                        'selected':selected
                    };
                    wpvivid_post_request(ajax_data, function (data)
                    {
                        jQuery('#wpvivid_uc_post_type_list').find('.action').prop('disabled', false);
                        jQuery('#wpvivid_uc_post_type_list').html('');
                        try
                        {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success')
                            {
                                alert('success');
                                jQuery('#wpvivid_uc_post_type_list').html(jsonarray.html);
                            }
                            else
                            {
                                alert(jsonarray.error);
                            }
                        }
                        catch (err)
                        {
                            alert(err);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        jQuery('#wpvivid_uc_post_type_list').find('.action').prop('disabled', false);

                        var error_message = wpvivid_output_ajaxerror('achieving backup', textStatus, errorThrown);
                        alert(error_message);
                    });
                }
            </script>
        </div>
        */
    }

    public function get_exclude_files_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            if(isset($_POST['file_exclude'])&&!empty($_POST['file_exclude']))
            {
                $file_exclude=$_POST['file_exclude'];
                $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
                $white_list[]=$file_exclude;
                update_option('wpvivid_uc_exclude_files_regex',$white_list);
            }

            $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
            $list=new WPvivid_Exclude_Files_List();

            if(isset($_POST['page']))
            {
                $list->set_list($white_list,$_POST['page']);
            }
            else
            {
                $list->set_list($white_list);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function delete_exclude_files()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
            $white_list = array_diff($white_list, $files);

            update_option('wpvivid_uc_exclude_files_regex',$white_list);

            $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
            $list=new WPvivid_Exclude_Files_List();

            if(isset($_POST['page']))
            {
                $list->set_list($white_list,$_POST['page']);
            }
            else
            {
                $list->set_list($white_list);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_post_type_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $default_post_types=array();
            $default_post_types[]='attachment';
            $default_post_types[]='revision';
            $default_post_types[]='auto-draft';
            $default_post_types[]='nav_menu_item';
            $default_post_types[]='shop_order';
            $default_post_types[]='shop_order_refund';
            $default_post_types[]='oembed_cache';

            if(isset($_POST['post_type'])&&!empty($_POST['post_type']))
            {
                $file_exclude=$_POST['post_type'];

                $post_types=get_option('wpvivid_uc_post_types',$default_post_types);
                $post_types[]=$file_exclude;
                update_option('wpvivid_uc_post_types',$post_types);
            }

            $post_types=get_option('wpvivid_uc_post_types',array());
            $list=new WPvivid_Post_Type_List();

            if(isset($_POST['page']))
            {
                $list->set_list($post_types,$_POST['page']);
            }
            else
            {
                $list->set_list($post_types);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function delete_post_type()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $default_post_types=array();
            $default_post_types[]='attachment';
            $default_post_types[]='revision';
            $default_post_types[]='auto-draft';
            $default_post_types[]='nav_menu_item';
            $default_post_types[]='shop_order';
            $default_post_types[]='shop_order_refund';
            $default_post_types[]='oembed_cache';

            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $post_types=get_option('wpvivid_uc_post_types',$default_post_types);
            $post_types = array_diff($post_types, $files);

            update_option('wpvivid_uc_post_types',$post_types);

            $post_types=get_option('wpvivid_uc_post_types',$default_post_types);
            $list=new WPvivid_Post_Type_List();

            if(isset($_POST['page']))
            {
                $list->set_list($post_types,$_POST['page']);
            }
            else
            {
                $list->set_list($post_types);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }
}