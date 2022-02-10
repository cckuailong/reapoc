<?php
/**
 * Extension Factory
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions;

use NotificationX\Core\Database;
use NotificationX\GetInstance;
use NotificationX\Core\Modules;

/**
 * ExtensionFactory Class
 */
class ExtensionFactory {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
	use GetInstance;

	public $active_items;
	public $types = [];
	public $extensions = [];
	public $extension_classes = [
			'cf7'                             => 'NotificationX\Extensions\CF7\CF7',
			'convertkit'                      => 'NotificationX\Extensions\ConvertKit\ConvertKit',
			'custom_notification'             => 'NotificationX\Extensions\CustomNotification\CustomNotification',
			'custom_notification_conversions' => 'NotificationX\Extensions\CustomNotification\CustomNotificationConversions',
			'edd'                             => 'NotificationX\Extensions\EDD\EDD',
			'envato'                          => 'NotificationX\Extensions\Envato\Envato',
			'freemius_conversions'            => 'NotificationX\Extensions\Freemius\FreemiusConversions',
			'freemius_reviews'                => 'NotificationX\Extensions\Freemius\FreemiusReviews',
			'freemius_stats'                  => 'NotificationX\Extensions\Freemius\FreemiusStats',
			'grvf'                            => 'NotificationX\Extensions\GRVF\GravityForms',
			'give'                            => 'NotificationX\Extensions\Give\Give',
			'google'                          => 'NotificationX\Extensions\Google_Analytics\Google_Analytics',
			'learndash'                       => 'NotificationX\Extensions\LearnDash\LearnDash',
			'mailchimp'                       => 'NotificationX\Extensions\MailChimp\MailChimp',
			'njf'                             => 'NotificationX\Extensions\NJF\NinjaForms',
			'press_bar'                       => 'NotificationX\Extensions\PressBar\PressBar',
			'tutor'                           => 'NotificationX\Extensions\Tutor\Tutor',
			'wpf'                             => 'NotificationX\Extensions\WPF\WPForms',
			'reviewx'                         => 'NotificationX\Extensions\ReviewX\ReviewX',
			'woocommerce'                     => 'NotificationX\Extensions\WooCommerce\WooCommerce',
			'woo_reviews'                     => 'NotificationX\Extensions\WooCommerce\WooReviews',
			'wp_comments'                     => 'NotificationX\Extensions\WordPress\WPComments',
			'wp_reviews'                      => 'NotificationX\Extensions\WordPress\WPOrgReview',
			'wp_stats'                        => 'NotificationX\Extensions\WordPress\WPOrgStats',
			'zapier_conversions'              => 'NotificationX\Extensions\Zapier\ZapierConversions',
			'zapier_email_subscription'       => 'NotificationX\Extensions\Zapier\ZapierEmailSubscription',
			'zapier_reviews'                  => 'NotificationX\Extensions\Zapier\ZapierReviews',
			'woo_inline'                      => 'NotificationX\Extensions\WooCommerce\WooInline',
			'edd_inline'                      => 'NotificationX\Extensions\EDD\EDDInline',
		];

	/**
	 * Initially Invoked when initialized.
	 */
	public function __construct(){
		// GlobalFields::get_instance();
		$this->register_extensions();
	}

	public function register_extensions(){
		$this->extension_classes = apply_filters( 'nx_extension_classes', $this->extension_classes );
		foreach ($this->extension_classes as $extension) {
			// initializing extension.
			if(class_exists($extension)){
				$obj = $extension::get_instance();
				if(Modules::get_instance()->is_enabled($obj->module)){
					$this->add($obj);
				}
			}
		}
	}

	/**
     * This function is responsible for adding an extension to the extensions array!
     *
     * @param array $extensions
     * @param string $classname
     * @return void
     */
    protected function add( $extension) {
		$this->extensions[ $extension->id ] = $extension;
		$this->types[$extension->types][ $extension->id ] = $extension;
		return $this->extensions;
	}

	/**
	 * Get Extensions data
	 *
	 * @param Extension $extension The notifications type.
	 * @param array     $args Settings arguments for notificationx.
	 * @return array
	 */
	public static function getExtension( Extension $extension, $args = array() ){
		return $extension->get_data( $args );
	}

    /**
     * This function is responsible for getting the extension from loaded extension.
     *
     * @param string $key
     * @return Extension
     */
    public function get( $key ){
        if( empty( $key ) ) {
            return false;
        }
		try {
			$class = $this->extension_classes[$key];
			if(class_exists($class)){
				return $class::get_instance();
			}
		}
		catch (\Exception $e) {

		}
        return isset( $this->extensions[ $key ] ) ? $this->extensions[ $key ] : false;
	}

    /**
     * This function is responsible for getting the extension from loaded extension.
     *
     * @param string $key
     * @return array|bool
     */
    public function get_type( $type ){
        if( empty( $type ) ) {
            return false;
        }
        return isset( $this->types[ $type ] ) ? $this->types[ $type ] : false;
	}

    /**
     * This function is responsible for getting the extension from loaded extension.
     *
     * @param string $key
     * @return array
     */
    public function get_themes_for_type( $type ){
		$themes = [];

		$extensions = $this->get_type($type);

		if(is_array($extensions)){
			foreach($extensions as $extension){
				if(!$extension->exclude_custom_themes){
					$themes = array_merge($themes, $extension->get_themes_name());
				}
			}
		}

		return array_unique($themes);
	}

    /**
     * This function is responsible for getting the extension from loaded extension.
     *
     * @param string $key
     * @return bool|void
     */
    public function get_all(){
        return $this->extensions;
    }
}
