<?php

/**
 * Start output buffering
 *
 * This is needed for redirecting to post when a new post has made
 *
 * @since 0.8
 */
function wpuf_buffer_start() {
    ob_start();
}

add_action( 'init', 'wpuf_buffer_start' );

/**
 * Format the post status for user dashboard
 *
 * @param string $status
 *
 * @since version 0.1
 *
 * @author Tareq Hasan
 */
function wpuf_show_post_status( $status ) {
    if ( 'publish' === $status ) {
        $title     = __( 'Live', 'wp-user-frontend' );
        $fontcolor = '#33CC33';
    } elseif ( 'draft' === $status ) {
        $title     = __( 'Offline', 'wp-user-frontend' );
        $fontcolor = '#bbbbbb';
    } elseif ( 'pending' === $status ) {
        $title     = __( 'Awaiting Approval', 'wp-user-frontend' );
        $fontcolor = '#C00202';
    } elseif ( 'future' === $status ) {
        $title     = __( 'Scheduled', 'wp-user-frontend' );
        $fontcolor = '#bbbbbb';
    } elseif ( 'private' === $status ) {
        $title     = __( 'Private', 'wp-user-frontend' );
        $fontcolor = '#bbbbbb';
    }

    $show_status = '<span style="color:' . $fontcolor . ';">' . $title . '</span>';
    echo wp_kses_post( apply_filters( 'wpuf_show_post_status', $show_status, $status ) );
}

/**
 * Format the post status for user dashboard
 *
 * @param string $status
 *
 * @since version 0.1
 *
 * @author Tareq Hasan
 */
function wpuf_admin_post_status( $status ) {
    if ( 'publish' === $status ) {
        $title     = __( 'Published', 'wp-user-frontend' );
        $fontcolor = '#009200';
    } elseif ( 'draft' === $status || 'private' === $status ) {
        $title     = __( 'Draft', 'wp-user-frontend' );
        $fontcolor = '#bbbbbb';
    } elseif ( 'pending' === $status ) {
        $title     = __( 'Pending', 'wp-user-frontend' );
        $fontcolor = '#C00202';
    } elseif ( 'future' === $status ) {
        $title     = __( 'Scheduled', 'wp-user-frontend' );
        $fontcolor = '#bbbbbb';
    }

    echo wp_kses_post( '<span style="color:' . $fontcolor . ';">' . $title . '</span>' );
}

/**
 * Upload the files to the post as attachemnt
 *
 * @param <type> $post_id
 */
function wpuf_upload_attachment( $post_id ) {
    if ( ! isset( $_FILES['wpuf_post_attachments'] ) ) {
        return false;
    }

    $fields = (int) wpuf_get_option( 'attachment_num' );

    $wpuf_post_attachments = isset( $_FILES['wpuf_post_attachments'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_FILES['wpuf_post_attachments'] ) ) : [];

    for ( $i = 0; $i < $fields; $i++ ) {
        $file_name = basename( $wpuf_post_attachments['name'][ $i ] );

        if ( $file_name ) {
            if ( $file_name ) {
                $upload = [
                    'name'     => $wpuf_post_attachments['name'][ $i ],
                    'type'     => $wpuf_post_attachments['type'][ $i ],
                    'tmp_name' => $wpuf_post_attachments['tmp_name'][ $i ],
                    'error'    => $wpuf_post_attachments['error'][ $i ],
                    'size'     => $wpuf_post_attachments['size'][ $i ],
                ];

                wp_handle_upload( $upload );
            }//file exists
        }// end for
    }
}

/**
 * Get the attachments of a post
 *
 * @param int $post_id
 *
 * @return array attachment list
 */
function wpfu_get_attachments( $post_id ) {
    $att_list = [];

    $args = [
        'post_type'   => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => $post_id,
        'order'       => 'ASC',
        'orderby'     => 'menu_order',
    ];

    $attachments = get_posts( $args );

    foreach ( $attachments as $attachment ) {
        $att_list[] = [
            'id'    => $attachment->ID,
            'title' => $attachment->post_title,
            'url'   => wp_get_attachment_url( $attachment->ID ),
            'mime'  => $attachment->post_mime_type,
        ];
    }

    return $att_list;
}

/**
 * Remove the mdedia upload tabs from subscribers
 *
 * @author Tareq Hasan
 */
function wpuf_unset_media_tab( $list ) {
    if ( ! current_user_can( 'edit_posts' ) ) {
        unset( $list['library'] );
        unset( $list['gallery'] );
    }

    return $list;
}

add_filter( 'media_upload_tabs', 'wpuf_unset_media_tab' );

/**
 * Get the registered post types
 *
 * @return array
 */
function wpuf_get_post_types( $args = [] ) {
    $defaults = [];

    $args = wp_parse_args( $args, $defaults );

    $post_types = get_post_types( $args );

    $ignore_post_types = [
        'attachment',
        'revision',
        'nav_menu_item',
    ];

    foreach ( $post_types as $key => $val ) {
        if ( in_array( $val, $ignore_post_types, true ) ) {
            unset( $post_types[ $key ] );
        }
    }

    return apply_filters( 'wpuf-get-post-types', $post_types );
}

/**
 * Get lists of users from database
 *
 * @return array
 */
function wpuf_list_users() {
    global $wpdb;

    $users = $wpdb->get_results( "SELECT ID, user_login from $wpdb->users" );

    $list = [];

    if ( $users ) {
        foreach ( $users as $user ) {
            $list[ $user->ID ] = $user->user_login;
        }
    }

    return $list;
}

/**
 * Retrieve or display list of posts as a dropdown (select list).
 *
 * @return string HTML content, if not displaying
 */
function wpuf_get_pages( $post_type = 'page' ) {
    $array = [ '' => __( '&mdash; Select &mdash;', 'wp-user-frontend' ) ];
    $pages = get_posts(
        [
            'post_type'              => $post_type,
            'numberposts'            => - 1,
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]
    );

    if ( $pages ) {
        foreach ( $pages as $page ) {
            $array[ $page->ID ] = esc_attr( $page->post_title );
        }
    }

    return $array;
}

/**
 * Edit post link for frontend
 *
 * @since 0.7
 *
 * @param string $url     url of the original post edit link
 * @param int    $post_id
 *
 * @return string url of the current edit post page
 */
function wpuf_override_admin_edit_link( $url, $post_id ) {
    if ( is_admin() ) {
        return $url;
    }

    $override = wpuf_get_option( 'override_editlink', 'wpuf_general', 'no' );

    if ( $override === 'yes' ) {
        $url = '';

        if ( 'yes' === wpuf_get_option( 'enable_post_edit', 'wpuf_dashboard', 'yes' ) ) {
            $edit_page = (int) wpuf_get_option( 'edit_page_id', 'wpuf_frontend_posting' );
            $url       = get_permalink( $edit_page );

            $url = wp_nonce_url( $url . '?pid=' . $post_id, 'wpuf_edit' );
        }
    }

    return apply_filters( 'wpuf_front_post_edit_link', $url );
}

add_filter( 'get_edit_post_link', 'wpuf_override_admin_edit_link', 10, 2 );

/**
 * Create HTML dropdown list of Categories.
 *
 * @since 2.1.0
 *
 * @uses Walker
 */
class WPUF_Walker_Category_Multi extends Walker {

    /**
     * @see Walker::$tree_type
     *
     * @var string
     */
    public $tree_type = 'category';

    /**
     * @see Walker::$db_fields
     *
     * @var array
     */
    public $db_fields = [
        'parent' => 'parent',
        'id' => 'term_id',
    ];

    /**
     * @see Walker::start_el()
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category category data object
     * @param int    $depth    Depth of category. Used for padding.
     * @param array  $args     uses 'selected' and 'show_count' keys, if they exist
     */
    public function start_el( &$output, $category, $depth = 0, $args = [], $id = 0 ) {
        $pad = str_repeat( '&nbsp;', $depth * 3 );

        $cat_name = apply_filters( 'list_cats', $category->name, $category );
        $output .= "\t<option class=\"level-$depth\" value=\"" . $category->term_id . '"';

        if ( in_array( $category->term_id, $args['selected'], true ) ) {
            $output .= ' selected="selected"';
        }

        $output .= '>';
        $output .= $pad . $cat_name;

        if ( $args['show_count'] ) {
            $output .= '&nbsp;&nbsp;(' . $category->count . ')';
        }

        $output .= "</option>\n";
    }
}

/**
 * Category checklist walker
 *
 * @since 0.8
 */
class WPUF_Walker_Category_Checklist extends Walker {
    public $tree_type = 'category';

    public $db_fields = [
        'parent' => 'parent',
        'id' => 'term_id',
    ]; //TODO: decouple this

    public function start_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent<ul class='children'>\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }

    public function start_el( &$output, $category, $depth = 0, $args = [], $current_object_id = 0 ) {
        $taxonomy = $args['taxonomy'];

        if ( empty( $taxonomy ) ) {
            $taxonomy = 'category';
        }

        if ( 'category' === $taxonomy ) {
            $name = 'category';
        } else {
            $name = $taxonomy;
        }

        if ( 'yes' === $args['show_inline'] ) {
            $inline_class = 'wpuf-checkbox-inline';
        } else {
            $inline_class = '';
        }

        $class = isset( $args['class'] ) ? $args['class'] : '';
        $output .= "\n<li class='" . $inline_class . "' id='{$taxonomy}-{$category->term_id}'>" . '<label class="selectit"><input class="' . $class . '" value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"' . checked( in_array( $category->term_id, $args['selected_cats'], true ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
    }

    public function end_el( &$output, $category, $depth = 0, $args = [] ) {
        $output .= "</li>\n";
    }
}

/**
 * Displays checklist of a taxonomy
 *
 * @param int $post_id
 * @param array $selected_cats
 *
 * @since 0.8
 */
function wpuf_category_checklist( $post_id = 0, $selected_cats = false, $attr = [], $class = null ) {
    require_once ABSPATH . '/wp-admin/includes/template.php';

    $walker = new WPUF_Walker_Category_Checklist();

    $exclude_type = isset( $attr['exclude_type'] ) ? $attr['exclude_type'] : 'exclude';
    $exclude      = wpuf_get_field_settings_excludes( $attr, $exclude_type );

    $tax          = $attr['name'];
    $current_user = get_current_user_id();

    $args = [
        'taxonomy' => $tax,
    ];

    if ( $post_id ) {
        $args['selected_cats'] = wp_get_object_terms( $post_id, $tax, [ 'fields' => 'ids' ] );
    } elseif ( $selected_cats ) {
        $args['selected_cats'] = $selected_cats;
    } else {
        $args['selected_cats'] = [];
    }

    $args['show_inline'] = $attr['show_inline'];

    $args['class'] = $class;

    $tax_args = [
        'taxonomy'    => $tax,
        'hide_empty'  => false,
        $exclude['type'] => ( 'child_of' === $exclude_type ) ? $exclude['childs'] : $attr['exclude'],
        'orderby'     => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
        'order'       => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
    ];
    $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );

    $categories = (array) get_terms( $tax_args );

    echo wp_kses_post( '<ul class="wpuf-category-checklist">' );
    printf( '<input type="hidden" name="%s" value="0" />', esc_attr( $tax ) );
    echo wp_kses(
        call_user_func_array( [ &$walker, 'walk' ], [ $categories, 0, $args ] ), [
            'li'    => [
                'class' => [],
            ],
            'label' => [
                'class' => [],
            ],
            'input' => [
                'class'   => [],
                'type'    => [],
                'value'   => [],
                'name'    => [],
                'id'      => [],
                'checked' => [],
            ],
            'ul'    => [
                'class' => [],
            ],
        ]
    );
    echo wp_kses_post( '</ul>' );
}

/**
 * Get exclude settings for a field type
 *
 * @since 3.4.0
 *
 * @param array $field_settings
 * @param string $exclude_type
 *
 * @return array
 */
function wpuf_get_field_settings_excludes( $field_settings, $exclude_type ) {
    $attributes   = $field_settings['exclude'];
    $child_ids    = [];

    if ( ! empty( $attributes ) ) {
        foreach ( $attributes as $attr ) {
            $terms = get_terms(
                $field_settings['name'],
                array(
                    'hide_empty' => false,
                    'parent'     => $attr,
                )
            );

            foreach ( $terms as $term ) {
                array_push( $child_ids, $term->term_id );
            }
        }
    }

    if ( $exclude_type === 'child_of' ) {
        $exclude_type = 'include';
    }

    $excludes = [
        'type'   => $exclude_type,
        'childs' => $child_ids,
    ];

    return $excludes;
}

function wpuf_pre( $data ) {
    echo wp_kses_post( '<pre>' );
    print_r( $data );
    echo wp_kses_post( '</pre>' );
}

/**
 * Get all the image sizes
 *
 * @return array image sizes
 */
function wpuf_get_image_sizes() {
    $image_sizes_orig   = get_intermediate_image_sizes();
    $image_sizes_orig[] = 'full';
    $image_sizes        = [];

    foreach ( $image_sizes_orig as $size ) {
        $image_sizes[ $size ] = $size;
    }

    return $image_sizes;
}

function wpuf_allowed_extensions() {
    $extesions = [
        'images' => [
            'ext' => 'jpg,jpeg,gif,png,bmp',
            'label' => __( 'Images', 'wp-user-frontend' ),
        ],
        'audio'  => [
            'ext' => 'mp3,wav,ogg,wma,mka,m4a,ra,mid,midi',
            'label' => __( 'Audio', 'wp-user-frontend' ),
        ],
        'video'  => [
            'ext' => 'avi,divx,flv,mov,ogv,mkv,mp4,m4v,divx,mpg,mpeg,mpe',
            'label' => __( 'Videos', 'wp-user-frontend' ),
        ],
        'pdf'    => [
            'ext' => 'pdf',
            'label' => __( 'PDF', 'wp-user-frontend' ),
        ],
        'office' => [
            'ext' => 'doc,ppt,pps,xls,mdb,docx,xlsx,pptx,odt,odp,ods,odg,odc,odb,odf,rtf,txt',
            'label' => __( 'Office Documents', 'wp-user-frontend' ),
        ],
        'zip'    => [
            'ext' => 'zip,gz,gzip,rar,7z',
            'label' => __( 'Zip Archives', 'wp-user-frontend' ),
        ],
        'exe'    => [
            'ext' => 'exe',
            'label' => __( 'Executable Files', 'wp-user-frontend' ),
        ],
        'csv'    => [
            'ext' => 'csv',
            'label' => __( 'CSV', 'wp-user-frontend' ),
        ],
    ];

    return apply_filters( 'wpuf_allowed_extensions', $extesions );
}

