<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbPaymentGatewayStripe' ) ) {
/**
 * This class is responsible for payment processing via Stripe
 * 
 * @since 2.3.0
 */
class rtbPaymentGatewayStripe implements rtbPaymentGateway {

  private static $_instance;

  private final function __construct() {

    $this->register_hooks();

  }

  public static function register_gateway (array $gateway_list )
  {
    return array_merge(
      $gateway_list,
      [
        'stripe' => [
          'label'    => __( 'Stripe', 'restaurant-reservations' ),
          'instance' => self::get_instance()
        ]
      ]
    );
  }

  /**
   * Get singleton instance of the class
   * 
   * @return rtbPaymentGatewayStripe instance
   */
  public static function get_instance()
  {
    if( ! isset( self::$_instance ) ) {
      self::$_instance = new rtbPaymentGatewayStripe();
    }
    
    return self::$_instance;
  }

  public function register_hooks()
  {
    add_action( 'rtb_booking_form_init', [$this, 'process_payment'] );

    add_action( 'wp_ajax_rtb_stripe_get_intent', array( $this, 'create_stripe_pmtIntnt' ) );
    add_action( 'wp_ajax_nopriv_rtb_stripe_get_intent', array( $this, 'create_stripe_pmtIntnt' ) );

    add_action( 'wp_ajax_rtb_stripe_pmt_succeed', array( $this, 'stripe_sca_succeed' ) );
    add_action( 'wp_ajax_nopriv_rtb_stripe_pmt_succeed', array( $this, 'stripe_sca_succeed' ) );

    add_filter( 'rtb_booking_metadata_defaults', [$this, 'default_booking_stripe_info'], 30, 1 );
    add_action( 'rtb_booking_load_post_data', [$this, 'populate_booking_stripe_info'], 30, 2 );
    add_filter( 'rtb_insert_booking_metadata', [$this, 'save_booking_gateway_info'], 30, 2 );
  }

  public function print_payment_form( $booking )
  {
    global $rtb_controller;

    // Function alias
    $_gs = [$rtb_controller->settings, 'get_setting'];
    $SCA = $_gs( 'rtb-stripe-sca' );

    $btn_disabled = '';
    $stripe_lib_version = 'v2';

    if( $SCA )
    {
      $btn_disabled = "disabled='disabled'";
      $stripe_lib_version = 'v3';
    }

    // Stripe Lib
    wp_enqueue_script(
      'rtb-stripe', 
      "https://js.stripe.com/{$stripe_lib_version}/", 
      array( 'jquery' ), 
      RTB_VERSION, 
      true
    );

    // Stripe-JS processing logic
    wp_enqueue_script(
      'rtb-stripe-payment', 
      RTB_PLUGIN_URL . '/assets/js/stripe-payment.js', 
      array( 'jquery', 'rtb-stripe' ), 
      RTB_VERSION, 
      true
    );

    wp_localize_script(
      'rtb-stripe-payment',
      'rtb_stripe_payment',
      array(
        'stripe_mode' => $_gs( 'rtb-stripe-mode' ),
        'stripe_sca'  => $SCA,
        'live_publishable_key' => $_gs( 'rtb-stripe-live-publishable' ),
        'test_publishable_key' => $_gs( 'rtb-stripe-test-publishable' ),
      )
    );

    $payment_amount = $_gs( 'rtb-currency-symbol-location' ) == 'before' 
      ? $_gs( 'rtb-stripe-currency-symbol' ) . $booking->calculate_deposit() 
      : $booking->calculate_deposit() . $_gs( 'rtb-stripe-currency-symbol' );

    $cc_exp_single_field = null != $_gs( 'rtb-expiration-field-single' )
      ? "<input type='text' data-stripe='exp_month_year' class='single-masked'>"
      : "<input type='text' size='2' data-stripe='exp_month'>
        <span> / </span>
        <input type='text' size='4' data-stripe='exp_year'>";
    ?>

    <h2>
      <?php _e('Deposit Required: ', 'restaurant-reservations' ) .  $payment_amount; ?>
    </h2>

    <div class='payment-errors'></div>

    <form 
      action='#' 
      method='POST' 
      id='stripe-payment-form' 
      data-booking_id='<?php echo $booking->ID ;?>'>

      <?php if( $SCA ) { ?>

        <div class='form-row'>
          <label><?php _e('Card Detail', 'restaurant-reservations'); ?></label>
          <span id="cardElement"></span>
        </div>

      <?php } else { ?>

        <div class='form-row'>
          <label><?php _e('Card Number', 'restaurant-reservations'); ?></label>
          <input type='text' size='20' autocomplete='off' data-stripe='card_number'/>
        </div>
        <div class='form-row'>
          <label><?php _e('CVC', 'restaurant-reservations'); ?></label>
          <input type='text' size='4' autocomplete='off' data-stripe='card_cvc'/>
        </div>
        <div class='form-row'>
          <label><?php _e('Expiration (MM/YYYY)', 'restaurant-reservations'); ?></label>
          <?php echo $cc_exp_single_field; ?>
        </div>
        <input type='hidden' name='action' value='rtb_stripe_booking_payment'/>
        <input type='hidden' name='currency' value='<?php echo $_gs( 'rtb-currency' ); ?>' data-stripe='currency' />
        <input type='hidden' name='payment_amount' value='<?php echo $booking->calculate_deposit(); ?>' />
        <input type='hidden' name='booking_id' value='<?php echo $booking->ID; ?>' />

      <?php } ?>
      
      <p class="stripe-payment-help-text">
        <?php _e( 'Please wait. Do not refresh until the button enables or the page reloads.', 'restaurant-reservations' ); ?>
      </p>

      <button type='submit' id='stripe-submit' <?php echo $btn_disabled; ?>>
        <?php _e( 'Make Deposit', 'restaurant-reservations'); ?>
      </button>

    </form>
    <?php
  }

