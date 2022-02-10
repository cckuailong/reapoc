<?php
/**
 * WPvivid addon: yes
 * Addon Name: wpvivid-backup-pro-all-in-one
 * Description: Pro
 * Version: 1.9.1
 */

if ( ! class_exists( 'WP_List_Table' ) )
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPvivid_Post_List extends WP_List_Table
{
    public $post_ids;
    public $page_num;

    public function __construct( $args = array() ) {
        global $post_type_object, $wpdb;

        parent::__construct(
            array(
                'plural' => 'posts',
                'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
            )
        );

        $post_type        = $this->screen->post_type;
        $post_type_object = get_post_type_object( $post_type );

        $exclude_states         = get_post_stati(
            array(
                'show_in_admin_all_list' => false,
            )
        );
        $this->user_posts_count = intval(
            $wpdb->get_var(
                $wpdb->prepare(
                    "
			SELECT COUNT( 1 )
			FROM $wpdb->posts
			WHERE post_type = %s
			AND post_status NOT IN ( '" . implode( "','", $exclude_states ) . "' )
			AND post_author = %d
		",
                    $post_type,
                    get_current_user_id()
                )
            )
        );

        if ( $this->user_posts_count && ! current_user_can( $post_type_object->cap->edit_others_posts ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['all_posts'] ) && empty( $_REQUEST['author'] ) && empty( $_REQUEST['show_sticky'] ) ) {
            $_GET['author'] = get_current_user_id();
        }

        if ( 'post' === $post_type && $sticky_posts = get_option( 'sticky_posts' ) ) {
            $sticky_posts             = implode( ', ', array_map( 'absint', (array) $sticky_posts ) );
            $this->sticky_posts_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( 1 ) FROM $wpdb->posts WHERE post_type = %s AND post_status NOT IN ('trash', 'auto-draft') AND ID IN ($sticky_posts)", $post_type ) );
        }
    }

    public function print_column_headers( $with_id = true ) {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        if ( ! empty( $columns['cb'] ) )
        {
            $checked='';

            static $cb_counter = 1;
            $columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" '.$checked.'/>';
            $cb_counter++;
        }

        foreach ( $columns as $column_key => $column_display_name ) {
            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) ) {
                $class[] = 'hidden';
            }

            if ( 'cb' === $column_key ) {
                $class[] = 'check-column';
            } elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) ) {
                $class[] = 'num';
            }

            if ( $column_key === $primary ) {
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
        $post_type = $this->screen->post_type;

        $posts_columns = array();

        $posts_columns['cb'] = '<input type="checkbox"/>';
        /* translators: manage posts column name */
        $posts_columns['wpvivid_id'] = 'ID';

        $posts_columns['title'] = _x( 'Title', 'column name' );

        if ( post_type_supports( $post_type, 'author' ) ) {
            $posts_columns['author'] = __( 'Author' );
        }

        $taxonomies = get_object_taxonomies( $post_type, 'objects' );
        $taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name' );

        /**
         * Filters the taxonomy columns in the Posts list table.
         *
         * The dynamic portion of the hook name, `$post_type`, refers to the post
         * type slug.
         *
         * @since 3.5.0
         *
         * @param string[] $taxonomies Array of taxonomy names to show columns for.
         * @param string   $post_type  The post type.
         */
        $taxonomies = apply_filters( "manage_taxonomies_for_{$post_type}_columns", $taxonomies, $post_type );
        $taxonomies = array_filter( $taxonomies, 'taxonomy_exists' );

        foreach ( $taxonomies as $taxonomy ) {
            if ( 'category' === $taxonomy ) {
                $column_key = 'categories';
            } elseif ( 'post_tag' === $taxonomy ) {
                $column_key = 'tags';
            } else {
                $column_key = 'taxonomy-' . $taxonomy;
            }

            $posts_columns[ $column_key ] = get_taxonomy( $taxonomy )->labels->name;
        }

        $posts_columns['comments'] =__( 'Comments' );

        $posts_columns['date'] = __( 'Date' );

        return $posts_columns;
    }

    function set_post_ids($post_ids,$page_num=1)
    {
        $this->post_ids=$post_ids;
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

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $total_items =sizeof($this->post_ids);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => 30,
            )
        );
    }

    public function has_items()
    {
        return !empty($this->post_ids);
    }

    public function column_cb( $post )
    {
        $checked='';
        if($post->checked)
        {
            $checked='checked';
        }
        ?>
        <input id="cb-select-<?php echo $post->ID; ?>" type="checkbox" name="post[]" value="<?php echo $post->ID; ?>" <?php echo $checked ?>/>
        <?php
    }

    /**
     * @since 4.3.0
     *
     * @param WP_Post $post
     * @param string  $classes
     * @param string  $data
     * @param string  $primary
     */
    protected function _column_title( $post, $classes, $data, $primary ) {
        echo '<td class="' . $classes . ' page-title" ', $data, '>';
        echo $this->column_title( $post );
        echo '</td>';
    }

    public function column_wpvivid_id( $post )
    {
        echo '<span>'.$post->ID.'</span>';
    }
    /**
     * Handles the title column output.
     *
     * @since 4.3.0
     *
     * @global string $mode List table view mode.
     *
     * @param WP_Post $post The current WP_Post object.
     */
    public function column_title( $post ) {
        echo '<strong>';
        $title = $post->post_title;
        echo $title;
        echo "</strong>\n";
    }

    /**
     * Handles the post date column output.
     *
     * @since 4.3.0
     *
     * @global string $mode List table view mode.
     *
     * @param WP_Post $post The current WP_Post object.
     */
    public function column_date( $post )
    {
        global $mode;

        if ( '0000-00-00 00:00:00' === $post->post_date ) {
            $t_time    = $h_time = __( 'Unpublished', 'wpvivid-backuprestore' );
            $time_diff = 0;
        } else {
            $t_time = get_the_time( 'Y/m/d g:i:s a' );
            $m_time = $post->post_date;
            $time   = get_post_time( 'G', true, $post );

            $time_diff = time() - $time;

            if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
                $h_time = sprintf( __( '%s ago', 'wpvivid-backuprestore' ), human_time_diff( $time ) );
            } else {
                $h_time = mysql2date( 'Y/m/d', $m_time );
            }
        }

        if ( 'publish' === $post->post_status ) {
            $status = __( 'Published', 'wpvivid-backuprestore' );
        } elseif ( 'future' === $post->post_status ) {
            if ( $time_diff > 0 ) {
                $status = '<strong class="error-message">' . __( 'Missed schedule', 'wpvivid-backuprestore' ) . '</strong>';
            } else {
                $status = __( 'Scheduled', 'wpvivid-backuprestore' );
            }
        } else {
            $status = __( 'Last Modified', 'wpvivid-backuprestore' );
        }

        /**
         * Filters the status text of the post.
         *
         * @since 4.8.0
         *
         * @param string  $status      The status text.
         * @param WP_Post $post        Post object.
         * @param string  $column_name The column name.
         * @param string  $mode        The list display mode ('excerpt' or 'list').
         */
        $status = apply_filters( 'post_date_column_status', $status, $post, 'date', $mode );

        if ( $status ) {
            echo $status . '<br />';
        }

        if ( 'excerpt' === $mode ) {
            /**
             * Filters the published time of the post.
             *
             * If `$mode` equals 'excerpt', the published time and date are both displayed.
             * If `$mode` equals 'list' (default), the publish date is displayed, with the
             * time and date together available as an abbreviation definition.
             *
             * @since 2.5.1
             *
             * @param string  $t_time      The published time.
             * @param WP_Post $post        Post object.
             * @param string  $column_name The column name.
             * @param string  $mode        The list display mode ('excerpt' or 'list').
             */
            echo apply_filters( 'post_date_column_time', $t_time, $post, 'date', $mode );
        } else {

            /** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php */
            echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', $mode ) . '</abbr>';
        }
    }

    /**
     * Handles the comments column output.
     *
     * @since 4.3.0
     *
     * @param WP_Post $post The current WP_Post object.
     */
    public function column_comments( $post ) {
        ?>
        <div class="post-com-count-wrapper">
            <?php
            echo '<span style="text-align:center">'.get_comments_number($post->ID).'</span>'
            ?>
        </div>
        <?php
    }

    /**
     * Handles the post author column output.
     *
     * @since 4.3.0
     *
     * @param WP_Post $post The current WP_Post object.
     */
    public function column_author( $post ) {
        $user_data = get_userdata($post->post_author );

        echo '<span>'.$user_data->display_name.'</span>';
    }

    /**
     * Handles the default column output.
     *
     * @since 4.3.0
     *
     * @param WP_Post $post        The current WP_Post object.
     * @param string  $column_name The current column name.
     */
    public function column_default( $post, $column_name ) {
        if ( 'categories' === $column_name )
        {
            $taxonomy = 'category';
        } elseif ( 'tags' === $column_name )
        {
            $taxonomy = 'post_tag';
        } elseif ( 0 === strpos( $column_name, 'taxonomy-' ) )
        {
            $taxonomy = substr( $column_name, 9 );
        } else {
            $taxonomy = false;
        }
        if ( $taxonomy ) {
            $taxonomy_object = get_taxonomy( $taxonomy );
            $terms           = get_the_terms( $post->ID, $taxonomy );
            if ( is_array( $terms ) ) {
                $out = array();
                foreach ( $terms as $t ) {
                    $posts_in_term_qv = array();
                    if ( 'post' != $post->post_type ) {
                        $posts_in_term_qv['post_type'] = $post->post_type;
                    }
                    if ( $taxonomy_object->query_var ) {
                        $posts_in_term_qv[ $taxonomy_object->query_var ] = $t->slug;
                    } else {
                        $posts_in_term_qv['taxonomy'] = $taxonomy;
                        $posts_in_term_qv['term']     = $t->slug;
                    }

                    $label = esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) );

                    $out[] = $label;
                }
                /* translators: used between list items, there is a space after the comma */
                echo join(  ', ', $out );
            } else {
                echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . $taxonomy_object->labels->no_terms . '</span>';
            }
            return;
        }

        if ( is_post_type_hierarchical( $post->post_type ) ) {

            /**
             * Fires in each custom column on the Posts list table.
             *
             * This hook only fires if the current post type is hierarchical,
             * such as pages.
             *
             * @since 2.5.0
             *
             * @param string $column_name The name of the column to display.
             * @param int    $post_id     The current post ID.
             */
            do_action( 'manage_pages_custom_column', $column_name, $post->ID );
        } else {

            /**
             * Fires in each custom column in the Posts list table.
             *
             * This hook only fires if the current post type is non-hierarchical,
             * such as posts.
             *
             * @since 1.5.0
             *
             * @param string $column_name The name of the column to display.
             * @param int    $post_id     The current post ID.
             */
            do_action( 'manage_posts_custom_column', $column_name, $post->ID );
        }

        /**
         * Fires for each custom column of a specific post type in the Posts list table.
         *
         * The dynamic portion of the hook name, `$post->post_type`, refers to the post type.
         *
         * @since 3.1.0
         *
         * @param string $column_name The name of the column to display.
         * @param int    $post_id     The current post ID.
         */
        do_action( "manage_{$post->post_type}_posts_custom_column", $column_name, $post->ID );
    }

    public function display_rows()
    {
        $this->_display_rows( $this->post_ids );
    }

    private function _display_rows($post_ids)
    {
        $page_post_ids=$post_ids;
        $page=$this->get_pagenum();
        $count=0;
        while ( $count<$page )
        {
            $page_post_ids = array_splice( $post_ids, 0, 30);
            $count++;
        }
        foreach ( $page_post_ids as $post_id)
        {
            $this->single_row($post_id);
        }
    }

    public function single_row($post_id)
    {
        $post = get_post($post_id['id']);
        $post->checked=$post_id['checked'];
        $classes = 'iedit author-' . ( get_current_user_id() == $post->post_author ? 'self' : 'other' );

        ?>
        <tr id="post-<?php echo $post->ID; ?>" class="<?php echo implode( ' ', get_post_class( $classes, $post->ID ) ); ?>">
            <?php $this->single_row_columns( $post ); ?>
        </tr>
        <?php
    }

    /**
     * Display the pagination.
     *
     * @since 3.1.0
     *
     * @param string $which
     */
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
                "%s<input class='current-page' id='current-page-selector-export' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector-export" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
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

    /**
     * Generate the table navigation above or below the table
     *
     * @since 3.1.0
     * @param string $which
     */
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
            <div class="alignleft actions bulkactions">
                <?php echo '<input class="button-primary" id="wpvivid-post-research-submit" type="submit" name="post" value="Reset Filters">'; ?>
            </div>
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>

            <br class="clear" />
        </div>
        <?php
    }
}