/**
 * Adds notices on add post form if any
 *
 * @param string $text
 *
 * @return string
 */
function wpuf_addpost_notice( $text ) {
    $user = wp_get_current_user();

    if ( is_user_logged_in() ) {
        $lock = ( 'yes' === $user->wpuf_postlock ) ? 'yes' : 'no';

        if ( 'yes' === $lock ) {
            return $user->wpuf_lock_cause;
        }
    }

    return $text;
}

add_filter( 'wpuf_addpost_notice', 'wpuf_addpost_notice' );

/**
 * Associate attachemnt to a post
 *
 * @since 2.0
 *
 * @param type $attachment_id
 * @param type $post_id
 */
function wpuf_associate_attachment( $attachment_id, $post_id ) {
    $args = [
        'ID'          => $attachment_id,
        'post_parent' => $post_id,
    ];

    wpuf_update_post( $args );
}

/**
 * Update post when hooked to save_post
 *
 * @since 2.5.4
 *
 * @param array args
 */
function wpuf_update_post( $args ) {
    if ( ! wp_is_post_revision( $args['ID'] ) ) {
        // unhook this function so it doesn't loop infinitely
        remove_action( 'save_post', [ WPUF_Admin_Posting::init(), 'save_meta' ], 1 );

        // update the post, which calls save_post again
        wp_update_post( $args );

        // re-hook this function
        add_action( 'save_post', [ WPUF_Admin_Posting::init(), 'save_meta' ], 1 );
    }
}

/**
 * Get user role names
 *
 * @since 2.0
 *
 * @global WP_Roles $wp_roles
 *
 * @return array
 */
function wpuf_get_user_roles() {
    if ( ! function_exists( 'wp_roles' ) ) {
        require_once ABSPATH . WPINC . '/capabilities.php';
        $wp_roles = wp_roles();
    } else {
        $wp_roles = wp_roles();
    }

    return $wp_roles->get_names();
}

/**
 * Add custom avatar image size
 *
 * @since 3.3.0
 *
 * @return void
 */
function wpuf_avatar_add_image_size() {
    $avatar_size   = wpuf_get_option( 'avatar_size', 'wpuf_profile', '100x100' );
    $avatar_size   = explode( 'x', $avatar_size );
    $avatar_width  = $avatar_size[0];
    $avatar_height = $avatar_size[1];

    add_image_size( 'wpuf_avatar_image_size', $avatar_width, $avatar_height, true );
}

/**
 * Custom Avatar uploaded by user
 *
 * @since 3.3.0
 *
 * @param int $user_id
 *
 * @return string
 */
function wpuf_get_custom_avatar( $user_id ) {
    $avatar = get_user_meta( $user_id, 'user_avatar', true );

    if ( absint( $avatar ) > 0 ) {
        wpuf_avatar_add_image_size();

        $avatar_source = wp_get_attachment_image_src( $avatar, 'wpuf_avatar_image_size' );

        if ( $avatar_source ) {
            $avatar = $avatar_source[0];
        }
    }

    return $avatar;
}

/**
 * Conditionally ignore using WPUF avatar
 *
 * @since 3.3.0
 *
 * @return bool
 */
function wpuf_use_default_avatar() {
    if ( has_filter( 'pre_option_show_avatars', '__return_true' ) ) {
        return true;
    }

    return apply_filters( 'wpuf_use_default_avatar', false );
}

/**
 * User avatar wrapper for custom uploaded avatar
 *
 * @since 2.0
 *
 * @param string $avatar
 * @param mixed  $id_or_email
 * @param int    $size
 * @param string $default
 * @param string $alt
 *
 * @return string image tag of the user avatar
 */
function wpuf_get_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
    if ( wpuf_use_default_avatar() ) {
        return $avatar;
    }

    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', $id_or_email );
    } elseif ( is_object( $id_or_email ) ) {
        if ( $id_or_email->user_id !== '0' ) {
            $user = get_user_by( 'id', $id_or_email->user_id );
        } else {
            return $avatar;
        }
    } else {
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( ! $user ) {
        return $avatar;
    }

    $custom_avatar = wpuf_get_custom_avatar( $user->ID );

    if ( empty( $custom_avatar ) ) {
        return $avatar;
    }

    return sprintf( '<img src="%1$s" alt="%2$s" height="%3$s" width="%3$s" class="avatar">', esc_url( $custom_avatar ), $alt, $size );
}

add_filter( 'get_avatar', 'wpuf_get_avatar', 99, 6 );

/**
 * Filters custom avatar url
 *
 * @param $args
 * @param $id_or_email
 *
 * @return mixed
 */
function wpuf_custom_avatar_data( $args, $id_or_email ) {
    if ( wpuf_use_default_avatar() ) {
        return $args;
    }

    $user_id = $id_or_email;

    if ( $id_or_email instanceof WP_Comment ) {
        $user_id = $id_or_email->user_id;
    } elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
        $user_id = email_exists( $id_or_email );
    }

    if ( $user_id ) {
        $custom_avatar_url = wpuf_get_custom_avatar( $user_id );

        if ( ! empty( $custom_avatar_url ) ) {
            $args['url'] = $custom_avatar_url;
        }
    }

    return $args;
}

add_filter( 'get_avatar_data', 'wpuf_custom_avatar_data', 10, 2 );


function wpuf_update_avatar( $user_id, $attachment_id ) {
    $upload_dir   = wp_upload_dir();
    $relative_url = wp_get_attachment_url( $attachment_id );

    if ( function_exists( 'wp_get_image_editor' ) ) {
        // try to crop the photo if it's big
        $file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $relative_url );

        // as the image upload process generated a bunch of images
        // try delete the intermediate sizes.
        $ext             = strrchr( $file_path, '.' );
        $file_path_w_ext = str_replace( $ext, '', $file_path );
        $small_url       = $file_path_w_ext . '-avatar' . $ext;
        $relative_url    = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $small_url );

        $editor = wp_get_image_editor( $file_path );

        if ( ! is_wp_error( $editor ) ) {
            $avatar_size    = wpuf_get_option( 'avatar_size', 'wpuf_profile', '100x100' );
            $avatar_size    = explode( 'x', $avatar_size );
            $avatar_width   = $avatar_size[0];
            $avatar_height  = $avatar_size[1];

            $editor->resize( $avatar_width, $avatar_height, true );
            $editor->save( $small_url );

            // if the file creation successfull, delete the original attachment
            if ( file_exists( $small_url ) ) {
                wp_delete_attachment( $attachment_id, true );
            }
        }
    }

    // delete any previous avatar
    $prev_avatar = get_user_meta( $user_id, 'user_avatar', true );

    if ( ! empty( $prev_avatar ) ) {
        $prev_avatar_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $prev_avatar );

        if ( file_exists( $prev_avatar_path ) ) {
            unlink( $prev_avatar_path );
        }
    }

    // now update new user avatar
    update_user_meta( $user_id, 'user_avatar', $relative_url );
}

function wpuf_admin_role() {
    return apply_filters( 'wpuf_admin_role', 'manage_options' );
}

/**
 * Get all the payment gateways
 *
 * @return array
 */
function wpuf_get_gateways( $context = 'admin' ) {
    $gateways = WPUF_Payment::get_payment_gateways();
    $return   = [];

    foreach ( $gateways as $id => $gate ) {
        if ( 'admin' === $context ) {
            $return[ $id ] = $gate['admin_label'];
        } else {
            $return[ $id ] = [
                'label' => $gate['checkout_label'],
                'icon'  => isset( $gate['icon'] ) ? $gate['icon'] : '',
            ];
        }
    }

    return $return;
}

/**
 * Show custom fields in post content area
 *
 * @since 3.3.0 Introducing `render_field_data` to render field value
 *                   Rendering field values should be in field classes to follow
 *                   more OOP style.
 *
 * @todo Move the rendering snippets to respective field classes. The default case
 *       should be placed in the abstract class.
 *
 * @param string $content
 *
 * @return string
 */
