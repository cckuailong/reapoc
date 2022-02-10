<?php
class Mappress_Map extends Mappress_Obj {
	var $alignment,
		$center,
		$classname,
		$editable,
		$embed,
		$height,
		$hideEmpty,
		$initialOpenDirections,
		$initialOpenInfo,
		$layers,
		$layout,
		$mapid,
		$mapTypeId,
		$metaKey,
		$mapOpts,
		$name,
		$poiList,
		$postid,
		$query,
		$search,
		$status,
		$title,
		$width,
		$zoom
		;

	var $pois = array();

	function __sleep() {
		return array('mapid', 'center', 'height', 'mapTypeId', 'metaKey', 'pois', 'search', 'status', 'title', 'width', 'zoom');
	}

	function __construct($atts = null) {
		$this->update($atts);

		// Convert POIs from arrays to objects if needed
		foreach((array)$this->pois as $index => $poi) {
			if (is_array($poi))
				$this->pois[$index] = new Mappress_Poi($poi);
		}
	}

	static function register() {
		global $wpdb;

		add_action('deleted_post', array(__CLASS__, 'deleted_post'));
		add_action('wp_ajax_mapp_delete', array(__CLASS__, 'ajax_delete'));
		add_action('wp_ajax_mapp_duplicate', array(__CLASS__, 'ajax_duplicate'));
		add_action('wp_ajax_mapp_find', array(__CLASS__, 'ajax_find'));
		add_action('wp_ajax_mapp_get', array(__CLASS__, 'ajax_get'));
		add_action('wp_ajax_mapp_get_post', array(__CLASS__, 'ajax_get_post'));
		add_action('wp_ajax_nopriv_mapp_get_post', array(__CLASS__, 'ajax_get_post'));
		add_action('wp_ajax_mapp_mutate', array(__CLASS__, 'ajax_mutate'));
		add_action('wp_ajax_mapp_save', array(__CLASS__, 'ajax_save'));

		add_action('media_buttons', array(__CLASS__, 'media_buttons'));

		// Tables
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$wpdb->show_errors(true);

		$exists = $wpdb->get_var("show tables like '$maps_table'");
		if (!$exists) {
			$result = $wpdb->query ("CREATE TABLE $maps_table (
									mapid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
									obj LONGTEXT)
									CHARACTER SET utf8;");
		}

		$exists = $wpdb->get_var("show tables like '$posts_table'");
		if (!$exists) {
			$result = $wpdb->query ("CREATE TABLE $posts_table (
									postid INT,
									mapid INT,
									PRIMARY KEY (postid, mapid) )
									CHARACTER SET utf8;");
		}

		$wpdb->show_errors(false);
	}

	static function media_buttons($editor_id) {
		$button = sprintf("<button type='button' class='button wp-media-buttons-icon mapp-mce-button'><span class='dashicons dashicons-location'></span>%s</button>", __('MapPress', 'mappress-google-maps-for-wordpress'));
		echo "<div class='mapp-mce'>$button</div>";
	}

	static function duplicate($mapid, $postid) {
		$map = self::get($mapid);
		if (!$map)
			return null;

		$title = ($map->title) ? $map->title : __('Untitled', 'mappress-google-maps-for-wordpress');
		$map->title = sprintf(__('Copy of %s', 'mappress-google-maps-for-wordpress'), $title);

		$map->postid = $postid;
		$map->mapid = null;
		$map->metaKey = null;		// Map is no longer automatic

		$result = $map->save();
		return ($result) ? $map : null;
	}

	static function ajax_duplicate() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		ob_start();

		$mapid = (isset($_POST['mapid'])) ? $_POST['mapid'] : null;
		$postid = (isset($_POST['postid'])) ? $_POST['postid'] : null;

