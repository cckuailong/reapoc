<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbPaymentManager' ) ) {
/**
 *
 * This class registers all the payment gateways and
 * acts as a bridge between the rest of the plugin and payment gateways.
 * It renders forms, processes payments, handles IPNs etc
 *
 * @since 2.3.0
 */
class rtbPaymentManager {

  /**
   * Default values for Payment manager settings
   */
  public $defaults = array();

  /**
   * All the available payment processing gateway with their internal name, 
   * display name and the PHP Class
   * 
   * @var array [
   *        'paypal' => [
   *          'name' => 'PayPal', 
   *          'instance' => 'new rtbPaymentGatewayPayPal()'
   *        ]
   *      ]
   */
  public $available_gateway_list = array();

  /**
   * List of enabled gateway list from admin
   * 
   * @var array ['paypal']
   */
  public $enabled_gateway_list = array();

  /**
   * Gateway selected for the current booking
   * @var string
   */
  public $in_use_gateway = '';

  /**
   * Booking object. ID property does not exist when no booking loaded
   * @var rtbBooking
   */
  public $booking;

  public $booking_form_field_slug = 'payment-gateway';

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->load_basics();

    do_action( 'rtb_payment_manager_init' );
  }

  /**
   * Acts like a constructor
   */
  public function load_basics()
  {
    global $rtb_controller;

    require_once RTB_PLUGIN_DIR . "/includes/PaymentGateway.interface.php";
    require_once RTB_PLUGIN_DIR . "/includes/PaymentGatewayPayPal.class.php";
    require_once RTB_PLUGIN_DIR . "/includes/PaymentGatewayStripe.class.php";

    do_action( 'rtb_payment_manager_load_gateways' );

    $this->available_gateway_list = apply_filters(
      'rtb-payment-gateway-register', 
      $this->available_gateway_list
    );

    $this->strip_invalid_gateway();

    $this->enabled_gateway_list = $rtb_controller->settings->get_setting( 'rtb-payment-gateway' );

    // Temporary, because migration function do not work on automatic plugin updates
    $this->enabled_gateway_list = is_array( $this->enabled_gateway_list ) 
      ? $this->enabled_gateway_list
      : [ $this->enabled_gateway_list ];

    $this->enabled_gateway_list = apply_filters(
      'rtb-payment-active-gateway', 
      $this->enabled_gateway_list, 
      $this->available_gateway_list
    );

    // if multiple gateways enabled, print list to ask for one gateway
    if ( is_array( $this->enabled_gateway_list ) and 1 < count( $this->enabled_gateway_list ) ) {
      add_filter( 'rtb_booking_form_fields', [$this, 'add_field_booking_form_gateway'], 30, 3 );
    }

    // Determine $in_use_gateway
    add_action( 'rtb_validate_booking_submission', [$this, 'validate_booking_form_gateway'] );

    // Save gateway selected/used for booking as booking meta
    add_filter( 'rtb_insert_booking_metadata', [$this, 'save_booking_gateway_used'], 30, 2 );

    // Repopulate gateway information
    add_action( 'rtb_booking_load_post_data', [$this, 'populate_booking_gateway_used'], 30, 2 );
  }

  /**
   * Get available gateway list with gateway slug as key and label as value
   * 
   * @return array 
   */
  public function get_available_gateway_list()
  {
    $list = [];

    foreach ($this->available_gateway_list as $key => $value) {
      $list[$key] = $value['label'];
    }

    return $list;
  }

  /**
   * Get enabled gateway list with gateway slug as key and label as value
   * 
   * @return array 
   */
  public function get_enabled_gateway_list()
  {
    $list = [];

    // Not gateway has been enabled
    if( 1 > count( $this->enabled_gateway_list ) ) {
      return $list;
    }

    foreach ($this->enabled_gateway_list as $key) {
      $list[$key] = $this->available_gateway_list[$key]['label'];
    }

    return $list;
  }

  /**
   * Is Payments functionality enabled?
   * 
   * @return boolean
   */
  public function is_payment_enabled()
  {
    global $rtb_controller;

    return (
      $rtb_controller->settings->get_setting( 'require-deposit' ) 
      && 
      0 < count( $this->enabled_gateway_list )
    );
  }

  /**
   * Add the payment gateway selector field in the bookgin form
   * 
   * @param  array $fields  Booking form field array. For more info, refer to 
   *                        rtbSettings::get_booking_form_fields()
   * @param  stdObject $request
   * @param  array $args 
   */
  public function add_field_booking_form_gateway( $fields, $request, $args )
  {
    if ( ! $this->is_payment_enabled() ) {
      return $fields;
    }

    /**
     * This is different from admin setting, that is why, to reduce the confusion
     * we use variabel instead of direct field name
     * 
     * @var string rtb-payment-gateway
     */
    $prefixed_field_slug = "rtb-{$this->booking_form_field_slug}";

    $payment_gateway_field = [
      $this->booking_form_field_slug => [
        // 'legend' => __( 'Payment', 'restaurant-reservations' ),
        'fields' => [
          // Field names are prefixed with "rtb-" while rendering field's HTML
          'payment-gateway' => [
            'required'      => true,
            'title'         => __( 'Payment Gateway', 'restaurant-reservations' ),
            'callback'      => 'rtb_print_form_select_field',
            'callback_args' => [
              'options'      => $this->get_enabled_gateway_list(),
              'empty_option' => true
            ],
            'request_input' => isset( $request->raw_input[$prefixed_field_slug] ) 
              ? $request->raw_input[$prefixed_field_slug] 
              : (
                  property_exists($request, $this->booking_form_field_slug) 
                    ? $request->{$this->booking_form_field_slug} 
                    : ''
                )
          ]
        ]
      ]
    ];

    return array_merge( $fields, $payment_gateway_field );
  }

  /**
   * Validate the payment gateway option
   * 
   * $booking is not set yet
   * 
   * @param  stdObject $request
   */
  public function validate_booking_form_gateway( $request )
  {
    if ( ! $this->is_payment_enabled() ) {
      return;
    }

    $prefixed_field_slug = "rtb-{$this->booking_form_field_slug}";

    // Do not validate if only one gateway enabled
    if ( 1 === count( $this->enabled_gateway_list ) ) {
      $this->in_use_gateway = $this->enabled_gateway_list[0];
    }
    elseif (
      array_key_exists( $prefixed_field_slug, $_POST ) 
      && 
      ! empty( $_POST[$prefixed_field_slug] ) 
      && 
      in_array( $_POST[$prefixed_field_slug], $this->enabled_gateway_list )
    )
    {
      $this->in_use_gateway = $_POST[$prefixed_field_slug];
    }
    else {
      $request->validation_errors[] = array(
        'field'   => $prefixed_field_slug,
        'message' => __( 'Please select a valid payment gateway.', 'restaurant-reservations' )
      );
    }

    if ( $this->isset_gateway_in_use() ) {
      $request->{$this->booking_form_field_slug} = $this->in_use_gateway;
    }
  }

  /**
   * Attach payment gateway information with the booking
   * 
   * @param  array $meta
   * @param  rtbBooking $booking
   * 
   * @return array
   */
  public function save_booking_gateway_used( $meta, rtbBooking $booking )
  {
    if ( isset( $booking->{$this->booking_form_field_slug} ) ) {
      $meta[$this->booking_form_field_slug] = $booking->{$this->booking_form_field_slug};
    }

    return $meta;
  }

  /**
   * Repopulate $booking with gateway information
   * 
   * @param  rtbBooking $booking
   * @param  WP_Post $wp_post
   */
  public function populate_booking_gateway_used( rtbBooking $booking, $wp_post )
  {
    if ( is_array( $meta = get_post_meta( $booking->ID, 'rtb', true ) ) ) {
      $booking->{$this->booking_form_field_slug} = isset( $meta[$this->booking_form_field_slug] )
        ? $meta[$this->booking_form_field_slug]
        : '';
    }
  }

  /**
   * Print the payment form's HTML code, after a new booking has been inserted 
   * notices.
   * 
   * $booking must be set before this.
   */
  public function print_payment_form()
  {
    global $rtb_controller;

    require_once( RTB_PLUGIN_DIR . '/includes/Booking.class.php' );

    if ( ! $this->isset_gateway_in_use() ) {
      $this->set_gateway_in_use( $this->booking->{$this->booking_form_field_slug} );
    }

    if (
      in_array( $this->in_use_gateway, $this->enabled_gateway_list )
      &&
      property_exists($this->booking, 'ID')
    )
    {
      $gateway = $this->available_gateway_list[$this->in_use_gateway]['instance'];

      $gateway->print_payment_form( $this->booking );
    }
    else {
      $this->print_invalid_gateway();
    }
  }

  public function print_invalid_gateway()
  {
    echo __(
      'Invalid gateweay selected. Please contact us for the confirmation.', 
      'restaurant-reservations'
    );
  }

  public function process_payment()
  {
    // code...
  }

  public function is_payment_processed()
  {
    // code...
  }

  public function payment_processing_status()
  {
    // code...
  }

  /**
   * Set booking object
   * 
   * @param rtbBooking $booking
   */
  public function set_booking(rtbBooking $booking)
  {
    $this->booking = $booking;

    return $this;
  }

  public function set_gateway_in_use( $gateway )
  {
    $this->in_use_gateway = $gateway;
  }

  public function get_gateway_in_use()
  {
    if ( $this->isset_gateway_in_use() ) {
      return $this->in_use_gateway;
    }

    return '';
  }

  public function isset_gateway_in_use()
  {
    return in_array( $this->in_use_gateway, $this->enabled_gateway_list );
  }

  /**
   * Remove any class registered as payment gateway without implementing the 
   * payment gateway interface
   */
  public function strip_invalid_gateway() {
    
    foreach ( $this->available_gateway_list as $gateway => $data ) {

      if ( $data['instance'] instanceof rtbPaymentGateway ) { continue; }
        
      unset( $this->available_gateway_list[$gateway] );
    }
  }
}

}