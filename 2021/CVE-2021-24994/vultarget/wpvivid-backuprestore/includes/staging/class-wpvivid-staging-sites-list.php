<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Staging_List extends WP_List_Table
{
    public $list;
    public $page_num;
    public $parent;

    public function __construct( $args = array() )
    {
        global $wpdb;
        parent::__construct(
            array(
                'plural' => 'staging',
                'screen' => 'staging',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list)
    {
        $this->list=$list;
    }

    protected function get_table_classes() {
        return array( 'widefat', 'plugins', $this->_args['plural'] );
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
        $posts_columns = array();

        $posts_columns['pic']  = _('');
        $posts_columns['info'] = _('');

        return $posts_columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array('pic', 'info');
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $total_items =sizeof($this->list);
    }

    public function has_items()
    {
        return !empty($this->list);
    }

    protected function _column_pic( $item, $classes, $data, $primary )
    {
        if(isset($item['site']['fresh_install']))
        {
            $url=esc_url(WPVIVID_PLUGIN_IMAGES_URL.'staging/Fresh-list.png');
        }
        else
        {
            $url=esc_url(WPVIVID_PLUGIN_IMAGES_URL.'staging/living-site.png');
        }

        echo '<td class="column-primary" style="margin: 10px;">
                    <div>
                          <div style="margin:auto; width:100px; height:100px; right:50%;">
                            <img src="'.$url.'">
                          </div>
                          <div class="'.esc_attr($item['id']).'" style="margin-top:10px;">
                            <div class="wpvivid-delete-staging-site" style="margin: auto;width: 70px;background-color:#f1f1f1; padding-top:4px;padding-bottom:4px; cursor:pointer;text-align:center;" title="Delete the stating site">Delete</div>
                          </div>           
                     </div>
              </td>';
    }

    protected function _column_info( $item, $classes, $data, $primary ){
        $home_url = home_url();
        global $wpdb;
        $home_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'home' ) );
        foreach ( $home_url_sql as $home ){
            $home_url = $home->option_value;
        }
        $home_url = untrailingslashit($home_url);

        $admin_url  = apply_filters('wpvividstg_get_admin_url', '');
        if(isset($item['site']['mu_single']))
        {
            $admin_url =admin_url();
        }
        $admin_name = str_replace($home_url, '', $admin_url);
        $admin_name = trim($admin_name, '/');

        if(isset($item['site']['prefix']) && !empty($item['site']['prefix'])){
            $prefix = $item['site']['prefix'];
            if(isset($item['site']['db_connect']['dbname']) && !empty($item['site']['db_connect']['dbname'])){
                $db_name = $item['site']['db_connect']['dbname'];
            }
            else{
                $db_name = DB_NAME;
            }
        }
        else{
            $prefix = 'N/A';
            $db_name = 'N/A';
        }
        if(isset($item['site']['path']) && !empty($item['site']['path'])){
            $site_dir = $item['site']['path'];
        }
        else{
            $site_dir = 'N/A';
        }
        if(isset($item['site']['home_url']) && !empty($item['site']['home_url'])){
            $site_url = esc_url($item['site']['home_url']);
            $admin_url = esc_url($item['site']['home_url'].'/'.$admin_name.'/');
            $site_url_link = '<a href="'.esc_url($site_url).'" target="_blank">'.$site_url.'</a>';
            $admin_url_link = '<a href="'.esc_url($admin_url).'" target="_blank">'.$admin_url.'</a>';
        }
        else{
            $site_url_link = 'N/A';
            $admin_url_link = 'N/A';
        }

        if(isset($item['site']['fresh_install']))
        {
            $copy_btn='Copy the Fresh Install to Live';
            $update_btn='Update the Fresh Install';
            $site_url='Fresh Install URL';
            $admin_url='Fresh Install Admin URL';
            $tip_text='Tips: Click the \'Copy the Fresh Install to Live\' button above to migrate the fresh install to your live site. Click the \'Update the Fresh Install\' button to update the live site to the fresh install.';
            $class_btn='fresh-install';
        }
        else
        {
            $copy_btn='Copy the Staging Site to Live';
            $update_btn='Update the Staging Site';
            $site_url='Staging Site URL';
            $admin_url='Staging Site Admin URL';
            $tip_text='Tips: Click the \'Copy the Staging Site to Live\' button above to migrate the staging site to your live site. Click the \'Update the Staging Site\' button to update the live site to the staging site.';
            $class_btn='staging-site';
        }

        if(isset($item['site']['mu_single']) && $item['site']['mu_single'] == true){
            $mu_single_class = 'mu-single';
        }
        else{
            $mu_single_class = '';
        }

        echo '<td class="column-description desc" colspan="2">
                        <div style="border-left:4px solid #00a0d2;padding-left:10px;float:left;">
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>'.$site_url.':</strong></span><span class="wpvivid-element-space-right">'.$site_url_link.'</span></div>
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>'.$admin_url.':</strong></span><span class="wpvivid-element-space-right">'.$admin_url_link.'</span></div>
                        </div>
                        <div style="clear:both"></div>
                        <div style="border-left:4px solid #00a0d2;padding-left:10px;float:left;">
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Database:</strong></span><span class="wpvivid-element-space-right">'.__($db_name).'</span></div>
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Table Prefix:</strong></span><span class="wpvivid-element-space-right">'.__($prefix).'</span></div>
                            <div style="height:20px;display:block;float:left;"><span class="wpvivid-element-space-right"><strong>Site Directory:</strong></span><span class="wpvivid-element-space-right">'.__($site_dir).'</span></div>
                        </div>
                        <div style="clear:both"></div>
                        <div class="wpvivid-copy-staging-to-live-block '.$class_btn.' '.$mu_single_class.'" style="margin-top: 10px;">
                            <div>
                                <input class="button-primary wpvivid-copy-staging-to-live '.$class_btn.' '.$mu_single_class.'" type="button" value="'.$copy_btn.'" style="margin-right: 10px;" />
                                <input class="button-primary wpvivid-update-live-to-staging '.$class_btn.' '.$mu_single_class.'" type="button" value="'.$update_btn.'" />
                            </div>
                            <div style="border: 1px solid #f1f1f1; border-radius: 6px; margin-top: 10px;padding:5px;"><span>'.$tip_text.'</span></div>
                        </div>
                    </td>';
    }

    public function display_rows()
    {
        $this->_display_rows( $this->list );
    }

    private function _display_rows( $list )
    {
        foreach ( $list as $key=>$item)
        {
            $item['id']=$key;
            $this->single_row($item);
        }
    }

    public function single_row($item)
    {
        if(isset($item['site']['path']) && !empty($item['site']['path'])){
            $staging_site_name = basename($item['site']['path']);
        }
        else{
            $staging_site_name = 'N/A';
        }

        if(isset($item['site']['fresh_install']))
        {
            $text='Fresh Install Name';
        }
        else
        {
            $text='Staging Site Name';
        }

        if(isset($item['db_connect']['old_site_url']))
        {
            $live_domain = $item['db_connect']['old_site_url'];
        }
        else{
            $live_domain = 'N/A';
        }

        ?>
        <tr class="<?php echo $item['id']; ?>">
            <td class="column-primary" style="border-top:1px solid #f1f1f1; border-bottom:1px solid #f1f1f1;" colspan="3" >
                <span><strong><?php echo $text; ?>: </strong></span><span><?php echo _($staging_site_name); ?></span>
                <?php
                if(isset($item['site']['mu_single']))
                {
                    $site_id=$item['site']['mu_single_site_id'];
                    $site_url=get_site_url($site_id);
                    ?>
                    <span style="margin-left: 20px;"><strong>Live Site: </strong></span><span><?php echo _($site_url); ?></span>
                    <?php
                }
                else{
                    ?>
                    <span style="margin-left: 20px;"><strong>Live Site: </strong></span><span><?php echo $live_domain; ?></span>
                    <?php
                }
                ?>
            </td>
        </tr>
        <tr id="<?php echo $item['id']; ?>" class="<?php echo $item['id']; ?>">
            <?php $this->single_row_columns( $item ); ?>
        </tr>
        <?php
    }

    public function display() {
        $singular = $this->_args['singular'];

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" style="border: 1px solid #f1f1f1; border-top: none;">
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

    public function display_js()
    {
        ?>
        <script>

        </script>
        <?php
    }
}

class WPvivid_Staging_MU_Site_List_Free extends WP_List_Table
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
                'plural' => 'staging_mu_site',
                'screen' => 'staging_mu_site',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list,$type,$page_num=1)
    {
        $this->list=$list;
        $this->type=$type;
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
            'cb'          => '<input type="checkbox" />',
            'blogname'    => __( 'Subsite URL' ),
            'tables_folders'=>__( 'Subsite Tables/Folders' ),
            'title' => __( 'Subsite Title' ),
            'description'  => __( 'Subsite Description')
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

    public function column_cb( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname = get_object_vars($subsite)["domain"].get_object_vars($subsite)["path"];
        ?>
        <label class="screen-reader-text" for="blog_<?php echo $subsite_id; ?>">
            <?php
            printf( __( 'Select %s' ), $blogname );
            ?>
        </label>
        <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>" value="<?php echo esc_attr( $subsite_id ); ?>" checked />
        <?php
    }

    public function column_id( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        echo $subsite_id;
    }

    public function column_blogname( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname    = untrailingslashit( get_object_vars($subsite)['domain'] . get_object_vars($subsite)['path'] );
        ?>
        <strong>
            <a href="<?php echo esc_url( network_admin_url( 'site-info.php?id=' .$subsite_id ) ); ?>" class="edit"><?php echo $blogname; ?></a>
        </strong>
        <?php
    }

    public function column_tables_folders( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $disable='';
        /*if( $this->type=='copy_mu_site')
        {
            $disable='';
        }
        else
        {
            $disable='disabled';
        }*/
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_tables" value="<?php echo esc_attr( $subsite_id ); ?>" checked <?php echo esc_attr( $disable ); ?>/>
            Tables /
        </label>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_folders" value="<?php echo esc_attr( $subsite_id ); ?>" checked <?php echo esc_attr( $disable ); ?>/>
            Folders
        </label>
        <?php
    }

    public function column_title( $subsite )
    {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo ( get_option( 'blogname' ) ) ;
        restore_current_blog();
    }

    public function column_description( $subsite ) {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo (  get_option( 'blogdescription ' ) ) ;
        restore_current_blog();
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

        $total_pages     = $this->_pagination_args['total_pages'];
        if ( $total_pages >1)
        {
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

class WPvivid_Staging_MU_Single_Site_List_Free extends WP_List_Table
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
                'plural' => 'staging_mu_site',
                'screen' => 'staging_mu_site',
            )
        );
    }

    public function set_parent($parent)
    {
        $this->parent=$parent;
    }

    public function set_list($list,$type,$page_num=1)
    {
        $this->list=$list;
        $this->type=$type;
        $this->page_num=$page_num;
    }

    protected function get_table_classes()
    {
        return array( 'widefat striped' );
    }

    public function print_column_headers( $with_id = true )
    {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        /*
        if (!empty($columns['cb']))
        {
            static $cb_counter = 1;
            $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __('Select All') . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox"/>';
            $cb_counter++;
        }
        */

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
                //$class[] = 'check-column';
            }
            $tag='th';
            //$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
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
            'blogname'    => __( 'Subsite URL' ),
            //'tables_folders'=>__( 'Subsite Tables/Folders' ),
            'title' => __( 'Subsite Title' ),
            'description'  => __( 'Subsite Description')
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

    public function column_cb( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname = get_object_vars($subsite)["domain"].get_object_vars($subsite)["path"];
        ?>
        <label class="screen-reader-text" for="blog_<?php echo $subsite_id; ?>">
            <?php
            printf( __( 'Select %s' ), $blogname );
            ?>
        </label>
        <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>" value="<?php echo esc_attr( $subsite_id ); ?>" />
        <?php
    }

    public function column_id( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        echo $subsite_id;
    }

    public function column_blogname( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $blogname    = untrailingslashit( get_object_vars($subsite)['domain'] . get_object_vars($subsite)['path'] );
        ?>
        <strong>
            <a href="<?php echo esc_url( network_admin_url( 'site-info.php?id=' .$subsite_id ) ); ?>" class="edit"><?php echo $blogname; ?></a>
        </strong>
        <?php
    }

    public function column_tables_folders( $subsite )
    {
        $subsite_id = get_object_vars($subsite)["blog_id"];
        $disable='';
        /*if( $this->type=='copy_mu_site')
        {
            $disable='';
        }
        else
        {
            $disable='disabled';
        }*/
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_tables" value="<?php echo esc_attr( $subsite_id ); ?>" <?php echo esc_attr( $disable ); ?>/>
            Tables /
        </label>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->type ); ?>_folders" value="<?php echo esc_attr( $subsite_id ); ?>" <?php echo esc_attr( $disable ); ?>/>
            Folders
        </label>
        <?php
    }

    public function column_title( $subsite )
    {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo ( get_option( 'blogname' ) ) ;
        restore_current_blog();
    }

    public function column_description( $subsite ) {
        switch_to_blog( get_object_vars($subsite)["blog_id"] );
        echo (  get_option( 'blogdescription ' ) ) ;
        restore_current_blog();
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
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                ?>

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

class WPvivid_Custom_MU_Staging_List
{
    public $parent_id;
    public $is_staging_site   = false;
    public $is_sync_site      = false;
    public $staging_home_path = false;
    public $custom_uploads_path;
    public $custom_content_path;
    public $custom_additional_file_path;

    public function __construct(){

    }

    public function set_parent_id($parent_id){
        $this->parent_id = $parent_id;
    }

    public function set_staging_home_path($is_staging_site=false, $is_sync_site=false, $staging_home_path=false){
        $this->is_staging_site   = $is_staging_site;
        $this->is_sync_site      = $is_sync_site;
        $this->staging_home_path = $staging_home_path;
    }

    public function display_rows()
    {
        $core_check = 'checked';
        $database_check = 'checked';
        $database_text_style = 'pointer-events: auto; opacity: 1;';
        $themes_check = 'checked';
        $plugins_check = 'checked';
        $themes_plugins_check = 'checked';
        $themes_plugins_text_style = 'pointer-events: auto; opacity: 1;';
        $uploads_check = 'checked';
        $uploads_text_style = 'pointer-events: auto; opacity: 1;';
        $content_check = 'checked';
        $content_text_style = 'pointer-events: auto; opacity: 1;';
        $additional_file_check = '';
        $additional_file_text_style = 'pointer-events: none; opacity: 0.4;';
        $upload_extension = '';
        $content_extension = '';
        $additional_file_extension = '';

        $db_descript = 'All the tables in the WordPress MU database except for subsites tables.';
        $uploads_descript = 'The folder where images and media files of the main site are stored by default. All files will be copied to the staging site by default. You can exclude folders you do not want to copy.';
        $core_descript = 'These are the essential files for creating a staging site.';
        $themes_plugins_descript = 'All the plugins and themes files used by the MU network. The activated plugins and themes will be copied to the staging site by default. A child theme must be copied if it exists.';
        $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the staging site, except for the wp-content/uploads folder.';
        $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the staging site.';

        ?>
        <table class="wp-list-table widefat plugins wpvivid-custom-table">
            <tbody>
            <!-------- core -------->
            <tr>
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-wordpress-core">WordPress Core</td>
                <td class="column-description desc"><?php _e($core_descript); ?></td>
            </tr>
            <!-------- database -------->
            <tr style="cursor:pointer;">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-database-detail">Database</td>
                <td class="column-description desc wpvivid-handle-database-detail database-desc">
                    <?php _e($db_descript); ?>
                </td>
            </tr>
            <!-------- uploads -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-uploads-check" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-uploads-detail">wp-content/uploads</td>
                <td class="column-description desc wpvivid-handle-uploads-detail uploads-desc"><?php _e($uploads_descript); ?></td>
                <th class="wpvivid-handle-uploads-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-uploads-detail wpvivid-close" style="<?php esc_attr_e($uploads_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-uploads-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-uploads-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-uploads-list">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div>
                                    <div style="float: left; margin-right: 10px;">
                                        <input class="button-primary wpvivid-exclude-uploads-folder-btn" type="submit" value="Exclude Folders" disabled />
                                    </div>
                                    <small>
                                        <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                            <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                        </div>
                                    </small>
                                    <div style="clear: both;"></div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-uploads-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($upload_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-uploads-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- themes and plugins -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-themes-plugins-check" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-themes-plugins-detail">Themes and Plugins</td>
                <td class="column-description desc wpvivid-handle-themes-plugins-detail themes-plugins-desc">
                    <?php _e($themes_plugins_descript); ?>
                </td>
                <th class="wpvivid-handle-themes-plugins-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-themes-plugins-detail wpvivid-close" style="pointer-events: auto; opacity: 1; display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary wpvivid-custom-themes-plugins-info">
                    <div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left;">Archieving themes and plugins</div>
                    <div style="clear: both;"></div>
                </td>
            </tr>
            <!-------- content -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-content-check" checked disabled/>
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-content-detail">wp-content</td>
                <td class="column-description desc wpvivid-handle-content-detail content-desc"><?php _e($contents_descript); ?></td>
                <th class="wpvivid-handle-content-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-content-detail wpvivid-close" style="<?php esc_attr_e($content_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-content-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-content-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-content-list">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-exclude-content-folder-btn" type="submit" value="Exclude Folders" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-content-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($content_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-content-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- additional files -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-additional-file-check" <?php esc_attr_e($additional_file_check); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-additional-file-detail">Additional Files/Folder</td>
                <td class="column-description desc wpvivid-handle-additional-file-detail additional-file-desc"><?php _e($additional_file_descript); ?></td>
                <th class="wpvivid-handle-additional-file-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-additional-file-detail wpvivid-close" style="<?php esc_attr_e($additional_file_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-additional-file-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder/File Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-additional-file-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-include-additional-file-list">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-include-additional-file-btn" type="submit" value="Include folders/files" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-additional-file-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($additional_file_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-additional-file-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function load_js(){
        $upload_dir = wp_upload_dir();
        $upload_path = $this->is_staging_site === false ?  $upload_dir['basedir'] : $this->staging_home_path.'/wp-content/uploads';
        $upload_path = str_replace('\\','/',$upload_path);
        $upload_path = $upload_path.'/';
        $this->custom_uploads_path = $upload_path;

        $content_dir = $this->is_staging_site === false ? WP_CONTENT_DIR : $this->staging_home_path.'/wp-content';
        $content_path = str_replace('\\','/',$content_dir);
        $content_path = $content_path.'/';
        $this->custom_content_path = $content_path;

        $additional_file_path = $this->is_staging_site === false ? str_replace('\\','/',get_home_path()) : str_replace('\\','/',$this->staging_home_path);
        $this->custom_additional_file_path = $additional_file_path;
        ?>
        <script>
            function wpvivid_handle_custom_open_close(obj, sub_obj){
                if(obj.hasClass('wpvivid-close')) {
                    sub_obj.hide();
                    sub_obj.prev().find('details').prop('open', false);
                    sub_obj.removeClass('wpvivid-open');
                    sub_obj.addClass('wpvivid-close');
                    sub_obj.prev().css('background-color', '#fff');
                    obj.prev().css('background-color', '#f1f1f1');
                    obj.prev().find('details').prop('open', true);
                    obj.show();
                    obj.removeClass('wpvivid-close');
                    obj.addClass('wpvivid-open');
                }
                else{
                    obj.hide();
                    obj.prev().css('background-color', '#fff');
                    obj.prev().find('details').prop('open', false);
                    obj.removeClass('wpvivid-open');
                    obj.addClass('wpvivid-close');
                }
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-database-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-themes-plugins-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-plugins-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-uploads-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-uploads-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-content-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-content-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-additional-file-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-additional-file-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-check', function() {
                if (jQuery(this).prop('checked')) {
                    if(!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                        jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-check').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status) {
                        if (!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                            jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'none', 'opacity': '0.4'});
                        }
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under Custom option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-database-table-check', function() {
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-database-base-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-woo-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-other-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-database-base-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-woo-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-other-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=base_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=woo_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=other_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-themes-plugins-table-check', function(){
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-themes-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-plugins-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-themes-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-plugins-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).val() !== 'wpvivid-backuprestore' && jQuery(this).val() !== 'wpvivid-backup-pro'){
                                    jQuery(this).prop('checked', false);
                                }
                            });
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=themes][name=Themes]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=plugins][name=Plugins]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-uploads-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('upload', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-content-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('content', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-additional-file-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('additional_file', value);
                }
            });

            function wpvivid_update_staging_exclude_extension(type, value){
                var ajax_data = {
                    'action': 'wpvividstg_update_staging_exclude_extension_free',
                    'type': type,
                    'exclude_content': value
                };
                jQuery(this).css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                        }
                    }
                    catch (err) {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('saving staging extension', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-li-close', function(){
                jQuery(this).parent().parent().remove();
            });
        </script>
        <?php
    }
}

