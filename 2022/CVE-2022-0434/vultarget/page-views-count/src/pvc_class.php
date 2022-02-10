<?php
namespace A3Rev\PageViewsCount;

class A3_PVC
{

	public static $chart_icon = '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="chart-bar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-chart-bar fa-w-16 fa-2x"><path fill="currentColor" d="M396.8 352h22.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-192 0h22.4c6.4 0 12.8-6.4 12.8-12.8V140.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h22.4c6.4 0 12.8-6.4 12.8-12.8V204.8c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zM496 400H48V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16zm-387.2-48h22.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-22.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8z" class=""></path></svg>';

	public static $eye_icon = '<svg xmlns="http://www.w3.org/2000/svg" version="1.0" viewBox="0 0 502 315" preserveAspectRatio="xMidYMid meet"><g transform="translate(0,332) scale(0.1,-0.1)" fill="" stroke="none"><path d="M2394 3279 l-29 -30 -3 -207 c-2 -182 0 -211 15 -242 39 -76 157 -76 196 0 15 31 17 60 15 243 l-3 209 -33 29 c-26 23 -41 29 -80 29 -41 0 -53 -5 -78 -31z"/><path d="M3085 3251 c-45 -19 -58 -50 -96 -229 -47 -217 -49 -260 -13 -295 52 -53 146 -42 177 20 16 31 87 366 87 410 0 70 -86 122 -155 94z"/><path d="M1751 3234 c-13 -9 -29 -31 -37 -50 -12 -29 -10 -49 21 -204 19 -94 39 -189 45 -210 14 -50 54 -80 110 -80 34 0 48 6 76 34 21 21 34 44 34 59 0 14 -18 113 -40 219 -37 178 -43 195 -70 221 -36 32 -101 37 -139 11z"/><path d="M1163 3073 c-36 -7 -73 -59 -73 -102 0 -56 133 -378 171 -413 34 -32 83 -37 129 -13 70 36 67 87 -16 290 -86 209 -89 214 -129 231 -35 14 -42 15 -82 7z"/><path d="M3689 3066 c-15 -9 -33 -30 -42 -48 -48 -103 -147 -355 -147 -375 0 -98 131 -148 192 -74 13 15 57 108 97 206 80 196 84 226 37 273 -30 30 -99 39 -137 18z"/><path d="M583 2784 c-38 -19 -67 -74 -58 -113 9 -42 211 -354 242 -373 16 -10 45 -18 66 -18 51 0 107 52 107 100 0 39 -1 41 -124 234 -80 126 -108 162 -133 173 -41 17 -61 16 -100 -3z"/><path d="M4250 2784 c-14 -9 -74 -91 -133 -183 -95 -150 -107 -173 -107 -213 0 -55 33 -94 87 -104 67 -13 90 8 211 198 130 202 137 225 78 284 -27 27 -42 34 -72 34 -22 0 -50 -8 -64 -16z"/><path d="M2275 2693 c-553 -48 -1095 -270 -1585 -649 -135 -104 -459 -423 -483 -476 -23 -49 -22 -139 2 -186 73 -142 361 -457 571 -626 285 -228 642 -407 990 -497 242 -63 336 -73 660 -74 310 0 370 5 595 52 535 111 1045 392 1455 803 122 121 250 273 275 326 19 41 19 137 0 174 -41 79 -309 363 -465 492 -447 370 -946 591 -1479 653 -113 14 -422 18 -536 8z m395 -428 c171 -34 330 -124 456 -258 112 -119 167 -219 211 -378 27 -96 24 -300 -5 -401 -72 -255 -236 -447 -474 -557 -132 -62 -201 -76 -368 -76 -167 0 -236 14 -368 76 -213 98 -373 271 -451 485 -162 444 86 934 547 1084 153 49 292 57 452 25z m909 -232 c222 -123 408 -262 593 -441 76 -74 138 -139 138 -144 0 -16 -233 -242 -330 -319 -155 -123 -309 -223 -461 -299 l-81 -41 32 46 c18 26 49 83 70 128 143 306 141 649 -6 957 -25 52 -61 116 -79 142 l-34 47 45 -20 c26 -10 76 -36 113 -56z m-2057 25 c-40 -58 -105 -190 -130 -263 -110 -324 -59 -707 132 -981 25 -35 42 -64 37 -64 -19 0 -241 119 -326 174 -188 122 -406 314 -532 468 l-58 71 108 103 c185 178 428 349 672 473 66 33 121 60 123 61 2 0 -10 -19 -26 -42z"/><path d="M2375 1950 c-198 -44 -350 -190 -395 -379 -18 -76 -8 -221 19 -290 114 -284 457 -406 731 -260 98 52 188 154 231 260 27 69 37 214 19 290 -38 163 -166 304 -326 360 -67 23 -215 33 -279 19z"/></g></svg>';

