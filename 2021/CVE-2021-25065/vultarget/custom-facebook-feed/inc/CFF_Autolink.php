<?php
/**
 * Autolink
 *
 *
 * @since 2.19
 */

namespace CustomFacebookFeed;

class CFF_Autolink{


	static function cff_autolink($text, $link_color='', $span_tag = false, $limit=100, $tagfill='class="cff-break-word"', $auto_title = true){
	    $text = self::cff_autolink_do($text, $link_color, '![a-z][a-z-]+://!i',    $limit, $tagfill, $auto_title, $span_tag);
	    $text = self::cff_autolink_do($text, $link_color, '!(mailto|skype):!i',    $limit, $tagfill, $auto_title, $span_tag);
	    $text = self::cff_autolink_do($text, $link_color, '!www\\.!i',         $limit, $tagfill, $auto_title, 'http://', $span_tag);
	    return $text;
	}

	static function cff_autolink_do($text, $link_color, $sub, $limit, $tagfill, $auto_title, $span_tag, $force_prefix=null){

	    $text_l = StrToLower($text);
	    $cursor = 0;
	    $loop = 1;
	    $buffer = '';

	    $autolink_options = [
	        # Should http:// be visibly stripped from the front
	        # of URLs?
	        'strip_protocols' => true,
	    ];

	    while (($cursor < strlen($text)) && $loop){

	        $ok = 1;
	        $matched = preg_match($sub, $text_l, $m, PREG_OFFSET_CAPTURE, $cursor);

	        if (!$matched){

	            $loop = 0;
	            $ok = 0;

	        }else{

	            $pos = $m[0][1];
	            $sub_len = strlen($m[0][0]);

	            $pre_hit = substr($text, $cursor, $pos-$cursor);
	            $hit = substr($text, $pos, $sub_len);
	            $pre = substr($text, 0, $pos);
	            $post = substr($text, $pos + $sub_len);

	            $fail_text = $pre_hit.$hit;
	            $fail_len = strlen($fail_text);

	            #
	            # substring found - first check to see if we're inside a link tag already...
	            #

	            $bits = preg_split("!</a>!i", $pre);
	            $last_bit = array_pop($bits);
	            if (preg_match("!<a\s!i", $last_bit)){

	                #echo "fail 1 at $cursor<br />\n";

	                $ok = 0;
	                $cursor += $fail_len;
	                $buffer .= $fail_text;
	            }
	        }

	        #
	        # looks like a nice spot to autolink from - check the pre
	        # to see if there was whitespace before this match
	        #

	        if ($ok){

	            if ($pre){
	                if (!preg_match('![\s\(\[\{>]$!s', $pre)){

	                    #echo "fail 2 at $cursor ($pre)<br />\n";

	                    $ok = 0;
	                    $cursor += $fail_len;
	                    $buffer .= $fail_text;
	                }
	            }
	        }

	        #
	        # we want to autolink here - find the extent of the url
	        #

	        if ($ok){
	            if (preg_match('/^([a-z0-9\-\.\/\-_%~!?=,:;&+*#@\(\)\$]+)/i', $post, $matches)){

	                $url = $hit.$matches[1];

	                $cursor += strlen($url) + strlen($pre_hit);
	                $buffer .= $pre_hit;

	                $url = html_entity_decode($url);


	                #
	                # remove trailing punctuation from url
	                #

	                while (preg_match('|[.,!;:?]$|', $url)){
	                    $url = substr($url, 0, strlen($url)-1);
	                    $cursor--;
	                }
	                foreach (array('()', '[]', '{}') as $pair){
	                    $o = substr($pair, 0, 1);
	                    $c = substr($pair, 1, 1);
	                    if (preg_match("!^(\\$c|^)[^\\$o]+\\$c$!", $url)){
	                        $url = substr($url, 0, strlen($url)-1);
	                        $cursor--;
	                    }
	                }


	                #
	                # nice-i-fy url here
	                #

	                $link_url = $url;
	                $display_url = $url;

	                if ($force_prefix) $link_url = $force_prefix.$link_url;

	                if ($autolink_options['strip_protocols']){
	                    if (preg_match('!^(http|https)://!i', $display_url, $m)){

	                        $display_url = substr($display_url, strlen($m[1])+3);
	                    }
	                }

	                $display_url = self::cff_autolink_label($display_url, $limit);


	                #
	                # add the url
	                #

	                if ($display_url != $link_url && !preg_match('@title=@msi',$tagfill) && $auto_title) {

	                    $display_quoted = preg_quote($display_url, '!');

	                    if (!preg_match("!^(http|https)://{$display_quoted}$!i", $link_url)){

	                        $tagfill .= ' title="'.$link_url.'"';
	                    }
	                }

	                $link_url_enc = HtmlSpecialChars($link_url);
	                $display_url_enc = HtmlSpecialChars($display_url);


	                if( substr( $link_url_enc, 0, 4 ) !== "http" ) $link_url_enc = 'http://' . $link_url_enc;
	                $buffer .= "<a href=\"{$link_url_enc}\" rel='nofollow noopener noreferrer'>{$display_url_enc}</a>";


	            }else{
	                #echo "fail 3 at $cursor<br />\n";

	                $ok = 0;
	                $cursor += $fail_len;
	                $buffer .= $fail_text;
	            }
	        }

	    }

	    #
	    # add everything from the cursor to the end onto the buffer.
	    #

	    $buffer .= substr($text, $cursor);

	    return $buffer;
	}


