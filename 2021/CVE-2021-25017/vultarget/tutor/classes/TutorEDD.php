<?php

/**
 * Tutor Course attachments Main Class
 */

namespace TUTOR;

if (!defined('ABSPATH'))
	exit;

class TutorEDD extends Tutor_Base {

	public function __construct() {
		parent::__construct();

		//add_action('tutor_options_before_tutor_edd', array($this, 'notice_before_option'));

		//Add Tutor Option
		add_filter('tutor_monetization_options', array($this, 'tutor_monetization_options'));
		//add_filter('tutor/options/attr', array($this, 'add_options'));

		$monetize_by = tutils()->get_option('monetize_by');

		if ($monetize_by !== 'edd') {
			return;
		}

		add_action('add_meta_boxes', array($this, 'register_meta_box'));
		add_action('save_post_' . $this->course_post_type, array($this, 'save_course_meta'));

		/**
		 * Is Course Purchasable
		 */
		add_filter('is_course_purchasable', array($this, 'is_course_purchasable'), 10, 2);
		add_filter('get_tutor_course_price', array($this, 'get_tutor_course_price'), 10, 2);
		add_filter('tutor_course_sell_by', array($this, 'tutor_course_sell_by'));

		add_action('edd_update_payment_status', array($this, 'edd_update_payment_status'), 10, 3);
	}

	public function notice_before_option() {
		$has_edd = tutor_utils()->has_edd();
		if ($has_edd) {
			return;
		}

		ob_start();
		?>
		<div class="tutor-notice-warning">
			<p>
				<?php _e(' Seems like you don’t have <strong>Easy Digital Downloads</strong> plugin installed on your site. In order to use this functionality, you need to have the  <strong>Easy Digital Downloads</strong> plugin installed. Get back on this page after installing the plugin and enable the following feature to start selling courses with Tutor.', 'tutor'); ?>
			</p>
			<p><?php _e('This notice will disappear after activating <strong>EDD</strong>', 'tutor'); ?></p>
		</div>
		<?php
		echo ob_get_clean();
	}

	/**
	 * @param $attr
	 *
	 * @return mixed
	 *
	 * Add Option for tutor
	 */
	public function add_options($attr) {
		$attr['tutor_edd'] = array(
			'label' => __('EDD', 'tutor-edd'),

			'sections'    => array(
				'general' => array(
					'label' => __('General', 'tutor-edd'),
					'desc' => __('Tutor Course Attachments Settings', 'tutor-edd'),
					'fields' => array(
						'enable_tutor_edd' => array(
							'type'          => 'checkbox',
							'label'         => __('Enable EDD', 'tutor'),
							'desc'          => __('This will enable sell your product via EDD',	'tutor'),
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
		$has_edd = tutils()->has_edd();
		if ($has_edd) {
			$arr['edd'] = __('Easy Digital Downloads', 'tutor');
		}
		return $arr;
	}

	public function register_meta_box() {
		add_meta_box('tutor-attached-edd-product', __('Add Product', 'tutor'), array($this, 'course_add_product_metabox'), $this->course_post_type, 'advanced', 'high');
	}

	/**
	 * @param $post
	 * MetaBox for Lesson Modal Edit Mode
	 */
	public function course_add_product_metabox() {
		include  tutor()->path . 'views/metabox/course-add-edd-product-metabox.php';
	}

	public function save_course_meta($post_ID) {
		$product_id = tutor_utils()->avalue_dot('_tutor_course_product_id', $_POST);

		if ($product_id !== '-1') {
			$product_id = (int) $product_id;
			if ($product_id) {
				update_post_meta($post_ID, '_tutor_course_product_id', $product_id);
				update_post_meta($product_id, '_tutor_product', 'yes');
			}
		} else {
			delete_post_meta($post_ID, '_tutor_course_product_id');
		}
	}

	public function is_course_purchasable($bool, $course_id) {
		if (!tutor_utils()->has_edd()) {
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
		$product_id = tutor_utils()->get_course_product_id($course_id);

		return edd_price($product_id, false);
	}

	public function tutor_course_sell_by() {
		return 'edd';
	}

	public function edd_update_payment_status($payment_id, $new_status, $old_status) {
		if ($new_status !== 'publish') {
			return;
		}

		$payment = new \EDD_Payment($payment_id);
		$cart_details   = $payment->cart_details;
		$user_id        = $payment->user_info['id'];

		if (is_array($cart_details)) {
			foreach ($cart_details as $cart_index => $download) {

				$if_has_course = tutor_utils()->product_belongs_with_course($download['id']);
				if ($if_has_course) {
					$course_id = $if_has_course->post_id;
					$has_any_enrolled = tutor_utils()->has_any_enrolled($course_id, $user_id);
					if (!$has_any_enrolled) {
						tutor_utils()->do_enroll($course_id, $payment_id, $user_id);
					}
				}
			}
			tutor_utils()->complete_course_enroll($payment_id);
		}
	}
}
