<?php

class WPTC_Bridge_Core {

	private $restore_app_functions;
	private $site_url;
	private $restore_url;

	public function __construct(){
		$this->suppress_errors();
	}

	private function suppress_errors(){
		error_reporting(0);
		ini_set('display_errors', 0);
	}

	public function wptc_choose_functions() {
		switch ($_GET['step']) {
			case 'connect_db':
				$this->show_db_creds_page();
				break;
			case 'show_points':
				$this->include_files();
				$this->show_points();
				$this->initiate_database();
				$this->include_js_vars();
				break;
			case 'restore':
				$this->include_files();
				$this->load_restore_process_queue();
				break;
		}
	}

	private function show_db_creds_page() {
		$this->load_header();
		$this->load_footer();
		?>
<div class="container" style="padding:10%">
	<h5 style="text-align:center;">Database Details</h5>
  <div class="row" style="position: relative;padding: 1rem;margin: 1rem -15px;border: 1px solid #e5e5e5;background-color: #fff;">
	<div class="col-sm-6" style="text-align: center;border-right: 1px solid #e5e5e5; padding: 15px">
		<div style="font-size: 13px;padding: 15px;font-weight: 600;">Load from WP Config</div>
		<a id="load_from_wp_config" href="#" role="button" class="btn btn-primary btn_wptc  wptc-custom-btn" style=" right: 16%;  position: relative;">Load restore points</a> <br><br><br>
		<div style="font-size: 13px;padding: 15px;">(It will fetch the database details from <strong>wp-config.php</strong> )</div>
	</div>
	<div></div>
	<div class="col-sm-6" style="text-align: center; padding: 15px">
		<div style="font-size: 13px;padding: 15px;font-weight: 600;">Custom database details</div>
		<a id="custom_creds" href="#" role="button" class="btn btn-primary btn_wptc  wptc-custom-btn" style=" right: 21%;  position: relative;">Database details</a>
		<br><br><br>
		<div style="font-size: 13px;padding: 15px;">(Enter the database details manually)</div>
	</div>
  </div>
  <div class="row center_div" style="margin: 0 auto;width:80%;">
		 <div class="col-sm-6">
			<form action="index.php?step=show_points" id="db_creds_form" method="POST" style="display: none;">
			<div class="clearfix">
				<div class="form-group">
					<label for="exampleInputEmail1">Name</label>
					<input type="text" id="db_name" class="form-control" required="" name="db_name">
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">Host</label>
					<input type="text" id="db_host" class="form-control" required="" value="localhost" name="db_host">
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">Prefix</label>
					<input type="text" id="db_prefix" class="form-control" required="" value="wp_" name="db_prefix">
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">Username</label>
					<input type="text" id="db_username" class="form-control" required="" name="db_username">
				</div>
				<div class="form-group">
					<label for="exampleInputPassword1">Password</label>
					<input type="text" id="db_password" class="form-control" name="db_password">
				</div>
				<div class="form-group" style="display: none">
					<label for="exampleInputPassword1">Charset</label>
					<input type="text" id="db_charset" class="form-control" name="db_charset">
				</div>
				<div class="form-group" style="display: none">
					<label for="exampleInputPassword1">Collate</label>
					<input type="text" id="db_collate" class="form-control" name="db_collate">
				</div>
				<div class="form-group" style="display: none">
					<label for="exampleInputPassword1">WP content dir</label>
					<input type="text" id="wp_content_dir" class="form-control" name="wp_content_dir">
				</div>
			</div>
			<div class="error" style="padding: 15px;"></div>
			<div class="clearfix" style="text-align: center;"><a id="check_db_creds" class="btn btn-primary btn_wptc  wptc-custom-btn" role="button"  style=" right: 29%;  position: relative;" herf="#">Load restore points</a></div>
		</form>
		 </div>
  </div>
</div>

		<?php
	}

	private function show_meta_uploader(){
		$this->load_header();
		$this->load_upload_meta_html();
		$this->load_upload_meta_script();
		$this->load_footer();
	}

