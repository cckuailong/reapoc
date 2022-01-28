<?php

namespace ProfilePress\Core\Admin\SettingsPages;

use ProfilePress\Core\Base;
use ProfilePress\Core\Classes\ExtensionManager;
use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Core\Themes\Shortcode\ThemesRepository as ShortcodeThemesRepository;
use ProfilePress\Core\Themes\DragDrop\ThemesRepository as DragDropThemesRepository;

if ( ! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class FormList extends \WP_List_Table
{
    private $table;

    /** @var \wpdb */
    private $wpdb;

    public function __construct($wpdb)
    {
        $this->wpdb  = $wpdb;
        $this->table = Base::form_db_table();
        parent::__construct(array(
                'singular' => esc_html__('form', 'wp-user-avatar'), //singular name of the listed records
                'plural'   => esc_html__('forms', 'wp-user-avatar'), //plural name of the listed records
                'ajax'     => false //does this table support ajax?
            )
        );
    }

    public function get_forms($per_page, $current_page = 1, $form_type = '')
    {
        $per_page     = absint($per_page);
        $current_page = absint($current_page);
        $form_type    = ! empty($form_type) ? sanitize_text_field($form_type) : FR::LOGIN_TYPE;

        $offset = ($current_page - 1) * $per_page;
        $sql    = "SELECT * FROM $this->table";
        $args   = [];

        $sql    .= " WHERE form_type = %s";
        $args[] = $form_type;

        $sql .= " ORDER BY date DESC";

        $args[] = $per_page;

        $sql .= " LIMIT %d";
        if ($current_page > 1) {
            $args[] = $offset;
            $sql    .= "  OFFSET %d";
        }

        $result = $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $args),
            'ARRAY_A'
        );

        if ( ! ExtensionManager::is_premium()) {
            $shortcode_premium_theme_classes = wp_list_pluck(ShortcodeThemesRepository::premiumThemes(), 'theme_class');
            $dnd_premium_theme_classes       = wp_list_pluck(DragDropThemesRepository::premiumThemes(), 'theme_class');

            $filtered = [];
            foreach ($result as $form) {

                $builder_type = $form['builder_type'];
                $form_type    = $form['form_type'];
                $form_id      = $form['form_id'];

                $form_class = FR::get_form_class($form_id, $form_type);

                if ($builder_type == FR::SHORTCODE_BUILDER_TYPE &&
                    ! in_array($form_class, $shortcode_premium_theme_classes)
                ) {
                    $filtered[] = $form;
                }

                if ($builder_type == FR::DRAG_DROP_BUILDER_TYPE &&
                    ! in_array($form_class, $dnd_premium_theme_classes)
                ) {
                    $filtered[] = $form;
                }

            }

            $result = $filtered;
        }

        return $result;
    }

    /**
     * Returns the count of records in the database.
     *
     * @param string $form_type
     *
     * @return null|string
     */
    public function record_count($form_type = '')
    {
        $form_type = ! empty($form_type) ? sanitize_text_field($form_type) : FR::LOGIN_TYPE;

        $sql = "SELECT COUNT(*) FROM $this->table";
        if ( ! empty($form_type)) {
            $form_type = esc_sql($form_type);
            $sql       .= "  WHERE form_type = '$form_type'";
        }

        return $this->wpdb->get_var($sql);
    }

    public static function customize_url($form_id, $form_type, $builder_type = FR::SHORTCODE_BUILDER_TYPE)
    {
        $slug = $form_type == FR::MEMBERS_DIRECTORY_TYPE ? PPRESS_MEMBER_DIRECTORIES_SLUG : PPRESS_FORMS_SETTINGS_SLUG;

        $url = admin_url(
            sprintf(
                'admin.php?page=%s&view=edit-shortcode-%s&id=%d',
                $slug,
                $form_type,
                $form_id
            )
        );

        if ($builder_type == FR::DRAG_DROP_BUILDER_TYPE) {

            $url = admin_url(
                sprintf(
                    'admin.php?page=%s&view=drag-drop-builder&form-type=%s&id=%d',
                    $slug,
                    $form_type,
                    $form_id
                )
            );
        }

        return $url;
    }

    public static function delete_url($form_id, $form_type)
    {
        return admin_url(
            sprintf(
                'admin.php?page=pp-forms&action=delete&form_type=%s&id=%d&_wpnonce=%s',
                $form_type,
                $form_id,
                ppress_create_nonce()
            )
        );
    }

    public static function clone_url($form_id, $form_type)
    {
        return admin_url(
            sprintf(
                'admin.php?page=pp-forms&action=clone&form_type=%s&id=%d&_wpnonce=%s',
                $form_type,
                $form_id,
                ppress_create_nonce()
            )
        );
    }

    public static function preview_url($form_id, $form_type)
    {
        return add_query_arg(['pp_preview_form' => $form_id, 'type' => $form_type], home_url());
    }

    /**
     * Text displayed when no email optin form is available
     */
    public function no_items()
    {
        printf(
            esc_html__('No form is currently available. %sConsider creating one%s', 'wp-user-avatar'),
            '<a href="' . add_query_arg('view', 'add-new-form', PPRESS_FORMS_SETTINGS_PAGE) . '">',
            '</a>'
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'title'     => esc_html__('Title', 'wp-user-avatar'),
            'shortcode' => esc_html__('Shortcode', 'wp-user-avatar'),
            'builder'   => esc_html__('Builder Type', 'wp-user-avatar'),
            'date'      => esc_html__('Date', 'wp-user-avatar')
        );

        return $columns;
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="form_id[]" value="%s" />', $item['form_id']
        );
    }

    /**
     * Method for Title column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_title($item)
    {
        $form_id       = absint($item['form_id']);
        $form_type     = sanitize_text_field($item['form_type']);
        $builder_type  = sanitize_text_field($item['builder_type']);
        $customize_url = self::customize_url($form_id, $form_type, $builder_type);
        $delete_url    = self::delete_url($form_id, $form_type);
        $clone_url     = self::clone_url($form_id, $form_type);
        $preview_url   = self::preview_url($form_id, $form_type);

        $actions = array(
            'delete'       => sprintf("<a class='pp-form-delete' href='%s'>%s</a>", $delete_url, esc_attr__('Delete', 'wp-user-avatar')),
            'clone'        => sprintf("<a href='%s'>%s</a>", $clone_url, esc_attr__('Duplicate', 'wp-user-avatar')),
            // using form prefix cos there is a preview admin class that floats element to the right
            'form-preview' => sprintf("<a target='_blank' href='%s'>%s</a>", $preview_url, esc_attr__('Preview', 'wp-user-avatar'))
        );

        $name = '<strong><a href="' . $customize_url . '">' . $item['name'] . '</a></strong>';


        return $name . $this->row_actions($actions);
    }

    /**
     * Method for Shortcode column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_shortcode($item)
    {
        $form_type = sanitize_text_field($item['form_type']);

        $shortcode = sprintf('[profilepress-%s id="%s"]', $form_type, absint($item['form_id']));

        $output = '<input type="text" onfocus="this.select();" readonly="readonly" value="' . esc_attr($shortcode) . '" class="shortcode-in-list-table" />';

        return $output;
    }

    public function column_builder($item)
    {
        $builder_type = esc_html__('Shortcode', 'wp-user-avatar');
        if ($item['builder_type'] == FR::DRAG_DROP_BUILDER_TYPE) {
            $builder_type = esc_html__('Drag & Drop', 'wp-user-avatar');
        }

        return $builder_type;
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'date' :
                $value = mysql2date('F j, Y', $item['date']);
                break;
            default:
                $value = $item[$column_name];
                break;
        }

        return apply_filters('ppress_forms_table_column', $value, $item, $column_name);
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = array(
            'bulk-delete' => esc_html__('Delete', 'wp-user-avatar'),
        );

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     *
     * @param string $form_type
     */
    public function prepare_items($form_type = '')
    {
        if (isset($_GET['page']) && $_GET['page'] == PPRESS_FORMS_SETTINGS_SLUG && ! empty($_GET['form-type'])) {
            $form_type = sanitize_text_field($_GET['form-type']);
        }

        $this->_column_headers = $this->get_column_info();
        /** Process bulk action */
        $this->process_actions();
        $per_page     = $this->get_items_per_page('forms_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count($form_type);
        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = $this->get_forms($per_page, $current_page, $form_type);
    }

    public function process_actions()
    {
        // Bail if user is not an admin or without admin privileges.
        if ( ! current_user_can('manage_options')) return;

        if ('delete' === $this->current_action()) {

            if ( ! ppress_verify_nonce()) wp_nonce_ays(ppress_nonce_action_string());

            $form_id   = absint($_GET['id']);
            $form_type = sanitize_text_field($_GET['form_type']);

            FR::delete_form($form_id, $form_type);

            $url = PPRESS_FORMS_SETTINGS_PAGE;

            if (isset($_GET['form_type'])) {

                $url = add_query_arg('form-type', sanitize_text_field($_GET['form_type']), $url);

                if (FR::MEMBERS_DIRECTORY_TYPE == $_GET['form_type']) {
                    $url = PPRESS_MEMBER_DIRECTORIES_SETTINGS_PAGE;
                }
            }


            wp_safe_redirect($url);
            exit;

        }

        if ('clone' === $this->current_action()) {

            if ( ! ppress_verify_nonce()) wp_nonce_ays(ppress_nonce_action_string());

            $form_id   = absint($_GET['id']);
            $form_type = sanitize_text_field($_GET['form_type']);

            FR::clone_form($form_id, $form_type);

            $url = PPRESS_FORMS_SETTINGS_PAGE;

            if (isset($_GET['form_type'])) {
                $url = add_query_arg('form-type', sanitize_text_field($_GET['form_type']), $url);

                if (FR::MEMBERS_DIRECTORY_TYPE == $_GET['form_type']) {
                    $url = PPRESS_MEMBER_DIRECTORIES_SETTINGS_PAGE;
                }
            }

            wp_safe_redirect($url);
            exit;
        }

        // Detect when a bulk action is being triggered...
        if ('bulk-delete' == $this->current_action()) {
            check_admin_referer('bulk-forms');
            $form_ids = $_POST['form_id'];

            foreach ($form_ids as $form_id) {
                $form_id   = absint($form_id);
                $form_type = ! empty($_GET['form-type']) ? sanitize_text_field($_GET['form-type']) : FR::LOGIN_TYPE;
                if (isset($_GET['page']) && $_GET['page'] == PPRESS_MEMBER_DIRECTORIES_SLUG) {
                    $form_type = FR::MEMBERS_DIRECTORY_TYPE;
                }

                FR::delete_form($form_id, $form_type);
            }
        }
    }

    /**
     * @return FormList
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new static($GLOBALS['wpdb']);
        }

        return $instance;
    }
}