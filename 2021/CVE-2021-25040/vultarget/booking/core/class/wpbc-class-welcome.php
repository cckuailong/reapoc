<?php
/**
 * Welcome Page Class
 * Shows a feature overview for the new version (major).
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson) and WP.
 * @version     2.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


class WPBC_Welcome {

    public $minimum_capability = 'read';    //'manage_options';
    
    private $asset_path = 'https://wpbookingcalendar.com/assets/';
    //private $asset_path = 'http://beta/assets/';


    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menus' ) );
        //add_action( 'admin_head', array( $this, 'admin_head' ) );	//FixIn: 8.5.1.2
        add_action( 'admin_init', array( $this, 'welcome' ) );

        add_action( 'wpbc_premium_content_overview', array( $this, 'content_premium' ) );
    }

    
    private function css() {
        
        ?><style type="text/css">
			.feature-section.three-col,
			.feature-section.two-col {
				display: flex;
				flex-direction: row;
				justify-content: space-between;
				align-items: flex-start;
			}
			.feature-section.two-col .col.col-1{
				width:100%;
			}
			.feature-section.two-col .col.col-2{
				width: 70%;
				padding: 0 0 0 7%;
				box-sizing: border-box;
			}
			@media (max-width: 782px) {
				/* iPad mini and all iPhones  and other Mobile Devices */
				.feature-section.three-col,
				.feature-section.two-col {
					display: block;
				}
			}

            /* Welcome Page ***************************************************************/
            .wpbc-welcome-page .about-text {
                margin-right:0px;
                margin-bottom:0px;
                min-height: 50px;
            }
            .wpbc-welcome-page .wpbc-section-image {
                border:none;
                box-shadow: 0 1px 3px #777777;   
            }
            .wpbc-welcome-page .versions {
                color: #999999;
                font-size: 12px;
                font-style: normal;
                margin: 0;
                text-align: right;
                text-shadow: 0 -1px 0 #EEEEEE;
            }
            .wpbc-welcome-page .versions a,
            .wpbc-welcome-page .versions a:hover{
                color: #999;
                text-decoration:none;
            }
            .wpbc-welcome-page .update-nag {
                border-color: #E3C58E;
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
                box-shadow: 0 1px 3px #EEEEEE;
                color: #998877;
                font-size: 12px;
                font-weight: 600;
                margin: 15px 0 0;   
                width:90%;
            }
            .wpbc-welcome-page .feature-section {
                margin-top:20px;
                border:none;                
            }
            .wpbc-welcome-page .feature-section div {
                line-height: 1.5em;
            }
            .about-wrap.wpbc-welcome-page .feature-section .last-feature {
                margin-right:0;
            }
            .about-wrap.wpbc-welcome-page .changelog {
                margin-bottom: 10px;
            }
            .about-wrap.wpbc-welcome-page .feature-section h4 {
                font-size: 1.2em;
                margin-bottom: 0.6em;
                margin-left: 0;
                margin-right: 0;
                margin-top: 1.4em;
            }
            .about-wrap.wpbc-welcome-page .feature-section {
                overflow-x: hidden;
                overflow-y: hidden;
                padding-bottom: 20px;
            }
			.about-wrap.wpbc-welcome-page [class$="-col"]{
				align-items: initial;
			}
            @media (max-width: 782px) {      /* iPad mini and all iPhones  and other Mobile Devices */
                .wpbc-welcome-page .feature-section.one-col > div, 
                .wpbc-welcome-page .feature-section.three-col > div, 
                .wpbc-welcome-page .feature-section.two-col > div {
                    border-bottom: none;
                    margin:0px !important;
                }
                .wpbc-welcome-page .feature-section img{
                    width:98% !important;
                    margin:0 1% !important;
                }
				.about-wrap.wpbc-welcome-page .feature-section div.col {
					display:block;
					float:none;
				}
            }            
        </style><?php
    }
    // SUPPORT /////////////////////////////////////////////////////////////////

        public function show_separator() {
            ?><div class="clear" style="height:1px;border-bottom:1px solid #DFDFDF;"></div><?php
        }


        public function show_header( $text = '' , $header_type = 'h3', $style = '' ) {
            echo '<' , $header_type  ;
            if ( ! empty($style) )
                echo " style='{$style}'";
            echo '>';    
            echo wpbc_recheck_strong_symbols( $text ); 
            echo '</' , $header_type , '>' ;
        }


        public function show_col_section( $sections_array = array( ) ) {

            $columns_num = count( $sections_array );

            if ( isset($sections_array['h3'] ) )
                $columns_num--;
            if ( isset($sections_array['h2'] ) )
                $columns_num--;
            ?>
            <div class="changelog"><?php 

                if ( isset( $sections_array[ 'h3' ] ) ) {
                    echo "<h3>" . wpbc_recheck_strong_symbols( $sections_array[ 'h3' ] ) . "</h3>";
                    unset($sections_array[ 'h3' ]);
                }
                if ( isset( $sections_array[ 'h2' ] ) ) {
                    echo "<h2>" . wpbc_recheck_strong_symbols( $sections_array[ 'h2' ] ) . "</h2>";
                    unset($sections_array[ 'h2' ]);
                }

                ?><div class="feature-section <?php 
                        if ( $columns_num == 2 ) {
                            echo ' two-col';
                        } if ( $columns_num == 3 ) {
                            echo ' three-col';
                        } ?>">
                    <?php
                    foreach ( $sections_array as $section_key => $section ) {
                        $col_num = ( $section_key + 1 );
                        if ( $columns_num == $col_num )
                            $is_last_feature = ' last-feature ';
                        else
                            $is_last_feature = '';

                        echo "<div class='col col-{$col_num}{$is_last_feature}'>";

                        if ( isset( $section[ 'header' ] ) ) 
                            echo "<h4>" . wpbc_recheck_strong_symbols( $section[ 'header' ] ) . "</h4>";

                        if ( isset( $section[ 'h4' ] ) ) 
                            echo "<h4>" . wpbc_recheck_strong_symbols( $section[ 'h4' ] ) . "</h4>";

                        if ( isset( $section[ 'h3' ] ) ) 
                            echo "<h3>" . wpbc_recheck_strong_symbols( $section[ 'h3' ] ) . "</h3>";

                        if ( isset( $section[ 'h2' ] ) ) 
                            echo "<h2>" . wpbc_recheck_strong_symbols( $section[ 'h2' ] ) . "</h2>";

                        if ( isset( $section[ 'text' ] ) ) 
                            echo wpbc_recheck_strong_symbols( $section[ 'text' ] );

                        if ( isset( $section[ 'img' ] ) ) {                         

							$is_full_link = strpos( $section[ 'img' ], 'http' );
							if ( false === $is_full_link ) {
								echo '<img src="' . $this->asset_path . $section['img'] . '" ';
							} else {
								echo '<img src="' . $section[ 'img' ] . '" ';
							}
                            if ( isset( $section[ 'img_style' ] ) ) 
                                echo ' style="'. $section[ 'img_style' ] .'" ';
                            echo ' class="wpbc-section-image" />' ;    
                        }

                        echo "</div>";
                    }
                    ?>        
                </div>                    
            </div>
            <?php
        }

        
        public function get_img( $img, $img_style = '' ) {

			$is_full_link = strpos( $img, 'http' );
			if ( false === $is_full_link ) {
				$img_result = '<img src="' . $this->asset_path . $img  . '" ';
			} else {
				$img_result = '<img src="' . $img  . '" ';
			}

            if ( ! empty( $img_style ) ) 
                $img_result .= ' style="'. $img_style .'" ';
            $img_result .= ' class="wpbc-section-image" />' ;    
            
            return $img_result;
        }
    ////////////////////////////////////////////////////////////////////////////
        
    // Menu    
    public function admin_menus() {
        // What's New
        add_dashboard_page(
                sprintf( 'Welcome to Booking Calendar' ),
                sprintf( 'What\'s New' ),
                $this->minimum_capability, 'wpbc-about',
                array( $this, 'content_whats_new' )
        );
        // Getted Started
        add_dashboard_page(
                sprintf( 'Get Started - Booking Calendar' ),
                sprintf( 'Get Started' ),
                $this->minimum_capability, 'wpbc-getting-started',
                array( $this, 'content_getted_started' )
        );
        // Pro
        add_dashboard_page(
                sprintf( 'Get Premium - Booking Calendar' ),
                sprintf( 'Get Premium' ),
                $this->minimum_capability, 'wpbc-about-premium',
                array( $this, 'content_premium' )
        );
        //FixIn: 8.5.1.2
 		remove_submenu_page( 'index.php', 'wpbc-about' );
        remove_submenu_page( 'index.php', 'wpbc-getting-started' );
        remove_submenu_page( 'index.php', 'wpbc-about-premium' );
    }

    // Head
    public function admin_head() {
        remove_submenu_page( 'index.php', 'wpbc-about' );
        remove_submenu_page( 'index.php', 'wpbc-getting-started' );
        remove_submenu_page( 'index.php', 'wpbc-about-premium' );
    }

    // Title
    public function title_section() {
        list( $display_version ) = explode( '-', WPDEV_BK_VERSION );
        ?>
        <h1><?php printf( 'Welcome to Booking Calendar %s', $display_version ); ?></h1>
        <div class="about-text"><?php
        //echo('Thank you for updating to the latest version!'); 
        // printf(  '%s is more polished, powerful and easy to use than ever before.' , ' Booking Calendar ' . $display_version ); 
        // printf(  '%s has become more powerful and flexible in configuration and easy to use than ever before.' , '<br/>Booking Calendar ');
        printf( 'Booking Calendar is ready to receive and manage bookings from your visitors!' );
        ?></div>


        <h2 class="nav-tab-wrapper">
        <?php
        $is_about_tab_active = $is_about_premium_tab_active = $is_getting_started_tab_active = '';
        if ( ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wpbc-about' ) )
            $is_about_tab_active = ' nav-tab-active ';
        if ( ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wpbc-about-premium' ) )
            $is_about_premium_tab_active = ' nav-tab-active ';
        if ( ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wpbc-getting-started' ) )
            $is_getting_started_tab_active = ' nav-tab-active ';
        ?>
            <a class="nav-tab<?php echo $is_about_tab_active; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
            'page' => 'wpbc-about' ), 'index.php' ) ) ); ?>">
                    <?php echo( "What's New" ); ?>
                <a class="nav-tab<?php echo $is_getting_started_tab_active; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
                'page' => 'wpbc-getting-started' ), 'index.php' ) ) ); ?>">
        <?php echo( "Get Started" ); ?>
                </a><a class="nav-tab<?php echo $is_about_premium_tab_active; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
            'page' => 'wpbc-about-premium' ), 'index.php' ) ) ); ?>">
        <?php echo( "Get even more functionality" ); // echo( "Even more Premium Features" ); ?>
                </a>
        </h2>                
        <?php
    }

    // Maintence section
    public function maintence_section() {

        if ( !( ( defined( 'WP_BK_MINOR_UPDATE' )) && (WP_BK_MINOR_UPDATE) ) )
            return;

        list( $display_version ) = explode( '-', WPDEV_BK_VERSION );
        ?>
        <div class="changelog point-releases">
            <h3><?php echo( "Maintenance Release" ); ?></h3>
            <p><strong><?php printf( 'Version %s',
                $display_version ); ?></strong> <?php printf( 'addressed some minor issues amd improvement in functionality',
                '' ); ?>. 
        <?php printf( 'For more information, see %sthe release notes%s',
                '<a href="https://wpbookingcalendar.com/changelog/" target="_blank">',
                '</a>' ) ?>.
            </p>
        </div>                        
        <?php
    }

    // Start
    public function welcome() {

        $booking_activation_process = get_bk_option( 'booking_activation_process' );
        if ( $booking_activation_process == 'On' )
            return;

        // Bail if no activation redirect transient is set
        if ( ! get_transient( '_booking_activation_redirect' ) )
            return;

        // Delete the redirect transient
        delete_transient( '_booking_activation_redirect' );

        // Bail if DEMO or activating from network, or bulk, or within an iFrame
        if ( wpbc_is_this_demo() || is_network_admin() || isset( $_GET[ 'activate-multi' ] ) || defined( 'IFRAME_REQUEST' ) )
            return;

        // Set mark,  that  we already redirected to About screen               //FixIn: 5.4.5
        $redirect_for_version = get_bk_option( 'booking_activation_redirect_for_version' );
        if ( $redirect_for_version == WP_BK_VERSION_NUM )
            return;
        else
            update_bk_option( 'booking_activation_redirect_for_version', WP_BK_VERSION_NUM );
        
        wp_safe_redirect( admin_url( 'index.php?page=wpbc-about' ) );
        exit;
    }


    // CONTENT /////////////////////////////////////////////////////////////////
    
    public function content_whats_new() {

        $this->css();
        
        ?>
		<style type="text/css">
			.feature-section.two-col {
				display: flex;
				flex-direction: row;
				justify-content: space-between;
				align-items: flex-start;
			}
			.feature-section.two-col .col.col-1{
				width:100%;
			}
			.feature-section.two-col .col.col-2{
				width: 70%;
				padding: 0 0 0 7%;
				box-sizing: border-box;
			}
			@media (max-width: 782px) {
				/* iPad mini and all iPhones  and other Mobile Devices */
				.feature-section.two-col {
					display: block;
				}
			}

			.wpbc-changelog-list ul {
				list-style: outside;
			}
			.wpbc-changelog-list ul li{
				margin-bottom: 0.5em;
				line-height: 1.5em;
			}
			.wpbc-changelog-list ul li strong{
				padding:0 5px;		
			}
			.wpbc_expand_section_link,
			a.wpbc_expand_section_link:hover,
			a.wpbc_expand_section_link:focus {
				color:#21759b;
				cursor: pointer;
				outline: 0;
				border:none;
				border-bottom:1px dashed #21759B;
				text-decoration: none;      
			}
		</style>
		<div class="wrap about-wrap wpbc-welcome-page">

            <?php $this->title_section(); ?>

            <table class="about-text" style="margin-bottom:30px;height:auto;font-size:1em;width:100%;" >
                <tr>
                    <td>
                        <?php  list( $display_version ) = explode( '-', WPDEV_BK_VERSION );  ?>
                        Thank you for updating to the latest version. <strong><code><?php echo $display_version; ?></code></strong>
                        <br/>Booking Calendar has become more polished, powerful and easy to use than ever before.
                    </td>
                    <td style="width:10%">
                        <a  href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-getting-started' ), 'index.php' ) ) ); ?>"
                            style="float: right; height: 36px; line-height: 34px;" 
                            class="button-primary"
                            >&nbsp;<strong>Get Started</strong> <span style="font-size: 20px;line-height: 18px;padding-left: 5px;">&rsaquo;&rsaquo;&rsaquo;</span>
                        </a>
                    </td>
                </tr>
            </table>
            <?php 


            $this->maintence_section();
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.9
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>
			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;
							font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;">8.9</span></h2><?php


			?><div class="feature-section  two-col">

			<div class="col col-1" style="flex: 1 1 50%;width: 100%;">
				<?php
				echo
					'<h4>' .wpbc_recheck_strong_symbols( 'New' ) . '</h4>' .
					'<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Show debug cost information of "Daily costs" and "Additional costs" to better understand how costs are working. Activate it at the Booking > Settings > Payment page in the Payment options section. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to calculate the deposit amount based on daily costs only, without additional costs. Activate it at  the Booking > Settings > Payment page in the Payment options section.  *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to export into .ics feeds only bookings, that was created in the Booking Calendar plugin,  without any  other imported bookings. Activate it at Booking > Settings > Sync > "General" page.  Available in Booking Manager update 2.0.20 or newer. ' ) . '</li>'

											. '</ul>'
											. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Show error message, if activated to use CAPTCHA and PHP configuration does not have activated GD library.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show help message, about troubleshooting of "Request do not pass security check!" error.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Showing centered booking form,  while using simple booking form  configuration.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'The Debug function  shows HTML elements during output of strings.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'If we are using the [cost_corrections] shortcode in the booking form for entering our cost at Booking > Add booking page, then we can use in the New booking emails such shortcodes [corrected_total_cost], [corrected_deposit_cost], [corrected_balance_cost]. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Escape any  html  tags from  the booking resource  titles in emails. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'

											. '</ul>';

				?>
			</div>
			<div class="col col-2 last-feature" style="flex: 1 1 auto;width: 60%;">
				<img src="https://wpbookingcalendar.com/assets/8.9/debug_valuation_days.png"
					style="margin:30px 5px 0;width: 98%;box-shadow: 0 1px 3px #aaa;border-radius: 2px;"
					class="wpbc-section-image" />
				<img src="https://wpbookingcalendar.com/assets/8.9/debug_advanced_costs.png"
					style="margin:30px 5px 0;width: 98%;box-shadow: 0 1px 3px #aaa;border-radius: 2px;"
					class="wpbc-section-image" />

				<img src="https://wpbookingcalendar.com/assets/8.9/rates_debug_show.png"
					style="margin:30px 5px 0;width: 98%;box-shadow: 0 1px 3px #aaa;border-radius: 2px;"
					class="wpbc-section-image" />
				<span style="font-size:0.8em;padding:1em;">* This feature is available in the Booking Calendar Business Medium or higher versions.</span>
			</div>		</div><?php


			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.8
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>
			<div class="clear" style="margin-top:20px;"></div>
			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;
							font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;">8.8</span></h2><?php


			?><div class="feature-section  two-col">
			<div class="col col-1" style="flex: 1 1 auto;width: 60%;">
				<img src="https://wpbookingcalendar.com/assets/9.0/time-picker-premium-black.png"
					style="margin:30px 5px;width: 98%;box-shadow: 0 1px 3px #aaa;border-radius: 2px;"
					class="wpbc-section-image">
			</div>
			<div class="col col-2 last-feature" style="flex: 1 1 50%;width: 100%;">
				<?php
				echo
					'<h4>' .wpbc_recheck_strong_symbols( 'New' ) . '</h4>' .
					'<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Time picker** for **times slots selection** in the booking form. Activate it at the Booking > Settings General page in the "Time Slots" section.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Skins** for **Time picker** available for **times slots selection** in the booking form. Activate it at the Booking > Settings General page in Time Slots section.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Premium calendar skins** now available in **Booking Calendar Free** version.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Form template **2 columns with time slots" for showing booking form fields in 2 columns with time slots selection.  *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'

											. '</ul>'
											. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'More intuitive adding and editing new fields (during editing in simple booking form mode). Showing the "Save changes" button relative only to active action.' ) . '</li>'
											. '</ul>';

				?>
			</div>
		</div><?php



            $this->show_col_section( array(
                                array( 'text' =>
''
											. '<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Error of correct converting special  symbols,  like #, %, \', " to URL symbols during clicking on "Export to Google Calendar" button. (8.7.11.4)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of correct showing special  symbols,  like #, %, \', " in the titles of bookings at  Calendar Overview page. (8.7.11.5)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of no ability to  book  some time slots when activated multiple days selection. (8.7.11.6)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.parseJSON event shorthand is deprecated.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.fn.mousedown() event shorthand is deprecated.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.fn.click() event shorthand is deprecated.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.fn.focus() event shorthand is deprecated.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.fn.change() event shorthand is deprecated.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.isFunction() event shorthand is deprecated.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.fn.bind() event shorthand is deprecated.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Warning jQuery.fn.removeAttr no longer sets boolean properties: disabled.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Fixing issue of incorrectly showing booking date in plugin, if visitor was entered end time as 24:00 instead of 23:59. (8.7.11.1) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Fixing issue of incorrectly showing coupon code discount hints, if activated option "Apply discount coupon code directly to days cost". (8.7.11.2) *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Select first available option (timeslot) in the dropdown list, that showing based on days conditions , after selection of date in calendar. (8.7.11.3) (Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not sending "approved email", if sending email checkbox was unchecked at the Booking > Add booking page and auto approval for Booking > Add booking page has been activated. (8.7.11.8) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
											. '</ul>'
											)
											, array( 'text' =>

											  '<h4>' .wpbc_recheck_strong_symbols( 'Compatibility' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Support **WordPress 5.6**.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Full support of **jQuery 3.5**.' ) . '</li>'
											. '</ul>'

											. '<h4>' .wpbc_recheck_strong_symbols( 'Translation' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Dutch translation by Boris Hoekmeijer.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Swedish translation by Jimmy Sjølander.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Norwegian translation by Jimmy Sjølander.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Spanish translation by Jairo Alzate.' ) . '</li>'
											. '</ul>'

											. '<h4>' .wpbc_recheck_strong_symbols( 'Under Hood' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Trigger event "wpbc_hook_timeslots_disabled" after disabling times in the booking form. To bind this event use this JS: jQuery( ".booking_form_div" ).on( \'wpbc_hook_timeslots_disabled\', function ( event, bk_type, all_dates ){ ... } );' ) . '</li>'
											. '</ul>'

										  )
                                )
                            );


			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.7
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_7' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.7</span></a>

			<div class="version_update_8_7" style="display:none;">

			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;
							font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;">8.7</span></h2><?php

			?>
			<img src="<?php echo $this->asset_path; ?>8.7/booking-calendar-black2.png" style="border:none;box-shadow: 0 0px 2px #bbb;margin: 2%;width:98%;display:block;" />
			<div class="clear"></div>
			<?php

            $this->show_col_section( array(
                                array( 'text' =>
											  '<h4>' .wpbc_recheck_strong_symbols( 'New' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'New **Calendar Skin** with dark colors: "**Black 2**"' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to define using **Pending days as Selectable days** - its means that such days have the colors the same as Pending days, but visitor still can select and submit new booking, until you do not approve some booking. Its useful in case, if you need to show that at some days already exist bookings, but visitors still can submit the booking. Please note, such feature will not work correctly if you will make bookings for specific time-slots (its will show warning). How to Use ? In the page, where you are having Booking Calendar shortcode, you need to define the js, like this: &lt;script type="text/javascript"&gt; wpbc_settings.set_option( "pending_days_selectable", true ); &lt;/script&gt; [booking type=1 nummonths=2] (8.6.1.18)' ) . '</li>'

. '<li>' . wpbc_recheck_strong_symbols( 'Ability to define **dates format** for **search availability form** at the Booking > Settings > Search page. (8.6.1.21) *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Logging** of approving or set as pending bookings to notes section. You can activate this option "Logging of booking approving or rejection" at the Booking > Settings General page in "Booking Admin panel" section. (8.6.1.10) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Updated **iPay88** - Payment Gateway integration v1.6.4 (For Malaysia Only) (8.6.1.3) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
											. '</ul>'

											. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to auto fill "nickname" of user, when user logged in, and checked this option "Auto-fill fields". In booking form have to be field with name "nickname". (8.6.1.2)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Usage of **new Wizard style booking form**, where possible to configure several steps in booking form - **more than 2 steps** (8.6.1.15) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to use option "Use check in/out time", for adding check in/out times to use change over days, when importing events via Google Calendar API (using Google API Key) (8.6.1.1) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to use option "Append check out day", for adding check out day, when importing events via Google Calendar API (using Google API Key) (8.6.1.4) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Disable the edit / cancel / payment request links in the "Booking Customer Listing" view for "Approved bookings", in case, if you have activated this option " Change hash after the booking is approved " at the Booking > Settings General page in Advanced section. (8.6.1.6) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Replace non standard symbols (like: . or , or ` ) in options for ability correct saving Advanced cost. Otherwise sometimes was not possible to save "Advanced cost" at Booking > Resources > Advanced cost page.  (8.6.1.7) *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Added filter hook "wpbc_booking_resources_selection_class" for controlling CSS class in dropdown element of booking resource selections (8.6.1.9) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Update booking hash during moving booking to trash or restore bookings, for do not ability to edit or cancel such bookings by visitor (8.6.1.11) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Add ability to use only labels in shortcode for showing one payment method (its works only with these exact options): [select payment-method "All payment methods@@" "Stripe" "PayPal" "Authorize.Net" "Sage Pay" "Bank Transfer" "Pay in Cash" "iPay88" "iDEAL"] (8.6.1.16) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to  activate updating booking cost after editing booking in admin panel, based on new booking data. You can activate this option  at the Booking > Settings > Payment page  (8.6.1.24) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
											. '</ul>'
										    . '<h4>' .wpbc_recheck_strong_symbols( 'Translation' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'

. '<li>' . wpbc_recheck_strong_symbols( 'German translation [99% completed] by Markus Neumann.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Slovenian translation [99% completed] by Klemen Gaber.' ) . '</li>'
											. '</ul>'

											)
											, array( 'text' =>

											  '<h4>' .wpbc_recheck_strong_symbols( 'Compatibility' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Support **WordPress 5.3** - updated styles of booking admin panel.' ) . '</li>'
											. '</ul>'

											. '<h4>' .wpbc_recheck_strong_symbols( 'Deprecated' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Removing deprecated Timeline v.1. Currently available only new Flex Timeline (Calendar Overview) (8.6.1.13)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Removing deprecated Stripe v.1 integration. Now available only Stripe v.3 integration that support SCA (8.6.1.12) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
											. '</ul>'

											. '<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue Undefined index: name in ../core/admin/wpbc-class-timeline.php on line 2137' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not ability to enter new value of CAPTCHA without page reloading, if previous entered value was incorrect. (8.6.1.8)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Order of week days in Arabic translation for calendar' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show payment description about the booking in Stripe dashboard in Metadata section for Stripe v.3 integration (8.6.1.20) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of showing negative balance hint, during using deposit feature with zero cost (8.6.1.5) *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of incorrectly showing available results in "Advanced search results" (while using the shortcode like this [additional_search "3"] at the Booking > Settings > Search page), and if dates in some resources was marked as unavailable via season filters. (8.6.1.14) *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of incorrectly showing available results, when searching only for 1 specific day (check in/out dates the same in availability form), and we have booked (as full day), this day in specific booking resource. (8.6.1.19) *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of incorrectly disabling end time options in select-box (8.6.1.17) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of slow loading calendar (executing too many sql requests), when season filter was deleted at the Booking > Resources > Filters page, but reference relative (Rates) still exist at Booking > Resources > Cost and rates page. Its means that the Rates was not updated (re-saved) relative specific booking resource at the Booking > Resources > Cost and rates page. (8.6.1.22) *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of possible showing status of Stripe v.3 payment as successful at the Booking Listing page, even when its was not completed yet. (8.6.1.23) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'

											. '</ul>'
										  )
                                )
                            );
			?></div><?php

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.6
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_6' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.6</span></a>

			<div class="version_update_8_6" style="display:none;">

			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;
							font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;">8.6</span></h2><?php
			$this->show_separator();
			/*
			$this->show_col_section( array(

					array( 'h4'   => wpbc_recheck_strong_symbols( 'Different structures of booking forms' ),
						   'text' => wpbc_recheck_strong_symbols( '<ul style="list-style: none;padding: 5px;margin:0;">'
									. '<li>' . 'Ability to define different structures of booking forms at Booking > Settings > Form page' . '</li>'
									. '<li style="list-style: disc inside;">' . '**Vertical** - form under calendar' . '</li>'
   								    . '<li style="list-style: disc inside;">' . '**Side by side** - form at right side of calendar' . '</li>'
   								    . '<li style="list-style: disc inside;">' . '**Centered** - form and calendar are centered' . '</li>'
   								    . '<li style="list-style: disc inside;">' . '**Dark** - form for dark background' . '</li>'
								  . '</ul>' )

						   . '<span style="font-size:0.85em;">' .wpbc_recheck_strong_symbols( 'Available in Booking Calendar **Free** version' ) . '</span>'

					     )
				//, array(  'img'  => '8.1/booking-form-structure-2.png', 'img_style'=>'margin-top:20px;width: 85%;' )
				)
			);
			*/
			/*
			 *	This update in memory of my Father - great, very responsible and lovely person, that set right direction in my life. [ SVI 2.19.52 - 8.6.19 ]
			 */
			?>
			<p style="text-align:center;"><?php echo wpbc_recheck_strong_symbols( 'New interface of **Calendar Overview** in admin panel  and **Timeline** at front-end side with new clean, flex design.'); ?></p>
			<img src="<?php echo $this->asset_path; ?>8.6/flex-timeline-single-month-pure-2.png" style="border:none;box-shadow: 0 0px 2px #bbb;margin: 2%;width:98%;display:block;" />
			<div class="clear"></div>
			<?php

            $this->show_col_section( array(
                                array( 'text' =>
											  '<h4>' .wpbc_recheck_strong_symbols( 'New' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Updated new interface of **Calendar Overview** in admin panel  and **Timeline** at front-end side with new clean, flex design.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Timeline & Calendar Overview** - mobile friendly look.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Timeline & Calendar Overview** - nicely showing several bookings for the same date(s) (dividing day into several rows). For example during bookings for specific times,  while showing Month Timeline view.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Timeline & Calendar Overview** - very handy hints for each day of booking, when mouse over specific booking day.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Timeline & Calendar Overview** - aggregated booking details title marked with different color for easy finding and checking how many bookings in specific date(s).' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Timeline & Calendar Overview** - ability to restore old Timeline look at  Booking > Settings General page in Timeline section.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Section "Calendar Overview | Timeline" at  Booking > Settings General page (8.5.2.20)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Button "**Empty Trash**" at  Booking Listing  page in Action toolbar to completely  delete All bookings from  Trash (8.5.2.24)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to **export only approved bookings into .ics feeds**. Available in Booking Manager plugin since 2.0.11 or newer update. (8.5.2.3)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Do not update cost of booking, while editing this booking. (8.5.2.1)  *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
											. '</ul>'

											. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'More clean colors for booking details at the Booking Listing page (8.5.2.5)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Added <code>[add_to_google_cal_url]</code> - shortcode in "Approved booking" email template for fast manual adding of booking to Google Calendar (8.5.2.13)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'New Flex Template for search form - more nice CSS style for search form and search results (8.5.2.11)  *(Business Large, MultiUser)*' ) . '</li>'
											. '</ul>'


											. '<h4>' .wpbc_recheck_strong_symbols( 'Under Hub' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Useful **hook** for **Auto approve** bookings only for **specific booking resources**: <code>apply_filters( \'wpbc_get_booking_resources_arr_to_auto_approve\', $booking_resources_to_approve );</code>.<br> Add code similar  to this in your functions.php file in your theme,  or in some other php file: <br/><code>function my_wpbc_get_booking_resources_arr_to_auto_approve( $resources_to_approve ) { <br>$resources_to_approve = array( 1, 9, 12, 33 ); <br>return $resources_to_approve; } <br>add_filter( \'wpbc_get_booking_resources_arr_to_auto_approve\', \'my_wpbc_get_booking_resources_arr_to_auto_approve\' );</code>  (8.5.2.27)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Useful **hook** for Google Adwords Conversion tracking: <code>do_action( \'wpbc_track_new_booking\', $params );</code> Add code similar  to this in your functions.php file in your theme,  or in some other php file: <code>add_action( \'wpbc_track_new_booking\', \'my_booking_tracking\' ); <br>function my_booking_tracking( $params ){  <br>//*Your Google Code for Booking Conversion Page*<br>}</code>   (8.5.2.25)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to define rechecking cost with PayPal tax during response of PayPal IPN. Require of adding function like this: <br/><code>function my_wpbc_paypal_ipn_tax( $paypal_tax_percent ){ return 20; } <br/>add_filter( \'wpbc_paypal_ipn_tax\', \'my_wpbc_paypal_ipn_tax\' );</code> (8.5.2.2)  *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'More easy find lost bookings (in booking resource(s) that have been deleted). Now, its show only lost bookings. Use link like this: <br/><code>http://server/wp-admin/admin.php?page=wpbc&wh_booking_type=lost</code> (8.5.2.19)  *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show only one payment system after booking process, if visitor selected payment system in booking form. Example:  of shortcode for showing selection of payment forms: <code>Select payment method: [select payment-method "All payment methods@@" "Stripe@@stripe_v3" "PayPal@@paypal" "Authorize.Net@@authorizenet" "Sage Pay@@sage" "Bank Transfer@@bank_transfer" "Pay in Cash@@pay_cash" "iPay88@@ipay88" "iDEAL@@ideal"]</code>  This solution  was suggested by "Dan Brown". Thank you. (8.5.2.28) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
											. '</ul>'

										    . '<h4>' .wpbc_recheck_strong_symbols( 'Translation' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'

. '<li>' . wpbc_recheck_strong_symbols( 'French translation [100% completed] by Philippe Nowak and Alain Pruvost' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Hungarian translation [99% completed] by Vincze István' ) . '</li>'
											. '</ul>'

											)
											, array( 'text' =>

											  '<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'
											. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of blocking entire day, if in booking form was used start time and end or duration of time fields and visitor use multiple days selection mode, and all start time options for specific day was booked. In multiple day selection mode its incorrect, because user can start days selection at available day, and finish selection with end time at this partially booked day, where no available start-time. Now system block such dates only during single day selection mode. (8.5.2.4)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Disable send button,  after submit booking, for prevent of several same bookings (8.5.2.7)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not showing bookings that  start  from  \'yesterday\' date at Booking Listing  page,  when  selecting \'Current dates\' in Filter toolbar. (8.5.2.14)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not showing bookings that  start  from  \'today\' date at Booking Listing  page,  when  selecting \'Past dates\' in Filter toolbar. (8.5.2.16)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not ability to submit the booking for additional calendar(s),  if used booking form  with  several  calendars and was not selected date(s) in main calendar (8.5.2.26) *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not showing booking resource in search availability results, if resource was booked for specific time-slot on specific date, where we search availability. (8.5.2.7) *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of showing default booking resource instead of "All booking resources" for Regular user in  MultiUser version at the Booking Listing  and Calendar Overview pages,  while was set show "All resources" at  the Booking > Settings General page. (8.5.2.8) *(MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of prevent loading Stripe v.3 at  some systems,  where PHP version lower than PHP 5.4 (8.5.2.9) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of "not auto selecting dates" during editing/cancellation of the booking by  visitor,  and not updating cost / dates hints in some systems. Conflict with  "WPBakery Page Builder" plugin.  (8.5.2.10) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not showing warning message about not checked checkbox, during validation required checkboxes that have several options and one option was checked. (8.5.2.12) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not submitting booking for additional calendars (if using several  calendars and one booking form), if payment form does not show for such  bookings (8.5.2.17) *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of not showing as booked dates in calendar,  that  relative to  change-over days,  while activated "Allow unlimited bookings per same day(s)" option. (8.5.2.18) *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Issue of incorrectly  showing additional  cost  hints for options,  that  was defined as percentage at the Booking > Resources > Advanced cost page. (8.5.2.21) *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Do  not send emails,  if was empty email  field (its possible in situation,  when  in booking form several email  fields for several  persons), otherwise was showing error (8.5.2.22) *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Start using "choozen"  library  for selection of booking resources just during page loading (because library loaded in head), instead of using after  full  page loaded. Its prevent issue of showing wide selectbox during page loading. (8.5.2.23)' ) . '</li>'

											. '</ul>'
										  )
                                )
                            );
			$this->show_separator();
			?></div><?php

			/*
			$this->show_col_section( array(

				    //array(  'img'  => '8.1/booking-calendar-stripe-gateway-2.png', 'img_style'=>'margin-top:20px;width: 85%;' ),

					 array( 'h4'   => wpbc_recheck_strong_symbols( '**Stripe** payment system integration' ),
						   'text' => wpbc_recheck_strong_symbols( '<ul style="list-style: none;padding: 5px;margin:0;">'
									. '<li>' . 'Integration with **<a target="_blank" href="https://stripe.com/">Stripe</a>** payment gateway.' . '</li>'
									. '<li>' . 'Showing on screen (same page) payment form,  with ability to pay by cards.' . '</li>'
   								    . '<li>' . 'Ability to  auto approve or auto  decline booking,  after successful  or failed payment.' . '</li>'
								  . '</ul>' )

						   . '<span style="font-size:0.85em;">' .wpbc_recheck_strong_symbols( 'Available in **Business Small / Business Medium / Business Large / MultiUser** versions' ) . '</span>'

					     )
				)
			);
			*/

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.5
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ?>
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_5' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.5</span></a>

			<div class="version_update_8_5" style="display:none;">

					<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;
									font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;">8.5</span></h2><?php
					$this->show_separator();

								$this->show_col_section( array(
													array( 'text' =>
					  '<h4>' .wpbc_recheck_strong_symbols( 'New' ) . '</h4>'
					. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
					. '<li>' . wpbc_recheck_strong_symbols( '**Highlight code syntax** for **booking form** configuration at Booking > Settings > Form page,  and show warnings about possible issues. (8.4.7.18)  *(Personal Business Small/Medium/Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Highlight code syntax** for **search form** and search results form configuration at Booking > Settings > Search page,  and show warnings about possible issues. (8.4.7.18)  *(Business Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( 'Update of **Stripe** Integration via "**Checkout Server**" method, which use "**Strong Customer Authentication**" (SCA) - a new rule coming into effect on September 14, 2019 as part of PSD2 regulation in Europe, will require changes to how your European customers authenticate online payments. (8.4.7.20)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Approve booking in 1 mouse click** on link in email about new booking sending to Administrator. Even without requirement to login to WordPress admin panel. Its require to  use **[click2approve]** shortcode at Booking > Settings > Emails > New (admin) page. (8.4.7.25)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Decline booking in 1 mouse click** on link in email about new booking sending to Administrator. Even without requirement to login to WordPress admin panel. Its require to  use **[click2decline]** shortcode at Booking > Settings > Emails > New (admin) page. (8.4.7.25)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Trash booking in 1 mouse click** on link in email about new booking sending to Administrator. Even without requirement to login to WordPress admin panel. Its require to  use **[click2trash]** shortcode at Booking > Settings > Emails > New (admin) page. (8.4.7.25)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( 'Ability to define sort **order of search  availability results** at the Booking > Settings > Search page. (8.4.7.8) *(Business Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '** Experimental Feature**. Trash all imported bookings before new import. Move all previously imported bookings to trash before new import bookings. Its can **resolve issue of updating deleted and edited events in external sources**. Activate this option at Booking > Settings > Sync > "General" page. Its work only, if you are using one source (.ics feed) for importing into specific booking resource! Work only in update of Booking Manager 2.0.10 or newer. (8.4.7.12)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Force import**. Ability to import bookings without checking, if such bookings already have been imported. Activate this option at Booking > Settings > Sync > "General" page.  Available in the Booking Manager 2.0.10 or newer. (2.0.10.1)(8.4.7.1)' ) . '</li>'
					. '</ul>'

					. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'
					. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
					. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Changed color of "Imported" label for bookings in Booking Listing page (8.4.7.2)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Show "Do you really want to do this ?" popup, when admin try to Trash or Delete booking in Calendar Overview page (8.4.7.14)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Show button "Find Lost Bookings" at the Booking Settings General page in Help  section,  for ability to  show all  exist  bookings, and find possible some lost bookings. (8.4.7.19)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Booking Calendar does not require jquery-migrate library, as obligatory library anymore. Its means that plugin can work with latest jQuery versions (like 3.4.1) just in strait way, without additional libraries. (8.4.7.23)' ) . '</li>'

					. '<li>' . wpbc_recheck_strong_symbols( '**Improvement**. Checking for seasonal availability in "child booking resources" during submitting booking for booking resource with specific capacity. If you have set unavailable dates in child booking resource via season filters, system will not save bookings in this child booking resource. (8.4.7.3) *(Business Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Improvement**. Set as unavailable the end time fields options,  depend from  selected date with booked timeslots (8.4.7.6) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Improvement**. Added autocomplete Off to the search form fields,  to  prevent of showing tooltips in search fields. (8.4.7.7) *(Business Large, MultiUser)*' ) . '</li>'
					. '</ul>'

					)
					, array( 'text' =>
					 '<h4>' .wpbc_recheck_strong_symbols( 'Translation' ) . '</h4>'
					. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'

					. '<li>' . wpbc_recheck_strong_symbols( 'New Romanian translation by Silviu Nita' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( 'Update of Slovenian translation by Klemen Gaber' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( 'Update of Dutch translation by Boris Hoekmeijer' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( 'Update of German translation by Dominik Ziegler' ) . '</li>'
					. '</ul>'

					. '<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'
					. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Issue of not working "Read All" button (issue was exist  in updates 8.4.5, 8.4.6. (8.4.7.15)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Issue of incorrectly  showing months scroll in calendar at some iPads (8.4.7.17)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Issue of not showing bookings for "Today" date in Booking Listing page, when bookings was made for entire date. (8.4.7.21)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Issue of showing bookings,  that was made during "Today" date in Booking Listing page. Previously system  was show some bookings, that was made yesterday, as well. (8.4.7.22)' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Warnings in PHP 7.2 relative INI directive safe_mode is deprecated since PHP 5.3 and removed since PHP 5.4 (8.4.7.24)' ) . '</li>'

					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Warning: Invalid argument supplied for foreach() in ..\multiuser.php on line 558 (8.4.7.4) *(MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Showing of users in Booking > Settings > Users page in WordPress MU installation (8.4.7.5) *(MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Issue with Stripe payment,  when "Subject" have too long description with  dates to book. (8.4.7.10) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Translation  issue of Completed payment status (8.4.7.11) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Showing of showing dates instead of titles of booking resources in Timeline,  when  some Regular  user  was logged in and try  to  scroll timeline (8.4.7.13) *(MultiUser)*' ) . '</li>'

					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Showing Notice: Undefined offset: 9 in ../inc/_bl/wpbc-search-availability.php on line 689 (8.4.7.16) *(Business Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Issue of not updating cost by  making booking at  Booking > Add booking page, while using [cost_correction] shortcode in the booking form (8.4.7.28) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
					. '<li>' . wpbc_recheck_strong_symbols( '**Fix**. Issue of not showing change over days in calendar for single booking resource (capacity = 1),  where maximum  number of visitors > 1 (8.4.7.29) *(Business Large, MultiUser)*' ) . '</li>'

					. '</ul>'
														  )
													)
												);
					$this->show_separator();
			?></div><?php

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.4
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ?>

			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_4' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.4</span></a>

			<div class="version_update_8_4" style="display:none;">

				<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;"
					>8.4</span></h2><?php
				$this->show_separator();
				//   <!--iframe width="560" height="315" src="https://www.youtube.com/embed/kLrI7zqKeQQ?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe-->
				?><div style="text-align: center;margin-top:2em;"><iframe width="560" height="315" src="https://www.youtube.com/embed/rpg1kApZCdw?rel=0&amp;start=0&amp;rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div><?php
				?><div style="width:100%;font-size:0.8em;margin:2em 1em;text-align: center;">
					<?php
					printf( 'For more information about current update, see %srelease notes%s',
							'<a class="" href="https://wpbookingcalendar.com/changelog/" target="_blank">', '</a>.' );
					?>
				</div><?php
			?></div><?php

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.3
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
            ?>
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_3' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.3</span></a>

			<div class="version_update_8_3" style="display:none;">

				<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;"
					>8.3</span></h2><?php
				$this->show_separator();
	//							   <!--iframe width="560" height="315" src="https://www.youtube.com/embed/kLrI7zqKeQQ?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe-->
				?><div style="text-align: center;margin-top:2em;"><iframe width="560" height="315" src="https://www.youtube.com/embed/-pOTMiyp6Q8?rel=0&amp;start=0&amp;rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div><?php
				?><div style="width:100%;font-size:0.8em;margin:2em 1em;text-align: center;">
					<?php
					printf( 'For more information about current update, see %srelease notes%s',
							'<a class="" href="https://wpbookingcalendar.com/changelog/" target="_blank">', '</a>.' );
					?>
				</div><?php
			?></div><?php
            ?>
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_2' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.2</span></a>

			<div class="version_update_8_2" style="display:none;">

				<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;"
					>8.2</span></h2><?php
				$this->show_separator();
	//							   <!--iframe width="560" height="315" src="https://www.youtube.com/embed/kLrI7zqKeQQ?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe-->
				?><div style="text-align: center;margin-top:2em;"><iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list=PLabuVtqCh9dzBEZCIqayAfvarrngZuqUl&amp;start=0&amp;rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div><?php

				?><div style="width:100%;font-size:0.8em;margin:2em 1em;text-align: center;">
					<?php
					printf( 'For more information about current update, see %srelease notes%s',
							'<a class="" href="https://wpbookingcalendar.com/changelog/" target="_blank">', '</a>.' );
					?>
				</div><?php

			?></div><?php
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.1
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_1' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.1</span></a>

			<div class="version_update_8_1" style="display:none;">

			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;
							font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;">8.1</span></h2><?php
			$this->show_separator();

			$this->show_col_section( array(

					array( 'h4'   => wpbc_recheck_strong_symbols( 'Different structures of booking forms' ),
						   'text' => wpbc_recheck_strong_symbols( '<ul style="list-style: none;padding: 5px;margin:0;">'
									. '<li>' . 'Ability to define different structures of booking forms at Booking > Settings > Form page' . '</li>'
									. '<li style="list-style: disc inside;">' . '**Vertical** - form under calendar' . '</li>'
   								    . '<li style="list-style: disc inside;">' . '**Side by side** - form at right side of calendar' . '</li>'
   								    . '<li style="list-style: disc inside;">' . '**Centered** - form and calendar are centered' . '</li>'
   								    . '<li style="list-style: disc inside;">' . '**Dark** - form for dark background' . '</li>'
								  . '</ul>' )

						   . '<span style="font-size:0.85em;">' .wpbc_recheck_strong_symbols( 'Available in Booking Calendar **Free** version' ) . '</span>'

					     )
				, array(  'img'  => '8.1/booking-form-structure-2.png', 'img_style'=>'margin-top:20px;width: 85%;' )
				)
			);

			$this->show_separator();

			$this->show_col_section( array(

				    array(  'img'  => '8.1/booking-calendar-stripe-gateway-2.png', 'img_style'=>'margin-top:20px;width: 85%;' )

					, array( 'h4'   => wpbc_recheck_strong_symbols( '**Stripe** payment system integration' ),
						   'text' => wpbc_recheck_strong_symbols( '<ul style="list-style: none;padding: 5px;margin:0;">'
									. '<li>' . 'Integration with **<a target="_blank" href="https://stripe.com/">Stripe</a>** payment gateway.' . '</li>'
									. '<li>' . 'Showing on screen (same page) payment form,  with ability to pay by cards.' . '</li>'
   								    . '<li>' . 'Ability to  auto approve or auto  decline booking,  after successful  or failed payment.' . '</li>'
								  . '</ul>' )

						   . '<span style="font-size:0.85em;">' .wpbc_recheck_strong_symbols( 'Available in **Business Small / Business Medium / Business Large / MultiUser** versions' ) . '</span>'

					     )
				)
			);

			$this->show_separator();


	    $this->show_col_section( array(
			    array( 'text' =>
							 '<h4>' .wpbc_recheck_strong_symbols( 'New' ) . '</h4>'
							. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
							. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to insert modification/creation date or (Year, month, day, hours,  minutes or seconds) of booking into email templates or in payment summary' ) . '</li>'
							 . '<li>' . wpbc_recheck_strong_symbols( '**New.** Shortcode for showing check out date plus one additional day: <code>[check_out_plus1day_hint]</code> at Booking > Settings > Form page. (8.0.2.12) *(Business Medium/Large, MultiUser)*' ) . '</li>'
							. '</ul>'
							. '<h4>' .wpbc_recheck_strong_symbols( 'Translation' ) . '</h4>'
							. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
							. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Spanish translation [100% completed] by Martin Romero' ) . '</li>'
							. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Galician (Spanish) translation [100% completed] by Martin Romero' ) . '</li>'
							. '</ul>'

							. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'
							. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
							. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Improve admin UI styles in Chrome browser, by setting more sleek view of UI elements (8.0.2.4/5)' ) . '</li>'
							. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Do not export to .ics feed bookings, that inside of Trash folder (8.0.2.7)' ) . '</li>'
							. '</ul>'
			    )
			    , array( 'text' =>

							 '<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'
				           . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'

				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** showing booking listing correctly  for "next  1 month" without bookings,  that  include past ("yesterday day") bookings (8.0.1.1)' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** force to load jquery-migrate in case, if we do  not know the version  of jQuery which  was loaded. (8.0.1.2)' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of showing warning "parsererror ~ SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data" during import process,  when  some bookings already  was imported (8.0.2.1)' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** add support of Apache 2.4 directives relative captcha saving.' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of showing warning: "Email different from website DNS, its can be a reason of not delivery emails" at Booking > Settings > Emails page, in case if website DNS starting with "www." ot some other sub-domain. (8.0.2.9)' ) . '</li>'

				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** showing correctly  change-over days (triangles),  when  inserted only "availability calendar", without booking form (8.0.1.2) *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** ability to use symbol **/** in placeholders in booking form fields shortcodes at Settings Form page (8.0.1.13) *(Personal Business Small/Medium/Large, MultiUser)*' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** correctly showing single and double quotes (\' and ") symbols in textarea during editing booking (8.0.1.3) *(Personal Business Small/Medium/Large, MultiUser)*' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of not saving changes during editing, if you try to search some booking resource (or other item), and this booking resource was not at the 1st  page (during usual listing)  (8.0.1.12) *(Personal Business Small/Medium/Large, MultiUser)*' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of incorrect  cost calculation, during editing booking,  when selected days from 1 to 9 and used some rates. Issue relative of not using leading 0 in textarea. (8.0.2.2) *(Business Medium/Large, MultiUser)*' ) . '</li>'
				           . '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of showing coupon discount description,  does not depend from uppercase or lowercase of entered coupon code (8.0.2.7) *(Business Large, MultiUser)*' ) . '</li>'
				           . '</ul>'
			    )
		    )
	    );
	    $this->show_separator();
		?></div><?php

	    //--
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 8.0
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_8_0' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">8.0</span></a>

			<div class="version_update_8_0" style="display:none;">

			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar <span style="font-size: 1.1em;
						font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;">8.0</span></h2><?php 
			$this->show_separator();
            ?><!--h2 style='font-size: 1.6em;margin:40px 0 0 0;text-align: left;'>Changes in all versions</h2--><?php
            
			?><h2 style='font-size: 1.6em;margin:20px 0 -10px 0;'>Sync bookings between different sources easily via <strong>.ics</strong> feeds</h2><?php
			if(0) {
			?>
		<span class="wpbc-settings-notice notice notice-info" 
			 style="text-align: left;border-top: 1px solid #f0f0f0;border-right: 1px solid #f0f0f0;line-height: 2em;padding: 5px 20px;display: block !important;margin: 40px 0 -30px;border-left-color: #00a0d2;"
			 >
			<?php
				$message_ics = sprintf( 
						__( '.ics - is a file format of iCalendar standard for exchanging calendar and scheduling information between different sources %s Using a common calendar format (.ics), you can keep all your calendars updated and synchronized.', 'booking' )
						, '<br/>' /*
						'<br/><em>(<strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
						. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
						. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
						. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
						. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
						. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '
						. str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), 
									 __( 'and any other calendar that uses .ics format', 'booking' )
									)
						. ')</em>.<br/>' */
					);
				$message_ics = str_replace( array( '.ics', 'iCalendar' ), array( '<strong>.ics</strong>', '<strong>iCalendar</strong>' ), $message_ics );
				echo $message_ics;
			?>
		</span>
			<?php
			}
			
            $this->show_col_section( array( 
                                  
                                  array( 'h4'   => wpbc_recheck_strong_symbols( 'Import of **.ics** feeds (files)' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Native integration  with our **<a target="_blank" href="https://wpbookingcalendar.com/faq/booking-manager/">Booking Manager</a>** plugin for ability to import **.ics** feeds or files' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Import external **.ics** feeds via shortcodes at pages. 
							It gives a great flexibility to import **.ics** feeds from different sources <em>(like ' ) 
						. '<strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
						. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
						. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
						. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
						. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
						. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '						
						. ' or any other calendar that uses .ics format)</em> ' 
						. ' into same booking resource.'
						. ' <br>Its means that  you can import bookings or events from different sources into same resource.'
									  . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Define different parameters in this import shortcode. For example, you can  set "start from" and "finish to" date condition or maximum number of items to import or import only events for available dates in exist calendar,	etc...' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Additionally you can configure your server CRON for periodically access these pages and import .ics feeds in automatic way.' ) . '</li>'
									   									  
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '8.0/import-ics2.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            ); 

			$this->show_separator();	

            $this->show_col_section( array( 
                                  array(  'img'  => '8.0/export-ics.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                  , array( 'h4'   => wpbc_recheck_strong_symbols( 'Export of **.ics** feeds (files)' ), 
                                         'text' =>   
                                                     '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Native integration  with our **<a target="_blank" href="https://wpbookingcalendar.com/faq/booking-manager/">Booking Manager</a>** plugin for ability to export **.ics** feeds' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Configure specific ULR feed(s) at setting page' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Use this URL(s) in external websites <em>(like ' ) 
						. '<strong><a href="https://www.airbnb.com/help/article/99/how-do-i-sync-my-airbnb-calendar-with-another-calendar" target="_blank">Airbnb</a></strong>, '
						. '<strong><a href="https://partnersupport.booking.com/hc/en-us/articles/213424709-How-do-I-export-my-calendar-" target="_blank">Booking.com</a></strong>, '
						. '<strong><a href="https://help.homeaway.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">HomeAway</a></strong>, '
						. '<strong><a href="https://rentalsupport.tripadvisor.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">TripAdvisor</a></strong>, '
						. '<strong><a href="https://help.vrbo.com/articles/How-do-I-export-my-calendar-data-to-a-Google-calendar" target="_blank">VRBO</a></strong>, '
						. '<strong><a href="https://helpcenter.flipkey.com/articles/FAQ/noc-How-does-calendar-sync-work" target="_blank">FlipKey</a></strong> '						
						. ' or any other calendar that uses .ics format)</em> '			  
						. ' for ability to  import bookings into  these third-party  websites.'									  
. '<li>' . wpbc_recheck_strong_symbols( 'Or you can simply  download .ics file for later  use in some application.' ) . '</li>'									  
									  . '</li>'									  
                                                    . '</ul>'
                                      )                                 
                                ) 
                            );  
			$this->show_separator();	
    
            
            $this->show_col_section( array(                                    
                                array( 'text' =>   
 '<h4>' .wpbc_recheck_strong_symbols( 'Translation' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
									
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Dutch translation [100% completed] by Boris Hoekmeijer and Alex Rabayev and Iris Schuster' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Finnish translation [98% completed] by Teemu Valkeapää' ) . '</li>' 
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Chinese (Taiwan) translation [98% completed] by Howdy Lee' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Norwegian translation [98% completed] by Bjørn Kåre Løland' ) . '</li>' 
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Brazilian Portuguese translation [98% completed] by Rafael Rocha' ) . '</li>'
. '</ul>'		
									
. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** New setting option for activation showing system  debug log,  for Beta features. Useful in case, if need to find reason, if something was going wrong. You can activate it at the Booking > Settings General page in Advanced section after clicking on "Show advanced settings of JavaScript loading." ( 7.2.1.15 )' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Showing system  messages one under other instead of replacing each other  in admin panel. Its possible to hide top one and see previous notices (7.2.1.16)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Show in "New (visitor)" email (that is sending to the visitor after new booking) the date that is one day  previous to  the last  selected day,  by using this shortcode: <code>[check_out_minus1day]</code> (7.2.1.6)' ) . '</li>'

. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Shortcode for showing coupon discount value of the booking: <code>[coupon_discount_hint]</code> at Booking > Settings > Form page. **(Business Large, MultiUser)**' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Discount coupon codes will not depend from symbols lowercase or uppercase. Prevent of saving coupon codes with specific symbols, which can generate issue of not showing discount at payment form.  (7.2.1.3) **(Business Large, MultiUser)**' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Show "blank" bookings with  different border color at Calendar Overview page. (7.2.1.8) **(Personal Business Small/Medium/Large, MultiUser)**' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Apply "Valuation days" cost  settings "For = LAST",  even if previous TOGATHER = X% settings was applied. (7.2.1.20) **(Business Medium/Large, MultiUser)**' ) . '</li>'
. '</ul>'							
									
)
, array( 'text' =>   				
  '<h4>' .wpbc_recheck_strong_symbols( 'Under Hood' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Under Hood.** New **API File** <code>/{Booking Calendar Folder}/core/wpbc-dev-api.php</code> - well documented list of functions and hooks that possible to use for third-party integrations.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Under Hood.** New column in booking resources table for saving export info (7.2.1.13) **(Personal Business Small/Medium/Large, MultiUser)**' ) . '</li>'
. '</ul>'
	
. '<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Correctly load custom jQuery via https (in some servers), if website is using SSL ( 7.2.1.4 )' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Compatibility issue with  other plugins,  during expand/collapsing sections at  settings pages (7.2.1.10)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Additional  checking about $_SERVER variables, for preventing of showing "Warning Notices" at  some servers ( 7.2.1.17 )' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Loading correct language, if language was set to English in user profile but in WordPress > General > Settings page was set some other default language ( 7.2.1.21 )' ) . '</li>'
	
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Issue of not showing search results (during searching in same page - ajax request), when  using custom fields parameters and selected - "" (which is means "any value") ( 7.2.1.5 ) **(Business Large, MultiUser)**' ) . '</li>'		
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Issue of showing correct  number of decimal digits depend from  cost  format,  in calendar days cells and mouse-over tooltips ( 7.2.1.11) **(Business Medium/Large, MultiUser)**' ) . '</li>'	
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Do not check about required fields, if the fields are hidden (7.2.1.12) **(Personal Business Small/Medium/Large, MultiUser)**' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Issue of not showing links for booking resources in timeline after scrolling, if using (resource_link) parameter with links in timeline shortcode. (7.2.1.14) **(Personal Business Small/Medium/Large, MultiUser)**' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** "Request-URI Too Long" fatal error at "Calendar Overview" page,  when visitor have too many  booking resources (7.2.1.18) **(Personal Business Small/Medium/Large, MultiUser)**' ) . '</li>'

. '</ul>'									
                                      )                                 
                                ) 
                            );  
			$this->show_separator();			
			?></div><?php



			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 7.1 - 7.2
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>							
			<div class="clear" style="margin-top:20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
			   onclick="javascript:jQuery( '.version_update_7_2' ).toggle();"
		    >+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">7.1 - 7.2</span></a>
		
			<div class="version_update_7_2" style="display:none;">				

			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar 7.1 - 7.2</h2><?php 
			$this->show_separator();
            ?><h2 style='font-size: 1.6em;margin:40px 0 0 0;text-align: left;'>Changes in all versions</h2><?php
            
            $this->show_col_section( array( 
                                  
                                  array( 'h4'   => wpbc_recheck_strong_symbols( 'Fast Adding bookings to Google Calendar' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Manual **export to Google Calendar** of specific booking by clicking on **Export** button near booking at Booking Listing page' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Shortcode <code>[add_to_google_cal_url]</code> in email template (to admin) for fast manual (adding) of booking to Google Calendar' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.2/add-to-google-calendar.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();
            $this->show_col_section( array( 
                                  array(  'img'  => '7.2/timeline-hours-limit.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                  , array( 'h4'   => wpbc_recheck_strong_symbols( '**Timeline** tricks' ), 
                                         'text' =>   
                                                     '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Limit times for showing cells in TimeLine for 1 day view mode. <br>For Example: <code>[bookingtimeline type=\'1\' limit_hours=\'9,22\']</code>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Constant **WP_BK_TIMILINE_LIMIT_HOURS** in wpbc-constants.php file. Limit times for showing cells in Calendar Overview page in admin panel for 1 day view mode. ' ) . '</li>'
                                                    . '</ul>'
                                      )                                 
                                ) 
                            );  
	
    $this->show_separator();
            
            $this->show_col_section( array(                                    
                                array( 'text' =>   
 '<h4>' .wpbc_recheck_strong_symbols( 'Translation' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
									
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Danish translation [100% completed] by Daniel Moesgaard' ) . '</li>' 
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Swedish translation [100% completed] by Mikael Göransson' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Translation.** Italian translation [100% completed]' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Hebrew translation [100% completed] by Alex Rabayev and Iris Schuster' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Translation** Arabic translation [84% completed]' ) . '</li>' 
. '<li>' . wpbc_recheck_strong_symbols( '**Translation.** Dutch translation [99% completed] ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Translation.** German translation [99% completed]' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Translation.** French translation [99% completed]' ) . '</li>'
. '</ul>'
									
. '<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** remove today day highlighting in calendar, after loading of page (7.1.2.8)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** additional checking of correct loading popover function to  prevent JavaScript error. If visitor disable loading of Bootstrap files or because of some JS conflict,  instead of showing JavaScript error system  will skip showing popover tooltip when mouse over days in calendar,  or when click on booking in timeline. (7.0.1.2)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** added checking about minimum required version of WordPress for using Booking Calendar  (7.0.1.6)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Ability to  use <code>[reason]</code> or <code>[approvereason]</code> in Booking > Settings > Emails > Approve email template.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Prepare functionality for removing language folder from plugin in a future, for reducing size of plugin. (7.0.1.53)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Showing popovers in timeline (calendar  overview) only at  bottom  direction for better looking at mobile devices (7.0.1.42)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Set color of placeholder text in settings fields lighter. (7.0.1.54)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement.** Increase time for script execution during initial activation  of plugin. (7.0.1.57)' ) . '</li>'
. '</ul>'							
									
. '<h4>' .wpbc_recheck_strong_symbols( 'Under Hood' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Under Hood.** do_action( \'wpbc_jquery_unknown\' )  - new hook  for situation,  when we can not make identification version of jQuery,  sometimes,  need manually to  load  jquery-migrate (7.0.1.33)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Under Hood.** Trigger event "timeline_nav" after clicking navigation  in timeline. To bind this event use this JS: <code>jQuery( ".wpbc_timeline_front_end" ).on(\'timeline_nav\', function(event, timeline_obj, nav_step ) { ... } );</code> (7.0.1.48)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Under Hood.** New constant. <code>WP_BK_AUTO_APPROVE_WHEN_IMPORT_GCAL</code> - Auto  approve booking,  if imported from Google Calendar. Default set to false (7.0.1.59)' ) . '</li>'
. '</ul>'
)
, array( 'text' =>   				
'<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** some translation issue (7.1.2.1)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of showing today bookings in Booking Listing page (7.1.2.8)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Renamed Greek translation files from booking-el_GR.mo to booking-el.mo (booking-el_GR.po to booking-el.po) Its seems that  default locale for Greek  is \'el\' (7.1.2.10)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** add possibility to check  and load file for \'short\' translation locale (like \'en\'), if file for \'long\' locale (like \'en_US\') was not found in translation folder. (7.1.2.11)' ) . '</li>'	
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Update captcha 1.1 to captcha 1.9,  which protect from potensional PHP file inclusion vulnerability (7.0.1.67)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Minimum version of jQuery required as 1.9.1' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Issue of disabling sending emails during approving or cancellation of bookings at  Booking Listing or Calendar Overview pages,  when checkbox "Emails sending" unchecked. (7.0.1.5)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Issue of **auto import events** from Google Calendar into the Booking Calendar (7.0.1.9)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** Issue of generating **JavaScript errors** when  user  deactivated loading of Bootstrap JS files at Booking Settings General page in Advanced section. Instead of it show warning message or skip showing tooltips. (7.0.1.10)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of order loading translation,  if default language is not English  (7.0.1.12)    ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of redirection  to "Thank  you" page. Using home_url (www.server.com) instead of site_url (www.server.com/wordpress/) at some servers. (7.0.1.20)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of ability to translate options in selectbox in format like <code>Eng 1 [lang=it_IT] Italian 1</code> at Settings Fields page in Booking Calendar Free version  (7.0.1.21)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** set email field as required field in Booking Calendar Free version  (7.0.1.22)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of not sending emails, if server was set with using error_reporting(E_STRICT); and show this warning: "PHP Strict Standards: Only variables should be passed by reference in /wp-content/plugins/booking/core/admin/page-email-new-admin.php on line 1105"  (7.0.1.32)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of not submitting booking in IE. Issue relative to  note support by IE String.trim() function. (7.0.1.39)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of showing additional slashes in emails at reason of cancellation (7.0.1.46) (Also  fixed same issue for approve reason, payment request  text and adding notes to the booking).' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of showing in TimeLine (Calendar Overview) 1st day  of next Month, that  does not belong to current visible month. Sometimes in such  view if booking starting from 1st day of next month, system does not show this booking, and its can confuse visitors. (7.0.1.47)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Fix** issue of not saving Booking > Settings General page if pressed on Save Changes button  at top right side in French language,  and some other languages (7.0.1.56)' ) . '</li>'
. '</ul>'

									
                                                                                          
                                      )                                 
                                ) 
                            );  

	
	
	
	    ?><h2 style='font-size: 1.6em;margin:40px 0 0 0;text-align: left;'><?php echo wpbc_recheck_strong_symbols( 'Changes in **Personal / Business Small / Business Medium / Business Large / MultiUser** versions' ); ?></h2><br/><?php
    
		$this->show_separator();

            $this->show_col_section( array( 
                                  
                                  array( 'h4'   => wpbc_recheck_strong_symbols( 'iDEAL payment gateway' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Integration of **iDEAL via Sisow** payment gateway. (7.0.1.64) <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.2/ideal-settings.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();
            $this->show_col_section( array( 
                                  array(  'img'  => '7.2/change-over-days.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                  , array( 'h4'   => wpbc_recheck_strong_symbols( '**Change over days as triangles**' ), 
                                         'text' =>   
                                                     '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( ' **New.** **Show change over days as triangles.** <em>Beta Feature</em>. Its require showing calendar days cells as square (not rectangle). Width and height of calendar you can define in shortcode options parameter. Supported by: Chrome 36.0+, MS IE 10.0+, Firefox 16.0+, Safari 9.0+, Opera 23.0+ (7.0.1.24) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'                                                    . '</ul>'
                                      )                                 
                                ) 
                            );  
    $this->show_separator();
		
            $this->show_col_section( array(                                    
                                array( 'text' =>   
									
'<h4>' .wpbc_recheck_strong_symbols( 'Improvement' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement** New form template with  30 minutes time-slots selection at Booking > Settings > Form page (7.1.2.6) <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement** Ability to  add empty parameter "&booking_hash" to URL in browser at  Booking > Add booking page for ability to add bookings for past  days (7.1.2.10) <em>(Personal Business Small/Medium/Large, MultiUser)</em>' ) . '</li>' 
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement** Ability to use "Valuation days" cost  settings, if activated "Use time selections as recurrent time slots" and set  cost "per 1 day" and option "Time impact to cost" deactivated at  Booking > Settings > Payment page. Useful, when  need to set cost  per days, but also save time-slots during booking on several days. (7.1.2.11) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'

. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Ability to set lower interval (15, 30 or 45 minutes) for auto cancellation pending bookings that have no successfully paid status (7.0.1.25)  <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Ability to  use aggregate parameter  in the <code>[bookingedit]</code> shortcode  (7.0.1.26) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Ability to  use in field "From Name" in email templates at Booking - Settings - Emails page different shortcodes from booking form,  like <code>[name] [secondname]</code> (7.0.1.29) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Ability to  show in cost_hints negative (discounted) cost for additional items. Previously  system  set instead of negative value just 0 (7.0.1.30) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Increase accuracy of rates calculation, if we are having more than 2 digits after comma in rates configurations  (7.0.1.44) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Ability to use HTML tags in popup window during sending payment request and then showing <code>[paymentreason]</code> in email template with  HTML formating (7.0.1.60) <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Showing "blank  bookings" in Calendar Overview page with  different color (red) (7.0.1.40) <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Improvement.** Showing all title for booking resources with long name (longer than 19 symbols) at the Booking Listing page. Previously  its was cutted of (7.0.1.66) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
									
									
. '</ul>'							
									
. '<h4>' .wpbc_recheck_strong_symbols( 'Under Hood' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( ' **New** Constant WP_BK_CHECK_IF_CUSTOM_PARAM_IN_SEARCH in wpbc-constants.php file. Check in search  results custom fields parameter that  can  include to  multiple selected options in search  form.  Logical OR (7.1.2.9) <em>(Business Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Trick** Using in Booking > Resources page parameter "show_all_resources" in browser URL, will  show all booking resources,  even lost booking resources. Lost  booking resources can be, if you was assigned as parent booking resource to single booking resource,  itself. (7.1.2.2) <em>(Business Large, MultiUser)</em>' ) . '</li>'

. '<li>' . wpbc_recheck_strong_symbols( ' **New.** Ability to define links for booking resource titles in TimeLine. Example: <code>[bookingtimeline  ... options=\'{resource_link 3="http://beta/resource-apartment3-id3/"},{resource_link 4="http://beta/resource-3-id4/"}\' ... ]</code> (7.0.1.50) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Tip.** Skip showing rows of booking resource(s) in TimeLine or Calendar Overview, if no any exist booking(s) for current view. For activation this feature you need to add only_booked_resources parameter to the URL. For example: http://server.com/wp-admin/admin.php?page=wpbc&view_mode=vm_calendar&only_booked_resources  Its have to  improve speed of page loading,  when  we are having too many resources at  the page. (7.0.1.51) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'

. '<li>' . wpbc_recheck_strong_symbols( ' **Under Hood.** Trigger event "show_cost_hints" after showing cost or time hints in booking form. To bind this event use this JS: jQuery( ".booking_form_div" ).on(\'show_cost_hints\', function(event, bk_type ) { ... } ); (7.0.1.53) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Under Hood.** Add automatically new payment system, after visit Settings Payment page, if payment system folder and file(s) was created correctly. (7.0.1.55,7.0.1.61) <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
									
. '</ul>'
)
, array( 'text' =>   				
'<h4>' .wpbc_recheck_strong_symbols( 'Fixes' ) . '</h4>'									
. '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
	
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** do not show option for ability to select as parent booking resource itself, at Booking > Resources page. Its prevent from generating lost booking resources. (7.1.2.3) <em>(Business Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of not having access in modal  windows (like payment request) to enter some data,  when opened page with  mobile device (7.1.2.7) <em>(Personal Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue in Danish translation,  which  was show warning at Booking > Settings > Payment > Bank transfer page (7.1.2.9) <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'    
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of showing &ampp;#36, instead of $ symbol in the Booking Listing,  if was used in "Content of booking fields data" form HINT cost shortcodes (7.1.2.12) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'        
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of hiding selection of booking resources field after submit of booking (7.1.2.13) <em>(Personal Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of not checking (during booking submit process) elements from  conditional fields logic, if these fields does not visible. (7.1.2.14) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'	

. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of not showing "reason of cancellation" in emails, that are sending after auto-cancellation of pending not successfully paid bookings. (7.0.1.1) <em>(Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of incorrectly  booking cost calculation if setted cost  "per 1 night" and previously was used "Valuation days" cost  settings for specific booking resource. (7.0.1.4) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** Do not apply "LAST" cost option for "Valuation days" if previously  was applied "Together" term. No need to  apply "LAST", because its have to be already calculated in together term (7.0.1.7) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** Correctly replacing shortcodes with custom URL parameter, like: \'visitorbookingediturl\', \'visitorbookingcancelurl\', \'visitorbookingpayurl\' in email templates. (7.0.1.8) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of showing notice: "Use of undefined constant <code>WPDEV_BK_LOCALE_RELOAD</code>" in seacrh  results  (7.0.1.9) <em>(Business Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of start showing timeline in "Day view" starting from Today date based on WordPress timezone. (7.0.1.13)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of not showing some bookings,  which was made for specific times in 1 day view mode. (7.0.1.16)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of saving additional  cost  at the Booking > Resources > Advanced cost page,  if some options have LABELs (options still  must  be simple words) with  umlauts. (7.0.1.27) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of updating <code>[cost_correction]</code> shortcode, if selecting dates for new booking and not editing exist booking (7.0.1.28) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of blocking days selection in calendar, when visitor use the search form and manually input dates that lower than minimum number of days selection in settings (7.0.1.31) <em>(Business Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of showing blank page for printing in Chrome browser (7.0.1.34) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of not changing hash of booking after approving of booking,  if this option was activated at settings (7.0.1.35) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of rechecking booking dates (if activated "Checking to prevent double booking, during submitting booking" option), during booking editing (7.0.1.36) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of not correctly blocking check-out day (showing weird 2 checkout days), if activated "Unavailable time before / after booking" option and set unavailable DAYs after booking (7.0.1.38) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of wrong deleting booking,  if activated option "Disable bookings in different booking resources" during editing booking that  try  to  store in different booking resources (7.0.1.43) <em>(Business Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** position of currency symbol in calendar day cells and in mouseover tooltip,  depend from  settings at  Booking > Settings > Payment page  (7.0.1.49) <em>(Business Medium/Large, MultiUser)</em>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** replacinng shortcodes in a loop, if we are having several shortcodes with  bookingedit{cancel} in email templates (For example, if we have several  languges ). (7.0.1.52)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of infinite loop,  which  was exist  since update 7.0.1.52 to 7.0.1.57 (7.0.1.58)' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( ' **Fix** issue of not saving data for radio button selection field in emails and may be in booking listing (7.0.1.62) <em>(Personal, Business Small/Medium/Large, MultiUser)</em>' ) . '</li>'
	
. '</ul>'                                                                                          
                                      )                                 
                                ) 
                            );  
		
		
			?>
			<?php //$this->show_separator(); ?>
		</div>
			<?php  
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 7.0
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			?>
			<div class="clear" style="margin-top: 20px;"></div>
			<a id="wpbc_show_advanced_section_link_show" class="wpbc_expand_section_link" href="javascript:void(0)"
		       onclick="javascript:jQuery( '.version_update_7_0' ).toggle();"
			>+ Show changes in version update <span style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;">7.0</span></a>
		
			<div class="version_update_7_0" style="display:none;">				
			<h2 style='font-size: 2.1em;'>What's New in Booking Calendar 7.0</h2><?php 
            if (0) {
				?><h2 style="text-align:center;">The only thing that's new is everything... Almost:)</h2><?php 
			}
            //$this->show_separator();

            ?><h2 style='font-size: 1.6em;margin:40px 0 0 0;text-align: left;'>Changes in all versions</h2><?php
            
            $this->show_col_section( array( 
                                  
                                  array( 'h4'   => wpbc_recheck_strong_symbols( '**New** **Timeline at front-end** side.' ), 
                                         'text' =>    '<em>' . wpbc_recheck_strong_symbols( 'Show availability in fully new awesome way (old "Calendar Overview page from admin panel). Free version support showing booked dates with "blank pipelines". Paid versions have much more functionality here.') 
                                                    . '</em>'
                                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to show **Timeline at front-end** in **month format**. Shortcode: [bookingtimeline view_days_num=90 scroll_start_date="" scroll_day=-30]' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to show **Timeline at front-end** in **year format**. Shortcode: [bookingtimeline view_days_num=365 scroll_start_date=""  scroll_month=-3]' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to show **Timeline at front-end** in **day format**. Shortcode: [bookingtimeline view_days_num=30 scroll_start_date="" scroll_day=-15]' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ajax updating info during scrolling months, without page reloading.' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/free_timeline_2.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();
            
            $this->show_col_section( array( 
                                    array(  'img'  => '7.0/free_admin_calendar-overview.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                  , array( 'h4'   => wpbc_recheck_strong_symbols( '**Updated** **Timeline** (Calendar Overview page) in admin panel.' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Showing **popover** with booking details by **mouse click**, instead of mouse-over. Its help to show booking data at mobile devices.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Link in popover to Booking Listing page with  this booking. ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Approve or cancel exist booking from popover.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Even better looking on mobile devices.' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                
                                ) 
                            );  
    $this->show_separator();
    
            $this->show_col_section( array( 
                                array( 'h4'   => wpbc_recheck_strong_symbols( '**Updated** **Booking Listing** page.' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Updated Filters and Actions toolbars.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to select range of bookings, like in gMail (Shift + Click) by clicking on first checkbox and Shift+Click on last checkbox.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Showing new bookings with new icon.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Even better looking on mobile devices.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data.' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/free_admin_booking_listing.png', 'img_style'=>'margin-top:20px;width: 99%;' )                                 
                                ) 
                            );  
    $this->show_separator();
            
            $this->show_col_section( array( 
                                    array(  'img'  => '7.0/free_admin_add_booking.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                  , array( 'h4'   => wpbc_recheck_strong_symbols( '**Updated** **Add New Booking** page.' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** redesigned options toolbar.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** configuration number of month to show and width/height of calendar at Add New Booking page and saving this info. In advanced options toolbar section.' ) . '</li>'
                                                    . '</ul>'
                                                    . '<h4>' .wpbc_recheck_strong_symbols( 'Updated **General Settings** page. ' ) . '</h4>'
                                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to define position of Booking menu (top, middle, bottom section).' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data.' ) . '</li>'
                                                    . '</ul>'                                      
                                      ) 
                                
                                ) 
                            );  
    $this->show_separator();
            
            $this->show_col_section( array(                                    
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated **Form Fields** Settings page.' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** ability to **create unlimited number of booking form fields**.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Support** **Text** fields, **Textarea** fields, **Dropdown** lists, and (new) **Checkboxes** fields.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Arrange **order of form  fields** in booking form by **drag and drop** sorting.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Ability to edit exist form fields settings.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Ability to delete exist form fields.' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/free_admin_form-fields.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();
            
            $this->show_col_section( array(                                    
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated **Emails** Settings page.' ), 
                                         'text' =>   '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Configuration of sending emails in **text, html or multipart format**.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Selection **stylee of email templates** for HTML/multipart format.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Definition of **colors** for some email styles.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Configuration of **header and footer content** for emails.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Validation of saving email addresses in correct format,  and showing warnings otherwise. Its have to prevent of not sending emails issue in some cases.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** **Pending** email template - send email, if booking set as pending.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** **Trash** email template - send email, if booking has been declined - moved to trash.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** **Deleted** email template - send email, if booking has been deleted - completely erased.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** **Test sending email** button - for ability to  test  that emails are sending.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** **Shortcodes** for using in email templates.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data.' ) . '</li>'                                   
                                                    . '</ul>'                                      
                                      ) 
                                , array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated **Import** Settings page.' ), 
                                         'text' =>   '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data.' ) . '</li>'
                                                    . '</ul>'                                      
                                                    . '<h4>' .wpbc_recheck_strong_symbols( 'Under the Hood' ) . '</h4>'
                                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Booking Menu items in Top WordPress Admin Bar' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Full refactoring of source code.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Updated of BS version. ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Updated all **UI elements** - all buttons and UI elements looks even more sharp and nice. ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** New icons for UI  elements. Good looking on retina displays. Instead of images is using font icons.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Updated showing info and warning messages.  ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Improved pagination.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Under the Hood** Added many new hooks in source code.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Under the Hood** New URL (parameters) for booking menu pages.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Under the Hood** Updated CSS files.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Under the Hood** Updated JS files.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'And many other improvements...' ) . '</li>'
                                                    . '</ul>'                                      
                                      ) 

                                ) 
                            );  
   

    

    ?><h2 style='font-size: 1.6em;margin:40px 0 0 0;text-align: left;'><?php echo wpbc_recheck_strong_symbols( 'Changes in **Personal / Business Small / Business Medium / Business Large / MultiUser** versions' ); ?></h2><br/><?php
    
    $this->show_separator();

            $this->show_col_section( array(                                    
                                array( 'h4'   => wpbc_recheck_strong_symbols( '**New** **Timeline** at **front-end** side.' ), 
                                         'text' =>   '<em>' . wpbc_recheck_strong_symbols( 'Show availability in fully new awesome way (old "Calendar Overview page from admin panel). Free version support showing booked dates with "blank pipelines". Paid versions have much more functionality here. *(Personal, Business Small/Medium/Large, MultiUser)*') 
                                                    . '</em>'
                                                    .'<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( 'Show **Timeline** at **front-end** for **several** resources in **month format**. Shortcode: [bookingtimeline type="1,2,3,4" view_days_num=30 scroll_start_date="" scroll_month=0 header_title="All Bookings"] *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show **Timeline** at **front-end** for **several** resources in **2 months format**. Shortcode: [bookingtimeline type="4,2,1,3" view_days_num=60 scroll_start_date="" scroll_month=-1 header_title="All Bookings"] *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show **Timeline** at **front-end** for **several** resources in **week format**. Shortcode: [bookingtimeline type="3,4" view_days_num=7 scroll_start_date="" scroll_day=-7 header_title="All Bookings"] *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show **Timeline** at **front-end** for **several** resources in **day format**. Shortcode: [bookingtimeline type="3,4" view_days_num=1 scroll_start_date="" scroll_day=0 header_title="All Bookings"] *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show **Timeline** at **front-end** for **single** resource in **month format**. Shortcode: [bookingtimeline type="4" view_days_num=90 scroll_start_date="" scroll_day=-30] *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show **Timeline** at **front-end** for **single** resource in **year format**. Shortcode: [bookingtimeline type="4" view_days_num=365 scroll_start_date="" scroll_month=-3] *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Show **Timeline** at **front-end** for **single** resource in **day format**. Shortcode: [bookingtimeline type="4" view_days_num=30 scroll_start_date="" scroll_day=-15] *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to activate showing bookings detail in popover,  when  mouse click on specific booking "pipeline",  in the same way  as in admin panel. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to configure showing titles of booking,  like ID, Name or other fields,  in "pipeline of bookings". *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Support** responsive interface for showing on mobile devices. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/front-timeline2.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();

            $this->show_col_section( array( 
                                array( 'h4'   => wpbc_recheck_strong_symbols( '**Updated** **Timeline** (Calendar Overview page) in admin panel. *(Personal, Business Small/Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Show notes in booking popover at Timeline page.' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to print specific booking from Timeline page by  clicking on Print buttin  in popover. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Even more nice view at  mobile devices. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/admin-timeline.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();

            $this->show_col_section( array(                                    
                                array( 'h4'   => wpbc_recheck_strong_symbols( '**Updated** **Booking Listing** page. *(Personal, Business Small/Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Showing notes button with  different color,  if booking have some notes. For more easy checking.  *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Changing languages at Booking Listing page for specific action. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Updated Print modal window.  *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Showing currency relative to each  specific user settings in MultiUser version.  *(MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/admin-booking-listing.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();
    
            $this->show_col_section( array(                                     
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated **Resources** settings page. *(Personal, Business Small/Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to select range of booking resources, like in gMail (Shift + Click) by clicking on first checkbox and Shift+Click on last checkbox. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Sort of booking resources in resources table by different parameters (ID, Name, Priority, Users). By clicking on column header title. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Creating several  booking resources during one process. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to re-assign exist booking resource to other activated booking user  *(MultiUser)* ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Showing additional info near each  booking resources (like "Capacity" or booking resource "Single", "Child" type of resource). *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '*Shortcode* Ability to  use shortcode like: [bookingresource type=1 show="capacity" date="2016-09-13""] (fix:6.2.3.5.1) *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Ability to  hide children booking resources  *(Business Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/admin-resources.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();

            $this->show_col_section( array(                                    
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated "**Cost and rates**" settings page - **Rates** section *(Business Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to select several rates (like in gMail {Shift + Click}) by clicking on first checkbox and Shift+Click on last checkbox. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Direct  links to seasons for editing from each rate. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** When  logged in as super admin user ,  ability to show or hide seasons from all regular  users. *(MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Setting "Rates" to  several selected booking resources (by selecting bulk action option). *(Business Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/admin-rates.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();

            $this->show_col_section( array(                                   
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated "**Cost and rates**" settings page - **Valuation days** section *(Business Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** **Sorting** "Valuation days" by drag and drop specific cost row. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to select several costs (like in gMail {Shift + Click}) by clicking on first checkbox and Shift+Click on last checkbox. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** When  logged in as super admin user,  ability to show or hide seasons from all regular  users. *(MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Setting "Valuation days" to  several selected booking resources (by selecting bulk action option). *(Business Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/admin-valuation-days.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();

            $this->show_col_section( array(                                   
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated "**Cost and rates**" settings page - **Deposit** section *(Business Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** When  logged in as super admin user,  ability to show or hide seasons from all regular  users. *(MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Setting "Deposit" to  several selected booking resources (by selecting bulk action option). *(Business Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                      ) 
                                , array(  'img'  => '7.0/admin-deposit.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  

            $this->show_col_section( array(                                    
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated **Advanced cost** settings page  *(Business Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Support radio buttons for setting additional  cost. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Updated interface of configuration advanced cost - more clear selection type of additional cost in drop down lists. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Saving "Advanced costs" for each Custom  booking form separately. Its improve of searching issues during saving if some form  will  have wrong configuration. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'If having several fields with the same name in booking form (for example, if configured several languages),  showing specific field only once,  for correct saving additional cost. Please note,  in this case options in selectbox must be same withing any languages. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'Default value for new field,  right now 0 USD,  instead of previous 100%. For more easy  to  understand this logic. *(Business Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'                                                                        
                                    ) 
                                , array(  'img'  => '7.0/admin-advanced-cost.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                ) 
                            );  
    $this->show_separator();
                $this->show_col_section( array( 
                                array('h4'   => wpbc_recheck_strong_symbols( 'Updated **Discount Coupons** settings page  *(Business Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to select several coupons (like in gMail {Shift + Click}) by clicking on first checkbox and Shift+Click on last checkbox. *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to re-assign exist coupons filter to other activated booking user  *(MultiUser)* ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Sort of coupons by different fields. (By clicking on column header title). *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data. *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Editing fields of several coupons from one listing page,  like minimum cost, number of usage and expiration  date. *(Business Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                    )     
                                , array(  'img'  => '7.0/admin-coupons.png', 'img_style'=>'margin-top:20px;width: 99%;' )                                  
                                 
                                    ) 
                                );                                           
            
    $this->show_separator();

            $this->show_col_section( array(                                    
                                array( 'h4'   => wpbc_recheck_strong_symbols( 'Updated **Availability** settings page  *(Business Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to select several seasons (like in gMail {Shift + Click}) by clicking on first checkbox and Shift+Click on last checkbox. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Sort of availability by different fields. (By clicking on column header title). *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Direct  links to seasons for editing from each rate. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** When  logged in as super admin user ,  ability to show or hide seasons from all regular  users. *(MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Setting availability to  several selected booking resources (by selecting bulk action option). *(Business Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'                                                                        
                                    ) 
                                , array(  'img'  => '7.0/admin-availability.png', 'img_style'=>'margin-top:20px;width: 99%;' ) 
                                    ) 
                                ); 
    $this->show_separator();
                $this->show_col_section( array( 
                                array('h4'   => wpbc_recheck_strong_symbols( 'Updated **Season Filters** settings page  *(Business Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to select several seasons (like in gMail {Shift + Click}) by clicking on first checkbox and Shift+Click on last checkbox. *(Business Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to re-assign exist season filter to other activated booking user  *(MultiUser)* ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Sort of seasons by different fields. (By clicking on column header title). *(Business Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( 'New more clear interface of selecting dates. *(Business Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                    )     
                                , array(  'img'  => '7.0/admin-seasons.png', 'img_style'=>'margin-top:20px;width: 99%;' )                                  
                                 
                                    ) 
                                ); 
    $this->show_separator();
                $this->show_col_section( array( 
                                array('h4'   => wpbc_recheck_strong_symbols( 'Updated **General Settings** page. *(Personal, Business Small/Medium/Large, MultiUser)*' ), 
                                         'text' =>  '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Removed **Cost section** to Settings Payment page. *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Customization of booking title in timeline at front-end side for showing different info, like Name or Second Name of person who made the booking,  etc... *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to enable showing popover with booking details in timeline at front-end side, in the same way as its showing in admin panel at Calendar Overview (timeline) page . *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                    
                                    
                                    . '<h4>' .wpbc_recheck_strong_symbols( 'Updated **Fields** Settings page. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</h4>'
                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data. ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** shortcodes for showing hints in booking form: [resource_title_hint], [bookingresource show="id"], [bookingresource show="title"], [bookingresource show="cost"], [bookingresource show="capacity"], [bookingresource show="maxvisitors"] *(Business Medium/Large, MultiUser)*' ) . '</li>'
                                                    . '</ul>'
                                    
                                    . '<h4>' .wpbc_recheck_strong_symbols( 'Updated **Emails** Settings page. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</h4>'
                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data.' ) . '</li>'
                                                    . '</ul>'

                                    . '<h4>' .wpbc_recheck_strong_symbols( 'Updated **Import** Settings page. *(Personal, Business Small/Medium/Large, MultiUser)*' ) . '</h4>'
                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to  search specific booking resource by ID and Title' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data.' ) . '</li>'
                                                    . '</ul>'
                                    . '<h4>' .wpbc_recheck_strong_symbols( 'Updated **Search** Settings page. *(Business Large, MultiUser)*' ) . '</h4>'
                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data. ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Updated help sections with  shortcodes that  possible to  use in search  forms.' ) . '</li>'
                                    . '</ul>'

                                    )     
                                , array( 'h4'   => wpbc_recheck_strong_symbols( '**Updated** **Add New Booking** page. *(Personal, Business Small/Medium/Large, MultiUser)*' ), 
                                         'text' =>  
                                        '<h4>' .wpbc_recheck_strong_symbols( 'Updated **Payment Gateways** Settings page. *(Business Small/Medium/Large, MultiUser)*' ) . '</h4>'
                                      . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** payment **gateway files**. Important! If you was customized previously own payment gateway, in update 7.0 you need to customize your payment system  relative to  new payment gateway structure. In the same was as its done with  any  exist  payment system. For including loading o your payment gateway file,  you need to use this code and hook: <code>function add_my_gateway( $gateway ){ return $gateway . ",gateway_ID"; } add_filter( "wpbc_gateways_original_id_list", "add_my_gateway" );</code>' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Sorting payment **gateways order** by  drug and dropt specific payment gateways rows *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** showing active currency and status for each payment gateways *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** configuration  of **payment summary details**. Many new shortcodes for configuration payment summary info. *(Business Small/Medium/Large, MultiUser)*    ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** setting general currency for plugin interface *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** setting currency position  and format *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** configuration  of cost  per period at Settings > Payment page *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** configuration  of options: "Time impact to cost", "Advanced cost option" at Settings > Payment page *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** configuration  of billing form fields assignment at Settings > Payment page *(Business Small/Medium/Large, MultiUser)*' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data. ' ) . '</li>'
                                                    . '</ul>'
                                    . '<h4>' .wpbc_recheck_strong_symbols( 'Updated **Users** Settings page. *(MultiUser)*' ) . '</h4>'
                                    . '<ul style="list-style: disc outside;padding: 20px;margin:0;">'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to search specific user by ID and Title' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**New** Ability to sort users by ID, Name and Role' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Showing additional info near each  user,  like status and role. ' ) . '</li>'
. '<li>' . wpbc_recheck_strong_symbols( '**Improvement** Advanced checking during saving data. ' ) . '</li>'
                                    . '</ul>'
                                    
                                    
                                    )
                                 
                                    ) 
                                );                                           
                
    $this->show_separator();
	?></div><?php
	
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// Footer
			////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			?>
            <table class="about-text" style="margin-bottom:30px;height:auto;font-size:1em;width:100%;" >
                <tr>
                    <td>

                    </td>
                    <td style="width:10%">
                        <a  href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-getting-started' ), 'index.php' ) ) ); ?>"
                            style="float: right; height: 36px; line-height: 34px;" 
                            class="button-primary"
                            >&nbsp;<strong>Get Started</strong> <span style="font-size: 20px;line-height: 18px;padding-left: 5px;">&rsaquo;&rsaquo;&rsaquo;</span>
                        </a>
                    </td>
                </tr>
            </table>
  
        </div><?php
    }


    public function content_getted_started() {
        
        $this->css();
        
        list( $display_version ) = explode( '-', WPDEV_BK_VERSION );
        ?>
            <div class="wrap about-wrap wpbc-welcome-page">

                <?php $this->title_section(); ?>

                <table class="about-text" style="margin-bottom:30px;height:auto;font-size:1em;width:100%;" >
                    <tr>
                        <td>
                            <a  href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about' ), 'index.php' ) ) ); ?>"
                                style="float: left; height: 36px; line-height: 34px;" 
                                class="button-primary"
                                >&nbsp;<span style="font-size: 20px;line-height: 18px;padding-right: 5px;">&lsaquo;&lsaquo;&lsaquo;</span> <strong>What's New</strong> 
                            </a>
                        </td>
                        <td style="width:50%">
                            <a  href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about-premium' ), 'index.php' ) ) ); ?>"
                                style="float: right; height: 36px; line-height: 34px;" 
                                class="button-primary"
                                >&nbsp;<strong>Premium Features</strong> <span style="font-size: 20px;line-height: 18px;padding-left: 5px;">&rsaquo;&rsaquo;&rsaquo;</span>
                            </a>
                        </td>
                    </tr>
                </table>

                <h2 style='font-size: 2.1em;'>Get Started</h2>
                <?php

?><div style="text-align: center;"><iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list=PLabuVtqCh9dwLA5cpz1p2RrZOitLuVupR&amp;start=28&amp;rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div><?php

                //$this->show_separator();

                $this->show_col_section( array( 
                                              array( 'h4'   => sprintf( 'Add booking form to your post or page' ),
                                                     'text' => '<ul style="margin:0px;">' 
                                                     . '<li>' . sprintf( 'Open exist or add new %spost%s or %spage%s' 
                                                                        ,  '<a href="' . admin_url( 'edit.php' ) . '">', '</a>'
                                                                        ,  '<a href="' . admin_url( 'edit.php?post_type=page' ) . '">', '</a>' ) . '</li>'
                                                     . '<li>' . sprintf( ' Click on Booking Calendar icon *(button with calendar icon at toolbar)*' ) . '</li>'
                                                     . '<li>' . sprintf( ' In popup dialog select your options, and insert shortcode' ) . '</li>'
                                                     . '<li>' . sprintf( ' Publish or update page' ) . '</li>'
                                                     . '<li>' . sprintf( ' Now your visitors can see and make bookings at the booking form' ) . '</li>'
                                                            . '</ul>'
                                                    )
                                            , array(  'img'  => 'get-started/booking-calendar-insert-form.png', 'img_style'=>'margin: 20px;width:75%;float:right;' ) 
                                           ) 
                                        );  
                $this->show_col_section( array( 

                                              array(
                                                    'text' => 
                                                             '<p class="">' 
                                                             . sprintf( 'Or add Booking Calendar %s**widget**%s to your sidebar.', '<a href="' . admin_url( 'widgets.php' ) . '">', '</a>' ) 
                                                             . '</p>'
                                                             . '<p>' . sprintf( 'If you need to add shortcode manually, you can read how to add it %shere%s.', 
                                                                                '<a href="https://wpbookingcalendar.com/faq/booking-calendar-shortcodes/">', '</a>')
                                                             . '</p>'
                                                             . '<p>' . sprintf( '* **Note.** You can add new booking(s) also from the admin panel (Booking > Add booking page).*' )
                                                             . '</p>'                                                  
                                                    )
                                            , array(  'img'  => 'get-started/booking-calendar-add-widget.png', 'img_style'=>'margin:0 20px;width:75%;float:right;' )

                                           ) );

                ?>
                <div class="feature-section two-col"> 
                    <div class="col col-1 last-feature"  style="margin-top: 0px;width:59%">                    
                        <h4><?php printf( 'Check and manage your bookings' ); ?></h4>
                        <p><?php echo wpbc_recheck_strong_symbols( 'After email notification about new booking(s), you can check and **approve** or **decline** your **booking(s)** in **responsive**, modern and **easy to use Booking Admin Panel**.'); ?></p>                

                    </div>
                </div>
                <img src="<?php echo $this->asset_path; ?>get-started/booking-listing_350.png" style="float:left;border:none;box-shadow: 0 1px 3px #777777;margin:1% 2%;width:72.3%;" />
                <img src="<?php echo $this->asset_path; ?>get-started/booking-listing-mobile_350.png" style="float:left;border:none;box-shadow: 0 1px 3px #777777;margin: 1% 1% 1% 0;width:19.1%;" />
                <div class="clear"></div>

                <p style="text-align:center;"><?php echo wpbc_recheck_strong_symbols( 'or get clear view to **all your bookings in** stylish **Calendar Overview** mode, that looks great on any device'); ?></p>                
                <img src="<?php echo $this->asset_path; ?>get-started/booking-calendar-overview.png" style="border:none;box-shadow: 0 1px 3px #777777;margin: 2%;width:94%;display:block;" />
                <div class="clear"></div>


                <h2 style='font-size: 2.1em;margin-top:50px;'><?php printf( 'Next Steps' ); ?></h2>
                <?php 

                $this->show_separator();

                $this->show_col_section( array( 
                                              array( 'h4'   => sprintf( 'Configure different settings' ),
                                                    'text' =>  '<ul style="margin:0px;">' 

    . '<li>' . sprintf( 'Select your calendar skin, for natively fit to your website design.' ) . '</li>'
    . '<li>' . sprintf( 'Configure number of month(s) in calendar.' ) . '</li>'
    . '<li>' . sprintf( 'Set single or multiple days selection mode.' ) . '</li>'
    . '<li>' . sprintf( 'Set specific weekday(s) as unavailable.' ) . '</li>'
    . '<li>' . sprintf( 'Customize calendar legend.' ) . '</li>'
    . '<li>' . sprintf( 'Enable CAPTCHA.' ) . '</li>'
    . '<li>' . sprintf( 'Set redirection to the "Thank you" page, after the booking process.' ) . '</li>'
    . '<li>' . sprintf( 'Configure different settings for your booking admin panel.' ) . '</li>'
    . '<li>' . sprintf( 'And much more ...' ) . '</li>'

                                                             . '</ul>'
                                                    )
                                            , array(  'img'  => 'get-started/settings-general.png', 'img_style'=>'margin: 20px;width:75%;float:right;' ) 
                                           ) 
                                        );  

                ?><div clas="clear"></div><?php

                $this->show_col_section( array( 
                                              array( 'h4'   => sprintf( 'Customize booking form fields and email templates' ),
                                                    'text' =>  '<ul style="margin:0px;">' 

    . '<li>' . sprintf( 'Activate or deactivate specific form fields in your booking form.' ) . '</li>'
    . '<li>' . sprintf( 'Configure labels in your booking form near form fields.' ) . '</li>'
    . '<li>' . sprintf( 'Set specific form fields as required.' ) . '</li>'
    . '<li style="margin-top:30px;">' . sprintf( 'Activate or deactivate specific email(s).' ) . '</li>'
    . '<li>' . sprintf( 'Customize your email templates.' ) . '</li>'
    . '<li style="margin-top:30px;">' . sprintf( 'Or even activate and configure <strong>import</strong> of <strong>Google Calendar Events</strong>.' ) . '</li>'                                                  
    . '<li style="margin-top:30px;">' . sprintf( 'And much more ...' ) . '</li>'

                                                             . '</ul>'
//                                                  . '<h4>' . sprintf( 'Or even activate importing events from Google Calendar' ) . '</h4>'

                                                    )
                                            , array(  'img'  => 'get-started/settings-fields.png', 'img_style'=>'margin: 20px;width:75%;float:right;' ) 
                                           ) 
                                        );  

                ?>                

                <h2 style='font-size: 2.1em;;margin-top:20px;'><?php printf( 'Have a questions?' ); ?></h2>
                <?php 

                $this->show_separator();

                $this->show_col_section( array( 
                                              array( 
                                                     'text' => '<span>' . sprintf( 'Check out our %sHelp%s', '<a href="https://wpbookingcalendar.com/help/" target="_blank" >', '</a>' ) . '</span>'
                                                             . '<p>' . sprintf( 'See %sFAQ%s', '<a href="https://wpbookingcalendar.com/faq/" target="_blank">', '</a>' ) . '</p>'
                                                   ) 
                                            , array( 
                                                     'text' => '<strong>' . sprintf( 'Still having questions?' ) . '</strong>'
                                                             . '<p>' . sprintf( 'Check our %sForum%s or contact %sSupport%s', '<a href="https://wpbookingcalendar.com/support/" target="_blank">', '</a>', '<a href="https://wpbookingcalendar.com/contact/" target="_blank">', '</a>' ) . '</p>'
                                                   ) 
                                            , array( 
                                                     'text' => '<strong>' . sprintf( 'Need even more functionality?' ) . '</strong>'
                                                             . '<p>' . sprintf( ' Check %shigher versions%s of Booking Calendar', '<a href="https://wpbookingcalendar.com/overview/" target="_blank">', '</a>' ) . '</p>'

                                                   ) 

                                            ) 
                                        );  

                ?>                                                                   
                <table class="about-text" style="margin-bottom:30px;height:auto;font-size:1em;width:100%;" >
                    <tr>
                        <td>
                            <a  href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about' ), 'index.php' ) ) ); ?>"
                                style="float: left; height: 36px; line-height: 34px;" 
                                class="button-primary"
                                >&nbsp;<span style="font-size: 20px;line-height: 18px;padding-right: 5px;">&lsaquo;&lsaquo;&lsaquo;</span> <strong>What's New</strong> 
                            </a>
                        </td>
                        <td style="width:50%">
                            <a  href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about-premium' ), 'index.php' ) ) ); ?>"
                                style="float: right; height: 36px; line-height: 34px;" 
                                class="button-primary"
                                >&nbsp;<strong>Premium Features</strong> <span style="font-size: 20px;line-height: 18px;padding-left: 5px;">&rsaquo;&rsaquo;&rsaquo;</span>
                            </a>
                        </td>
                    </tr>
                </table>

            </div>
        <?php
    }

    
    public function content_premium() {
        
        $this->css();
        
        list( $display_version ) = explode( '-', WPDEV_BK_VERSION );
        
        // $upgrade_link = esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about-premium' ), 'index.php' ) ) );
        
        ?>
        <div class="wrap about-wrap wpbc-welcome-page">

                <?php $this->title_section(); ?>

                <table class="about-text" style="margin-bottom:30px;height:auto;font-size:1em;width:100%;" >
                    <tr>
                        <td>
                            <a  href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-getting-started' ), 'index.php' ) ) ); ?>"
                                style="float: left; height: 36px; line-height: 34px;" 
                                class="button-primary"
                                >&nbsp;<span style="font-size: 20px;line-height: 18px;padding-right: 5px;">&lsaquo;&lsaquo;&lsaquo;</span> <strong>Get Started</strong> 
                            </a>
                        </td>
                        <td style="width:50%">                            
                            <a class="button button-primary" style="font-weight: 600;float: right; height: 36px; line-height: 34px;"  href="<?php echo wpbc_up_link(); ?>" target="_blank">&nbsp;<?php if ( wpbc_get_ver_sufix() == '' ) { _e('Purchase' ,'booking'); } else { _e('Upgrade Now' ,'booking'); } ?>&nbsp;&nbsp;</a>
                        </td>
                    </tr>
                </table>                        
            <?php
            
            echo '<div style="color: #999;font-size: 24px;margin-top: 0px;text-align: center;width: 100%;">';
                echo 'Get even more functionality with premium versions...';
            echo '</div>';


            
 //           echo '<div class="clear" style="height:30px;"></div>';
?><div style="text-align: center;margin:20px 0;"><iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list=PLabuVtqCh9dyc_EO8L_1FKJyLpBuIv21_&amp;rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div><?php
            
            $this->show_header('Booking Calendar Personal version'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 0;" href="http://personal.wpbookingcalendar.com/admin-panel/" target="_blank"' 
                                    . '> **Live Demo** *Admin Panel*</a>'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 10px 6px 0;" href="http://personal.wpbookingcalendar.com/" target="_blank"'
                                    . '> **Live Demo** *Front End*</a><div class="clear"></div>'
                              , 'h2', 'font-size: 2.1em;  background:#e5e5e5;color: #777;line-height: 1.5em;padding:5px 15px;' );

            
            echo $this->get_img( 'premium/admin-panel-calendar-overvew4.png', 'margin:15px auto; width: 98%;' );

			$this->show_separator();

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Unlimited number of **Booking Resources**',
                                                 'text' => 
'<p>Booking resources - it\'s your **services** or **properties** *(like houses, cars, tables, etc...)*, that can be booked by visitors of your website.</p>'
. '<p>Each booking resource have own unique calendar *(with  booking form)*, which  will **prevent of double bookings** for the same date(s).</p>'
. '<p>It\'s means that you can **receive bookings and show unavailable, booked dates** in different calendars **for different booking resources** *(services or properties)*.</p>'
                                
.  $this->get_img( 'premium/2-booking-forms.png', 'margin:0 0 10px 0; width: 97%;' )
                                              
.'<p>You can add/delete/modify your booking resources at the Booking > Resource page. 
You can define the calendar *(booking form)* to the specific booking resources, 
at the popup configuration dialog, during inserting booking shortcode into post or page.</p>'

                                                ) 
                                        , array(  'img'  => 'premium/booking-resources.png', 'img_style'=>'margin-top:40px;width:95%;' ) 
                                        ) 
                                    );  
            
            
            
            $this->show_col_section( array( 
                                          
                                          array( 'h4'   => 'Configure Booking Form and Email Templates',
                                                 'text' =>
  '**Booking Form**<br />'
. '<p>Configure any format and view of your booking form *(for example two columns view,  with calendar in left column and form fields at right side, etc...)*</p>'
. '<p>Add **any number of new form fields** *(text fields, drop down lists, radio-buttons, check-boxes or textarea elements, etc...)*</p>'
. '<br />**Email Templates**<br />'
. '<p>You can activate and configure email templates for the different booking actions with shortcodes for any exist form fields and some other system shortcodes *(like inserting address of the page (or user IP), where user made this action)*.</p>' 
                                                ) 
                                        , array(  'img'  => 'premium/booking-form-fields.png', 'img_style'=>'margin-top:20px;width:95%;' ) 
                                        ) 
                                    );  

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Manage Bookings',
                                                 'text' => 
//'You can edit the exist bookings, add notes to the bookings, print and export bookings to the CSV format, etc...' 
'<ul>
    <li style="margin-left: 1em;">**Edit** your bookings by changing details or dates of specific booking</li>
    <li style="margin-left: 1em;">**Duplicate** booking in other booking resource</li>
    <li style="margin-left: 1em;">**Change resource** for exist booking</li>
    <li style="margin-left: 1em;">Add **notes** to bookings for do not forget important info</li>
    <li style="margin-left: 1em;">**Print** booking listings</li>
    <li style="margin-left: 1em;">**Export** bookings to the **CSV format**</li>
    <li style="margin-left: 1em;">**Import** bookings from **Google Calendar**</li>
    <li style="margin-left: 1em;">**Sync bookings via .ics feeds**, as well</li>
    <li style="margin-left: 1em;">And much more...</li>
</ul>'                                               
                                              ) 
                                        , array(  'img'  => 'premium/booking-actions-buttons.png', 'img_style'=>'margin-top:20px;width:95%;' ) 
                                        ) 
                                    );  
            
            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.95em;font-style:italic;text-align:right;margin:5px 0 10px;">Check many other nice features in  Booking Calendar Personal version at <a target="_blank" href="https://wpbookingcalendar.com/overview/">features list</a> and test <a target="_blank" href="https://wpbookingcalendar.com/demo/">live demo</a>.</div>' );
            
            ?><div class="clear" style="height:30px;"></div><?php
            
            $this->show_header('Booking Calendar Business Small version'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 0;" href="http://bs.wpbookingcalendar.com/admin-panel/" target="_blank"' 
                                    . '> **Live Demo** *Admin Panel*</a>'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 10px 6px 0;" href="http://bs.wpbookingcalendar.com/" target="_blank"'
                                    . '> **Live Demo** *Front End*</a><div class="clear"></div>'
                              , 'h2', 'font-size: 2.1em;  background:#e5e5e5;color: #777;line-height: 1.5em;padding:5px 15px;' );

            
            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.85em;font-style: italic;margin: 5px 0 0 10px;">**Note!** This version support **all functionality** from the Booking Calendar **Personal** version.</div>' );
            
            
            $this->show_col_section( array( 
                                          array( 'h4'   => 'Advanced Hourly Bookings',
                                                 'text' => 
  '<p>Add ability to make bookings for specific **times** *(in addition to timeslots bookings, the bookings for specific start time and time duration or start time and end time, as well)*.</p>'
. '<p>Configure selections or entering any times interval *(several hours or few minutes)* at the Booking > Settings > Fields page:'
.'<ul>
    <li style="margin-left: 1em;">Start time and end **time entering** in *"time text fields"*</li>
    <li style="margin-left: 1em;">Selections **start time and end time**</li>
    <li style="margin-left: 1em;">Selections **start time and duration** of time</li>
    <li style="margin-left: 1em;">Selections specific time in **timeslot** list</li>
</ul></p>'
                                              
.'<p>**Please note**, if you will make the booking for specific timeslot, this timeslot become unavailable for the other visitors for this selected date.</p>'

.'<p>You can even activate booking of same timeslot in the several selected dates during the same booking session.</p>'
                                              ) 
                                        , array(  'img'  => 'premium/time-slots-booking.png', 'img_style'=>'margin:20px 25% auto;width:50%;' ) 
                                        ) 
                                    );         
            
            $this->show_col_section( array( 
                                          array( 'h4'   => 'Online Payments',
                                                 'text' => 
    '<p>' . 'You can set cost per specific booking resource and activate online payments' . '</p>'                                              
  . '<p>' . 'Suport Payment Gateways:'
  .'<ul>
      <li style="margin-left: 1em;">**Stripe**</li>
      <li style="margin-left: 1em;">PayPal Standard</li>
      <li style="margin-left: 1em;">PayPal Pro Hosted Solution *(note, its doesn\'t PayPal Pro)*</li>
      <li style="margin-left: 1em;">Authorize.Net *(Server Integration Method (SIM))*</li>
      <li style="margin-left: 1em;">Sage Pay</li>
      <li style="margin-left: 1em;">iDEAL via Sisow</li>
      <li style="margin-left: 1em;">iPay88</li>
      <li style="margin-left: 1em;">Direct/wire bank transfer</li>
      <li style="margin-left: 1em;">Cash payments</li>
  </ul></p>'
  .'<p>' . 'You can activate and configure these gateways at Booking > Settings > Payment page.' . '</p>'
  .'<p>' . '*You can even send payment request by email for specific booking*.' . '</p>'
                                               )
                                         , array(  'img'  => 'premium/payment-buttons1.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              

            
            
            $this->show_col_section( array( 
                                          array( 'h4'   => 'Change over days',
                                                 'text' => 
    '<p>' . 'You can use the **same date** as **"check in/out"** for **different bookings**.' . '</p>'

  . '<p>' . 'These **half booked days** will mark  by vertical line *(as in <a href="http://bm.wpbookingcalendar.com/" targe="_blank">this live demo</a>)*.' . '</p>'

  . '<p>' . 'It\'s means that  your visitors can start  new booking on the same date,  where some old bookings was ending.' . '</p>'

  . '<p>' . 'To activate this feature you need select *range days selection* or *multiple days selections* mode on the *General Booking Settings* page in calendar  section.'
          . ' After  this you can activate the *"Use check in/out time"* option  and configure the check in/out times. For example, check in time as 14:00 and check out time as 12:00.' . '</p>'

  . '<p>' . '**Tip**. You can also activate to show change-over days as triangles (diagonal lines), instead of showing them via vertical lines.' . '</p>'
                                               )
                                        , array(  'img'  => 'premium/change-over-days2.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                    
                                    );              

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Range days selection',
                                                 'text' =>  
  '<p>' . 'Activate **several days selection with 1 or 2 mouse clicks** *(by selecting check in and check out dates, all middle days will be selected automatically)*.' . '</p>'
. '<p>' . 'Its means that you can set only **week(s) selections** or any other number of days selections.' . '</p>'
. '<p>' . 'Configure **specific number of days** selections for *range days selection with one mouse click*. ' 
        . 'Or set **minimum and maximum number of days** selections (or even several  specific number of days) for *range days selection with two mouse clicks*.' . '</p>'
. '<p>' . 'In addition you can **set start day(s)** selections for only **specific week days**.' . '</p>'
                                               )
                                        , array(  'img'  => 'premium/range-days-settings.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              

            

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Auto Cancellation  / Auto Approval',
                                                 'text' => 
  '<p>' . 'You can activate **auto cancellation of all pending booking(s)**, which have no successfully paid status, after specific amount of time, when booking(s) was making.' . '</p>'
. '<p>' . 'This feature will set dates again available for new booking(s) to other visitors.' . '</p>'
. '<p>' . 'You can even activate sending of emails to the visitors, during such cancelation.' . '</p>'
. '<p>' . 'Or you can activate **auto approval of all incoming bookings**.' . '</p>'
                                              )
                                        , array(  'img'  => 'premium/auto-cancelation-settings.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              

            
                         
            
            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.95em;font-style:italic;text-align:right;margin:5px 0 10px;">Check many other nice features in Booking Calendar Business Small version at <a target="_blank" href="https://wpbookingcalendar.com/overview/">features list</a> and test <a target="_blank" href="https://wpbookingcalendar.com/demo/">live demo</a>.</div>' );
            
            ?><div class="clear" style="height:30px;"></div><?php
            
            $this->show_header('Booking Calendar Business Medium version'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 0;" href="http://bm.wpbookingcalendar.com/admin-panel/" target="_blank"' 
                                    . '> **Live Demo** *Admin Panel*</a>'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 10px 6px 0;" href="http://bm.wpbookingcalendar.com/" target="_blank"'
                                    . '> **Live Demo** *Front End*</a><div class="clear"></div>'
                              , 'h2', 'font-size: 2.1em;  background:#e5e5e5;color: #777;line-height: 1.5em;padding:5px 15px;' );

            
            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.85em;font-style: italic;margin: 5px 0 0 10px;">**Note!** This version support **all functionality** from the Booking Calendar **Business Small** version.</div>' );

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Season Availability',
                                                 'text' => 
  '<p>' . 'You can set as **unavailable days** in your booking resources **for specific seasons**.' . '</p>'
. '<p>' . 'Its useful, when you need to **block days for holidays** or any other seasons during a year.' . '</p>'
. '<p>' . 'You can set days as conditional seasons filters *(for example, only weekends during summer)* or simply select range of days for specific seasons.' . '</p>'
. '<p>' . 'Note, instead of definition days as unavailable, you can set all days unavailable and only days from specific season filer as available.' . '</p>'
. '<p>' . '* **Configuration.** You can create season filters at the Booking > Resources > Filters page and then at the Booking > Resources > **Availability** page set days from  specific season as unavailable for the specific booking resources.*' . '</p>'
                                              )
                                        , array(  'img'  => 'premium/season-filters.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              
            $this->show_col_section( array(
                                          array( 'h4'   => 'Set available days interval depending from today date',
                                                 'text' =>
  '<p>' . '**Limit available days from today** - defining specific number of available days, that start from today. All other future days will be unavailable. Also in any versions of Booking Calendar possible to define **Unavailable days from today** - defining specific number of unavailable days in calendar start from today. Its means that with these 2 options you can set interval of available days, that depending from today date.' . '</p>'
                                              )
                                        , array( 'h4'   => 'Set unavailable minutes/hours/days before or after booking date/time',
                                                 'text' =>
   '<p>' . 'This option is useful, if you need to define some unavailable time/days for cleaning or any other srvices before or after booking.' . '</p>'
.  '<p>' . 'Important! This feature is applying only for bookings for specific timeslots, or if activated change-over days feature. Its does not work for full booked days.' . '</p>'
                                              )
                                        )
                                    );


            $this->show_col_section( array(
                                          array( 'h4'   => 'Set Rates for different Seasons',
                                                 'text' => 
 '<p>' . 'Set different **daily cost (rates) for** different **seasons**.' . '</p>'
. '<p>' . '*For example, you can have higher cost for the "High Season" or at weekends.*' . '</p>'
. '<p>' . 'You can set rates as **fixed cost per day** (night) **or as percent** from original cost of booking resource.' . '</p>'
. '<p>' . '* **Configuration.** You can set rates for your booking resources at Booking > Resources > **Cost and rates** page by clicking on **Rate** button.*' . '</p>'
                                              )
                                        , array(  'img'  => 'premium/season-rates.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              
            $this->show_col_section( array( 
                                          array( 'h4'   => 'Cost depends from number of selected days',
                                                 'text' => 
  '<p>' . 'You can configure **different cost** for different **number of selected days**.' . '</p>'
. '<p>' . '*For example, cost of second selected week, can be lower then cost of first week.*' . '</p>'
. '<p>' . 'You can set **cost per day(s)** or **percentage** from the original cost:' 
  .'<ul>
      <li style="margin-left: 2em;">**For** specific selected day number</li>
      <li style="margin-left: 2em;">**From** one day number **to** other selected day number</li>
  </ul>'                                                                                             
. 'or you can set the **total cost** of booking for **all days**:'
  .'<ul>
      <li style="margin-left: 2em;">If selected, exactly specific number of days *(term "**Together**")*</li>      
  </ul></p>'
. '<p>' . 'In addition, you can even set applying this cost only, if the "Check In" day in specific season filter.' . '</p>'
. '<p>' . '* **Configuration.** You can set rates for your booking resources at Booking > Resources > **Cost and rates** page by clicking on "**Valuation days**" button.*' . '</p>'
                                              )
                                        , array(  'img'  => 'premium/valuation-days.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              

            $this->show_col_section( array(
                                          array( 'h4'   => 'Early booking / Last minute booking discounts',
                                                 'text' =>
  '<p>' . '**Last minute booking discounts.**' . '</p>'
. '<p>' . 'Set discount, if difference between "today" and "check in" day **LESS** than N days.' . '</p>'
. '<p>' . '**Early booking discount.**' . '</p>'
. '<p>' . 'Set discount, if difference between "today" and "check in" day **MORE** than N days.' . '</p>'
. '<p>' . 'You can set discounts as fixed cost or as percent from original cost of booking resource.' . '</p>'
. '<p>' . '* **Configuration.** You can set these discounts for your booking resources at Booking > Resources > **Cost and rates** page by clicking on **Early / Late** button.*' . '</p>'
                                              )
                                        , array(  'img'  => 'https://wpbookingcalendar.com/wp-content/uploads/2018/06/booking-calendar-early-booking-last-minute-discounts.png', 'img_style'=>'margin:20px 0;width:99%;' )
                                        )
                                    );
            $this->show_col_section( array(
                                          array( 'h4'   => 'Cost depends from selection options in booking form',
                                                 'text' => 
  '<p>' . 'You can set additional costs, like tax or some other additional charges *(cleaning, breakfast,  etc...)*, or just increase the cost of booking depends from the visitor number selection in your booking form.' . '</p>'
. '<p>' . 'Its means that you can set additional cost for any selected option(s) in select-boxes or checkboxes at your booking form.' . '</p>'
. '<p>' . 'You can set fixed cost or percentage from the total booking cost or additional cost per each selected day or night.' . '</p>'
. '<p>' . '* **Configuration.** Firstly you need to configure options selection in select-boxes or checkboxes in your booking form at Booking > Settings > Fields page, then you be able to configure additional cost for each such option at the Booking > Resources > **Advanced cost** page .*' . '</p>'
. '<p>' . '* **Tip & Trick.** ' .  'You can **show cost hints** separately for the each items, that have additional cost *at Booking > Resources > Advanced cost page*. 
                                    <br>For example, if you have configured additional cost for **my_tax** option at **Advanced cost page**, 
                                    then in booking form you can use this shortcode <code>[my_tax_hint]</code> to show additional cost of this specific option. 
                                    <br>Add **"_hint"** term to name of shortcode for creation hint shortcode. *'
          .'</p>'                                              
                                              )
                                        , array(  'img'  => 'premium/advanced-cost.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              
            $this->show_col_section( array( 
                                          array( 'h4'   => 'Deposit payments',
                                                 'text' => 
  '<p>' . 'You can activate ability to **pay deposit (part of the booking cost)**, after visitor made the booking. ' . '</p>'
. '<p>' . 'It\'s possible to set fixed deposit value or percent from the original cost for the specific booking resource.' . '</p>'
. '<p>' . 'You can even activate to show deposit payment form, only when  the difference between *"today"* and *"check in"* days more than specific number of days. Or if *"check in"* day inside of specific season.' . '</p>'
. '<p>' . '* **Configuration.** You can activate and configure **deposit** value for specific booking resources at the Booking > Resources > **Cost and rates** page by clicking on "**Deposit amount**" button.*' . '</p>'
                                              )
                                        , array(  'img'  => 'premium/deposit-settings.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Multiple Custom Booking Forms',
                                                 'text' => 
  '<p>' . 'You can create **several custom forms** configurations.' . '</p>'
. '<p>' . 'Its means that you can have the different booking forms *(which have the different form fields)* for different booking resources.' . '</p>'
. '<p>' . 'You can also set specific custom form  as **default booking form to** each  of your **booking resources** at Booking > Resources page.' . '</p>'
. '<p>' . '* **Configuration.** You can create several custom booking forms at the Booking > Settings > **Fields** page by clicking on **"Add new Custom Form"** button.*' . '</p>'                                                                                           
                                              )
                                        , array(  'img'  => 'https://wpbookingcalendar.com/wp-content/uploads/2018/01/custom-booking-forms.png', 'img_style'=>'margin:20px 0;width:99%;' )
                                        ) 
                                    );              
                                              

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Advanced days selection',
                                                 'text' => 
  '<p>' . 'Specify that on **specific week days** (or during certain seasons), the specific minimum (or fixed) **number of days** must be booked.' 
        . '<br/>*For example: visitor can select only 3 days starting at Friday and Saturday, 4 days – Friday, 5 days – Monday, 7 days – Saturday, etc...*' . '</p>'
                                              
. '<p>' . 'Also, you can define **specific week day(s) as start day** in calendar selection for the **specific season**.' 
        . '<br/>*For example, in "High Season", you can allow start day selection only at Friday in the "Low Season" to start day selection from any weekday.*' . '</p>'

. '<p>' . '*Read more about this configuration <a href="https://wpbookingcalendar.com/faq/booking-calendar-shortcodes/" targe="_blank">here</a> (at **options** parameter section).*' . '</p>'
                                                                                            
                                              )
                                        
                                         , array( 'h4'   => 'Different time slots for different days',
                                                 'text' => 
  '<p>' . 'This feature provide ability to use the **different time slots selections** in the booking form **for different selected week days or seasons**.' . '</p>' 
. '<p>' . 'Each week day (day of specific season filter) can have different time slots list.' . '</p>'
                                              
. '<p>' . 'You can check more info about this configuration at <a href="https://wpbookingcalendar.com/faq/different-time-slots-selections-for-different-days/" targe="_blank">this page</a>.' . '</p>'
. '<p>' . '**Note.** In the same way you can configure showing any different form fields, not only  timeslots.' . '</p>'                                             
                                              )
                                        ) 
                                    );              

            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.95em;font-style:italic;text-align:right;margin:5px 0 10px;">Check many other nice features in Booking Calendar Business Medium version at <a target="_blank" href="https://wpbookingcalendar.com/overview/">features list</a> and test <a target="_blank" href="https://wpbookingcalendar.com/demo/">live demo</a>.</div>' );
            
            ?><div class="clear" style="height:30px;"></div><?php
            
            $this->show_header('Booking Calendar Business Large version'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 0;" href="http://bl.wpbookingcalendar.com/admin-panel/" target="_blank"' 
                                    . '> **Live Demo** *Admin Panel*</a>'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 10px 6px 0;" href="http://bl.wpbookingcalendar.com/" target="_blank"'
                                    . '> **Live Demo** *Front End*</a><div class="clear"></div>'
                              , 'h2', 'font-size: 2.1em;  background:#e5e5e5;color: #777;line-height: 1.5em;padding:5px 15px;' );

            
            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.85em;font-style: italic;margin: 5px 0 0 10px;">**Note!** This version support **all functionality** from the Booking Calendar **Business Medium** version.</div>' );

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Capacity and Availability',
                                                 'text' => 
 '<p>' . 'You can receive **several specific number of bookings per same days**. ' . '</p>'
.'<p>' . 'Define **capacity** for your **booking resource(s)**, 
          and then **dates** in calendar will be **available until number of bookings less than capacity** of the booking resource.' . '</p>'

.'<p>' . '**Note!** Its possible to make reservation only for **entire date(s)**, not a time slots  
   *(data about time slots for booking resources with capacity higher than one, will be record into your DB, but do not apply to availability)*.' . '</p>'
. '<p>' . '* **Configuration.** Set capacity of booking resources at Booking > **Resources** page. You can read more info about configurations of booking resources, capacity and availability  at  <a href="https://wpbookingcalendar.com/faq/booking-resource/" target="_blank">this page</a>.*' . '</p>'
                                              )
                                        , array(  'img'  => 'premium/capacity3.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Search Availability',
                                                 'text' =>
 '<p>' . 'Your visitors can even **search available booking resources** (properties or services) **for specific dates** *(like in this <a href="http://bl.wpbookingcalendar.com/search/" target="_blank">live demo</a>)*.' . '</p>'
.'<p>' . 'Beside standard parameters: **check in** and **check out** dates, number of **visitors**, you can define **additional parameters** for your search form *(for example, searching property  with  specific amenities)*.
    <br />You can read more about this configurations at <a href="https://wpbookingcalendar.com/faq/selecting-tags-in-search-form/" target="_blank">FAQ</a>.' . '</p>'
.'<p>' . '**Note!** Plugin  will search only among pages with booking forms for *<a href="https://wpbookingcalendar.com/faq/booking-resource/" target="_blank">single or parent</a>* booking resources. You need to insert one booking form per page.' . '</p>'
. '<p>' . '* **Configuration.** Customize your **search form**  and **search  results** at Booking > Settings > **Search** page. 
    After that you can <a href="https://wpbookingcalendar.com/faq/booking-calendar-shortcodes/"  target="_blank">insert search form</a> shortcode into page and test.*' . '</p>'

                                              )
                                        , array(  'img'  => 'premium/search-results2.png', 'img_style'=>'margin:20px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              
            $this->show_col_section( array( 
                                          array( 'h4'   => 'Coupons for Discounts',
                                                 'text' => 
 '<p>' . 'You can provide **discounts for bookings** to your visitors. Your visitors can **enter coupon codes** in booking form to **get discount** for booking(s).' . '</p>'
.'<p>' . 'Its possible to create coupon code(s), which  will apply to  all or specific booking resources.
    You can set **expiration  date** of coupon code and **minimum cost** of booking, where this coupon code will apply.
    <br/>You can define discount as **fixed cost** or as **percentage** from the total cost  of booking.
' . '</p>'
. '<p>' . '* **Configuration.** Create your coupons codes for discounts at Booking > Resources > **Coupons** page. 
    Then insert <a href="https://wpbookingcalendar.com/faq/booking-form-fields/" target="_blank">coupon text field</a> into your booking form at Booking > Settings > Fields page.*' . '</p>'

                                              )
                                        , array(  'img'  => 'premium/coupons.png', 'img_style'=>'margin:2px 0;width:99%;' ) 
                                        ) 
                                    );              
                                              
            $this->show_col_section( array( 
                                          array( 'h4'   => 'Automatic cancellation of pending bookings',
                                                 'text' => 
//  '<p>' . 'Set **pending days as available** in booking form to prevent from SPAM bookings.' . '</p>'
  '<p>' . 'Activate **automatic cancelation** of **pending bookings** for specific date(s), if you **approved booking** on these date(s) at same booking resource.' . '</p>'
. '<p>' . '*You can activate this feature at the General Booking Settings page in "Advanced" section.*' . '</p>'
                                              )
                                        , array(  'img'  => 'premium/pending-available.png', 'img_style'=>'margin:40px 0;width:99%;' ) 
                                        ) 
                                    );              
              
            

            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.95em;font-style:italic;text-align:right;margin:5px 0 10px;">Check many other nice features in Booking Calendar Business Large version at <a target="_blank" href="https://wpbookingcalendar.com/overview/">features list</a> and test <a target="_blank" href="https://wpbookingcalendar.com/demo/">live demo</a>.</div>' );
            
            ?><div class="clear" style="height:30px;"></div><?php
            
            $this->show_header('Booking Calendar MultiUser version'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 0;" href="http://multiuser.wpbookingcalendar.com/admin-panel/" target="_blank"' 
                                    . '> **Live Demo** *Admin Panel*</a>'
                                    . '<a class="button-secondary" style="float: right; height: 36px; line-height: 34px;margin:6px 10px 6px 0;" href="http://multiuser.wpbookingcalendar.com/" target="_blank"'
                                    . '> **Live Demo** *Front End*</a><div class="clear"></div>'
                              , 'h2', 'font-size: 2.1em;  background:#e5e5e5;color: #777;line-height: 1.5em;padding:5px 15px;' );

            
            $this->show_separator();

            echo wpbc_recheck_strong_symbols( '<div style="font-size: 0.85em;font-style: italic;margin: 5px 0 0 10px;">**Note!** This version support **all functionality** from the Booking Calendar **Business Large** version.</div>' );

            $this->show_col_section( array( 
                                          array( 'h4'   => 'Separate Booking Admin Panels for your Users',
                                                 'text' => 
  '<p>' . 'You can activate **independent booking admin panels** for each registered wordpress **users of your website** *(withing one website)*. ' . '</p>'
. '<p>' . 'Such users *(**owners**)* can **see and manage only own bookings** and booking resources. 
           Other active users *(owners)* will not see the bookings from this owner, they can see only own bookings.' . '</p>' 
                                              
. '<p>' . 'Each *owner* can **configure own booking form**  and **own email templates**, activate and configure payment gateways to **own payment account**. 
           <br />Such users will receive notifications about new bookings to own emails and can approve or decline such  bookings. 
           ' . '</p>'  

. '<p>' . 'There are 2 types of the users: **super booking admin** and **regular users**. 
          Super booking admins can see and manage the bookings and booking resources from any users. Super booking admin can activate and manage status of other users.' . '</p>' 

. '<p>' . 'You can read more about the initial configuration at <a href="https://wpbookingcalendar.com/faq/multiuser-version-init-config/" target="_blank">FAQ</a>.' . '</p>'   

                                              ) 
                                        , array(  'img'  => 'premium/users2.png', 'img_style'=>'margin-top:20px;width:95%;' ) 
                                        ) 
                                    );              
            
            $this->show_separator();
            
            ?><div class="clear" style="height:30px;"></div><?php
            
            
            ?>
            <table class="about-text" style="margin-bottom:30px;height:auto;font-size:1.1em;width:100%;" >
                <tr>
                    <td>
<?php
                            printf( 'Start using %scurrent version%s of Booking Calendar or upgrade to higher version'
                                    , '<a class="button-secondary" style="height: 36px; line-height: 32px;font-size:15px;margin-top: -3px;" href="'
                                      . wpbc_get_bookings_url() .'" >'
                                    , '</a>' 
                                    );
                            ?>
                            <a class="button button-primary" style="font-weight: 600; height: 36px; line-height: 32px;font-size:15px;margin-top: -3px;"  href="<?php echo wpbc_up_link(); ?>" target="_blank">&nbsp;<?php if ( wpbc_get_ver_sufix() == '' ) { _e('Purchase' ,'booking'); } else { _e('Upgrade Now' ,'booking'); } ?>&nbsp;&nbsp;</a>
                    </td>
                </tr>
            </table> 
            
        </div>
        <?php
    }

    }


$wpbc_welcome = new WPBC_Welcome();