	private function show_points() {
		global $wpdb;
		if (empty($_POST)) {
			$db_info = $this->fetch_creds_from_wp_config();
			if (empty($db_info)) {
				die('we cannot fetch data from wp-config.php, Please go back and enter database details manually !.');
			}
			$wpdb = new wpdb($db_info['db_username'], $db_info['db_password'], $db_info['db_name'], $db_info['db_host']);
			$wpdb->base_prefix = $db_info['db_prefix'];
			$response = $this->create_config_file($db_info);
		} else {
			$wpdb = new wpdb($_POST['db_username'], $_POST['db_password'], $_POST['db_name'], $_POST['db_host']);
			$wpdb->base_prefix = $_POST['db_prefix'];
			$response = $this->create_config_file($_POST);
		}

		if($response === false){
			die( "Cannot write in wp-tc-config.php, please set 755 permission for wp-tcapsule-bridge folder" );
		}

		include_once dirname(__FILE__). '/' .'wp-modified-functions.php';
		include_once dirname(__FILE__). '/' .'wp-tc-config.php';
		$processed_files = WPTC_Factory::get('processed-files');
		$stored_backups = $processed_files->get_stored_backups();
		$detailed_backups = $processed_files->get_point_details($stored_backups);

		if (empty($detailed_backups)) {
			$this->show_meta_uploader();
			die();
		}

		$html = $processed_files->get_bridge_html($detailed_backups);

		if (empty($html)) {
			$this->show_meta_uploader();
			die();
		}

		echo $html;
	}

	private function get_specific_constant ($content, $find){
		if (!preg_match("/define\((.*?)$find(.*?), (.*?)\)/", $content , $matches)) {
			return false;
		}

		$result = trim($matches[3]);
		return substr($result, 1, -1);
	}

	private function get_table_prefix ($content){
		if (!preg_match("/table_prefix(.*?)=(.*?);/", $content , $result)) {
			return false;
		}

		$prefix = trim($result[2]);
		return substr($prefix, 1, -1);
	}

	private function fetch_creds_from_wp_config(){
		if (!file_exists('../wp-config.php')) {
			return false;
		}

		$content = file_get_contents("../wp-config.php");

		if (empty($content)) {
			return false;
		}

		$db_info['db_name'] 	= $this->get_specific_constant($content, 'DB_NAME');
		$db_info['db_username'] = $this->get_specific_constant($content, 'DB_USER');
		$db_info['db_password'] = $this->get_specific_constant($content, 'DB_PASSWORD');
		$db_info['db_host'] 	= $this->get_specific_constant($content, 'DB_HOST');
		$db_info['db_prefix'] 	= $this->get_table_prefix($content);

		return $db_info;
	}

	public function define_constants() {
		if (!defined('WP_DEBUG')) {
			define('WP_DEBUG', true);
		}
		if (!defined('WP_DEBUG_DISPLAY')) {
			define('WP_DEBUG_DISPLAY', true);
		}
	}

	public function load_header() {?>
		<!doctype html>
		<html>
		<head>
		<meta charset="UTF-8">
		<title>Restore your website</title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet" type="text/css" >
		<link href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css" rel="stylesheet" type="text/css" >
		<link rel="stylesheet" type="text/css" href="bridge_style.css">
		<link rel="stylesheet" href="bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb">
		<script type="text/javascript" src="wp-files/jquery.js"></script>
		<script type="text/javascript" src="bridge_init.js"></script>
		<script type="text/javascript" src="wptc-monitor.js"></script>
		</head>
		<body style="background-color: #f1f1f1;">
		<?php
	}

	public function include_js_vars() {?>
		<script type="text/javascript" language="javascript">
			//initiating Global Variables here
		var sitenameWPTC =  '';
		var freshBackupWptc = '';
		var startBackupFromSettingsWPTC = '';
		var bp_in_progress = false;
		var wp_base_prefix_wptc = '<?php if (defined('DB_PREFIX_WPTC')) {echo DB_PREFIX_WPTC;} else {echo '';}?>';
		var this_home_url_wptc = '<?php echo wptc_get_home_page_url(); ?>';
		var defaultDateWPTC = '<?php echo date('Y-m-d', time()) ?>' ;
		var wptcOptionsPageURl = '<?php echo wptc_get_options_page_url(); ?>';
		var this_plugin_url_wptc = '';
		var wptcMonitorPageURl = '<?php echo wptc_get_monitor_page_url(); ?>';
		var wptcPluginURl = '';
		var freshbackupPopUpWPTC = false;
		var on_going_restore_process = false;
		var cuurent_bridge_file_name = 'wp-tcapsule-bridge';
		var ajaxurl = '';
		var seperate_bridge_call = 1;
		var resume_count_wptc = 0;
		</script> <?php
	}

