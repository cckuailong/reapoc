<?php

namespace ProfilePress\Core\Themes\DragDrop;

use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;
use ProfilePress\Core\ShortcodeParser\Builder\FieldsShortcodeCallback;
use WP_User_Query;

abstract class AbstractMemberDirectoryTheme extends AbstractTheme
{
    public function __construct($form_id, $form_type)
    {
        parent::__construct($form_id, $form_type);

        add_action('ppress_drag_drop_builder_admin_page', [$this, 'js_script']);
    }

    abstract function form_wrapper_class();

    abstract function directory_structure();

    public function form_structure()
    {
        $wp_user_query = $this->wp_user_query();

        $total_users_found = $wp_user_query['total_users_found'];

        $query_params = $this->search_filter_query_params();

        ob_start();

        printf('[pp-form-wrapper class="%s"]', $this->form_wrapper_class());

        $this->search_filter_sort_structure();

        if ( ! $this->is_result_after_search_enabled() || (isset($query_params['ppmd-search']) && $query_params['ppmd-search'] == $this->form_id)) {

            if (0 === $total_users_found) { ?>

                <div class="ppressmd-members-total-wrap">
                    <div class="ppressmd-members-total">
                        <?= $this->get_no_result_text() ?>
                    </div>
                </div>

                <?php

            } else {

                if ( ! empty($query_params[$this->search_query_key()])) { ?>

                    <div class="ppressmd-members-total-wrap">
                        <div class="ppressmd-members-total">

                            <?= str_replace(
                                '{total_users}', $total_users_found,
                                $total_users_found > 1 ? $this->get_results_text() : $this->get_single_result_text()
                            ) ?>
                        </div>
                    </div>

                    <?php
                }
            }

            if ($total_users_found > 0) {

                $this->directory_structure();

                $this->display_pagination($total_users_found);
            }
        }

        echo '[/pp-form-wrapper]';

        return ob_get_clean();
    }

    public function js_script()
    {
        ?>
        <script type="text/javascript">
            (function ($) {

                var run = function () {
                    $('#ppress_md_enable_custom_sort').change(function () {
                        $('.ppress_md_sort_method_fields_wrap').toggle(this.checked);
                    }).trigger('change');

                    $('#ppress_md_enable_search').change(function () {
                        $('.ppress_md_search_fields_wrap').toggle(this.checked);
                        $('.ppress_md_enable_filters_wrap').toggle(this.checked);
                        $('.ppress_md_filter_fields_wrap').toggle(this.checked);
                    }).trigger('change');
                };

                $(run);

            })(jQuery);
        </script>
        <?php
    }

    public function default_metabox_settings()
    {
        $data                             = parent::default_metabox_settings();
        $data['ppress_md_user_roles']     = [];
        $data['ppress_md_specific_users'] = '';
        $data['ppress_md_exclude_users']  = '';

        $data['ppress_md_sort_default']       = 'newest';
        $data['ppress_md_enable_custom_sort'] = 'false';
        $data['ppress_md_sort_method_fields'] = [];

        $data['ppress_md_enable_search']  = 'true';
        $data['ppress_md_search_fields']  = ['pp_email_address', 'pp_website_url', 'pp_display_name', 'first_name', 'last_name'];
        $data['ppress_md_enable_filters'] = 'false';
        $data['ppress_md_filter_fields']  = [];

        $data['ppress_md_enable_result_after_search'] = 'false';
        $data['ppress_md_result_number_per_page']     = '9';
        $data['ppress_md_results_text']               = sprintf(esc_html__('%s Members', 'wp-user-avatar'), '{total_users}');
        $data['ppress_md_single_result_text']         = sprintf(esc_html__('%s Member', 'wp-user-avatar'), '{total_users}');
        $data['ppress_md_no_result_text']             = esc_html__('We could not find any user that matches your search criteria', 'wp-user-avatar');

        $data['ppress_md_search_filter_field_text_color']   = '#666666';
        $data['ppress_md_search_filter_field_border_color'] = '#dddddd';

        $data['ppress_md_pagination_link_color']              = '#666666';
        $data['ppress_md_pagination_active_link_color']       = '#ffffff';
        $data['ppress_md_pagination_active_background_color'] = '#007bff';

        return $data;
    }

