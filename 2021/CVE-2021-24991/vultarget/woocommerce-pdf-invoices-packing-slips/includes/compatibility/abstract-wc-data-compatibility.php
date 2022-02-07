<?php
namespace WPO\WC\PDF_Invoices\Compatibility;

/**
 * Derived from SkyVerge WooCommerce Plugin Framework https://github.com/skyverge/wc-plugin-framework/
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\\WPO\\WC\\PDF_Invoices\\Compatibility\\Data' ) ) :

/**
 * WooCommerce data compatibility class.
 *
 * @since 4.6.0-dev
 */
abstract class Data {

	/**
	 * Creates aliases for add_meta_data, update_meta_data and delete_meta_data without the _data suffix
	 *
	 * @param  string $name      static function name
	 * @param  array  $arguments function arguments
	 */
	public static function __callStatic( $name, $arguments ) {
		if ( substr( $name, -strlen('_meta') ) == '_meta' && method_exists( __CLASS__, $name.'_data' ) ) {
			call_user_func_array( array( __CLASS__, $name.'_data' ), $arguments );
		}
	}


	/**
	 * Gets an object property.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $prop the property name
	 * @param string $context if 'view' then the value will be filtered
	 * @param array $compat_props Compatibility properties.
	 * @return mixed
	 */
	public static function get_prop( $object, $prop, $context = 'edit', $compat_props = array() ) {

		$value = '';

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			if ( is_callable( array( $object, "get_{$prop}" ) ) ) {
				$value = $object->{"get_{$prop}"}( $context );
			}

		} else {

			// backport the property name
			if ( isset( $compat_props[ $prop ] ) ) {
				$prop = $compat_props[ $prop ];
			}

			// if this is the 'view' context and there is an accessor method, use it
			if ( is_callable( array( $object, "get_{$prop}" ) ) && 'view' === $context ) {
				$value = $object->{"get_{$prop}"}();
			} else {
				$value = $object->$prop;
			}
		}

		return $value;
	}


	/**
	 * Sets an object's properties.
	 *
	 * Note that this does not save any data to the database.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param array $props the new properties as $key => $value
	 * @param array $compat_props Compatibility properties.
	 * @return \WC_Data
	 */
	public static function set_props( $object, $props, $compat_props = array() ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			$object->set_props( $props );

		} else {

			foreach ( $props as $prop => $value ) {

				if ( isset( $compat_props[ $prop ] ) ) {
					$prop = $compat_props[ $prop ];
				}

				$object->$prop = $value;
			}
		}

		return $object;
	}


	/**
	 * Gets an object's stored meta value.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 * @param bool $single whether to get the meta as a single item. Defaults to `true`
	 * @param string $context if 'view' then the value will be filtered
	 * @return mixed
	 */
	public static function get_meta( $object, $key = '', $single = true, $context = 'edit' ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {
			$value = $object->get_meta( $key, $single, $context );
		} else {
			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;
			$value = get_post_meta( $object_id, $key, $single );
		}

		return $value;
	}


	/**
	 * Stores an object meta value.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 * @param string $value the meta value
	 * @param string $meta_id Optional. The specific meta ID to update
	 * @param bool $unique Optional. Whether the meta should be unique.
	 */
	public static function add_meta_data( $object, $key, $value, $unique = false ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			$object->add_meta_data( $key, $value, $unique );

			$object->save_meta_data();

		} else {

			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;
			add_post_meta( $object_id, $key, $value, $unique );
		}
	}


	/**
	 * Updates an object's stored meta value.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 * @param string $value the meta value
	 * @param int|strint $meta_id Optional. The specific meta ID to update
	 */
	public static function update_meta_data( $object, $key, $value, $meta_id = '' ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			$object->update_meta_data( $key, $value, $meta_id );

			$object->save_meta_data();

		} else {

			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;
			update_post_meta( $object_id, $key, $value );
		}
	}


	/**
	 * Deletes an object's stored meta value.
	 *
	 * @since 4.6.0-dev
	 * @param \WC_Data $object the data object, likely \WC_Order or \WC_Product
	 * @param string $key the meta key
	 */
	public static function delete_meta_data( $object, $key ) {

		if ( WC_Core::is_wc_version_gte_3_0() ) {

			$object->delete_meta_data( $key );

			$object->save_meta_data();

		} else {

			$object_id = is_callable( array( $object, 'get_id' ) ) ? $object->get_id() : $object->id;
			delete_post_meta( $object_id, $key );
		}
	}


}


endif; // Class exists check