class WPvivid_Exporter_taskmanager
{
    public static function get_task($task_id)
    {
        $default = array();
        $tasks = get_option('wpvivid_exporter_task_list', $default);

        if(array_key_exists ($task_id,$tasks))
        {
            return $tasks[$task_id];
        }
        else
        {
            return false;
        }
    }

    public static function update_task($task_id,$task)
    {
        $default = array();
        $options = get_option('wpvivid_exporter_task_list', $default);
        $options[$task_id]=$task;
        WPvivid_Setting::update_option('wpvivid_exporter_task_list',$options);
    }

    public static function get_tasks()
    {
        $default = array();
        return $options = get_option('wpvivid_exporter_task_list', $default);
    }

    public static function get_backup_task_status($task_id)
    {
        $tasks=self::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            return $task['status'];
        }
        else
        {
            return false;
        }
    }

    public static function delete_task($task_id)
    {
        $options = get_option('wpvivid_exporter_task_list', array());
        unset($options[$task_id]);
        WPvivid_Setting::update_option('wpvivid_exporter_task_list',$options);
    }

    public static function update_backup_task_status($task_id,$reset_start_time=false,$status='',$reset_timeout=false,$resume_count=false,$error='')
    {
        $tasks=self::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            $task['status']['run_time']=time();
            if($reset_start_time)
                $task['status']['start_time']=time();
            if(!empty($status))
            {
                $task['status']['str']=$status;
            }
            if($reset_timeout)
                $task['status']['timeout']=time();
            if($resume_count!==false)
            {
                $task['status']['resume_count']=$resume_count;
            }

            if(!empty($error))
            {
                $task['status']['error']=$error;
            }
            self::update_task($task_id,$task);
            return $task;
        }
        else
        {
            return false;
        }
    }

    public static function get_task_options($task_id,$option_names)
    {
        $tasks=self::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task=$tasks[$task_id];

            if(is_array($option_names))
            {
                $options=array();
                foreach ($option_names as $name)
                {
                    $options[$name]=$task['options'][$name];
                }
                return $options;
            }
            else
            {
                return $task['options'][$option_names];
            }
        }
        else
        {
            return false;
        }
    }

    public static function is_tasks_running()
    {
        $tasks=self::get_tasks();
        foreach ($tasks as $task)
        {
            if ($task['status']['str']=='running'||$task['status']['str']=='no_responds')
            {
                return true;
            }
        }
        return false;
    }

    public static function update_main_task_progress($task_id,$job_name,$progress,$finished,$job_data=array())
    {
        $task=self::get_task($task_id);
        if($task!==false)
        {
            $task['status']['run_time']=time();
            $task['status']['str']='running';
            $task['data']['doing']=$job_name;
            $task['data'][$job_name]['finished']=$finished;
            $task['data'][$job_name]['progress']=$progress;
            $task['data'][$job_name]['job_data']=$job_data;
            self::update_task($task_id,$task);
        }
    }

    public static function get_backup_tasks_progress($task_id)
    {
        $tasks=self::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            $current_time=date("Y-m-d H:i:s");
            $create_time=date("Y-m-d H:i:s",$task['status']['start_time']);
            $time_diff=strtotime($current_time)-strtotime($create_time);
            $running_time='';
            if(date("G",$time_diff) > 0){
                $running_time .= date("G",$time_diff).'hour';
            }
            if(intval(date("i",$time_diff)) > 0){
                $running_time .= intval(date("i",$time_diff)).'min';
            }
            if(intval(date("s",$time_diff)) > 0){
                $running_time .= intval(date("s",$time_diff)).'second';
            }

            $ret['type']=$task['data']['doing'];
            $ret['progress']=$task['data'][$ret['type']]['progress'];
            $ret['doing']=$task['data'][$ret['type']]['doing'];
            if(isset($task['data'][$ret['type']]['sub_job'][$ret['doing']]['progress']))
                $ret['descript']=__($task['data'][$ret['type']]['sub_job'][$ret['doing']]['progress'], 'wpvivid-backuprestore');
            else
                $ret['descript']='';
            if(isset($task['data'][$ret['type']]['sub_job'][$ret['doing']]['upload_data']))
                $ret['upload_data']=$task['data'][$ret['type']]['sub_job'][$ret['doing']]['upload_data'];
            $task['data'][$ret['type']]['sub_job'][$ret['doing']]['upload_data']=false;
            $ret['running_time']=$running_time;
            $ret['running_stamp']=$time_diff;

            return $ret;
        }
        else
        {
            return false;
        }
    }
}