	static function cff_autolink_label($text, $limit){

	    if (!$limit){ return $text; }

	    if (strlen($text) > $limit){
	        return substr($text, 0, $limit-3).'...';
	    }

	    return $text;
	}


	static function cff_autolink_email($text, $tagfill=''){

	    $atom = '[^()<>@,;:\\\\".\\[\\]\\x00-\\x20\\x7f]+'; # from RFC822

	    #die($atom);

	    $text_l = StrToLower($text);
	    $cursor = 0;
	    $loop = 1;
	    $buffer = '';

	    while(($cursor < strlen($text)) && $loop){

	        #
	        # find an '@' symbol
	        #

	        $ok = 1;
	        $pos = strpos($text_l, '@', $cursor);

	        if ($pos === false){

	            $loop = 0;
	            $ok = 0;

	        }else{

	            $pre = substr($text, $cursor, $pos-$cursor);
	            $hit = substr($text, $pos, 1);
	            $post = substr($text, $pos + 1);

	            $fail_text = $pre.$hit;
	            $fail_len = strlen($fail_text);

	            #die("$pre::$hit::$post::$fail_text");

	            #
	            # substring found - first check to see if we're inside a link tag already...
	            #

	            $bits = preg_split("!</a>!i", $pre);
	            $last_bit = array_pop($bits);
	            if (preg_match("!<a\s!i", $last_bit)){

	                #echo "fail 1 at $cursor<br />\n";

	                $ok = 0;
	                $cursor += $fail_len;
	                $buffer .= $fail_text;
	            }
	        }

	        #
	        # check backwards
	        #

	        if ($ok){
	            if (preg_match("!($atom(\.$atom)*)\$!", $pre, $matches)){

	                # move matched part of address into $hit

	                $len = strlen($matches[1]);
	                $plen = strlen($pre);

	                $hit = substr($pre, $plen-$len).$hit;
	                $pre = substr($pre, 0, $plen-$len);

	            }else{

	                #echo "fail 2 at $cursor ($pre)<br />\n";

	                $ok = 0;
	                $cursor += $fail_len;
	                $buffer .= $fail_text;
	            }
	        }

	        #
	        # check forwards
	        #

	        if ($ok){
	            if (preg_match("!^($atom(\.$atom)*)!", $post, $matches)){

	                # move matched part of address into $hit

	                $len = strlen($matches[1]);

	                $hit .= substr($post, 0, $len);
	                $post = substr($post, $len);

	            }else{
	                #echo "fail 3 at $cursor ($post)<br />\n";

	                $ok = 0;
	                $cursor += $fail_len;
	                $buffer .= $fail_text;
	            }
	        }

	        #
	        # commit
	        #

	        if ($ok) {

	            $cursor += strlen($pre) + strlen($hit);
	            $buffer .= $pre;
	            $buffer .= "<a href=\"mailto:$hit\"$tagfill>$hit</a>";

	        }

	    }

	    #
	    # add everything from the cursor to the end onto the buffer.
	    #

	    $buffer .= substr($text, $cursor);

	    return $buffer;
	}


}