	public function continue_restore(){ ?>
		<script type="text/javascript" language="javascript">
		start_bridge_download_wptc();
		</script> <?php
	}

	public function start_from_beginning(){?>
		<script type="text/javascript" language="javascript">
		start_bridge_download_wptc({ initialize: true });
		</script> <?php
	}

	public function load_footer() {?>
		</body> <?php
	}

	public function include_config() {
		include_once dirname(__FILE__). '/' .'wp-modified-functions.php';
		include_once dirname(__FILE__). '/' .'wp-tc-config.php';

	}

	public function initiate_database() {
		//initialize wpdb since we are using it independently
		global $wpdb;
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

		//setting the prefix from post value;
		$wpdb->prefix = $wpdb->base_prefix = DB_PREFIX_WPTC;
	}

	public function initiate_filesystem() {
		$creds = request_filesystem_credentials("", "", false, false, null);
		if (false === $creds) {
			return false;
		}

		if (!WP_Filesystem($creds)) {
			return false;
		}
	}

	public function include_files() {
		define('WPTC_BRIDGE', true); //used in wptc-constants.php

		require_once dirname(__FILE__). '/' ."common_include_files.php";
		$Common_Include_Files = new Common_Include_Files('WPTC_Bridge_Core');
		$Common_Include_Files->init();
	}

	public function check_db_creds() {
		$this->define_constants();
		require_once dirname(__FILE__). '/' ."wp-modified-functions.php";
		require_once dirname(__FILE__). '/' ."wp-db-custom.php";
		require_once dirname(__FILE__). '/' .'wp-files/class-wp-error.php';

		global $wpdb;
		$wpdb = new wpdb($_POST['data']['db_username'], $_POST['data']['db_password'], $_POST['data']['db_name'], $_POST['data']['db_host']);
	}

	private function load_restore_process_queue() {
		?><div class="pu_title">Restoring your website</div>
	<div class="wcard progress_reverse" style="height:60px; padding:0;"><div class="progress_bar" style="width: 0%;"></div>  <div class="progress_cont">Preparing files to restore...</div></div><div style="padding: 10px; text-align: center;">Note: Please do not close this tab until restore completes.</div><?php
	}

