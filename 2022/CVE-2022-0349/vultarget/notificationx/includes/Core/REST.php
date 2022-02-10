<?php

/**
 * Extension Factory
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Core;

use NotificationX\Admin\ImportExport;
use NotificationX\Types\ContactForm;
use NotificationX\Admin\Settings;
use NotificationX\CoreInstaller;
use NotificationX\Extensions\PressBar\PressBar;
use NotificationX\Admin\Reports\ReportEmail;
use NotificationX\FrontEnd\FrontEnd;
use NotificationX\GetInstance;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

/**
 * ExtensionFactory Class
 */
class REST {
    /**
     * Instance of REST
     *
     * @var REST
     */
    use GetInstance;

	private static $_namespace = 'notificationx';
	private static $_version = 1;

    public static function _namespace(){
        return  self::$_namespace . '/v' . self::$_version;
    }

    /**
     * Invoked Automatically
     */
    public function __construct(){
        Rest\Posts::get_instance();
        Rest\Integration::get_instance();
        Rest\Entries::get_instance();
        Rest\Analytics::get_instance();
        Rest\BulkAction::get_instance();

        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Check if a given request has access to get items
     *
     * @param \WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     */
    public function read_permission( $request ) {
        return current_user_can('read_notificationx');
    }
    public function edit_permission( $request ) {
        return current_user_can('edit_notificationx');
    }
    public function settings_permission( $request ) {
        return current_user_can('edit_notificationx_settings');
    }
    public function activate_plugin_permission( $request ) {
        $params = $request->get_params();
        if(isset($params['is_installed'])){
            if($params['is_installed']){
                return current_user_can('activate_plugins');
            }
            else{
                return current_user_can('install_plugins');
            }
        }
        return current_user_can('activate_plugins') && current_user_can('install_plugins');
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $namespace = self::_namespace();
        register_rest_route( $namespace, '/builder', array(
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'get_builder' ),
            'permission_callback' => array($this, 'read_permission'),
        ));
        register_rest_route( $namespace, '/core-install', array(
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( $this, 'core_install' ),
            'permission_callback' => array($this, 'activate_plugin_permission'),
        ));
        // Elementor Import
        register_rest_route( $namespace, '/elementor/import', array(
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( $this, 'elementor_import' ),
            'permission_callback' => array($this, 'edit_permission'),
        ));
        register_rest_route( $namespace, '/elementor/remove', array(
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( $this, 'elementor_remove' ),
            'permission_callback' => array($this, 'edit_permission'),
        ));
        // Reporting Import
        register_rest_route( $namespace, '/reporting-test', array(
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( $this, 'reporting_test' ),
            'permission_callback' => array($this, 'settings_permission'),
        ));

        // NX Settings
        register_rest_route($namespace, '/settings', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'save_settings' ),
                'permission_callback' => array($this, 'settings_permission'),
                'args'                => array(),
            ),
        ));

        // ajax select
		register_rest_route($namespace, '/get-data', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'get_data'),
                'permission_callback' => array($this, 'read_permission'),
                'args'                => [],
            ),
        ));
        // For Frontend Notice
		register_rest_route($namespace, '/notice', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'notice'),
                'permission_callback' => '__return_true',
                'args'                => [],
            ),
        ));
        // For entries page.
        // register_rest_route($namespace, '/entries/(?P<nx_id>[0-9]+)', array(
        //     array(
        //         'methods'             => WP_REST_Server::READABLE,
        //         'callback'            => array($this, 'get_entries'),
        //         'permission_callback' => '__return_true',
        //         'args'                => [],
        //     ),
        // ));

        // import/export
        register_rest_route( $namespace, '/import', array(
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( ImportExport::get_instance(), 'import' ),
            'permission_callback' => array($this, 'edit_permission'),
        ));
        register_rest_route( $namespace, '/export', array(
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( ImportExport::get_instance(), 'export' ),
            'permission_callback' => array($this, 'edit_permission'),
        ));
    }

    public function get_builder( $request ){
        return PostType::get_instance()->get_localize_scripts();
    }

    /**
     * Elementor Import for PressBar design.
     *
     * @param [type] $request
     * @return void
     */
    public function elementor_import( $request ){
        $params = $request->get_params();
        PressBar::get_instance()->create_bar_of_type_bar_with_elementor($params);
        return true;
    }

    /**
     * Elementor Import for PressBar design.
     *
     * @param [type] $request
     * @return void
     */
    public function elementor_remove( $request ){
        $params = $request->get_params();
        PressBar::get_instance()->delete_elementor_post($params['elementor_id']);
        return true;
    }

    /**
     * Analytics Reporting
     *
     * @param WP_REST_Request $request
     * @return void|boolean|array
     */
    public function reporting_test( $request ){
        return ReportEmail::get_instance()->reporting( $request );
    }

    public function get_notificationX( $request ){
        if( $request->get_method() === 'GET' ) {
            return PostType::get_instance()->get_post_with_analytics();
        }
        if( $request->get_method() === 'POST' ) {
            $params = $request->get_params();
            return PostType::get_instance()->save_post($params);
        }
    }

    /**
     * Get data for specific type
     *
     * @param \WP_REST_Request $request Full data about the request.
     * @return \WP_Error|\WP_REST_Response
     */
	public function get_data( $request ){
		$params = $request->get_params();
        if( ! $request->has_param('type') ) {
            return $this->error( 'type' );
        }

        switch( $params['type'] ) {
            case 'ContactForm' :
                return ContactForm::restResponse( $request->get_json_params() );
                break;
        }

		return $this->error();
	}

    /**
     *
     *
     * @param \WP_REST_Request $request Full data about the request.
     * @return \WP_Error|\WP_REST_Response
     */
    public function save_settings($request) {
        //   $item = $this->prepare_item_for_database( $request );

        $result = Settings::get_instance()->save_settings($request->get_params());
        if($result){
            return [
                'success' => true,
            ];
        }
        else{
            return [
                'success' => false,
            ];
        }
    }

    /**
     * Return notices for frontend.
     *
     * @param \WP_REST_Request $request Full data about the request.
     * @return void
     */
    public function notice($request) {
        $params = $request->get_params();
        return FrontEnd::get_instance()->get_notifications_data( $params );

    }

    public function rest_data(){
        return apply_filters('nx_rest_data', array(
            'root'      => rest_url(),
            'namespace' => $this->_namespace(),
            'nonce'     => wp_create_nonce( 'nx_rest' ),
        ));
    }

    /**
     * Undocumented function
     *
     * @param \WP_REST_Request $request
     * @return
     */
    public function core_install( \WP_REST_Request $request ){
        $params = $request->get_params();
        $slug = $params['slug'];
        $file = $params['file'];
        $result = CoreInstaller::get_instance()->install_plugin($slug, $file);
        return $result == null;
    }

    /**
     * This is function will throw error for API
     *
     * @param string $type
     * @return \WP_Error
     */
    public function error( $type = '' ) {
        switch( $type ) {
            case 'api':
                return $this->formattedError( 'api_error', __( 'Unauthorized Access: You have to logged in first.', 'notificationx' ), 401 );
                break;
            case 'type':
                return $this->formattedError( 'type_error', __( 'Invalid Type: You have to give a type.', 'notificationx' ), 401 );
                break;
            default:
                return $this->formattedError( 'response_error', __( '400 Bad Request.', 'notificationx' ), 400 );
        }
    }

    /**
     * This function is responsible for format Error Message by \WP_Error
     *
     * @param string $code
     * @param string $message
     * @param integer $http_code
     * @param array $args
     * @return \WP_Error
     */
    private function formattedError( $code, $message, $http_code, $args = [] ){
        return new \WP_Error( "nx_$code", $message, [ 'status' => $http_code ] );
    }
}