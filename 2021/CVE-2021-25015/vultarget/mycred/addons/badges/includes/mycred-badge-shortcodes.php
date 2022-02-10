<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Shortcode: mycred_my_badges
 * Allows you to show the current users earned badges.
 * @since 1.5
 * @version 1.2.1
 */
if ( ! function_exists( 'mycred_render_my_badges' ) ) :
    function mycred_render_my_badges( $atts, $content = '' ) {

        extract( shortcode_atts( array(
            'show'     => 'earned',
            'width'    => MYCRED_BADGE_WIDTH,
            'height'   => MYCRED_BADGE_HEIGHT,
            'user_id'  => 'current'
        ), $atts, MYCRED_SLUG . '_my_badges' ) );

        if ( ! is_user_logged_in() && $user_id == 'current' )
            return $content;

        $user_id = mycred_get_user_id( $user_id );

        ob_start();

        echo '<div class="row" id="mycred-users-badges"><div class="col-xs-12">';

        // Show only badges that we have earned
        if ( $show == 'earned' ) {

            mycred_display_users_badges( $user_id, $width, $height );

        }

        // Show all badges highlighting the ones we earned
        elseif ( $show == 'all' ) {

            $users_badges = mycred_get_users_badges( $user_id );
            $all_badges   = mycred_get_badge_ids();

            foreach ( $all_badges as $badge_id ) {

                echo '<div class="the-badge">';

                // User has not earned badge
                if ( ! array_key_exists( $badge_id, $users_badges ) ) {

                    $badge = mycred_get_badge( $badge_id );
                    $badge->image_width  = $width;
                    $badge->image_height = $height;

                    if ( $badge->main_image !== false )
                        echo $badge->get_image( 'main' );

                }

                // User has earned badge
                else {

                    $level = $users_badges[ $badge_id ];
                    $badge = mycred_get_badge( $badge_id, $level );
                    $badge->image_width  = $width;
                    $badge->image_height = $height;

                    if ( $badge->level_image !== false )
                        echo $badge->get_image( $level );

                }

                echo '</div>';

            }

        }
        echo '</div></div>';

        $output = ob_get_contents();
        ob_end_clean();

        return apply_filters( 'mycred_my_badges', $output, $user_id );

    }
endif;

/**
 * Shortcode: mycred_badges
 * Allows you to show all published badges
 * @since 1.5
 * @version 1.1.1
 */
if ( ! function_exists( 'mycred_render_badges' ) ) :
    function mycred_render_badges( $atts, $template = '' ) {
        extract( shortcode_atts( array(
            'width'  => MYCRED_BADGE_WIDTH,
            'height' => MYCRED_BADGE_HEIGHT
        ), $atts, MYCRED_SLUG . '_badges' ) );

        $all_badges = mycred_get_badge_ids();

        if ( $template == '' )
            $template = '<div class="the-badge row"><div class="col-xs-12"><h3 class="badge-title">%badge_title%</h3><div class="badge-requirements">%requirements%</div><div class="users-with-badge">%count%</div><div class="badge-images">%default_image% %main_image%</div></div></div>';

        $output = '<div id="mycred-all-badges">';

        if ( ! empty( $all_badges ) ) {

            foreach ( $all_badges as $badge_id ) {

                $badge               = mycred_get_badge( $badge_id, 0 );
                $badge->image_width  = $width;
                $badge->image_height = $height;

                $row = $template;
                $row = str_replace( '%badge_title%',   $badge->title,                                  $row );
                $row = str_replace( '%requirements%',  mycred_display_badge_requirements( $badge_id ), $row );
                $row = str_replace( '%count%',         $badge->earnedby,                               $row );
                $row = str_replace( '%default_image%', $badge->get_image( 'main' ),                    $row );
                
                if( mycred_user_has_badge( get_current_user_id(), $badge_id) ) {
                    $user_id = get_current_user_id();
                    $badge   = mycred_get_badge( $badge_id );
                    $level   = $badge->get_users_current_level( $user_id );
                    $row     = str_replace( '%main_image%',    $badge->get_image( $level ), $row );
                }
                else {
                    $row = str_replace( '%main_image%',    '', $row );
                }

                $output .= apply_filters( 'mycred_badges_badge', $row, $badge );

            }

        }

        $output .= '</div>';

        return apply_filters( 'mycred_badges', $output );

    }
