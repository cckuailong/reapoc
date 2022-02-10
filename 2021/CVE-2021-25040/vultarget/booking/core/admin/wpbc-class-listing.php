<?php /**
 * @version 1.1
 * @package Booking Calendar
 * @category Booking Listing Table in Admin Panel
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-12-28
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

class WPBC_Booking_Listing_Table {
    
    public $bookings;
    public $booking_types;
    
    private $url;                   // URL for differnt Menus
    private $user_id;               // ID of logged in users
    private $is_free;               // Version  type
    private $days_column_style;     // CSS Styles for days columns
    private $date_view_type;        // Initial Days View mode 
    
    
    public function __construct( $bookings, $booking_types ) {
        
        $this->bookings = $bookings;
        $this->booking_types = $booking_types;
        
        $this->url = array();
        $this->days_column_style = array( 'wide' => 'color:#333;', 'short' => 'color:#333;' );
        $this->init_params();                        
    }

    
    /**
	 * Get URL  for specific menu
     * 
     * @param sting $url    - 'listing' | 'add' | 'resources' | 'settings' | 'request'
     * @return string       - url
     */
    public function get_url( $url = 'listing') {
        if ( isset( $this->url[ $url ] ) )
            return $this->url[ $url ];
        else 
            $this->url[ 'listing' ];
    }

    
    /** Check if Bookings exist or no */
    public function is_bookings_exist() {
        if ( count( $this->bookings ) > 0 )
            return true;
        else 
            return false;
    }

    
    /** Init Paramteres */
    private function init_params() {
        
        $user = wp_get_current_user();  
        $this->user_id = $user->ID;

        $this->url['listing']     = 'admin.php?page=' . wpbc_get_bookings_url( false, false );
        $this->url['add']         = 'admin.php?page=' . wpbc_get_new_booking_url( false, false);
        $this->url['resources']   = 'admin.php?page=' . wpbc_get_resources_url( false, false );
        $this->url['settings']    = 'admin.php?page=' . wpbc_get_settings_url( false, false );

        // Transform the REQESTS parameters (GET and POST) into URL
        $this->url['request'] = wpbc_get_params_in_url( wpbc_get_bookings_url( false, false ), array('page_num', 'wh_booking_type') );

        
        $this->date_view_type = get_bk_option( 'booking_date_view_type');
        if ( $this->date_view_type == 'short' ) $this->days_column_style['wide']  .= 'display:none;';
        else                                    $this->days_column_style['short'] .= 'display:none;';

        $version = get_bk_version();
        if ( $version == 'free' ) $this->is_free = true;
        else                      $this->is_free = false;    
    }

    
    /** Show Listing Table */
    public function show() {
        
        ?><div id="listing_visible_wpbc" class="container-fluid table table-striped wpbc_selectable_table"><?php 
        
        if ( $this->is_bookings_exist() ) {
            
            $this->header( $this->is_free  );
            
            ?><span class="wpbc_selectable_body"><?php
            
                $this->rows( $this->is_free  );
            
            ?></span><?php
            
        } else {
            ?><center>
                <h4><?php _e('Nothing Found', 'booking'); ?>.</h4>
            </center><?php
        }
        
        ?></div><?php
    }
    
    
    /** Show Header */
    public function header( $is_free ) {
        
        ?>
        <div class="row wpbc-listing-header wpbc_selectable_head">
            <div class="wpbc-listing-collumn col-sm-<?php echo $is_free ? '2' : '3'; ?> col-xs-4">
                <div class="row">
                    <div class="wpbc-no-margin col-sm-1 col-xs-1 wpbc_column_1 check-column" >
                        <input type="checkbox" onclick="javascript:wpbc_set_checkbox_in_table( this.checked, 'wpbc_list_item_checkbox' );" class="wpbc-no-margin" style="vertical-align: middle;"/>
                    </div>
                    <div class="wpbc-no-margin col-sm-1 col-xs-5 text-center wpbc_column_2">
                        &nbsp;<?php _e('ID', 'booking'); ?>
                    </div>
                    <div class="wpbc-no-margin col-sm-<?php echo $is_free ? '6' : '7'; ?> text-left hide-sm wpbc_column_3">
                        <?php _e('Labels' ,'booking');  if ( ! $is_free ) { echo ' / '; _e('Actions' ,'booking'); } ?>
                    </div>
                </div>
            </div>            
            <div class="wpbc-listing-collumn col-sm-<?php echo ( $is_free ) ? '5' : '6'; ?> col-xs-6 text-center wpbc_column_4"><?php _e('Booking Data', 'booking'); ?></div>	<?php //FixIn: 7.1.2.5 ?>
            <div class="wpbc-listing-collumn col-sm-3 hide-sm text-center wpbc_column_5"><?php _e('Booking Dates', 'booking'); ?>&nbsp;&nbsp;&nbsp;
                <a  id="booking_dates_full" 
                    onclick="javascript:jQuery('#booking_dates_full,.booking_dates_small').hide();jQuery('#booking_dates_small,.booking_dates_full').show();" href="javascript:void(0)" 
                    title="<?php _e('Show ALL dates of booking' ,'booking'); ?>" 
                    style="<?php echo $this->days_column_style['short']; ?>" 
                    class="tooltip_top" 
                ><i class="glyphicon glyphicon-resize-full" style=" margin-top: 2px;"></i></a>
                <a  id="booking_dates_small" 
                    onclick="javascript:jQuery('#booking_dates_small,.booking_dates_full').hide();jQuery('#booking_dates_full,.booking_dates_small').show();" href="javascript:void(0)" 
                    title="<?php _e('Show only check in/out dates' ,'booking'); ?>"  
                    style="<?php echo $this->days_column_style['wide']; ?>" 
                    class="tooltip_top" 
                ><i class="glyphicon glyphicon-resize-small" style=" margin-top: 2px;"></i></a>                
            </div>
            <?php if ( $is_free ) { ?>
            <div class="wpbc-listing-collumn col-sm-2 hide-sm text-center wpbc_column_6"><?php _e('Actions', 'booking'); ?></div>	<?php //FixIn: 7.1.2.5 ?>
            <?php } ?>
        </div>
        <?php
    }
    
    
    /**
	 * Show Listing Rows
     * 
     * @param boolean $is_free
     */
    public function rows( $is_free ) {

        $availbale_locales_in_system = get_available_languages();
        $print_data = apply_bk_filter( 'wpbc_print_get_header', array( array() ) );   // P        
        $bk_key = 0;

        foreach ( $this->bookings as $bk ) {            
            $bk_key++;

			$bk->form_show = str_replace( "&amp;", '&', $bk->form_show );												//FixIn:7.1.2.12

			$row_data = array();                
            $row_data[ 'availbale_locales' ] = $availbale_locales_in_system;            
            $row_data[ 'css' ]  = '';
            $row_data[ 'css' ] .= $bk_key % 2 ? '' : ' row_alternative_color';
            $row_data[ 'css' ] .= ( $bk_key == ( count( $this->bookings ) ) ) ? ' wpbc-listing-last_row' : '';        
            ////////////////////////////////////////////////////////////////////
            
            $date_format = get_bk_option( 'booking_date_format' );
            if ( empty( $date_format ) ) $date_format = 'm / d / Y, D';
            $time_format = get_bk_option( 'booking_time_format' );            
            if ( empty( $time_format ) ) $time_format = 'h:i a';
            $row_data['cr_date'] = date_i18n( $date_format  , mysql2date( 'U', $bk->modification_date ) );
            $row_data['cr_time'] = date_i18n( $time_format  , mysql2date( 'U', $bk->modification_date ) );
            $row_data['id']         = $bk->booking_id;                          // 100
            $row_data['is_new']     = (isset( $bk->is_new )) ? $bk->is_new : '0';
            $row_data['modification_date']   = (isset( $bk->modification_date )) ? $bk->modification_date : '';    // 2012-02-29 16:01:58
            $row_data['form']       = $bk->form;                                // select-one^rangetime5^10:00 - 12:00~text^name5^Jonny~text^secondname5^Smith~email^ ....
            $row_data['form_show']  = $bk->form_show;                           // First Name:Jonny   Last Name:Smith   Email:email@server.com  Country:GB  ....
            $row_data['form_data']  = $bk->form_data;                           // Array ([name] => Jonny... [_all_] => Array ( [rangetime5] => 10:00 - 12:00 [name5] => Jonny ... ) .... )
            $row_data['dates']      = $bk->dates;                               // Array ( [0] => stdClass Object ( [booking_id] => 8 [booking_date] => 2012-04-16 10:00:01 [approved] => 0 [type_id] => )
            $row_data['dates_short'] = $bk->dates_short;                        // Array ( [0] => 2012-04-16 10:00:01 [1] => - [2] => 2012-04-20 12:00:02 [3] => , [4] => 2012-04-16 10:00:01 ....
            $row_data['is_approved'] = ( count( $bk->dates ) > 0 ) ? $bk->dates[0]->approved : 0;

			$row_data['sync_gid'] = $bk->sync_gid ;                             //FixIn: 8.4.5.10
            //Is booking in Trash.
            $row_data['is_trash'] = $bk->trash ;                                //FixIn: 6.1.1.10     
            
            // BL **************************************************************
            $row_data['dates_short_id'] = ( ( count( $bk->dates ) > 0 ) && ( isset( $bk->dates_short_id ) ) ) ? $bk->dates_short_id : array();    // Array ([0] => [1] => .... [4] => 6... [11] => [12] => 8 )
            
            // Get SHORT Dates showing data ////////////////////////////////////
            $row_data['short_dates_content'] = wpbc_get_short_dates_formated_to_show( $row_data['dates_short'], $row_data['is_approved'], $row_data['dates_short_id'], $this->booking_types );

            // Get WIDE Dates showing data /////////////////////////////////////
            $row_data['wide_dates_content'] = wpbc_get_wide_dates_formated_to_show( $row_data['dates'], $row_data['is_approved'], $this->booking_types );
            
            // P ***************************************************************
            $row_data['resource']       = ( isset( $bk->booking_type ) ) ? $bk->booking_type : '1';
            $row_data['resource_name']  = '<span class="label_resource_not_exist">' . __( 'Default', 'booking' ) . '</span>';
            
            if ( class_exists( 'wpdev_bk_personal' ) ) {
                
                if ( isset( $this->booking_types[ $row_data['resource'] ] ) ) {
                    
                    $row_data['resource_name'] = $this->booking_types[$row_data['resource']]->title;
                    $row_data['resource_name'] = apply_bk_filter('wpdev_check_for_active_language', $row_data['resource_name'] );
                    if ( (0) && ( strlen( $row_data['resource_name'] ) > 19 ) ) {       //FixIn: 7.0.1.66
                        $row_data['resource_name'] = '<span style="cursor:pointer;" class="tooltip_top" title="' . $row_data['resource_name'] . '">' 
                                                    . substr( $row_data['resource_name'], 0, 13 ) 
                                                    . ' ... ' . substr( $row_data['resource_name'], -3 ) 
                                                    . '</span>';
                    }
                } else 
                    $row_data['resource_name'] = '<span class="label_resource_not_exist">' . __( 'Resource not exist', 'booking' ) . '</span>';                
            }
            
            $row_data['hash']       = (isset( $bk->hash )) ? $bk->hash : '';                // 99c9c2bd4fd0207e4376bdbf5ee473bc
            $row_data['remark']     = (isset( $bk->remark )) ? $bk->remark : '';
            
            // BS **************************************************************
            $row_data['cost']        = (isset( $bk->cost )) ? $bk->cost : '';                // 150.00
            $row_data['pay_status']  = (isset( $bk->pay_status )) ? $bk->pay_status : '';    // 30800
            $row_data['pay_request'] = (isset( $bk->pay_request )) ? $bk->pay_request : '';  // 0
            $row_data['status']      = (isset( $bk->status )) ? $bk->status : '';
            $row_data['is_paid']     = 0;
            $row_data['current_payment_status_titles'] = '';
            $row_data['pay_print_status'] = '';
            
            if ( class_exists( 'wpdev_bk_biz_s' ) ) {

                if ( wpbc_is_payment_status_ok( trim( $row_data['pay_status'] ) ) )  $row_data['is_paid'] = 1;

                $payment_status_titles = get_payment_status_titles();
                $row_data['current_payment_status_titles']  = array_search( $row_data['pay_status'], $payment_status_titles );
                if ( $row_data['current_payment_status_titles'] === false )
                     $row_data['current_payment_status_titles'] = $row_data['pay_status'];


                if ( $row_data['is_paid'] ) {
                    //$row_data['pay_print_status'] = __( 'Paid OK', 'booking' );
                    $row_data['pay_print_status'] = $row_data['pay_status'];			//Payment status with  payment system description,  like PayPal:Ok	//FixIn: 8.2.1.25
                    if ( $row_data['current_payment_status_titles'] == 'Completed' )
                        $row_data['pay_print_status'] = $row_data['current_payment_status_titles'];
                } else if ( ( is_numeric( $row_data['pay_status'] ) ) || ( $row_data['pay_status'] == '' ) ) {
                    $row_data['pay_print_status'] = __( 'Unknown', 'booking' );
                } else {
                    $row_data['pay_print_status'] = $row_data['current_payment_status_titles'];
                }
            }
            
            // Print data  /////////////////////////////////////////////////////
            $print_data[] = apply_bk_filter( 'wpbc_print_get_row'
                                            , array() 
                                            , $row_data['id']
                                            , $row_data['is_approved']
                                            , $row_data['form_show'] 	.  ( ! empty($row_data['remark'] ) ? ( "<br/>" . $row_data['remark'] ) : '' )	//FixIn: 8.4.2.2
                                            , $row_data['resource_name']
                                            , $row_data['is_paid']
                                            , $row_data['pay_print_status']
                                            , ( $this->date_view_type == 'short' ) ? '<div class="booking_dates_small">' 
                                                                                       . $row_data['short_dates_content'] 
                                                                                       . '</div>' 
                                                                                     : '<div class="booking_dates_full">' 
                                                                                       . $row_data['wide_dates_content'] 
                                                                                       . '</div>'
                                            , $row_data['cost']
                                            , $row_data['resource']
                    );

            ////////////////////////////////////////////////////////////////////
            
            $this->show_row( $row_data, $is_free );               
        }
                
        make_bk_action( 'wpbc_listing_show_change_booking_resources', $this->booking_types );
                
        make_bk_action( 'wpbc_print_loyout', $print_data );
    }
    
    
    /**
	 * Show 1 Listing Row
     * 
     * @param array $row_data - Array of data to  show
     * @param boolean $is_free 
     */
    public function show_row( $row_data, $is_free ) {
        
        // is New
      ?><div id="booking_mark_<?php echo $row_data[ 'id' ]; ?>"  
            class="<?php if ( $row_data[ 'is_new'] != '1') echo ' hidden_items '; ?> wpbc-listing-collumn new-label clearfix-height">
             <a href="javascript:void(0)"  
                onclick="javascript:mark_read_booking( '<?php echo $row_data[ 'id' ]; ?>', 0, <?php echo $this->user_id; ?>, '<?php echo wpbc_get_booking_locale(); ?>' );"
                class="tooltip_right approve_bk_link"                
                title="<?php _e('New booking' ,'booking'); ?>" 
                ><i class="glyphicon glyphicon-flash"></i></a>
        </div><?php 
          
        // Row start
        ?><div id="booking_row_<?php echo $row_data[ 'id' ]; ?>" class="row wpbc_row clearfix-height wpbc-listing-row <?php echo $row_data[ 'css' ]; ?><?php echo $is_free ? ' wpbc_free' : ''; ?>"><?php

            ?><div class="wpbc-listing-collumn col-sm-<?php echo $is_free ? '2' : '3'; ?> col-xs-12">
                <div class="row"><?php 
                
                    // Checkbox
                  ?><div class="wpbc-no-margin col-sm-1 col-xs-1 field-checkbox wpbc_column_1 check-column">
                        <input type="checkbox" class="wpbc-no-margin wpbc_list_item_checkbox booking_list_item_checkbox"
                               onclick="javascript: if (jQuery(this).attr('checked') !== undefined ) { jQuery(this).parent().parent().parent().parent().addClass('row_selected_color'); } else {jQuery(this).parent().parent().parent().parent().removeClass('row_selected_color');}"
                               id="booking_id_selected_<?php  echo $row_data[ 'id' ];  ?>"  
                               name="booking_appr_<?php  $row_data[ 'id' ];  ?>"
                               />
                    </div><?php
                    // ID
                  ?><div class="wpbc-no-margin col-sm-1 col-xs-1 field-id text-center wpbc_column_2">                            
                        <span class="label"><?php echo $row_data[ 'id' ]; ?></span>
                    </div><?php 
                    
                    // Labels
                  ?><div class="wpbc-no-margin col-sm-<?php echo $is_free ? '6' : '8'; ?> col-xs-10 text-left field-labels booking-labels wpbc_column_3" >	<?php //FixIn: 8.0.2.5    $is_free ? '6' : '7' ?>
                        <?php make_bk_action('wpbc_booking_listing_show_label_resource', $row_data['resource_name'], $this->url['request'] .'&wh_booking_type='. $row_data['resource'] );  ?>
                        <span class="label label-default label-pending <?php if ($row_data['is_approved']) echo ' hidden_items '; ?> "><?php _e('Pending' ,'booking'); ?></span>
                        <span class="label label-default label-approved <?php if (! $row_data['is_approved']) echo ' hidden_items '; ?>"><?php _e('Approved' ,'booking'); ?></span>
		                <?php make_bk_action( 'wpdev_bk_listing_show_payment_label', $row_data['is_paid'], $row_data['pay_print_status'], $row_data['current_payment_status_titles'], $row_data['pay_status'] );  //FixIn: 8.7.7.13		 ?>
                        <span class="label label-trash label-danger <?php if (! $row_data['is_trash']) echo ' hidden_items '; ?> "><?php _e('In Trash / Rejected' ,'booking'); ?></span><?php //FixIn: 6.1.1.10 ?>
		                <?php //FixIn: 8.4.5.10
		                if ( ! empty ( $row_data['sync_gid'] ) ) {
			                ?><span class="label label-imported label-primary"><?php _e( 'Imported', 'booking' ); ?></span><?php
		                } ?>
                    </div><?php 
              ?></div>
            </div><?php 
                        
            // Data
            ?><div class="wpbc-listing-collumn col-sm-<?php echo ( $is_free ) ? '5' : '6'; ?> col-xs-12 wpbc-text-justify field-content wpbc_column_4">	<?php //FixIn: 7.1.2.5 ?>
                <?php  echo $row_data['form_show'];  ?>
				<?php //  echo ( ! empty($row_data['remark'] ) ? ( "<br/>" . $row_data['remark'] ) : '' );	//FixIn: 8.4.2.2  // Its will  add the Notes section to the listing at  Booking Listing page?>
            </div><?php 
            
            //Dates
            ?><div class="wpbc-listing-collumn col-sm-3 col-xs-12 text-center field-dates booking-dates wpbc_column_5">
                <div class="booking_dates_small" style="<?php echo $this->days_column_style['short']; ?>"><?php echo $row_data['short_dates_content']; ?></div>
                <div class="booking_dates_full"  style="<?php echo $this->days_column_style['wide'];  ?>"><?php echo $row_data['wide_dates_content'];  ?></div>                
            </div><?php
            
            if ( ! $is_free ) {
                ?><div class="clear"></div><?php 
            }
            
            // Actions
            ?><div class="wpbc-listing-collumn col-sm-<?php echo $is_free ? '2' : '10'; ?> col-xs-12 text-left field-action-buttons booking-actions wpbc_column_6"><?php  //FixIn: 7.1.2.5

                // Cost
                make_bk_action( 'wpbc_booking_listing_button_cost_edit', $row_data );
                
                
                ?><div class="actions-fields-group control-group"><?php 
                
                    // Payment Status                    
                    make_bk_action('wpbc_booking_listing_button_payment_status', $row_data );

                    ?><span class="wpbc-buttons-separator"></span><?php

                    // Edit
                    $row_data['edit_booking_url'] = $this->url['add'] . '&booking_type=' . $row_data['resource'] . '&booking_hash=' . $row_data['hash'] . '&parent_res=1' ;
                    make_bk_action( 'wpbc_booking_listing_button_edit', $row_data );

                    // Change booking resource
                    make_bk_action( 'wpbc_booking_listing_button_change_resource', $row_data );

                    // Duplicate
                    make_bk_action( 'wpbc_booking_listing_button_duplicate', $row_data );
                    
                    // Print
                    make_bk_action( 'wpbc_booking_listing_button_print', $row_data );
                    
                    // Notes
                    make_bk_action( 'wpbc_booking_listing_button_notes', $row_data );
                    
                    // Change Locale
                    make_bk_action( 'wpbc_booking_listing_button_locale', $row_data );
                    
                    wpbc_btn_add_booking_to_google_calendar( $row_data );												//FixIn: 7.1.2.5
					
                    ?><span class="wpbc-buttons-separator"></span><?php

                                                                                //FixIn: 6.1.1.10
                    // Trash
                   ?><a href="javascript:void(0)"
                        onclick="javascript:if ( wpbc_are_you_sure('<?php echo esc_js(__('Do you really want to do this ?' ,'booking')); ?>') ) trash__restore_booking( 1, <?php echo $row_data[ 'id' ]; ?>, <?php echo $this->user_id; ?>, '<?php echo wpbc_get_booking_locale(); ?>' , 1   );"
                        class="tooltip_top button-secondary button trash_bk_link <?php if ( $row_data['is_trash'] ) echo ' hidden_items '; ?>"
                        title="<?php _e('Reject - move to trash' ,'booking'); ?>"
                    ><i class="glyphicon glyphicon-trash"></i></a><?php
                    // Restore
                   ?><a href="javascript:void(0)"
                        onclick="javascript:if ( wpbc_are_you_sure('<?php echo esc_js(__('Do you really want to do this ?' ,'booking')); ?>') ) trash__restore_booking( 0, <?php echo $row_data[ 'id' ]; ?>, <?php echo $this->user_id; ?>, '<?php echo wpbc_get_booking_locale(); ?>' , 1   );"
                        class="tooltip_top button-secondary button restore_bk_link <?php if ( ! $row_data['is_trash'] ) echo ' hidden_items '; ?>"
                        title="<?php _e('Restore' ,'booking'); ?>"
                    ><i class="glyphicon glyphicon-repeat"></i></a><?php
                    // Delete
                   ?><a href="javascript:void(0)"
                        onclick="javascript:if ( wpbc_are_you_sure('<?php echo esc_js(__('Do you really want to delete this booking ?' ,'booking')); ?>') ) delete_booking(<?php echo $row_data[ 'id' ]; ?>, <?php echo $this->user_id; ?>, '<?php echo wpbc_get_booking_locale(); ?>' , 1   );"
                        class="tooltip_top button-secondary button delete_bk_link <?php if ( ! $row_data['is_trash'] ) echo ' hidden_items '; ?>"
                        title="<?php _e('Completely Delete' ,'booking'); ?>"
                    ><i class="glyphicon glyphicon-remove"></i></a><?php
                                                                                //End FixIn: 6.1.1.10


                    // Approve
                   ?><a href="javascript:void(0)" 
                        onclick="javascript:approve_unapprove_booking(<?php echo $row_data[ 'id' ]; ?>,1,<?php echo $this->user_id; ?>,'<?php echo wpbc_get_booking_locale(); ?>',1);" 
                        class="tooltip_top approve_bk_link button-secondary button <?php if ($row_data['is_approved']) echo ' hidden_items '; ?> " 
                        title="<?php _e('Approve' ,'booking'); ?>"
                    ><i class="glyphicon glyphicon-ok-circle"></i></a><?php  

                    // Reject
                   ?><a href="javascript:void(0)"
                        onclick="javascript:if ( wpbc_are_you_sure('<?php echo esc_js(__('Do you really want to set booking as pending ?' ,'booking')); ?>') ) approve_unapprove_booking(<?php echo $row_data[ 'id' ]; ?>,0, <?php echo $this->user_id; ?>, '<?php echo wpbc_get_booking_locale(); ?>' , 1  );"
                        class="tooltip_top pending_bk_link button-secondary button <?php if (! $row_data['is_approved']) echo ' hidden_items '; ?> "
                        title="<?php _e('Pending' ,'booking'); ?>"
                    ><i class="glyphicon glyphicon-ban-circle"></i></a><?php 
                
                ?></div><?php 
                
            ?></div><?php 

            // Created Date
            ?><div class="wpbc-listing-collumn col-sm-<?php echo $is_free ? '12' : '2'; ?> col-xs-12 text-left field-system-info wpbc_column_7"><?php  
                ?><span><?php _e('Created' ,'booking'); ?>:</span> <span class="field-creation-date"><?php echo $row_data['cr_date'], ' ', $row_data['cr_time']; ?></span><?php 
            ?></div><?php
            
                        
            // Notes Section
            make_bk_action( 'wpbc_booking_listing_section_notes', $row_data );
            
            // Change Resources section
            make_bk_action( 'wpbc_booking_listing_section_change_resource', $row_data );
            
            // Payment Status Section
            make_bk_action( 'wpbc_booking_listing_section_payment_status', $row_data );
            
            
        ?></div><?php 
        
    }
        
}