<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

define('WPVIVID_UPLOADS_ISO_DIR','WPvivid_Uploads'.DIRECTORY_SEPARATOR.'Isolate');

if ( ! class_exists( 'WP_List_Table' ) )
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPvivid_Unused_Upload_Files_List extends WP_List_Table
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
            'thumb'    =>__( 'Thumbnail' ),
            'path'    => __( 'Path' ),
            //'folder' => __( 'Folder' ),
            'size'=>__( 'Size' )
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
        $html='<input type="checkbox" name="uploads" value="'.$item['id'].'" />';
        echo $html;
    }

    public function column_thumb($item)
    {
        $supported_image = array(
            'gif',
            'jpg',
            'jpeg',
            'png'
        );

        $upload_dir=wp_upload_dir();

        $path=$upload_dir['basedir'].DIRECTORY_SEPARATOR.$item['path'];

        $ext = strtolower(pathinfo($item['path'], PATHINFO_EXTENSION));
        if (in_array($ext, $supported_image)&&file_exists( $path ))
        {
            echo "<a target='_blank' href='" . $upload_dir['baseurl'].'/'.$item['path'] .
                "'><img style='max-width: 48px; max-height: 48px;' src='" .
                $upload_dir['baseurl'].'/'.$item['path'] . "' />";
        }
        else {
            echo '<span class="dashicons dashicons-no-alt"></span>';
        }

    }

    public function column_path( $item )
    {
        echo '...\uploads\\'.$item['path'];
    }

    public function column_folder( $item )
    {
        if($item['folder']=='.')
        {
            echo 'Uploads root';
        }
        else
        {
            echo $item['folder'];
        }
    }

    public function column_size( $item )
    {
        $upload_dir=wp_upload_dir();
        $file_name=$upload_dir['basedir'].DIRECTORY_SEPARATOR.$item['path'];

        if(file_exists($file_name))
        {
            echo size_format(filesize($file_name),2);
        }
        else
        {
            echo 'file not found';
        }

    }

    public function has_items()
    {
        return !empty($this->list);
    }

    /*
    public function no_items()
    {
        _e( '<a class="wpvivid-no-item" style="cursor:pointer">No items found. Click here to reset</a>' );
    }*/

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
                'per_page'    => 20,
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
            $temp_page_list = array_splice( $page_list, 0, 20);
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
        <tr>
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
                    <label for="wpvivid_uc_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_bulk_action">
                        <option value="-1">Bulk Actions</option>
                        <option value="wpvivid_isolate_selected_image">Isolate selected images</option>
                        <option value="wpvivid_isolate_list_image">Isolate all images</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <div id="wpvivid_isolate_progress" style="margin-top: 4px; display: none;">
                    <div class="spinner is-active" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left; margin-top: 2px;">Isolating images...</div>
                    <div style="clear: both;"></div>
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
                    <label for="wpvivid_uc_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_bulk_action">
                        <option value="-1">Bulk Actions</option>
                        <option value="wpvivid_isolate_selected_image">Isolate selected images</option>
                        <option value="wpvivid_isolate_list_image">Isolate all images</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <div id="wpvivid_isolate_progress" style="margin-top: 4px; display: none;">
                    <div class="spinner is-active" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left; margin-top: 2px;">Isolating images...</div>
                    <div style="clear: both;"></div>
                </div>
                <br class="clear" />
            </div>
            <?php
        }
    }

    public function display() {
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

class WPvivid_Isolate_Files_List extends WP_List_Table
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
            'thumb'    =>__( 'Thumbnail' ),
            'path'    => __( 'Path' ),
            //'folder' => __( 'Folder' ),
            'size'=>__( 'Size' )
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
        $html='<input type="checkbox" name="uploads" />';
        echo $html;
    }

    public function column_thumb($item)
    {
        $supported_image = array(
            'gif',
            'jpg',
            'jpeg',
            'png'
        );



        $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR.DIRECTORY_SEPARATOR.$item['path'];

        $ext = strtolower(pathinfo($item['path'], PATHINFO_EXTENSION));
        if (in_array($ext, $supported_image)&&file_exists( $path ))
        {
            echo "<a target='_blank' href='" . WP_CONTENT_URL.'/'.WPVIVID_UPLOADS_ISO_DIR.'/'.$item['path'] .
                "'><img style='max-width: 48px; max-height: 48px;' src='" .
                WP_CONTENT_URL.'/'.WPVIVID_UPLOADS_ISO_DIR.'/'.$item['path'] . "' />";
        }
        else {
            echo '<span class="dashicons dashicons-no-alt"></span>';
        }

    }

    public function column_path( $item )
    {
        echo '...\uploads\\'.$item['path'];
    }

    public function column_folder( $item )
    {
        if($item['folder']=='.')
        {
            echo 'Uploads root';
        }
        else
        {
            echo $item['folder'];
        }
    }

    public function column_size( $item )
    {
        $file_name=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR.DIRECTORY_SEPARATOR.$item['path'];

        if(file_exists($file_name))
        {
            echo size_format(filesize($file_name),2);
        }
        else
        {
            echo 'file not found';
        }

    }

    public function has_items()
    {
        return !empty($this->list);
    }

    /*
    public function no_items()
    {
        _e( '<a class="wpvivid-no-item" style="cursor:pointer">No items found. Click here to reset</a>' );
    }
    */

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
                'per_page'    => 20,
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
            $temp_page_list = array_splice( $page_list, 0, 20);
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
        <tr path="<?php echo $item['path']?>">
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

        $admin_url = apply_filters('wpvivid_get_admin_url', '');

        if ( $total_pages >1)
        {
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>" style="<?php esc_attr_e($css_type); ?>">
                <div class="alignleft actions bulkactions">
                    <label for="wpvivid_uc_iso_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_iso_bulk_action">
                        <option value="-1">Bulk Actions</option>
                        <option value="wpvivid_restore_selected_image">Restore selected images</option>
                        <option value="wpvivid_restore_list_image">Restore all images</option>
                        <option value="wpvivid_delete_selected_image">Delete selected images</option>
                        <option value="wpvivid_delete_list_image">Delete all images</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <div id="wpvivid_restore_delete_progress" style="margin-top: 4px; display: none;">
                    <div class="spinner is-active" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div id="wpvivid_restore_delete_text" style="float: left; margin-top: 2px;">Restoring images...</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="wpvivid-backup-tips" style="background: #fff; border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;margin-bottom: 10px">
                    <div style="float: left;">
                        <div style="padding: 10px;">
                            <strong><?php _e('Note: ', 'wpvivid'); ?></strong>
                            <?php _e('Once deleted, images will be lost permanently. The action cannot be undone, unless you have <a href="'. $admin_url . 'admin.php?page=WPvivid'.'">a backup</a> in place.', 'wpvivid'); ?>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
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
                    <label for="wpvivid_uc_iso_bulk_action" class="screen-reader-text">Select bulk action</label>
                    <select name="action" id="wpvivid_uc_iso_bulk_action">
                        <option value="-1">Bulk Actions</option>
                        <option value="wpvivid_restore_selected_image">Restore selected images</option>
                        <option value="wpvivid_restore_list_image">Restore all images</option>
                        <option value="wpvivid_delete_selected_image">Delete selected images</option>
                        <option value="wpvivid_delete_list_image">Delete all images</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                <div id="wpvivid_restore_delete_progress" style="margin-top: 4px; display: none;">
                    <div class="spinner is-active" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div id="wpvivid_restore_delete_text" style="float: left; margin-top: 2px;">Restoring images...</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="wpvivid-backup-tips" style="background: #fff; border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;margin-bottom: 10px">
                    <div style="float: left;">
                        <div style="padding: 10px;">
                            <strong><?php _e('Note: ', 'wpvivid'); ?></strong>
                            <?php _e('Once deleted, images will be lost permanently. The action cannot be undone, unless you have <a href="'. $admin_url . 'admin.php?page=WPvivid'.'">a backup</a> in place.', 'wpvivid'); ?>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <br class="clear" />
            </div>
            <?php
        }
    }

    public function display() {
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

class WPvivid_Uploads_Cleaner
{
    public $main_tab;


    //public $screen_ids;
    //public $version;
    //public $plugin_name;

    public function __construct()
    {
        //$this->version = WPVIVID_UPLOADS_CLEANER_VERSION;
        //$this->plugin_name = WPVIVID_UPLOADS_CLEANER_SLUG;
        //$this->screen_ids=array();
        //$this->screen_ids[]='toplevel_page_'. $this->plugin_name;
        //add_action('admin_enqueue_scripts',array( $this,'enqueue_styles'));
        //add_action('admin_enqueue_scripts',array( $this,'enqueue_scripts'));
        //add_action('admin_menu',array( $this,'add_plugin_admin_menu'));
        //$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . 'wpvivid-uploads-cleaner.php' );
        //add_filter('plugin_action_links_' . $plugin_basename, array( $this,'add_action_links'));

        add_filter('wpvivid_scan_post_types',array($this,'scan_post_types'),10);

        add_action('wp_ajax_wpvivid_start_scan_uploads_files_task', array($this, 'start_scan_uploads_files_task'));
        add_action('wp_ajax_wpvivid_scan_uploads_files_from_post',array($this, 'scan_uploads_files_from_post'));

        add_action('wp_ajax_wpvivid_start_unused_files_task',array($this, 'start_unused_files_task'));
        add_action('wp_ajax_wpvivid_unused_files_task',array($this, 'unused_files_task'));

        add_action('wp_ajax_wpvivid_get_result_list',array($this, 'get_result_list'));

        add_action('wp_ajax_wpvivid_isolate_selected_image',array($this, 'isolate_selected_image'));
        add_action('wp_ajax_wpvivid_start_isolate_all_image',array($this, 'start_isolate_all_image'));
        add_action('wp_ajax_wpvivid_isolate_all_image',array($this, 'isolate_all_image'));
        //
        add_action('wp_ajax_wpvivid_get_iso_list',array($this, 'get_iso_list'));

        add_action('wp_ajax_wpvivid_delete_selected_image',array($this, 'delete_selected_image'));
        add_action('wp_ajax_wpvivid_start_delete_all_image',array($this, 'delete_all_image'));
        add_action('wp_ajax_wpvivid_delete_all_image',array($this, 'delete_all_image'));

        add_action('wp_ajax_wpvivid_restore_selected_image',array($this, 'restore_selected_image'));
        add_action('wp_ajax_wpvivid_start_restore_all_image',array($this, 'restore_all_image'));
        add_action('wp_ajax_wpvivid_restore_all_image',array($this, 'restore_all_image'));

        add_action('wp_ajax_wpvivid_uc_add_exclude_files',array($this, 'add_exclude_files'));
        //
        add_filter('wpvivid_uc_scan_include_files_regex',array($this,'scan_include_files_regex'),10);
        add_filter('wpvivid_uc_scan_exclude_files_regex',array($this,'scan_exclude_files_regex'),10);


        include_once WPVIVID_PLUGIN_DIR . '/includes/upload-cleaner/class-wpvivid-uploads-scanner.php';
        include_once WPVIVID_PLUGIN_DIR . '/includes/upload-cleaner/class-wpvivid-isolate-files.php';
        include_once WPVIVID_PLUGIN_DIR. '/includes/upload-cleaner/class-wpvivid-upload-cleaner-setting.php';

        $scan=new WPvivid_Uploads_Scanner();
        $scan->check_table_exist();
        $scan->check_unused_uploads_files_table_exist();
        $iso=new WPvivid_Isolate_Files();
        $iso->check_folder();

        $setting=new WPvivid_Uploads_Cleaner_Setting();


        add_filter('wpvivid_get_toolbar_menus',array($this,'get_toolbar_menus'),22);
        add_filter('wpvivid_get_admin_menus',array($this,'get_admin_menus'),22);
        add_filter('wpvivid_get_screen_ids',array($this,'get_screen_ids'),12);
    }

    public function get_screen_ids($screen_ids)
    {
        $screen_ids[]=apply_filters('wpvivid_white_label_screen_id', 'wpvivid-backup_page_wpvivid-cleaner');
        return $screen_ids;
    }

    public function get_toolbar_menus($toolbar_menus)
    {
        $admin_url = apply_filters('wpvivid_get_admin_url', '');

        $menu['id']='wpvivid_admin_menu_cleaner';
        $menu['parent']='wpvivid_admin_menu';
        $menu['title']=__('Image Cleaner', 'wpvivid-backuprestore');
        $menu['tab']= 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner');
        $menu['href']=$admin_url . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner');
        $menu['capability']='administrator';
        $menu['index']=4;
        $toolbar_menus[$menu['parent']]['child'][$menu['id']]=$menu;
        return $toolbar_menus;
    }

    public function get_admin_menus($submenus)
    {
        $submenu['parent_slug']=apply_filters('wpvivid_white_label_slug', WPVIVID_PLUGIN_SLUG);
        $submenu['page_title']= apply_filters('wpvivid_white_label_display', 'WPvivid Backup');
        $submenu['menu_title']=__('Image Cleaner', 'wpvivid-backuprestore');
        $submenu['capability']='administrator';
        $submenu['menu_slug']=strtolower(sprintf('%s-cleaner', apply_filters('wpvivid_white_label_slug', 'wpvivid')));
        $submenu['index']=4;
        $submenu['function']=array($this, 'display');
        $submenus[$submenu['menu_slug']]=$submenu;
        return $submenus;
    }


    private function transfer_path($path)
    {
        $path = str_replace('\\','/',$path);
        $values = explode('/',$path);
        return implode(DIRECTORY_SEPARATOR,$values);
    }

    public function display()
    {
        $upload_dir=wp_upload_dir();

        $path=$this->transfer_path($upload_dir['basedir']);

        $path1=$this->transfer_path(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'uploads');

        if($path!==$path1)
        {
            echo '<div class="notice notice-error inline"><p>The current version does not support custom uploads directory</p></div>';
            return;
        }

        ?>
        <div class="wrap" style="max-width:1720px;">
            <h1>
                <?php
                echo __('WPvivid Image Cleaner', 'wpvivid');
                ?>
            </h1>
            <?php

            if(!class_exists('WPvivid_Tab_Page_Container'))
                include_once WPVIVID_PLUGIN_DIR . '/includes/class-wpvivid-tab-page-container.php';

            $args['is_parent_tab']=1;
            $this->main_tab=new WPvivid_Tab_Page_Container();
            $this->main_tab->add_tab('Scan Media','scan',array($this, 'output_scan'), $args);
            $this->main_tab->add_tab('Isolated Media','isolate',array($this, 'output_isolate'), $args);
            //$this->main_tab->add_tab('Database','database',array($this, 'output_database'), $args);
            $this->main_tab->display();
            if (isset($_GET['tab']))
            {
                $tab=esc_html($_GET['tab']);
                ?>
                <script>
                    jQuery(document).ready(function($)
                    {
                        jQuery( document ).trigger( '<?php echo $this->main_tab->container_id; ?>-show','<?php echo $tab; ?>');
                    });
                </script>
                <?php
            }
            ?>
        </div>
        <?php
    }

    public function output_scan()
    {

        $scanner=new WPvivid_Uploads_Scanner();

        $count=$scanner->get_scan_result_count();
        $size=$scanner->get_scan_result_size();
        if($count===false)
        {
            $text='';
        }
        else
        {
            $text="<p style=\"margin-top: 10px; margin-bottom: 0px;\">Last Scan: Unused media file(s) found: <strong>$count</strong>. ";
            if($size!==false)
            {
                $text.='Total size: '.$size.' .';
            }
            $text.="</p>";
        }

        $upload_dir=wp_upload_dir();

        $path=$this->transfer_path($upload_dir['basedir']);
        $abs=$this->transfer_path(ABSPATH);

        $path=str_replace($abs,'...'.DIRECTORY_SEPARATOR,$path);

        $folders=$scanner->get_all_folder();
        $admin_url = apply_filters('wpvivid_get_admin_url', '');
        //Before running a scan, it is recommended to <a style="cursor:pointer;" href="<?php echo $admin_url . 'admin.php?page=WPvivid';">[make a full website backup]</a> to avoid losing images.
        $progress_bar='<div class="action-progress-bar"><div class="action-progress-bar-percent" style="height:24px;width:0%"></div></div>    <div style="clear:both;"></div><div style="margin-left:10px; float: left; width:100%;"><p>Ready to scan</p></div> <div style="clear: both;"></div><div><div class="backup-log-btn"><input class="button-primary" id="wpvivid_uc_cancel" type="submit" value="Cancel" /></div></div><div style="clear: both;"></div>';
        ?>
        <div class="postbox quickbackup-addon">
            <div style="margin-top: 10px;margin-bottom: 10px;">
                In the tab, you can scan your media folder (uploads) to find unused images and isolate specific or all unused images.
            </div>
            <div id="wpvivid_uc_scan">
                <div style="margin-top: 10px;margin-bottom: 10px;">
                    Media path: <a><?php echo $path?></a>
                </div>
                <input class="button-primary" style="width: 200px; height: 50px; font-size: 20px;" id="wpvivid_start_scan" type="submit" value="<?php esc_attr_e('Scan', 'wpvivid'); ?>">
                <div style="clear: both;"></div>
                <div style="margin-top: 10px">
                    <span>
                        Clicking the 'Scan' button to find unused images in your media folder. Currently it only scans JPG and PNG images.
                    </span>
                </div>
                <?php echo $text?>
                <div class="wpvivid-backup-tips" style="background: #fff; border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;">
                    <div style="float: left;">
                        <div style="padding: 10px;">
                            <strong><?php _e('Note: ', 'wpvivid'); ?></strong>
                            <?php _e('Please don\'t refresh the page while running a scan.', 'wpvivid'); ?>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>
            <div id="wpvivid_uc_progress" style="display: none;">
                <?php echo $progress_bar;?>
            </div>
            <br/>
        </div>
        <div class="postbox quickbackup-addon">
            <p>
                <input id="wpvivid_result_list_search" type="search" name="s" value="" placeholder="Search">
                <select id="wpvivid_result_list_folder" style="margin-top: -5px;">
                    <option selected="selected" value="0">All Folders</option>
                    <?php
                    if(!empty($folders))
                    {
                        asort($folders);
                        foreach ($folders as $folder)
                        {
                            echo "<option value='$folder'>$folder</option>";
                        }
                    }
                    ?>
                </select>
                <input id="wpvivid_result_list_search_btn" type="submit" class="button" value="Search">
            </p>
        </div>
        <div class="postbox">

            <div id="wpvivid_scan_result_list" style="margin: 10px;">
                <?php

                $result=$scanner->get_scan_result('','');

                $list = new WPvivid_Unused_Upload_Files_List();

                $list->set_list($result);
                $list->prepare_items();
                $list ->display();
                ?>
            </div>
        </div>
        <script>
            var wpvivid_result_list_search='';
            var wpvivid_result_list_folder='';

            jQuery('#wpvivid_result_list_search_btn').click(function()
            {
                wpvivid_result_list_search=jQuery('#wpvivid_result_list_search').val();
                wpvivid_result_list_folder=jQuery('#wpvivid_result_list_folder').val();
                if(wpvivid_result_list_folder=='0')
                {
                    wpvivid_result_list_folder='';
                }

                if(wpvivid_result_list_folder=='root')
                {
                    wpvivid_result_list_folder='.';
                }

                wpvivid_get_result_list('first');
            });

            function wpvivid_get_result_list(page)
            {
                var ajax_data = {
                    'action': 'wpvivid_get_result_list',
                    'page':page,
                    'search':wpvivid_result_list_search,
                    'folder':wpvivid_result_list_folder
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    //var old_html= jQuery('#wpvivid_scan_result_list').html();
                    //jQuery('#wpvivid_scan_result_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.empty)
                            {
                                jQuery('#wpvivid_result_list_search').val('');
                                wpvivid_result_list_search='';
                                alert('No items found.');
                                //jQuery('#wpvivid_scan_result_list').html(old_html);
                            }
                            else
                            {
                                jQuery('#wpvivid_scan_result_list').html(jsonarray.html);
                            }
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
                    var error_message = wpvivid_output_ajaxerror('get list', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#wpvivid_scan_result_list').on("click",'.wpvivid-no-item',function()
            {
                wpvivid_result_list_search='';
                jQuery('#wpvivid_result_list_search').val('');
                wpvivid_get_result_list('first');
            });

            jQuery('#wpvivid_scan_result_list').on("click",'.first-page',function()
            {
                wpvivid_get_result_list('first');
            });

            jQuery('#wpvivid_scan_result_list').on("click",'.prev-page',function()
            {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_result_list(page-1);
            });

            jQuery('#wpvivid_scan_result_list').on("click",'.next-page',function()
            {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_result_list(page+1);
            });

            jQuery('#wpvivid_scan_result_list').on("click",'.last-page',function()
            {
                wpvivid_get_result_list('last');
            });

            jQuery('#wpvivid_scan_result_list').on("keypress", '.current-page', function()
            {
                if(event.keyCode === 13){
                    var page = jQuery(this).val();
                    wpvivid_get_result_list(page);
                }
            });

            jQuery('#wpvivid_scan_result_list').on("click",'.action',function()
            {
                var selected=jQuery('#wpvivid_uc_bulk_action').val();

                if(selected=='wpvivid_isolate_selected_image')
                {
                    wpvivid_isolate_selected_image();
                }
                else if(selected=='wpvivid_isolate_list_image')
                {
                    wpvivid_start_isolate_all_image();
                }
                else if(selected=='wpvivid_ignore_selected_image')
                {
                    wpvivid_ignore_selected_image();
                }


            });

            function wpvivid_ignore_selected_image()
            {
                var json = {};
                json['selected']=Array();
                jQuery('input[name=uploads][type=checkbox]').each(function(index, value)
                {
                    if(jQuery(value).prop('checked'))
                    {
                        json['selected'].push(jQuery(value).val())
                    }
                });
                var selected= JSON.stringify(json);

                jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', true);
                //jQuery('#wpvivid_isolate_selected_image').prop('disabled', true);
                //jQuery('#wpvivid_isolate_list_image').prop('disabled', true);
                var ajax_data = {
                    'action': 'wpvivid_uc_add_exclude_files',
                    'selected':selected,
                    'search':wpvivid_result_list_search,
                    'folder':wpvivid_result_list_folder
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);

                    jQuery('#wpvivid_scan_result_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_scan_result_list').html(jsonarray.html);
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
                    jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                    var error_message = wpvivid_output_ajaxerror('add options', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_isolate_selected_image()
            {
                var json = {};
                json['selected']=Array();
                jQuery('input[name=uploads][type=checkbox]').each(function(index, value)
                {
                    if(jQuery(value).prop('checked'))
                    {
                        json['selected'].push(jQuery(value).val())
                    }
                });
                var selected= JSON.stringify(json);

                //jQuery('#wpvivid_isolate_selected_image').prop('disabled', true);
                //jQuery('#wpvivid_isolate_list_image').prop('disabled', true);
                jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', true);
                var ajax_data = {
                    'action': 'wpvivid_isolate_selected_image',
                    'selected':selected,
                    'search':wpvivid_result_list_search,
                    'folder':wpvivid_result_list_folder
                };
                jQuery('#wpvivid_isolate_progress').show();
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_isolate_progress').hide();
                    //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                    jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                    jQuery('#wpvivid_scan_result_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_scan_result_list').html(jsonarray.html);
                            jQuery('#wpvivid_iso_files_list').html(jsonarray.iso);
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
                    jQuery('#wpvivid_isolate_progress').hide();
                    jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                    var error_message = wpvivid_output_ajaxerror('add isolate files', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_start_isolate_all_image()
            {
                var ajax_data = {
                    'action': 'wpvivid_start_isolate_all_image',
                    'search':wpvivid_result_list_search,
                    'folder':wpvivid_result_list_folder
                };
                jQuery('#wpvivid_isolate_progress').show();
                //jQuery('#wpvivid_isolate_selected_image').prop('disabled', true);
                //jQuery('#wpvivid_isolate_list_image').prop('disabled', true);
                jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', true);
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_isolate_progress').hide();
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.continue)
                            {
                                wpvivid_isolate_all_image();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner');?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                            //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                            //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                        //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                        //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    jQuery('#wpvivid_isolate_progress').hide();
                    jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);

                    var error_message = wpvivid_output_ajaxerror('add isolate files', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_isolate_all_image()
            {
                var ajax_data = {
                    'action': 'wpvivid_isolate_all_image',
                    'search':wpvivid_result_list_search,
                    'folder':wpvivid_result_list_folder
                };
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.continue)
                            {
                                wpvivid_isolate_all_image();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner');?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                            //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                            //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                        //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                        //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('add isolate files', textStatus, errorThrown);
                    alert(error_message);
                    jQuery('#wpvivid_scan_result_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_isolate_list_image').prop('disabled', false);
                });
            }

            jQuery('#wpvivid_rescan').click(function()
            {
                jQuery( document ).trigger( '<?php echo $this->main_tab->container_id ?>-show','scan');
            });

        </script>
        <script>
            var wpvivid_cancel=false;
            jQuery('#wpvivid_start_scan').click(function()
            {
                wpvivid_start_scan();
                //wpvivid_start_unused_files_task();
            });

            jQuery('#wpvivid_uc_progress').on("click",'#wpvivid_uc_cancel',function()
            {
                wpvivid_cancel_scan();
            });

            function wpvivid_cancel_scan()
            {
                wpvivid_cancel=true;
                jQuery('#wpvivid_uc_cancel').prop('disabled', true);
            }

            function wpvivid_start_scan()
            {
                jQuery('#wpvivid_uc_progress').show();

                jQuery('#wpvivid_uc_progress').html('<?php echo $progress_bar?>');
                jQuery('#wpvivid_uc_scan').hide();
                jQuery('#wpvivid_uc_cancel').prop('disabled', false);

                wpvivid_cancel=false;

                var ajax_data = {
                    'action': 'wpvivid_start_scan_uploads_files_task'
                };
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_uc_progress').html(jsonarray.progress_html);
                            if(jsonarray.continue)
                            {
                                scan_uploads_files(jsonarray.start);
                            }
                            else
                            {
                                wpvivid_start_unused_files_task();
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            jQuery('#wpvivid_uc_progress').hide();
                            jQuery('#wpvivid_uc_scan').show();
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_uc_progress').hide();
                        jQuery('#wpvivid_uc_scan').show();
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('scan files', textStatus, errorThrown);
                    alert(error_message);

                    jQuery('#wpvivid_uc_progress').hide();
                    jQuery('#wpvivid_uc_scan').show();
                });
            }

            function scan_uploads_files(start)
            {
                if(wpvivid_cancel)
                {
                    jQuery('#wpvivid_uc_progress').hide();
                    jQuery('#wpvivid_uc_scan').show();
                    jQuery('#wpvivid_uc_cancel').prop('disabled', false);
                    return;
                }

                var ajax_data = {
                    'action': 'wpvivid_scan_uploads_files_from_post',
                    'start':start
                };
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_uc_progress').html(jsonarray.progress_html);
                            if(jsonarray.continue)
                            {
                                scan_uploads_files(jsonarray.start);
                            }
                            else
                            {
                                wpvivid_start_unused_files_task();
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_uc_progress').hide();
                            jQuery('#wpvivid_uc_scan').show();
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_uc_progress').hide();
                        jQuery('#wpvivid_uc_scan').show();
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('scan files', textStatus, errorThrown);
                    alert(error_message);

                    jQuery('#wpvivid_uc_progress').hide();
                    jQuery('#wpvivid_uc_scan').show();
                });
            }

            function wpvivid_start_unused_files_task()
            {
                if(wpvivid_cancel)
                {
                    jQuery('#wpvivid_uc_progress').hide();
                    jQuery('#wpvivid_uc_scan').show();
                    jQuery('#wpvivid_uc_cancel').prop('disabled', false);
                    return;
                }

                var ajax_data = {
                    'action': 'wpvivid_start_unused_files_task'
                };
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_uc_progress').html(jsonarray.progress_html);
                            if(jsonarray.continue)
                            {
                                wpvivid_unused_files_task();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner');?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_uc_progress').hide();
                            jQuery('#wpvivid_uc_scan').show();
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_uc_progress').hide();
                        jQuery('#wpvivid_uc_scan').show();
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('scan files', textStatus, errorThrown);
                    alert(error_message);

                    jQuery('#wpvivid_uc_progress').hide();
                    jQuery('#wpvivid_uc_scan').show();
                });
            }

            function wpvivid_unused_files_task()
            {
                if(wpvivid_cancel)
                {
                    jQuery('#wpvivid_uc_progress').hide();
                    jQuery('#wpvivid_uc_scan').show();
                    jQuery('#wpvivid_uc_cancel').prop('disabled', false);
                    jQuery('#wpvivid_uc_scan_log').html("");
                    return;
                }

                var ajax_data = {
                    'action': 'wpvivid_unused_files_task'
                };
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_uc_progress').html(jsonarray.progress_html);
                            if(jsonarray.continue)
                            {
                                wpvivid_unused_files_task();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner');?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_uc_progress').hide();
                            jQuery('#wpvivid_uc_scan').show();
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_uc_progress').hide();
                        jQuery('#wpvivid_uc_scan').show();
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('scan files', textStatus, errorThrown);
                    alert(error_message);

                    jQuery('#wpvivid_uc_progress').hide();
                    jQuery('#wpvivid_uc_scan').show();
                });
            }

            jQuery(document).ready(function($)
            {
                jQuery('#wpvivid_uc_scan').show();
                jQuery('#wpvivid_uc_progress').hide();
            });
        </script>
        <?php
    }

    public function output_isolate()
    {
        $iso=new WPvivid_Isolate_Files();

        $path=$this->transfer_path(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR);
        $abs=$this->transfer_path(ABSPATH);

        $path=str_replace($abs,'...'.DIRECTORY_SEPARATOR,$path);
        $result=$iso->get_isolate_folder();
        ?>
        <div class="postbox quickbackup-addon">
            <div style="margin-top: 10px;margin-bottom: 10px;">
                This tab displays the isolated images and their locations. You can choose to restore or delete specific isolated images.
            </div>
            <div style="margin-top: 10px;margin-bottom: 10px;">
                lsolated Folder Path: <a><?php echo $path?></a>
            </div>
        </div>
        <div class="postbox quickbackup-addon">
            <p>
                <input id="wpvivid_iso_list_search" type="search" name="s" value="" placeholder="Search">
                <select id="wpvivid_iso_list_folder" style="margin-top: -5px;">
                    <option selected="selected" value="0">All Folders</option>
                    <?php
                    asort($result['folders']);
                    foreach ($result['folders'] as $folder)
                    {
                        echo "<option value='$folder'>$folder</option>";
                    }
                    ?>
                </select>
                <input id="wpvivid_iso_list_search_btn" type="submit" class="button" value="Search">
            </p>
        </div>
        <div class="postbox">
            <div id="wpvivid_iso_files_list" style="margin: 10px;">
                <?php
                $files=$iso->get_isolate_files();
                $list = new WPvivid_Isolate_Files_List();

                $list->set_list($files);
                $list->prepare_items();
                $list ->display();
                ?>
            </div>
        </div>
        <script>
            var wpvivid_iso_list_search='';
            var wpvivid_iso_list_folder='';

            jQuery('#wpvivid_iso_list_search_btn').click(function()
            {
                wpvivid_iso_list_search=jQuery('#wpvivid_iso_list_search').val();
                wpvivid_iso_list_folder=jQuery('#wpvivid_iso_list_folder').val();
                if(wpvivid_iso_list_folder=='0')
                {
                    wpvivid_iso_list_folder='';
                }

                if(wpvivid_iso_list_folder=='root')
                {
                    wpvivid_iso_list_folder='.';
                }

                wpvivid_get_iso_list('first');
            });

            function wpvivid_get_iso_list(page)
            {
                var ajax_data = {
                    'action': 'wpvivid_get_iso_list',
                    'page':page,
                    'search':wpvivid_iso_list_search,
                    'folder':wpvivid_iso_list_folder
                };

                wpvivid_post_request(ajax_data, function (data)
                {
                    //jQuery('#wpvivid_iso_files_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.empty)
                            {
                                jQuery('#wpvivid_iso_list_search').val('');
                                wpvivid_iso_list_search='';
                                alert('No items found.');
                            }
                            else
                            {
                                jQuery('#wpvivid_iso_files_list').html(jsonarray.html);
                            }

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
                    var error_message = wpvivid_output_ajaxerror('get list', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#wpvivid_iso_files_list').on("click",'.first-page',function()
            {
                wpvivid_get_iso_list('first');
            });

            jQuery('#wpvivid_iso_files_list').on("click",'.wpvivid-no-item',function()
            {
                wpvivid_iso_list_search='';
                jQuery('#wpvivid_iso_files_list').val('');
                wpvivid_get_iso_list('first');
            });

            jQuery('#wpvivid_iso_files_list').on("click",'.prev-page',function()
            {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_iso_list(page-1);
            });

            jQuery('#wpvivid_iso_files_list').on("click",'.next-page',function()
            {
                var page=parseInt(jQuery(this).attr('value'));
                wpvivid_get_iso_list(page+1);
            });

            jQuery('#wpvivid_iso_files_list').on("click",'.last-page',function()
            {
                wpvivid_get_iso_list('last');
            });

            jQuery('#wpvivid_iso_files_list').on("keypress", '.current-page', function()
            {
                if(event.keyCode === 13){
                    var page = jQuery(this).val();
                    wpvivid_get_iso_list(page);
                }
            });

            jQuery('#wpvivid_iso_files_list').on("click",'.action',function()
            {
                var selected=jQuery('#wpvivid_uc_iso_bulk_action').val();

                if(selected=='wpvivid_delete_selected_image')
                {
                    wpvivid_delete_selected_image();
                }
                else if(selected=='wpvivid_delete_list_image')
                {
                    wpvivid_start_delete_all_image();
                }
                else if(selected=='wpvivid_restore_selected_image')
                {
                    wpvivid_restore_selected_image();
                }
                else if(selected=='wpvivid_restore_list_image')
                {
                    wpvivid_start_restore_all_image();
                }

            });

            function wpvivid_delete_selected_image()
            {
                var json = {};
                json['selected']=Array();
                jQuery('input[name=uploads][type=checkbox]').each(function(index, value)
                {
                    if(jQuery(value).prop('checked'))
                    {
                        jQuery(value).closest('tr');
                        var path = jQuery(this).closest('tr').attr('path');
                        json['selected'].push(path)
                    }
                });
                var selected= JSON.stringify(json);
                jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', true);

                var ajax_data = {
                    'action': 'wpvivid_delete_selected_image',
                    'selected':selected,
                    'search':wpvivid_iso_list_search,
                    'folder':wpvivid_iso_list_folder
                };
                jQuery('#wpvivid_restore_delete_progress').show();
                jQuery('#wpvivid_restore_delete_text').html('Deleting images...');
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);

                    jQuery('#wpvivid_iso_files_list').html('');
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_iso_files_list').html(jsonarray.html);
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
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_list_image').prop('disabled', false);

                    var error_message = wpvivid_output_ajaxerror('delete files', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_start_delete_all_image()
            {
                var ajax_data = {
                    'action': 'wpvivid_start_delete_all_image',
                    'search':wpvivid_iso_list_search,
                    'folder':wpvivid_iso_list_folder
                };
                jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', true);
                jQuery('#wpvivid_restore_delete_progress').show();
                jQuery('#wpvivid_restore_delete_text').html('Deleting images...');
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.continue)
                            {
                                wpvivid_delete_all_image();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page=' .apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner'). '&tab=isolate'?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                        //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                        //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                        //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                        //jQuery('#wpvivid_restore_list_image').prop('disabled', false);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_list_image').prop('disabled', false);

                    var error_message = wpvivid_output_ajaxerror('delete files', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_delete_all_image()
            {
                var ajax_data = {
                    'action': 'wpvivid_delete_all_image',
                    'search':wpvivid_iso_list_search,
                    'folder':wpvivid_iso_list_folder
                };
                jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', true);

                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.continue)
                            {
                                wpvivid_delete_all_image();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page=' .apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner'). '&tab=isolate'?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('delete files', textStatus, errorThrown);
                    alert(error_message);
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                });
            }

            function wpvivid_restore_selected_image()
            {
                var json = {};
                json['selected']=Array();
                jQuery('input[name=uploads][type=checkbox]').each(function(index, value)
                {
                    if(jQuery(value).prop('checked'))
                    {
                        jQuery(value).closest('tr');
                        var path = jQuery(this).closest('tr').attr('path');
                        json['selected'].push(path)
                    }
                });
                var selected= JSON.stringify(json);
                jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', true);

                var ajax_data = {
                    'action': 'wpvivid_restore_selected_image',
                    'selected':selected,
                    'search':wpvivid_iso_list_search,
                    'folder':wpvivid_iso_list_folder
                };
                jQuery('#wpvivid_restore_delete_progress').show();
                jQuery('#wpvivid_restore_delete_text').html('Restoring images...');
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);

                    jQuery('#wpvivid_iso_files_list').html('');
                    try
                    {

                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            jQuery('#wpvivid_iso_files_list').html(jsonarray.html);
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
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_list_image').prop('disabled', false);

                    var error_message = wpvivid_output_ajaxerror('restore files', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_start_restore_all_image()
            {
                var ajax_data = {
                    'action': 'wpvivid_start_restore_all_image',
                    'search':wpvivid_iso_list_search,
                    'folder':wpvivid_iso_list_folder
                };
                jQuery('#wpvivid_restore_delete_progress').show();
                jQuery('#wpvivid_restore_delete_text').html('Restoring images...');
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.continue)
                            {
                                wpvivid_restore_all_image();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page=' .apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner'). '&tab=isolate'?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                        //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                        //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                        //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                        //jQuery('#wpvivid_restore_list_image').prop('disabled', false);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    jQuery('#wpvivid_restore_delete_progress').hide();
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_list_image').prop('disabled', false);

                    var error_message = wpvivid_output_ajaxerror('restore files', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_restore_all_image()
            {
                var ajax_data = {
                    'action': 'wpvivid_restore_all_image',
                    'search':wpvivid_iso_list_search,
                    'folder':wpvivid_iso_list_folder
                };
                wpvivid_post_request(ajax_data, function(data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success')
                        {
                            if(jsonarray.continue)
                            {
                                wpvivid_restore_all_image();
                            }
                            else
                            {
                                location.href = '<?php echo admin_url() . 'admin.php?page=' .apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-cleaner'). '&tab=isolate'?>';
                            }
                        }
                        else if (jsonarray.result === 'failed')
                        {
                            alert(jsonarray.error);
                            jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                            //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                            //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                            //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                            //jQuery('#wpvivid_restore_list_image').prop('disabled', false);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                        jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                    }

                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('restore files', textStatus, errorThrown);
                    alert(error_message);
                    jQuery('#wpvivid_iso_files_list').find('.action').prop('disabled', false);
                    //jQuery('#wpvivid_delete_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_delete_list_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_selected_image').prop('disabled', false);
                    //jQuery('#wpvivid_restore_list_image').prop('disabled', false);
                });
            }
        </script>
        <?php
    }

    public function output_database()
    {
        ?>
        <div class="postbox quickbackup-addon">
            <h1>Coming soon</h1>
        </div>
        <?php
    }

    public function start_scan_uploads_files_task()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        set_time_limit(30);

        $uploads_scanner=new WPvivid_Uploads_Scanner();
        $uploads_scanner->init_scan_task();
        $uploads_files=array();

        $uploads_files[0]=$uploads_scanner->scan_sidebars_widgets();

        $files=$uploads_scanner->scan_termmeta_thumbnail();
        $uploads_files[0]=array_merge($uploads_files[0],$files);
        $files=$uploads_scanner->scan_divi_options();
        $uploads_files[0]=array_merge($uploads_files[0],$files);

        $count=$uploads_scanner->get_post_count();

        $start=0;
        $limit=min(get_option('wpvivid_uc_scan_limit',20),$count);

        $posts=$uploads_scanner->get_posts($start,$limit);



        foreach ($posts as $post)
        {
            $media=$uploads_scanner->get_media_from_post_content($post);
            //$uploads_files['post_id']=$post;
            //$uploads_files['uploads_files']=$media;
            //$uploads_files=array_merge($uploads_files,$media);

            if(!empty($media))
            {
                $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_meta($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_meta_elementor($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }
            //$uploads_ids=array_merge($uploads_ids,$media);
            $media=$uploads_scanner->get_media_from_post_custom_meta($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }
        }

        $start+=$limit;

        $result['result']='success';
        if($count == 0){
            $result['percent']=0;
        }
        else{
            $result['percent']=intval(($start/$count)*100);
        }
        $result['total_posts']=$start;
        $result['scanned_posts']=$count;
        $result['descript']='Scanning files from posts';
        $result['progress_html']='
        <div class="action-progress-bar">
            <div class="action-progress-bar-percent" style="height:24px;width:' . $result['percent'] . '%"></div>
        </div>
        <div style="float:left;">
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Total Posts:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['total_posts'] . '</span>
            </div>
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Scanned:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['scanned_posts'] . '</span>
            </div>
        </div>       
        <div style="clear:both;"></div>
        <div style="margin-left:10px; float: left; width:100%;">
            <p>' .  $result['descript'] . '</p>
        </div>
        <div style="clear: both;"></div>
        <div>
             <div class="backup-log-btn">
                <input class="button-primary" id="wpvivid_uc_cancel" type="submit" value="' . esc_attr('Cancel', 'wpvivid-backuprestore') . '" />
             </div>          
        </div>
        <div style="clear: both;"></div>';

        if($start>=$count)
        {
            $uploads_scanner->update_scan_task($uploads_files,$start,'finished',100);
            $result['start']=$start;
            $result['status']='finished';
            $result['continue']=0;
            $result['log']='scan upload files finished'.PHP_EOL;
        }
        else
        {
            $uploads_scanner->update_scan_task($uploads_files,$start,'running');
            $result['start']=$start;
            $result['status']='running';
            $result['continue']=1;
            $result['log']='scanned posts:'.$start.PHP_EOL.'total posts:'.$count.PHP_EOL;
        }


        //$uploads_files=$uploads_scanner->get_files();
        //$result['count']=$count;
        //$result['files']=$uploads_files;

        echo json_encode($result);
        die();
    }

    public function scan_uploads_files_from_post()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if(!isset($_POST['start']))
        {
            die();
        }

        $start=intval($_POST['start']);

        if(!is_int($start))
        {
            die();
        }

        set_time_limit(30);

        $uploads_scanner=new WPvivid_Uploads_Scanner();

        $count=$uploads_scanner->get_post_count();

        $limit=min(get_option('wpvivid_uc_scan_limit',20),$count);

        $posts=$uploads_scanner->get_posts($start,$limit);

        $uploads_files=array();

        foreach ($posts as $post)
        {
            $media=$uploads_scanner->get_media_from_post_content($post);
            //$uploads_files['post_id']=$post;
            //$uploads_files['uploads_files']=$media;
            //$uploads_files=array_merge($uploads_files,$media);

            if(!empty($media))
            {
                $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_meta($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_meta_elementor($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_custom_meta($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }
        }

        $start+=$limit;

        $result['result']='success';
        $result['percent']=intval(($start/$count)*100);
        $result['total_posts']=$start;
        $result['scanned_posts']=$count;
        $result['descript']='Scanning files from posts';
        $result['progress_html']='
        <div class="action-progress-bar">
            <div class="action-progress-bar-percent" style="height:24px;width:' . $result['percent'] . '%"></div>
        </div>
        <div style="float:left;">
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Total Posts:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['total_posts'] . '</span>
            </div>
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Scanned:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['scanned_posts'] . '</span>
            </div>
        </div>       
        <div style="clear:both;"></div>
        <div style="margin-left:10px; float: left; width:100%;">
            <p>' .  $result['descript'] . '</p>
        </div>
        <div style="clear: both;"></div>
        <div>
             <div class="backup-log-btn">
                <input class="button-primary" id="wpvivid_uc_cancel" type="submit" value="' . esc_attr('Cancel', 'wpvivid-backuprestore') . '" />
             </div>          
        </div>
        <div style="clear: both;"></div>';

        if($start>=$count)
        {
            $uploads_scanner->update_scan_task($uploads_files,$start,'finished',100);
            $result['start']=$start;
            $result['status']='finished';
            $result['continue']=0;
            $result['log']='scan upload files finished'.PHP_EOL;
        }
        else
        {
            $uploads_scanner->update_scan_task($uploads_files,$start,'running');
            $result['start']=$start;
            $result['status']='running';
            $result['continue']=1;
            $result['log']='scanned posts:'.$start.PHP_EOL.'total posts:'.$count.PHP_EOL;
        }

        $ret=$uploads_scanner->get_unused_uploads_progress();
        $result['total_folders']=$ret['total_folders'];
        $result['scanned_folders']=$ret['scanned_folders'];
        $result['percent']=$ret['percent'];

        echo json_encode($result);
        die();
    }

    public function start_unused_files_task()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        set_time_limit(30);

        $uploads_scanner=new WPvivid_Uploads_Scanner();

        $result=$uploads_scanner->get_folders();

        $uploads_scanner->init_unused_uploads_task($result['folders']);

        $files=array();
        foreach ($result['files'] as $file)
        {

            if(!$uploads_scanner->is_uploads_files_exist($file))
            {
                $files[]=$file;
            }
        }

        $uploads_scanner->update_unused_uploads_task($files,'.',1,0,'running',0,$result['size']);

        $result['result']='success';
        $result['status']='running';
        $result['continue']=1;
        $result['log']='scanning files'.PHP_EOL;

        $ret=$uploads_scanner->get_unused_uploads_progress();
        $result['total_folders']=$ret['total_folders'];
        $result['scanned_folders']=$ret['scanned_folders'];
        $result['percent']=$ret['percent'];
        $result['descript']='Scanning upload folder.';
        $result['progress_html']='
        <div class="action-progress-bar">
            <div class="action-progress-bar-percent" style="height:24px;width:' . $result['percent'] . '%"></div>
        </div>
        <div style="float:left;">
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Total Folders:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['total_folders'] . '</span>
            </div>
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Scanned:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['scanned_folders'] . '</span>
            </div>
        </div>       
        <div style="clear:both;"></div>
        <div style="margin-left:10px; float: left; width:100%;">
            <p>' .  $result['descript'] . '</p>
        </div>
        <div style="clear: both;"></div>
        <div>
             <div class="backup-log-btn">
                <input class="button-primary" id="wpvivid_uc_cancel" type="submit" value="' . esc_attr('Cancel', 'wpvivid-backuprestore') . '" />
             </div>          
        </div>
        <div style="clear: both;"></div>';
        $result['.']=$files;
        echo json_encode($result);
        die();
    }

    public function unused_files_task()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        set_time_limit(30);

        $uploads_scanner=new WPvivid_Uploads_Scanner();

        $ret=$uploads_scanner->get_unfinished_folder();

        if($ret===false)
        {
            $uploads_scanner->update_unused_uploads_task(array(),'.',1,0,'finished',100);
            $result['result']='success';
            $result['status']='finished';
            $result['log']='scanning files finished'.PHP_EOL;
            $result['percent']=100;
            $result['continue']=0;

        }
        else
        {
            $size=0;
            $folder=$ret['folder'];
            $offset=$ret['offset'];
            $total=$ret['total'];
            $files=$uploads_scanner->get_files($folder);

            $upload_folder = wp_upload_dir();

            $root_path =$upload_folder['basedir'];

            $start=0;
            $count=0;
            $limit=get_option('wpvivid_uc_files_limit',100);

            $unused_files=array();
            foreach ($files as $file)
            {
                if($count>$limit)
                {
                    $uploads_scanner->update_unused_uploads_task($unused_files,$folder,0,$start,'running',0,$size);

                    $result['result']='success';
                    $result['status']='running';
                    $result['continue']=1;
                    $task=get_option('unused_uploads_task',array());
                    $result['task']=$task;
                    $result[$folder]=$unused_files;
                    $result['log']='scanning folder '.$folder.PHP_EOL.'scanned files:'.$start.PHP_EOL;
                    $ret=$uploads_scanner->get_unused_uploads_progress();
                    $result['total_folders']=$ret['total_folders'];
                    $result['scanned_folders']=$ret['scanned_folders'];
                    $result['percent']=$ret['percent'];

                    $result['descript']='Scanning upload folder:'.$folder.'<br>'.$start.' files have been scanned in '.$total.' files';
                    $result['progress_html']='
        <div class="action-progress-bar">
            <div class="action-progress-bar-percent" style="height:24px;width:' . $result['percent'] . '%"></div>
        </div>
        <div style="float:left;">
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Total Folders:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['total_folders'] . '</span>
            </div>
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Scanned:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['scanned_folders'] . '</span>
            </div>
        </div>       
        <div style="clear:both;"></div>
        <div style="margin-left:10px; float: left; width:100%;">
            <p>' .  $result['descript'] . '</p>
        </div>
        <div style="clear: both;"></div>
        <div>
             <div class="backup-log-btn">
                <input class="button-primary" id="wpvivid_uc_cancel" type="submit" value="' . esc_attr('Cancel', 'wpvivid-backuprestore') . '" />
             </div>          
        </div>
        <div style="clear: both;"></div>';
                    echo json_encode($result);
                    die();
                }

                if($start>=$offset)
                {
                    if(!$uploads_scanner->is_uploads_files_exist($file))
                    {
                        $unused_files[]=$file;
                        $size+=filesize($root_path.DIRECTORY_SEPARATOR . $file);
                    }
                    $count++;
                }
                $start++;
            }

            $uploads_scanner->update_unused_uploads_task($unused_files,$folder,1,0,'running',0,$size);

            $result['result']='success';
            $result['status']='running';
            $result['continue']=1;
            $result[$folder]=$unused_files;
            $result['log']='scanning folder '.$folder.PHP_EOL.'scanned files:'.$start.PHP_EOL;
            $ret=$uploads_scanner->get_unused_uploads_progress();
            $result['total_folders']=$ret['total_folders'];
            $result['scanned_folders']=$ret['scanned_folders'];
            $result['percent']=$ret['percent'];

            $upload_folder = wp_upload_dir();

            $result['descript']='Scanning upload folder:'.$folder.'<br>'.$start.' files have been scanned in '.$total.' files';
            $result['progress_html']='
        <div class="action-progress-bar">
            <div class="action-progress-bar-percent" style="height:24px;width:' . $result['percent'] . '%"></div>
        </div>
        <div style="float:left;">
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Total Folders:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['total_folders'] . '</span>
            </div>
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Scanned:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['scanned_folders'] . '</span>
            </div>
        </div>       
        <div style="clear:both;"></div>
        <div style="margin-left:10px; float: left; width:100%;">
            <p>' .  $result['descript'] . '</p>
        </div>
        <div style="clear: both;"></div>
        <div>
             <div class="backup-log-btn">
                <input class="button-primary" id="wpvivid_uc_cancel" type="submit" value="' . esc_attr('Cancel', 'wpvivid-backuprestore') . '" />
             </div>          
        </div>
        <div style="clear: both;"></div>';
        }
        echo json_encode($result);
        die();
    }

    public function scan_post_types($post_types)
    {
        $default_post_types=array();
        $default_post_types[]='attachment';
        $default_post_types[]='revision';
        $default_post_types[]='auto-draft';
        $default_post_types[]='nav_menu_item';
        $default_post_types[]='shop_order';
        $default_post_types[]='shop_order_refund';
        $default_post_types[]='oembed_cache';
        $post_types=get_option('wpvivid_uc_post_types',$default_post_types);
        return $post_types;
    }

    public function scan_exclude_files_regex($regex)
    {
        $files=get_option('wpvivid_uc_exclude_files_regex',array());
        if(empty($files))
        {
            return $regex;
        }
        foreach ($files as $file)
        {
            $regex[]='#'.$file.'$#';
        }
        $regex[]='#webp$#';
        return $regex;
    }

    public function scan_include_files_regex($regex)
    {
        $default_file_types=array();
        $default_file_types[]='png';
        $default_file_types[]='jpg';
        $default_file_types[]='jpeg';
        $scan_file_types=get_option('wpvivid_uc_scan_file_types',$default_file_types);

        $regex=array();
        foreach ($scan_file_types as $scan_file_type)
        {
            $regex[]='#.*\.'.$scan_file_type.'#';
        }

        return $regex;
    }

    public function add_exclude_files()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $json = $_POST['selected'];
        $json = stripslashes($json);
        $json = json_decode($json, true);

        $selected_list=$json['selected'];

        $sanitize_list=array();
        foreach ($selected_list as $item)
        {
            $sanitize_list[]=intval($item);
        }

        $scanner=new WPvivid_Uploads_Scanner();
        $files=$scanner->get_selected_files_list($sanitize_list);

        $list=new WPvivid_Unused_Upload_Files_List();

        if($files===false||empty($files))
        {

        }
        else
        {
            $options=get_option('wpvivid_uc_exclude_files_regex',array());

            $options=array_merge($files,$options);

            update_option('wpvivid_uc_exclude_files_regex',$options);

            $scanner->delete_selected_files_list($sanitize_list);
        }


        $search='';
        if(isset($_POST['search']))
        {
            $search=$_POST['search'];
        }

        $folder='';
        if(isset($_POST['folder']))
        {
            $folder=$_POST['folder'];
        }

        $result=$scanner->get_scan_result($search,$folder);

        $list->set_list($result);

        $list->prepare_items();
        ob_start();
        $list->display();
        $html = ob_get_clean();

        $ret['result']='success';
        $ret['html']=$html;
        echo json_encode($ret);
        die();
    }

    public function get_result_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];

            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $list=new WPvivid_Unused_Upload_Files_List();
            $scanner=new WPvivid_Uploads_Scanner();
            $result=$scanner->get_scan_result($search,$folder);

            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            if(empty($result))
            {
               $ret['empty']=1;
            }
            else
            {
                $ret['empty']=0;
            }
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

    public function isolate_selected_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $selected_list=$json['selected'];
            $sanitize_list=array();
            foreach ($selected_list as $item)
            {
                $sanitize_list[]=intval($item);
            }

            $scanner=new WPvivid_Uploads_Scanner();
            $files=$scanner->get_selected_files_list($sanitize_list);

            if($files===false||empty($files))
            {

            }
            else
            {
                $iso=new WPvivid_Isolate_Files();
                $result=$iso->isolate_files($files);

                if($result['result']=='success')
                {
                    $scanner->delete_selected_files_list($selected_list);
                }
                else
                {
                    echo json_encode($result);
                    die();
                }
            }


            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $list=new WPvivid_Unused_Upload_Files_List();
            $scanner=new WPvivid_Uploads_Scanner();
            $result=$scanner->get_scan_result($search,$folder);

            $list->set_list($result);

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,'');
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $iso = ob_get_clean();
            $ret['iso']=$iso;
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

    public function start_isolate_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();
            $scanner=new WPvivid_Uploads_Scanner();

            $offset=0;
            $count=100;

            $iso->init_isolate_task();
            $files=$scanner->get_all_files_list($search,$folder,$offset,$count);

            if($files===false||empty($files))
            {
                $iso->update_isolate_task(0,'finished',100);

                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $offset+=$count;
                $result=$iso->isolate_files($files);

                $scanner->delete_all_files_list($search,$folder,$count);

                if($result['result']=='success')
                {
                    $iso->update_isolate_task($offset);
                }
                else
                {
                    echo json_encode($result);
                    die();
                }
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function isolate_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();
            $scanner=new WPvivid_Uploads_Scanner();

            $offset=$iso->get_isolate_task_offset();

            if($offset===false)
            {
                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            $start=0;
            $count=100;
            $files=$scanner->get_all_files_list($search,$folder,$start,$count);

            if($files===false||empty($files))
            {
                $iso->update_isolate_task(0,'finished',100);

                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $offset+=$count;
                $result=$iso->isolate_files($files);
                $scanner->delete_all_files_list($search,$folder,$count);

                if($result['result']=='success')
                {
                    $iso->update_isolate_task($offset);
                }
                else
                {
                    echo json_encode($result);
                    die();
                }
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_iso_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $folder = str_replace('\\\\', '\\', $folder);

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,$folder);
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            if(empty($result))
            {
                $ret['empty']=1;
            }
            else
            {
                $ret['empty']=0;
            }
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

    public function delete_selected_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $iso=new WPvivid_Isolate_Files();

            $iso->delete_files($files);

            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $folder = str_replace('\\\\', '\\', $folder);

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,$folder);
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
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

    public function delete_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();

            $count=1000;

            $files=$iso->get_isolate_files($search,$folder,$count);

            if($files===false||empty($files))
            {
                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $iso->delete_files_ex($files);
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    //restore_selected_image
    public function restore_selected_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $iso=new WPvivid_Isolate_Files();
            $iso->restore_files($files);

            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $folder = str_replace('\\\\', '\\', $folder);

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,$folder);
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
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

    public function restore_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();

            $count=100;

            $files=$iso->get_isolate_files($search,$folder,$count);

            if($files===false||empty($files))
            {
                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $iso->restore_files_ex($files);
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
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