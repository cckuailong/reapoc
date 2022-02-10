<?php
    global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;

    media_upload_header();

    $post_id = intval($_REQUEST['post_id']);

    $form_action_url = admin_url("media-upload.php?type=$type&tab=<pluginname>&post_id=$post_id");
        $form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);
        $form_class = 'media-upload-form validate';

        if ( get_user_setting('uploader') )
        $form_class .= ' html-uploader';

        $_GET['paged'] = isset( $_GET['paged'] ) ? intval($_GET['paged']) : 0;
        if ( $_GET['paged'] < 1 )
        $_GET['paged'] = 1;
        $start = ( $_GET['paged'] - 1 ) * 10;
        if ( $start < 1 )
        $start = 0;

        list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();
?>
        <form id="filter" action="" method="get">
            <input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />
            <input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>" />
            <input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />
            <input type="hidden" name="post_mime_type" value="<?php echo isset( $_GET['post_mime_type'] ) ? esc_attr( $_GET['post_mime_type'] ) : ''; ?>" />

            <p id="media-search">
                <label for="media-search-input"><?php _e('Search Media');?>:</label>
                <input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
                <?php submit_button( __( 'Search Media' ), 'button', '', false ); ?>
            </p>

            <ul>
                <?php
                $type_links = array();
                $_num_posts = (array) wp_count_attachments();
                $matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
                foreach ( $matches as $_type => $reals )
                    foreach ( $reals as $real )
                        if ( isset($num_posts[$_type]) )
                            $num_posts[$_type] += $_num_posts[$real];
                        else
                            $num_posts[$_type] = $_num_posts[$real];
                // If available type specified by media button clicked, filter by that type
                if ( empty($_GET['post_mime_type']) && !empty($num_posts[$type]) ) {
                    $_GET['post_mime_type'] = $type;
                    list($post_mime_types, $avail_post_mime_types) = wp_edit_attachments_query();
                }
                if ( empty($_GET['post_mime_type']) || $_GET['post_mime_type'] == 'all' )
                    $class = '';
                else
                    $class = '';
                $type_links[] = "<li><a href='" . esc_url(add_query_arg(array('post_mime_type'=>'all', 'paged'=>false, 'm'=>false))) . "'$class>".__('All Types')."</a>";
                foreach ( $post_mime_types as $mime_type => $label ) {
                    $class = '';

                    if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
                        continue;

                    if ( isset($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
                        $class = '';

                    $type_links[] = "<li><a href='" . esc_url(add_query_arg(array('post_mime_type'=>$mime_type, 'paged'=>false))) . "'$class>" . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), "<span id='$mime_type-counter'>" . number_format_i18n( $num_posts[$mime_type] ) . '</span>') . '</a>';
                }
                echo implode(' | </li>', apply_filters( 'media_upload_mime_type_links', $type_links ) ) . '</li>';
                unset($type_links);
                ?>
            </ul>

            <div>

                <?php
                $page_links = paginate_links( array(
                    'base' => add_query_arg( 'paged', '%#%' ),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($wp_query->found_posts / 10),
                    'current' => (int)$_GET['paged']
                ));

                if ( $page_links )
                    echo "<div class='tablenav-pages'>$page_links</div>";
                ?>

                <div>
                    <?php

                    $arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'attachment' ORDER BY post_date DESC";

                    $arc_result = $wpdb->get_results( $arc_query );

                    $month_count = count($arc_result);

                    if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>
                        <select name='m'>
                            <option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>
                            <?php
                            foreach ($arc_result as $arc_row) {
                                if ( $arc_row->yyear == 0 )
                                    continue;
                                $arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

                                if ( isset($_GET['m']) && ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] ) )
                                    $default = ' selected="selected"';
                                else
                                    $default = '';

                                echo "<option$default value='" . esc_attr( $arc_row->yyear . $arc_row->mmonth ) . "'>";
                                echo esc_html( $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear" );
                                echo "</option>\n";
                            }
                            ?>
                        </select>
                    <?php } ?>

                    <?php submit_button( __( 'Filter »' ), 'secondary', 'post-query-submit', false ); ?>

                </div>

                <br />
            </div>
        </form>

        <form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" id="<pluginname>-form">

            <?php wp_nonce_field('media-form'); ?>
            <?php //media_upload_form( $errors ); ?>

            <script type="text/javascript">
                <!--
                jQuery(function($){
                    var preloaded = $(".media-item.preloaded");
                    if ( preloaded.length > 0 ) {
                        preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
                        updateMediaForm();
                    }
                });
                -->
            </script>

            <div id="media-items">
                <?php add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2); ?>
                <?php echo get_media_items(null, $errors); ?>
            </div>
            <p>
                <?php submit_button( __( 'Save all changes' ), 'button savebutton', 'save', false ); ?>
                <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
            </p>
        </form>
