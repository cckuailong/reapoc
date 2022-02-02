<?php

class WPTC_Constants{
	public function __construct(){
	}

	public function init_live_plugin(){
		$this->path();
		$this->set_env();
		$this->general();
		$this->versions();
		$this->debug();
		$this->set_mode();
	}

	public function init_staging_plugin(){
		$this->path();
		$this->set_env();
		$this->general();
		$this->versions();
		$this->debug();
		$this->set_mode();
	}

	public function init_restore(){
		$this->path();
		$this->set_env();
		$this->general();
		$this->versions();
		$this->debug();
		$this->set_mode();
	}

	public function bridge_restore(){
		$this->set_env($type = 'bridge');
		$this->general();
		$this->versions();
		$this->debug();
		$this->set_mode();
	}

	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public function set_env($type = false){
		$path = ($type === 'bridge') ? '' : WPTC_PLUGIN_DIR ;

		if (file_exists($path . 'wptc-env-parameters.php')){
			include_once ($path . 'wptc-env-parameters.php');
		}

		$this->define( 'WPTC_ENV', 'production' );
	}

	public function set_mode(){
		switch (WPTC_ENV) {
			case 'production':
				$this->production_mode();
				break;
			case 'staging':
				$this->staging_mode();
				break;
			case 'local':
			default:
				$this->development_mode();
		}
	}

	public function debug(){
		$this->define( 'WPTC_DEBUG', false );
	}

	public function versions(){
		$this->define( 'WPTC_VERSION', '1.21.15' );
		$this->define( 'WPTC_DATABASE_VERSION', '17.0' );
	}

	public function general(){

		$this->define( 'WPTC_DEFAULT_CRON_TYPE', 'SCHEDULE');
		$this->define( 'WPTC_CHUNKED_UPLOAD_THREASHOLD', 5242880); //5 MB
		$this->define( 'WPTC_MIN_REQUIRED_STORAGE_SPACE', 5242880); //5 MB
		$this->define( 'WPTC_MINUMUM_PHP_VERSION', '5.2.16' );
		$this->define( 'WPTC_NO_ACTIVITY_WAIT_TIME', 60); //5 mins to allow for socket timeouts and long uploads
		$this->define( 'WPTC_PLUGIN_PREFIX', 'wptc' );
		$this->define( 'WPTC_TC_PLUGIN_NAME', 'wp-time-capsule' );
		$this->define( 'WPTC_DEBUG_SIMPLE', false );
		$this->define( 'WPTC_TIMEOUT', 23 );
		$this->define( 'WPTC_HASH_FILE_LIMIT', 1024 * 1024 * 15); //15 MB
		$this->define( 'WPTC_STAGING_COPY_SIZE', 1024 * 1024 * 2); //2 MB
		$this->define( 'HASH_CHUNK_LIMIT', 1024 * 128); // 128  KB
		$this->define( 'WPTC_CLOUD_DIR_NAME', 'wp-time-capsule' );
		$this->define( 'WPTC_STAGING_PLUGIN_DIR_NAME', 'wp-time-capsule-staging' );
		$this->define( 'WPTC_RESTORE_FILES_NOT_WRITABLE_COUNT', 15 );
		$this->define( 'WPTC_DEFAULT_BACKUP_SLOT', 'daily'); //subject to change
		$this->define( 'WPTC_DEFAULT_SCHEDULE_TIME_STR', '12:00 am' );
		$this->define( 'WPTC_NOTIFY_ERRORS_THRESHOLD', 10 );
		$this->define( 'WPTC_LOCAL_AUTO_BACKUP', true );
		$this->define( 'WPTC_AUTO_BACKUP', false );
		$this->define( 'WPTC_DEBUG_PRINT_ALL', true );
		$this->define( 'WPTC_DONT_BACKUP_META', true); // remove to take meta on every backup
		$this->define( 'WPTC_FALLBACK_REVISION_LIMIT_DAYS', 30 );
		$this->define( 'WPTC_DEFAULT_MAX_REVISION_LIMIT', 30 ); //30 days
		$this->define( 'WPTC_GDRIVE_TOKEN_ON_INIT_LIMIT', 5); //total connected sites limit for showing google drive
		$this->define( 'WPTC_ACTIVITY_LOG_LAZY_LOAD_LIMIT', 25);
		$this->define( 'WPTC_RESTORE_ADDING_FILES_LIMIT', 30);
		$this->define( 'WPTC_STAGING_DEFAULT_DEEP_LINK_REPLACE_LIMIT', 5000);
		$this->define( 'WPTC_CHECK_CURRENT_STATE_FILE_LIMIT', 500);
		$this->define( 'WPTC_STAGING_DEFAULT_FILE_COPY_LIMIT', 200);
		$this->define( 'WPTC_STAGING_DEFAULT_COPY_DB_ROWS_LIMIT', 1000);
		$this->define( 'WPTC_KEEP_MAX_BACKUP_DAYS_LIMIT', 366);
		$this->define( 'WPTC_REAL_TIME_BACKUP_MAX_PHP_DUMP_DB_SIZE', 209715200); //200 MB
		$this->define( 'WPTC_AUTO_BACKUP_CHECK_TIME_TOLERENCE', 600); // 10 mins (60 * 10)
		$this->define( 'WPTC_DEFAULT_DB_ROWS_BACKUP_LIMIT', 300); // 10 mins (60 * 10)
		$this->define( 'WPTC_DEFAULT_CURL_CONTENT_TYPE','Content-Type: application/x-www-form-urlencoded'); // some servers outbound requests are got blocked due to without content type
		$this->define( 'WPTC_MAX_REQUEST_PROGRESS_WAIT_TIME', 180); // 3 mins (3 * 60)
		$this->define( 'WPTC_GDRIVE_IPV4_ONLY', false);
		$this->define( 'WPTC_S3_VERIFICATION_FILE', 'wptc-verify.txt');
		$this->define( 'WPTC_CRYPT_BUFFER_SIZE', 2097152);

		//below PHP 5.4
		$this->define( 'JSON_UNESCAPED_SLASHES', 64);
		$this->define( 'JSON_UNESCAPED_UNICODE', 256);


		//Create backups in backwards for testing
		$this->define( 'WPTC_BACKWARD_BACKUPS_CREATION', false);
		$this->define( 'WPTC_BACKWARD_BACKUPS_CREATION_DAYS', 30);

		if (!defined('WPTC_BRIDGE')) {
			$this->define( 'WPTC_DROPBOX_WP_REDIRECT_URL', urlencode(base64_encode(network_admin_url() . 'admin.php?page=wp-time-capsule&cloud_auth_action=dropbox&env='.WPTC_ENV))); //state wp redirect url for dropbox
		}

		$this->define( 'ADVANCED_SECONDS_FOR_TIME_CALCULATION', 300);
		$this->define( 'TRIGGER_PREVENT_TABLES_COUNT_WPTC', 180);  //this is dummy, set in js file
		$this->define( 'TRIGGER_TABLE_CRON_DELETE_FREQUENCY', 86400 + 10800); //1 day 3 hours
		$this->define( 'ITERATOR_FILES_COUNT_CHECK', 10000);
		$this->define( 'SHOW_QUERY_RECORDER_TABLE_SIZE_EXCEED_WARNING', true);
	}