class WPvivid_Exporter_task
{
    private $task;

    public function __construct($task_id=false,$task=false)
    {
        if($task_id!==false)
        {
            $this->task=WPvivid_Exporter_taskmanager::get_task($task_id);
        }

        if($task!==false)
        {
            $this->task=$task;
        }
    }

    public function get_id()
    {
        return $this->task['id'];
    }

    public function new_backup_task($options)
    {
        $id=uniqid('wpvivid-');
        $this->task=false;
        $this->task['id']=$id;

        $this->task['status']['start_time']=time();
        $this->task['status']['run_time']=time();
        $this->task['status']['timeout']=time();
        $this->task['status']['str']='ready';
        $this->task['status']['resume_count']=0;

        if(isset($options['remote'])) {
            if($options['remote']=='1') {
                $this->task['options']['remote_options'] = isset($options['remote_options']) ? $options['remote_options'] : WPvivid_Setting::get_remote_options();
            }
            else {
                $this->task['options']['remote_options']=false;
            }
        }
        else {
            $this->task['options']['remote_options']=false;
        }

        $this->task['options']['remote_options'] = apply_filters('wpvivid_set_remote_options', $this->task['options']['remote_options'],$options);

        if(isset($options['local'])) {
            $this->task['options']['save_local'] = $options['local']=='1' ? 1 : 0;
        }
        else {
            $this->task['options']['save_local']=1;
        }

        $this->task['options']['post_comment'] = $options['post_comment'];

        if(empty($backup_prefix))
            $this->task['options']['file_prefix'] = $this->task['id'] . '_' . date('Y-m-d-H-i', $this->task['status']['start_time']);
        else
            $this->task['options']['file_prefix'] = $backup_prefix . '_' . $this->task['id'] . '_' . date('Y-m-d-H-i', $this->task['status']['start_time']);

        $this->task['options']['log_file_name']=$id.'_export';
        $log=new WPvivid_Log();
        $log->CreateLogFile($this->task['options']['log_file_name'],'no_folder','export');
        $this->task['options']['backup_options']['prefix']=$this->task['options']['file_prefix'];
        $this->task['options']['backup_options']['compress']=WPvivid_Setting::get_option('wpvivid_compress_setting');
        $this->task['options']['backup_options']['dir']=WPvivid_Setting::get_backupdir();
        $this->task['options']['backup_options']['post_ids']=$options['post_ids'];
        $this->task['options']['backup_options']['post_type']=$options['post_type'];
        $export_data['json_info']['post_type']=$options['post_type'];
        //$export_data['json_info']['post_ids']=$options['post_ids'];
        $export_data['json_info']['post_comment']=$options['post_comment'];
        $this->task['options']['backup_options']['backup'][$options['post_type']]=$export_data;
        $this->task['data']['doing']='export';
        $this->task['data']['export']['doing']='';
        $this->task['data']['export']['finished']=0;
        $this->task['data']['export']['progress']=0;
        if(sizeof($options['post_ids'])>50) {
            $this->task['data']['export']['pre_progress']=(50/sizeof($options['post_ids']))*100;
        }
        else {
            $this->task['data']['export']['pre_progress']=100;
        }
        $this->task['data']['export']['job_data']=array();
        $this->task['data']['export']['sub_job']=array();
        $this->task['data']['export']['export_info']['post_count']=sizeof($options['post_ids']);
        $this->task['data']['upload']['doing']='';
        $this->task['data']['upload']['finished']=0;
        $this->task['data']['upload']['progress']=0;
        $this->task['data']['upload']['job_data']=array();
        $this->task['data']['upload']['sub_job']=array();
        WPvivid_Exporter_taskmanager::update_task($id,$this->task);
        $ret['result']='success';
        $ret['task_id']=$this->task['id'];
        $log->CloseFile();
        return $ret;
    }

    private function parse_url_all($url)
    {
        $parse = parse_url($url);
        $path=str_replace('/','_',$parse['path']);
        return $parse['host'].$path;
    }

    public function update_sub_task_progress($key,$finished,$progress)
    {
        $this->task=WPvivid_Exporter_taskmanager::get_task($this->get_id());
        $this->task['status']['run_time']=time();
        $this->task['status']['str']='running';
        $this->task['data']['doing']='export';
        $sub_job_name=$key;
        $this->task['data']['export']['doing']=$key;
        $this->task['data']['export']['sub_job'][$sub_job_name]['finished']=$finished;
        $this->task['data']['export']['sub_job'][$sub_job_name]['progress']=$progress;
        if(!isset( $this->task['data']['export']['sub_job'][$sub_job_name]['job_data']))
        {
            $this->task['data']['export']['sub_job'][$sub_job_name]['job_data']=array();
        }
        WPvivid_Exporter_taskmanager::update_task($this->get_id(),$this->task);
    }

