<?php

/**
 * Created by PhpStorm.
 * User: themeum
 * Date: 1/10/18
 * Time: 3:01 PM
 */

namespace TUTOR;

if (!defined('ABSPATH'))
	exit;

class WooCommerce extends Tutor_Base {

	public function __construct() {
		parent::__construct();

		add_action('tutor_options_before_woocommerce', array($this, 'notice_before_option'));

		//Add option settings
		add_filter('tutor_monetization_options', array($this, 'tutor_monetization_options'));
		add_filter('tutor/options/attr', array($this, 'add_options'));

		$monetize_by = tutils()->get_option('monetize_by');
		if ($monetize_by !== 'wc') {
			return;
		}

		/**
		 * Is Course Purchasable
		 */
		add_filter('is_course_purchasable', array($this, 'is_course_purchasable'), 10, 2);
		add_filter('get_tutor_course_price', array($this, 'get_tutor_course_price'), 10, 2);
		add_filter('tutor_course_sell_by', array($this, 'tutor_course_sell_by'));

		add_filter('product_type_options', array($this, 'add_tutor_type_in_wc_product'));

		add_action('add_meta_boxes', array($this, 'register_meta_box'));
		add_action('save_post_' . $this->course_post_type, array($this, 'save_course_meta'));
		add_action('save_post_product', array($this, 'save_wc_product_meta'));

		add_action('tutor_course/single/before/enroll', 'wc_print_notices');

		/**
		 * After place new order
		 */
		add_action('woocommerce_new_order', array($this, 'course_placing_order_from_admin'), 10, 3);
		add_action('woocommerce_new_order_item', array($this, 'course_placing_order_from_customer'), 10, 3);

		/**
		 * Order Status Hook
		 *
		 * Remove course from active courses if an order is cancelled or refunded
		 */
		add_action('woocommerce_order_status_changed', array($this, 'enrolled_courses_status_change'), 10, 3);

		/**
		 * Add Earning Data
		 */
		add_action('woocommerce_new_order_item', array($this, 'add_earning_data'), 10, 3);
		add_action('woocommerce_order_status_changed', array($this, 'add_earning_data_status_change'), 10, 3);

		/**
		 * WC Print Notices After Enroll
		 * @since v.1.3.5
		 */
		if (tutils()->has_wc()) {
			add_action('tutor_course/single/before/inner-wrap', 'wc_print_notices', 10);
			add_action('tutor_course/single/enrolled/before/inner-wrap', 'wc_print_notices', 10);
		}

		/**
		 * Manage WooCommerce plugin dependency
		 * @since v.1.7.8
		 */
		$woocommerce_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR .  'woocommerce' . DIRECTORY_SEPARATOR . 'woocommerce.php';
		register_deactivation_hook( $woocommerce_path, array($this, 'disable_tutor_monetization') );
		/**
		 * Redirect student on enrolled courses after course 
		 * enrollment complete
		 * @since 1.9.0
		*/			
		add_action( 'woocommerce_thankyou', array($this, 'redirect_to_enrolled_courses') );	
		
		/**
		 * Change woo commerce cart product link if it is tutor product
		 */
		add_filter( 'woocommerce_cart_item_permalink', array( $this, 'tutor_update_product_url' ), 10, 2 );
	}

