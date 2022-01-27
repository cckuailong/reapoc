<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! function_exists('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

## Idle Session Logout: Client Browser based js Event Listener. 
/*
Notes: Logs Users out whether they are inactive|idle, have navigated to another Browser tab window or closed their Browser.
milliseconds: 60000 = 1 minute | 3600000 = 60 minutes
Applies to WordPress Post, Page & Comment page TinyMCE editors: 
Adding an Event Listener for the WordPress Post and Page Visual editing window or other instances of TinyMCE is not possible to do with TinyMCE outside of TinyMCE due to conflicting Event Listeners. If someone does NOT choose to disable ISL for TinyMCE instances, the native WP Confirm alert popup prevents the ISL redirect from occurring on WordPress Post and Page editors as long as some content has been entered into the TinyMCE visual editor in the Post and Page Editing windows. User is prompted to either allow the ISL redirect or cancel the ISL redirect if the most current Post or Page content has not been saved. If the most current content has been saved then the user is redirected by ISL and logged out of the site. The Event Listeners for the TinyMCE text textarea editor works normally.
Special Note for the WP Dashboard page:  An instance of TinyMCE loads in Dashboard > Activity > Comments section. ISL loads the standard js and not the disable ISL TinyMCE js.
Applies to all other instances of TinyMCE used by plugins and themes: 
wp_editor() instances Visual editor cannot have Event Listeners added outside of TinyMCE. If someone does NOT choose to disable ISL for TinyMCE instances, then if someone is idle for X minutes and focused in the TinyMCE Visual editor window and the ISL timeout fires then they will be logged out and all unsaved content in the TinyMCE Visual editing window will be lost.
TinyMCE Notes: 
Consistent element id to check for instances of TinyMCE: id='editor-buttons-css'
WordPress element id to check for Post, Page & Comment TinyMCE Editor: id="wp-content-editor-container"
.54.1: switched to Roles instead of using user_level
.54.1: Request URI Exclusion option|condition added
.54.2: Added Custom Roles
*/
##
function bpsPro_idle_session_logout() {
$BPS_ISL_options = get_option('bulletproof_security_options_idle_session');
	
	if ( $BPS_ISL_options['bps_isl'] == 'On' ) {
		
		if ( $BPS_ISL_options['bps_isl_timeout'] == '' ) {
			return;
		}

		$uri_exclusions = array_filter( explode( ', ', trim( $BPS_ISL_options['bps_isl_uri_exclusions'], ", \t\n\r") ) );

		if ( in_array( esc_html($_SERVER['REQUEST_URI']), $uri_exclusions ) ) {
			return;
		}

		global $current_user, $pagenow;
		$current_user = wp_get_current_user();
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);

		if ( @! preg_match( '/'.$current_user->user_login.'/i', $BPS_ISL_options['bps_isl_user_account_exceptions'], $matches ) ) {

			if ( $user_role == 'administrator' && $BPS_ISL_options['bps_isl_administrator'] == '1' || $user_role == 'editor' && $BPS_ISL_options['bps_isl_editor'] == '1' || $user_role == 'author' && $BPS_ISL_options['bps_isl_author'] == '1' || $user_role == 'contributor' && $BPS_ISL_options['bps_isl_contributor'] == '1' || $user_role == 'subscriber' && $BPS_ISL_options['bps_isl_subscriber'] == '1' ) {

			$timeout = $BPS_ISL_options['bps_isl_timeout'] * 60000;
				
			if ( $BPS_ISL_options['bps_isl_tinymce'] == '1' && 'index.php' != $pagenow ) {
				
?>

<script type="text/javascript">
/* <![CDATA[ */
window.addEventListener("load", function () {
     
	 var bpsTinymce = document.getElementById("editor-buttons-css");
	 var bpsTinymceContainer = document.getElementById("wp-content-editor-container");
	 
	 if (bpsTinymce == null && bpsTinymceContainer == null) {
		// Testing: Chrome + F12 Console tab		
		//console.log("TinyMCE null"); 

		// Fires when keyboard key is pressed for most keys
		document.addEventListener("keypress", bpsResetTimeout);
		// Fires when mouse is moved
		document.addEventListener("mousemove", bpsResetTimeout);
		// Fires when mouse button is pressed
		document.addEventListener("mousedown", bpsResetTimeout);
		// Fires when the mouse wheel is rolled up or down
		document.addEventListener("wheel", bpsResetTimeout);
		// Fires when a finger is placed on the touch surface/screen.
		document.addEventListener("touchstart", bpsResetTimeout);
		// Fires when a finger already placed on the screen is moved across the screen.
		document.addEventListener("touchmove", bpsResetTimeout);

	 } else {
		// Testing: Chrome + F12 Console tab		 
		//console.log("TinyMCE not null");
	 }
});

var bpsTimeout;

function bpsSessionExpired() {
	window.location.assign("<?php echo plugins_url('/bulletproof-security/isl-logout.php'); ?>");
}

function bpsResetTimeout() {
	clearTimeout(bpsTimeout);
	bpsTimeout = setTimeout(bpsSessionExpired, <?php echo json_encode( $timeout, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>);
	// Testing: Chrome + F12 Console tab
	//console.log("TinyMCE null Event logged");
}
/* ]]> */
</script>

<?php 
} else { // TinyMCE Editor checkbox is not checked
?>
		
<script type="text/javascript">
/* <![CDATA[ */
// Fires when keyboard key is pressed for most keys
document.addEventListener("keypress", bpsResetTimeout);
// Fires when mouse is moved
document.addEventListener("mousemove", bpsResetTimeout);
// Fires when mouse button is pressed
document.addEventListener("mousedown", bpsResetTimeout);
// Fires when the mouse wheel is rolled up or down
document.addEventListener("wheel", bpsResetTimeout);
// Fires when a finger is placed on the touch surface/screen.
document.addEventListener("touchstart", bpsResetTimeout);
// Fires when a finger already placed on the screen is moved across the screen.
document.addEventListener("touchmove", bpsResetTimeout);

var bpsTimeout;

function bpsSessionExpired() {
	window.location.assign("<?php echo plugins_url('/bulletproof-security/isl-logout.php'); ?>");
}

function bpsResetTimeout() {
	clearTimeout(bpsTimeout);
	bpsTimeout = setTimeout(bpsSessionExpired, <?php echo json_encode( $timeout, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>);
	// Testing: Chrome + F12 Console tab
	//console.log("Standard Event logged");
}
/* ]]> */
</script>	
	
<?php }			

		} elseif ( $user_role != 'administrator' && $user_role != 'editor' && $user_role != 'author' && $user_role != 'contributor' && $user_role != 'subscriber' ) {
				
			if ( ! $BPS_ISL_options['bps_isl_custom_roles'] ) {
				return;
			}
			
			foreach ( $BPS_ISL_options as $key => $value ) {
		
				if ( $key == 'bps_isl_custom_roles' && is_array($value) ) {
					
					foreach ( $value as $ckey => $cvalue ) {
						
						if ( $user_role == $ckey && $cvalue == '1' ) {
							$timeout = $BPS_ISL_options['bps_isl_timeout'] * 60000;
						} else {
							return;
						}
					}
				}
			}			

			if ( $BPS_ISL_options['bps_isl_tinymce'] == '1' && 'index.php' != $pagenow ) {

?>

<script type="text/javascript">
/* <![CDATA[ */
window.addEventListener("load", function () {
     
	 var bpsTinymce = document.getElementById("editor-buttons-css");
	 var bpsTinymceContainer = document.getElementById("wp-content-editor-container");
	 
	 if (bpsTinymce == null && bpsTinymceContainer == null) {
		// Testing: Chrome + F12 Console tab		
		//console.log("TinyMCE null"); 

		// Fires when keyboard key is pressed for most keys
		document.addEventListener("keypress", bpsResetTimeout);
		// Fires when mouse is moved
		document.addEventListener("mousemove", bpsResetTimeout);
		// Fires when mouse button is pressed
		document.addEventListener("mousedown", bpsResetTimeout);
		// Fires when the mouse wheel is rolled up or down
		document.addEventListener("wheel", bpsResetTimeout);
		// Fires when a finger is placed on the touch surface/screen.
		document.addEventListener("touchstart", bpsResetTimeout);
		// Fires when a finger already placed on the screen is moved across the screen.
		document.addEventListener("touchmove", bpsResetTimeout);

	 } else {
		// Testing: Chrome + F12 Console tab		 
		//console.log("TinyMCE not null");
	 }
});

var bpsTimeout;

function bpsSessionExpired() {
	window.location.assign("<?php echo plugins_url('/bulletproof-security/isl-logout.php'); ?>");
}

function bpsResetTimeout() {
	clearTimeout(bpsTimeout);
	bpsTimeout = setTimeout(bpsSessionExpired, <?php echo json_encode( $timeout, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>);
	// Testing: Chrome + F12 Console tab
	//console.log("TinyMCE null Event logged");
}
/* ]]> */
</script>

<?php 
} else { // TinyMCE Editor checkbox is not checked
?>
		
<script type="text/javascript">
/* <![CDATA[ */
// Fires when keyboard key is pressed for most keys
document.addEventListener("keypress", bpsResetTimeout);
// Fires when mouse is moved
document.addEventListener("mousemove", bpsResetTimeout);
// Fires when mouse button is pressed
document.addEventListener("mousedown", bpsResetTimeout);
// Fires when the mouse wheel is rolled up or down
document.addEventListener("wheel", bpsResetTimeout);
// Fires when a finger is placed on the touch surface/screen.
document.addEventListener("touchstart", bpsResetTimeout);
// Fires when a finger already placed on the screen is moved across the screen.
document.addEventListener("touchmove", bpsResetTimeout);

var bpsTimeout;

function bpsSessionExpired() {
	window.location.assign("<?php echo plugins_url('/bulletproof-security/isl-logout.php'); ?>");
}

function bpsResetTimeout() {
	clearTimeout(bpsTimeout);
	bpsTimeout = setTimeout(bpsSessionExpired, <?php echo json_encode( $timeout, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>);
	// Testing: Chrome + F12 Console tab
	//console.log("Standard Event logged");
}
/* ]]> */
</script>	
	
<?php }

} } } }

add_action('admin_notices', 'bpsPro_idle_session_logout');
add_action('network_admin_notices', 'bpsPro_idle_session_logout');
add_action('wp_footer', 'bpsPro_idle_session_logout');

?>