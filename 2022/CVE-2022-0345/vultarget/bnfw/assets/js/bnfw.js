jQuery(document).ready(function($) {
	var multisiteNotifications = [
		'multisite-site-created',
		'multisite-new-user-created',
		'multisite-network-admin-email-change-attempted',
		'multisite-network-admin-email-changed',
		'multisite-site-welcome',
		'multisite-site-deleted',
		'multisite-site-admin-email-change-attempted',
		'multisite-site-admin-email-changed',
		'multisite-site-registered',
		'multisite-new-user-welcome',
		'multisite-new-user-invited'
	];

    function toggle_fields() {
    	var show_fields = $('#show-fields').is(":checked");
        var notification = $( "#notification" ).val();
        if('user-login' === notification){
    	if ( show_fields ) {
			$('#email, #reply').show();
            } else {
			$('#email, #reply').hide();
            }
        }else{
            if ( show_fields ) {
			$('#email, #cc, #bcc, #reply').show();
    	} else {
			$('#email, #cc, #bcc, #reply').hide();
    	}
        }
	    $( '#subject-wrapper' ).show();
    }

    function toggle_users() {
    	if ( $( '#only-post-author' ).is( ':checked' ) ) {
    		$( '#current-user' ).hide();
    	} else {
    		$( '#current-user' ).show();
    	}

	    var notification = $( '#notification' ).val();
        var check_comment = notification.split('-');
	    if ( 'new-comment' === notification || 'approve' === check_comment[0] || 'moderate-comment' === notification ) {
		    $( '#current-user' ).show();
	    }
    }

	function init() {
		var notification = $('#notification').val();

		$("#notification, .bnfw-select2").select2();

		$(".user-select2").select2({
			tags: BNFW.enableTags,
            tokenSeparators: BNFW.enabletokenSeparators
		} );

		$(".user-ajax-select2").select2( {
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				data: function( params ) {
					return {
						action: 'bnfw_search_users',
						query: params.term,
						page: params.page
					};
				},
				processResults: function( data, page ) {
					return {
						results: data
					};
				}
			},
			minimumInputLength: 1,
			tags: BNFW.enableTags
		} );

		if ( ! $( '#notification' ).length ) {
			return;
		}

		toggle_fields();

        var check_comment = notification.split('-');

		if ( 'reply-comment' === notification || notification.startsWith( 'commentreply-' ) ||
				'new-user' === notification || 'welcome-email' === notification || 'user-password' === notification ||
				'password-changed' === notification || 'email-changed' === notification || 'email-changing' === notification || 'user-role' === notification ||
				'ca-export-data' === notification || 'ca-erase-data' === notification ||
				'uc-export-data' === notification || 'uc-erase-data' === notification ||
				'data-export' === notification || 'data-erased' === notification ||
				'multisite-new-user-invited' === notification || 'multisite-new-user-created' === notification || 'multisite-new-user-welcome' === notification ||
				'multisite-site-registered' === notification || 'multisite-site-welcome' === notification ||
				'multisite-site-created' === notification || 'multisite-site-deleted' === notification ||
				'multisite-site-admin-email-change-attempted' === notification || 'multisite-site-admin-email-changed' === notification ||
				'multisite-network-admin-email-change-attempted' === notification || 'multisite-network-admin-email-changed' === notification) {

			$('#toggle-fields, #email, #cc, #bcc, #reply, #users, #exclude-users, #current-user, #post-author').hide();
			$('#user-password-msg, #disable-autop, #email-formatting').show();

			$( '#subject-wrapper' ).show();
			if ( 'multisite-new-user-created' === notification || 'multisite-site-created' === notification || 'multisite-site-deleted' === notification ||
					'multisite-site-admin-email-change-attempted' === notification  || 'multisite-network-admin-email-change-attempted' === notification ||
					'uc-export-data' === notification || 'uc-erase-data' === notification || 'data-export' === notification ||
					'ca-export-data' === notification || 'ca-erase-data' === notification || 'email-changing' === notification ) {

				$( '#subject-wrapper' ).hide();
			}

			if ( 'uc-export-data' === notification || 'uc-erase-data' === notification || 'data-export' === notification ||
				'ca-export-data' === notification || 'ca-erase-data' === notification || 'data-erased' === notification || ( -1 !== multisiteNotifications.indexOf( notification ) ) ) {

				$( '#email-formatting' ).hide();
			}
		} else if ( 'new-comment' === notification || 'approve' === check_comment[0] || notification.startsWith( 'moderate-comment-' ) || 'new-trackback' === notification || 'new-pingback' === notification ||
				'admin-password' === notification || 'admin-user' === notification || 'admin-role' === notification ) {

			if ( 'new-comment' === notification || 'approve' === check_comment[0] || notification.startsWith( 'moderate-comment-' ) || 'new-trackback' === notification || 'new-pingback' === notification ) {
				$('#post-author').show();
			} else {
				$('#post-author').hide();
			}

			$('#toggle-fields, #users, #exclude-users, #email-formatting, #disable-autop, #current-user').show();
			toggle_fields();
			toggle_users();
			$( '#user-password-msg' ).hide();
		} else if ( 'admin-password-changed' === notification || 'admin-email-changed' === notification || 'core-updated' === notification ) {
			$( '#toggle-fields, #users, #exclude-users, #email-formatting, #disable-autop' ).show();
			toggle_fields();
			toggle_users();
			$( '#user-password-msg, #current-user, #post-author' ).hide();
		} else if ('user-login' === notification){ 
			$('#cc, #bcc, #users, #exclude-users, #current-user, #post-author').hide();
			$('#toggle-fields').show();
		} else {
			$('#toggle-fields, #users, #exclude-users, #email-formatting, #disable-autop, #current-user, #post-author').show();
			toggle_fields();
			toggle_users();
			$('#user-password-msg').hide();
		}
	}

	init();

	/**
	 * Show a warning message if a notification is configured for more than 200 emails.
	 */
	$( '#users-select' ).on( 'change', function () {
		var emailCount = $( '#users-select' ).find( ':selected' ).length,
			$msg = $( '#users-count-msg' );

		if ( emailCount > 200 ) {
			$msg.show();
		} else {
			$msg.hide();
		}
	} );

    $('#notification').on('change', function() {
		var $this = $(this),
			notification = $this.val();

        var check_comment = notification.split('-');

		if ( 'reply-comment' === notification || notification.startsWith( 'commentreply-' ) ||
			'new-user' === notification || 'welcome-email' === notification || 'user-password' === notification ||
			'password-changed' === notification || 'email-changed' === notification || 'email-changing' === notification || 'user-role' === notification ||
			'ca-export-data' === notification || 'ca-erase-data' === notification ||
			'uc-export-data' === notification || 'uc-erase-data' === notification ||
			'data-export' === notification || 'data-erased' === notification ||
			'multisite-new-user-invited' === notification || 'multisite-new-user-created' === notification || 'multisite-new-user-welcome' === notification ||
			'multisite-site-registered' === notification || 'multisite-site-welcome' === notification ||
			'multisite-site-created' === notification || 'multisite-site-deleted' === notification ||
			'multisite-site-admin-email-change-attempted' === notification || 'multisite-site-admin-email-changed' === notification ||
			'multisite-network-admin-email-change-attempted' === notification || 'multisite-network-admin-email-changed' === notification) {

			$('#toggle-fields, #email, #cc, #bcc, #reply, #users, #exclude-users, #current-user, #post-author').hide();
			$('#user-password-msg, #disable-autop, #email-formatting').show();

			$( '#subject-wrapper' ).show();
			if ( 'multisite-new-user-created' === notification || 'multisite-site-created' === notification || 'multisite-site-deleted' === notification ||
					'multisite-site-admin-email-change-attempted' === notification  || 'multisite-network-admin-email-change-attempted' === notification ||
					'uc-export-data' === notification || 'uc-erase-data' === notification || 'data-export' === notification ||
					'ca-export-data' === notification || 'ca-erase-data' === notification || 'email-changing' === notification ) {

				$( '#subject-wrapper' ).hide();
			}

			if ( 'uc-export-data' === notification || 'uc-erase-data' === notification || 'data-export' === notification ||
				'ca-export-data' === notification || 'ca-erase-data' === notification || 'data-erased' === notification || ( -1 !== multisiteNotifications.indexOf( notification ) ) ) {

				$( '#email-formatting' ).hide();
			}
		} else if ( 'new-comment' === notification || 'approve' === check_comment[0] ||
					notification.startsWith( 'moderate-comment-' ) || 'new-trackback' === notification || 'new-pingback' === notification ||
					'admin-password' === notification || 'admin-user' === notification || 'admin-role' === notification ) {

			if ( 'new-comment' === notification || 'approve' === check_comment[0] || notification.startsWith( 'moderate-comment-' ) || 'new-trackback' === notification || 'new-pingback' === notification ) {
				$('#post-author').show();
			} else {
				$('#post-author').hide();
			}

			$('#toggle-fields, #users, #exclude-users, #email-formatting, #disable-autop, #current-user').show();
			$('#user-password-msg').hide();
			toggle_fields();
			toggle_users();
		} else if ( 'admin-password-changed' === notification || 'admin-email-changed' === notification || 'core-updated' === notification ) {
			$( '#toggle-fields, #users, #exclude-users, #email-formatting, #disable-autop' ).show();
			toggle_fields();
			toggle_users();
			$( '#user-password-msg, #current-user, #post-author' ).hide();
		} else if ('user-login' === notification){ 
			$('#cc, #bcc, #users, #exclude-users, #current-user, #post-author').hide();
			$('#toggle-fields').show();
		} else {
			$('#toggle-fields, #users, #exclude-users, #email-formatting, #disable-autop, #current-user, #post-author').show();
			$('#user-password-msg').hide();
			toggle_fields();
			toggle_users();
		}
    });

    $('#show-fields').change(function() {
    	toggle_fields();
    });

    $( '#only-post-author' ).change(function() {
		toggle_users();
	} );

	// send test email
	$( '#test-email' ).click(function() {
		$( '#send-test-email' ).val( 'true' );
	});

	// Validate before saving notification
	$( '#publish' ).click(function() {
		if ( $('#users').is(':visible') ) {
			if ( null === $(BNFW.validation_element).val() && $('#only-post-author:checked').length <= 0 ) {
				$('#bnfw_error').remove();
				$('.wrap h1').after('<div class="error" id="bnfw_error"><p>' + BNFW.empty_user + '</p></div>');
				return false;
			}
		}

		return true;
	});

	$( '#shortcode-help' ).on( 'click', function() {
		var notification = $( '#notification' ).val(),
			notification_slug = '',
			splited;

		switch( notification ) {
			case 'new-comment':
			case 'new-trackback':
			case 'new-pingback':
			case 'reply-comment':
			case 'commentreply-page':
			case 'user-password':
			case 'admin-password':
			case 'admin-password-changed':
			case 'admin-email-changed':
			case 'password-changed':
			case 'email-changed':
			case 'email-changing':
			case 'new-user':
			case 'user-login':
			case 'admin-user-login':
			case 'welcome-email':
			case 'user-role':
			case 'admin-role':
			case 'admin-user':
			case 'new-post':
			case 'core-updated':
			case 'update-post':
			case 'pending-post':
			case 'future-post':
			case 'newterm-category':
			case 'new-media':
			case 'comment-attachment':
			case 'update-media':
			case 'newterm-post_tag':
				notification_slug = notification;
				break;

			default:
				splited = notification.split( '-' );
				switch( splited[0] ) {
					case 'new':
						notification_slug = 'new-post';
						break;
					case 'update':
						notification_slug = 'update-post';
						break;
					case 'pending':
						notification_slug = 'pending-post';
						break;
					case 'private':
						notification_slug = 'private-post';
						break;
					case 'future':
						notification_slug = 'future-post';
						break;
					case 'comment':
						notification_slug = 'new-comment';
						break;
                    case 'approve':
						notification_slug = 'approve-comment';
						break;
					case 'moderate':
						notification_slug = 'moderate-comment';
						break;
					case 'commentreply':
						notification_slug = 'reply-comment';
						break;
					case 'newterm':
						notification_slug = 'newterm-category';
						break;
					// ideally these should be in the add-ons. But hardcoding them here for now
					case 'customfield':
						notification_slug = 'customfield-post';
						break;
					case 'updatereminder':
						notification_slug = 'updatereminder-post';
						break;

					default:
						notification_slug = notification;
						break;
				}

				break;
		}

		$(this).attr( 'href', 'https://betternotificationsforwp.com/documentation/notifications/shortcodes/?notification=' + notification_slug + '&utm_source=WP%20Admin%20Notification%20Editor%20-%20"Shortcode%20Help"&utm_medium=referral' );
	});

	/**
	 * Insert Default Message for notification.
	 */
	$( '#insert-default-msg' ).on( 'click', function() {
		var notification = $( '#notification' ).val(),
			subject = '',
			body = '';

		switch ( notification ) {
			case 'new-comment':
			case 'moderate-comment':
			case 'new-trackback':
			case 'new-pingback':
			case 'reply-comment':
				subject = '[[global_site_title]] Comment: "[post_title]"';
				body = 'New comment on your post "[post_title]"<br>' +
					'Author: [comment_author] (IP address: [comment_author_IP]) <br>' +
					'Email: [comment_author_email] <br>' +
				    'URL: [comment_author_url] <br>' +
					'Comment: <br> ' +
					'[comment_content] <br>' +
					'<br>' +
					'You can see all comments on this post here: <br>' +
					'[permalink]#comments';

				break;

			case 'admin-user':
				subject = '[[global_site_title]] New User Registration';
				body = 'New user registration on your site [global_site_title]: <br>' +
					'Username: [user_login] <br>' +
					'E-mail: [user_email]';

				break;

			case 'admin-password-changed':
				subject = '[[global_site_title]] Password Changed';
				body = 'Password changed for user: [user_login] <br>';

				break;

			case 'user-password':
				subject = '[[global_site_title]] Password Reset';
				body = 'Someone has requested a password reset for the following account: <br>' +
					'Site Name: [global_site_title] <br>' +
					'Username: [email_user_login] <br>' +
					'If this was a mistake, just ignore this email and nothing will happen. <br>' +
					'To reset your password, visit the following address: [password_reset_link]';

				break;

			case 'password-changed':
				subject = '[[global_site_title]] Notice of Password Change';
				body = 'Hi [email_user_login], <br>' +
					'<br>' +
					'This notice confirms that your password was changed on [global_site_title].' +
					'<br><br>' +
					'If you did not change your password, please contact the Site Administrator at [admin_email] <br>' +
					'<br>' +
					'This email has been sent to [global_user_email]' +
					'<br>' +
					'Regards, <br>' +
					'All at [global_site_title] <br>' +
					'[global_site_url]';
				break;

			case 'email-changing':
				subject = '[[global_site_title]] New Email Address';
				body = 'Hi [user_nicename], <br>' +
					'<br>' +
					'You recently requested to have the email address on your account changed.' +
					'<br>' +
					'If this is correct, please click on the following link to change it:' +
					'<br>' +
					'[global_site_url]/wp-admin/profile.php' +
					'<br>' +
					'You can safely ignore and delete this email if you do not want to take this action.' +
					'<br>' +
					'This email has been sent to [global_user_email]' +
					'<br>' +
					'Regards, <br>' +
					'All at [global_site_title] <br>' +
					'[global_site_url]';
				break;

			case 'email-changed':
				subject = '[[global_site_title]] Notice of Email Change';
				body = 'Hi [user_nicename], <br>' +
					'<br>' +
					'This notice confirms that your email address on [global_site_title] was changed to [user_email].' +
					'<br>' +
					'If you did not change your email, please contact the Site Administrator at [admin_email] <br>' +
					'<br>' +
					'This email has been sent to [global_user_email]' +
					'<br>' +
					'Regards, <br>' +
					'All at [global_site_title] <br>' +
					'[global_site_url]';
				break;

			case 'new-user':
				subject = '[[global_site_title]] Your username and password info';
				body = 'Username: [user_login] <br>' +
					'To set your password, visit the following address: [password_url]';

				break;

			case 'multisite-new-user-invited':
				subject = '[[network_name] Activate [user_login]';
				body = 'To activate your user, please click the following link:' +
					'<br>' +
					'[activation_link]' +
					'<br>' +
					'After you activate, you will receive *another email* with your login.';

				break;

			case 'ca-export-data':
				subject = '[[global_site_title]] Confirm Action: Export Personal Data';
				body = 'Howdy,' +
					'<br>' +
					'<br>' +
				'A request has been made to perform the following action on your account:' +
					'<br>' +
					'<br>' +
				'[data_request_type]' +
					'<br>' +
					'<br>' +
				'To confirm this, please click on the following link:' +
					'<br>' +
					'<br>' +
				'[request_confirmation_link]' +
					'<br>' +
					'<br>' +
				'You can safely ignore and delete this email if you do not want to' +
					'<br>' +
				'take this action.' +
					'<br>' +
					'<br>' +
				'This email has been sent to [global_user_email].' +
					'<br>' +
					'<br>' +
				'Regards,' +
					'<br>' +
					'All at [global_site_title]' +
					'<br>' +
				'[global_site_url]';

				break;

			case 'ca-erase-data':
				subject = '[[global_site_title]] Confirm Action: Erase Personal Data';
				body = 'Howdy,' +
					'<br>' +
					'<br>' +
					'A request has been made to perform the following action on your account:' +
					'<br>' +
					'<br>' +
					'[data_request_type]' +
					'<br>' +
					'<br>' +
					'To confirm this, please click on the following link:' +
					'<br>' +
					'<br>' +
					'[request_confirmation_link]' +
					'<br>' +
					'<br>' +
					'You can safely ignore and delete this email if you do not want to' +
					'<br>' +
					'take this action.' +
					'<br>' +
					'<br>' +
					'This email has been sent to [global_user_email].' +
					'<br>' +
					'<br>' +
					'Regards,' +
					'<br>' +
					'All at [global_site_title]' +
					'<br>' +
					'[global_site_url]';

				break;

			case 'uc-export-data':
				subject = 'Action Confirmed';

				body = 'Howdy,' +
					'<br>' +
					'<br>' +
					'A user data privacy request has been confirmed on [global_site_title]:' +
					'<br>' +
					'<br>' +
					'User: [email_user_email]' +
					'<br>' +
					'Request: [data_request_type]' +
					'<br>' +
					'<br>' +
					'You can view and manage these data privacy requests here:' +
					'<br>' +
					'<br>' +
					'[data_privacy_requests_url]' +
					'<br>' +
					'<br>' +
					'Regards,' +
					'<br>' +
					'All at [global_site_title]' +
					'<br>' +
					'[global_site_url]';
				break;

			case 'uc-erase-data':
				subject = 'Action Confirmed';

				body = 'Howdy,' +
					'<br>' +
					'<br>' +
					'A user data privacy request has been confirmed on [global_site_title]:' +
					'<br>' +
					'<br>' +
					'User: [email_user_email]' +
					'<br>' +
					'Request: [data_request_type]' +
					'<br>' +
					'<br>' +
					'You can view and manage these data privacy requests here:' +
					'<br>' +
					'<br>' +
					'[data_privacy_requests_url]' +
					'<br>' +
					'<br>' +
					'Regards,' +
					'<br>' +
					'All at [global_site_title]' +
					'<br>' +
					'[global_site_title]';
				break;

			case 'data-export':
				subject = 'Personal Data Export';

				body = 'Howdy,' +
					'<br>' +
					'<br>' +
					'Your request for an export of personal data has been completed. You may' +
					'<br>' +
					'download your personal data by clicking on the link below. For privacy' +
					'<br>' +
					'and security, we will automatically delete the file on [data_privacy_download_expiry],' +
					'<br>' +
					'so please download it before then.' +
					'<br>' +
					'<br>' +
					'[data_privacy_download_url]' +
					'<br>' +
					'<br>' +
					'This email has been sent to [global_user_email].' +
					'<br>' +
					'<br>' +
					'Regards,' +
					'<br>' +
					'All at [global_site_title]' +
					'<br>' +
					'[global_site_url]';
				break;

			case 'data-erased':
				subject = '[sitename] Erasure Request Fulfilled';

				body = 'Howdy,' +
					'<br>' +
					'<br>' +
					'Your request to erase your personal data on [sitename] has been completed.' +
					'<br>' +
					'If you have any follow-up questions or concerns, please contact the site administrator.' +
					'<br>' +
					'<br>' +
					'Regards,' +
					'<br>' +
					'All at [global_site_title]' +
					'<br>' +
					'[global_site_url]';
				break;
			default:
				alert( "This is a new notification that is not available in WordPress by default and has been added by Better Notifications for WP. As such, it doesn't have any default content." );
				break;
		}

		if ( subject !== '' ) {
			$( '#subject' ).val( subject );
		}

		if ( body !== '' ) {
			if ( tinyMCE && tinyMCE.editors && tinyMCE.editors['notification_message'] ) {
				tinyMCE.editors['notification_message'].selection.setContent( body );
			}
		}

		return false;
	} );
});