function wpuf_show_custom_fields( $content ) {
    global $post;

    $show_custom = wpuf_get_option( 'cf_show_front', 'wpuf_frontend_posting' );

    if ( 'on' !== $show_custom ) {
        return $content;
    }

    $show_caption  = wpuf_get_option( 'image_caption', 'wpuf_frontend_posting' );
    $form_id       = get_post_meta( $post->ID, '_wpuf_form_id', true );
    $form_settings = wpuf_get_form_settings( $form_id );

    if ( ! $form_id ) {
        return $content;
    }

    $html = '<ul class="wpuf_customs">';

    $form_vars = wpuf_get_form_fields( $form_id );
    $meta      = [];

    if ( $form_vars ) {
        foreach ( $form_vars as $attr ) {
            // get column field input fields
            if ( 'column_field' === $attr['input_type'] ) {
                $inner_fields = $attr['inner_fields'];

                foreach ( $inner_fields as $column_key => $column_fields ) {
                    if ( ! empty( $column_fields ) ) {
                        // ignore section break and HTML input type
                        foreach ( $column_fields as $column_field_key => $column_field ) {
                            if ( isset( $column_field['show_in_post'] ) && 'yes' === $column_field['show_in_post'] ) {
                                $meta[] = $column_field;
                            }
                        }
                    }
                }
                continue;
            }

            if ( isset( $attr['show_in_post'] ) && 'yes' === $attr['show_in_post'] ) {
                $meta[] = $attr;
            }
        }

        if ( ! $meta ) {
            return $content;
        }

        foreach ( $meta as $attr ) {
            $wpuf_field = wpuf()->fields->get_field( $attr['template'] );

            if ( ! isset( $attr['name'] ) ) {
                $attr['name'] = $attr['input_type'];
            }

            $field_value    = get_post_meta( $post->ID, $attr['name'] );
            $hide_label     = isset( $attr['hide_field_label'] ) ? $attr['hide_field_label'] : 'no';

            $return_for_no_cond = 0;

            if ( isset( $attr['wpuf_cond']['condition_status'] ) && 'yes' === $attr['wpuf_cond']['condition_status'] ) {
                foreach ( $attr['wpuf_cond']['cond_field'] as $field_key => $cond_field_name ) {

                    //check if the conditional field is a taxonomy
                    if ( taxonomy_exists( $cond_field_name ) ) {
                        $post_terms       = wp_get_post_terms( $post->ID, $cond_field_name, true );
                        $cond_field_value = [];

                        if ( is_array( $post_terms ) ) {
                            foreach ( $post_terms as $term_key => $term_array ) {
                                $cond_field_value[] = $term_array->term_id;
                            }
                        }
                        $cond_field_value = isset( $post_terms[0] ) ? $post_terms[0]->term_id : '';
                    } else {
                        $cond_field_value = get_post_meta( $post->ID, $cond_field_name, 'true' );
                    }

                    if ( isset( $attr['wpuf_cond']['cond_option'][ $field_key ] ) ) {
                        if ( is_array( $cond_field_value ) ) {
                            continue;
                        } else {
                            if ( (string) $attr['wpuf_cond']['cond_option'][ $field_key ] !== (string) $cond_field_value ) {
                                $return_for_no_cond = 1;
                            } else {
                                $return_for_no_cond = 0;
                                break;
                            }
                        }
                    }
                }
            }

            if ( $return_for_no_cond === 1 ) {
                continue;
            }

            if ( ! count( $field_value ) ) {
                continue;
            }

            if ( 'hidden' === $attr['input_type'] ) {
                continue;
            }

            if ( method_exists( $wpuf_field, 'render_field_data' ) ) {
                $html .= $wpuf_field->render_field_data( $field_value, $attr );
                continue;
            }

            switch ( $attr['input_type'] ) {
                case 'image_upload':
                case 'file_upload':
                    $image_html = '<li style="list-style-type:none;">';

                    if ( 'no' === $hide_label ) {
                        $image_html .= '<label>' . $attr['label'] . ':</label> ';
                    }

                    if ( $field_value ) {
                        if ( is_serialized( $field_value[0] ) ) {
                            $field_value = maybe_unserialize( $field_value[0] );
                        }

                        if ( is_array( $field_value[0] ) ) {
                            $field_value = $field_value[0];
                        }

                        foreach ( $field_value as $attachment_id ) {
                            if ( 'image_upload' === $attr['input_type'] ) {
                                $image_size = wpuf_get_option( 'insert_photo_size', 'wpuf_frontend_posting', 'thumbnail' );
                                $thumb      = wp_get_attachment_image( $attachment_id, $image_size );
                            } else {
                                $thumb = get_post_field( 'post_title', $attachment_id );
                            }

                            $full_size = wp_get_attachment_url( $attachment_id );
                            $path      = parse_url( $full_size, PHP_URL_PATH );
                            $extension = pathinfo( $path, PATHINFO_EXTENSION );

                            if ( $thumb ) {
                                $playable                   = isset( $attr['playable_audio_video'] ) ? $attr['playable_audio_video'] : 'no';
                                $wpuf_allowed_extensions    = wpuf_allowed_extensions();
                                $allowed_audio_extensions   = explode( ',', $wpuf_allowed_extensions['audio']['ext'] );
                                $allowed_video_extensions   = explode( ',', $wpuf_allowed_extensions['video']['ext'] );
                                $allowed_extenstions        = array_merge( $allowed_audio_extensions, $allowed_video_extensions );

                                if ( 'yes' === $playable && in_array( $extension, $allowed_extenstions, true ) ) {
                                    $is_video       = in_array( $extension, $allowed_video_extensions, true );
                                    $is_audio       = in_array( $extension, $allowed_audio_extensions, true );
                                    $preview_width  = isset( $attr['preview_width'] ) ? $attr['preview_width'] : '123';
                                    $preview_height = isset( $attr['preview_height'] ) ? $attr['preview_height'] : '456';

                                    $image_html .= '<div class="wpuf-embed-preview">';

                                    if ( $is_video ) {
                                        $image_html .= '[video src="' . $full_size . '" width="' . $preview_width . '" height="' . $preview_height . '"]';
                                    }

                                    if ( $is_audio ) {
                                        $image_html .= '[audio src="' . $full_size . '" width="' . $preview_width . '" height="' . $preview_height . '"]';
                                    }

                                    $image_html .= '</div>';
                                } else {
                                    $image_html .= sprintf( '<a href="%s">%s</a> ', $full_size, $thumb );
                                }

                                if ( 'on' === $show_caption ) {
                                    $post_detail = get_post( $attachment_id );

                                    if ( ! empty( $post_detail->post_title ) ) {
                                        $image_html .= '<br /><label>' . __( 'Title', 'wp-user-frontend' ) . ':</label> <span class="image_title">' . esc_html( $post_detail->post_title ) . '</span>';
                                    }

                                    if ( ! empty( $post_detail->post_excerpt ) ) {
                                        $image_html .= '<br /><label>' . __( 'Caption', 'wp-user-frontend' ) . ':</label> <span class="image_caption">' . esc_html( $post_detail->post_excerpt ) . '</span>';
                                    }

                                    if ( ! empty( $post_detail->post_content ) ) {
                                        $image_html .= '<br /><label>' . __( 'Description', 'wp-user-frontend' ) . ':</label> <span class="image_description">' . esc_html( $post_detail->post_content ) . '</span>';
                                    }
                                }
                            }
                        }
                    }

                    $html .= $image_html . '</li>';
                    break;

                case 'map':
                    ob_start();
                    wpuf_shortcode_map_post( $attr['name'], $post->ID );

                    if ( isset( $attr['directions'] ) && $attr['directions'] ) {
                        $location   = get_post_meta( $post->ID, $attr['name'], true );
                        $def_lat    = isset( $location['lat'] ) ? $location['lat'] : 40.7143528;
                        $def_long   = isset( $location['lng'] ) ? $location['lng'] : -74.0059731; ?>
                        <div>
                            <a class="btn btn-brand btn-sm" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?php echo esc_attr( $def_lat ); ?>,<?php echo esc_attr( $def_long ); ?>" target="_blank" rel="nofollow external"><?php esc_html_e( 'Directions »', 'wp-user-frontend' ); ?></a>
                        </div>
                        <?php
                    }

                    $html .= ob_get_clean();
                    break;

                case 'address':
                    include_once __DIR__ . '/includes/countries.php';

                    $address_html = '';

                    if ( isset( $field_value[0] ) && is_array( $field_value[0] ) ) {
                        foreach ( $field_value[0] as $field_key => $value ) {
                            if ( 'country_select' === $field_key ) {
                                if ( isset( $countries[ $value ] ) ) {
                                    $value = $countries[ $value ];
                                }
                            }

                            if ( ! empty( $value ) ) {
                                $address_html .= '<li>';

                                if ( 'no' === $hide_label ) {
                                    $address_html .= '<label>' . $attr['address'][ $field_key ]['label'] . ': </label> ';
                                }

                                $address_html .= ' ' . $value . '</li>';
                            }
                        }
                    }

                    $html .= $address_html;
                    break;

                case 'repeat':
                    $value    = get_post_meta( $post->ID, $attr['name'] );
                    $newvalue = [];

                    foreach ( $value as $i => $str ) {
                        if ( preg_match( '/[^\|\s]/', $str ) ) {
                            $newvalue[] = $str;
                        }
                    }

                    $new = implode( ', ', $newvalue );

                    if ( $new ) {
                        $html .= '<li>';

                        if ( 'no' === $hide_label ) {
                            $html .= '<label>' . $attr['label'] . ': </label>';
                        }

                        $html .= make_clickable( $new ) . '</li>';
                    }
                    break;

                case 'url':
                    $value = get_post_meta( $post->ID, $attr['name'], true );

                    if ( empty( $value ) ) {
                        break;
                    }

                    if ( 'embed' === $attr['template'] ) {
                        global $wp_embed;

                        $preview_width  = isset( $attr['preview_width'] ) ? $attr['preview_width'] : '123';
                        $preview_height = isset( $attr['preview_height'] ) ? $attr['preview_height'] : '456';
                        $shortcode      = '[embed width="' . $preview_width . '" height="' . $preview_height . '"]' . $value . '[/embed]';

                        $preview = '<li>';

                        if ( 'no' === $hide_label ) {
                            $preview .= sprintf( '<label>%s: </label>', $attr['label'] );
                        }

                        $preview .= "<div class='wpuf-embed-preview'>";
                        $preview .= $wp_embed->run_shortcode( $shortcode );
                        $preview .= '</div>';
                        $preview .= '</li>';

                        $html .= $preview;
                        break;
                    }

                    $open_in = 'same' === $attr['open_window'] ? '' : '_blank';

                    $link = '<li>';

                    if ( 'no' === $hide_label ) {
                        $link .= '<label>' . $attr['label'] . ':</label>';
                    }

                    $link .= sprintf( " <a href='%s' target = '%s'>%s</a></li>", $value, $open_in, $value );

                    $html .= $link;
                    break;

                case 'date':
                    $value = get_post_meta( $post->ID, $attr['name'], true );

                    $html .= '<li>';

                    if ( 'no' === $hide_label ) {
                        $html .= '<label>' . $attr['label'] . ':</label>';
                    }

                    $html .= sprintf( ' %s</li>', make_clickable( $value ) );
                    break;

                case 'country_list':
                    $value         = get_post_meta( $post->ID, $attr['name'], true );
                    $country_state = new CountryState();
                    $countries     = $country_state->countries();

                    if ( isset( $countries[ $value ] ) ) {
                        $value = $countries[ $value ];
                    }

                    $html .= '<li>';

                    if ( 'no' === $hide_label ) {
                        $html .= '<label>' . $attr['label'] . ':</label>';
                    }

                    $html .= sprintf( ' %s</li>', make_clickable( $value ) );
                    break;

                default:
                    $value       = get_post_meta( $post->ID, $attr['name'] );
                    $filter_html = apply_filters( 'wpuf_custom_field_render', '', $value, $attr, $form_settings );
                    $separator   = ' | ';

                    if ( ! empty( $filter_html ) ) {
                        $html .= $filter_html;
                    } elseif ( is_serialized( $value[0] ) ) {
                        $new            = maybe_unserialize( $value[0] );
                        $modified_value = implode( $separator, $new );

                        if ( $modified_value ) {
                            $html .= '<li>';

                            if ( 'no' === $hide_label ) {
                                $html .= '<label>' . $attr['label'] . ':</label>';
                            }

                            $html .= sprintf( ' %s</li>', make_clickable( $modified_value ) );
                        }
                    } elseif ( ( 'checkbox' === $attr['input_type'] || 'multiselect' === $attr['input_type'] ) && is_array( $value[0] ) ) {
                        if ( ! empty( $value[0] ) ) {
                            $modified_value = implode( $separator, $value[0] );

                            if ( $modified_value ) {
                                $html .= '<li>';

                                if ( 'no' === $hide_label ) {
                                    $html .= '<label>' . $attr['label'] . ':</label>';
                                }

                                $html .= sprintf( ' %s</li>', make_clickable( $modified_value ) );
                            }
                        }
                    } else {
                        $new = implode( ', ', $value );

                        if ( $new ) {
                            $html .= '<li>';

                            if ( 'no' === $hide_label ) {
                                $html .= '<label>' . $attr['label'] . ':</label>';
                            }

                            $html .= sprintf( ' %s</li>', make_clickable( $new ) );
                        }
                    }

                    break;
            }
        }
    }

    $html .= '</ul>';

    return $content . $html;
}

add_filter( 'the_content', 'wpuf_show_custom_fields', 10 );

/**
 * Map display shortcode
 *
 * @param string $meta_key
 * @param int    $post_id
 * @param array  $args
 */
function wpuf_shortcode_map( $location, $post_id = null, $args = [], $meta_key = '' ) {
    if ( ! wpuf()->is_pro() || ! $location ) {
        return;
    }

    global $post;

    // compatibility
    if ( $post_id ) {
        wpuf_shortcode_map_post( $location, $post_id, $args );

        return;
    }

    $default        = [
        'width' => 450,
        'height' => 250,
        'zoom' => 12,
    ];
    $args           = wp_parse_args( $args, $default );

    if ( is_array( $location ) ) {
        $def_address = isset( $location['address'] ) ? $location['address'] : '';
        $def_lat     = isset( $location['lat'] ) ? $location['lat'] : '';
        $def_long    = isset( $location['lng'] ) ? $location['lng'] : '';
        $location    = implode( ' || ', $location );
    } else {
        list( $def_lat, $def_long ) = explode( ',', $location );
        $def_lat                    = $def_lat ? $def_lat : 0;
        $def_long                   = $def_long ? $def_long : 0;
    }
    ?>

    <div class="google-map" style="margin: 10px 0; height: <?php echo esc_attr( $args['height'] ); ?>px; width: <?php echo esc_attr( $args['width'] ); ?>px;" id="wpuf-map-<?php echo esc_attr( $meta_key . $post->ID ); ?>"></div>

    <script type="text/javascript">
        jQuery(function($){
            var curpoint = new google.maps.LatLng(<?php echo esc_html( $def_lat ); ?>, <?php echo esc_html( $def_long ); ?>);

            var gmap = new google.maps.Map( $('#wpuf-map-<?php echo esc_attr( $meta_key . $post->ID ); ?>')[0], {
                center: curpoint,
                zoom: <?php echo esc_attr( $args['zoom'] ); ?>,
                mapTypeId: window.google.maps.MapTypeId.ROADMAP
            });

            var marker = new window.google.maps.Marker({
                position: curpoint,
                map: gmap,
                draggable: true
            });

        });
    </script>
    <?php
}

/**
 * Map shortcode for users
 *
 * @param string $meta_key
 * @param int    $user_id
 * @param array  $args
 */
function wpuf_shortcode_map_user( $meta_key, $user_id = null, $args = [] ) {
    $location = get_user_meta( $user_id, $meta_key, true );
    wpuf_shortcode_map( $location, null, $args, $meta_key );
}

/**
 * Map shortcode post posts
 *
 * @global object $post
 *
 * @param string $meta_key
 * @param int    $post_id
 * @param array  $args
 */
function wpuf_shortcode_map_post( $meta_key, $post_id = null, $args = [] ) {
    global $post;

    if ( ! $post_id ) {
        $post_id = $post->ID;
    }

    $location = get_post_meta( $post_id, $meta_key, true );
    wpuf_shortcode_map( $location, null, $args, $meta_key );
}

function wpuf_meta_shortcode( $atts ) {
    global $post;

    $attrs = shortcode_atts(
        [
            'name'   => '',
            'type'   => 'normal',
            'size'   => 'thumbnail',
            'height' => 250,
            'width'  => 450,
            'zoom'   => 12,
        ], $atts
    );

    $name   = $attrs['name'];
    $type   = $attrs['type'];
    $size   = $attrs['size'];
    $width  = $attrs['width'];
    $height = $attrs['height'];
    $zoom   = $attrs['zoom'];

    if ( empty( $name ) ) {
        return;
    }

    if ( 'image' === $type || 'file' === $type ) {
        $images = get_post_meta( $post->ID, $name, true );

        if ( ! is_array( $images ) ) {
            $images = (array) $images;
        }

        if ( $images ) {
            $html = '';

            foreach ( $images as $attachment_id ) {
                if ( 'image' === $type ) {
                    $thumb = wp_get_attachment_image( $attachment_id, $size );
                } else {
                    $thumb = get_post_field( 'post_title', $attachment_id );
                }

                $full_size = wp_get_attachment_url( $attachment_id );
                $html      .= sprintf( '<a href="%s">%s</a> ', $full_size, $thumb );
            }

            return $html;
        }
    } elseif ( 'map' === $type ) {
        ob_start();
        wpuf_shortcode_map(
            $name, $post->ID, [
                'width' => $width,
                'height' => $height,
                'zoom' => $zoom,
            ]
        );

        return ob_get_clean();
    } elseif ( 'repeat' === $type ) {
        return implode( '; ', get_post_meta( $post->ID, $name ) );
    } elseif ( 'normal' === $type ) {
        return implode( ', ', get_post_meta( $post->ID, $name ) );
    } else {
        return make_clickable( implode( ', ', get_post_meta( $post->ID, $name ) ) );
    }
}

add_shortcode( 'wpuf-meta', 'wpuf_meta_shortcode' );

/**
 * Get the value of a settings field
 *
 * @param string $option  settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function wpuf_get_option( $option, $section, $default = '' ) {
    $options = get_option( $section );

    if ( isset( $options[ $option ] ) ) {
        return $options[ $option ];
    }

    return $default;
}

/**
 * Check the current post for the existence of a short code
 *
 * @see http://wp.tutsplus.com/articles/quick-tip-improving-shortcodes-with-the-has_shortcode-function/
 *
 * @param string $shortcode
 *
 * @return bool
 */
function wpuf_has_shortcode( $shortcode = '', $post_id = false ) {
    $post_to_check = ( false === $post_id ) ? get_post( get_the_ID() ) : get_post( $post_id );

    if ( ! $post_to_check ) {
        return false;
    }

    // false because we have to search through the post content first
    $found = false;

    // if no short code was provided, return false
    if ( ! $shortcode ) {
        return $found;
    }

    // check the post content for the short code
    if ( stripos( $post_to_check->post_content, '[' . $shortcode ) !== false ) {
        // we have found the short code
        $found = true;
    }

    return $found;
}