	private function create_config_file($params, $is_staging = false) {

		$config = WPTC_Factory::get('config');
		$default_repo = $config->get_option('default_repo');

		if ($is_staging) {
			$this_config_like_file = '../wp-tcapsule-bridge/wp-tc-config.php';
			$default_repo = $params['default_repo'];
		} else {
			$this_config_like_file = 'wp-tc-config.php';
		}

		$db_username 	= 	isset( $params['db_username'] ) 	? $params['db_username'] 	: '' ;
		$db_password 	= 	isset( $params['db_password'] ) 	? $params['db_password'] 	: '' ;
		$db_name 		= 	isset( $params['db_name'] ) 		? $params['db_name'] 		: '' ;
		$db_host 		= 	isset( $params['db_host'] ) 		? $params['db_host'] 		: '' ;
		$db_prefix 		= 	isset( $params['db_prefix'] ) 		? $params['db_prefix'] 		: '' ;
		$db_charset 	= 	isset( $params['db_charset'] ) 		? $params['db_charset'] 	: '' ;
		$db_collate 	= 	isset( $params['db_collate'] ) 		? $params['db_collate'] 	: '' ;

		wptc_log($db_prefix, "-----create_config_file---db_prefix--------");

		if (empty($db_charset)) {
			$db_charset = 'utf8mb4';
		}
		if (empty($db_collate)) {
			$db_collate = '';
		}

		$contents_to_be_written = "
			<?php
			/** The name of the database for WordPress */
			if(!defined('DB_NAME'))
			define('DB_NAME', '" . $db_name . "');

			/** MySQL database username */
			if(!defined('DB_USER'))
			define('DB_USER', '" . $db_username . "');

			/** MySQL database password */
			if(!defined('DB_PASSWORD'))
			define('DB_PASSWORD', '" . $db_password . "');

			/** MySQL hostname */
			if(!defined('DB_HOST'))
			define('DB_HOST', '" . $db_host . "');

			/** Database Charset to use in creating database tables. */
			if(!defined('DB_CHARSET'))
			define('DB_CHARSET', '" . $db_charset . "');

			/** The Database Collate type. Don't change this if in doubt. */
			if(!defined('DB_COLLATE'))
			define('DB_COLLATE', '" . $db_collate . "');

			if(!defined('DB_PREFIX_WPTC'))
			define('DB_PREFIX_WPTC', '" . $db_prefix . "');

			if(!defined('DEFAULT_REPO'))
			define('DEFAULT_REPO', '" . $default_repo . "');

			if(!defined('BRIDGE_NAME_WPTC'))
			define('BRIDGE_NAME_WPTC', 'wp-tcapsule-bridge');

			if (!defined('WP_MAX_MEMORY_LIMIT')) {
				define('WP_MAX_MEMORY_LIMIT', '256M');
			}

			define( 'FS_METHOD', 'direct' );

			if(!defined('WP_DEBUG'))
			define('WP_DEBUG', false);
			if(!defined('WP_DEBUG_DISPLAY'))
			define('WP_DEBUG_DISPLAY', false);

			if ( !defined('MINUTE_IN_SECONDS') )
			define('MINUTE_IN_SECONDS', 60);
			if ( !defined('HOUR_IN_SECONDS') )
			define('HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS);
			if ( !defined('DAY_IN_SECONDS') )
			define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);
			if ( !defined('WEEK_IN_SECONDS') )
			define('WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS);
			if ( !defined('YEAR_IN_SECONDS') )
			define('YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS);



			/** Absolute path to the WordPress directory. */
			if ( !defined('ABSPATH') )
			define('ABSPATH',  wp_normalize_path(dirname(dirname(__FILE__)) . '/'));

			if ( !defined('WP_CONTENT_DIR') )
			define('WP_CONTENT_DIR',  wp_normalize_path(ABSPATH . 'wp-content'));

			if ( !defined('WPTC_ABSPATH') )
			define('WPTC_ABSPATH',  wp_normalize_path(dirname(dirname(__FILE__)) . '/'));

			if ( !defined('WPTC_WP_CONTENT_DIR') )
			define('WPTC_WP_CONTENT_DIR',  wp_normalize_path(ABSPATH . 'wp-content'));

			if ( !defined('WPTC_UPLOADS_DIR') )
			define('WPTC_UPLOADS_DIR',  wp_normalize_path(ABSPATH . 'wp-content/uploads'));

			if ( !defined('WPTC_RELATIVE_UPLOADS_DIR') )
			define('WPTC_RELATIVE_UPLOADS_DIR',  wp_normalize_path('/wp-content/uploads'));

			if ( !defined('WP_LANG_DIR') )
			define('WP_LANG_DIR',  wp_normalize_path(ABSPATH . 'wp-content/lang'));";

		$fh = fopen($this_config_like_file, 'w');

		if (empty($fh)) {
			return false;
		}

		if(!fwrite($fh, $contents_to_be_written)){
			return false;
		}

		fclose($fh);
		return true;
	}

	public function start_restore_tc_callback_bridge($req_data = '') {
		require_once dirname(__FILE__). '/' ."common-functions.php";
		require_once dirname(__FILE__). '/' .'wptc-constants.php';

		wptc_log('', "--------start_restore_tc_callback_bridge--------");

		reset_restore_related_settings_wptc();

		$config = WPTC_Factory::get('config');
		$config->set_option('current_bridge_file_name', 'wp-tcapsule-bridge');
		$config->set_option('is_bridge_restore', 'true');

		$old_wasabi_us_east = $config->get_option('wasabi_bucket_region');
		if($old_wasabi_us_east == 'us-east-1'){
			$config->set_option('wasabi_bucket_region', 'us-east-2');
		}

		$data = array();
		if (empty($req_data)) {
			if (isset($_POST['data'])) {
				$data = $_POST['data'];
			}
		} else {
			if (isset($req_data['data'])) {
				$data = $req_data['data'];
			}
		}

		$app_restore_functions = new WPTC_Restore_App_Functions();

		if (empty($data)) {
			$app_restore_functions->die_with_msg(array('error' => 'Post Data is missing.'));
		}

		try {

			//initializing restore options
			$config->set_option('wptc_profiling_start', time());
			$config->set_option('restore_action_id', time()); //main ID used througout the restore process
			$config->set_option('in_progress_restore', true);
			$config->set_option('restore_post_data', serialize($data));
			$this->add_migration_link_replacing($data);

			if ($data['is_latest_restore_point']) {
				wptc_log(array(),'-----------is latest restore point----------------');
				$config->set_option('is_latest_restore_point', true);
			} else {
				$config->set_option('is_latest_restore_point', false);
			}

			$config->create_dump_dir(array('is_bridge' => true)); //This will initialize wp_filesystem

			$app_restore_functions->die_with_msg(array('restoreInitiatedResult' => array('bridgeFileName' => BRIDGE_NAME_WPTC, 'safeToCallPluginAjax' => true)));
		} catch (Exception $e) {
			$app_restore_functions->die_with_msg(array('error' => $e->getMessage()));
		}

	}

	private function add_migration_link_replacing($data){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		if (empty($data['migration_url'])) {
			return ;
		}

		wptc_log(array(),'-----------Migration link saved----------------');
		$config = WPTC_Factory::get('config');
		$config->set_option('migration_url', $data['migration_url']);
	}

	private function load_upload_meta_html(){ ?>

	<div class="container" style="padding:10%">
		<h5 style="text-align:center;">Upload meta file</h5>
		<div class="row" style="position: relative;padding: 1rem;margin: 1rem -15px;border: 1px solid #e5e5e5;background-color: #fff;">
			<div class="col-sm-12" style="text-align: center; padding: 15px">
				<div style="font-size: 13px;padding: 15px;font-weight: 600;">(No restore points are found, Please upload meta file )</div>
				<button class="fileinput-button btn btn-primary btn_wptc" style="cursor: pointer;margin-left: 37%;">Upload meta file
					<input id="fileupload" type="file" name="files">
				</button>
				<div style="font-size: 13px;padding: 15px;"><a target="_blank" href="http://docs.wptimecapsule.com/article/31-how-to-restore-your-site-if-the-database-is-deleted">Where do I get meta file?</a></div>
				<div class="progress" style="display: none; margin-top: 25px;">
					<div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
				</div>
				<div id="files" class="files" style="font-size: 13px;"><p></p></div>
			</div>
		</div>
	</div><?php
	}

	private function load_upload_meta_script(){ ?>

		<script src="upload/js/jquery.min.js"></script>
		<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
		<script src="upload/js/jquery.ui.widget.js"></script>
		<!-- The basic File Upload plugin -->
		<script src="upload/js/jquery.fileupload.js"></script>
		<script >
			init_meta_upload_listener();
		</script>
		<?php
	}

	private function init_restore_app_functions(){
		//common app functions for restore
		require_once dirname(__FILE__). '/' ."wptc-restore-app-functions.php";
		$this->restore_app_functions = new WPTC_Restore_App_Functions();
		$this->restore_app_functions->init_db_connection();
		$this->restore_app_functions->start_request_time();
	}

	public function import_meta_file() {
		$this->include_config();
		$this->include_files();
		$this->initiate_database();
		$this->initiate_filesystem();
		$this->init_restore_app_functions();
		$this->validate_meta();
	}

	private function validate_meta(){
		wptc_log($_POST, '---------------$_POST-----------------');

		if (empty($_POST['data'])) {
			$this->restore_app_functions->die_with_msg(array('error' => 'Cannot find the meta file'));
		}

		$request = $_POST['data'];

		$file = dirname( __FILE__ ) . '/upload/php/files/' . $request['file'];

		if($request['position'] === 'uncompress'){
			$this->uncompress_meta_file($file);
		}

		if($request['position'] === 'import'){
			$this->process_import_meta_file($request, $file);
		}

		$this->restore_app_functions->die_with_msg(array('status' => 'error', 'msg' => 'position missing or undefined'));
	}

	private function uncompress_meta_file($file){
		if (!$this->restore_app_functions->is_gzip_available()) {
			$this->restore_app_functions->die_with_msg(array('error' => 'gzip not installed on this server so could not uncompress the sql file'));
		}

		if (strpos($file, '.gz') === false) {
			$this->restore_app_functions->die_with_msg(array('status' => 'continue', 'position' => 'import', 'offset' => 0));
		}

		$this->restore_app_functions->gz_uncompress_file($file, $offset = 0);
		$this->restore_app_functions->die_with_msg(array('status' => 'continue', 'position' => 'import', 'offset' => 0));
	}

	private function process_import_meta_file($request, $file){
		$this->restore_app_functions->init_other_objects();

		WPTC_Factory::get('config')->set_option('is_meta_restore_running', true);

		$this->restore_app_functions->set_additional_flags();

		$file = $this->restore_app_functions->remove_gz_ext_from_file($file);

		wptc_log($file, '---------------$file-----------------');
		$response = $this->restore_app_functions->import_sql_file($file, $request['offset'], $request['replace_collation']);

		wptc_log($response, '---------------$response-----------------');

		if (empty( $response ) || empty($response['status']) || $response['status'] === 'error') {
			$err = $response['status'] === 'error' ? $response['status']['msg'] : 'Unknown error during database import';
			$this->restore_app_functions->die_with_msg(array('error' => $err));
		}

		if ($response['status'] === 'continue') {
			$this->restore_app_functions->die_with_msg(array('status' => 'continue', 'position' => 'import', 'offset' => $response['offset'], 'replace_collation' => $response['replace_collation']));
		}

		reset_restore_related_settings_wptc();
		reset_backup_related_settings_wptc();

		WPTC_Factory::get('config')->set_option('is_meta_restore_running', false);

		if ($response['status'] === 'completed') {
			$this->restore_app_functions->die_with_msg(array('status' => 'completed'));
		}

		$this->restore_app_functions->die_with_msg(array('status' => 'completed'));
	}

	public function get_migration_data($url) {
		$this->restore_url = $url;

		$this->include_config();
		$this->include_files();
		$this->initiate_database();
		$this->initiate_filesystem();
		$this->init_restore_app_functions();
		$this->check_migtration_status();
	}

	private function check_migtration_status(){
		$site_url = WPTC_Factory::get('config')->get_option('site_url_wptc');
		$this->site_url = wptc_remove_trailing_slash($site_url);

		$this->restore_url = $this->trim_current_bridge_url();

		wptc_log($this->site_url,'-----------$this->site_url----------------');
		wptc_log($this->restore_url,'-----------$this->restore_url----------------');

		if ($this->site_url == $this->restore_url) {
			$this->restore_app_functions->die_with_msg(array('status' => 'not_required'));
		}

		$this->restore_app_functions->die_with_msg(array('status' => 'success', 'html' => $this->get_migration_html()), 'unescape_slashes');
	}

	private function trim_current_bridge_url(){
		return wptc_remove_trailing_slash(substr($this->restore_url, 0, strpos($this->restore_url, "wp-tcapsule-bridge")));
	}

	private function get_migration_html(){
		return $this->add_migration_alert() . $this->add_migration_settings();
	}

	private function add_migration_alert(){
		return '<div class="alert alert-warning" role="alert" style="font-size: 15px;">
				URL Mismatch Alert: The previously connected site URL (' . $this->site_url . ') does not match with the current restore URL (' . $this->restore_url . ') So all <strong> ' . $this->site_url . ' </strong> links will be replaced with <strong> ' . $this->restore_url . ' </strong> automatically and all the backup records will be deleted and you will be asked to start fresh backup from the beginning. If you do not want to replace the links, uncheck the checkbox below.
		</div>';
	}

	private function add_migration_settings(){
		return '<div>
					<span style=" position: relative; top: 7px;"> <input type="checkbox" id="bridge_replace_links" checked="" > <label for="bridge_replace_links" style="margin-left: 10px;">Replace links : </label> </span>
					<input type="text" class="form-control" id="replace_link_migration" style="width: 350px;float: right; margin-right: 660px;" value = "' . $this->restore_url . '">
					<div style="margin-left: 133px; font-size: 13px;"> All links will be replaced with this.</div>
				</div><br><br>';
	}

}