  /**
   * Maybe process payment, if coming from Stripe payment form
   * 
   * @return void Redirect and exit or return
   */
  public function process_payment()
  {
    global $rtb_controller;

    if ( ! isset( $_POST['stripeToken'] ) ||  ! isset( $_POST['booking_id'] ) ) {
      return;
    }

    // Function alias
    $_gs = [$rtb_controller->settings, 'get_setting'];
    $booking_id = $_POST['booking_id'];

    // Define the form's action parameter
    $booking_page = $_gs( 'booking-page' );
    if ( ! empty( $booking_page ) ) {
      $booking_page = get_permalink( $booking_page );
    }
    else {
     $booking_page = get_permalink();
    }

    // load the stripe libraries
    require_once(RTB_PLUGIN_DIR . '/lib/stripe/init.php');
      
    // retrieve the token generated by stripe.js
    $token = $_POST['stripeToken'];

    // JPY currency does not have any decimal palces
    $is_JPY = "JPY" != $_gs( 'rtb-currency' );
    $payment_amount = $is_JPY ? ( $_POST['payment_amount'] * 100 ) : $_POST['payment_amount'];

    $stripe_secret = 'test' == $_gs( 'rtb-stripe-mode' ) 
      ? $_gs( 'rtb-stripe-test-secret' ) 
      : $_gs( 'rtb-stripe-live-secret' );
   
    require_once( RTB_PLUGIN_DIR . '/includes/Booking.class.php' );
    $booking = new rtbBooking();
    $booking->load_post( $booking_id );

    try {
      \Stripe\Stripe::setApiKey( $stripe_secret );
      $charge = \Stripe\Charge::create(array(
          'amount' => $payment_amount, 
          'currency' => strtolower( $_gs( 'rtb-currency' ) ),
          'card' => $token
        )
      );

      $booking->deposit = $is_JPY ? $payment_amount / 100 : $payment_amount;
      $booking->receipt_id = $charge->id;

      $booking->determine_status( true );

      $booking->insert_post_data();

      do_action( 'rtb_booking_paid', $booking );

      // redirect on successful payment
      $redirect = add_query_arg( 
        array(
          'payment' => 'paid',
          'booking_id' => $booking_id
        ), 
        $booking_page
      );
     
    } catch (Exception $e) {
      
      $booking->post_status = 'payment_failed';
      $booking->payment_failure_message = $e->getDeclineCode();

      $booking->insert_post_data();

      // redirect on failed payment
      $redirect = add_query_arg(
        array(
          'payment' => 'failed',
          'booking_id' => $booking_id,
          'error_code' => urlencode( $e->getDeclineCode() )
        ),
        $booking_page
      );
    }
   
    // redirect back to our previous page with the added query variable
    wp_redirect($redirect); exit;
  }