    public function appearance_settings($settings)
    {
        $settings[] = [
            'id'          => 'ppress_md_user_roles',
            'type'        => 'select2',
            'label'       => esc_html__('User Roles to Display', 'wp-user-avatar'),
            'description' => esc_html__('If you do not want to show all members, select the user roles to appear in this directory.', 'wp-user-avatar'),
            'options'     => ppress_wp_roles_key_value(false),
            'priority'    => 5
        ];

        $settings[] = [
            'id'          => 'ppress_md_specific_users',
            'type'        => 'textarea',
            'placeholder' => esc_html__('Example: 1, 6, 32', 'wp-user-avatar'),
            'label'       => esc_html__('Comma Separated List of Users ID to Only Show', 'wp-user-avatar'),
            'priority'    => 10
        ];

        $settings[] = [
            'id'          => 'ppress_md_exclude_users',
            'type'        => 'textarea',
            'placeholder' => esc_html__('Example: 1, 6, 32', 'wp-user-avatar'),
            'label'       => esc_html__('Comma Separated List of Users ID to Exclude', 'wp-user-avatar'),
            'priority'    => 10
        ];

        return $settings;
    }

    public function color_settings($settings)
    {
        $settings2 = [
            [
                'id'    => 'ppress_md_search_filter_field_text_color',
                'type'  => 'color',
                'label' => esc_html__('Search & Filter Fields Text', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_search_filter_field_border_color',
                'type'  => 'color',
                'label' => esc_html__('Search & Filter Fields Border', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_pagination_link_color',
                'type'  => 'color',
                'label' => esc_html__('Pagination Links', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_pagination_active_link_color',
                'type'  => 'color',
                'label' => esc_html__('Pagination Active Link Color', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_pagination_active_background_color',
                'type'  => 'color',
                'label' => esc_html__('Pagination Active Link Background', 'wp-user-avatar')
            ]
        ];

        return array_merge($settings, $settings2);
    }

    public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
    {
        $sorting_options = [esc_html__('Standard Fields', 'wp-user-avatar') => $this->md_standard_sort_fields()];

        $search_options = [
            esc_html__('Standard', 'wp-user-avatar') => [
                'pp_email_address' => esc_html__('Email Address', 'wp-user-avatar'),
                'pp_website_url'   => esc_html__('Website', 'wp-user-avatar'),
                'pp_display_name'  => esc_html__('Display Name', 'wp-user-avatar'),
                'first_name'       => esc_html__('First Name', 'wp-user-avatar'),
                'last_name'        => esc_html__('Last Name', 'wp-user-avatar'),
                'description'      => esc_html__('Biography', 'wp-user-avatar')
            ]
        ];

        if (EM::is_enabled(EM::CUSTOM_FIELDS)) {
            $cf_label                   = esc_html__('Custom Fields', 'wp-user-avatar');
            $sorting_options[$cf_label] = ppress_custom_fields_key_value_pair(true);
            $search_options[$cf_label]  = ppress_custom_fields_key_value_pair(true);
        }

        $new_settings['ppress_md_sorting'] = [
            'tab_title' => esc_html__('Sorting', 'wp-user-avatar'),
            [
                'id'      => 'ppress_md_sort_default',
                'label'   => esc_html__('Default Sorting method', 'wp-user-avatar'),
                'type'    => 'select',
                'options' => $sorting_options
            ],
            [
                'id'    => 'ppress_md_enable_custom_sort',
                'label' => esc_html__('Enable custom sorting', 'wp-user-avatar'),
                'type'  => 'checkbox',
            ],
            [
                'id'          => 'ppress_md_sort_method_fields',
                'label'       => esc_html__('Sorting Method Fields', 'wp-user-avatar'),
                'type'        => 'select2',
                'options'     => $sorting_options,
                'description' => esc_html__('Fields to show in sorting dropdown menu', 'wp-user-avatar')
            ],
        ];

        $new_settings['ppress_md_search'] = [
            'tab_title' => esc_html__('Search', 'wp-user-avatar'),
            [
                'id'    => 'ppress_md_enable_search',
                'label' => esc_html__('Display Search Form', 'wp-user-avatar'),
                'type'  => 'checkbox'
            ],
            [
                'id'          => 'ppress_md_search_fields',
                'label'       => esc_html__('Search Fields', 'wp-user-avatar'),
                'type'        => 'select2',
                'options'     => $search_options,
                'description' => esc_html__('Select fields to search in.', 'wp-user-avatar')
            ]
        ];


        if (EM::is_enabled(EM::CUSTOM_FIELDS)) {

            $new_settings['ppress_md_search'][] = [
                'id'          => 'ppress_md_enable_filters',
                'label'       => esc_html__('Enable Filters', 'wp-user-avatar'),
                'type'        => 'checkbox',
                'description' => esc_html__('If enabled, users will be able to filter members in this directory', 'wp-user-avatar')
            ];

            $new_settings['ppress_md_search'][] = [
                'id'          => 'ppress_md_filter_fields',
                'label'       => esc_html__('Filter Fields', 'wp-user-avatar'),
                'type'        => 'select2',
                'options'     => array_reduce(PROFILEPRESS_sql::get_profile_custom_fields_by_types(['select', 'checkbox', 'radio', 'country', 'date']), function ($carry, $item) {
                    $carry[$item->field_key] = ppress_woocommerce_field_transform($item->field_key, $item->label_name);

                    return $carry;
                }, []),
                'description' => esc_html__('Select custom fields that members can be filtered by. Only Select, Checkbox, Radio, Country and Date/Time fields are supported.', 'wp-user-avatar')
            ];
        }

        if ( ! EM::is_enabled(EM::CUSTOM_FIELDS)) {
            $upgrade_url                        = 'https://profilepress.net/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=md_custom_field_upsell';
            $new_settings['ppress_md_search'][] = [
                'id'      => 'ppress_md_search_filter_upsell',
                'label'   => '',
                'type'    => 'custom',
                'content' => sprintf(
                    esc_html__('%sUpgrade to ProfilePress premium%s if you don\'t have the custom field addon so you can enable search and filtering by custom fields.', 'wp-user-avatar'),
                    '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
                ),
            ];
        }

        $new_settings['ppress_md_result_pagination'] = [
            'tab_title' => esc_html__('Result & Pagination', 'wp-user-avatar'),
            [
                'id'          => 'ppress_md_enable_result_after_search',
                'label'       => esc_html__('Show Results Only After a Search', 'wp-user-avatar'),
                'type'        => 'checkbox',
                'description' => esc_html__('Enable to only show members after a search is performed', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_result_number_per_page',
                'label' => esc_html__('Number of Members per Page', 'wp-user-avatar'),
                'type'  => 'number'
            ],
            [
                'id'    => 'ppress_md_results_text',
                'label' => esc_html__('Results Text', 'wp-user-avatar'),
                'type'  => 'text'
            ],
            [
                'id'    => 'ppress_md_single_result_text',
                'label' => esc_html__('Single Result Text', 'wp-user-avatar'),
                'type'  => 'text'
            ],
            [
                'id'    => 'ppress_md_no_result_text',
                'label' => esc_html__('No Result Text', 'wp-user-avatar'),
                'type'  => 'text'
            ],
        ];

        return $new_settings;
    }

    protected function search_query_key()
    {
        return 'search-' . $this->form_id;
    }

    protected function get_current_page()
    {
        return absint(max(1, ppressGET_var('mdpage' . $this->form_id, 1, true)));
    }

    protected function wp_user_query()
    {
        static $cache = [];

        if ( ! isset($cache[$this->form_id])) {

            $search_q_key = self::search_query_key();

            $current_page = $this->get_current_page();

            $users_per_page = $this->get_default_result_number_per_page();

            $roles = array_filter($this->get_meta('ppress_md_user_roles'));

            $include_user_ids = $this->get_meta('ppress_md_specific_users');

            $exclude_user_ids = $this->get_meta('ppress_md_exclude_users');

            $default_sort_method = $this->get_meta('ppress_md_sort_default');

            if (empty($include_user_ids)) {
                $include_user_ids = [];
            } else {
                $include_user_ids = array_map(function ($val) {
                    return absint(trim($val));
                }, explode(',', $include_user_ids));
            }

            if (empty($exclude_user_ids)) {
                $exclude_user_ids = [];
            } else {
                $exclude_user_ids = array_map(function ($val) {
                    return absint(trim($val));
                }, explode(',', $exclude_user_ids));
            }

            $search_field_harsh_map = [
                'pp_email_address' => 'user_email',
                'pp_website_url'   => 'user_url',
                'pp_display_name'  => 'display_name'
            ];

            $db_search_fields = array_filter($this->get_meta('ppress_md_search_fields'), function ($item) {
                return ! empty($item);
            });

            // get meta fields to be used by meta query of WP_User_Query
            $meta_fields = array_filter($db_search_fields, function ($item) use ($search_field_harsh_map) {
                return ! in_array($item, array_keys($search_field_harsh_map));
            });

            // get wp_user table columns to use in searching for users
            $search_columns = array_reduce($db_search_fields, function ($carry, $item) use ($search_field_harsh_map) {

                if (isset($search_field_harsh_map[$item])) {
                    $carry[] = $search_field_harsh_map[$item];
                }

                return $carry;

            }, ['user_login']);

            $sort_method = ppress_var($_GET, 'sortby' . $this->form_id, $default_sort_method, true);

            $query_params = $this->search_filter_query_params();

            $offset = $current_page > 1 ? ($current_page - 1) * $users_per_page : 0;

            $wp_user_query = $this->member_directory_users([
                'number'             => $users_per_page,
                'paged'              => $current_page,
                'offset'             => $offset,
                'roles'              => $roles,
                'include_user_ids'   => $include_user_ids,
                'exclude_user_ids'   => $exclude_user_ids,
                'sort_method'        => $sort_method,
                'search_columns'     => $search_columns,
                'search_meta_fields' => $meta_fields,
                'filter_meta_fields' => isset($query_params['filters']) ? array_filter(
                    array_map('ppress_recursive_trim', ppress_var($query_params, 'filters', [], true))
                ) : [],
                'is_search_query'    => ! empty($query_params['ppmd-search']),
                'search_q'           => isset($query_params[$search_q_key]) ? sanitize_text_field($query_params[$search_q_key]) : ''
            ]);

            $users = (array)$wp_user_query->get_results();

            $total_users_found = $wp_user_query->get_total();

            $cache[$this->form_id] = [
                'users'             => $users,
                'total_users_found' => $total_users_found
            ];
        }

        return $cache[$this->form_id];
    }

    protected function search_filter_query_params()
    {

        static $cache = [];

        if ( ! isset($cache[$this->form_id])) {

            $query_params = [];

            if (empty($query_params) && ! empty($_GET['filter' . $this->form_id])) {
                $query_params = json_decode(base64_decode($_GET['filter' . $this->form_id]), true);
            }

            $cache[$this->form_id] = $query_params;
        }

        return $cache[$this->form_id];
    }

    public function md_standard_sort_fields()
    {
        return [
            'newest'       => esc_html__('Newest Users First', 'wp-user-avatar'),
            'oldest'       => esc_html__('Oldest Users First', 'wp-user-avatar'),
            'display-name' => esc_html__('Display Name', 'wp-user-avatar'),
            'first-name'   => esc_html__('First Name', 'wp-user-avatar'),
            'last-name'    => esc_html__('Last Name', 'wp-user-avatar'),
            'username'     => esc_html__('Username', 'wp-user-avatar')
        ];
    }

    public function get_sort_field_label($field)
    {
        $pp_custom_fields = ppress_custom_fields_key_value_pair(true);

        return ppress_var($this->md_standard_sort_fields(), $field, ppress_var($pp_custom_fields, $field));
    }

    /**
     * @param array $parsed_args
     *
     * @return WP_User_Query
     */
    public function member_directory_users($parsed_args = [])
    {
        $parsed_args = apply_filters('ppress_member_directory_parsed_args', wp_parse_args($parsed_args, [
            'number'             => 9,
            'paged'              => 1,
            'offset'             => 0,
            'roles'              => [],
            'include_user_ids'   => [],
            'exclude_user_ids'   => [],
            'count_total'        => true,
            'sort_method'        => 'newest',
            'is_search_query'    => false,
            'search_q'           => '',
            'search_columns'     => ['user_login', 'user_email', 'user_url', 'display_name'],
            'search_meta_fields' => ['first_name', 'last_name'],
            'filter_meta_fields' => []
        ]));

        $args = [
            'number'   => $parsed_args['number'],
            'paged'    => $parsed_args['paged'],
            'offset'   => $parsed_args['offset'],
            'role__in' => $parsed_args['roles'],
            'include'  => $parsed_args['include_user_ids'],
            'exclude'  => $parsed_args['exclude_user_ids']
        ];

        // no check for username because it's the default orderby
        switch ($parsed_args['sort_method']) {
            case 'newest':
                $args['orderby'] = 'user_registered';
                $args['order']   = 'DESC';
                break;
            case 'oldest':
                $args['orderby'] = 'user_registered';
                break;
            case 'username':
                $args['orderby'] = 'user_login';
                break;
            case 'display-name':
                $args['meta_key'] = 'display_name';
                $args['orderby']  = 'meta_value';
                $args['order']    = 'ASC';
                break;
            case 'first-name':
                $args['meta_key'] = 'first_name';
                $args['orderby']  = 'meta_value';
                $args['order']    = 'ASC';
                break;
            case 'last-name':
                $args['meta_key'] = 'last_name';
                $args['orderby']  = 'meta_value';
                $args['order']    = 'ASC';
                break;
        }

        if ($parsed_args['is_search_query'] === true) {

            $search_term = sanitize_text_field($parsed_args['search_q']);

            $search_columns = $parsed_args['search_columns'];

            $filter_meta_fields = $parsed_args['filter_meta_fields'];

            $args['search'] = '*' . $search_term . '*';

            // we need to empty out the search column so wp user query doesn't restrict the search only
            // to supplied search columns. We want to also check usermeta too.
            add_filter('user_search_columns', '__return_empty_array', 999999999);

            add_action('pre_user_query', function ($query) {
                // removes "AND ()" from query which causes the sql to be invalid.
                // SELECT DISTINCT wp_users.* FROM wp_users INNER JOIN wp_usermeta ON
                // ( wp_users.ID = wp_usermeta.user_id ) WHERE 1=1 AND ( wp_users.user_nicename LIKE '%little%' OR
                // wp_users.user_email LIKE '%little%' OR ( ( wp_usermeta.meta_key = 'first_name' AND wp_usermeta.meta_value LIKE '%little%' )
                // OR ( wp_usermeta.meta_key = 'last_name' AND wp_usermeta.meta_value LIKE '%little%' ) OR ( wp_usermeta.meta_key = 'twitter'
                // AND wp_usermeta.meta_value LIKE '%little%' ) ) ) AND () ORDER BY user_registered DESC
                $query->query_where = str_replace('AND ()', '', $query->query_where);
            });

            /**
             * Modifies the query so we can tactically include searching of $search_columns in wp_users table
             * @see https://wordpress.stackexchange.com/a/248674/59917
             */
            add_filter('get_meta_sql', function ($sql) use ($search_term, $search_columns, $filter_meta_fields) {

                global $wpdb;

                // Only run once:
                static $nr = 0;

                if (0 != $nr++) return $sql;

                $OR_placeholders = [];
                $queries         = [];


                if (is_array($search_columns) && ! empty($search_columns)) {

                    foreach ($search_columns as $search_column) {

                        $OR_placeholders[] = '%s';

                        $queries[] = $wpdb->prepare("{$wpdb->users}.$search_column LIKE %s", '%' . $wpdb->esc_like($search_term) . '%');
                    }
                }

                $OR_placeholders[] = '%s';

                $queries[] = ppress_mb_function(
                    ['mb_substr', 'substr'],
                    [
                        $sql['where'],
                        5,
                        ppress_mb_function(['mb_strlen', 'strlen'], [$sql['where']])
                    ]
                );


                $filter_queries = '';

                /** @see https://stackoverflow.com/a/65653006/2648410 */
                if ( ! empty($filter_meta_fields)) {

                    foreach ($filter_meta_fields as $meta_key => $meta_value) {

                        $filter_queries .= "AND EXISTS ( SELECT 1 FROM {$wpdb->usermeta} WHERE {$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND ";

                        $filter_queries .= '(';

                        if (is_array($meta_value)) {

                            $meta_value_count = count($meta_value);

                            foreach ($meta_value as $index2 => $value) {
                                $index2++;

                                $filter_queries .= $wpdb->prepare(
                                    "({$wpdb->usermeta}.meta_key = '$meta_key' AND {$wpdb->usermeta}.meta_value LIKE %s)",
                                    '%' . $wpdb->esc_like($value) . '%'
                                );

                                if ($index2 != $meta_value_count) {
                                    $filter_queries .= ' OR ';
                                }
                            }

                        } else {

                            $filter_queries .= $wpdb->prepare(
                                "({$wpdb->usermeta}.meta_key = '$meta_key' AND {$wpdb->usermeta}.meta_value = %s)",
                                $meta_value
                            );
                        }

                        $filter_queries .= ')';

                        $filter_queries .= ')';
                    }
                }

                $queries[] = $filter_queries;

                $where = vsprintf(
                    " AND ((" . implode(' OR ', $OR_placeholders) . " ) %s )",
                    $queries
                );

                $sql['where'] = $where;

                return $sql;
            });

            if (is_array($parsed_args['search_meta_fields']) && ! empty($parsed_args['search_meta_fields'])) {

                $args['meta_query'][0]['relation'] = 'OR';

                foreach ($parsed_args['search_meta_fields'] as $search_meta_field) {

                    $args['meta_query'][0][] = [
                        'key'     => $search_meta_field,
                        'value'   => $search_term,
                        'compare' => 'LIKE'
                    ];
                }
            }
        }

        return new WP_User_Query(apply_filters('ppress_member_directory_wp_user_args', $args, $this->form_id, $this));
    }

    protected function sort_method_dropdown_menu()
    {
        $sortby_query_key = 'sortby' . $this->form_id;

        $custom_sort_enabled = $this->get_meta('ppress_md_enable_custom_sort') == 'true';

        $default_sort_field = ppress_var($_GET, $sortby_query_key, $this->get_meta('ppress_md_sort_default'), true);

        $custom_sort_fields = array_filter($this->get_meta('ppress_md_sort_method_fields'), function ($item) use ($default_sort_field) {
            return ! empty($item) && $item != $default_sort_field;
        });

        if ( ! $custom_sort_enabled || empty($custom_sort_fields)) return;

        ?>
        <div class="ppressmd-member-directory-sorting">
            <span><?= esc_html__('Sort by', 'wp-user-avatar') ?>:&nbsp;</span>
            <div class="ppressmd-member-directory-sorting-a">
                <a href=" <?= esc_url(add_query_arg([$sortby_query_key => $default_sort_field])) ?>" class="ppressmd-member-directory-sorting-a-text">
                    <?= $this->get_sort_field_label($default_sort_field) ?>
                    <span class="ppress-material-icons">keyboard_arrow_down</span>
                </a>

                <div class="ppressmd-new-dropdown">
                    <ul>
                        <?php foreach ($custom_sort_fields as $field) : ?>
                            <li>
                                <a href="<?= esc_url(add_query_arg([$sortby_query_key => $field])) ?>">
                                    <?= $this->get_sort_field_label($field) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }

    protected function filter_structure($show_filter_fields = false)
    {
        $filter_enabled = $this->get_meta('ppress_md_enable_filters') == 'true';

        $filter_fields = array_filter($this->get_meta('ppress_md_filter_fields'), function ($item) {
            return ! empty($item);
        });

        if ( ! $filter_enabled || empty($filter_fields)) return;

        if ( ! $show_filter_fields) : ?>
            <span class="ppressmd-member-directory-filters">
		    <span class="ppressmd-member-directory-filters-a">
			    <a href="#">
				    <?= esc_html__('More Filters', 'wp-user-avatar') ?>
					    <span class="ppress-material-icons ppress-up">keyboard_arrow_up</span>
                        <span class="ppress-material-icons ppress-down">keyboard_arrow_down</span>
                </a>
			</span>
		</span>
        <?php endif;

        if ($show_filter_fields) : $query_params = $this->search_filter_query_params(); ?>

            <div class="ppressmd-member-directory-header-row ppressmd-member-directory-filters-bar">

                <div class="ppressmd-search ppressmd-search-invisible">

                    <?php foreach ($filter_fields as $field_key) : ?>

                        <?php $custom_field = PROFILEPRESS_sql::get_profile_custom_field_by_key($field_key); ?>

                        <div class="ppressmd-search-filter ppressmd-text-filter-type">

                            <?php

                            switch ($custom_field['type']) {
                                case 'select' :
                                case 'checkbox' :
                                case 'radio' :
                                case 'country' :

                                    if ( ! empty($custom_field['options'])) {

                                        $is_multiple = false;

                                        if ($custom_field['type'] == 'select') {
                                            $is_multiple = ppress_is_select_field_multi_selectable($field_key);

                                        }

                                        if ($custom_field['type'] == 'checkbox') $is_multiple = true;

                                        printf(
                                            '<select name="%s" data-placeholder="%s" class="ppressmd-form-field ppmd-select2"%s>',
                                            $is_multiple ? 'filters[' . $field_key . '][]' : 'filters[' . $field_key . ']',
                                            $custom_field['label_name'],
                                            $is_multiple ? ' multiple' : ' data-allow-clear="true"'
                                        );

                                        $options = array_map('trim', explode(',', $custom_field['options']));

                                        if ( ! $is_multiple) {
                                            echo '<option></option>';
                                        }

                                        foreach ($options as $option) {
                                            $bucket = ppress_var(ppress_var($query_params, 'filters', []), $field_key);
                                            printf(
                                                '<option value="%1$s" %2$s>%1$s</option>',
                                                $option,
                                                ! $is_multiple ? selected($option, $bucket, false) : (is_array($bucket) && in_array($option, $bucket) ? 'selected=selected' : '')
                                            );
                                        }

                                        echo '</select>';
                                    }
                                    break;

                                case 'date' :

                                    $dateFormat = ! empty($custom_field['options']) ? $custom_field['options'] : 'Y-m-d';

                                    $hasTime = FieldsShortcodeCallback::hasTime($dateFormat);
                                    $time24  = false;

                                    if ($hasTime && strpos($dateFormat, 'H') !== false) {
                                        $time24 = true;
                                    }

                                    $config = apply_filters('ppress_frontend_flatpickr_date_config', [
                                        'dateFormat'    => $dateFormat,
                                        'enableTime'    => $hasTime,
                                        'noCalendar'    => ! FieldsShortcodeCallback::hasDate($dateFormat),
                                        'disableMobile' => true,
                                        'time_24hr'     => $time24
                                    ]);

                                    printf(
                                        '<input type="text" name="%1$s" placeholder="%2$s" value="%4$s" class="ppressmd-form-field ppmd-date" data-config="%3$s">',
                                        'filters[' . $custom_field['field_key'] . ']',
                                        $custom_field['label_name'],
                                        esc_attr(json_encode($config)),
                                        ppress_var(ppress_var($query_params, 'filters', []), $field_key)
                                    );
                                    break;
                            }
                            ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>
        <?php endif;
    }

    protected function search_form()
    {
        if ($this->get_meta('ppress_md_enable_search') != 'true') return;

        $search_string = esc_html__('Search', 'wp-user-avatar');

        $entered_search_term = ppress_var($this->search_filter_query_params(), 'search-' . $this->form_id, '');

        ?>
        <div class="ppressmd-member-directory-header-row ppressmd-member-directory-search-row">
            <div class="ppressmd-member-directory-search-line">
                <label>
                    <input name="search-<?= $this->form_id ?>" type="search" class="ppressmd-search-line" placeholder="<?= $search_string ?>" value="<?= $entered_search_term ?>">
                </label>
                <input type="submit" class="ppressmd-do-search ppressmd-button" value="<?= $search_string ?>">
            </div>
        </div>
        <?php
    }

    protected function display_pagination($total_users_found)
    {
        $current_page = $this->get_current_page();

        $users_per_page = $this->get_default_result_number_per_page();

        $total_pages = ceil($total_users_found / $users_per_page);

        if ($total_pages > 1) {

            echo '<div class="ppmd-pagination-wrap">';

            // from https://wordpress.stackexchange.com/questions/275527/paginate-links-ignore-my-format
            echo paginate_links([
                //'base'      => '%_%', somehow with this enabled, pagination breaks where page 1 link becomes the current page url
                'total'     => $total_pages,
                'current'   => $current_page,
                'format'    => '?mdpage' . $this->form_id . '=%#%',
                'prev_text' => '<span class="ppress-material-icons">keyboard_arrow_left</span>',
                'next_text' => '<span class="ppress-material-icons">keyboard_arrow_right</span>',
            ]);

            echo '</div>';
        }
    }

    protected function search_filter_sort_structure()
    {
        $is_filters_expanded = false;

        $query_params = $this->search_filter_query_params();

        if (isset($query_params['filters']) && ! empty(array_filter($query_params['filters']))) {
            $is_filters_expanded = true;
        }
        ?>

        <div class="ppressmd-member-directory-header ppressmd-form<?= $is_filters_expanded ? ' ppmd-filters-expand' : ''; ?>">

            <form action="<?= ppress_get_current_url_query_string() ?>" method="get">

                <?php $this->search_form(); ?>

                <div class="ppressmd-member-directory-header-row">
                    <div class="ppressmd-member-directory-nav-line">

                        <?php $this->sort_method_dropdown_menu(); ?>

                        <?php $this->filter_structure(); ?>

                    </div>
                </div>

                <?php $this->filter_structure(true) ?>

                <input type="hidden" name="ppmd-search" value="<?= $this->form_id ?>">

            </form>

        </div>
        <?php
    }

    protected function is_result_after_search_enabled()
    {
        return $this->get_meta('ppress_md_enable_result_after_search') == 'true';
    }

    protected function get_results_text()
    {
        return $this->get_meta('ppress_md_results_text');
    }

    protected function get_single_result_text()
    {
        return $this->get_meta('ppress_md_single_result_text');
    }

    protected function get_no_result_text()
    {
        return $this->get_meta('ppress_md_no_result_text');
    }

    protected function get_default_result_number_per_page()
    {
        static $cache = [];

        if ( ! isset($cache[$this->form_id])) {
            $cache[$this->form_id] = absint($this->get_meta('ppress_md_result_number_per_page'));
        }

        return $cache[$this->form_id];
    }

    /**
     * @return MemberDirectoryListing
     */
    protected function directory_listing($user_id = false)
    {
        return (new MemberDirectoryListing($this->form_id, $user_id))->defaults($this->default_fields_settings());
    }

    public function form_css()
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        $search_filter_field_text_color   = $this->get_meta('ppress_md_search_filter_field_text_color');
        $search_filter_field_border_color = $this->get_meta('ppress_md_search_filter_field_border_color');

        $pagination_link_color        = $this->get_meta('ppress_md_pagination_link_color');
        $pagination_active_link_color = $this->get_meta('ppress_md_pagination_active_link_color');
        $pagination_active_bg_color   = $this->get_meta('ppress_md_pagination_active_background_color');

        return <<<CSS
#pp-$form_type-$form_id.pp-member-directory .ppressmd-member-directory-header .ppressmd-member-directory-header-row .ppressmd-member-directory-search-line label .ppressmd-search-line,
#pp-$form_type-$form_id.pp-member-directory .ppressmd-member-directory-header .ppressmd-member-directory-header-row .ppressmd-search .ppressmd-search-filter.ppressmd-text-filter-type input:not(.select2-search__field),
#pp-$form_type-$form_id.pp-member-directory .ppressmd-member-directory-header .select2.select2-container .select2-selection {
    border: 1px solid $search_filter_field_border_color !important;
}

#pp-$form_type-$form_id.pp-member-directory .ppressmd-member-directory-header .ppressmd-member-directory-header-row .ppressmd-search .ppressmd-search-filter.ppressmd-text-filter-type input,
#pp-$form_type-$form_id.pp-member-directory .ppressmd-member-directory-header .ppressmd-member-directory-header-row .ppressmd-member-directory-search-line label .ppressmd-search-line,
#pp-$form_type-$form_id.pp-member-directory .ppressmd-member-directory-header .ppressmd-member-directory-header-row .ppressmd-member-directory-nav-line .ppress-material-icons,
#pp-$form_type-$form_id.pp-member-directory .ppressmd-member-directory-header .select2.select2-container .select2-selection__rendered {
    color: $search_filter_field_text_color !important;
}

#pp-$form_type-$form_id.pp-member-directory .ppmd-pagination-wrap .page-numbers {
    color: $pagination_link_color !important;
}

#pp-$form_type-$form_id.pp-member-directory .ppmd-pagination-wrap .page-numbers.current {
    background: $pagination_active_bg_color !important;
    color: $pagination_active_link_color !important;
}
CSS;

    }
}