/**
 * Get attachment ID from a URL
 *
 * @since 2.1.8
 * @see http://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/ Original Implementation
 *
 * @global type $wpdb
 *
 * @param type $attachment_url
 *
 * @return type
 */
function wpuf_get_attachment_id_from_url( $attachment_url = '' ) {
    global $wpdb;

    $attachment_id = false;

    // If there is no url, return.
    if ( '' === $attachment_url ) {
        return;
    }

    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();

    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

        // Remove the upload path base directory from the attachment URL
        $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

        // Finally, run a custom database query to get the attachment ID from the modified attachment URL
        $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = %s AND wposts.post_type = 'attachment'", $attachment_url ) );
    }

    return $attachment_id;
}

/**
 * Non logged in users tag autocomplete
 *
 * @since 2.1.9
 *
 * @global object $wpdb
 */
function wpufe_ajax_tag_search() {
    global $wpdb;

    $taxonomy = isset( $_GET['tax'] ) ? sanitize_text_field( wp_unslash( $_GET['tax'] ) ) : '';
    $term_ids = isset( $_GET['term_ids'] ) ? sanitize_text_field( wp_unslash( $_GET['term_ids'] ) ) : '';
    $tax      = get_taxonomy( $taxonomy );

    if ( ! $tax ) {
        wp_die( 0 );
    }

    $s = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

    $comma = _x( ',', 'tag delimiter', 'wp-user-frontend' );

    if ( ',' !== $comma ) {
        $s = str_replace( $comma, ',', $s );
    }

    if ( false !== strpos( $s, ',' ) ) {
        $s = explode( ',', $s );
        $s = $s[ count( $s ) - 1 ];
    }

    $s = trim( $s );

    if ( strlen( $s ) < 2 ) {
        wp_die();
    } // require 2 chars for matching

    if ( ! empty( $term_ids ) ) {
        //phpcs:disable
        $results = $wpdb->get_col( $wpdb->prepare( "SELECT t.name FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.term_id IN ($term_ids) AND t.name LIKE (%s)", $taxonomy, '%' . $wpdb->esc_like( $s ) . '%' ) );
    } else {
        $results = $wpdb->get_col( $wpdb->prepare( "SELECT t.name FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.name LIKE (%s)", $taxonomy, '%' . $wpdb->esc_like( $s ) . '%' ) );
    }
    echo esc_html( join( $results, "\n" ) );
    wp_die();
}

add_action( 'wp_ajax_wpuf-ajax-tag-search', 'wpufe_ajax_tag_search' );
add_action( 'wp_ajax_nopriv_wpuf-ajax-tag-search', 'wpufe_ajax_tag_search' );

/**
 * Option dropdown helper
 *
 * @param array  $options
 * @param string $selected
 *
 * @return string
 */
function wpuf_dropdown_helper( $options, $selected = '' ) {
    $string = '';

    foreach ( $options as $key => $label ) {
        $string .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $selected, $key, false ), $label );
    }

    return $string;
}

/**
 * Include a template file
 *
 * Looks up first on the theme directory, if not found
 * lods from plugins folder
 *
 * @since 2.2
 *
 * @param string $file file name or path to file
 */
function wpuf_load_template( $file, $args = [] ) {
    //phpcs:ignore
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $child_theme_dir  = get_stylesheet_directory() . '/wpuf/';
    $parent_theme_dir = get_template_directory() . '/wpuf/';
    $wpuf_dir         = WPUF_ROOT . '/templates/';

    if ( file_exists( $child_theme_dir . $file ) ) {
        include $child_theme_dir . $file;
    } elseif ( file_exists( $parent_theme_dir . $file ) ) {
        include $parent_theme_dir . $file;
    } else {
        include $wpuf_dir . $file;
    }
}

/**
 * Include a template file
 *
 * Looks up first on the theme directory, if not found
 * lods from pro plugin folder
 *
 * @since 3.1.11
 *
 * @param string $file file name or path to file
 */
function wpuf_load_pro_template( $file, $args = [] ) {
    //phpcs:ignore
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    if ( wpuf()->is_pro() ) {
        $child_theme_dir    = get_stylesheet_directory() . '/wpuf/';
        $parent_theme_dir   = get_template_directory() . '/wpuf/';
        $wpuf_pro_dir       = WPUF_PRO_INCLUDES . '/templates/';

        if ( file_exists( $child_theme_dir . $file ) ) {
            include $child_theme_dir . $file;
        } elseif ( file_exists( $parent_theme_dir . $file ) ) {
            include $parent_theme_dir . $file;
        } else {
            include $wpuf_pro_dir . $file;
        }
    }
}

/**
 * Helper function for formatting date field
 *
 * @since 0.1
 *
 * @param string $date
 * @param bool   $show_time
 *
 * @return string
 */
function wpuf_get_date( $date, $show_time = false, $format = false ) {
    if ( empty( $date ) ) {
        return $date;
    }

    $timestamp = strtotime( $date );

    if ( $format ) {
        $dateobj = DateTime::createFromFormat( $format, $date );

        if ( $dateobj ) {
            $timestamp = $dateobj->getTimestamp();
        }
    }

    $format = get_option( 'date_format' );

    if ( $show_time ) {
        $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
    }

    return date_i18n( $format, $timestamp );
}

/**
 * Helper function for converting a normal date string to unix date/time string
 *
 * @since 0.1
 *
 * @param string $date
 * @param int    $gmt
 *
 * @return string
 */
function wpuf_date2mysql( $date, $gmt = 0 ) {
    if ( empty( $date ) ) {
        return;
    }
    $time = strtotime( $date );

    return ( $gmt ) ? gmdate( 'Y-m-d H:i:s', $time ) : gmdate( 'Y-m-d H:i:s', ( $time + ( intval( get_option( 'timezone_string' ) ) * 3600 ) ) );
}

/**
 * Get form fields from a form
 *
 * @param int $form_id
 *
 * @return array
 */