	public function notice_before_option() {
		$has_wc = tutor_utils()->has_wc();
		if ($has_wc) {
			return;
		}

		ob_start();
		?>
		<div class="tutor-notice-warning">
			<p>
				<?php _e(' Seems like you don’t have WooCommerce plugin installed on your site. In order to use this functionality, you need to have the 
                WooCommerce plugin installed. Get back on this page after installing the plugin and enable the following feature to start selling 
                courses with Tutor.', 'tutor'); ?>
			</p>
			<p><?php _e('This notice will disappear after activating <strong>WooCommerce</strong>', 'tutor'); ?></p>
		</div>
		<?php
		echo ob_get_clean();
	}

	public function is_course_purchasable($bool, $course_id) {
		if (!tutor_utils()->has_wc()) {
			return false;
		}

		$course_id = tutor_utils()->get_post_id($course_id);
		$has_product_id = get_post_meta($course_id, '_tutor_course_product_id', true);
		if ($has_product_id) {
			return true;
		}
		return false;
	}

	public function get_tutor_course_price($price, $course_id) {
		$price = null;

		if (tutor_utils()->is_course_purchasable($course_id)) {
			if (tutor_utils()->has_wc()) {
				$product_id = tutor_utils()->get_course_product_id($course_id);
				$product    = wc_get_product($product_id);

				if ($product) {
					ob_start();
					?>
					<div class="price">
						<?php echo $product->get_price_html(); ?>
					</div>
					<?php
					return ob_get_clean();
				}
			}
		}

		return $price;
	}

	public function tutor_course_sell_by() {
		return 'woocommerce';
	}

	public function add_tutor_type_in_wc_product($types) {
		$types['tutor_product'] =  array(
			'id'            => '_tutor_product',
			'wrapper_class' => 'show_if_simple',
			'label'         => __('For Tutor', 'tutor'),
			'description'   => __('This checkmark ensure that you will sell a specif course via this product.', 'tutor'),
			'default'       => 'no',
		);

		return $types;
	}

	public function register_meta_box() {
		add_meta_box('tutor-attach-product', __('Add Product', 'tutor'), array($this, 'course_add_product_metabox'), $this->course_post_type, 'advanced', 'high');
	}

	public function course_add_product_metabox() {
		include  tutor()->path . 'views/metabox/course-add-product-metabox.php';
	}

	/**
	 * @param $post_ID
	 *
	 * Save course meta for attaching product
	 */
	public function save_course_meta($post_ID) {
		$product_id = (int)sanitize_text_field( tutor_utils()->avalue_dot('_tutor_course_product_id', $_POST, 0) ) ;

		if ($product_id === -1) {
			delete_post_meta($post_ID, '_tutor_course_product_id');
		} else if ($product_id) {
			update_post_meta($post_ID, '_tutor_course_product_id', $product_id);
			//Mark product for woocommerce
			update_post_meta($product_id, '_virtual', 'yes');
			update_post_meta($product_id, '_tutor_product', 'yes');
		}
	}

	public function save_wc_product_meta($post_ID) {
		$is_tutor_product = sanitize_text_field( tutor_utils()->avalue_dot('_tutor_product', $_POST) );
		if ($is_tutor_product === 'on') {
			update_post_meta($post_ID, '_tutor_product', 'yes');
		} else {
			delete_post_meta($post_ID, '_tutor_product');
		}
	}

	/**
	 *
	 * Take enrolled course action based on order status change
	 */
	public function enrolled_courses_status_change($order_id, $status_from, $status_to) {
		if (!tutor_utils()->is_tutor_order($order_id)) {
			return;
		}
		global $wpdb;

		$enrolled_ids_with_course = $this->get_course_enrolled_ids_by_order_id($order_id);

		if ($enrolled_ids_with_course) {
			$enrolled_ids = wp_list_pluck($enrolled_ids_with_course, 'enrolled_id');

			if (is_array($enrolled_ids) && count($enrolled_ids)) {
				foreach ($enrolled_ids as $enrolled_id) {

					tutils()->course_enrol_status_change($enrolled_id, $status_to);

					// Invoke enrolled hook
					if($status_to == 'completed'){
						$user_id = get_post_field('post_author', $enrolled_id);
						$course_id = get_post_field('post_parent', $enrolled_id);
						do_action('tutor_after_enrolled', $course_id, $user_id, $enrolled_id);
					}
				}
			}
		}
	}

	/**
	 * @param $order_id
	 *
	 * @return array|bool
	 */
	public function get_course_enrolled_ids_by_order_id($order_id) {
		global $wpdb;
		//Getting all of courses ids within this order

		$courses_ids = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE '_tutor_order_for_course_id_%' ", $order_id));

		if (is_array($courses_ids) && count($courses_ids)) {
			$course_enrolled_by_order = array();
			foreach ($courses_ids as $courses_id) {
				$course_id = str_replace('_tutor_order_for_course_id_', '', $courses_id->meta_key);
				//array(order_id =>  array('course_id' => $course_id, 'enrolled_id' => enrolled_id, 'order_id' => $courses_id->post_id))
				$course_enrolled_by_order[] = array('course_id' => $course_id, 'enrolled_id' => $courses_id->meta_value, 'order_id' => $courses_id->post_id);
			}
			return $course_enrolled_by_order;
		}
		return false;
	}

	/**
	 * Remove course
	 *
	 * TODO: right now it's unused
	 */
	public function remove_active_course($order_id) {
		global $wpdb;
		//Getting all of courses ids within this order

		$courses_ids = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d meta_key LIKE '_tutor_order_for_course_id_%' ", $order_id));
	}


	/**
	 * @param $attr
	 *
	 * @return mixed
	 *
	 * Add option for WooCommerce settings
	 */
	public function add_options($attr) {

		$attr['woocommerce'] = array(
			'label' => __('WooCommerce', 'tutor'),

			'sections'    => array(
				'general' => array(
					'label' => __('General', 'tutor'),
					'desc' => __('WooCommerce Settings', 'tutor'),
					'fields' => array(
						/*'enable_course_sell_by_woocommerce' => array(
							'type'      => 'checkbox',
							'label'     => __('Enable / Disable', 'tutor'),
							'label_title'   => __('Enable WooComerce to sell course', 'tutor'),
							'desc'      => __('By integrating WooCommerce, you can sell your course',	'tutor'),
						),*/
						'enable_guest_course_cart' => array(
							'type'      => 'checkbox',
							'label'     => __('Enable / Disable', 'tutor'),
							'label_title'   => __('Enable add to cart feature for guest users', 'tutor'),
							'desc'      => __('Enabling this will let an unregistered user purchase any course from the Course Details page. Head over to Documentation to know how to configure this setting.',	'tutor'),
						),
					),
				),
			),
		);

		return $attr;
	}

	/**
	 * @param $arr
	 *
	 * @return mixed
	 *
	 * Returning monetization options
	 *
	 * @since v.1.3.5
	 */
	public function tutor_monetization_options($arr) {
		$has_wc = tutils()->has_wc();
		if ($has_wc) {
			$arr['wc'] = __('WooCommerce', 'tutor');
		}
		return $arr;
	}

	/**
	 * @param $item_id
	 * @param $item
	 * @param $order_id
	 *
	 * Adding Earning Data processing WooCommerce
	 *
	 * @since v.1.1.2
	 */
	public function add_earning_data($item_id, $item, $order_id) {
		global $wpdb;
		$item = new \WC_Order_Item_Product($item);

		$product_id = $item->get_product_id();
		$if_has_course = tutor_utils()->product_belongs_with_course($product_id);

		if ($if_has_course) {

			$enable_tutor_earning = tutor_utils()->get_option('enable_tutor_earning');
			if (!$enable_tutor_earning) {
				return;
			}

			$course_id = $if_has_course->post_id;
			$user_id = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM {$wpdb->posts} WHERE ID = %d ", $course_id));
			$order_status = $wpdb->get_var($wpdb->prepare("SELECT post_status from {$wpdb->posts} where ID = %d ", $order_id));

			/**
			 * Return here if already added this product from this order
			 * @since v1.9.7
			 */
			$exist_count = (int)$wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(earning_id) 
				FROM {$wpdb->prefix}tutor_earnings 
				WHERE course_id=%d 
					AND order_id=%d 
					AND user_id=%d",
				$course_id,
				$order_id,
				$user_id
			));

			if($exist_count>0) {
				return;
			}

			$total_price = $item->get_total();

			$fees_deduct_data = array();
			$tutor_earning_fees = tutor_utils()->get_option('tutor_earning_fees');
			$enable_fees_deducting = tutor_utils()->avalue_dot('enable_fees_deducting', $tutor_earning_fees);

			$course_price_grand_total = $total_price;

			if ($enable_fees_deducting) {
				$fees_name = tutor_utils()->avalue_dot('fees_name', $tutor_earning_fees);
				$fees_amount = (int) tutor_utils()->avalue_dot('fees_amount', $tutor_earning_fees);
				$fees_type = tutor_utils()->avalue_dot('fees_type', $tutor_earning_fees);

				if ($fees_amount > 0) {
					if ($fees_type === 'percent') {
						$fees_amount = ($total_price * $fees_amount) / 100;
					}

					/*
					if ( $fees_type === 'fixed' ) {
						$course_price_grand_total = $total_price - $fees_amount;
					}*/

					$course_price_grand_total = $total_price - $fees_amount;
				}

				$fees_deduct_data = array(
					'deduct_fees_amount'    => $fees_amount,
					'deduct_fees_name'      => $fees_name,
					'deduct_fees_type'      => $fees_type,
				);
			}

			$instructor_rate = tutor_utils()->get_option('earning_instructor_commission');
			$admin_rate = tutor_utils()->get_option('earning_admin_commission');

			$instructor_amount = 0;
			if ($instructor_rate > 0) {
				$instructor_amount = ($course_price_grand_total * $instructor_rate) / 100;
			}

			$admin_amount = 0;
			if ($admin_rate > 0) {
				$admin_amount = ($course_price_grand_total * $admin_rate) / 100;
			}

			$commission_type = 'percent';

			// (Use Pro Filter - Start)
			// The response must be same array structure.
			// Do not change used variable names here, or change in both of here and pro plugin
			$pro_arg = [
				'user_id' => $user_id,
				'instructor_rate' => $instructor_rate,
				'admin_rate' => $admin_rate,
				'instructor_amount' => $instructor_amount,
				'admin_amount' => $admin_amount,
				'course_price_grand_total'  => $course_price_grand_total,
				'commission_type' => $commission_type
			];
			$pro_calculation = apply_filters('tutor_pro_earning_calculator', $pro_arg);
			extract($pro_calculation);
			// (Use Pro Filter - End) 

			$earning_data = array(
				'user_id'                   => $user_id,
				'course_id'                 => $course_id,
				'order_id'                  => $order_id,
				'order_status'              => $order_status,
				'course_price_total'        => $total_price,
				'course_price_grand_total'  => $course_price_grand_total,

				'instructor_amount'         => $instructor_amount,
				'instructor_rate'           => $instructor_rate,
				'admin_amount'              => $admin_amount,
				'admin_rate'                => $admin_rate,

				'commission_type'           => $commission_type,
				'process_by'                => 'woocommerce',
				'created_at'                => date('Y-m-d H:i:s', tutor_time()),
			);
			$earning_data = apply_filters('tutor_new_earning_data', array_merge($earning_data, $fees_deduct_data));

			$wpdb->insert($wpdb->prefix . 'tutor_earnings', $earning_data);
		}
	}

	/**
	 * @param $order_id
	 * @param $status_from
	 * @param $status_to
	 *
	 * Change Earning data status
	 *
	 * @since v.1.1.2
	 */
	public function add_earning_data_status_change($order_id, $status_from, $status_to) {
		if (!tutor_utils()->is_tutor_order($order_id)) {
			return;
		}
		global $wpdb;

		$is_earning_data = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(earning_id) FROM {$wpdb->prefix}tutor_earnings WHERE order_id = %d  ", $order_id));
		if ($is_earning_data) {
			$wpdb->update($wpdb->prefix . 'tutor_earnings', array('order_status' => $status_to), array('order_id' => $order_id));
		}
	}

	/**
	 * Course placing order from admin
	 * 
	 * @param $order_id
	 * @since v.1.6.7
	 */
	public function course_placing_order_from_admin($order_id) {
		if (!is_admin()) {
			return;
		}
		
		$order = wc_get_order( $order_id );
		foreach ($order->get_items() as $item) {
			$product_id = $item->get_product_id();
			$if_has_course = tutor_utils()->product_belongs_with_course($product_id);
			if ($if_has_course) {
				$course_id = $if_has_course->post_id;
				$customer_id = $order->get_customer_id();
				tutor_utils()->do_enroll($course_id, $order_id, $customer_id);
			}
		}
	}

	/**
	 * Course placing order from customer
	 *
	 * @param $order_id
	 * @since v.1.6.7
	 */
	public function course_placing_order_from_customer($item_id, $item, $order_id) {
		if (is_admin()) {
			return;
		}

		$item = new \WC_Order_Item_Product($item);
		$product_id = $item->get_product_id();
		$if_has_course = tutor_utils()->product_belongs_with_course($product_id);

		if ($if_has_course){
			$course_id = $if_has_course->post_id;
			tutor_utils()->do_enroll($course_id, $order_id);		
		}
	}

	/**
	 * Disable course monetization on woocommerce deactivation
	 * @since v.1.7.8
	 */
	public function disable_tutor_monetization() {
		tutils()->update_option('monetize_by', 'free');
		update_option('tutor_show_woocommerce_notice', true);
	}

	/**
	 * Redirect student on enrolled courses after course 
	 * enrollment complete if course is purchasable
	 * @param $order_id | int
	 * @since 1.9.0
	*/
	public function redirect_to_enrolled_courses( $order_id ) {
			if(!tutils()->get_option( 'wc_automatic_order_complete_redirect_to_courses' )) {
				// Since 1.9.1
				return;
			}

			//get woo order details
		    $order 			= wc_get_order( $order_id );
		    $tutor_product 	= false;
		    $url = tutor_utils()->tutor_dashboard_url().'enrolled-courses/';
		    foreach ($order->get_items() as $item) {
				$product_id = $item->get_product_id();
				//check if product associated with tutor course
				$if_has_course = tutor_utils()->product_belongs_with_course($product_id);
				if( $if_has_course ) {
					$tutor_product = true;
				}
			}
			//if tutor product & order status completed
		    if ( $order->has_status( 'completed' ) && $tutor_product ) {
		        wp_safe_redirect( $url );
		        exit;
		    }
	}

	/**
	 * Change product url on cart page if product is tutor course
	 * 
	 * @since 1.9.8
	 */
	function tutor_update_product_url( $permalink, $cart_item ) {

		$woo_product_id = $cart_item['product_id']; 
		$product_meta 	= get_post_meta( $woo_product_id );

		if( isset( $product_meta['_tutor_product'] ) && $product_meta['_tutor_product'][0] ) {

			global $wpdb; 
			$table 			= $wpdb->base_prefix . 'postmeta';
			$post_id 		= $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$table} WHERE meta_key = '_tutor_course_product_id' AND meta_value = %d ", $woo_product_id));

			if( $post_id ) {
				$data =  get_post_permalink( $post_id );
				return $data; 
			}
		}
	}
}


add_action('admin_notices', function() {

	$show = get_option( 'tutor_show_woocommerce_notice' ) && tutils()->get_option('monetize_by', 'free')=='free';

	if($show) {
		$message = __('Since WooCommerce is disabled, your monetized courses have been set to free. Please make sure to enable Tutor LMS monetization if you decide to re-enable WooCommerce.', 'tutor');
		echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
	}
});