	public function path(){

		$this->define( 'WPTC_ABSPATH', wp_normalize_path( ABSPATH ) );
		$this->define( 'WPTC_RELATIVE_ABSPATH', '/' );
		$this->define( 'WPTC_WP_CONTENT_DIR', wp_normalize_path( WP_CONTENT_DIR ) );
		$this->define( 'WPTC_WP_CONTENT_BASENAME', basename( WPTC_WP_CONTENT_DIR ) );
		$this->define( 'WPTC_RELATIVE_WP_CONTENT_DIR', '/' . WPTC_WP_CONTENT_BASENAME );

		//Before modifying these, think about existing users
		$this->define( 'WPTC_TEMP_DIR_BASENAME', 'tCapsule' );
		$this->define( 'WPTC_REALTIME_DIR_BASENAME', 'wptc_realtime_tmp' );

		if (defined('WPTC_BRIDGE')) {
			$this->define( 'WPTC_EXTENSIONS_DIR', wp_normalize_path(BRIDGE_NAME_WPTC . '/Classes/Extension/') );
			$this->define( 'WPTC_PLUGIN_DIR', '' );
			$this->define( 'WPTC_RELATIVE_PLUGIN_DIR', '' );
			return ;
		}

		$this->define( 'WPTC_EXTENSIONS_DIR', wp_normalize_path(plugin_dir_path(__FILE__) . 'Classes/Extension/' ));
		$this->define( 'WPTC_CLASSES_DIR', wp_normalize_path(plugin_dir_path(__FILE__) . 'Classes/') );
		$this->define( 'WPTC_PRO_DIR', wp_normalize_path(plugin_dir_path(__FILE__) . 'Pro/') );

		$plugin_dir_path = wp_normalize_path( plugin_dir_path( __FILE__ ) );
		$this->define( 'WPTC_RELATIVE_PLUGIN_DIR', str_replace(WPTC_ABSPATH, WPTC_RELATIVE_ABSPATH, $plugin_dir_path ) );
		$this->define( 'WPTC_PLUGIN_DIR', $plugin_dir_path );

		$uploads_meta = wp_upload_dir();
		$basedir_path = wp_normalize_path( $uploads_meta['basedir'] );
		$this->define( 'WPTC_RELATIVE_UPLOADS_DIR', str_replace(WPTC_WP_CONTENT_DIR . '/', WPTC_RELATIVE_ABSPATH, $basedir_path ) );
		$this->define( 'WPTC_UPLOADS_DIR', $basedir_path);

	}