	public static function upgrade_version_1_2(){
		global $wpdb;
		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_total CHANGE `postnum` `postnum` VARCHAR( 255 ) NOT NULL";
		$wpdb->query($sql);

		$sql = "ALTER TABLE ". $wpdb->prefix . "pvc_daily CHANGE `postnum` `postnum` VARCHAR( 255 ) NOT NULL";
		$wpdb->query($sql);
	}
	public static function install_database(){
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( !empty($wpdb->charset) ) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( !empty($wpdb->collate) ) $collate .= " COLLATE $wpdb->collate";
		}

		$sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "pvc_daily" ." (
         `id` mediumint(9) NOT NULL AUTO_INCREMENT,
		 `time` date DEFAULT '0000-00-00' NOT NULL,
		 `postnum` varchar(255) NOT NULL,
		 `postcount` int DEFAULT '0' NOT NULL,
		 UNIQUE KEY id (id)) $collate;";

		$wpdb->query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "pvc_total" ." (
			 `id` mediumint(9) NOT NULL AUTO_INCREMENT,
			 `postnum` varchar(255) NOT NULL,
			 `postcount` int DEFAULT '0' NOT NULL,
			 UNIQUE KEY id (id)) $collate;";

		$wpdb->query($sql);
	}

	public static function pvc_fetch_posts_stats( $post_ids ) {
		global $wpdb;
		$nowisnow = date('Y-m-d');

		if ( !is_array( $post_ids ) ) $post_ids = array( $post_ids );

		$sql = $wpdb->prepare( "SELECT t.postnum AS post_id, t.postcount AS total, d.postcount AS today FROM ". $wpdb->prefix . "pvc_total AS t
			LEFT JOIN ". $wpdb->prefix . "pvc_daily AS d ON t.postnum = d.postnum
			WHERE t.postnum IN ( ".implode( ',', $post_ids )." ) AND d.time = %s", $nowisnow );
		return $wpdb->get_results($sql);
	}

	public static function pvc_fetch_post_counts( $post_id ) {
		$total = self::pvc_fetch_post_total( $post_id );
		$today = self::pvc_fetch_post_today( $post_id );

		$post_counts        = new \stdClass();
		$post_counts->total = 0;
		$post_counts->today = 0;

		if ( ! empty( $total ) ) {
			$post_counts->total = $total;
		}

		if ( ! empty( $today ) ) {
			$post_counts->today = $today;
		}

		return $post_counts;
	}

	public static function pvc_fetch_post_total( $post_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT postcount AS total FROM ". $wpdb->prefix . "pvc_total
			WHERE postnum = %s", $post_id );
		return $wpdb->get_var($sql);
	}

	public static function pvc_fetch_post_today( $post_id ) {
		global $wpdb;
		$nowisnow = date('Y-m-d');

		$sql = $wpdb->prepare( "SELECT postcount AS today FROM ". $wpdb->prefix . "pvc_daily
			WHERE postnum = %s AND time = %s", $post_id, $nowisnow );
		return $wpdb->get_var($sql);
	}

	public static function pvc_stats_update($post_id) {
		global $wpdb;

		// get the local time based off WordPress setting
		$nowisnow = date('Y-m-d');

		// first try and update the existing total post counter
		$results = $wpdb->query( $wpdb->prepare( "UPDATE ". $wpdb->prefix . "pvc_total SET postcount = postcount+1 WHERE postnum = '%s' LIMIT 1", $post_id ) );

		// error_log( json_encode( $results ) );

		// if it doesn't exist, then insert two new records
		// one in the total views, another in today's views
		if ($results == 0) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO ". $wpdb->prefix . "pvc_total (postnum, postcount) VALUES ('%s', 1)", $post_id ) );
			$wpdb->query( $wpdb->prepare ( "INSERT INTO ". $wpdb->prefix . "pvc_daily (time, postnum, postcount) VALUES ('%s', '%s', 1)", $nowisnow, $post_id ) );
		// post exists so let's just update the counter
		} else {
			$results2 = $wpdb->query( $wpdb->prepare ( "UPDATE ". $wpdb->prefix . "pvc_daily SET postcount = postcount+1 WHERE time = '%s' AND postnum = '%s' LIMIT 1", $nowisnow, $post_id ) );
			// insert a new record since one hasn't been created for current day
			if ($results2 == 0)
				$wpdb->query( $wpdb->prepare( "INSERT INTO ". $wpdb->prefix . "pvc_daily (time, postnum, postcount) VALUES ('%s', '%s', 1)", $nowisnow, $post_id ) );
		}

	}

	public static function pvc_stats_manual_update( $post_id, $new_total_views, $new_today_views ) {
		global $wpdb;

		// get the local time based off WordPress setting
		$nowisnow = date('Y-m-d');

		// if it doesn't exist, then insert new record
		// one in the total views
		if ( '1' != $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM ". $wpdb->prefix . "pvc_total WHERE postnum = %s LIMIT 0, 1 )", $post_id ) ) ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO ". $wpdb->prefix . "pvc_total ( postnum, postcount ) VALUES ( %s, %d )", $post_id, $new_total_views ) );
		// post exists so let's just update the counter
		} else {
			$wpdb->query( $wpdb->prepare( "UPDATE ". $wpdb->prefix . "pvc_total SET postcount = %d WHERE postnum = %s LIMIT 1", $new_total_views, $post_id ) );
		}

		// if it doesn't exist, then insert new record
		// one in today's views
		if ( '1' != $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM ". $wpdb->prefix . "pvc_daily WHERE time = %s AND postnum = %s LIMIT 0, 1 )", $nowisnow, $post_id ) ) ) {
			$wpdb->query( $wpdb->prepare ( "INSERT INTO ". $wpdb->prefix . "pvc_daily ( time, postnum, postcount ) VALUES ( %s, %s, %d )", $nowisnow, $post_id, $new_today_views ) );
		// post exists so let's just update the counter
		} else {
			$wpdb->query( $wpdb->prepare ( "UPDATE ". $wpdb->prefix . "pvc_daily SET postcount = %d WHERE time = %s AND postnum = %s LIMIT 1", $new_today_views, $nowisnow, $post_id ) );
		}
	}

	public static function pvc_get_stats( $post_id, $views_type = 'all' ) {
		global $wpdb;
		global $pvc_settings;

		$output_html = '';
		// get all the post view info to display
		$total = self::pvc_fetch_post_total( $post_id );
		$today = self::pvc_fetch_post_today( $post_id );

		$output_html .= $pvc_settings['total_text_before'] . '&nbsp;' . number_format( $total ) . '&nbsp;' . $pvc_settings['total_text_after'];

		if ( empty( $views_type ) ) {
			$views_type = $pvc_settings['views_type'];
		}

		if ( 'all' === $views_type && ! empty( $today ) ) {
			$output_html .= ', ';

			$output_html .= $pvc_settings['today_text_before'] . '&nbsp;' . number_format( $today ) . '&nbsp;' . $pvc_settings['today_text_after'];
		}

		$output_html = apply_filters( 'pvc_filter_get_stats', $output_html, $post_id );

		return $output_html;
	}

	// get the total page views and daily page views for the post
	public static function pvc_stats_counter( $post_id, $increase_views = false, $attributes = array() ) {
		global $wpdb;
		global $pvc_settings;

		$load_by_ajax_update_class = '';
		if ( $increase_views ) $load_by_ajax_update_class = 'pvc_load_by_ajax_update';

		// get the stats and
		$html = '<div class="pvc_clear"></div>';

		$custom_class = '';
		if ( ! empty( $attributes['className'] ) ) {
			$custom_class = $attributes['className'];
		}

		$custom_style = '';
		if ( ! empty( $attributes['align'] ) ) {
			switch ( $attributes['align'] ) {
				case 'center':
					$custom_style = 'text-align:center;float:none;';
					break;
				case 'right':
					$custom_style = 'float:right;';
					break;
				default:
					$custom_style = 'float:left;';
					break;
			}
		}

		$views_type = '';
		if ( ! empty( $attributes['views_type'] ) ) {
			$views_type = esc_attr( $attributes['views_type'] );
		} else {
			$views_type = $pvc_settings['views_type'];
		}

		if ( $pvc_settings['enable_ajax_load'] == 'yes' && empty( $attributes['in_editor'] ) ) {
			$stats_html = '<p id="pvc_stats_'.$post_id.'" class="pvc_stats '. $views_type .' '. $custom_class .' '.$load_by_ajax_update_class.'" data-element-id="'.$post_id.'" style="'. $custom_style .'"><i class="pvc-stats-icon '.$pvc_settings['icon_size'].'" aria-hidden="true">'. ( 'eye' == $pvc_settings['icon'] ? self::$eye_icon : self::$chart_icon ) .'</i> <img src="'.A3_PVC_URL.'/ajax-loader.gif" border=0 /></p>';
		} else {
			$stats_html = '<p class="pvc_stats '. $views_type .' '. $custom_class .'" data-element-id="'.$post_id.'" style="'. $custom_style .'"><i class="pvc-stats-icon '.$pvc_settings['icon_size'].'" aria-hidden="true">'. ( 'eye' == $pvc_settings['icon'] ? self::$eye_icon : self::$chart_icon ) .'</i> ' . self::pvc_get_stats( $post_id, $views_type ) . '</p>';
		}

		$html .= apply_filters( 'pvc_filter_stats', $stats_html, $post_id );
		$html .= '<div class="pvc_clear"></div>';
		return $html;
	}

	public static function register_plugin_scripts() {
		global $pvc_settings;
		global $pvc_api;

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'a3-pvc-style', A3_PVC_CSS_URL . '/style'.$suffix.'.css', array(), A3_PVC_VERSION );

		if ( $pvc_settings['enable_ajax_load'] != 'yes' ) return;

	?>
	<?php // @codingStandardsIgnoreStart ?>
	<?php // phpcs:disable ?>
    <!-- PVC Template -->
    <script type="text/template" id="pvc-stats-view-template">
    <i class="pvc-stats-icon <?php echo esc_attr( $pvc_settings['icon_size'] ); ?>" aria-hidden="true"><?php echo ( 'eye' == $pvc_settings['icon'] ? self::$eye_icon : self::$chart_icon ); ?></i> 
	<?php echo esc_html( $pvc_settings['total_text_before'] ); ?> <%= total_view %> <?php echo esc_html( $pvc_settings['total_text_after'] ); ?>
	<% if ( today_view > 0 ) { %>
		<span class="views_today">, <?php echo esc_html( $pvc_settings['today_text_before'] ); ?> <%= today_view %> <?php echo esc_html( $pvc_settings['today_text_after'] ); ?></span>
	<% } %>
	</span>
	</script>
	<?php // phpcs:enable ?>
	<?php // @codingStandardsIgnoreEnd ?>
    <?php
		wp_enqueue_script( 'a3-pvc-backbone', A3_PVC_JS_URL . '/pvc.backbone'.$suffix.'.js', array( 'jquery', 'backbone', 'underscore' ), A3_PVC_VERSION );
		wp_localize_script( 'a3-pvc-backbone', 'vars', array( 'rest_api_url' => rest_url( '/' . $pvc_api->namespace ) ) );
	}

	public static function pvc_remove_stats($content) {
		remove_action('the_content', array( __CLASS__, 'pvc_stats_show'));
		return $content;
	}

	public static function pvc_stats_show($content){
		remove_action('loop_end', array( __CLASS__, 'pvc_stats_echo'));
		remove_action('genesis_before_post_content', array( __CLASS__, 'genesis_pvc_stats_echo'));
		remove_action('genesis_after_post_content', array( __CLASS__, 'genesis_pvc_stats_echo'));
		global $post;
		if ( ! $post ) return;

		$args=array(
			  'public'   => true,
			  '_builtin' => false
			);
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator );

		global $pvc_settings;
		if ( empty( $pvc_settings ) ) {
			$pvc_settings = get_option('pvc_settings', array() );
		}

		if ( self::pvc_is_activated( $post->ID ) ) {
			if ( is_singular() || is_singular( $post_types ) ) {
				if ( ! isset( $pvc_settings['enable_ajax_load'] ) || $pvc_settings['enable_ajax_load'] != 'yes' ) {
					self::pvc_stats_update($post->ID);
				}
				$pvc_stats_html = self::pvc_stats_counter($post->ID, true );
			} else {
				$pvc_stats_html = self::pvc_stats_counter($post->ID);
			}

			if ( 'top' == $pvc_settings['position'] ) {
				$content = $pvc_stats_html . $content;
			} else {
				$content .= $pvc_stats_html;
			}
		}
		return $content;
	}

	public static function excerpt_pvc_stats_show($excerpt){
		remove_action('loop_end', array( __CLASS__, 'pvc_stats_echo'));
		remove_action('genesis_before_post_content', array( __CLASS__, 'genesis_pvc_stats_echo'));
		remove_action('genesis_after_post_content', array( __CLASS__, 'genesis_pvc_stats_echo'));
		global $post;
		if ( ! $post ) return;
		global $pvc_settings;
		if ( empty( $pvc_settings ) ) {
			$pvc_settings = get_option('pvc_settings', array() );
		}
		if ( self::pvc_is_activated( $post->ID ) && 'no' != $pvc_settings['show_on_excerpt_content'] ) {
			$pvc_stats_html = self::pvc_stats_counter($post->ID);
			if ( 'top' == $pvc_settings['position'] ) {
				$excerpt = $pvc_stats_html . $excerpt;
			} else {
				$excerpt .= $pvc_stats_html;
			}
		}
		return $excerpt;
	}

	public static function pvc_stats_echo(){
		global $post;
		global $pvc_settings;
		if ( empty( $pvc_settings ) ) {
			$pvc_settings = get_option('pvc_settings', array() );
		}
		if ( self::pvc_is_activated( $post->ID ) && 'no' != $pvc_settings['show_on_excerpt_content'] ) {
			echo self::pvc_stats_counter($post->ID);
		}
	}

	public static function genesis_pvc_stats_echo(){
		remove_action('loop_end', array( __CLASS__, 'pvc_stats_echo'));
		global $post;
		global $pvc_settings;
		if ( empty( $pvc_settings ) ) {
			$pvc_settings = get_option('pvc_settings', array() );
		}
		if ( self::pvc_is_activated( $post->ID ) && 'no' != $pvc_settings['show_on_excerpt_content'] ) {
			echo self::pvc_stats_counter($post->ID);
		}
	}

	public static function custom_stats_echo($postid=0, $have_echo = 1, $attributes = array() ){
		if($have_echo == 1)
			echo self::pvc_stats_counter($postid, false, $attributes );
		else
			return self::pvc_stats_counter($postid, false, $attributes );
	}

	public static function custom_stats_update_echo($postid=0, $have_echo=1, $attributes = array() ){
		$output = '';
		global $pvc_settings;
		if ( empty( $pvc_settings ) ) {
			$pvc_settings = get_option('pvc_settings', array() );
		}
		if ( ! isset( $pvc_settings['enable_ajax_load'] ) || $pvc_settings['enable_ajax_load'] != 'yes' ) {
			self::pvc_stats_update($postid);
		}

		$output .= self::pvc_stats_counter($postid, true, $attributes );

		if ( $have_echo == 1 )
			echo $output;
		else
			return $output;
	}

	public static function pvc_is_activated( $post_id = 0 ) {
		if ( 0 == $post_id || '' == trim( $post_id ) ) {
			global $post;
			if ( $post ) {
				$post_id = $post->ID;
			} else {
				return false;
			}
		}

		global $pvc_settings;
		if ( empty( $pvc_settings ) ) {
			$pvc_settings = get_option( 'pvc_settings', array() );
		}

		// Don't show counter on homepage, frontpage, archive page if admin deactivate for excerpt
		if( 'no' == $pvc_settings['show_on_excerpt_content'] && ( is_home() || is_front_page() || is_archive() || is_tax() ) ) {
			return false;
		}

		$is_acticvated = self::pvc_admin_is_activated( $post_id );

		return $is_acticvated;
	}

	public static function pvc_admin_is_activated( $post_id = 0 ) {
		if ( $post_id < 0 ) {
			return false;
		}

		$post_type = get_post_type( $post_id );
		if ( false == $post_type ) {
			return false;
		}

		$is_activated = get_post_meta( $post_id, '_a3_pvc_activated', true );
		if ( empty( $is_activated ) ) {
			global $pvc_settings;
			if ( empty( $pvc_settings ) ) {
				$pvc_settings = get_option( 'pvc_settings', array() );
			}
			if ( isset( $pvc_settings['post_types'] ) && in_array( $post_type, (array) $pvc_settings['post_types'] ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			if ( 'true' == $is_activated ) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}

	public static function pvc_reset_individual_items() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_a3_pvc_activated' " );
	}

	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', A3_PVC_CSS_URL . '/a3_wp_admin.css' );
	}

	public static function plugin_extension_box( $boxes = array() ) {
		$support_box = '<a href="https://wordpress.org/support/plugin/page-views-count" target="_blank" alt="'.__('Go to Support Forum', 'page-views-count').'"><img src="'.A3_PVC_IMAGES_URL.'/go-to-support-forum.png" /></a>';
		$boxes[] = array(
			'content' => $support_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$first_box = '<a href="https://profiles.wordpress.org/a3rev/#content-plugins" target="_blank" alt="'.__('Free WordPress Plugins', 'page-views-count').'"><img src="'.A3_PVC_IMAGES_URL.'/free-wordpress-plugins.png" /></a>';

		$boxes[] = array(
			'content' => $first_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$second_box = '<a href="https://profiles.wordpress.org/a3rev/#content-plugins" target="_blank" alt="'.__('Free WooCommerce Plugins', 'page-views-count').'"><img src="'.A3_PVC_IMAGES_URL.'/free-woocommerce-plugins.png" /></a>';

		$boxes[] = array(
			'content' => $second_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

        $third_box = '<div style="margin-bottom: 5px; font-size: 12px;"><strong>' . __('Is this plugin is just what you needed? If so', 'page-views-count') . '</strong></div>';
        $third_box .= '<a href="https://wordpress.org/support/view/plugin-reviews/page-views-count#postform" target="_blank" alt="'.__('Submit Review for Plugin on WordPress', 'page-views-count').'"><img src="'.A3_PVC_IMAGES_URL.'/a-5-star-rating-would-be-appreciated.png" /></a>';

        $boxes[] = array(
            'content' => $third_box,
            'css' => 'border: none; padding: 0; background: none;'
        );

        $four_box = '<div style="margin-bottom: 5px;">' . __('Connect with us via','page-views-count') . '</div>';
		$four_box .= '<a href="https://www.facebook.com/a3rev" target="_blank" alt="'.__('a3rev Facebook', 'page-views-count').'" style="margin-right: 5px;"><img src="'.A3_PVC_IMAGES_URL.'/follow-facebook.png" /></a> ';
		$four_box .= '<a href="https://twitter.com/a3rev" target="_blank" alt="'.__('a3rev Twitter', 'page-views-count').'"><img src="'.A3_PVC_IMAGES_URL.'/follow-twitter.png" /></a>';

		$boxes[] = array(
			'content' => $four_box,
			'css' => 'border-color: #3a5795;'
		);

		return $boxes;
	}

	public static function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="options-general.php?page=a3-pvc">' . __( 'Settings', 'page-views-count' ) . '</a>' ), $actions );

		return $actions;
	}

	public static function plugin_extra_links($links, $plugin_name) {
		if ( $plugin_name != A3_PVC_PLUGIN_NAME) {
			return $links;
		}
		$links[] = '<a href="http://docs.a3rev.com/user-guides/page-view-count/" target="_blank">'.__('Documentation', 'page-views-count').'</a>';
		$links[] = '<a href="http://wordpress.org/support/plugin/page-views-count/" target="_blank">'.__('Support', 'page-views-count').'</a>';
		return $links;
	}
}