		if (!$mapid)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		$map = self::duplicate($mapid, $postid);
		if ($map)
			Mappress::ajax_response('OK', $map);
		else
			Mappress::ajax_response('Internal error when copying');
	}

	static function find($args) {
		global $wpdb;

		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$sql = "SELECT SQL_CALC_FOUND_ROWS $maps_table.mapid, $maps_table.obj, $posts_table.postid, $wpdb->posts.post_status, $wpdb->posts.post_title "
			. " FROM $maps_table "
			. " INNER JOIN $posts_table ON ($posts_table.mapid = $maps_table.mapid)"
			. " LEFT OUTER JOIN $wpdb->posts ON ($wpdb->posts.ID = $posts_table.postid)"
		;
		$results = $wpdb->get_results($sql);

		$items = array();
		foreach($results as $result) {

			// Only check if map is attached to a post (postid > 0)
			if ($result->postid) {
				if (!current_user_can('edit_post', $result->postid))
					continue;
				if (in_array($result->post_status, array('trash', 'auto-draft', 'inherit')))
					continue;
			}

			$mapdata = unserialize($result->obj);
			$items[] = array(
				'mapid' => $result->mapid,
				'map_title' => $mapdata->title,
				'postid' => $result->postid,
				'post_title' => $result->post_title,
				'status' => $mapdata->status
			);
		}

		return $items;
	}

	static function ajax_find() {
		check_ajax_referer('mappress', 'nonce');
		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');
		Mappress::ajax_response('OK', self::find($_GET));
	}

	/**
	* Get a map.  Output is 'raw' or 'object'
	*/
	static function get($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$sql = "SELECT $posts_table.postid, $maps_table.mapid, $maps_table.obj FROM $posts_table INNER JOIN $maps_table ON ($maps_table.mapid = $posts_table.mapid) WHERE $maps_table.mapid = %d";
		$result = $wpdb->get_row($wpdb->prepare($sql, $mapid));

		if (!$result)
			return false;

		// Read the map data and construct a new map from it
		$mapdata = unserialize($result->obj);
		$mapdata->postid = $result->postid;
		$mapdata->mapid = $result->mapid;
		return new Mappress_Map($mapdata);
	}

	static function ajax_get() {
		check_ajax_referer('mappress', 'nonce');
		ob_start();
		$mapid = (isset($_GET['mapid'])) ? $_GET['mapid']  : null;
		$map = ($mapid) ? self::get($mapid) : null;
		if (!$map)
			Mappress::ajax_response(sprintf(__('Map not found', 'mappress-google-maps-for-wordpress'), $mapid));
		else
			Mappress::ajax_response('OK', $map);
	}

	/**
	* Get list of mapids for a post or all maps
	*
	* @return array of mapids | empty array
	*
	*/
	static function get_list($postid = null, $output = 'objects') {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$where = ($postid) ? $wpdb->prepare("WHERE postid = %d", $postid) : '';

		$mapids = $wpdb->get_col("SELECT mapid FROM $posts_table $where");
		if (!$mapids)
			return array();

		if ($output == 'ids') {
			return $mapids;
		} else {
			$maps = array();
			foreach($mapids as $mapid) {
				$map = Mappress_Map::get($mapid);
				if ($map)
					$maps[] = $map;
			}
			return $maps;
		}
	}

	static function ajax_get_post() {
		global $post;

		check_ajax_referer('mappress', 'nonce');
		ob_start();
		$postid = (isset($_GET['postid'])) ? $_GET['postid']  : null;
		$post = get_post( $postid );

		if (!$post)
			die(sprintf(__('Post not found', 'mappress-google-maps-for-wordpress'), $postid));

		setup_postdata($post);
		$html = Mappress_Template::get_template('mashup-modal');
		die($html);
	}

	function save() {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Apply wpautop to POI bodies
		foreach($this->pois as &$poi)
			$poi->body = wpautop($poi->body);

		// Make sure there's a postid
		if (is_null($this->postid))
			return false;

		$map = serialize($this);

		// Update map
		if (!$this->mapid) {
			// If no ID then autonumber
			$result = $wpdb->query($wpdb->prepare("INSERT INTO $maps_table (obj) VALUES(%s)", $map));
			$this->mapid = $wpdb->get_var("SELECT LAST_INSERT_ID()");
		} else {
			// Id provided, so insert or update
			$result = $wpdb->query($wpdb->prepare("INSERT INTO $maps_table (mapid, obj) VALUES(%d, '%s') ON DUPLICATE KEY UPDATE obj = %s", $this->mapid, $map, $map));
		}

		if ($result === false || !$this->mapid)
			return false;

		// Delete any existing post assignment to prevent multiple attachment
		$wpdb->query($wpdb->prepare("DELETE FROM $posts_table WHERE mapid = %d", $this->mapid));

		$result = $wpdb->query($wpdb->prepare("INSERT INTO $posts_table (postid, mapid) VALUES(%d, %d) ON DUPLICATE KEY UPDATE postid = %d, mapid = %d", $this->postid, $this->mapid,
			$this->postid, $this->mapid));

		if ($result === false)
			return false;

		$wpdb->query("COMMIT");
		return true;
	}

	static function ajax_save() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		ob_start();
		$mapdata = (isset($_POST['mapdata'])) ? json_decode(stripslashes($_POST['mapdata']), true) : null;

		if (!$mapdata)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		$map = new Mappress_Map($mapdata);
		$result = $map->save();

		if (!$result)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		do_action('mappress_map_save', $map); 	// Use for your own developments

		// Return saved mapid
		Mappress::ajax_response('OK', $map->mapid);
	}

	/**
	* Delete a map and all of its post assignments
	*
	* @param mixed $mapid
	*/
	static function delete($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Delete from posts table
		$result = $wpdb->query($wpdb->prepare("DELETE FROM $posts_table WHERE mapid = %d", $mapid));
		if ($result === false)
			return false;

		// Don't delete the actual map data
		//		$result = $wpdb->query($wpdb->prepare("DELETE FROM $maps_table WHERE mapid = %d", $mapid));
		//		if ($result === false)
		//			return false;

		$wpdb->query("COMMIT");
		return true;
	}

	static function ajax_delete() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		ob_start();
		$mapid = (isset($_POST['mapid'])) ? $_POST['mapid'] : null;
		$result = Mappress_Map::delete($mapid);

		if (!$result)
			Mappress::ajax_response("Internal error when deleting map ID '$mapid'!");

		do_action('mappress_map_delete', $mapid); 	// Use for your own developments
		Mappress::ajax_response('OK');
	}

	static function mutate($mapid, $mapdata) {
		if (!$mapid || !$mapdata)
			return false;

		$map = self::get($mapid, true);
		if (!$map)
			return false;

		$map->update($mapdata);
		$result = $map->save();
		return ($result) ? true : false;
	}

	static function ajax_mutate() {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		ob_start();

		$mapid = (isset($_POST['mapid'])) ? $_POST['mapid'] : null;
		$mapdata = (isset($_POST['mapdata'])) ? $_POST['mapdata'] : null;

		$result = self::mutate($mapid, $mapdata);
		if (!$result)
			Mappress::ajax_response('Internal error when mutating, your data was not saved!');

		// Return updated map
		Mappress::ajax_response('OK', $result);
	}

	/**
	* When a post is deleted, trash attached maps
	*/
	static function deleted_post($postid) {
		$mapids = self::get_list($postid, 'ids');
		$result = true;
		foreach($mapids as $mapid)
			$result = $result && self::mutate($mapid, array('status' => 'trashed'));
		return $result;
	}

	/**
	* Display a map
	*
	* @param mixed $atts - override attributes.  Attributes applied from options -> map -> $atts
	*/
	function display($atts = null, $in_iframe = false) {
		static $div = 0;

		$this->update($atts);

		// Assign a map name, if none was provided.  Uniqid is used for for ajax to prevent repeating ids
		if (empty($this->name)) {
			$this->name = (defined('DOING_AJAX') && DOING_AJAX) ? "mapp" . uniqid() : "mapp$div";
			$div++;
		}

		if (!$in_iframe && (Mappress::$options->iframes))
			return $this->display_iframe();

		// Prepare POIs for maps
		if (empty($this->query))
			$this->prepare();

		// Last chance to alter map before display
		do_action('mappress_map_display', $this);

		// Container
		$id = $this->name . '-layout';
		$html = "<div class='mapp-layout' id='$id'></div>";

		// Enqueue scripts and data
		Mappress::scripts_enqueue();
		$script = "window.mapp = window.mapp || {}; mapp.data = mapp.data || [];\r\n";
		$script .= "mapp.data.push( " . json_encode($this) . " ); \r\nif (typeof mapp.load != 'undefined') { mapp.load(); };";

		// Use inline scripts for XHTML and some themes which mismatch tags in the content
		if (function_exists('wp_add_inline_script') && Mappress::is_footer())
			wp_add_inline_script('mappress', "//<![CDATA[\r\n" . $script . "\r\n//]]>");
		else
			$html .= Mappress::script($script);

		return $html;
	}

	function display_iframe() {
		$atts = (array) $this;

		$atts['mapp_iframe'] = true;
		unset($atts['name']);	// 'name' is a reserved word and causes 404 errors

		// If mapid, map will be re-read
		if ($this->mapid)
			unset($atts['pois']);

		// Programmatic maps
		else if ($this->pois) {
			$json = json_encode($this->pois);
			$key = 'mapp-iframe-' . ( ($json) ? md5($json) : uniqid() );
			$atts['transient'] = $key;
			set_transient($key, $this->pois, 20);
			unset($atts['pois']);
		}

		$url = get_home_url() . '?' . http_build_query($atts);
		$html = "<div class='mapp-iframe-layout'>"
			. "<div class='mapp-iframe-wrapper'>"
			. "<iframe class='mapp-iframe' src='$url'></iframe>"
			. "</div>"
			. "</div>";

		return $html;
	}

	/**
	* Prepare map for output
	*
	*/
	function prepare() {
		global $post, $wp_embed;

		// Sort the pois
		if (Mappress::$options->sort && !isset($this->query['orderby']))
			$this->sort_pois();

		// Set properties (mashups and maps)
		foreach($this->pois as $poi) {
			if ($poi->postid)
				$postid = $poi->postid;
			else
				$postid = ($post) ? $post->ID : null;
			$poi->props = apply_filters('mappress_poi_props', $poi->props, $postid, $poi);

			// Oembeds
			$poi->body = $wp_embed->autoembed($poi->body);

			// Embed shortcodes ([embed], etc):
			$poi->body = $wp_embed->run_shortcode($poi->body);
		}

		// Autoicons
		$this->autoicons();
	}

	/**
	* Autoicons
	*/
	function autoicons() {
		global $post;

		// Only 1 rule allowed
		$rule = (object) wp_parse_args(Mappress::$options->autoicons, array('key' => null, 'values' => array()));

		foreach($rule->values as $value => $iconid) {
			// Get all post IDs that match the current key & value
			if ($rule->key == 'post_type') {
				$wpq = new WP_Query(array('post_type' => $value, 'fields' => 'ids', 'posts_per_page' => -1));
				$postids = $wpq->posts;
			} else {
				$term = get_term_by('slug', $value, $rule->key);
				if (!is_object($term))
					continue;

				$objects = get_objects_in_term($term->term_id, $rule->key);
				if (!is_array($objects))
					continue;

				$postids = array_keys(array_flip($objects));
			}

			// Check each post ID to see if it's in the map's POIs, if so set iconid
			$current_post = ($post) ? $post->ID : null;
			foreach($this->pois as &$poi) {
				$postid = ($poi->postid) ? $poi->postid : $current_post;
				if (in_array($postid, $postids))
					$poi->iconid = $iconid;
			}
		}

		// Filter
		foreach($this->pois as &$poi)
			$poi->iconid = apply_filters('mappress_poi_iconid', $poi->iconid, $poi);
	}

	/**
	* Default action to sort the map
	*
	* @param mixed $map
	*/
	function sort_pois() {
		usort($this->pois, array(__CLASS__, 'compare_title'));
		do_action('mappress_sort_pois', $this);
	}

	/**
	* Compare two POIs by title
	* HTML tags are stripped - until URL is separated from title this is the only way to
	* sort titles with HTML
	*
	* @param mixed $a
	* @param mixed $b
	* @return mixed
	*/
	static function compare_title($a, $b) {
		return strcasecmp(strip_tags($a->title), strip_tags($b->title));
	}
}
?>