	public function production_mode(){
		$this->define( 'WPTC_CRSERVER_URL', 'https://cron.wptimecapsule.com' );
		$this->define( 'WPTC_USER_SERVICE_URL', 'https://service.wptimecapsule.com/service.php' );
		$this->define( 'WPTC_APSERVER_URL', 'https://service.wptimecapsule.com' );
		$this->define( 'WPTC_APSERVER_URL_FORGET', 'https://service.wptimecapsule.com/?show_forgot_pwd=true' );
		$this->define( 'WPTC_APSERVER_URL_SIGNUP', 'https://service.wptimecapsule.com/signup' );
		$this->define( 'WPTC_G_DRIVE_AUTHORIZE_URL', 'https://wptimecapsule.com/gdrive_auth/production/index.php' );
		$this->define( 'WPTC_DROPBOX_REDIRECT_URL', 'https://wptimecapsule.com/dropbox_auth/index.php' );
		$this->define( 'WPTC_DROPBOX_CLIENT_ID', base64_decode('aHA3ZzJkcTl0YzgxZHdl') );
		$this->define( 'WPTC_DROPBOX_CLIENT_SECRET', base64_decode('MnlqNTVwa2lna2g4NTg2') );
		$this->define( 'WPTC_CURL_TIMEOUT', 20 );
	}

	public function staging_mode(){
		$this->define( 'WPTC_CRSERVER_URL', 'https://wptc-dev-node.rxforge.in' );
		$this->define( 'WPTC_USER_SERVICE_URL', 'https://wptc-dev-service.rxforge.in/service/service.php' );
		$this->define( 'WPTC_APSERVER_URL', 'https://wptc-dev-service.rxforge.in/service' );
		$this->define( 'WPTC_APSERVER_URL_FORGET', 'https://service.wptimecapsule.com/?show_forgot_pwd=true' );
		$this->define( 'WPTC_APSERVER_URL_SIGNUP', 'https://service.wptimecapsule.com/signup' );
		$this->define( 'WPTC_G_DRIVE_AUTHORIZE_URL', 'https://wptimecapsule.com/gdrive_auth/staging/index.php' );
		$this->define( 'WPTC_DROPBOX_REDIRECT_URL', 'https://wptimecapsule.com/dropbox_auth/index.php' );
		$this->define( 'WPTC_DROPBOX_CLIENT_ID', base64_decode('dHU4djcwM3A3cWk4cDky') );
		$this->define( 'WPTC_DROPBOX_CLIENT_SECRET', base64_decode('dHZ2MXM4dmxpcTVwNHU3') );
		$this->define( 'WPTC_CURL_TIMEOUT', 20 );
	}

	public function development_mode(){
		$this->define( 'WPTC_CRSERVER_URL', 'http://localhost:9999' );
		$this->define( 'WPTC_USER_SERVICE_URL', 'http://dark.dev.com/wptc-service/service.php' );
		$this->define( 'WPTC_APSERVER_URL', 'http://dark.dev.com/wptc-service' );
		$this->define( 'WPTC_APSERVER_URL_FORGET', 'https://service.wptimecapsule.com/?show_forgot_pwd=true' );
		$this->define( 'WPTC_APSERVER_URL_SIGNUP', 'https://service.wptimecapsule.com/signup' );
		$this->define( 'WPTC_G_DRIVE_AUTHORIZE_URL', 'https://wptimecapsule.com/gdrive_auth/development/index.php');
		$this->define( 'WPTC_DROPBOX_REDIRECT_URL', 'https://wptimecapsule.com/dropbox_auth/index.php' );
		$this->define( 'WPTC_DROPBOX_CLIENT_ID', base64_decode('bTMwY2hlaTh5YXRoYTRr') );
		$this->define( 'WPTC_DROPBOX_CLIENT_SECRET', base64_decode('ZzA5Y2NoNHc5c3Fwazli') );
		$this->define( 'WPTC_CURL_TIMEOUT', 20 );
	}
}