function wpuf_get_form_fields( $form_id ) {
    $fields = get_children(
        [
            'post_parent' => $form_id,
            'post_status' => 'publish',
            'post_type'   => 'wpuf_input',
            'numberposts' => '-1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ]
    );

    $form_fields = [];

    foreach ( $fields as $key => $content ) {
        $field = maybe_unserialize( $content->post_content );

        $field['id'] = $content->ID;

        // Add inline property for radio and checkbox fields
        $inline_supported_fields = [ 'radio', 'checkbox' ];

        if ( in_array( $field['input_type'], $inline_supported_fields, true ) ) {
            if ( ! isset( $field['inline'] ) ) {
                $field['inline'] = 'no';
            }
        }

        // Add 'selected' property
        $option_based_fields = [ 'select', 'multiselect', 'radio', 'checkbox' ];

        if ( in_array( $field['input_type'], $option_based_fields, true ) ) {
            if ( ! isset( $field['selected'] ) ) {
                if ( 'select' === $field['input_type'] || 'radio' === $field['input_type'] ) {
                    $field['selected'] = '';
                } else {
                    $field['selected'] = [];
                }
            }
        }

        // Add 'multiple' key for input_type:repeat
        if ( 'repeat' === $field['input_type'] && ! isset( $field['multiple'] ) ) {
            $field['multiple'] = '';
        }

        if ( 'recaptcha' === $field['input_type'] ) {
            $field['name']              = 'recaptcha';
            $field['enable_no_captcha'] = isset( $field['enable_no_captcha'] ) ? $field['enable_no_captcha'] : '';
        }

        $form_fields[] = apply_filters( 'wpuf-get-form-fields', $field );
    }

    return $form_fields;
}

add_action( 'wp_ajax_wpuf_get_child_cat', 'wpuf_get_child_cats' );
add_action( 'wp_ajax_nopriv_wpuf_get_child_cat', 'wpuf_get_child_cats' );

/**
 * Returns child category dropdown on ajax request
 */
function wpuf_get_child_cats() {
    $nonce = isset( $_REQUEST['nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['nonce'] ) ) : '';

    $parent_cat  = isset( $_POST['catID'] ) ? sanitize_text_field( wp_unslash( $_POST['catID'] ) ) : '';
    $field_attr = isset( $_POST['field_attr'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['field_attr'] ) ) : [];

    wp_verify_nonce( $nonce, 'wpuf_nonce' );

    $allowed_tags = wp_kses_allowed_html( 'post' );

    $taxonomy = $field_attr['name'];

    $terms  = null;
    $result = '';

    if ( $parent_cat < 1 ) {
        die( wp_kses( $result, $allowed_tags ) );
    }

    $terms = get_categories( 'taxonomy=' . $taxonomy . '&child_of=' . $parent_cat . '&hide_empty=0' );

    if ( $terms ) {
        $field_attr['parent_cat'] = $parent_cat;

        if ( is_array( $terms ) ) {
            foreach ( $terms as $key => $term ) {
                $terms[ $key ] = (array) $term;
            }
        }

        $field_attr['form_id'] = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;

        $result .= taxnomy_select( '', $field_attr );
    } else {
        die( '' );
    }
    die( wp_kses( $result, $allowed_tags ) );
}

function taxnomy_select( $terms, $attr ) {
    $selected           = $terms ? $terms : '';
    $taxonomy           = $attr['name'];
    $class              = ' wpuf_' . $attr['name'] . '_' . $attr['form_id'];
    $exclude_type       = isset( $attr['exclude_type'] ) ? $attr['exclude_type'] : 'exclude';
    $exclude            = isset( $attr['exclude'] ) ? $attr['exclude'] : '';

    $dataset = sprintf(
        'data-required="%s" data-type="select" data-form-id="%d"',
        $attr['required'],
        $attr['form_id']
    );

    if ( 'child_of' === $exclude_type && ! empty( $exclude ) ) {
        $exclude = $exclude[0];
    }

    $tax_args = [
        'show_option_none' => __( '&mdash; Select &mdash;', 'wp-user-frontend' ),
        'hierarchical'     => 1,
        'hide_empty'       => 0,
        'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
        'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
        'name'             => $taxonomy . '[]',
        'taxonomy'         => $taxonomy,
        'echo'             => 0,
        'title_li'         => '',
        'class'            => 'cat-ajax ' . $taxonomy . $class,
        $exclude_type      => $exclude,
        'selected'         => $selected,
        'depth'            => 1,
        'child_of'         => isset( $attr['parent_cat'] ) ? $attr['parent_cat'] : '',
    ];

    $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );

    $select = wp_dropdown_categories( $tax_args );

    echo str_replace( '<select', '<select ' . $dataset, $select ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    $attr = [
        'required'     => $attr['required'],
        'name'         => $attr['name'],
        'exclude_type' => $attr['exclude_type'],
        'exclude'      => isset( $attr['exclude'] ) ? $attr['exclude'] : '',
        'orderby'      => $attr['orderby'],
        'order'        => $attr['order'],
        //'last_term_id' => isset( $attr['parent_cat'] ) ? $attr['parent_cat'] : '',
        //'term_id'      => $selected
    ];
    $attr = apply_filters( 'wpuf_taxonomy_checklist_args', $attr );
    ?>
    <span data-taxonomy=<?php echo json_encode( $attr ); ?>></span>
    <?php
}

/**
 * Returns form setting value
 *
 * @param init   $form_id
 * @param boolen $status
 *
 * @return array
 */
function wpuf_get_form_settings( $form_id, $status = true ) {
    return get_post_meta( $form_id, 'wpuf_form_settings', $status );
}

/**
 * Get form notifications
 *
 * @since 2.5.2
 *
 * @param int $form_id
 *
 * @return array
 */
function wpuf_get_form_notifications( $form_id ) {
    $notifications = get_post_meta( $form_id, 'notifications', true );

    if ( ! $notifications ) {
        return [];
    }

    return $notifications;
}

/**
 * Get form integration settings
 *
 * @since 2.5.4
 *
 * @param int $form_id
 *
 * @return array
 */
function wpuf_get_form_integrations( $form_id ) {
    $integrations = get_post_meta( $form_id, 'integrations', true );

    if ( ! $integrations ) {
        return [];
    }

    return $integrations;
}

/**
 * Check if an integration is active
 *
 * @since 2.5.4
 *
 * @param int    $form_id
 * @param string $integration_id
 *
 * @return bool
 */
function wpuf_is_integration_active( $form_id, $integration_id ) {
    $integrations = wpuf_get_form_integrations( $form_id );

    if ( ! $integrations ) {
        return false;
    }

    foreach ( $integrations as $id => $integration ) {
        if ( $integration_id === $id && $integration->enabled === true ) {
            return $integration;
        }
    }

    return false;
}

/**
 * Get the subscription page url
 *
 * @return string
 */
function wpuf_get_subscription_page_url() {
    $page_id = wpuf_get_option( 'subscription_page', 'wpuf_payment' );

    return get_permalink( $page_id );
}

/**
 * Clear the buffer
 *
 * Prevents ajax breakage and endless loading icon. A LIFE SAVER!!!
 *
 * @return void
 */
function wpuf_clear_buffer() {
    ob_clean();
}

/**
 * Check if the license has been expired
 *
 * @since 2.3.13
 *
 * @return bool
 */
function wpuf_is_license_expired() {
    $remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

    if ( in_array( $remote_addr, [ '127.0.0.1', '::1' ], true ) ) {
        return false;
    }

    $license_status = get_option( 'wpuf_license_status' );

    // seems like this wasn't activated at all
    if ( ! isset( $license_status->update ) ) {
        return false;
    }

    // if license has expired more than 15 days ago
    $update    = strtotime( $license_status->update );
    $threshold = strtotime( '+15 days', $update );

    // printf( 'Validity: %s, Threshold: %s', date( 'd-m-Y', $update), date( 'd-m-Y', $threshold ) );

    if ( time() >= $threshold ) {
        return true;
    }

    return false;
}

/**
 * Get post form templates
 *
 * @since 2.4
 *
 * @return array
 */
function wpuf_get_post_form_templates() {
    require_once WPUF_ROOT . '/class/post-form-templates/post.php';

    $integrations                                 = [];
    $integrations['WPUF_Post_Form_Template_Post'] = new WPUF_Post_Form_Template_Post();

    return apply_filters( 'wpuf_get_post_form_templates', $integrations );
}

/**
 * Get countries
 *
 * @since 2.4.1
 *
 * @param string $type (optional)
 *
 * @return array|string
 */
function wpuf_get_countries( $type = 'array' ) {
    $countries = include __DIR__ . '/includes/countries-formated.php';

    if ( 'json' === $type ) {
        $countries = json_encode( $countries );
    }

    return $countries;
}

/**
 * Get account dashboard's sections
 *
 * @since 2.4.2
 *
 * @return array
 */
function wpuf_get_account_sections() {
    $sections = [
        'edit-profile'    => __( 'Edit Profile', 'wp-user-frontend' ),
        'subscription'    => __( 'Subscription', 'wp-user-frontend' ),
        'billing-address' => __( 'Billing Address', 'wp-user-frontend' ),
    ];

    $post_types   = wpuf_get_option( 'cp_on_acc_page', 'wpuf_my_account', [ 'post' ] );
    $cpt_sections = [];

    if ( is_array( $post_types ) && $post_types ) {
        foreach ( $post_types as $post_type ) {
            $post_type_object = get_post_type_object( $post_type );

            $cpt_sections[ $post_type ] = $post_type_object->label;
        }
    }

    $sections = array_merge(
    // dashboard should be the first item
        [ 'dashboard' => __( 'Dashboard', 'wp-user-frontend' ) ],
        $cpt_sections,
        $sections
    );

    return apply_filters( 'wpuf_account_sections', $sections );
}

/**
 * Get account dashboard's sections in a list array
 *
 * @since 2.4.2
 *
 * @return array
 */
function wpuf_get_account_sections_list( $post_type = 'page' ) {
    $sections = wpuf_get_account_sections();
    $array    = [ '' => __( '&mdash; Select &mdash;', 'wp-user-frontend' ) ];

    if ( $sections ) {
        foreach ( $sections as $section => $label ) {
            $array[ $section ] = esc_attr( $label );
        }
    }

    return $array;
}
/**
 * Get all transactions
 *
 * @since 2.4.2
 *
 * @return array
 */
function wpuf_get_transactions( $args = [] ) {
    global $wpdb;

    $defaults = [
        'number'  => 20,
        'offset'  => 0,
        'orderby' => 'id',
        'order'   => 'DESC',
        'count'   => false,
    ];

    $args = wp_parse_args( $args, $defaults );

    if ( $args['count'] ) {
        return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wpuf_transaction" );
    }
    //phpcs:ignore
    $result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpuf_transaction ORDER BY `{$args['orderby']}` {$args['order']} LIMIT {$args['offset']}, {$args['number']}", OBJECT );

    return $result;
}

/**
 * Get all pending transactions
 *
 * @since 2.4.2
 *
 * @return array
 */
function wpuf_get_pending_transactions( $args = [] ) {
    global $wpdb;

    $defaults = [
        'number'  => 20,
        'offset'  => 0,
        'orderby' => 'id',
        'order'   => 'DESC',
        'count'   => false,
    ];

    $args = wp_parse_args( $args, $defaults );

    $pending_args = [
        'post_type'      => 'wpuf_order',
        'post_status'    => [ 'publish', 'pending' ],
        'posts_per_page' => $args['number'],
        'offset'         => $args['offset'],
        'orderby'        => $args['orderby'],
        'order'          => $args['order'],
    ];

    $wpuf_order_query = new WP_Query( $pending_args );

    if ( $args['count'] ) {
        return $wpuf_order_query->found_posts;
    }

    $transactions = $wpuf_order_query->get_posts();

    $items = [];

    foreach ( $transactions as $transaction ) {
        $info = get_post_meta( $transaction->ID, '_data', true );

        $items[] = (object) [
            'id'               => $transaction->ID,
            'user_id'          => $info['user_info']['id'],
            'status'           => 'pending',
            'cost'             => $info['price'],
            'tax'              => isset( $info['tax'] ) ? $info['tax'] : 0,
            'post_id'          => ( $info['type'] === 'post' ) ? $info['item_number'] : 0,
            'pack_id'          => ( $info['type'] === 'pack' ) ? $info['item_number'] : 0,
            'payer_first_name' => $info['user_info']['first_name'],
            'payer_last_name'  => $info['user_info']['last_name'],
            'payer_email'      => $info['user_info']['email'],
            'payment_type'     => ( $info['post_data']['wpuf_payment_method'] === 'bank' ) ? 'Bank/Manual' : ucwords( $info['post_data']['wpuf_payment_method'] ),
            'transaction_id'   => 0,
            'created'          => $info['date'],
        ];
    }

    wp_reset_postdata();

    return $items;
}

/**
 * Get full list of currency codes.
 *
 * @since 2.4.2
 *
 * @return array
 */
function wpuf_get_currencies() {
    $currencies = [
        [
            'currency' => 'AED',
            'label' => __( 'United Arab Emirates Dirham', 'wp-user-frontend' ),
            'symbol' => 'د.إ',
        ],
        [
            'currency' => 'AUD',
            'label' => __( 'Australian Dollars', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'AZD',
            'label' => __( 'Argentine Peso', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'BDT',
            'label' => __( 'Bangladeshi Taka', 'wp-user-frontend' ),
            'symbol' => '&#2547;',
        ],
        [
            'currency' => 'BRL',
            'label' => __( 'Brazilian Real', 'wp-user-frontend' ),
            'symbol' => '&#82;&#36;',
        ],
        [
            'currency' => 'BGN',
            'label' => __( 'Bulgarian Lev', 'wp-user-frontend' ),
            'symbol' => '&#1083;&#1074;.',
        ],
        [
            'currency' => 'CAD',
            'label' => __( 'Canadian Dollars', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'CLP',
            'label' => __( 'Chilean Peso', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'CNY',
            'label' => __( 'Chinese Yuan', 'wp-user-frontend' ),
            'symbol' => '&yen;',
        ],
        [
            'currency' => 'COP',
            'label' => __( 'Colombian Peso', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'CZK',
            'label' => __( 'Czech Koruna', 'wp-user-frontend' ),
            'symbol' => '&#75;&#269;',
        ],
        [
            'currency' => 'DKK',
            'label' => __( 'Danish Krone', 'wp-user-frontend' ),
            'symbol' => 'kr.',
        ],
        [
            'currency' => 'DOP',
            'label' => __( 'Dominican Peso', 'wp-user-frontend' ),
            'symbol' => 'RD&#36;',
        ],
        [
            'currency' => 'DZD',
            'label' => __( 'Algerian Dinar', 'wp-user-frontend' ),
            'symbol' => 'DA;',
        ],
        [
            'currency' => 'EUR',
            'label' => __( 'Euros', 'wp-user-frontend' ),
            'symbol' => '&euro;',
        ],
        [
            'currency' => 'HKD',
            'label' => __( 'Hong Kong Dollar', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'HRK',
            'label' => __( 'Croatia kuna', 'wp-user-frontend' ),
            'symbol' => 'Kn',
        ],
        [
            'currency' => 'HUF',
            'label' => __( 'Hungarian Forint', 'wp-user-frontend' ),
            'symbol' => '&#70;&#116;',
        ],
        [
            'currency' => 'ISK',
            'label' => __( 'Icelandic krona', 'wp-user-frontend' ),
            'symbol' => 'Kr.',
        ],
        [
            'currency' => 'IDR',
            'label' => __( 'Indonesia Rupiah', 'wp-user-frontend' ),
            'symbol' => 'Rp',
        ],
        [
            'currency' => 'INR',
            'label' => __( 'Indian Rupee', 'wp-user-frontend' ),
            'symbol' => 'Rs',
        ],
        [
            'currency' => 'MUR',
            'label' => __( 'Mauritian Rupee', 'wp-user-frontend' ),
            'symbol' => '&#8377;',
        ],
        [
            'currency' => 'NPR',
            'label' => __( 'Nepali Rupee', 'wp-user-frontend' ),
            'symbol' => 'Rs.',
        ],
        [
            'currency' => 'ILS',
            'label' => __( 'Israeli Shekel', 'wp-user-frontend' ),
            'symbol' => '&#8362;',
        ],
        [
            'currency' => 'JPY',
            'label' => __( 'Japanese Yen', 'wp-user-frontend' ),
            'symbol' => '&yen;',
        ],
        [
            'currency' => 'KIP',
            'label' => __( 'Lao Kip', 'wp-user-frontend' ),
            'symbol' => '&#8365;',
        ],
        [
            'currency' => 'KRW',
            'label' => __( 'South Korean Won', 'wp-user-frontend' ),
            'symbol' => '&#8361;',
        ],
        [
            'currency' => 'MYR',
            'label' => __( 'Malaysian Ringgits', 'wp-user-frontend' ),
            'symbol' => '&#82;&#77;',
        ],
        [
            'currency' => 'MXN',
            'label' => __( 'Mexican Peso', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'NGN',
            'label' => __( 'Nigerian Naira', 'wp-user-frontend' ),
            'symbol' => '&#8358;',
        ],
        [
            'currency' => 'NOK',
            'label' => __( 'Norwegian Krone', 'wp-user-frontend' ),
            'symbol' => '&#107;&#114;',
        ],
        [
            'currency' => 'NZD',
            'label' => __( 'New Zealand Dollar', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'NAD',
            'label' => __( 'Namibian dollar', 'wp-user-frontend' ),
            'symbol' => 'N&#36;',
        ],
        [
            'currency' => 'OMR',
            'label' => __( 'Omani Rial', 'wp-user-frontend' ),
            'symbol' => 'ر.ع.',
        ],
        [
            'currency' => 'IRR',
            'label' => __( 'Iranian Rial', 'wp-user-frontend' ),
            'symbol' => '﷼',
        ],
        [
            'currency' => 'PKR',
            'label' => __( 'Pakistani Rupee', 'wp-user-frontend' ),
            'symbol' => 'Rs',
        ],
        [
            'currency' => 'PYG',
            'label' => __( 'Paraguayan Guaraní', 'wp-user-frontend' ),
            'symbol' => '&#8370;',
        ],
        [
            'currency' => 'PHP',
            'label' => __( 'Philippine Pesos', 'wp-user-frontend' ),
            'symbol' => '&#8369;',
        ],
        [
            'currency' => 'PLN',
            'label' => __( 'Polish Zloty', 'wp-user-frontend' ),
            'symbol' => '&#122;&#322;',
        ],
        [
            'currency' => 'GBP',
            'label' => __( 'Pounds Sterling', 'wp-user-frontend' ),
            'symbol' => '&pound;',
        ],
        [
            'currency' => 'RON',
            'label' => __( 'Romanian Leu', 'wp-user-frontend' ),
            'symbol' => 'lei',
        ],
        [
            'currency' => 'RUB',
            'label' => __( 'Russian Ruble', 'wp-user-frontend' ),
            'symbol' => '&#1088;&#1091;&#1073;.',
        ],
        [
            'currency' => 'SR',
            'label' => __( 'Saudi Riyal', 'wp-user-frontend' ),
            'symbol' => 'SR',
        ],
        [
            'currency' => 'SGD',
            'label' => __( 'Singapore Dollar', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'ZAR',
            'label' => __( 'South African rand', 'wp-user-frontend' ),
            'symbol' => '&#82;',
        ],
        [
            'currency' => 'SEK',
            'label' => __( 'Swedish Krona', 'wp-user-frontend' ),
            'symbol' => '&#107;&#114;',
        ],
        [
            'currency' => 'CHF',
            'label' => __( 'Swiss Franc', 'wp-user-frontend' ),
            'symbol' => '&#67;&#72;&#70;',
        ],
        [
            'currency' => 'TWD',
            'label' => __( 'Taiwan New Dollars', 'wp-user-frontend' ),
            'symbol' => '&#78;&#84;&#36;',
        ],
        [
            'currency' => 'THB',
            'label' => __( 'Thai Baht', 'wp-user-frontend' ),
            'symbol' => '&#3647;',
        ],
        [
            'currency' => 'TRY',
            'label' => __( 'Turkish Lira', 'wp-user-frontend' ),
            'symbol' => '&#8378;',
        ],
        [
            'currency' => 'TTD',
            'label' => __( 'Trinidad and Tobago Dollar', 'wp-user-frontend' ),
            'symbol' => '&#84;&#84;&#36;',
        ],
        [
            'currency' => 'USD',
            'label' => __( 'US Dollar', 'wp-user-frontend' ),
            'symbol' => '&#36;',
        ],
        [
            'currency' => 'VND',
            'label' => __( 'Vietnamese Dong', 'wp-user-frontend' ),
            'symbol' => '&#8363;',
        ],
        [
            'currency' => 'EGP',
            'label' => __( 'Egyptian Pound', 'wp-user-frontend' ),
            'symbol' => 'EGP',
        ],
        [
            'currency' => 'JOD',
            'label' => __( 'Jordanian dinar', 'wp-user-frontend' ),
            'symbol' => 'د.أ',
        ],
    ];

    return apply_filters( 'wpuf_currencies', $currencies );
}

/**
 * Get global currency
 *
 * @since 2.4.2
 *
 * @param string $type
 *
 * @return mixed
 */
function wpuf_get_currency( $type = '' ) {
    $currency_code = wpuf_get_option( 'currency', 'wpuf_payment', 'USD' );

    if ( 'code' === $type ) {
        return $currency_code;
    }

    $currencies = wpuf_get_currencies();
    $index      = array_search( $currency_code, array_column( $currencies, 'currency' ), true );
    $currency   = $currencies[ $index ];

    if ( 'symbol' === $type ) {
        return $currency['symbol'];
    }

    return $currency;
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function get_wpuf_price_format() {
    $currency_pos = wpuf_get_option( 'currency_position', 'wpuf_payment', 'left' );
    $format       = '%1$s%2$s';

    switch ( $currency_pos ) {
        case 'left':
            $format = '%1$s%2$s';
            break;

        case 'right':
            $format = '%2$s%1$s';
            break;

        case 'left_space':
            $format = '%1$s&nbsp;%2$s';
            break;

        case 'right_space':
            $format = '%2$s&nbsp;%1$s';
            break;
    }

    return apply_filters( 'wpuf_price_format', $format, $currency_pos );
}

/**
 * Return the thousand separator for prices.
 *
 * @since  2.4.4
 *
 * @return string
 */
function wpuf_get_price_thousand_separator() {
    $separator = stripslashes( wpuf_get_option( 'wpuf_price_thousand_sep', 'wpuf_payment', ',' ) );

    return $separator;
}

/**
 * Return the decimal separator for prices.
 *
 * @since  2.4.4
 *
 * @return string
 */
function wpuf_get_price_decimal_separator() {
    $separator = stripslashes( wpuf_get_option( 'wpuf_price_decimal_sep', 'wpuf_payment', '.' ) );

    return $separator;
}

/**
 * Return the number of decimals after the decimal point.
 *
 * @since  2.4.4
 *
 * @return int
 */
function wpuf_get_price_decimals() {
    return absint( wpuf_get_option( 'wpuf_price_num_decimals', 'wpuf_payment', 2 ) );
}

/**
 * Trim trailing zeros off prices.
 *
 * @param mixed $price
 *
 * @return string
 */
function wpuf_trim_zeros( $price ) {
    return preg_replace( '/' . preg_quote( wc_get_price_decimal_separator(), '/' ) . '0++$/', '', $price );
}

/**
 * Format the pricing number
 *
 * @since 2.4.2
 *
 * @param number $number
 * @param  array
 *
 * @return mixed
 */
function wpuf_format_price( $price, $formated = true, $args = [] ) {

    $price_args = apply_filters(
        'wpuf_price_args', wp_parse_args(
            $args, [
                'currency'           => $formated ? wpuf_get_currency( 'symbol' ) : '',
                'decimal_separator'  => wpuf_get_price_decimal_separator(),
                'thousand_separator' => $formated ? wpuf_get_price_thousand_separator() : '',
                'decimals'           => wpuf_get_price_decimals(),
                'price_format'       => get_wpuf_price_format(),
            ]
        )
    );

    $currency = $price_args['currency'];
    $decimal_separator = $price_args['decimal_separator'];
    $thousand_separator = $price_args['thousand_separator'];
    $decimals = $price_args['decimals'];
    $price_format = $price_args['price_format'];
    $negative        = $price < 0;
    $price           = apply_filters( 'wpuf_raw_price', floatval( $negative ? $price * -1 : $price ) );
    $price           = apply_filters( 'wpuf_formatted_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

    if ( apply_filters( 'wpuf_price_trim_zeros', false ) && $decimals > 0 ) {
        $price = wpuf_trim_zeros( $price );
    }

    $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, $currency, $price );

    return apply_filters( 'wpuf_format_price', $formatted_price, $price, $args );
}

/*
 * Polyfill of array_column function
 *
 * @since 2.4.3
 */
if ( ! function_exists( 'array_column' ) ) {
    function array_column( $input, $column_key, $index_key = null ) {
        $result = [];

        foreach ( $input as $k => $v ) {
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
        }

        return $result;
    }
}

/**
 * API to duplicate a form
 *
 * @since 2.5
 *
 * @param int $post_id
 *
 * @return int New duplicated form id
 */
function wpuf_duplicate_form( $post_id ) {
    $post = get_post( $post_id );

    if ( ! $post ) {
        return;
    }

    $contents = wpuf_get_form_fields( $post_id );

    $new_form = [
        'post_title'  => $post->post_title,
        'post_type'   => $post->post_type,
        'post_status' => 'draft',
    ];

    $form_id = wp_insert_post( $new_form );

    foreach ( $contents as $content ) {
        wpuf_insert_form_field( $form_id, $content );
    }

    // update the post title to remove confusion
    wp_update_post(
        [
            'ID'         => $form_id,
            'post_title' => $post->post_title . ' (#' . $form_id . ')',
        ]
    );

    if ( $form_id ) {
        $form_settings = wpuf_get_form_settings( $post_id );
        $notifications = wpuf_get_form_notifications( $post_id );

        update_post_meta( $form_id, 'wpuf_form_settings', $form_settings );
        update_post_meta( $form_id, 'notifications', $notifications );

        return $form_id;
    }

    return 0;
}

/**
 * Save form fields
 *
 * @since 2.5
 *
 * @param int   $form_id
 * @param array $field
 * @param int   $field_id
 * @param int   $order
 *
 * @return int ID of updated or inserted post
 */
function wpuf_insert_form_field( $form_id, $field = [], $field_id = null, $order = 0 ) {
    $args = [
        'post_type'    => 'wpuf_input',
        'post_parent'  => $form_id,
        'post_status'  => 'publish',
        'post_content' => maybe_serialize( wp_unslash( $field ) ),
        'menu_order'   => $order,
    ];

    if ( $field_id ) {
        $args['ID'] = $field_id;
    }

    if ( $field_id ) {
        return wp_update_post( $args );
    } else {
        return wp_insert_post( $args );
    }
}

/**
 * Create a sample / base form
 *
 * @since  2.5
 *
 * @param string $post_title (optional)
 * @param string $post_type  (optional)
 * @param bool   $blank      (optional)
 *
 * @return int
 */
function wpuf_create_sample_form( $post_title = 'Sample Form', $post_type = 'wpuf_forms', $blank = false ) {
    $form_id = wp_insert_post(
        [
            'post_title'     => $post_title,
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'comment_status' => 'closed',
            'post_content'   => '',
        ]
    );

    if ( ! $form_id ) {
        return false;
    }

    $form_fields = [];
    $settings    = [];

    // Post form
    if ( 'wpuf_forms' === $post_type ) {
        $form_fields = [
            [
                'input_type'  => 'text',
                'template'    => 'post_title',
                'required'    => 'yes',
                'label'       => 'Post Title',
                'name'        => 'post_title',
                'is_meta'     => 'no',
                'help'        => '',
                'css'         => '',
                'placeholder' => '',
                'default'     => '',
                'size'        => '40',
                'wpuf_cond'   => [],
            ],
            [
                'input_type'   => 'textarea',
                'template'     => 'post_content',
                'required'     => 'yes',
                'label'        => 'Post Content',
                'name'         => 'post_content',
                'is_meta'      => 'no',
                'help'         => '',
                'css'          => '',
                'rows'         => '5',
                'cols'         => '25',
                'placeholder'  => '',
                'default'      => '',
                'rich'         => 'teeny',
                'insert_image' => 'yes',
                'wpuf_cond'    => [],
            ],
        ];

        $settings = [
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'post_format'         => '0',
            'default_cat'         => '-1',
            'guest_post'          => 'false',
            'guest_details'       => 'true',
            'name_label'          => 'Name',
            'email_label'         => 'Email',
            'message_restrict'    => 'This page is restricted. Please %login% / %register% to view this page.',
            'redirect_to'         => 'post',
            'message'             => 'Post saved',
            'page_id'             => '',
            'url'                 => '',
            'comment_status'      => 'open',
            'submit_text'         => 'Submit',
            'submit_button_cond'  => [
                'condition_status' => 'no',
                'cond_logic'       => 'any',
                'conditions'       => [
                    [
                        'name'             => '',
                        'operator'         => '=',
                        'option'           => '',
                    ],
                ],
            ],
            'draft_post'       => 'false',
            'edit_post_status' => 'publish',
            'edit_redirect_to' => 'same',
            'update_message'   => 'Post updated successfully',
            'edit_page_id'     => '',
            'edit_url'         => '',
            'subscription'     => '- Select -',
            'update_text'      => 'Update',
            'notification'     => [
                'new'          => 'on',
                'new_to'       => get_option( 'admin_email' ),
                'new_subject'  => 'New post created',
                'new_body'     => "Hi Admin, \r\n\r\nA new post has been created in your site %sitename% (%siteurl%). \r\n\r\nHere is the details: \r\nPost Title: %post_title% \r\nContent: %post_content% \r\nAuthor: %author% \r\nPost URL: %permalink% \r\nEdit URL: %editlink%",
                'edit'         => 'off',
                'edit_to'      => get_option( 'admin_email' ),
                'edit_subject' => 'A post has been edited',
                'edit_body'    => "Hi Admin, \r\n\r\nThe post \"%post_title%\" has been updated. \r\n\r\nHere is the details: \r\nPost Title: %post_title% \r\nContent: %post_content% \r\nAuthor: %author% \r\nPost URL: %permalink% \r\nEdit URL: %editlink%",
            ],
        ];
    }

    // Profile form
    if ( 'wpuf_profile' === $post_type ) {
        $form_fields = [
            [
                'input_type'  => 'email',
                'template'    => 'user_email',
                'required'    => 'yes',
                'label'       => 'Email',
                'name'        => 'user_email',
                'is_meta'     => 'no',
                'help'        => '',
                'css'         => '',
                'placeholder' => '',
                'default'     => '',
                'size'        => '40',
                'wpuf_cond'   => null,
            ],
            [
                'input_type'    => 'password',
                'template'      => 'password',
                'required'      => 'yes',
                'label'         => 'Password',
                'name'          => 'password',
                'is_meta'       => 'no',
                'help'          => '',
                'css'           => '',
                'placeholder'   => '',
                'default'       => '',
                'size'          => '40',
                'min_length'    => '5',
                'repeat_pass'   => 'yes',
                're_pass_label' => 'Confirm Password',
                'pass_strength' => 'yes',
                'wpuf_cond'     => null,
            ],
        ];

        $settings = [
            'role'             => 'subscriber',
            'reg_redirect_to'  => 'same',
            'message'          => 'Registration successful',
            'update_message'   => 'Profile updated successfully',
            'reg_page_id'      => '0',
            'registration_url' => '',
            'profile_url'      => '',
            'submit_text'      => 'Register',
            'update_text'      => 'Update Profile',
        ];
    }

    if ( ! empty( $form_fields ) && ! $blank ) {
        foreach ( $form_fields as $order => $field ) {
            wpuf_insert_form_field( $form_id, $field, false, $order );
        }
    }

    if ( ! empty( $settings ) ) {
        update_post_meta( $form_id, 'wpuf_form_settings', $settings );
    }

    //set form Version
    update_post_meta( $form_id, 'wpuf_form_version', WPUF_VERSION );

    return $form_id;
}

/**
 * Get the client IP address
 *
 * @since 2.5.2
 *
 * @return string
 */
function wpuf_get_client_ip() {
    $ipaddress = '';

    if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
    } elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
        $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ) );
    } elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
        $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ) );
    } elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
        $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED'] ) );
    } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
        $ipaddress = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    } else {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

/**
 * Delete a form with it's field and meta
 *
 * @since 2.5.2
 *
 * @param int  $form_id
 * @param bool $force
 *
 * @return void
 */
function wpuf_delete_form( $form_id, $force = true ) {
    global $wpdb;

    wp_delete_post( $form_id, $force );

    // delete form inputs as WP doesn't know the relationship
    $wpdb->delete(
        $wpdb->posts,
        [
            'post_parent' => $form_id,
            'post_type'   => 'wpuf_input',
        ]
    );
}

/**
 * Check save draft post status based on subscription
 *
 * @since 2.5.2
 *
 * @param array $form_settings
 *
 * @return string $post_status
 */
function wpuf_get_draft_post_status( $form_settings ) {
    $noce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

    if ( isset( $nonce ) && ! wp_verify_nonce( $noce, 'wpuf_form_add' ) ) {
        return;
    }

    $post_status                 = 'draft';
    $current_user                = wpuf_get_user();
    $charging_enabled            = $current_user->subscription()->current_pack_id();
    $user_wpuf_subscription_pack = get_user_meta( get_current_user_id(), '_wpuf_subscription_pack', true );

    if ( $charging_enabled && ! isset( $_POST['post_id'] ) ) {
        if ( ! empty( $user_wpuf_subscription_pack ) ) {
            if ( $current_user->subscription()->expired() ) {
                $post_status = 'pending';
            }
        }
    }

    return $post_status;
}

/**
 * Show helper texts to understand the type of page in admin page listing
 *
 * @since 2.6.0
 *
 * @param array    $state
 * @param \WP_Post $post
 *
 * @return array
 */
function wpuf_admin_page_states( $state, $post ) {
    if ( 'page' !== $post->post_type ) {
        return $state;
    }

    $pattern = '/\[(wpuf[\w\-\_]+).+\]/';

    preg_match_all( $pattern, $post->post_content, $matches );
    $matches = array_unique( $matches[0] );

    if ( ! empty( $matches ) ) {
        $page      = '';
        $shortcode = $matches[0];

        if ( '[wpuf_account]' === $shortcode ) {
            $page = 'WPUF Account Page';
        } elseif ( '[wpuf_edit]' === $shortcode ) {
            $page = 'WPUF Post Edit Page';
        } elseif ( '[wpuf-login]' === $shortcode ) {
            $page = 'WPUF Login Page';
        } elseif ( '[wpuf_sub_pack]' === $shortcode ) {
            $page = 'WPUF Subscription Page';
        } elseif ( '[wpuf_editprofile]' === $shortcode ) {
            $page = 'WPUF Profile Edit Page';
        } elseif ( stristr( $shortcode, '[wpuf_dashboard' ) ) {
            $page = 'WPUF Dashboard Page';
        } elseif ( stristr( $shortcode, '[wpuf_profile type="registration"' ) ) {
            $page = 'WPUF Registration Page';
        } elseif ( stristr( $shortcode, '[wpuf_profile type="profile"' ) ) {
            $page = 'WPUF Profile Edit Page';
        } elseif ( stristr( $shortcode, '[wpuf_form' ) ) {
            $page = 'WPUF Form Page';
        }

        if ( ! empty( $page ) ) {
            $state['wpuf'] = $page;
        }
    }

    return $state;
}

add_filter( 'display_post_states', 'wpuf_admin_page_states', 10, 2 );

/**
 * Encryption function for various usage
 *
 * @since 2.5.8
 *
 * @param string $id
 *
 * @return string $encoded_id
 */
function wpuf_encryption( $id ) {
    $secret_key     = AUTH_KEY;
    $secret_iv      = AUTH_SALT;

    $encrypt_method = 'AES-256-CBC';
    $key            = hash( 'sha256', $secret_key );
    $iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    $encoded_id     = base64_encode( openssl_encrypt( $id, $encrypt_method, $key, 0, $iv ) );

    return $encoded_id;
}

/**
 * Decryption function for various usage
 *
 * @since 2.5.8
 *
 * @param string $id
 *
 * @return string $encoded_id
 */
function wpuf_decryption( $id ) {
    $secret_key     = AUTH_KEY;
    $secret_iv      = AUTH_SALT;

    $encrypt_method = 'AES-256-CBC';
    $key            = hash( 'sha256', $secret_key );
    $iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    $decoded_id     = openssl_decrypt( base64_decode( $id ), $encrypt_method, $key, 0, $iv );

    return $decoded_id;
}

/**
 * Send guest verification mail
 *
 * @since 2.5.8
 *
 * @param string $post_id_encoded, $form_id_encoded, $charging_enabled, $flag
 *
 * @return void
 */
function wpuf_send_mail_to_guest( $post_id_encoded, $form_id_encoded, $charging_enabled, $flag ) {
    if ( 'on' !== wpuf_get_option( 'enable_guest_email_notification', 'wpuf_mails', 'on' ) ) {
        return;
    }

    $noce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

    if ( isset( $nonce ) && ! wp_verify_nonce( $noce, 'wpuf_edit' ) ) {
        return;
    }

    if ( $charging_enabled ) {
        $encoded_guest_url = add_query_arg(
            [
                'p_id'     => $post_id_encoded,
                'f_id'     => $form_id_encoded,
                'post_msg' => 'verified',
                'f'        => 2,
            ], get_home_url()
        );
    } else {
        $encoded_guest_url = add_query_arg(
            [
                'p_id'     => $post_id_encoded,
                'f_id'     => $form_id_encoded,
                'post_msg' => 'verified',
                'f'        => 1,
            ], get_home_url()
        );
    }

    $default_body     = 'Hey There, <br> <br> We just received your guest post and now we want you to confirm your email so that we can verify the content and move on to the publishing process. <br> <br> Please click the link below to verify: <br> <br> <a href="' . $encoded_guest_url . '">Publish Post</a> <br> <br> Regards, <br> <br>' . bloginfo( 'name' );
    $to               = isset( $_POST['guest_email'] ) ? sanitize_email( wp_unslash( $_POST['guest_email'] ) ) : '';
    $guest_email_sub  = wpuf_get_option( 'guest_email_subject', 'wpuf_mails', 'Please Confirm Your Email to Get the Post Published!' );
    $subject          = $guest_email_sub;
    $guest_email_body = wpuf_get_option( 'guest_email_body', 'wpuf_mails', $default_body );

    if ( ! empty( $guest_email_body ) ) {
        $blogname     = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        $field_search = [ '{activation_link}', '{sitename}' ];

        $field_replace = [
            '<a href="' . $encoded_guest_url . '">Publish Post</a>',
            $blogname,
        ];

        $body = str_replace( $field_search, $field_replace, $guest_email_body );
    } else {
        $body = $default_body;
    }

    $body = get_formatted_mail_body( $body, $subject );

    wp_mail( $to, $subject, $body );
}

/**
 * Check if it's post form builder
 *
 * @since 2.6
 *
 * @return bool
 */
function is_wpuf_post_form_builder() {
    $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

    return 'wpuf-post-forms' === $page ? true : false;
}

/**
 * Check if it's profile form builder
 *
 * @since 2.6
 *
 * @return bool
 */
function is_wpuf_profile_form_builder() {
    $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

    return 'wpuf-profile-forms' === $page ? true : false;
}

/**
 * Get a WP User
 *
 * @since 2.6.0
 *
 * @param int|WP_User $user_id
 *
 * @return \WPUF_User
 */
function wpuf_get_user( $user = null ) {
    if ( ! $user ) {
        $user = wp_get_current_user();
    }

    return new WPUF_User( $user );
}

/**
 * Add all terms as allowed terms
 *
 * @since 2.7.0
 *
 * @return void
 */
function wpuf_set_all_terms_as_allowed() {
    if ( class_exists( 'WP_User_Frontend_Pro' ) ) {
        $subscriptions  = WPUF_Subscription::init()->get_subscriptions();
        $allowed_term   = [];

        foreach ( $subscriptions as $pack ) {
            if ( ! metadata_exists( 'post', $pack->ID, '_sub_allowed_term_ids' ) ) {
                $cts = get_taxonomies( [ '_builtin' => true ], 'objects' );
                ?>
                <?php
                foreach ( $cts as $ct ) {
                    if ( is_taxonomy_hierarchical( $ct->name ) ) {
                        $tax_terms = get_terms(
                            [
                                'taxonomy'   => $ct->name,
                                'hide_empty' => false,
                            ]
                        );

                        foreach ( $tax_terms as $tax_term ) {
                            $allowed_term[] = $tax_term->term_id;
                        }
                    }
                }

                $cts = get_taxonomies( [ '_builtin' => false ], 'objects' );
                ?>
                <?php
                foreach ( $cts as $ct ) {
                    if ( is_taxonomy_hierarchical( $ct->name ) ) {
                        $tax_terms = get_terms(
                            [
                                'taxonomy'   => $ct->name,
                                'hide_empty' => false,
                            ]
                        );

                        foreach ( $tax_terms as $tax_term ) {
                            $allowed_term[] = $tax_term->term_id;
                        }
                    }
                }

                update_post_meta( $pack->ID, '_sub_allowed_term_ids', $allowed_term );
            }
        }
    }
}

/**
 * Post submitted by form
 *
 * @since 2.8
 *
 * @param int $form_id
 *
 * @return List of WP_Post objects
 */
function wpuf_posts_submitted_by( $form_id ) {
    $settings = wpuf_get_form_settings( $form_id );
    $settings['post_type'];
    $args = [
        'meta_key'         => '_wpuf_form_id',
        'meta_value'       => $form_id,
        'post_type'        => $settings['post_type'],
        'post_status'      => 'publish',
    ];
    $posts_array = get_posts( $args );

    return $posts_array;
}

/**
 * Count post submitted by form
 *
 * @since 2.8
 *
 * @param int $form_id
 *
 * @return int
 */
function wpuf_form_posts_count( $form_id ) {
    return count( wpuf_posts_submitted_by( $form_id ) );
}

/**
 * Get formatted email body
 *
 * @since  2.9
 *
 * @param string $message
 *
 * @return string
 */
function get_formatted_mail_body( $message, $subject ) {
    if ( wpuf()->is_pro() && wpuf_pro_is_module_active( 'email-templates/email-templates.php' ) ) {
        $css    = '';
        $header = apply_filters( 'wpuf_email_header', '', $subject );
        $footer = apply_filters( 'wpuf_email_footer', '' );

        if ( empty( $header ) ) {
            ob_start();

            wpuf_load_pro_template(
                'email/header.php',
                [ 'subject' => $subject ]
            );

            $header = ob_get_clean();
        }

        if ( empty( $footer ) ) {
            ob_start();

            wpuf_load_pro_template(
                'email/footer.php',
                []
            );

            $footer = ob_get_clean();
        }

        ob_start();

        wpuf_load_pro_template(
            'email/style.php',
            []
        );

        $css = apply_filters( 'wpuf_email_style', ob_get_clean() );

        $content = $header . '<pre>' . $message . '</pre>' . $footer;

        if ( ! class_exists( 'Emogrifier' ) ) {
            require_once WPUF_PRO_INCLUDES . '/libs/Emogrifier.php';
        }

        try {

            // apply CSS styles inline for picky email clients
            $emogrifier = new Emogrifier( $content, $css );
            $content    = $emogrifier->emogrify();
        } catch ( Exception $e ) {
            echo esc_html( $e->getMessage() );
        }

        return $content;
    }

    return $message;
}

/**
 * Renders an HTML Dropdown
 *
 * @param array $args
 *
 * @return string
 */
function wpuf_select( $args = [] ) {
    $defaults = [
        'options'          => [],
        'name'             => null,
        'class'            => '',
        'id'               => '',
        'selected'         => [],
        'chosen'           => false,
        'placeholder'      => null,
        'multiple'         => false,
        'show_option_all'  => __( 'All', 'wp-user-frontend' ),
        'show_option_none' => __( 'None', 'wp-user-frontend' ),
        'data'             => [],
        'readonly'         => false,
        'disabled'         => false,
    ];

    $args = wp_parse_args( $args, $defaults );

    $data_elements = '';
    $selected      = '';

    foreach ( $args['data'] as $key => $value ) {
        $data_elements .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
    }

    if ( $args['multiple'] ) {
        $multiple = ' MULTIPLE';
    } else {
        $multiple = '';
    }

    if ( $args['chosen'] ) {
        $args['class'] .= ' wpuf-select-chosen';

        if ( is_rtl() ) {
            $args['class'] .= ' chosen-rtl';
        }
    }

    if ( $args['placeholder'] ) {
        $placeholder = $args['placeholder'];
    } else {
        $placeholder = '';
    }

    if ( isset( $args['readonly'] ) && $args['readonly'] ) {
        $readonly = ' readonly="readonly"';
    } else {
        $readonly = '';
    }

    if ( isset( $args['disabled'] ) && $args['disabled'] ) {
        $disabled = ' disabled="disabled"';
    } else {
        $disabled = '';
    }

    $class  = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
    $output = '<select' . $disabled . $readonly . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( str_replace( '-', '_', $args['id'] ) ) . '" class="wpuf-select ' . $class . '"' . $multiple . ' data-placeholder="' . $placeholder . '"' . $data_elements . '>';

    if ( ! isset( $args['selected'] ) || ( is_array( $args['selected'] ) && empty( $args['selected'] ) ) || ! $args['selected'] ) {
        $selected = '';
    }

    if ( $args['show_option_all'] ) {
        if ( $args['multiple'] && ! empty( $args['selected'] ) ) {
            $selected = selected( true, in_array( 0, $args['selected'], true ), false );
        } else {
            $selected = selected( $args['selected'], 0, false );
        }
        $output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>';
    }

    if ( ! empty( $args['options'] ) ) {
        if ( $args['show_option_none'] ) {
            if ( $args['multiple'] ) {
                $selected = selected( true, in_array( -1, $args['selected'], true ), false );
            } elseif ( isset( $args['selected'] ) && ! is_array( $args['selected'] ) && ! empty( $args['selected'] ) ) {
                $selected = selected( $args['selected'], -1, false );
            }
            $output .= '<option value="-1"' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>';
        }

        foreach ( $args['options'] as $key => $option ) {
            if ( $args['multiple'] && is_array( $args['selected'] ) ) {
                $selected = selected( true, in_array( (string) $key, $args['selected'], true ), false );
            } elseif ( isset( $args['selected'] ) && ! is_array( $args['selected'] ) ) {
                $selected = selected( $args['selected'], $key, false );
            }

            $output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
        }
    }

    $output .= '</select>';

    return $output;
}

/**
 * Renders a Text field in settings field
 *
 * @param array $args Arguments for the text field
 *
 * @return string Text field
 */
function wpuf_text( $args = [] ) {
    $defaults = [
        'id'           => '',
        'name'         => isset( $name ) ? $name : 'text',
        'value'        => isset( $value ) ? $value : null,
        'label'        => isset( $label ) ? $label : null,
        'desc'         => isset( $desc ) ? $desc : null,
        'placeholder'  => '',
        'class'        => 'regular-text',
        'disabled'     => false,
        'autocomplete' => '',
        'data'         => false,
    ];

    $args = wp_parse_args( $args, $defaults );

    $class    = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
    $disabled = '';

    if ( $args['disabled'] ) {
        $disabled = ' disabled="disabled"';
    }

    $data = '';

    if ( ! empty( $args['data'] ) ) {
        foreach ( $args['data'] as $key => $value ) {
            $data .= 'data-' . $key . '="' . esc_attr( $value ) . '" ';
        }
    }

    $output = '<span id="wpuf-' . $args['name'] . '-wrap">';

    if ( ! empty( $args['label'] ) ) {
        $output .= '<label class="wpuf-label" for="' . $args['id'] . '">' . esc_html( $args['label'] ) . '</label>';
    }

    if ( ! empty( $args['desc'] ) ) {
        $output .= '<span class="wpuf-description">' . wp_kses_post( $args['desc'] ) . '</span>';
    }

    $output .= '<input type="text" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" autocomplete="' . esc_attr( $args['autocomplete'] ) . '" value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="' . $class . '" ' . $data . '' . $disabled . '/>';

    $output .= '</span>';

    return $output;
}

/**
 * Descriptive text callback
 *
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function wpuf_descriptive_text( $args ) {
    echo wp_kses_post( $args['desc'] );
}

/**
 * Update the value of a settings field
 *
 * @param string $option  settings field name
 * @param string $section the section name this field belongs to
 * @param string $value   the value to be set
 *
 * @return mixed
 */
function wpuf_update_option( $option, $section, $value ) {
    $options = get_option( $section );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $options[ $option ] = $value;

    update_option( $section, $options );
}

/**
 * Get terms of related taxonomy
 *
 * @since  2.8.5
 *
 * @param string $taxonomy
 *
 * @return array
 */
function wpuf_get_terms( $taxonomy = 'category' ) {
    $items = [];

    $terms = get_terms(
        [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]
    );

    foreach ( $terms as $key => $term ) {
        $items[ $term->term_id ] = $term->name;
    }

    return $items;
}

/**
 * Retrieve a states drop down
 *
 * @return void
 */
function wpuf_ajax_get_states_field() {
    check_ajax_referer( 'wpuf-ajax-address' );

    $country = isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '';
    $cs        = new CountryState();
    $countries = $cs->countries();
    $states    = $cs->getStates( $countries[ $country ] );

    if ( ! empty( $states ) ) {
        $args = [
            'name'             => isset( $_POST['field_name'] ) ? sanitize_text_field( wp_unslash( $_POST['field_name'] ) ) : '',
            'id'               => isset( $_POST['field_name'] ) ? sanitize_text_field( wp_unslash( $_POST['field_name'] ) ) : '',
            'class'            => isset( $_POST['field_name'] ) ? sanitize_text_field( wp_unslash( $_POST['field_name'] ) ) : '',
            'options'          => $states,
            'show_option_all'  => false,
            'show_option_none' => false,
            'data'             => [ 'required' => 'yes', 'type' => 'select' ],
        ];

        $allowed_html = [
            'select' => [
                'class'            => [],
                'name'             => [],
                'id'               => [],
                'data-placeholder' => [],
                'data-required'    => [],
                'data-type'        => [],
            ],
            'option' => [
                'value'    => [],
                'class'    => [],
                'id'       => [],
                'selected' => []
            ],
        ];

        $response = wp_kses( wpuf_select( $args ), $allowed_html );
    } else {
        $response = 'nostates';
    }

    wp_send_json( $response ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_ajax_wpuf-ajax-address', 'wpuf_ajax_get_states_field' );
add_action( 'wp_ajax_nopriv_wpuf-ajax-address', 'wpuf_ajax_get_states_field' );

/**
 * Performs tax calculations and updates billing address
 *
 * @return void
 */
function wpuf_update_billing_address() {
    check_ajax_referer( 'wpuf-ajax-address' );

    ob_start();

    $user_id        = get_current_user_id();
    $add_line_1 = isset( $_POST['billing_add_line1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_add_line1'] ) ) : '';
    $add_line_2 = isset( $_POST['billing_add_line2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_add_line2'] ) ) : '';
    $city       = isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '';
    $state      = isset( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '';
    $zip        = isset( $_POST['billing_zip'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_zip'] ) ) : '';
    $country    = isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '';
    $type       = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
    $id         = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

    $address_fields = [
        'add_line_1'    => $add_line_1,
        'add_line_2'    => $add_line_2,
        'city'          => $city,
        'state'         => $state,
        'zip_code'      => $zip,
        'country'       => $country,
    ];

    update_user_meta( $user_id, 'wpuf_address_fields', $address_fields );

    $post_data['type']            = $type;
    $post_data['id']              = $id;
    $post_data['billing_country'] = $country;
    $post_data['billing_state']   = $state;

    $is_pro = wpuf()->is_pro();

    if ( $is_pro ) {
        do_action( 'wpuf_calculate_tax', $post_data );
    } else {
        die();
    }
}
add_action( 'wp_ajax_wpuf_update_billing_address', 'wpuf_update_billing_address' );
add_action( 'wp_ajax_nopriv_wpuf_update_billing_address', 'wpuf_update_billing_address' );

/**
 * Retrieve user address
 *
 * @return mixed
 */
function wpuf_get_user_address( $user_id = 0 ) {
    $user_id        = $user_id ? $user_id : get_current_user_id();
    $address_fields = [];

    if ( metadata_exists( 'user', $user_id, 'wpuf_address_fields' ) ) {
        $address_fields = get_user_meta( $user_id, 'wpuf_address_fields', true );
    } else {
        $address_fields = array_fill_keys( [ 'add_line_1', 'add_line_2', 'city', 'state', 'zip_code', 'country' ], '' );

        if ( class_exists( 'WooCommerce' ) ) {
            $customer_id = get_current_user_id();
            $woo_address = [];
            $customer    = new WC_Customer( $customer_id );

            $woo_address = $customer->get_billing();
            unset( $woo_address['email'], $woo_address['tel'], $woo_address['phone'], $woo_address['company'] );

            $countries_obj        = new WC_Countries();
            $countries_array      = $countries_obj->get_countries();
            $country_states_array = $countries_obj->get_states();
            $woo_address['state'] = isset( $country_states_array[ $woo_address['country'] ][ $woo_address['state'] ] ) ? $country_states_array[ $woo_address['country'] ][ $woo_address['state'] ] : '';
            $woo_address['state'] = strtolower( str_replace( ' ', '', $woo_address['state'] ) );

            if ( ! empty( $woo_address ) ) {
                $address_fields = [
                    'add_line_1'    => $woo_address['address_1'],
                    'add_line_2'    => $woo_address['address_2'],
                    'city'          => $woo_address['city'],
                    'state'         => $woo_address['state'],
                    'zip_code'      => $woo_address['postcode'],
                    'country'       => $woo_address['country'],
                ];
            }
        }
    }

    return $address_fields;
}

/**
 * Displays a multi select dropdown for a settings field
 *
 * @param array $args settings field args
 */
function wpuf_settings_multiselect( $args ) {
    $settings = new WeDevs_Settings_API();
    $value    = $settings->get_option( $args['id'], $args['section'], $args['std'] );
    $value    = is_array( $value ) ? (array) $value : [];
    $size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
    $html     = sprintf( '<select multiple="multiple" class="%1$s" name="%2$s[%3$s][]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );

    foreach ( $args['options'] as $key => $label ) {
        $checked = in_array( $key, $value, true ) ? $key : '0';
        $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $checked, $key, false ), $label );
    }

    $html .= sprintf( '</select>' );
    $html .= $settings->get_field_description( $args );

    echo wp_kses(
        $html, [
            'p' => [],
            'select' => [
                'multiple' => [],
                'class'    => [],
                'name'     => [],
                'id'       => [],
            ],
            'option' => [
                'value' => [],
                'selected' => [],
            ],
        ]
    );
}

/**
 * Displays Form Schedule Messages
 *
 * @since 2.8.10
 *
 * @param int $form_id
 */
function wpuf_show_form_schedule_message( $form_id ) {
    $form_settings   = wpuf_get_form_settings( $form_id );
    $is_scheduled    = ( isset( $form_settings['schedule_form'] ) && $form_settings['schedule_form'] === 'true' ) ? true : false;

    if ( $is_scheduled ) {
        $start_time   = ! empty( $form_settings['schedule_start'] ) ? strtotime( $form_settings['schedule_start'] ) : 0;
        $end_time     = ! empty( $form_settings['schedule_end'] ) ? strtotime( $form_settings['schedule_end'] ) : 0;
        $current_time = current_time( 'timestamp' );

        if ( $current_time >= $start_time && $current_time <= $end_time ) {
            return;
        }

        // too early?
        if ( $current_time < $start_time ) {
            echo wp_kses_post( '<div class="wpuf-message">' . $form_settings['form_pending_message'] . '</div>' );
        } elseif ( $current_time > $end_time ) {
            echo wp_kses_post( '<div class="wpuf-message">' . $form_settings['form_expired_message'] . '</div>' );
        }
        ?>
        <script>
            jQuery( function($) {
                $(".wpuf-submit-button").attr("disabled", "disabled");
            });
        </script>
        <?php
        return;
    }
}
add_action( 'wpuf_before_form_render', 'wpuf_show_form_schedule_message' );

/**
 * Displays Form Limit Messages
 *
 * @since 2.8.10
 *
 * @param int $form_id
 */
function wpuf_show_form_limit_message( $form_id ) {
    $form_settings  = wpuf_get_form_settings( $form_id );
    $has_limit      = ( isset( $form_settings['limit_entries'] ) && $form_settings['limit_entries'] === 'true' ) ? true : false;
    $post_to_check  = get_post( get_the_ID() );
    $is_edit_page   = false;

    if ( stripos( $post_to_check->post_content, '[wpuf_edit' ) !== false ) {
        $is_edit_page = true;
    }

    if ( $has_limit && ! $is_edit_page ) {
        $limit        = (int) ! empty( $form_settings['limit_number'] ) ? $form_settings['limit_number'] : 0;
        $form_entries = wpuf_form_posts_count( $form_id );

        if ( $limit && $limit <= $form_entries ) {
            $info = $form_settings['limit_message'];
            echo wp_kses_post( '<div class="wpuf-info">' . $info . '</div>' );
            ?>
            <script>
                jQuery( function($) {
                    $(".wpuf-submit-button").attr("disabled", "disabled");
                });
            </script>
            <?php
        }

        return;
    }
}
add_action( 'wpuf_before_form_render', 'wpuf_show_form_limit_message' );

/**
 * Save frontend post revision
 *
 * @param int   $post_id
 * @param array $form_settings
 *
 * @return void
 */
function wpuf_frontend_post_revision( $post_id, $form_settings ) {
    $post = get_post( $post_id );

    if ( post_type_supports( $form_settings['post_type'], 'revisions' ) ) {
        $revisions = wp_get_post_revisions(
            $post_id, [
                'order' => 'ASC',
                'posts_per_page' => 1,
            ]
        );
        $revision  = current( $revisions );

        _wp_upgrade_revisions_of_post( $post, wp_get_post_revisions( $post_id ) );
    }
}

function wpuf_clean( $var ) {
    if ( is_array( $var ) ) {
        return array_map( 'wpuf_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( wp_unslash( $var ) ) : $var;
    }
}

/**
 * Calculate ini directives in bytes
 *
 * @since 3.3.0
 *
 * @param string|int $val
 *
 * @return int
 */
function wpuf_ini_get_byte( $val ) {
    $byte = absint( $val );
    $char = strtolower( str_replace( $byte, '', $val ) );

    switch ( $char ) {
        case 'g':
            $byte *= GB_IN_BYTES;
            break;

        case 'm':
            $byte *= MB_IN_BYTES;
            break;

        case 'k':
            $byte *= KB_IN_BYTES;
            break;
    }

    return $byte;
}

/**
 * The maximum file size allowed to upload
 *
 * To upload large files, `post_max_size` value must be larger than `upload_max_filesize`
 *
 * @see https://www.php.net/manual/en/ini.core.php#ini.post-max-size
 *
 * @since 3.3.0
 *
 * @return string|int
 */
function wpuf_max_upload_size() {
    $post_max_size       = ini_get( 'post_max_size' );
    $upload_max_filesize = ini_get( 'upload_max_filesize' );

    if ( wpuf_ini_get_byte( $upload_max_filesize ) > wpuf_ini_get_byte( $post_max_size ) ) {
        return $post_max_size;
    }

    return $upload_max_filesize;
}

/**
 * Validate a boolean variable
 *
 * @since 3.3.0
 *
 * @param mixed $var
 *
 * @return bool
 */
function wpuf_validate_boolean( $var ) {
    return filter_var( $var, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Check user has certain roles
 *
 * @since 3.4.0
 *
 * @param  array  $roles   Permitted user roles to submit a post
 * @param  int    $user_id User id will submit post
 *
 * @return bool
 */
function wpuf_user_has_roles( $roles, $user_id = 0 ) {
    if ( empty( $roles ) ) {
        return false;
    }

    $user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();

    if ( ! empty( array_intersect( $user->roles, $roles ) ) ) {
        return true;
    }

    return false;
}

/**
 * Create a private page for form preview
 *
 * @return bool|false|string|WP_Error
 */
function get_wpuf_preview_page() {
    $page_url        = '';
    $post_status     = '';
    $preview_page_id = get_option( 'wpuf_preview_page', false );

    if ( $preview_page_id ){
        $page_url    = get_permalink( $preview_page_id );
    }

    if ( $page_url ){
        $post_status = get_post_status( $preview_page_id );
    }

    if ( $page_url && $post_status === 'private' ) {
        return $page_url;
    }

    if ( $post_status && $post_status !== 'private' ) {
        wp_update_post(
            [
                'ID' => $preview_page_id,
                'post_status' => 'private',
            ]
        );
        $page_url = get_permalink( $preview_page_id );
    }


    if ( $page_url ) {
        return $page_url;
    }

    $post_id = wp_insert_post(
        [
            'post_title'  => 'wpuf-preview',
            'post_type'   => 'page',
            'post_status' => 'private',
        ]
    );
    update_option( 'wpuf_preview_page', $post_id );

    return get_permalink( get_option( 'wpuf_preview_page' ) );
}

/**
 * Sanitize nested text field
 *
 * @param $arr
 *
 * @return array
 */
function wpuf_recursive_sanitize_text_field($arr){
    foreach ($arr as $key => &$value) {
        if (is_array($value)) {
            $value = wpuf_recursive_sanitize_text_field($value);
        } else {
            $value = sanitize_text_field($value);
        }
    }

    return $arr;
}

/**
 * Determine page after payment success
 *
 * @since WPUF_PRO
 *
 * @param $data
 *
 * @return bool|false|string|WP_Error
 */
function wpuf_payment_success_page( $data ){
    $gateway          = ! empty( $data['wpuf_payment_method'] ) ? $data['wpuf_payment_method'] : '';
    $success_query    = "wpuf_${gateway}_success";
    $redirect_page    = '';
    $redirect_page_id = wpuf_get_option( 'payment_success', 'wpuf_payment' );
    if ( 'post' === $data['type'] ){
        $post_id           = array_key_exists( 'item_number', $data ) && ! empty( $data['item_number'] ) ? $data['item_number'] : $_GET['post_id'];
        $form_id           = get_post_meta( $post_id, '_wpuf_form_id', true );
        $form_settings     = wpuf_get_form_settings( $form_id );
        $ppp_success_page  = ! empty( $form_settings['ppp_payment_success_page'] ) ? $form_settings['ppp_payment_success_page'] : '';
        $redirect_page_id  = $ppp_success_page ? $ppp_success_page : $redirect_page_id;
    }

    $redirect_page = $redirect_page_id ? add_query_arg( 'action', $success_query,  untrailingslashit( get_permalink( $redirect_page_id ) ) ) : add_query_arg( 'action', $success_query, untrailingslashit( get_permalink( wpuf_get_option( 'subscription_page', 'wpuf_payment' ) ) ) );
    //for bank
    $redirect_page =  ! empty( $data['wpuf_payment_method'] ) && 'bank' === $data['wpuf_payment_method'] ? get_permalink( wpuf_get_option( 'bank_success', 'wpuf_payment' ) ) : $redirect_page;

    return $redirect_page;
}
