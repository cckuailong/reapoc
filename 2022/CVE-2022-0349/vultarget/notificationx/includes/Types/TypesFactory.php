<?php
/**
 * Type Factory
 *
 * @package NotificationX\Types
 */

namespace NotificationX\Types;
use NotificationX\GetInstance;

/**
 * TypeFactory Class
 */
class TypeFactory {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
	use GetInstance;

	public $types = [
            'conversions'		 	=> 'NotificationX\Types\Conversions',
			'comments'			 	=> 'NotificationX\Types\Comments',
			'reviews'			 	=> 'NotificationX\Types\Reviews',
			'download_stats'	 	=> 'NotificationX\Types\DownloadStats',
			'elearning'			 	=> 'NotificationX\Types\ELearning',
			'donation'			 	=> 'NotificationX\Types\Donations',
			'notification_bar'		=> 'NotificationX\Types\NotificationBar',
			'form'				 	=> 'NotificationX\Types\ContactForm',
			'email_subscription' 	=> 'NotificationX\Types\EmailSubscription',
			'page_analytics'	 	=> 'NotificationX\Types\PageAnalytics',
			'custom'			 	=> 'NotificationX\Types\CustomNotification',
			'inline'			 	=> 'NotificationX\Types\Inline',
		];

    public $types_enabled = [];

	/**
	 * Initially Invoked when initialized.
	 */
	public function __construct(){
		$this->types = apply_filters( 'nx_types_classes', $this->types );

	}

    /**
     * Registers a type.
     *
     * @param string $id
     * @return mixed
     */
	public function register_types($id){
		if(!isset($this->types_enabled[$id]) && isset($this->types[$id])){
            $type_class = $this->types[$id];
			$obj = $type_class::get_instance();
			return $this->add($obj);
		}
		return false;
	}

    /**
     * Enable a type.
     *
     * @param Type $types
     * @return Type
     */
    protected function add( $type) {
		$this->types_enabled[ $type->id ] = $type;
		return $type;
	}

    /**
     * This function is responsible for getting the type from loaded type.
     *
     * @param string $key
     * @return bool|void
     */
    public function get( $key ){
        if( empty( $key ) ) {
            return false;
        }
        if(isset($this->types_enabled[$key])){
            return isset( $this->types_enabled[ $key ] ) ? $this->types_enabled[ $key ] : false;
        }
        else if(isset( $this->types[ $key ] ) && $type_class = $this->types[ $key ]){
            return $type_class::get_instance();
        }
    }

    /**
     * Get all enabled types.
     *
     * @return array
     */
    public function get_all(){
        return $this->types_enabled;
    }
}
