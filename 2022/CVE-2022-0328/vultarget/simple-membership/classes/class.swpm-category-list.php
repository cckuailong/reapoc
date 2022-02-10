<?php

/**
 * BCategoryList
 *
 * @author nur
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SwpmCategoryList extends WP_List_Table {

    public $selected_level_id = 1;
    public $category;

    function __construct() {
        parent::__construct(array(
            'singular' => SwpmUtils::_('Membership Level'),
            'plural' => SwpmUtils::_('Membership Levels'),
            'ajax' => false
        ));
        $selected = filter_input(INPUT_POST, 'membership_level_id');
        $this->selected_level_id = empty($selected) ? 1 : $selected;
        $this->category = ($this->selected_level_id == 1) ?
                SwpmProtection::get_instance() :
                SwpmPermission::get_instance($this->selected_level_id);
    }

    function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />'
            , 'term_id' => SwpmUtils::_('Category ID')
            , 'name' => SwpmUtils::_('Category Name')
            , 'taxonomy' => SwpmUtils::_('Category Type (Taxonomy)')
            , 'description' => SwpmUtils::_('Description')
            , 'count' => SwpmUtils::_('Count')
        );
    }

    function get_sortable_columns() {
        return array();
    }

    function column_default($item, $column_name) {
        return stripslashes($item->$column_name);
    }

    function column_term_id($item) {
        return $item->term_id;
    }

    function column_taxonomy($item) {
        $taxonomy = $item->taxonomy;
        if ($taxonomy == 'category'){
            $taxonomy = 'Post Category';
        } else {
            $taxonomy = 'Custom Post Type ('.$taxonomy.')';
        }
        return $taxonomy;
    }
    
    function column_cb($item) {
        return sprintf(
                '<input type="hidden" name="ids_in_page[]" value="%s">
            <input type="checkbox" %s name="ids[]" value="%s" />', $item->term_id, $this->category->in_categories($item->term_id) ? "checked" : "", $item->term_id
        );
    }

    public static function update_category_list() {
        //Check we are on the admin end and user has management permission 
        SwpmMiscUtils::check_user_permission_and_is_admin('category protection update');
        
        //Check nonce
        $swpm_category_prot_update_nonce = filter_input(INPUT_POST, 'swpm_category_prot_update_nonce');
        if (!wp_verify_nonce($swpm_category_prot_update_nonce, 'swpm_category_prot_update_nonce_action')) {
            //Nonce check failed.
            wp_die(SwpmUtils::_("Error! Nonce security verification failed for Category Protection Update action. Clear cache and try again."));
        }
            
        $selected = filter_input(INPUT_POST, 'membership_level_id');
        $selected_level_id = empty($selected) ? 1 : $selected;
        $category = ($selected_level_id == 1) ?
                SwpmProtection::get_instance() :
                SwpmPermission::get_instance($selected_level_id);
        $args = array('ids' => array(
                'filter' => FILTER_VALIDATE_INT,
                'flags' => FILTER_REQUIRE_ARRAY,
        ));
        $filtered = filter_input_array(INPUT_POST, $args);
        $ids = $filtered['ids'];
        $args = array('ids_in_page' => array(
                'filter' => FILTER_VALIDATE_INT,
                'flags' => FILTER_REQUIRE_ARRAY,
        ));
        $filtered = filter_input_array(INPUT_POST, $args);
        $ids_in_page = $filtered['ids_in_page'];
        $category->remove($ids_in_page, 'category')->apply($ids, 'category')->save();
        $message = array('succeeded' => true, 'message' => '<p class="swpm-green-box">' . SwpmUtils::_('Category protection updated!') . '</p>');
        SwpmTransfer::get_instance()->set('status', $message);
    }

    function prepare_items() {
        $all_categories = array();
        $taxonomies = get_taxonomies($args = array('public' => true,'_builtin'=>false));
        $taxonomies['category'] = 'category';
        $all_terms = get_terms( $taxonomies, 'orderby=count&hide_empty=0&order=DESC');        
        $totalitems = count($all_terms);
        $perpage = 100;
        $paged = !empty($_GET["paged"]) ? sanitize_text_field($_GET["paged"]) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);
        $offset = 0;
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
        }
        for ($i = $offset; $i < ((int) $offset + (int) $perpage) && !empty($all_terms[$i]); $i++) {
            $all_categories[] = $all_terms[$i];
        }
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $all_categories;
    }

    function no_items() {
        SwpmUtils::e('No category found.');
    }

}