  /**
   * Create Stripe payment intent for reservation deposits
   * Respond to AJAX/XHR request
   * 
   * @since 2.2.8
   */
  public function create_stripe_pmtIntnt()
  {
    global $rtb_controller;

    $response = function ($success, $msg, $data = []) {
      echo json_encode(
        array_merge(
          [
            'success' => $success,
            'message' => $msg
          ], 
          $data
        )
      );

      exit(0);
    };

    if( ! isset( $_POST['booking_id'] ) ) {
      $response(false, 'Invalid booking.');
    }

    require_once( RTB_PLUGIN_DIR . '/includes/Booking.class.php' );
    $booking = new rtbBooking();
    $booking->load_post( $_POST['booking_id'] );

    $payment_amount = "JPY" != $rtb_controller->settings->get_setting( 'rtb-currency' ) 
      ? $booking->calculate_deposit() * 100 
      : $booking->calculate_deposit();

    // load the stripe libraries
    require_once(RTB_PLUGIN_DIR . '/lib/stripe/init.php');

    $stripe_secret = 'test' == $rtb_controller->settings->get_setting( 'rtb-stripe-mode' ) 
      ? $rtb_controller->settings->get_setting( 'rtb-stripe-test-secret' ) 
      : $rtb_controller->settings->get_setting( 'rtb-stripe-live-secret' );

    try {
      \Stripe\Stripe::setApiKey( $stripe_secret );

      // $customer = \Stripe\Customer::create([
      //  'email' => $booking->email,
      //  'name' => $booking->name
      // ]);

      // $booking->stripe_customer_id = $customer->id;
      // $booking->insert_post_data();

      $metadata = array_filter([
        'Booking ID' => $booking->ID,
        'Email'      => $booking->email,
        'Name'       => $booking->name,
        'Date'       => $booking->date,
        'Party'      => $booking->party
      ]);

      if( is_array( $booking->table ) && ! empty( $booking->table ) ) {
        $metadata['Table'] = implode('+', $booking->table);
      }

      $desc = implode(', ', $metadata );
      $stmt_desc = substr( implode( ';', $metadata ), 0, 22 );

      $intent = \Stripe\PaymentIntent::create([
        'amount' => $payment_amount,
        'currency' => $rtb_controller->settings->get_setting( 'rtb-currency' ),
        'payment_method_types' => ['card'],
        'receipt_email' => $booking->email,
        'description' => apply_filters( 'rtb-stripe-payment-desc', $desc ),
        'statement_descriptor' => apply_filters( 'rtb-stripe-payment-stmnt-desc', $stmt_desc ),
        'metadata' => $metadata
      ]);

      $response(
        true, 
        'Payment Intent generated succsssfully', 
        [
          'clientSecret' => $intent->client_secret,
          'name' => $booking->name,
          'email' => $booking->email,
        ]
      );
    }
    catch(Exception $ex) {
      $response( false, 'Please try again.', ['error' => $ex->getError()] );
    }
  }

  /**
   * Stripe SCA payment's final status for reservation deposits
   * Respond to AJAX/XHR request
   * 
   * @since 2.2.8
   */
  public function stripe_sca_succeed()
  {
    global $rtb_controller;

    $response = function ($success = false, $urlParams = '') {
      echo json_encode([
        'success' => $success,
        'urlParams' => $urlParams
      ]);

      exit(0);
    };

    $success = false;
    $urlParams = '';

    if( ! isset( $_POST['booking_id'] ) ) {
      $response();
    }

    require_once( RTB_PLUGIN_DIR . '/includes/Booking.class.php' );
    $booking = new rtbBooking();
    $booking->load_post( $_POST['booking_id'] );

    if( isset( $_POST['success'] ) && 'false' != $_POST['success'] ) {

      $booking->deposit = "JPY" != $rtb_controller->settings->get_setting( 'rtb-currency' ) 
        ? $_POST['payment_amount'] / 100 
        : $_POST['payment_amount'];

      $booking->receipt_id = $_POST['payment_id'];
      $booking->payment_paid();

      // urlParams on successful payment
      $success = true;

      $urlParams = array(
        'payment'    => 'paid',
        'booking_id' => $booking->ID
      );

    }
    else {
      $payment_failure_message = ! empty( $_POST['message'] )
        ? $_POST['message'] : '';

      $booking->payment_failed( $payment_failure_message );
    }

    $response($success, $urlParams);
  }

  /**
   * Repopulate $booking with stripe meta information
   * 
   * @param  rtbBooking $booking
   * @param  WP_Post $wp_post
   */
  public function populate_booking_stripe_info( rtbBooking $booking, $wp_post )
  {
    if ( is_array( $meta = get_post_meta( $booking->ID, 'rtb', true ) ) ) {
      $booking->stripe_customer_id = isset( $meta['stripe_customer_id'] )
        ? $meta['stripe_customer_id']
        : '';
    }
  }

  /**
   * Set $booking's default stripe meta information
   * 
   * @param  rtbBooking $booking
   * @param  WP_Post $wp_post
   */
  public function default_booking_stripe_info( $info_list )
  {
    $info_list['stripe_customer_id'] = '';

    return $info_list;
  }

  public function save_booking_gateway_info ( $meta, rtbBooking $booking )
  {
    if ( isset( $booking->stripe_customer_id ) && !empty( $booking->stripe_customer_id ) ) {
      $meta['stripe_customer_id'] = $this->stripe_customer_id;
    }

    return $meta;
  }
}

}

/**
 * Gateway has to register itself
 */
add_filter(
  'rtb-payment-gateway-register', 
  ['rtbPaymentGatewayStripe', 'register_gateway']
);