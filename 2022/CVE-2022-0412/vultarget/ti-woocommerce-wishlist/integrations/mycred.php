<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name myCRED
 *
 * @version 2.1.1
 *
 * @slug mycred
 *
 * @url https://wordpress.org/plugins/mycred/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "mycred";

$name = "myCRED";

$available = defined('myCRED_VERSION');

$tinvwl_integrations = is_array($tinvwl_integrations) ? $tinvwl_integrations : [];

$tinvwl_integrations[$slug] = array(
	'name' => $name,
	'available' => $available,
);

if (!tinv_get_option('integrations', $slug)) {
	return;
}

if (!$available) {
	return;
}

// myCred hooks
if (defined('myCRED_VERSION')) {

	/**
	 * Register Hook
	 */
	add_filter('mycred_setup_hooks', 'tinvwl_mycred_register_ti_woocommerce_wishlist_hook', 100);
	function tinvwl_mycred_register_ti_woocommerce_wishlist_hook($installed)
	{

		$installed['tinvwl'] = array(
			'title' => __('WooCommerce Wishlist', 'ti-woocommerce-wishlist'),
			'description' => __('Awards %_plural% for users adding products to their wishlist and purchased products from their wishlist.', 'ti-woocommerce-wishlist'),
			'callback' => array('myCRED_Hook_TinvWL'),
		);

		return $installed;

	}

	/**
	 * TI WooCommerce Wihslist Hook
	 */
	add_action('mycred_load_hooks', 'tinvwl_mycred_load_ti_woocommerce_wishlist_hook', 100);
	function tinvwl_mycred_load_ti_woocommerce_wishlist_hook()
	{

		// If the hook has been replaced or if plugin is not installed, exit now
		if (class_exists('myCRED_Hook_TinvWL')) {
			return;
		}

		class myCRED_Hook_TinvWL extends myCRED_Hook
		{

			/**
			 * Construct
			 */
			public function __construct($hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY)
			{

				parent::__construct(array(
					'id' => 'tinvwl',
					'defaults' => array(
						'tinvwl_added' => array(
							'creds' => 1,
							'log' => '%plural% for adding a product to a wishlist',
							'limit' => '0/x',
						),
						'tinvwl_purchased' => array(
							'creds' => 1,
							'log' => '%plural% for purchasing a product from a wishlist',
							'limit' => '0/x',
						),
					),
				), $hook_prefs, $type);

			}

			/**
			 * Run
			 */
			public function run()
			{
				add_action('tinvwl_product_added', array($this, 'added'));
				add_action('tinvwl_product_purchased', array($this, 'purchased'), 10, 3);
			}

			/**
			 * Added product to a wishlist
			 *
			 * @param array $data product data including author and wishlist IDs.
			 */
			public function added($data)
			{

				// Must be logged in
				if (!is_user_logged_in()) {
					return;
				}

				$user_id = get_current_user_id();

				// Award the user adding to wishlist
				if ($this->prefs['tinvwl_added']['creds'] != 0 && !$this->core->exclude_user($user_id)) {

					// Limit
					if (!$this->over_hook_limit('tinvwl_added', 'added_to_wishlist', $user_id)) {

						// Make sure this is unique event
						if (!$this->core->has_entry('added_to_wishlist', $data['product_id'], $user_id)) {

							// Execute
							$this->core->add_creds(
								'added_to_wishlist',
								$user_id,
								$this->prefs['tinvwl_added']['creds'],
								$this->prefs['tinvwl_added']['log'],
								$data['product_id'],
								array('ref_type' => 'post'),
								$this->mycred_type
							);

						}

					}

				}
			}

			/**
			 * Purchased product from a wishlist
			 *
			 * @param WC_order $order Order object.
			 * @param WC_Order_Item_Product $item Order item product object.
			 * @param array $wishlist A wishlist data where product added from.
			 */
			public function purchased($order, $item, $wishlist)
			{

				// Must be logged in
				if (!is_user_logged_in()) {
					return;
				}

				$user_id = get_current_user_id();

				// Award the user adding to wishlist
				if ($this->prefs['tinvwl_purchased']['creds'] != 0 && !$this->core->exclude_user($user_id)) {

					// Limit
					if (!$this->over_hook_limit('tinvwl_purchased', 'purchased_from_wishlist', $user_id)) {

						// Make sure this is unique event
						if (!$this->core->has_entry('purchased_from_wishlist', $item->get_id(), $user_id)) {

							// Execute
							$this->core->add_creds(
								'purchased_from_wishlist',
								$user_id,
								$this->prefs['tinvwl_purchased']['creds'],
								$this->prefs['tinvwl_purchased']['log'],
								$item->get_id(),
								array('ref_type' => 'post'),
								$this->mycred_type
							);

						}

					}

				}

			}

			/**
			 * Preferences
			 */
			public function preferences()
			{

				$prefs = $this->prefs;

				?>
				<div class="hook-instance">
					<h3><?php _e('Adding Product to Wishlist', 'ti-woocommerce-wishlist'); ?></h3>
					<div class="row">
						<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
							<div class="form-group">
								<label
									for="<?php echo $this->field_id(array('tinvwl_added' => 'creds')); ?>"><?php _e('Points', 'ti-woocommerce-wishlist'); ?></label>
								<input type="text"
									   name="<?php echo $this->field_name(array('tinvwl_added' => 'creds')); ?>"
									   id="<?php echo $this->field_id(array('tinvwl_added' => 'creds')); ?>"
									   value="<?php echo $this->core->number($prefs['tinvwl_added']['creds']); ?>"
									   class="form-control"/>
							</div>
						</div>
						<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $this->field_id(array(
									'tinvwl_added',
									'limit',
								)); ?>"><?php _e('Limit', 'ti-woocommerce-wishlist'); ?></label>
								<?php echo $this->hook_limit_setting($this->field_name(array(
									'tinvwl_added',
									'limit',
								)), $this->field_id(array(
									'tinvwl_added',
									'limit',
								)), $prefs['tinvwl_added']['limit']); ?>
							</div>
						</div>
						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label
									for="<?php echo $this->field_id(array('tinvwl_added' => 'log')); ?>"><?php _e('Log Template', 'ti-woocommerce-wishlist'); ?></label>
								<input type="text"
									   name="<?php echo $this->field_name(array('tinvwl_added' => 'log')); ?>"
									   id="<?php echo $this->field_id(array('tinvwl_added' => 'log')); ?>"
									   placeholder="<?php _e('required', 'ti-woocommerce-wishlist'); ?>"
									   value="<?php echo esc_attr($prefs['tinvwl_added']['log']); ?>"
									   class="form-control"/>
								<span class="description"><?php echo $this->available_template_tags(array(
										'general',
										'post',
									)); ?></span>
							</div>
						</div>
					</div>
					<h3><?php _e('Purchasing Product from Wishlist', 'ti-woocommerce-wishlist'); ?></h3>
					<div class="row">
						<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
							<div class="form-group">
								<label
									for="<?php echo $this->field_id(array('tinvwl_purchased' => 'creds')); ?>"><?php _e('Points', 'ti-woocommerce-wishlist'); ?></label>
								<input type="text"
									   name="<?php echo $this->field_name(array('tinvwl_purchased' => 'creds')); ?>"
									   id="<?php echo $this->field_id(array('tinvwl_purchased' => 'creds')); ?>"
									   value="<?php echo $this->core->number($prefs['tinvwl_purchased']['creds']); ?>"
									   class="form-control"/>
							</div>
						</div>
						<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
							<div class="form-group">
								<label for="<?php echo $this->field_id(array(
									'tinvwl_purchased',
									'limit',
								)); ?>"><?php _e('Limit', 'ti-woocommerce-wishlist'); ?></label>
								<?php echo $this->hook_limit_setting($this->field_name(array(
									'tinvwl_purchased',
									'limit',
								)), $this->field_id(array(
									'tinvwl_purchased',
									'limit',
								)), $prefs['tinvwl_purchased']['limit']); ?>
							</div>
						</div>
						<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<label
									for="<?php echo $this->field_id(array('tinvwl_purchased' => 'log')); ?>"><?php _e('Log Template', 'ti-woocommerce-wishlist'); ?></label>
								<input type="text"
									   name="<?php echo $this->field_name(array('tinvwl_purchased' => 'log')); ?>"
									   id="<?php echo $this->field_id(array('tinvwl_purchased' => 'log')); ?>"
									   placeholder="<?php _e('required', 'ti-woocommerce-wishlist'); ?>"
									   value="<?php echo esc_attr($prefs['tinvwl_purchased']['log']); ?>"
									   class="form-control"/>
								<span class="description"><?php echo $this->available_template_tags(array(
										'general',
										'post',
									)); ?></span>
							</div>
						</div>
					</div>
				</div>

				<?php

			}

			/**
			 * Sanitise Preferences
			 */
			public function sanitise_preferences($data)
			{

				if (isset($data['tinvwl_added']['limit']) && isset($data['tinvwl_added']['limit_by'])) {
					$limit = sanitize_text_field($data['tinvwl_added']['limit']);
					if ($limit == '') {
						$limit = 0;
					}
					$data['tinvwl_added']['limit'] = $limit . '/' . $data['tinvwl_added']['limit_by'];
					unset($data['tinvwl_added']['limit_by']);
				}

				if (isset($data['tinvwl_purchased']['limit']) && isset($data['tinvwl_purchased']['limit_by'])) {
					$limit = sanitize_text_field($data['tinvwl_purchased']['limit']);
					if ($limit == '') {
						$limit = 0;
					}
					$data['tinvwl_purchased']['limit'] = $limit . '/' . $data['tinvwl_purchased']['limit_by'];
					unset($data['tinvwl_purchased']['limit_by']);
				}

				return $data;

			}

		}

	}

	add_filter('mycred_all_references', 'tinvwl_mycred_references');

	function tinvwl_mycred_references($references)
	{

		$references['purchased_from_wishlist'] = __('Purchased From Wishlist', 'ti-woocommerce-wishlist');
		$references['added_to_wishlist'] = __('Added To Wishlist', 'ti-woocommerce-wishlist');

		return $references;
	}
}