    public function get_next_posts()
    {
        asort($this->task['options']['backup_options']['post_ids']);
        WPvivid_Exporter_taskmanager::update_task($this->get_id(),$this->task);
        $post_ids=$this->task['options']['backup_options']['post_ids'];
        if(empty($post_ids))
        {
            return false;
        }
        /*if(sizeof($post_ids)>50)
        {
            $next_post_ids = array_splice( $post_ids, 0, 50 );
        }
        else
        {
            $next_post_ids=$post_ids;
        }*/
        $next_post_ids=$post_ids;
        $ret=$this->get_post_contain_attachment_ids($next_post_ids);

        $next_post_ids = array_splice( $this->task['options']['backup_options']['post_ids'], 0, $ret['post_count'] );
        $ret['next_post_ids']=$next_post_ids;

        $post_type = $this->task['options']['backup_options']['post_type'];
        $ret['json_info'] = $this->task['options']['backup_options']['backup'][$post_type]['json_info'];

        $first=reset($next_post_ids);
        $last=end($next_post_ids);

        $post_comment = !empty($this->task['options']['post_comment']) ? $this->task['options']['post_comment'].'_' : '';
        $ret['file_name']=$post_comment.self::get_id().'_'.date('Y-m-d-H-i', $this->task['status']['start_time']);
        $ret['export_type']=$this->task['options']['backup_options']['post_type'];
        return $ret;
    }

    public function update_finished_posts($finished_posts)
    {
        $this->task=WPvivid_Exporter_taskmanager::get_task( $this->get_id());
        array_splice( $this->task['options']['backup_options']['post_ids'], 0, $finished_posts['post_count'] );
        $this->task['data']['export']['progress']=$this->task['data']['export']['progress']+$this->task['data']['export']['pre_progress'];
        if($this->task['data']['export']['progress']>100)
        {
            $this->task['data']['export']['progress']=100;
        }
        WPvivid_Exporter_taskmanager::update_task($this->get_id(),$this->task);
    }

    public function update_export_files($file_data)
    {
        $this->task=WPvivid_Exporter_taskmanager::get_task( $this->get_id());

        $this->task['data']['file_data'][]=$file_data;

        $this->task['data']['export']['export_info']['file_name']=$file_data['file_name'];
        $this->task['data']['export']['export_info']['size']=$file_data['size'];

        WPvivid_Exporter_taskmanager::update_task($this->get_id(),$this->task);
    }

    public function get_export_files()
    {
        $this->task=WPvivid_Exporter_taskmanager::get_task( $this->get_id());

        if(isset($this->task['data']['file_data']))
        {
            $file_data=$this->task['data']['file_data'];
            return $file_data;
        }
        else
        {
            return array();
        }
    }

    public function get_post_contain_attachment_ids($post_ids)
    {
        $max_size=1024*1024*100;
        $current_size=0;
        $count=0;
        $sum_attachment_ids=array();
        $attachment_added_ids=array();
        $files=array();
        foreach ($post_ids as $id)
        {
            $count++;

            $attachment_ids=array();
            $post   = get_post( $id );
            if (preg_match_all( '/<img [^>]+>/', $post->post_content, $matches ) )
            {
                foreach( $matches[0] as $image )
                {
                    if ( preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) && ( $attachment_id = absint( $class_id[1] ) ) )
                    {
                        if(!in_array($attachment_id,$attachment_added_ids))
                        {
                            if(!is_null(get_post($attachment_id)))
                            {
                                $attachment_ids[] = $attachment_id;
                                $attachment_added_ids[]=$attachment_id;
                            }
                            else
                            {
                                $ret_attachment=$this->get_image_from_post_content($image);
                                $current_size+=$ret_attachment['size'];
                                $files=array_merge($files,$ret_attachment['files']);
                            }
                        }
                    }
                    else
                    {
                        $ret_attachment=$this->get_image_from_post_content($image);
                        $current_size+=$ret_attachment['size'];
                        $files=array_merge($files,$ret_attachment['files']);
                    }
                }
            }

            $_elementor_meta=get_post_meta($id,'_elementor_data',true);
            if($_elementor_meta!=false)
            {
                if ( is_string( $_elementor_meta ) && ! empty( $_elementor_meta ) )
                {
                    $_elementor_meta = json_decode( $_elementor_meta, true );
                }
                if ( empty( $_elementor_meta ) )
                {
                    $_elementor_meta = array();
                }
                $elements_data=$_elementor_meta;
                foreach ( $elements_data as $element_data )
                {
                    $element_image=$this->get_element_image($element_data,$attachment_added_ids);
                    $attachment_ids=array_merge($attachment_ids,$element_image);
                }
            }

            //_thumbnail_id
            $_thumbnail_id=get_post_meta($id,'_thumbnail_id',true);
            if($_thumbnail_id!=false)
            {
                if(!in_array($_thumbnail_id,$attachment_added_ids))
                {
                    if(!is_null(get_post($_thumbnail_id)))
                    {
                        $attachment_ids[] = $_thumbnail_id;
                        $attachment_added_ids[]=$_thumbnail_id;
                    }
                }
            }

            $sum_attachment_ids=array_merge($sum_attachment_ids,$attachment_ids);

            foreach ($attachment_ids as $attachment_id)
            {
                $ret_attachment=$this->get_attachment_size($attachment_id);
                $current_size+=$ret_attachment['size'];
                $files=array_merge($files,$ret_attachment['files']);
            }

            if($current_size>$max_size)
            {
                break;
            }
        }

        $ret['attachment_ids']=$sum_attachment_ids;
        $ret['post_count']=$count;
        $ret['files']=$files;
        return $ret;
    }

    public function get_image_from_post_content($image)
    {
        $ret['size']=0;
        $ret['files']=array();

        if(class_exists('DOMDocument'))
        {
            $doc = new DOMDocument();
            $doc->loadHTML($image);
            $xpath = new DOMXPath($doc);
            $src = $xpath->evaluate("string(//img/@src)");
        }
        else
        {
            preg_match('/src="([^"]+)/i',$image, $src);
            $src= str_ireplace( 'src="', '',  $src[0]);
        }

        $src=str_replace('https://','',$src);
        $src=str_replace('http://','',$src);

        $upload=wp_upload_dir();

        $upload['baseurl']=str_replace('https://','',$upload['baseurl']);
        $upload['baseurl']=str_replace('http://','',$upload['baseurl']);


        $path=str_replace($upload['baseurl'],$upload['basedir'],$src);
        if(file_exists($path))
        {
            $ret['size']+=filesize($path);
            $ret['files'][]=$path;
        }

        return $ret;
    }

    public function get_attachment_size($attachment_id)
    {
        $files=array();
        global $wpdb;

        $postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $attachment_id ) );

        foreach ( $postmeta as $meta )
        {
            $upload_dir = wp_upload_dir();

            if ( $upload_dir['error'] !== false )
            {
                continue;
            }

            $dir=$upload_dir['basedir'];
            if ( apply_filters( 'wxr_export_skip_postmeta', false, $meta->meta_key, $meta ) ) {
                continue;
            }
            if($meta->meta_key=='_wp_attached_file')
            {
                $bfound=false;
                $name=$dir.DIRECTORY_SEPARATOR.$meta->meta_value;
                if(!in_array($name,$files)&&file_exists($name))
                {
                    $files[]=$name;
                    $bfound=true;
                }
                if($bfound)
                {
                    $attach_meta      = wp_get_attachment_metadata( $attachment_id );
                    if($attach_meta!=false)
                    {
                        if(isset($attach_meta['sizes']))
                        {
                            foreach ($attach_meta['sizes'] as $key=>$value)
                            {
                                $data=image_get_intermediate_size($attachment_id,$key);
                                $data['path']=ltrim($data['path'], './');
                                $name=$dir.DIRECTORY_SEPARATOR.$data['path'];
                                if(!in_array($name,$files)&&file_exists($name))
                                {
                                    $files[]=$dir.DIRECTORY_SEPARATOR.$data['path'];
                                }
                            }
                        }
                        else
                        {
                            global $wpvivid_plugin;
                            $wpvivid_plugin->wpvivid_log->WriteLog('attach_meta size not found id:'.$attachment_id,'notice');
                        }
                    }
                }
            }

        }

        $size=0;

        if(!empty($files))
        {
            foreach ($files as $file)
            {
                $size+=filesize($file);
            }
        }

        $ret['size']=$size;
        $ret['files']=$files;

        return $ret;
    }

    public function get_element_image($element_data,&$attachment_added_ids)
    {
        $element_image=array();

        if(!empty($element_data['settings']))
        {
            $settings=$element_data['settings'];
            if(isset($settings['image']))
            {
                if(!in_array($settings['image']['id'],$attachment_added_ids))
                {
                    $element_image[]=$settings['image']['id'];
                    $attachment_added_ids[]=$settings['image']['id'];
                }

            }
        }

        if(!empty($element_data['elements']))
        {
            foreach ($element_data['elements'] as $element)
            {
                $temp=$this->get_element_image($element,$attachment_added_ids);
                $element_image=array_merge($element_image,$temp);
            }
        }

        return $element_image;
    }

    public function add_new_export()
    {
        $files=$this->get_export_files();

        $backup_data=array();
        $status=WPvivid_Exporter_taskmanager::get_backup_task_status($this->task['id']);
        $backup_data['create_time']=$status['start_time'];

        global $wpvivid_plugin;
        $backup_data['log']=$wpvivid_plugin->wpvivid_log->log_file;
        $backup_data['export']=$files;
        $backup_data['id']=$this->task['id'];
        $list = get_option('wpvivid_export_list',array());
        $list[$this->task['id']]=$backup_data;
        WPvivid_Setting::update_option('wpvivid_export_list',$list);
    }
}