class WPvivid_Custom_Staging_List
{
    public $parent_id;
    public $is_staging_site   = false;
    public $staging_home_path = false;
    public $custom_uploads_path;
    public $custom_content_path;
    public $custom_additional_file_path;

    public function __construct(){

    }

    public function set_parent_id($parent_id){
        $this->parent_id = $parent_id;
    }

    public function set_staging_home_path($is_staging_site=false, $staging_home_path=false){
        $this->is_staging_site   = $is_staging_site;
        $this->staging_home_path = $staging_home_path;
    }

    public function display_rows(){
        $core_check = 'checked';
        $database_check = 'checked';
        $database_text_style = 'pointer-events: auto; opacity: 1;';
        $themes_check = 'checked';
        $plugins_check = 'checked';
        $themes_plugins_check = 'checked';
        $themes_plugins_text_style = 'pointer-events: auto; opacity: 1;';
        $uploads_check = 'checked';
        $uploads_text_style = 'pointer-events: auto; opacity: 1;';
        $content_check = 'checked';
        $content_text_style = 'pointer-events: auto; opacity: 1;';
        $additional_file_check = '';
        $additional_file_text_style = 'pointer-events: none; opacity: 0.4;';
        $upload_extension = '';
        $content_extension = '';
        $additional_file_extension = '';
        if($this->is_staging_site){
            $border_css = 'border: 1px solid #f1f1f1;';
            $checkbox_disable = '';
            $core_descript = 'If the staging site and the live site have the same version of WordPress. Then it is not necessary to copy the WordPress core files to the live site.';
            $db_descript = 'It is recommended to copy all tables of the database to the live site.';
            $themes_plugins_descript = 'The activated plugins and themes will be copied to the live site by default. The Child theme must be copied if it exists';
            $uploads_descript = 'Images and media files are stored in the Uploads directory by default. All files are copied to the live site by default. You can exclude folders you do not want to copy.';
            $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the live site, except for the wp-content/uploads folder.';
            $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the live site.';
        }
        else{
            $border_css = 'border: none;';
            $checkbox_disable = ' disabled';
            $core_descript = 'These are the essential files for creating a staging site.';
            $db_descript = 'The tables created by WordPress are required for the staging site. Database tables created by themes or plugins are optional.';
            $themes_plugins_descript = 'The activated plugins and themes will be copied to a staging site by default. A Child theme must be copied if it exists.';
            $uploads_descript = 'Images and media files are stored in the Uploads directory by default. All files are copied to the staging site by default. You can exclude folders you do not want to copy.';
            $contents_descript = '<strong style="text-decoration:underline;"><i>Exclude</i></strong> folders you do not want to copy to the staging site, except for the wp-content/uploads folder.';
            $additional_file_descript = '<strong style="text-decoration:underline;"><i>Include</i></strong> additional files or folders you want to copy to the staging site.';
            $options = get_option('wpvivid_staging_history', array());
            if(isset($options['additional_file_check'])) {
                $additional_file_check = $options['additional_file_check'] == '1' ? 'checked' : '';
                $additional_file_text_style = $options['additional_file_check'] == '1' ? 'pointer-events: auto; opacity: 1;' : 'pointer-events: none; opacity: 0.4;';
            }
            if(isset($options['upload_extension']) && !empty($options['upload_extension'])){
                $upload_extension = implode(",", $options['upload_extension']);
            }
            if(isset($options['content_extension']) && !empty($options['content_extension'])){
                $content_extension = implode(",", $options['content_extension']);
            }
            if(isset($options['additional_file_extension']) && !empty($options['additional_file_extension'])){
                $additional_file_extension = implode(",", $options['additional_file_extension']);
            }
        }
        ?>
        <table class="wp-list-table widefat plugins wpvivid-custom-table" style="<?php esc_attr_e($border_css); ?>">
            <tbody>
            <!-------- core -------->
            <tr>
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-core-check" <?php esc_attr_e($core_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-wordpress-core">Wordpress Core</td>
                <td class="column-description desc core-desc"><?php _e($core_descript); ?></td>
            </tr>
            <!-------- database -------->
            <tr style="cursor:pointer;">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-database-check" <?php esc_attr_e($database_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-database-detail">Database</td>
                <td class="column-description desc wpvivid-handle-database-detail database-desc">
                    <?php _e($db_descript); ?>
                </td>
                <th class="wpvivid-handle-database-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-database-detail wpvivid-close" style="<?php esc_attr_e($database_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary wpvivid-custom-database-info">
                    <div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left;">Archieving database tables</div>
                    <div style="clear: both;"></div>
                </td>
            </tr>
            <!-------- themes and plugins -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-themes-plugins-check" <?php esc_attr_e($themes_plugins_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-themes-plugins-detail">Themes and Plugins</td>
                <td class="column-description desc wpvivid-handle-themes-plugins-detail themes-plugins-desc">
                    <?php _e($themes_plugins_descript); ?>
                </td>
                <th class="wpvivid-handle-themes-plugins-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-themes-plugins-detail wpvivid-close" style="<?php esc_attr_e($themes_plugins_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary wpvivid-custom-themes-plugins-info">
                    <div class="spinner" style="margin: 0 5px 10px 0; float: left;"></div>
                    <div style="float: left;">Archieving themes and plugins</div>
                    <div style="clear: both;"></div>
                </td>
            </tr>
            <!-------- uploads -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-uploads-check" <?php esc_attr_e($uploads_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-uploads-detail">wp-content/uploads</td>
                <td class="column-description desc wpvivid-handle-uploads-detail uploads-desc"><?php _e($uploads_descript); ?></td>
                <th class="wpvivid-handle-uploads-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-uploads-detail wpvivid-close" style="<?php esc_attr_e($uploads_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-uploads-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-uploads-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-uploads-list">
                                    <?php
                                    if(!$this->is_staging_site){
                                        echo $this->wpvivid_load_custom_upload();
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div>
                                    <div style="float: left; margin-right: 10px;">
                                        <input class="button-primary wpvivid-exclude-uploads-folder-btn" type="submit" value="Exclude Folders" disabled />
                                    </div>
                                    <small>
                                        <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                            <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                        </div>
                                    </small>
                                    <div style="clear: both;"></div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-uploads-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($upload_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-uploads-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- content -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-content-check" <?php esc_attr_e($content_check.$checkbox_disable); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-content-detail">wp-content</td>
                <td class="column-description desc wpvivid-handle-content-detail content-desc"><?php _e($contents_descript); ?></td>
                <th class="wpvivid-handle-content-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-content-detail wpvivid-close" style="<?php esc_attr_e($content_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-content-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-content-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-exclude-content-list">
                                    <?php
                                    if(!$this->is_staging_site){
                                        echo $this->wpvivid_load_custom_content();
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-exclude-content-folder-btn" type="submit" value="Exclude Folders" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-content-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($content_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-content-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            <!-------- additional files -------->
            <tr style="cursor:pointer">
                <th class="check-column" scope="row" style="padding-left: 6px;">
                    <label class="screen-reader-text" for=""></label>
                    <input type="checkbox" name="checked[]" class="wpvivid-custom-check wpvivid-custom-additional-file-check" <?php esc_attr_e($additional_file_check); ?> />
                </th>
                <td class="plugin-title column-primary wpvivid-backup-to-font wpvivid-handle-additional-file-detail">Additional Files/Folder</td>
                <td class="column-description desc wpvivid-handle-additional-file-detail additional-file-desc"><?php _e($additional_file_descript); ?></td>
                <th class="wpvivid-handle-additional-file-detail">
                    <details class="primer" onclick="return false;" style="display: inline-block; width: 100%;">
                        <summary title="Show detail" style="float: right; color: #a0a5aa;"></summary>
                    </details>
                </th>
            </tr>
            <tr class="wpvivid-custom-detail wpvivid-additional-file-detail wpvivid-close" style="<?php esc_attr_e($additional_file_text_style); ?> display: none;">
                <th class="check-column"></th>
                <td colspan="3" class="plugin-title column-primary">
                    <table class="wp-list-table widefat plugins" style="width:100%;">
                        <thead>
                        <tr>
                            <th class="manage-column column-name column-primary" style="border-bottom: 1px solid #e1e1e1 !important;">
                                <label class="wpvivid-refresh-tree wpvivid-refresh-additional-file-tree" style="margin-bottom: 0; font-size: 13px;">Click Here to Refresh Folder/File Tree</label>
                            </th>
                            <th class="manage-column column-description" style="font-size: 13px; border-bottom: 1px solid #e1e1e1 !important;">Checked Folders or Files to Transfer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="wpvivid-custom-uploads-left" style="padding-right: 0;">
                                <div class="wpvivid-custom-uploads-tree">
                                    <div class="wpvivid-custom-tree wpvivid-custom-additional-file-tree-info"></div>
                                </div>
                            </td>
                            <td class="wpvivid-custom-uploads-right">
                                <div class="wpvivid-custom-uploads-table wpvivid-custom-include-additional-file-list">
                                    <?php
                                    if(!$this->is_staging_site){
                                        echo $this->wpvivid_load_additional_file();
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                <div style="float: left; margin-right: 10px;">
                                    <input class="button-primary wpvivid-include-additional-file-btn" type="submit" value="Include folders/files" disabled />
                                </div>
                                <small>
                                    <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                        <div class="wpvivid_tooltiptext">Double click to open the folder tree, press Ctrl + left-click to select multiple items.</div>
                                    </div>
                                </small>
                                <div style="clear: both;"></div>
                            </td>
                        </tr>
                        </tfoot>
                        <div style="clear:both;"></div>
                    </table>
                    <div style="margin-top: 10px;">
                        <div style="float: left; margin-right: 10px;">
                            <input type="text" class="regular-text wpvivid-additional-file-extension" placeholder="Exclude file types, for example: gif,jpg,webp" value="<?php esc_attr_e($additional_file_extension); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_,]/g,'')"/>
                            <input type="button" class="wpvivid-additional-file-extension-rule-btn" value="Save" />
                        </div>
                        <small>
                            <div class="wpvivid_tooltip" style="margin-top: 8px; float: left; line-height: 100%; white-space: normal;">?
                                <div class="wpvivid_tooltiptext">Exclude file types from the copy. All file types are separated by commas, for example: jpg, gif, tmp etc (without a dot before the file type).</div>
                            </div>
                        </small>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    public function wpvivid_load_custom_upload(){
        $options = get_option('wpvivid_staging_history', array());
        $ret = '';
        if(isset($options['uploads_list']) && !empty($options['uploads_list'])) {
            foreach ($options['uploads_list'] as $index => $value) {
                $ret .= '<ul style=\'margin: 0;\'>
                            <li>
                                <div class="'.$value['type'].'"></div>
                                <div class="wpvivid-custom-li-font">'.$value['name'].'</div>
                                <div class="wpvivid-custom-li-close" onclick="wpvivid_remove_custom_tree(this);" title="Remove" style="cursor: pointer;">X</div>
                            </li>
                         </ul>';
            }
        }
        return $ret;
    }

    public function wpvivid_load_custom_content(){
        $options = get_option('wpvivid_staging_history', array());
        $ret = '';
        if(isset($options['content_list']) && !empty($options['content_list'])) {
            foreach ($options['content_list'] as $index => $value) {
                $ret .= '<ul style=\'margin: 0;\'>
                            <li>
                                <div class="'.$value['type'].'"></div>
                                <div class="wpvivid-custom-li-font">'.$value['name'].'</div>
                                <div class="wpvivid-custom-li-close" onclick="wpvivid_remove_custom_tree(this);" title="Remove" style="cursor: pointer;">X</div>
                            </li>
                         </ul>';
            }
        }
        return $ret;
    }

    public function wpvivid_load_additional_file(){
        $options = get_option('wpvivid_staging_history', array());
        $ret = '';
        if(isset($options['additional_file_list']) && !empty($options['additional_file_list'])) {
            foreach ($options['additional_file_list'] as $index => $value) {
                $ret .= '<ul style=\'margin: 0;\'>
                            <li>
                                <div class="'.$value['type'].'"></div>
                                <div class="wpvivid-custom-li-font">'.$value['name'].'</div>
                                <div class="wpvivid-custom-li-close" onclick="wpvivid_remove_custom_tree(this);" title="Remove" style="cursor: pointer;">X</div>
                            </li>
                         </ul>';
            }
        }
        return $ret;
    }

    public function load_js(){
        $upload_dir = wp_upload_dir();
        $upload_path = $this->is_staging_site === false ?  $upload_dir['basedir'] : $this->staging_home_path.'/wp-content/uploads';
        $upload_path = str_replace('\\','/',$upload_path);
        $upload_path = $upload_path.'/';
        $this->custom_uploads_path = $upload_path;

        $content_dir = $this->is_staging_site === false ? WP_CONTENT_DIR : $this->staging_home_path.'/wp-content';
        $content_path = str_replace('\\','/',$content_dir);
        $content_path = $content_path.'/';
        $this->custom_content_path = $content_path;

        $additional_file_path = $this->is_staging_site === false ? str_replace('\\','/',get_home_path()) : str_replace('\\','/',$this->staging_home_path);
        $this->custom_additional_file_path = $additional_file_path;
        ?>
        <script>
            function wpvivid_handle_custom_open_close(obj, sub_obj){
                if(obj.hasClass('wpvivid-close')) {
                    sub_obj.hide();
                    sub_obj.prev().find('details').prop('open', false);
                    sub_obj.removeClass('wpvivid-open');
                    sub_obj.addClass('wpvivid-close');
                    sub_obj.prev().css('background-color', '#fff');
                    obj.prev().css('background-color', '#f1f1f1');
                    obj.prev().find('details').prop('open', true);
                    obj.show();
                    obj.removeClass('wpvivid-close');
                    obj.addClass('wpvivid-open');
                }
                else{
                    obj.hide();
                    obj.prev().css('background-color', '#fff');
                    obj.prev().find('details').prop('open', false);
                    obj.removeClass('wpvivid-open');
                    obj.addClass('wpvivid-close');
                }
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-database-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-themes-plugins-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-plugins-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-uploads-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-uploads-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-content-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-content-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-handle-additional-file-detail', function() {
                var obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-additional-file-detail');
                var sub_obj = jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-detail');
                wpvivid_handle_custom_open_close(obj, sub_obj);
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-check', function() {
                if (jQuery(this).prop('checked')) {
                    if(!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                        jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-custom-check').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status) {
                        if (!jQuery(this).hasClass('wpvivid-custom-core-check')) {
                            jQuery(jQuery(this).parents('tr').next().get(0)).css({'pointer-events': 'none', 'opacity': '0.4'});
                        }
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under Custom option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-database-table-check', function() {
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-database-base-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-woo-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-database-other-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-database-base-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-woo-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-database-other-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one table type under the Database option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=base_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=base_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-base-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=woo_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=woo_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-woo-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=other_db][name=Database]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=other_db][name=Database]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[name=Database]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-database-other-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one table type under the Database option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-themes-plugins-table-check', function(){
                if(jQuery(this).prop('checked')){
                    if(jQuery(this).hasClass('wpvivid-themes-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', true);
                    }
                    else if(jQuery(this).hasClass('wpvivid-plugins-table-check')){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    if (jQuery(this).hasClass('wpvivid-themes-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').prop('checked', false);
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                    else if (jQuery(this).hasClass('wpvivid-plugins-table-check')) {
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                        if(check_status) {
                            jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                                if(jQuery(this).val() !== 'wpvivid-backuprestore' && jQuery(this).val() !== 'wpvivid-backup-pro'){
                                    jQuery(this).prop('checked', false);
                                }
                            });
                        }
                        else{
                            jQuery(this).prop('checked', true);
                            alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                        }
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=themes][name=Themes]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-themes-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", 'input:checkbox[option=plugins][name=Plugins]', function(){
                if(jQuery(this).prop('checked')){
                    var all_check = true;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(!jQuery(this).prop('checked')){
                            all_check = false;
                        }
                    });
                    if(all_check){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', true);
                    }
                }
                else{
                    var check_status = false;
                    jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=plugins][name=Plugins]').each(function(){
                        if(jQuery(this).prop('checked')){
                            check_status = true;
                        }
                    });
                    if(!check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('input:checkbox[option=themes][name=Themes]').each(function(){
                            if(jQuery(this).prop('checked')){
                                check_status = true;
                            }
                        });
                    }
                    if(check_status){
                        jQuery('#<?php echo $this->parent_id; ?>').find('.wpvivid-plugins-table-check').prop('checked', false);
                    }
                    else{
                        jQuery(this).prop('checked', true);
                        alert('Please select at least one item under the Themes and Plugins option, or deselect the option.');
                    }
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-uploads-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('upload', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-content-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('content', value);
                }
            });

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-additional-file-extension-rule-btn', function(){
                var value = jQuery(this).prev().val();
                if(value!=='') {
                    wpvivid_update_staging_exclude_extension('additional_file', value);
                }
            });

            function wpvivid_update_staging_exclude_extension(type, value){
                var ajax_data = {
                    'action': 'wpvividstg_update_staging_exclude_extension_free',
                    'type': type,
                    'exclude_content': value
                };
                jQuery(this).css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                        }
                    }
                    catch (err) {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('saving staging extension', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            jQuery('#<?php echo $this->parent_id; ?>').on("click", '.wpvivid-custom-li-close', function(){
                jQuery(this).parent().parent().remove();
            });
        </script>
        <?php
    }
}

class WPvivid_Staging_Sites_List_Free
{
    public function __construct()
    {

    }
}