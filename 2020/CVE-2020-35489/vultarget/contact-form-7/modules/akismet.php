<?php
/**
** Akismet Filter
** Akismet API: http://akismet.com/development/api/
**/

add_filter( 'wpcf7_spam', 'wpcf7_akismet', 10, 2 );

function wpcf7_akismet( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	if ( ! wpcf7_akismet_is_available() ) {
		return false;
	}

	if ( ! $params = wpcf7_akismet_submitted_params() ) {
		return false;
	}

	$c = array();

	$c['comment_author'] = $params['author'];
	$c['comment_author_email'] = $params['author_email'];
	$c['comment_author_url'] = $params['author_url'];
	$c['comment_content'] = $params['content'];

	$c['blog'] = get_option( 'home' );
	$c['blog_lang'] = get_locale();
	$c['blog_charset'] = get_option( 'blog_charset' );
	$c['user_ip'] = $_SERVER['REMOTE_ADDR'];
	$c['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$c['referrer'] = $_SERVER['HTTP_REFERER'];
	$c['comment_type'] = 'contact-form';
	$c['comment_date_gmt'] = $submission->get_meta( 'timestamp' );

	if ( $permalink = get_permalink() ) {
		$c['permalink'] = $permalink;
	}

	$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );

	foreach ( $_SERVER as $key => $value ) {
		if ( ! in_array( $key, (array) $ignore ) ) {
			$c["$key"] = $value;
		}
	}

	if ( wpcf7_akismet_comment_check( $c ) ) {
		$spam = true;

		$submission->add_spam_log( array(
			'agent' => 'akismet',
			'reason' => __( "Akismet returns a spam response.", 'contact-form-7' ),
		) );
	} else {
		$spam = false;
	}

	return $spam;
}

function wpcf7_akismet_is_available() {
	if ( is_callable( array( 'Akismet', 'get_api_key' ) ) ) {
		return (bool) Akismet::get_api_key();
	}

	return false;
}

function wpcf7_akismet_submitted_params() {
	$params = array(
		'author' => '',
		'author_email' => '',
		'author_url' => '',
		'content' => '',
	);

	$has_akismet_option = false;

	foreach ( (array) $_POST as $key => $val ) {
		if ( '_wpcf7' == substr( $key, 0, 6 )
		or '_wpnonce' == $key ) {
			continue;
		}

		if ( is_array( $val ) ) {
			$val = implode( ', ', wpcf7_array_flatten( $val ) );
		}

		$val = trim( $val );

		if ( 0 == strlen( $val ) ) {
			continue;
		}

		if ( $tags = wpcf7_scan_form_tags( array( 'name' => $key ) ) ) {
			$tag = $tags[0];

			$akismet = $tag->get_option( 'akismet',
				'(author|author_email|author_url)', true );

			if ( $akismet ) {
				$has_akismet_option = true;

				if ( 'author' == $akismet ) {
					$params[$akismet] = trim( $params[$akismet] . ' ' . $val );
					continue;
				} elseif ( '' == $params[$akismet] ) {
					$params[$akismet] = $val;
					continue;
				}
			}
		}

		$params['content'] .= "\n\n" . $val;
	}

	if ( ! $has_akismet_option ) {
		return false;
	}

	$params['content'] = trim( $params['content'] );

	return $params;
}

function wpcf7_akismet_comment_check( $comment ) {
	$spam = false;
	$query_string = wpcf7_build_query( $comment );

	if ( is_callable( array( 'Akismet', 'http_post' ) ) ) {
		$response = Akismet::http_post( $query_string, 'comment-check' );
	} else {
		return $spam;
	}

	if ( 'true' == $response[1] ) {
		$spam = true;
	}

	if ( $submission = WPCF7_Submission::get_instance() ) {
		$submission->akismet = array( 'comment' => $comment, 'spam' => $spam );
	}

	return apply_filters( 'wpcf7_akismet_comment_check', $spam, $comment );
}
