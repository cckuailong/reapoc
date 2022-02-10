<?php
namespace PowerpackElementsLite;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Modules_Manager {
	/**
	 * @var Module_Base[]
	 */
	private $modules = [];

	public function register_modules() {
		$modules = [
			'advanced-accordion',
			'business-hours',
			'buttons',
			'contact-form-seven',
			'caldera-forms',
			'content-reveal',
			'content-ticker',
			'flipbox',
			'formidable-forms',
			'fluent-forms',
			'gravity-forms',
			'ninja-forms',
			'wpforms',
			'counter',
			'divider',
			'headings',
			'hotspots',
			'icon-list',
			'image-accordion',
			'image-comparison',
			'info-box',
			'info-list',
			'info-table',
			'instafeed',
			'link-effects',
			'logos',
			'posts',
			'pricing',
			'promo-box',
			'random-image',
			'scroll-image',
			'team-member',
			'twitter',
			'query-control',
			'display-conditions',
		];

		ksort($modules);

		foreach ( $modules as $module_name ) {
			$class_name = str_replace( '-', ' ', $module_name );

			$class_name = str_replace( ' ', '', ucwords( $class_name ) );

			$class_name = __NAMESPACE__ . '\\Modules\\' . $class_name . '\Module';

			/** @var Module_Base $class_name */
			if ( $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::instance();
			}
		}
	}

	/**
	 * @param string $module_name
	 *
	 * @return Module_Base|Module_Base[]
	 */
	public function get_modules( $module_name ) {
		if ( $module_name ) {
			if ( isset( $this->modules[ $module_name ] ) ) {
				return $this->modules[ $module_name ];
			}

			return null;
		}

		return $this->modules;
	}

	private function require_files() {
		require( POWERPACK_ELEMENTS_LITE_PATH . 'base/module-base.php' );
	}

	public function __construct() {
		$this->require_files();
		$this->register_modules();
	}
}