<?php
/**
 * Withdraw class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.0.0
 */

namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Withdraw {

	public $available_withdraw_methods;
	public $get_options;
	public $withdraw_methods;

	public function __construct() {
		$this->get_options = $this->get_options();
		$this->withdraw_methods = $this->withdraw_methods();
		$this->available_withdraw_methods = $this->available_withdraw_methods();

		add_action('tutor_options_tutor_withdraw_withdraw_methods_before', array($this, 'withdraw_admin_options'));
		add_action('tutor_option_save_after', array($this, 'withdraw_option_save'));

		add_action('wp_ajax_tutor_save_withdraw_account', array($this, 'tutor_save_withdraw_account'));
		add_action('wp_ajax_tutor_make_an_withdraw', array($this, 'tutor_make_an_withdraw'));
	}

	public function withdraw_methods(){
		$methods = array(
			'bank_transfer_withdraw' => array(
				'method_name'  => __('Bank Transfer', 'tutor'),
				'image' => tutor()->url . 'assets/images/payment-bank.png',
				'desc'  => __('Get your payment directly into your bank account', 'tutor'),

				'admin_form_fields'           => array(
					'instruction' => array(
						'type'      => 'textarea',
						'label'     => __('Instruction', 'tutor'),
						'desc'     => __('Write instruction for the instructor to fill bank information', 'tutor'),
					),
				),

				'form_fields'           => array(
					'account_name' => array(
						'type'      => 'text',
						'label'     => __('Account Name', 'tutor'),
					),

					'account_number' => array(
						'type'      => 'text',
						'label'      => __('Account Number', 'tutor'),
					),

					'bank_name' => array(
						'type'      => 'text',
						'label'     => __('Bank Name', 'tutor'),
					),
					'iban' => array(
						'type'      => 'text',
						'label'     => __('IBAN', 'tutor'),
					),
					'swift' => array(
						'type'      => 'text',
						'label'     => __('BIC / SWIFT', 'tutor'),
					),

				),
			),

			'echeck_withdraw' => array(
				'method_name'  => __('E-Check', 'tutor'),
				'image' => tutor()->url . 'assets/images/payment-echeck.png',
				'form_fields'           => array(
					'physical_address' => array(
						'type'      => 'textarea',
						'label'     => __('Your Physical Address', 'tutor'),
						'desc'      => __('We will send you an E-Check to this address directly.', 'tutor'),
					),
				),
			),

			'paypal_withdraw' => array(
				'method_name'  => __('PayPal', 'tutor'),
				'image'	=> tutor()->url . 'assets/images/payment-paypal.png',
				'form_fields'           => array(
					'paypal_email' => array(
						'type'      => 'email',
						'label'     => __('PayPal E-Mail Address', 'tutor'),
						'desc'      => __('We will use this email address to send the money to your Paypal account', 'tutor'),
					),

				),
			),

		);

		$withdraw_methods = apply_filters('tutor_withdraw_methods', $methods);

		return $withdraw_methods;
	}

	/**
	 * @return mixed|array
	 *
	 * Return only enabled methods
	 */
	public function available_withdraw_methods(){
		$withdraw_options = $this->get_options();
		$methods = $this->withdraw_methods();

		foreach ($methods as $method_id => $method){
			$is_enable = (bool) tutor_utils()->avalue_dot($method_id.".enabled", $withdraw_options);

			if ( ! $is_enable){
				unset($methods[$method_id]);
			}
		}

		return $methods;
	}

	public function get_options(){
		return (array) maybe_unserialize(get_option('tutor_withdraw_options'));
	}

	public function withdraw_admin_options(){
		include tutor()->path.'views/options/withdraw/withdraw_admin_options_generator.php';
	}


	/**
	 * Save Withdraw method
	 *
	 * @since v.1.2.0
	 */
	public function withdraw_option_save(){
		do_action('tutor_withdraw_options_save_before');

		$option = (array) isset($_POST['tutor_withdraw_options']) ? $_POST['tutor_withdraw_options'] : array();
		$option = apply_filters('tutor_withdraw_options_input', $option);
		update_option('tutor_withdraw_options', $option);

		do_action('tutor_withdraw_options_save_after');
	}

	/**
	 * Save Withdraw Method Data
	 *
	 * @since v.1.2.0
	 */

	public function tutor_save_withdraw_account(){
		//Checking nonce
		tutor_utils()->checking_nonce();

		$user_id = get_current_user_id();
		$post = $_POST;

		$method = tutor_utils()->avalue_dot('tutor_selected_withdraw_method', $post);
		if ( ! $method){
			wp_send_json_error();
		}

		$method_data = tutor_utils()->avalue_dot("withdraw_method_field.".$method, $post);
		$available_withdraw_method = tutor_withdrawal_methods();

		if (tutor_utils()->count($method_data)){
			$saved_data = array();
			$saved_data['withdraw_method_key'] = $method;
			$saved_data['withdraw_method_name'] = tutor_utils()->avalue_dot($method.".method_name",  $available_withdraw_method);

			foreach ($method_data as $input_name => $value){
				$saved_data[$input_name]['value'] = esc_sql( sanitize_text_field($value) ) ;
				$saved_data[$input_name]['label'] = tutor_utils()->avalue_dot($method.".form_fields.{$input_name}.label",  $available_withdraw_method);
			}

			update_user_meta($user_id, '_tutor_withdraw_method_data', $saved_data);
		}

		$msg = apply_filters('tutor_withdraw_method_set_success_msg', __('Withdrawal account information saved successfully!', 'tutor'));
		wp_send_json_success(array('msg' => $msg ));
	}

	public function tutor_make_an_withdraw(){
		global $wpdb;

		//Checking nonce
		tutor_utils()->checking_nonce();

		do_action('tutor_withdraw_before');


		$user_id = get_current_user_id();
		$post = $_POST;

		$withdraw_amount = sanitize_text_field(tutor_utils()->avalue_dot('tutor_withdraw_amount', $post));

		$earning_sum = tutor_utils()->get_earning_sum();
		$min_withdraw = tutor_utils()->get_option('min_withdraw_amount');

		$saved_withdraw_account = tutor_utils()->get_user_withdraw_method();
		$formatted_balance = tutor_utils()->tutor_price($earning_sum->balance);
		$formatted_min_withdraw_amount = tutor_utils()->tutor_price($min_withdraw);


		if ( ! tutor_utils()->count($saved_withdraw_account)){
			$no_withdraw_method = apply_filters('tutor_no_withdraw_method_msg', __('Please save withdraw method ', 'tutor')  );
			wp_send_json_error(array('msg' => $no_withdraw_method ));
		}

		if ((!is_numeric($withdraw_amount) && !is_float($withdraw_amount)) || $withdraw_amount < $min_withdraw){
			$required_min_withdraw = apply_filters('tutor_required_min_amount_msg', sprintf(__('Minimum withdrawal amount is %s %s %s ', 'tutor') , '<strong>', $formatted_min_withdraw_amount, '</strong>' ) );
			wp_send_json_error(array('msg' => $required_min_withdraw ));
		}

		if ($earning_sum->balance < $withdraw_amount){
			$insufficient_balence = apply_filters('tutor_withdraw_insufficient_balance_msg', __('Insufficient balance.', 'tutor'));

			wp_send_json_error(array('msg' => $insufficient_balence ));
		}


        $date = date("Y-m-d H:i:s", tutor_time());

		$withdraw_data = apply_filters('tutor_pre_withdraw_data', array(
			'user_id'       => $user_id,
			'amount'        => $withdraw_amount,
			'method_data'   => maybe_serialize($saved_withdraw_account),
			'status'        => 'pending',
			'created_at'    => $date,
		));

		do_action('tutor_insert_withdraw_before', $withdraw_data);

		$wpdb->insert($wpdb->prefix."tutor_withdraws", $withdraw_data);
		$withdraw_id = $wpdb->insert_id;

		do_action('tutor_insert_withdraw_after', $withdraw_id, $withdraw_data);


		/**
		 * Getting earning and balance data again
		 */
		$earning = tutor_utils()->get_earning_sum();
		$new_available_balance = tutor_utils()->tutor_price($earning->balance);


		do_action('tutor_withdraw_after');

		$withdraw_successfull_msg = apply_filters('tutor_withdraw_successful_msg', __('Withdrawal Request Sent!', 'tutor'));
		wp_send_json_success(array('msg' => $withdraw_successfull_msg, 'available_balance' => $new_available_balance ));
	}



}