endif;

/**
 * myCRED Renders Badges List ShortCode
 * @param string $atts
 * @since 2.1
 * @version 1.0
 */
if( !function_exists( 'mycred_render_badges_list' ) ) :
    function mycred_render_badges_list( $atts = '' ) {

        extract( shortcode_atts( array(
                'achievement_tabs'  =>  '1'
            ),
            $atts, MYCRED_SLUG . '_badges_list'
        ) );

        ob_start();?>

        <div class="mycred-badges-list">
            <div class="mycred-search-bar">
                <form method="post">
                    <input id="mycerd-badges-search" type="text" placeholder="Search for badge">
                    <button class="mycred-achieved-badge-btn">Achieved</button>
                    <button class="mycred-not-achieved-badge-btn">Not Achieved</button>
                    <button class="mycred-clear-filter-btn">Clear All</button>
                </form>
            </div>

            <?php 
            if ( $achievement_tabs == 1 ) {

                $badges = mycred_get_categorized_badge_list();

                if ( $badges['category_count'] > 0 ) { ?>
                    <div class="mycred-badges-list-nav">
                        <ul class="mycred-badges-list-tabs">
                            <?php 
                                foreach ( $badges['tabs'] as $id => $element ) {
                                        
                                    echo $element;

                                }
                            ?>
                        </ul>
                    </div>
                    <div class="mycred-badges-list-panels">
                        <?php 
                            foreach ( $badges['panels'] as $id => $element ) {
                                    
                                echo $element;

                            }
                        ?>
                    </div>
                <?php
                }

            }
            else {

                echo '<div class="mycred-badges-list-all">';
                echo mycred_get_uncategorized_badge_list();
                echo '</div>';
            
            }
            wp_reset_query();
            ?>
        </div>
        <script type="text/javascript">
                
            jQuery(document).ready(function(){

                jQuery('.mycred-badges-list-item').click(function(){

                    window.location.href = jQuery(this).data('url');

                });

                jQuery('.mycred-badges-list-tabs li').click(function(){

                    jQuery('.mycred-badges-list-tabs li').removeClass('active');
                    jQuery( this ).addClass('active');

                    jQuery('.mycred-badges-list-panel').removeClass('active');
                    jQuery('.mycred-badges-list-panel[data-id="'+ jQuery(this).data('id') +'"]').addClass('active');

                });

            });

        </script>

        <?php
        $content = ob_get_clean();

        return $content;
    }
endif;

/**
 * Renders Badge's Evidence ShortCode
 * @param string $atts
 * @since 2.1
 * @version 1.0
 */
if ( !function_exists( 'mycred_render_badge_evidence' ) ) :
    function mycred_render_badge_evidence( $atts = '' ) {

        $content = '<div class="mycred-evidence-page">Evidence not found</div>'; 

        if ( isset( $_GET['uid'] ) && isset( $_GET['bid'] ) ) {

            $user_id  = intval( $_GET['uid'] );
            $badge_id = intval( $_GET['bid'] );

            $user_info = get_userdata( $user_id );
            $badge     = mycred_get_badge( $badge_id );

            if ( $user_info && $badge && $badge->open_badge ) {
                
                $issued_on = mycred_get_user_meta( $user_id, MYCRED_BADGE_KEY . $badge_id, '_issued_on', true );

                $content = '<div class="mycred-evidence-page">
                                <div class="mycred-left">
                                    <img src="' . $badge->get_earned_image( $user_id ) . '" alt="">
                                </div>
                                <div class="mycred-left intro">
                                    <h4 class="mycred-remove-margin">' . $badge->title . '</h4>
                                    <div class="mycred-remove-margin">
                                        <p>Name: '. $user_info->display_name .'</p>
                                        <p>Email: ' . $user_info->user_email . '</p>
                                        <p>Issued On: ' . date( 'Y-m-d\TH:i:sP', $issued_on ) . '</p>
                                        <p><span class="dashicons dashicons-yes-alt"></span> <span class="icon-txt"> Verified</span></p>
                                    </div>
                                </div>
                                <div class="mycred-clear"></div>
                            </div>';

            }
            
            
        }

        return $content;
    }
endif;