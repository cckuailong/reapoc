<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Gravity Forms Product Add-Ons
 *
 * @version 3.3.19
 *
 * @slug woocommerce-gravityforms-product-addons
 *
 * @url https://woocommerce.com/products/gravity-forms-add-ons/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-gravityforms-product-addons";

$name = "WooCommerce Gravity Forms Product Add-Ons";

$available = class_exists('WC_GFPA_Main');

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

if (!function_exists('tinv_wishlist_metasupport_wc_gf_addons')) {

	/**
	 * Set description for meta WooCommerce - Gravity Forms Product Add-Ons
	 *
	 * @param array $meta Meta array.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_wc_gf_addons($meta)
	{
		if (array_key_exists('wc_gforms_form_id', $meta) && class_exists('RGFormsModel')) {
			$form_meta = RGFormsModel::get_form_meta($meta['wc_gforms_form_id']['display']);

			if (array_key_exists('fields', $form_meta)) {
				$_meta = array();
				foreach ($form_meta['fields'] as $field) {

					if (isset($field['displayOnly']) && $field['displayOnly']) {
						continue;
					}

					$field_name = $field->get_first_input_id(array('id' => 0));

					if (array_key_exists($field_name, $meta)) {

						if ($field['type'] == 'product') {
							$inputs = $field instanceof GF_Field ? $field->get_entry_inputs() : rgar($field, 'inputs');
							if (is_array($inputs)) {
								$value = array();
								foreach ($inputs as $input) {
									$full_name = 'input_' . str_replace('.', '_', $input['id']);
									if ($field_name == $full_name) {
										continue;
									}
									if (array_key_exists($full_name, $meta)) {
										$value[] = $field->inputs[array_search($input['id'], array_column($field->inputs, 'id'))]['label'] . ': ' . $meta[$full_name]['display'];
									}
								}

								$meta[$field_name]['display'] = (is_array($value)) ? implode(', ', $value) : '-';
							}
						}

						$meta[$field_name]['key'] = $field->label;
						$_meta[$field_name] = $meta[$field_name];

					}
				}
				$meta = $_meta;
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_wc_gf_addons');
}

if (!function_exists('tinvwl_wishlist_item_url_wc_gf_addons')) {

	function tinvwl_wishlist_item_url_wc_gf_addons($url, $wl_product, $product)
	{

		$gravity_form_data = wc_gfpa()->get_gravity_form_data($product->get_id());

		if (isset($gravity_form_data['enable_cart_edit']) && $gravity_form_data['enable_cart_edit'] !== 'no') {
			$url = add_query_arg(array('wc_gforms_wishlist_product_id' => $wl_product['ID']), $product->get_permalink());
		}

		return $url;
	}

	add_filter('tinvwl_wishlist_item_url', 'tinvwl_wishlist_item_url_wc_gf_addons', 10, 3);
}

if (!function_exists('tinvwl_item_price_wc_gf_addons')) {

	/**
	 * Modify price for WooCommerce - Gravity Forms Product Add-Ons
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_wc_gf_addons($price, $wl_product, $product)
	{
		if (array_key_exists('wc_gforms_form_id', $wl_product['meta']) && class_exists('RGFormsModel')) {
			$form = RGFormsModel::get_form_meta($wl_product['meta']['wc_gforms_form_id']);

			if (empty($form)) {
				return $price;
			}

			$lead = array(
				'form_id' => $wl_product['meta']['wc_gforms_form_id']
			);
			$lead['id'] = uniqid() . time() . rand();

			foreach ($wl_product['meta'] as $key => $meta) {
				if (strpos($key, 'input_') === 0) {
					$lead[str_replace('_', '.', substr($key, strlen('input_')))] = $meta;
				}
			}

			$use_choice_text = false;
			$use_admin_label = false;
			$products = array();
			$total = 0;

			foreach ($form["fields"] as $field) {
				$id = $field["id"];
				$lead_value = RGFormsModel::get_lead_field_value($lead, $field);

				$quantity_field = GFCommon::get_product_fields_by_type($form, array("quantity"), $id);
				$quantity = sizeof($quantity_field) > 0 ? RGFormsModel::get_lead_field_value($lead, $quantity_field[0]) : 1;

				switch ($field["type"]) {

					case "product" :

						//if single product, get values from the multiple inputs
						if (is_array($lead_value)) {
							$product_quantity = sizeof($quantity_field) == 0 && !rgar($field, "disableQuantity") ? rgget($id . ".3", $lead_value) : $quantity;
							if (empty($product_quantity)) {
								break;
							}

							if (!rgget($id, $products)) {
								$products[$id] = array();
							}

							$products[$id]["name"] = $use_admin_label && !rgempty("adminLabel", $field) ? $field["adminLabel"] : $lead_value[$id . ".1"];
							$products[$id]["price"] = rgar($lead_value, $id . ".2");
							$products[$id]["quantity"] = $product_quantity;
						} else if (!empty($lead_value)) {

							if (empty($quantity)) {
								break;
							}

							if (!rgar($products, $id)) {
								$products[$id] = array();
							}

							if ($field["inputType"] == "price") {
								$name = $field["label"];
								$price = $lead_value;
							} else {
								list($name, $price) = explode("|", $lead_value);
							}

							$products[$id]["name"] = !$use_choice_text ? $name : RGFormsModel::get_choice_text($field, $name);
							$products[$id]["price"] = $price;
							$products[$id]["quantity"] = $quantity;
							$products[$id]["options"] = array();
						}

						if (isset($products[$id])) {
							$options = GFCommon::get_product_fields_by_type($form, array("option"), $id);
							foreach ($options as $option) {
								$option_value = RGFormsModel::get_lead_field_value($lead, $option);
								$option_label = empty($option["adminLabel"]) ? $option["label"] : $option["adminLabel"];
								if (is_array($option_value)) {
									foreach ($option_value as $value) {
										$option_info = GFCommon::get_option_info($value, $option, $use_choice_text);
										if (!empty($option_info)) {
											$products[$id]["options"][] = array(
												"field_label" => rgar($option, "label"),
												"option_name" => rgar($option_info, "name"),
												"option_label" => $option_label . ": " . rgar($option_info, "name"),
												"price" => rgar($option_info, "price")
											);
										}
									}
								} else if (!empty($option_value)) {
									$option_info = GFCommon::get_option_info($option_value, $option, $use_choice_text);
									$products[$id]["options"][] = array(
										"field_label" => rgar($option, "label"),
										"option_name" => rgar($option_info, "name"),
										"option_label" => $option_label . ": " . rgar($option_info, "name"),
										"price" => rgar($option_info, "price")
									);
								}
							}
						}
						break;
				}
			}

			$shipping_field = GFCommon::get_fields_by_type($form, array("shipping"));
			$shipping_price = $shipping_name = "";

			if (!empty($shipping_field) && !RGFormsModel::is_field_hidden($form, $shipping_field[0], array(), $lead)) {
				$shipping_price = RGFormsModel::get_lead_field_value($lead, $shipping_field[0]);
				$shipping_name = $shipping_field[0]["label"];
				if ($shipping_field[0]["inputType"] != "singleshipping") {
					list($shipping_method, $shipping_price) = explode("|", $shipping_price);
					$shipping_name = $shipping_field[0]["label"] . " ($shipping_method)";
				}
			}

			$shipping_price = GFCommon::to_number($shipping_price);

			$product_info = array(
				"products" => $products,
				"shipping" => array("name" => $shipping_name, "price" => $shipping_price)
			);

			$products = apply_filters("gform_product_info_{$form["id"]}", apply_filters("gform_product_info", $product_info, $form, $lead), $form, $lead);

			if (!empty($products["products"])) {

				foreach ($products["products"] as $_product) {
					$price = GFCommon::to_number($_product["price"]);
					if (is_array(rgar($_product, "options"))) {
						$index = 1;
						foreach ($_product["options"] as $option) {
							$price += GFCommon::to_number($option["price"]);
							$index++;
						}
					}
					$subtotal = floatval($_product["quantity"]) * $price;
					$total += $subtotal;
				}

				$total += floatval($products["shipping"]["price"]);
			}
			$price = $product->get_price('edit');
			$price += (float)$total;

			return wc_price($price);
		}
		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_wc_gf_addons', 10, 3);
} // End if().

if (!function_exists('tinvwl_wc_gf_addons_edit_link')) {
	function tinvwl_wc_gf_addons_edit_link($form)
	{

		if (isset($_GET['wc_gforms_wishlist_product_id']) && empty($_POST)) {

			$wl_product_id = $_GET['wc_gforms_wishlist_product_id'];
			$wlp = new TInvWL_Product();
			$wl_product = $wlp->get(array('ID' => $wl_product_id));
			$wl_product = array_shift($wl_product);

			if (empty($wl_product)) {
				return $form;
			}
			if (array_key_exists('wc_gforms_form_id', $wl_product['meta'])) {

				$entry = array(
					'form_id' => $wl_product['meta']['wc_gforms_form_id']
				);
				$entry['id'] = uniqid() . time() . rand();

				foreach ($wl_product['meta'] as $key => $meta) {
					if (strpos($key, 'input_') === 0) {
						$entry[str_replace('_', '.', substr($key, strlen('input_')))] = $meta;
					}
				}

				$exclude_fields = array();
				foreach ($form['fields'] as &$field) {
					if (in_array($field['id'], $exclude_fields)) {
						$field['cssClass'] = 'gform_hidden';
						$field['isRequired'] = false;
					} else {
						$value = null;
						if ($field['type'] == 'checkbox' || ($field['type'] == 'option' && $field['inputType'] == 'checkbox')) { // handle checkbox fields
							// only pull the field values from the entry that match the form field we are evaluating
							$field_values = array();

							foreach ($entry as $key => $value) {
								$entry_key = explode('.', $key);
								if ($entry_key[0] == $field['id']) {
									$v = explode('|', $value);
									$field_values[] = $v[0];
								}
							}
							foreach ($field->choices as &$choice) {
								$choice['isSelected'] = (in_array($choice['value'], $field_values, true)) ? true : '';
							}
						} elseif (is_array($field->inputs)) { // handle other multi-input fields (address, name, time, etc.)

							// for time field, parse entry string to get individual parts of time string
							if ($field['type'] == 'time') {
								// separate time string from entry into individual parts
								list($HH, $time_end_part) = explode(':', $entry[strval($field['id'])]);
								list($MM, $AMPM) = explode(' ', $time_end_part);
								// save the time parts into individual array elements within the entry for our loop
								$entry[$field['id'] . '.1'] = $HH;
								$entry[$field['id'] . '.2'] = $MM;
								$entry[$field['id'] . '.3'] = $AMPM;
							}

							// loop each field input and set the default value from the entry
							foreach ($field->inputs as $key => &$input) {
								$value = '';
								if (isset($entry[strval($input['id'])])) {
									$value = $entry[strval($input['id'])];
								} elseif (isset($entry[$field['id']])) {
									$value = $entry[$field['id']];
								} elseif (isset($entry[$field['id'] . '1'])) {
									$value = $entry[$field['id']] . '.1';
								}

								$input['defaultValue'] = $value;
							}
						} else { // handle remaining single input fields
							if (isset($entry[$field['id']])) {
								$value = $entry[$field['id']];
							}
						}

						// if we have a value for the field from the provided entry, set the default value for the field
						if (!empty($value)) {
							$field['defaultValue'] = $value;
						}
					}
				}

			}

		}

		return $form;

	}

	add_filter('gform_pre_render', 'tinvwl_wc_gf_addons_edit_link', 99, 1);
}

if (!function_exists('tinv_wishlist_metaprepare_wc_gf_addons')) {

	/**
	 * Prepare save meta for WooCommerce - Gravity Forms Product Add-Ons
	 *
	 * @param array $meta Meta array.
	 *
	 * @return array
	 */
	function tinv_wishlist_metaprepare_wc_gf_addons($meta)
	{
		if (array_key_exists('wc_gforms_form_id', $meta) && class_exists('RGFormsModel')) {
			foreach ($meta as $key => $value) {
				if (strpos($key, 'input_') === 0) {
					unset($meta[$key]);
					$meta['input_' . str_replace('.', '_', substr($key, strlen('input_')))] = $value;
				}
			}
		}
		return $meta;
	}

	add_filter('tinvwl_product_prepare_meta', 'tinv_wishlist_metaprepare_wc_gf_addons');
}