class WPvivid_Exporter_Item{
    private $config;

    public function __construct($options){
        $this->config=$options;
    }

    public function get_download_export_files(){
        $files = isset($this->config['export']) ? $this->config['export'] : array();
        if(empty($files)){
            $ret['result'] = WPVIVID_FAILED;
            $ret['error']='Failed to get export files.';
        }
        else{
            $ret['result'] = WPVIVID_SUCCESS;
            $ret['files']=$files;
        }
        return $ret;
    }

    public function get_download_progress($backup_id, $files){
        $this->config['local']['path'] = 'wpvividbackups';
        foreach ($files as $file){
            $need_download = false;
            $file_path     = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->config['local']['path'].DIRECTORY_SEPARATOR.$file;
            $download_url  = content_url().DIRECTORY_SEPARATOR.$this->config['local']['path'].DIRECTORY_SEPARATOR.$file;
            if(file_exists($file_path)){
                //need calc file size, then compare is need download

            }
            else{
                $need_download = true;
            }

            if($need_download){

            }
            else{
                $ret['result'] = WPVIVID_SUCCESS;
                $ret['files'][$file]['status'] = 'completed';
                $ret['files'][$file]['download_path'] = $file_path;
                $ret['files'][$file]['download_url'] = $download_url;
                ob_start();
                ?>
                <div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px;">
                    <span>Part01</span><br>
                    <span><a class="wpvivid-download-export" id="trtr" name="<?php echo $file; ?>" style="cursor: pointer;">Download</a></span><br>
                    <div style="width:100%;height:5px; background-color:#dcdcdc;">
                        <div style="background-color:#0085ba; float:left;width:100%;height:5px;"></div>
                    </div>
                    <span>size: </span><span>1K</span>
                </div>
                <?php
                $html = ob_get_clean();
                $ret['html']=$html;
            }
        }
        return $ret;
    }
}

class WPvivid_Exporter
{
    public $task;
    //public $config;

    public function __construct($task_id=false,$task=false)
    {
        if($task_id!==false)
        {
            $this->task=new WPvivid_Exporter_task($task_id);
        }
        else if($task!==false)
        {
            $this->task=new WPvivid_Exporter_task(false,$task);
        }
        else
        {
            $this->task=new WPvivid_Exporter_task();
        }
    }

    public function init_options($task_id)
    {
        $this->task=new WPvivid_Exporter_task($task_id);
    }

    public function export($task_id)
    {
        $this->init_options($task_id);

        global $wpvivid_plugin;

        $next=$this->task->get_next_posts();

        $ret['result']='success';
        WPvivid_Exporter_taskmanager::update_main_task_progress($task_id, 'export', 5, 0);
        while($next!==false)
        {
            @set_time_limit(900);
            $wpvivid_plugin->wpvivid_log->WriteLog('Prepare to export post '.$next['file_name'],'notice');

            $this->task->update_sub_task_progress($next['file_name'],0,'Start export file '.$next['file_name']);
            $ret=$this->export_post_to_xml($next['next_post_ids'], $next['attachment_ids'],$next['file_name'],$next['export_type']);
            $wpvivid_plugin->wpvivid_log->WriteLog('Finished to export post '.$next['file_name'],'notice');
            if($ret['result']=='success')
            {
                $wpvivid_plugin->wpvivid_log->WriteLog('Prepare to zip file '.$next['file_name'],'notice');
                $next['json_info']['posts_count']=sizeof($next['next_post_ids']);
                $ret=$this->zip_media_files($ret['xml_file_name'],$next['files'],$next['file_name'],$next['export_type'],$next['json_info']);
                $wpvivid_plugin->wpvivid_log->WriteLog('Finished to zip file '.$next['file_name'],'notice');
                if($ret['result']!='success')
                {
                    $wpvivid_plugin->wpvivid_log->WriteLog('Failed to zip post '.$next['file_name'].' '.json_encode($ret),'notice');
                    return $ret;
                }
                $this->task->update_sub_task_progress($next['file_name'],1,'Backing up '.$next['file_name'].' finished');
                $this->task->update_finished_posts($next);
                $this->task->update_export_files($ret['file_data']);
            }
            else
            {
                $wpvivid_plugin->wpvivid_log->WriteLog('Failed to export post '.$next['file_name'].' '.json_encode($ret),'notice');
                return $ret;
            }
            $next=$this->task->get_next_posts();
        }
        WPvivid_Exporter_taskmanager::update_main_task_progress($task_id, 'export', 100, 1);

        return $ret;
    }

