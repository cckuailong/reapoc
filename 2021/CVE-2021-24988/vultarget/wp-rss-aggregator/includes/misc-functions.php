<?php
/**
 * WPRSS Let To Num
 *
 * Does Size Conversions
 *
 * @since 3.1
 * @author Chris Christoff
 * @return $ret
 */
function wprss_let_to_num( $v ) {
	$l   = substr( $v, -1 );
	$ret = substr( $v, 0, -1 );

	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
		case 'T':
			$ret *= 1024;
		case 'G':
			$ret *= 1024;
		case 'M':
			$ret *= 1024;
		case 'K':
			$ret *= 1024;
			break;
	}

	return $ret;
}




/**
 * An enhanced version of WP's media_sideload_image function.
 *
 * If media_sideload_image fails, the file is downloaded manually
 * as an image, inserted as an attachment, and attached to the post.
 * 
 * @since 3.5.1
 */
function wprss_media_sideload_image( $file, $post_id, $desc = null ) {
	try {

		if ( ! empty( $file ) ) {

			// Download file to temp location
			$tmp = download_url( $file );

			// Set variables for storage
			// fix file filename for query strings
			preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );

			if ( count( $matches ) > 0 ) {
				$file_array['name'] = basename( $matches[0] );
			}
			else {
				preg_match( '/[\/\?\=\&]([^\/\?\=\&]*)[\?]*$/i', $file, $matches2 );
				if ( count( $matches2 ) > 1 ) {
					$file_array['name'] = $matches2[1] . '.png';
				} else {
					@unlink( $tmp );
					return "<img src='$file' alt='' />";
				}
			}
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				$file_array['tmp_name'] = '';
			}

			// do the validation and storage stuff
			$id = media_handle_sideload( $file_array, $post_id, $desc );
			// If error storing permanently, unlink
			if ( is_wp_error($id) ) {
				@unlink( $file_array['tmp_name'] );
				return "<img src='$file' alt='' />";
			}

			$src = wp_get_attachment_url( $id );
		}

		// Finally check to make sure the file has been saved, then return the html
		if ( ! empty( $src ) ) {
			$alt = isset( $desc )? esc_attr($desc) : '';
			$html = "<img src='$src' alt='$alt' />";
			return $html;
		}

	}
	catch( Exception $e ) {
		return "<img src='$file' alt='' />";
	}
}


/**
 * A list of void tags, e.g. tags that don't require a closing tag,
 * also known as self-closing tags.
 * 
 * @since 4.2.7
 * @link http://stackoverflow.com/questions/13915201/what-tags-in-html5-are-acknowledged-of-being-self-closing
 * @return array An array where values are tag names.
 */
function wprss_html5_get_void_tags() {
	return apply_filters( 'wprss_html5_void_tags', array(
		'area',
		'base',
		'br',
		'col',
		'command',
		'embed',
		'hr',
		'img',
		'input',
		'keygen',
		'link',
		'meta',
		'param',
		'source',
		'track',
		'wbr',
		'basefont',
		'bgsound',
		'frame',
		'isindex'
	));
}


/**
 * Trims the given text by a fixed number of words, and preserving HTML.
 *
 * Collapses all white space, trims the text up to a certain number of words, and
 * preserves all HTML markup. HTML tags do not count as words.
 * Uses WordPress `wp_trim_words` internally.
 * Uses mostly trivial regex. Works by removing, then re-adding tags.
 * Just as well closes open tags by counting them.
 * 
 * @param string $text The text to trim.
 * @param string $max_words The maximum number of words.
 * @param array $allowed_tags The allows tags. Regular array of tag names.
 * @return string The trimmed text.
 */
