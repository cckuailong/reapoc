<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbMigrationManager' ) ) {
/**
 * Class to handle post update migrations for Restaurant Reservations
 *
 * @since 2.2.4
 */
class rtbMigrationManager {
  
  public function __construct() {

    $this->convert_draft_status_to_payment_pending_status();

    $this->convert_payment_gateway_setting();

    $this->update_invalid_setting_values();

    $this->update_setting_value_types();
  }

  

  /************************* Migration callback ahead *************************/

  /**
   * Changes rtb-booking post status from draft to payment_pending while a booking has been created but
   * the payment is yet to be paid. Update all existing rtb-booking posts with status draft to 
   * payment_pending. This change will let admins check and process/delete the existing bookings 
   * without payment, and also let the use pay if they have accidently move elsewhere before
   * the payment is completed.
   * Currently users or admins can not do anything with bookings with the status draft. If the same 
   * booking needs to be made again, at least name or phone number or email needs to be cahgned 
   * for the same slot otherwise validation will fail stating there is a duplicate booking
   * 
   * @removal 2022-01-01
   */
  public function convert_draft_status_to_payment_pending_status() {
    global $wpdb;
    
    $wpdb->query("
      UPDATE
        {$wpdb->posts} 
      SET 
        `post_status` = 'payment_pending' 
      WHERE 
        `post_status` = 'draft' AND `post_type` = 'rtb-booking'
    ");
  }

  /**
   * Update the rtb-payment-gateway setting from string to array
   * Previously this setting was a radio, now it is a checkbox.
   * 
   * @since 2.3.0
   * 
   * @removal 2022-04-01
   */
  public function convert_payment_gateway_setting()
  {
    $settings_page = 'rtb-settings';
    $options = get_option( $settings_page );

    if ( ! is_array( $options ) || ! array_key_exists( 'rtb-payment-gateway', $options ) ) { return; }

    if( ! is_array( $options['rtb-payment-gateway'] ) && version_compare( RTB_VERSION, '2.2.11', '>' ) ) {
      $options['rtb-payment-gateway'] = [ $options['rtb-payment-gateway'] ];

      update_option( $settings_page, $options );
    }
  }

  /**
   * Update setting of type count, which are 'time-reminder-user' and 
   * 'time-late-user'. When no value is being set in admin, it has underscore 
   * at least. Whereas, its expected to be empty
   * 
   * @since 2.3.1
   * 
   * @removal 2022-04-01
   */
  public function update_invalid_setting_values() {
    $settings_page = 'rtb-settings';
    $options = get_option( $settings_page );

    if(
      is_array( $options ) 
      &&
      isset( $options['time-reminder-user'] )
      &&
      '_' == $options['time-reminder-user']
      &&
      version_compare( RTB_VERSION, '2.2.11', '>' )
    )
    {
      $options['time-reminder-user'] = '';
    }

    if(
      is_array( $options ) 
      &&
      isset( $options['time-late-user'] )
      &&
      '_' == $options['time-late-user']
      &&
      version_compare( RTB_VERSION, '2.2.11', '>' )
    )
    {
      $options['time-late-user'] = '';
    }

    update_option( $settings_page, $options );
  }

  /**
   * Updates a number of settings, which now are using the "select" type instead of the "count"
   * type, since they have no units to select
   * 
   * @since 2.3.0
   * 
   * @removal 2022-07-01
   */
  public function update_setting_value_types() {

    $options = get_option( 'rtb-settings' );

    if ( is_array( $options ) ) {

      $options['rtb-dining-block-length'] = ! empty( $options['rtb-dining-block-length'] ) ? intval( $options['rtb-dining-block-length'] ) : '';
      $options['rtb-max-tables-count'] = ! empty( $options['rtb-max-tables-count'] ) ? intval( $options['rtb-max-tables-count'] ) : '';
      $options['rtb-max-people-count'] = ! empty( $options['rtb-max-people-count'] ) ? intval( $options['rtb-max-people-count'] ) : '';
      $options['auto-confirm-max-reservations'] = ! empty( $options['auto-confirm-max-reservations'] ) ? intval( $options['auto-confirm-max-reservations'] ) : '';
      $options['auto-confirm-max-seats'] = ! empty( $options['auto-confirm-max-seats'] ) ? intval( $options['auto-confirm-max-seats'] ) : '';

      update_option( 'rtb-settings', $options );
    }
  }
}

}