    public function export_post_to_xml($posts_ids,$attachment_ids,$file_name,$export_type)
    {
        $all_ids=array_merge($posts_ids,$attachment_ids);
        //$xml_file_name=$file_name.'.xml';
        $xml_file_name=$file_name.'_'.$export_type.'.xml';
        //$files=array();
        $export_folder = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR;
        if(!file_exists($export_folder)) {
            @mkdir($export_folder);
        }
        $path=$export_folder.DIRECTORY_SEPARATOR.$xml_file_name;
        $ret['xml_file_name']=$path;
        if(file_exists($path))
        {
            @unlink($path);
        }

        $this->write_header_to_file($path);

        $this->write_authors_list_to_file($path,$all_ids);

        $this->write_cat_to_file($path,$posts_ids);

        global $wp_query,$wpdb;

        // Fake being in the loop.
        $wp_query->in_the_loop = true;

        $task_id = $this->task->get_id();
        while ( $next_posts = array_splice( $posts_ids, 0, 20 ) )
        {
            $where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
            $posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );
            // Begin Loop.
            foreach ( $posts as $post )
            {
                $this->write_post_to_file($path,$post);
            }
        }
        WPvivid_Exporter_taskmanager::update_main_task_progress($task_id, 'export', 25, 0);
        while ( $next_posts = array_splice( $attachment_ids, 0, 20 ) )
        {
            $where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
            $posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );
            // Begin Loop.
            foreach ( $posts as $post )
            {
                $this->write_media_post_to_file($path,$post);
                //$post_files=$this->write_media_post_to_file($path,$post);
                //$files=array_merge($post_files,$files);
            }
        }
        WPvivid_Exporter_taskmanager::update_main_task_progress($task_id, 'export', 50, 0);
        $this->write_footer_to_file($path);

        //$ret['files']=$files;
        $ret['result']='success';
        return $ret;
    }

    private function zip_media_files($xml_file,$files,$file_name,$export_type,$json_info=false)
    {
        if (!class_exists('PclZip'))
            include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
        $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR.DIRECTORY_SEPARATOR.$file_name.'_export_'.$export_type.'.zip';
        $options['compress']['no_compress']=1;
        $options['compress']['use_temp_file']=1;
        $options['compress']['use_temp_size']=16;
        $options['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;

        if(file_exists($path))
            @unlink($path);
        $archive = new PclZip($path);

        if($json_info!==false) {
            $temp_path = dirname($path).DIRECTORY_SEPARATOR.'wpvivid_export_package_info.json';
            if(file_exists($temp_path)) {
                @unlink($temp_path);
            }
            $json_info['create_time']=time();
            $json_info['xml_file']=basename($xml_file);
            $json_info['media_size']=0;
            foreach ($files as $file)
            {
                $json_info['media_size']+=@filesize($file);
            }
            file_put_contents($temp_path,print_r(json_encode($json_info),true));
            $archive -> add($temp_path,PCLZIP_OPT_REMOVE_PATH,dirname($temp_path));
            @unlink($temp_path);
        }

        $ret =$archive -> add($xml_file,PCLZIP_OPT_REMOVE_PATH,dirname($xml_file));
        @unlink($xml_file);
        if(!$ret)
        {
            return array('result'=>WPVIVID_FAILED,'error'=>$archive->errorInfo(true));
        }

        if(!empty($files)) {
            $ret = $archive->add($files, PCLZIP_OPT_REMOVE_PATH, WP_CONTENT_DIR, PCLZIP_OPT_TEMP_FILE_THRESHOLD, 16);
        }

        if(!$ret)
        {
            return array('result'=>WPVIVID_FAILED,'error'=>$archive->errorInfo(true));
        }

        $file_data = array();
        $file_data['file_name'] = basename($path);
        $file_data['size'] = filesize($path);

        return array('result'=>WPVIVID_SUCCESS,'file_data'=>$file_data);
    }

    public function write_header_to_file($file)
    {
        $wxr_version=1.2;

        $line='<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . "\" ?>\n";
        $line.='<!-- This is a WordPress eXtended RSS file generated by WordPress as an export of your site. -->
<!-- It contains information about your site\'s posts, pages, comments, categories, and other content. -->
<!-- You may use this file to transfer that content from one site to another. -->
<!-- This file is not intended to serve as a complete backup of your site. -->

<!-- To import this information into a WordPress site follow these steps: -->
<!-- 1. Log in to that site as an administrator. -->
<!-- 2. Go to Tools: Import in the WordPress admin panel. -->
<!-- 3. Install the "WordPress" importer from the list. -->
<!-- 4. Activate & Run Importer. -->
<!-- 5. Upload this file using the form provided on that page. -->
<!-- 6. You will first be asked to map the authors in this export file to users -->
<!--    on the site. For each author, you may choose to map to an -->
<!--    existing user on the site or to create a new user. -->
<!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->
<!--    contained in this file into your site. -->';
        $line.=apply_filters( 'the_generator', get_the_generator( 'export' ),  'export' ) . "\n";
        $line.='
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/'.$wxr_version.'/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/'.$wxr_version.'/"
>      ';
        $line.='
<channel>
    <title>'.apply_filters( 'bloginfo_rss', get_bloginfo_rss( 'name' ), 'name' ).'</title>
    <link>'.apply_filters( 'bloginfo_rss', get_bloginfo_rss( 'url' ), 'url' ).'</link>
    <description>'.apply_filters( 'bloginfo_rss', get_bloginfo_rss( 'description' ), 'description' ).'</description>
    <pubDate>'.date( 'D, d M Y H:i:s +0000' ).'</pubDate>
    <language>'.apply_filters( 'bloginfo_rss', get_bloginfo_rss( 'language' ), 'language' ).'</language>
    <wp:wxr_version>'.$wxr_version.'</wp:wxr_version>
    <wp:base_site_url>'.$this->wxr_site_url().'</wp:base_site_url>
    <wp:base_blog_url>'.apply_filters( 'bloginfo_rss', get_bloginfo_rss( 'url' ), 'url' ).'</wp:base_blog_url>
    ';
        file_put_contents($file,$line);
    }

    public function write_authors_list_to_file($file,$post_ids)
    {
        $line=$this->wxr_authors_list( $post_ids );
        file_put_contents($file,$line,FILE_APPEND);
    }

    public function write_footer_to_file($file)
    {
        $line='
</channel>
</rss> ';
        file_put_contents($file,$line,FILE_APPEND);
    }

    public function write_post_header_to_file($file,$post)
    {
        $is_sticky = is_sticky( $post->ID ) ? 1 : 0;
        $post = get_post( $post );

        $guid = isset( $post->guid ) ? get_the_guid( $post ) : '';
        $id   = isset( $post->ID ) ? $post->ID : 0;

        $guid= apply_filters( 'the_guid', $guid, $id );
        $item_header_line='
        <item>
            <title>
                '.apply_filters( 'the_title_rss', $post->post_title ).'
            </title>
            <link>'.esc_url( apply_filters( 'the_permalink_rss', get_permalink($post->ID) ) ).'</link>
            <pubDate>'.mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true,$post ), false ).'</pubDate>
            <dc:creator>'.$this->wxr_cdata( get_the_author_meta( 'login' ) ).'</dc:creator>
            <guid isPermaLink="false">'.$guid.'</guid>
		    <description></description>
		    <content:encoded>'.$this->wxr_cdata( apply_filters( 'the_content_export', $post->post_content ) ).' </content:encoded>
		    <excerpt:encoded>'.$this->wxr_cdata( apply_filters( 'the_excerpt_export', $post->post_excerpt ) ).'</excerpt:encoded>
		    <wp:post_id>'.intval( $post->ID ).'</wp:post_id>
		    <wp:post_date>'.$this->wxr_cdata( $post->post_date ).'</wp:post_date>
		    <wp:post_date_gmt>'.$this->wxr_cdata( $post->post_date_gmt ).'</wp:post_date_gmt>
		    <wp:comment_status>'.$this->wxr_cdata( $post->comment_status ).'</wp:comment_status>
		    <wp:ping_status>'.$this->wxr_cdata( $post->ping_status ).'</wp:ping_status>
		    <wp:post_name>'.$this->wxr_cdata( $post->post_name ).'</wp:post_name>
		    <wp:status>'.$this->wxr_cdata( $post->post_status ).'</wp:status>
		    <wp:post_parent>'.intval( $post->post_parent ).'</wp:post_parent>
		    <wp:menu_order>'.intval( $post->menu_order ).'</wp:menu_order>
		    <wp:post_type>'.$this->wxr_cdata( $post->post_type ).'</wp:post_type>
		    <wp:post_password>'.$this->wxr_cdata( $post->post_password ).'</wp:post_password>
		    <wp:is_sticky>'.intval( $is_sticky ).'</wp:is_sticky>
		    ';
        if ( $post->post_type == 'attachment' )
            $item_header_line.='<wp:attachment_url>'.$this->wxr_cdata( wp_get_attachment_url( $post->ID ) ).'</wp:attachment_url>';
        file_put_contents($file,$item_header_line,FILE_APPEND);

        $line=$this->wxr_post_taxonomy($post);
        file_put_contents($file,$line,FILE_APPEND);
    }

    public function write_media_post_to_file($file,$post)
    {
        global $wpdb;

        $postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );
        $post_meta_line='';
        $added_meta_key=array();
        foreach ( $postmeta as $meta )
        {
            if(in_array($meta->meta_key,$added_meta_key))
                continue;
            $added_meta_key[]=$meta->meta_key;

            $post_meta_line.='
                <wp:postmeta>
                <wp:meta_key>'.$this->wxr_cdata( $meta->meta_key ).'</wp:meta_key>
		        <wp:meta_value>'.$this->wxr_cdata( $meta->meta_value ).'</wp:meta_value>
		        </wp:postmeta>';
        }

        $this->write_post_header_to_file($file,$post);

        file_put_contents($file,$post_meta_line,FILE_APPEND);

        $_comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID ) );
        $comments  = array_map( 'get_comment', $_comments );
        $line='';
        foreach ( $comments as $c )
        {
            $line.='
                    <wp:comment>
                    <wp:comment_id>'.intval( $c->comment_ID ).'</wp:comment_id>
                    <wp:comment_author>'.$this->wxr_cdata( $c->comment_author ).'</wp:comment_author>
                    <wp:comment_author_email>'.$this->wxr_cdata( $c->comment_author_email ).'</wp:comment_author_email>
			        <wp:comment_author_url>'.esc_url_raw( $c->comment_author_url ).'</wp:comment_author_url>
			        <wp:comment_author_IP>'.$this->wxr_cdata( $c->comment_author_IP ).'</wp:comment_author_IP>
			        <wp:comment_date>'.$this->wxr_cdata( $c->comment_date ).'</wp:comment_date>
			        <wp:comment_date_gmt>'.$this->wxr_cdata( $c->comment_date_gmt ).'</wp:comment_date_gmt>
			        <wp:comment_content>'.$this->wxr_cdata( $c->comment_content ).'</wp:comment_content>
			        <wp:comment_approved>'.$this->wxr_cdata( $c->comment_approved ).'</wp:comment_approved>
			        <wp:comment_type>'.$this->wxr_cdata( $c->comment_type ).'</wp:comment_type>
			        <wp:comment_parent>'.intval( $c->comment_parent ).'</wp:comment_parent>
			        <wp:comment_user_id>'.intval( $c->user_id ).'</wp:comment_user_id>';
            $c_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID ) );
            foreach ( $c_meta as $meta )
            {
                if ( apply_filters( 'wxr_export_skip_commentmeta', false, $meta->meta_key, $meta ) )
                {
                    continue;
                }
                $line.='
                        <wp:commentmeta>
                            <wp:meta_key>'.$this->wxr_cdata( $meta->meta_key ).'</wp:meta_key>
			                <wp:meta_value>'.$this->wxr_cdata( $meta->meta_value ).'</wp:meta_value>
			            </wp:commentmeta>';
            }
            $line.='
                    </wp:comment>';
        }
        file_put_contents($file,$line,FILE_APPEND);
        $line='
        </item>';
        file_put_contents($file,$line,FILE_APPEND);
    }

    public function write_post_to_file($file,$post)
    {
        global $wpdb;

        setup_postdata( $post );

        $postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );
        $post_meta_line='';

        $added_meta_key=array();
        foreach ( $postmeta as $meta )
        {
            //if ( apply_filters( 'wxr_export_skip_postmeta', false, $meta->meta_key, $meta ) ) {
            //    continue;
            //}
            if(in_array($meta->meta_key,$added_meta_key))
                continue;
            $added_meta_key[]=$meta->meta_key;
            $post_meta_line.='
                <wp:postmeta>
                <wp:meta_key>'.$this->wxr_cdata( $meta->meta_key ).'</wp:meta_key>
		        <wp:meta_value>'.$this->wxr_cdata( $meta->meta_value ).'</wp:meta_value>
		        </wp:postmeta>';
        }

        $this->write_post_header_to_file($file,$post);

        file_put_contents($file,$post_meta_line,FILE_APPEND);

        $_comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID ) );
        $comments  = array_map( 'get_comment', $_comments );
        $line='';
        foreach ( $comments as $c )
        {
            $line.='
                    <wp:comment>
                    <wp:comment_id>'.intval( $c->comment_ID ).'</wp:comment_id>
                    <wp:comment_author>'.$this->wxr_cdata( $c->comment_author ).'</wp:comment_author>
                    <wp:comment_author_email>'.$this->wxr_cdata( $c->comment_author_email ).'</wp:comment_author_email>
			        <wp:comment_author_url>'.esc_url_raw( $c->comment_author_url ).'</wp:comment_author_url>
			        <wp:comment_author_IP>'.$this->wxr_cdata( $c->comment_author_IP ).'</wp:comment_author_IP>
			        <wp:comment_date>'.$this->wxr_cdata( $c->comment_date ).'</wp:comment_date>
			        <wp:comment_date_gmt>'.$this->wxr_cdata( $c->comment_date_gmt ).'</wp:comment_date_gmt>
			        <wp:comment_content>'.$this->wxr_cdata( $c->comment_content ).'</wp:comment_content>
			        <wp:comment_approved>'.$this->wxr_cdata( $c->comment_approved ).'</wp:comment_approved>
			        <wp:comment_type>'.$this->wxr_cdata( $c->comment_type ).'</wp:comment_type>
			        <wp:comment_parent>'.intval( $c->comment_parent ).'</wp:comment_parent>
			        <wp:comment_user_id>'.intval( $c->user_id ).'</wp:comment_user_id>';
            $c_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID ) );
            foreach ( $c_meta as $meta )
            {
                if ( apply_filters( 'wxr_export_skip_commentmeta', false, $meta->meta_key, $meta ) )
                {
                    continue;
                }
                $line.='
                        <wp:commentmeta>
                            <wp:meta_key>'.$this->wxr_cdata( $meta->meta_key ).'</wp:meta_key>
			                <wp:meta_value>'.$this->wxr_cdata( $meta->meta_value ).'</wp:meta_value>
			            </wp:commentmeta>';
            }
            $line.='
                    </wp:comment>';
        }
        file_put_contents($file,$line,FILE_APPEND);
        $line='
        </item>';
        file_put_contents($file,$line,FILE_APPEND);
        return true;
    }

    public function write_cat_to_file($file,$post_ids)
    {
        $cats = $tags = $terms = array();

        $categories = (array) get_categories( array( 'object_ids' => $post_ids ) );
        $tags       = (array) get_tags( array( 'object_ids' => $post_ids ) );

        $custom_taxonomies = get_taxonomies( array( '_builtin' => false ) );
        $custom_terms      = (array) get_terms( $custom_taxonomies,array( 'object_ids' => $post_ids ) );

        // Put categories in order with no child going before its parent.
        while ( $cat = array_shift( $categories ) ) {
            if ( $cat->parent == 0 || isset( $cats[ $cat->parent ] ) ) {
                $cats[ $cat->term_id ] = $cat;
            } else {
                $categories[] = $cat;
            }
        }

        // Put terms in order with no child going before its parent.
        while ( $t = array_shift( $custom_terms ) ) {
            if ( $t->parent == 0 || isset( $terms[ $t->parent ] ) ) {
                $terms[ $t->term_id ] = $t;
            } else {
                $custom_terms[] = $t;
            }
        }

        unset( $categories, $custom_taxonomies, $custom_terms );

        $line='';
        foreach ($cats as $c)
        {
            $line.='<wp:category>
            <wp:term_id>'.intval( $c->term_id ).'</wp:term_id>
            <wp:category_nicename>'.$this->wxr_cdata( $c->slug ).'</wp:category_nicename>
            <wp:category_parent>'.$this->wxr_cdata( $c->parent ? $cats[ $c->parent ]->slug : '' ).'</wp:category_parent>
            '.$this->wxr_cat_name( $c ).'
            '.$this->wxr_category_description( $c ).'
            '.$this->wxr_term_meta( $c ).'
            </wp:category>';
        }
        file_put_contents($file,$line,FILE_APPEND);
        $line='';
        foreach ( $tags as $t )
        {
            $line.='<wp:tag>
            <wp:term_id>'.intval( $t->term_id ).'</wp:term_id>
            <wp:tag_slug>'.$this->wxr_cdata( $t->slug ).'</wp:tag_slug>
            '.$this->wxr_tag_name( $t ).'
            '.$this->wxr_tag_description( $t ).'
            '.$this->wxr_term_meta( $t ).'
            </wp:tag>';
        }
        file_put_contents($file,$line,FILE_APPEND);

        $line='';

        foreach ( $terms as $t)
        {
            $line.='<wp:term>
            <wp:term_id>'.$this->wxr_cdata( $t->term_id ).'</wp:term_id>
            <wp:term_taxonomy>'.$this->wxr_cdata( $t->taxonomy ).'</wp:term_taxonomy>
            <wp:term_slug>'.$this->wxr_cdata( $t->slug ).'</wp:term_slug>
            <wp:term_parent>'.$this->wxr_cdata( $t->parent ? $terms[ $t->parent ]->slug : '' ).'</wp:term_parent>          
            '.$this->wxr_term_name( $t ).'
            '.$this->wxr_term_description( $t ).'
            '.$this->wxr_term_meta( $t ).'          
        </wp:term>';
        }
    }

    private function wxr_cat_name( $category )
    {
        if ( empty( $category->name ) )
        {
            return '';
        }

        return '<wp:cat_name>' . $this->wxr_cdata( $category->name ) . "</wp:cat_name>";
    }

    private function wxr_category_description( $category ) {
        if ( empty( $category->description ) ) {
            return '<wp:category_description></wp:category_description>\n';
        }

        return '<wp:category_description>' . $this->wxr_cdata( $category->description ) . "</wp:category_description>";
    }

    private function wxr_tag_name( $tag ) {
        if ( empty( $tag->name ) ) {
            return '';
        }

        return '<wp:tag_name>' . $this->wxr_cdata( $tag->name ) . "</wp:tag_name>";
    }

    private function wxr_tag_description( $tag ) {
        if ( empty( $tag->description ) ) {
            return '';
        }

        return '<wp:tag_description>' . $this->wxr_cdata( $tag->description ) . "</wp:tag_description>";
    }

    private function wxr_term_name( $term ) {
        if ( empty( $term->name ) ) {
            return '';
        }

        return '<wp:term_name>' . $this->wxr_cdata( $term->name ) . "</wp:term_name>";
    }

    private function wxr_term_description( $term ) {
        if ( empty( $term->description ) ) {
            return '';
        }

        return "\t\t<wp:term_description>" . $this->wxr_cdata( $term->description ) . "</wp:term_description>";
    }

    private function wxr_term_meta( $term ) {
        global $wpdb;

        $termmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE term_id = %d", $term->term_id ) );

        $line='';
        foreach ( $termmeta as $meta )
        {
            /**
             * Filters whether to selectively skip term meta used for WXR exports.
             *
             * Returning a truthy value to the filter will skip the current meta
             * object from being exported.
             *
             * @since 4.6.0
             *
             * @param bool   $skip     Whether to skip the current piece of term meta. Default false.
             * @param string $meta_key Current meta key.
             * @param object $meta     Current meta object.
             */
            if ( ! apply_filters( 'wxr_export_skip_termmeta', false, $meta->meta_key, $meta ) )
            {
                $line.="\t\t<wp:termmeta>\n\t\t\t<wp:meta_key>".$this->wxr_cdata( $meta->meta_key )."</wp:meta_key>\n\t\t\t<wp:meta_value>".$this->wxr_cdata( $meta->meta_value )."</wp:meta_value>\n\t\t</wp:termmeta>\n";
            }
        }
        return $line;
    }

    private function wxr_cdata( $str )
    {
        if ( ! seems_utf8( $str ) ) {
            $str = utf8_encode( $str );
        }
        // $str = ent2ncr(esc_html($str));
        $str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

        return $str;
    }

    private function wxr_site_url() {
        if ( is_multisite() ) {
            // Multisite: the base URL.
            return network_home_url();
        } else {
            // WordPress (single site): the blog URL.
            return get_bloginfo_rss( 'url' );
        }
    }

    private function wxr_authors_list( array $post_ids = null )
    {
        global $wpdb;

        if ( ! empty( $post_ids ) ) {
            $post_ids = array_map( 'absint', $post_ids );
            $and      = 'AND ID IN ( ' . implode( ', ', $post_ids ) . ')';
        } else {
            $and = '';
        }

        $authors = array();
        $results = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft' $and" );
        foreach ( (array) $results as $result )
        {
            $authors[] = get_userdata( $result->post_author );
        }

        $authors = array_filter( $authors );

        $line='';
        foreach ( $authors as $author )
        {
            $line.= "\t<wp:author>";
            $line.= '<wp:author_id>' . intval( $author->ID ) . '</wp:author_id>';
            $line.= '<wp:author_login>' . $this->wxr_cdata( $author->user_login ) . '</wp:author_login>';
            $line.= '<wp:author_email>' . $this->wxr_cdata( $author->user_email ) . '</wp:author_email>';
            $line.= '<wp:author_display_name>' . $this->wxr_cdata( $author->display_name ) . '</wp:author_display_name>';
            $line.= '<wp:author_first_name>' . $this->wxr_cdata( $author->first_name ) . '</wp:author_first_name>';
            $line.= '<wp:author_last_name>' . $this->wxr_cdata( $author->last_name ) . '</wp:author_last_name>';
            $line.= "</wp:author>\n";
        }
        return $line;
    }

    private function wxr_post_taxonomy($post)
    {
        $taxonomies = get_object_taxonomies( $post->post_type );
        if ( empty( $taxonomies ) ) {
            return;
        }
        $terms = wp_get_object_terms( $post->ID, $taxonomies );
        $line='';
        foreach ( (array) $terms as $term )
        {
            $line.= "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . $this->wxr_cdata( $term->name ) . "</category>\n";
        }
        return $line;
    }
}