function wprss_trim_words( $text, $max_words, $allowed_tags = array(), $self_closing_tags = null ) {	
	// See http://haacked.com/archive/2004/10/25/usingregularexpressionstomatchhtml.aspx/
	$html_regex = <<<EOS
(</?(\w+)(?:(?:\s+\w+(?:\s*=\s*(?:".*?"|'.*?'|[^'">\s]+))?)+\s*|\s*)/?>)
EOS;
	$html_regex_str = sprintf ('!%1$s!', $html_regex );
	// Collapsing single-line white space
	$text = preg_replace( '!\s+!', ' ', $text );

	// Tags that are always self-closing
	if ( is_null( $self_closing_tags ) ) {
		$self_closing_tags = function_exists('wprss_html5_get_void_tags')
				? array_flip( wprss_html5_get_void_tags() )
				: array();
	}
	
	// Enum of tag types
	$tag_type = array(
		'opening'		=> 1,
		'closing'		=> 2,
		'self-closing'	=> 0
	);
	
	/*
	 * Split text using tags as delimiters.
	 * The resulting array is a sequence of elements as follows:
	 * 	0 - The complete tag that it was delimited by
	 * 	1 - The name of that tag
	 * 	2 - The text that follows it until the next tag
	 * 
	 * Each element contains 2 indexes:
	 * 	0 - The element content
	 * 	1 - The position in the original string, at which it was found
	 *
	 * For instance:
	 *		<span>hello</span> how do <em>you do</em>?
	 *
	 * Will result in an array (not actaul structure) containing:
	 * <span>, span, hello, </span>, span, how do, <em>, em, you do, </em>, em, ?
	 */
	$text_array = preg_split(
		$html_regex_str,				// Match HTML Regex above
		$text,							// Split the text
		-1,								// No split limit
		// FLAGS
			PREG_SPLIT_DELIM_CAPTURE	// Capture delimiters (html tags)
		|	PREG_SPLIT_OFFSET_CAPTURE	// Record the string offset of each part
	);
	/*
	 * Get first element of the array (leading text with no HTML), and add it to a string.
	 * This string will contain the plain text (no HTML) only after the follow foreach loop.
	 */
	$text_start = array_shift( $text_array );
	$plain_text = $text_start[0];

	/*
	 * Chunk the array in groups of 3. This will take each 3 consecutive elements
	 * and group them together.
	 */
	$pieces = array_chunk( $text_array, 3 );


	/*
	 * Iterate over each group and:
	 *	1. Generate plain text without HTML
	 *	2. Add apropriate tag type to each group
	 */
	foreach ( $pieces as $_idx => $_piece ) {
		// Get the data
		$tag_piece = $_piece[0];
		$text_piece = $_piece[2];
		$tag_name = $_piece[1][0];
		// Compile all plain text together
		$plain_text .= $text_piece[0];
		// Check the tag and assign the proper tag type
		$tag = $tag_piece[0];
		$pieces[ $_idx ][1][2] =
			( substr( $tag, 0, 2 ) === '</' ) ?
				$tag_type['closing'] :
			( (substr( $tag, strlen( $tag ) - 2, 2 ) === '/>'
			|| array_key_exists( $tag_name, $self_closing_tags)) ?
				$tag_type['self-closing'] :
				$tag_type['opening'] );
	}

	// Stock trimming of words
	$plain_text = wp_trim_words_wprss( $plain_text, $max_words, '' );

	/*
	 * Put the tags back, using the offsets recorded
	 * This is where the sweet magic happens
	 */

	// Cache to only check `in_array` once for each tag type
	$allowed_tags_cache = array();
	// For counting open tags
	$tags_to_close = array();
	// Since some tags will not be included...
	$tag_position_offset = 0;
	$text = $plain_text;

	// Iterate the groups once more
	foreach ( $pieces as $_idx => $_piece ) {
		// Tag and tagname
		$_tag_piece = $_piece[0];
		$_tag_name_piece = $_piece[1];
		// Name of the tag
		$_tag_name = strtolower( $_tag_name_piece[0] );
		// Tag type
		$_tag_type = $_tag_name_piece[2];
		// Text of the tag
		$_tag = $_tag_piece[0];
		// Position of the tag in the original string
		$_tag_position = $_tag_piece[1];
		$_actual_tag_position = $_tag_position - $tag_position_offset;

		// Caching result
		if ( !isset( $allowed_tags_cache[$_tag_name] ) )
			$allowed_tags_cache[$_tag_name] = in_array( $_tag_name, $allowed_tags );

		// Whether to stop (tag position is outside the trimmed text)
		if( $_actual_tag_position >= strlen( $text ) ) break;

		// Whether to skip tag
		if ( !$allowed_tags_cache[$_tag_name] ) {
			$tag_position_offset += strlen( $_tag ); // To correct for removed chars
			continue;
		}

		// If the tag is an opening tag, record it in $tags_to_close
		if( $_tag_type === $tag_type['opening'] )
			array_push( $tags_to_close, $_tag_name );
		// If it is a closing tag, remove it from $tags_to_close
		elseif( $_tag_type === $tag_type['closing'] )
			array_pop( $tags_to_close );

		// Inserting tag back into place
		$text = substr_replace( $text, $_tag, $_actual_tag_position, 0);
	}

	// Add the appropriate closing tags to all unclosed tags
	foreach( $tags_to_close as $_tag_name ) {
		$text .= sprintf('</%1$s>', $_tag_name);
	}
	
	return $text;
}


/**
 * Clone of wp_trim_words, without using the PREG_SPLIT_NO_EMPTY flag for preg_split
 * 
 * Trims text to a certain number of words.
 * This function is localized. For languages that count 'words' by the individual
 * character (such as East Asian languages), the $num_words argument will apply
 * to the number of individual characters.
 *
 * @param string $text Text to trim.
 * @param int $num_words Number of words. Default 55.
 * @param string $more Optional. What to append if $text needs to be trimmed. Default '&hellip;'.
 * @return string Trimmed text.
 */
function wp_trim_words_wprss( $text, $num_words = 55, $more = null ) {
	if ( null === $more ) {
		$more = __( '&hellip;' );
	}
	$original_text = $text;
	/* translators: If your word count is based on single characters (East Asian characters),
	   enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
	if ( 'characters' == _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
		$sep = '';
	} else {
		$words_array = preg_split( "/[\n\r\t ]/", $text, $num_words + 1 );
		$sep = ' ';
	}
	if ( count( $words_array ) > $num_words ) {
		array_pop( $words_array );
		$text = implode( $sep, $words_array );
		$text = $text . $more;
	} else {
		$text = implode( $sep, $words_array );
	}
	/**
	 * Filter the text content after words have been trimmed.
	 *
	 * @since 3.3.0
	 *
	 * @param string $text          The trimmed text.
	 * @param int    $num_words     The number of words to trim the text to. Default 5.
	 * @param string $more          An optional string to append to the end of the trimmed text, e.g. &hellip;.
	 * @param string $original_text The text before it was trimmed.
	 */
	return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
}


function wprss_validate_url( $url ) {
	$expression =
	'(' .                           # Capture 1: entire matched URL
		'(?:' .
			'[a-z][\w-]+:' .                # URL protocol and colon
			'(?:' .
				'/{1,3}' .							# 1-3 slashes
				'|' .								#   or
				'a-z0-9%' .							# Single letter or digit or '%'
											# (Trying not to match e.g. "URI::Escape")
			')' .
			'|' .                           #   or
			'www\d{0,3}[.]' .               # "www.", "www1.", "www2." … "www999."
			'|' .                           #   or
			'[a-z0-9.\-]+[.][a-z]{2,4}/' .  # looks like domain name followed by a slash
		')' . 
		'(?:' .								# One or more:
			'[^\s()<>]+' .							# Run of non-space, non-()<>
			'|' .									#   or
			'\(([^\s()<>]+|(\([^\s()<>]+\)))*\)' .	# balanced parens, up to 2 levels
		')+' .
		'(?:' .									# End with:
			'\(([^\s()<>]+|(\([^\s()<>]+\)))*\)' .  # balanced parens, up to 2 levels
			'|' .                                   #   or
			'[^\s`\!()\[\]{};:\'".,<>?«»“”‘’]' .		# not a space or one of these punct chars
		')' .
	')';
	
	return preg_match('!' . $expression . '!', $url) ? $url : null;
}

if (!function_exists('wprss_verify_nonce'))
{
    /**
     * Check if a WP nonce sent in a reques is valid.
     *
     * @since 4.9
     * @see wp_verify_nonce()
     * @param string $action ID of the action, for which checking the nonce.
     * @param string $queryArg Name of the key in the $_REQUEST global
     *  which contains the nonce value.
     * @return bool|int False if nonce invalid, 1 if it's the first 12 hours of
     *  validity, 2 if the second 12 hours.
     */
    function wprss_verify_nonce($action, $queryArg)
    {
        return isset($_REQUEST[$queryArg])
                ? wp_verify_nonce($_REQUEST[$queryArg], $action)
                : false;
    }
}

/**
 * Formats a hook callback into a readable string.
 *
 * @param array $callback A callback entry.
 *
 * @return string The callback name.
 */
function wprss_format_hook_callback(array $callback)
{
    // Break static strings: "Example::method"
    // into arrays: ["Example", "method"]
    if (is_string($callback['function']) && (strpos($callback['function'], '::') !== false)) {
        $callback['function'] = explode('::', $callback['function']);
    }

    if (is_array($callback['function'])) {
        if (is_object($callback['function'][0])) {
            $class = get_class($callback['function'][0]);
            $access = '->';
        } else {
            $class = $callback['function'][0];
            $access = '::';
        }

        $callback['name'] = $class . $access . $callback['function'][1] . '()';
    } elseif (is_object($callback['function'])) {
        if (is_a($callback['function'], 'Closure')) {
            $callback['name'] = 'Closure';
        } else {
            $class = get_class($callback['function']);

            $callback['name'] = $class . '->__invoke()';
        }
    } else {
        $callback['name'] = $callback['function'] . '()';
    }

    return $callback['name'];
}

/**
 * Retrieves the file extension from a URI.
 *
 * @since 4.18
 *
 * @param string $uri The URI
 *
 * @return string|null The file extension or null if it could not be determined.
 */
function wpra_get_uri_extension($uri)
{
    $path = parse_url($uri, PHP_URL_PATH);

    if (!$path || empty($path)) {
        return null;
    }

    return strtolower(pathinfo($path, PATHINFO_EXTENSION));
}
