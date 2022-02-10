<?php
/**
* Options
*/
class Mappress_Options extends Mappress_Obj {
	var $acf,
		$alignment,
		$autoicons,
		$autoupdate,
		$apiKey,
		$apiKeyServer,
		$autodisplay,
		$betas,
		$clustering = false,
		$clusteringOptions,
		$country,
		$defaultIcon,
		$deregister = true,
		$directions = 'google',
		$directionsServer = 'https://maps.google.com',
		$engine = 'leaflet',
		$filter,					// deprecated
		$filters = array(),
		$filtersPos = 'top',
		$footer = true,
		$geocoder = 'nominatim',
		$highlight,
		$highlightIcon,
		$iconScale,
		$iframes,
		$initialOpenInfo,
		$language,
		$layout = 'left',
		$license,
		$mapbox,
		$mapboxStyles = array(),	// deprecated
		$mashupBody = 'poi',
		$mashupClick = 'poi',
		$mashupKml,
		$metaKeys = array(),
		$metaSyncSave = true,
		$mini = 400,
		$poiList = true,
		$poiListPageSize = 20,
		$poiListOpen = true,
		$poiZoom = 15,
		$postTypes = array('post', 'page'),
		$radius = 15,
		$scrollWheel = true,
		$search = true,
		$searchBox,
		$size = 0,
		$sizes = array(
			array('width' => '100%', 'height' => '350px'),
			array('width' => '50%', 'height' => '50%'),
			array('width' => '75%', 'height' => '50%'),
			array('width' => '100%', 'height' => '50%'),
			array('width' => '100vw', 'height' => '100vh')
		),
		$sort,
		$style,
		$styles = array(),		// deprecated
		$stylesGoogle = array(),
		$stylesMapbox = array(),
		$thumbs = true,			// deprecated
		$thumbSize = 'medium',
		$thumbsPopup = 'left',
		$thumbsList = null,
		$thumbWidth,
		$thumbHeight,
		$tooltips = false,
		$userLocation = false,
		$wpml = true
		;

	function __construct($options = '') {
		$this->update($options);
	}

	// Options are saved as array because WP settings API is fussy about objects
	static function get() {
		$options = get_option('mappress_options');

		// Iframes
		if (isset($_REQUEST['mp_compat']))
			$options['iframes'] = true;
		return new Mappress_Options($options);
	}

	function save() {
		return update_option('mappress_options', get_object_vars($this));
	}
}


/**
* Options menu display
*/
class Mappress_Settings {

	static function register() {
		add_action('wp_ajax_mapp_license_check', array(__CLASS__, 'ajax_license_check'));
		add_action('wp_ajax_mapp_options_reset', array(__CLASS__, 'ajax_reset'));
		add_action('wp_ajax_mapp_option_save', array(__CLASS__, 'ajax_option_save'));
		add_action('wp_ajax_mapp_options_save', array(__CLASS__, 'ajax_options_save'));
		add_action('wp_ajax_mapp_style_delete', array(__CLASS__, 'ajax_style_delete'));
		add_action('wp_ajax_mapp_style_save', array(__CLASS__, 'ajax_style_save'));
		add_action('load-toplevel_page_mappress', array(__CLASS__, 'review_admin_notice'));
	}

	static function ajax_license_check() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		$license = (isset($_POST['license'])) ? (object) $_POST['license'] : null;
		if (!$license)
			Mappress::ajax_response('Internal error, missing license!');

		ob_start();
		$status = Mappress::$updater->check($license);
		Mappress::ajax_response('OK', $status);
	}

	static function ajax_style_delete() {
		check_ajax_referer('mappress', 'nonce');
		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		$id = (isset($_POST['id'])) ? $_POST['id'] : null;
		if (!$id)
			Mappress::ajax_response('Missing style ID');

		$options = Mappress_Options::get();
		$setting = ($options->engine == 'google') ? 'stylesGoogle' : 'stylesMapbox';
		$i = array_search($id, array_column($options->$setting, 'id'));

		//unset($options->$setting[$key]);
		array_splice($options->$setting, $i, 1);
		$options->save();
		Mappress::ajax_response('OK', $options->$setting);
	}

	// Save map style, for JSON styles will assign an ID if none exists
	static function ajax_style_save() {
		check_ajax_referer('mappress', 'nonce');
		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		$style = (isset($_POST['style'])) ? wp_unslash($_POST['style']) : null;
		if (!$style)
			Mappress::ajax_response('Missing style');

		$options = Mappress_Options::get();
		$setting = ($options->engine == 'google') ? 'stylesGoogle' : 'stylesMapbox';

		// Update if style has an ID, otherwise treat it as new.  New Snazzy styles have an ID, otherwise assign uniqid
		$id = ($style['id']) ? $style['id'] : null;
		$i = ($id) ? array_search($id, array_column($options->$setting, 'id')) : false;

		if ($i === false) {
			$style['id'] = ($id) ? $id : uniqid();
			$options->{$setting}[] = $style;
		} else {
			$options->{$setting}[$i] = $style;
		}
		$options->save();
		Mappress::ajax_response('OK', $options->$setting);
	}

	// Save one or more options
	static function ajax_option_save() {
		check_ajax_referer('mappress', 'nonce');
		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');


		$settings = (isset($_POST['settings'])) ? (object) wp_unslash($_POST['settings']) : array();
		$options = Mappress_Options::get();
		foreach($settings as $setting => $value)
			$options->$setting = wp_unslash($value);
		$options->save();
		Mappress::ajax_response('OK');
	}

	// Save all the options
	static function ajax_options_save() {
		check_ajax_referer('mappress', 'nonce');
		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		ob_start();

		$settings = (isset($_POST['settings'])) ? wp_unslash($_POST['settings']) : null;
		$settings = ($settings) ? (object) json_decode($settings, true) : null;

		if (!$settings)
			Mappress::ajax_response('Internal error, missing settings!');

		// Convert JS object arrays to PHP associative arrays
		self::assoc($settings->autoicons['values'], true);
		self::assoc($settings->metaKeys, true);

		// If license changed, clear cache so it re-checks on next load
		if ($settings->license && $settings->license != Mappress::$options->license)
			Mappress::$updater->clear_cache();

		// Update() converts strings to booleans, but it's not recursive, so explicitly convert nested booleans inside arrays
		if (isset($settings->clusteringOptions['spiderfyOnMaxZoom']))
			$settings->clusteringOptions['spiderfyOnMaxZoom'] = ($settings->clusteringOptions['spiderfyOnMaxZoom'] == "true") ? true : false;

		// Merge in old values so they're not lost, e.g. stylesMapbox and stylesGoogle
		$options = Mappress_Options::get();
		$options->update($settings);
		$options->save();
		Mappress::ajax_response('OK');
	}

	static function ajax_reset() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		ob_start();
		$options = new Mappress_Options();
		$options->save();
		Mappress::ajax_response('OK');
	}

	static function get_initial_state() {
		$state = array();

		// Settings
		$state = Mappress::$options;

		// Don't send over styles, they're saved via ajax
		unset($state->stylesMapbox);
		unset($state->stylesGoogle);

		// Convert PHP associative arrays to object arrays for JS
		self::assoc($state->autoicons['values'], false);
		self::assoc($state->metaKeys, false);

		// Setup helpers
		$helpers = (object) array(
			'demo_map' => self::demo_map(),
			'geocoding_errors' => self::geocoding_errors(),
			'icon_directory' => (class_exists('Mappress_Icons')) ? Mappress_Icons::$icons_dir : null,
			'is_multisite' => is_multisite(),
			'is_super_admin' => is_super_admin(),
			'is_main_site' => is_main_site(),
			'jetpack' => (class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' )),
			'languages' => array('' => __('Default', 'mappress-google-maps-for-wordpress'), 'ab' => 'Abkhazian', 'aa' => 'Afar', 'af' => 'Afrikaans', 'ak' => 'Akan', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'an' => 'Aragonese', 'hy' => 'Armenian', 'as' => 'Assamese', 'av' => 'Avaric', 'ae' => 'Avestan', 'ay' => 'Aymara', 'az' => 'Azerbaijani', 'bm' => 'Bambara', 'ba' => 'Bashkir', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali', 'bh' => 'Bihari languages', 'bi' => 'Bislama', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian', 'my' => 'Burmese', 'ca' => 'Catalan, Valencian', 'km' => 'Central Khmer', 'ch' => 'Chamorro', 'ce' => 'Chechen', 'ny' => 'Chichewa, Chewa, Nyanja', 'zh' => 'Chinese', 'cv' => 'Chuvash', 'kw' => 'Cornish', 'co' => 'Corsican', 'cr' => 'Cree', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'dv' => 'Divehi, Dhivehi, Maldivian', 'nl' => 'Dutch, Flemish', 'dz' => 'Dzongkha', 'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe', 'fo' => 'Faroese', 'fj' => 'Fijian', 'fi' => 'Finnish', 'fr' => 'French', 'ff' => 'Fulah', 'gd' => 'Gaelic, Scottish Gaelic', 'gl' => 'Galician', 'lg' => 'Ganda', 'ka' => 'Georgian', 'de' => 'German', 'ki' => 'Gikuyu, Kikuyu', 'el' => 'Greek (Modern)', 'kl' => 'Greenlandic, Kalaallisut', 'gn' => 'Guarani', 'gu' => 'Gujarati', 'ht' => 'Haitian, Haitian Creole', 'ha' => 'Hausa', 'he' => 'Hebrew', 'hz' => 'Herero', 'hi' => 'Hindi', 'ho' => 'Hiri Motu', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'io' => 'Ido', 'ig' => 'Igbo', 'id' => 'Indonesian', 'iu' => 'Inuktitut', 'ik' => 'Inupiaq', 'ga' => 'Irish', 'it' => 'Italian', 'ja' => 'Japanese', 'jv' => 'Javanese', 'kn' => 'Kannada', 'kr' => 'Kanuri', 'ks' => 'Kashmiri', 'kk' => 'Kazakh', 'rw' => 'Kinyarwanda', 'kv' => 'Komi', 'kg' => 'Kongo', 'ko' => 'Korean', 'kj' => 'Kwanyama, Kuanyama', 'ku' => 'Kurdish', 'ky' => 'Kyrgyz', 'lo' => 'Lao', 'la' => 'Latin', 'lv' => 'Latvian', 'lb' => 'Letzeburgesch, Luxembourgish', 'li' => 'Limburgish, Limburgan, Limburger', 'ln' => 'Lingala', 'lt' => 'Lithuanian', 'lu' => 'Luba-Katanga', 'mk' => 'Macedonian', 'mg' => 'Malagasy', 'ms' => 'Malay', 'ml' => 'Malayalam', 'mt' => 'Maltese', 'gv' => 'Manx', 'mi' => 'Maori', 'mr' => 'Marathi', 'mh' => 'Marshallese', 'ro' => 'Moldovan, Moldavian, Romanian', 'mn' => 'Mongolian', 'na' => 'Nauru', 'nv' => 'Navajo, Navaho', 'nd' => 'Northern Ndebele', 'ng' => 'Ndonga', 'ne' => 'Nepali', 'se' => 'Northern Sami', 'no' => 'Norwegian', 'nb' => 'Norwegian Bokmål', 'nn' => 'Norwegian Nynorsk', 'ii' => 'Nuosu, Sichuan Yi', 'oc' => 'Occitan (post 1500)', 'oj' => 'Ojibwa', 'or' => 'Oriya', 'om' => 'Oromo', 'os' => 'Ossetian, Ossetic', 'pi' => 'Pali', 'pa' => 'Panjabi, Punjabi', 'ps' => 'Pashto, Pushto', 'fa' => 'Persian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'qu' => 'Quechua', 'rm' => 'Romansh', 'rn' => 'Rundi', 'ru' => 'Russian', 'sm' => 'Samoan', 'sg' => 'Sango', 'sa' => 'Sanskrit', 'sc' => 'Sardinian', 'sr' => 'Serbian', 'sn' => 'Shona', 'sd' => 'Sindhi', 'si' => 'Sinhala, Sinhalese', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'so' => 'Somali', 'st' => 'Sotho, Southern', 'nr' => 'South Ndebele', 'es' => 'Spanish, Castilian', 'su' => 'Sundanese', 'sw' => 'Swahili', 'ss' => 'Swati', 'sv' => 'Swedish', 'tl' => 'Tagalog', 'ty' => 'Tahitian', 'tg' => 'Tajik', 'ta' => 'Tamil', 'tt' => 'Tatar', 'te' => 'Telugu', 'th' => 'Thai', 'bo' => 'Tibetan', 'ti' => 'Tigrinya', 'to' => 'Tonga (Tonga Islands)', 'ts' => 'Tsonga', 'tn' => 'Tswana', 'tr' => 'Turkish', 'tk' => 'Turkmen', 'tw' => 'Twi', 'ug' => 'Uighur, Uyghur', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek', 've' => 'Venda', 'vi' => 'Vietnamese', 'vo' => 'Volap_k', 'wa' => 'Walloon', 'cy' => 'Welsh', 'fy' => 'Western Frisian', 'wo' => 'Wolof', 'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'za' => 'Zhuang, Chuang', 'zu' => 'Zulu'),
			'license_status' => (Mappress::$pro && Mappress::$options->license) ? Mappress::$updater->get_status() : null,
			'meta_fields' => self::get_meta_fields(),
			'meta_keys' => self::get_meta_keys(),
			'post_types' => self::get_post_types(),
			'taxonomies' => self::get_taxonomies(),
			'thumbnail_sizes' => self::get_thumbnail_sizes()
		);
		$state->helpers = $helpers;
		return json_encode($state);
	}

	static function assoc(&$a, $to_assoc) {
		$result = [];

		if (!is_array($a) || empty($a)) {
			$a = array();
			return;
		}

		// Convert to associative
		if ($to_assoc) {
			foreach($a as $i => $row) {
				$key = $row['key'];
				$value = $row['value'];
				$result[$key] = $value;
			}
		} else {
			// Convert from associative
			foreach($a as $key => $value)
				$result[] = array('key' => $key, 'value' => $value);
		}
		$a = $result;
	}

	static function demo_map() {
		$poi = new Mappress_Poi(array('address' => 'San Francisco, CA', "title" => "MapPress", "body" => __("Maps for WordPress", 'mappress-google-maps-for-wordpress'), "point" => array('lat' => 37.774095, 'lng' => -122.418731)));
		$map = new Mappress_Map(array('alignment' => 'default', 'width' => '100%', 'height' => 300,'pois' => array($poi), 'zoom' => 8));
		return $map;
	}

	static function geocoding_errors() {
		$geocoding_errors = array();
		$query = new WP_Query(array('meta_key' => 'mappress_error', 'posts_per_page' => 20));
		foreach($query->posts as $post) {
			$geocoding_errors[] = array(
				'ID' => $post->ID,
				'post_title' => $post->post_title,
				'error' => get_post_meta($post->ID, 'mappress_error', true)
			);
		};
		return $geocoding_errors;
	}

	static function get_meta_fields() {
		for ($i = 1; $i < 7; $i++)
			$fields['address' . $i] = __('Address line ', 'mappress-google-maps-for-wordpress') . ' ' . $i;
		$fields = array_merge($fields, array('lat' => __('Latitude', 'mappress-google-maps-for-wordpress'), 'lng' => __('Longitude', 'mappress-google-maps-for-wordpress'), 'title' => __('Title', 'mappress-google-maps-for-wordpress'), 'body' => __('Body', 'mappress-google-maps-for-wordpress'), 'iconid' => __('Icon', 'mappress-google-maps-for-wordpress'), 'zoom' => __('Zoom', 'mappress-google-maps-for-wordpress')));
		return $fields;
	}

	static function get_post_types() {
		$results = array();
		$post_types = get_post_types(array('show_ui' => true, 'public' => true), 'objects');
		unset($post_types['mappress_map'], $post_types['attachment']);
		foreach($post_types as $type => $obj)
			$results[$type] = $obj->label;
		return $results;
	}


	static function get_thumbnail_sizes() {
		// Note: WP doesn't return dimensions, just the size names - ticket is > 6 months old now: http://core.trac.wordpress.org/ticket/18947
		$sizes = get_intermediate_image_sizes();
		$sizes = array_combine(array_values($sizes), array_values($sizes));
		return $sizes;
	}

	static function get_meta_keys() {
		global $wpdb;
		$keys = $wpdb->get_col( "
			SELECT DISTINCT meta_key
			FROM $wpdb->postmeta
			WHERE meta_key NOT in ('_edit_last', '_edit_lock', '_encloseme', '_pingme', '_thumbnail_id')
			AND meta_key NOT LIKE ('\_wp%')"
		);
		$results = (is_array($keys) && !empty($keys)) ? array_combine($keys, $keys) : array();
		return $results;
	}

	static function get_meta_values($meta_key) {
		global $wpdb;
		$sql = "SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value != '' ORDER BY meta_value";
		$meta_values = $wpdb->get_col($wpdb->prepare($sql, $meta_key));
		$results = ($meta_values) ? array_combine($meta_values, $meta_values) : array();
		return $results;
	}

	static function get_taxonomies() {
		$results = array();
		$tax_objs = get_taxonomies(array('public' => true), 'objects');
		unset($tax_objs['post_format']);
		foreach($tax_objs as $tax_obj)
			$results[$tax_obj->name] = $tax_obj->label;
		return $results;
	}

	static function get_usage() {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$usage = new stdClass();
		foreach(array('alignment', 'autodisplay', 'betas', 'engine', 'footer', 'geocoder', 'highlight', 'layout', 'language', 'poiList') as $key) {
			if (isset(Mappress::$options->$key))
				$usage->$key = Mappress::$options->$key;
		}
		$usage->mp_version = Mappress::VERSION;
		$usage->wp_version = get_bloginfo('version');
		$usage->gutenberg = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) && function_exists('is_plugin_active') && !is_plugin_active( 'classic-editor/classic-editor.php' );
		$usage->license = (trim(Mappress::$options->license)) ? true : false;
		$usage->filters = (Mappress::$options->filters) ? true : false;
		$usage->mapbox = (Mappress::$options->mapbox) ? true : false;
		$usage->autoicons = Mappress::$options->autoicons && Mappress::$options->autoicons['key'];
		$usage->multisite = is_multisite();

		$usage->assignment1 = $wpdb->get_var("SELECT count(distinct(mapid)) FROM $posts_table GROUP BY mapid HAVING count(*) > 1");
		$usage->count1 = $wpdb->get_var("SELECT count(*) from $maps_table");
		return $usage;
	}

	static function review_admin_notice() {
		$first_time = get_option('mappress_review');

		if (!$first_time) {
			update_option('mappress_review', time());
			return;
		}

		if (time() <= $first_time + (60 * 60 * 24 * 10))
			return;

		$ids = Mappress_Map::get_list(null, 'ids');
		if (count($ids) < 1)
			return;

		$review_link = sprintf("<a class='button button-primary mapp-dismiss' href='https://wordpress.org/support/view/plugin-reviews/mappress-google-maps-for-wordpress?filter=5' target='_blank'>%s</a>", __('OK, you deserve it!', 'mappress-google-maps-for-wordpress'));
		$no_link = sprintf("<a class='button mapp-dismiss' href='#'>%s</a>", __('Nope, maybe later', 'mappress-google-maps-for-wordpress'));
		$help_link = sprintf("<a class='mapp-dismiss' href='https://mappresspro.com/chris-contact' target='_blank'>%s</a>", __('I need help using the plugin', 'mappress-google-maps-for-wordpress'));
		$body = "<div class='mapp-review'>";
		$body .= "<h3>" . __("Help Spread the Word", 'mappress-google-maps-for-wordpress') . "</h3>";
		$body .= "<p>" . __("Hi, I hope you're enjoying MapPress.  Would you mind taking a moment to write a brief review?  It would mean a lot to me!", 'mappress-google-maps-for-wordpress') . "</p>";
		$body .= "<p>" . "~ Chris Richardson" . "</p>";
		$body .= "<div class='mapp-review-options'>" . $review_link . $no_link . $help_link . "</div>";
		$body .= "</div>";
		Mappress::$notices['review'] = array('review', $body);
	}

	static function options_page() {
		?>
		<script>var mappress_options_state=<?php echo self::get_initial_state();?>;</script>
		<div class="mapp-options">
			<div class='mapp-options-header'>
				<div class='mapp-options-header-version'>
					<h1><?php _e('MapPress', 'mappress-google-maps-for-wordpress'); ?></h1>
					<?php echo Mappress::$version; ?>
				</div>
				<div class='mapp-options-header-links'>
					<a target='_blank' href='https://mappresspro.com/mappress/mappress-documentation'><?php _e('Get help', 'mappress-google-maps-for-wordpress');?></a>
					<a target='_blank' href='https://mappresspro.com/whats-new/'><?php _e("What's new", 'mappress-google-maps-for-wordpress');?></a>
					<?php if (Mappress::$pro) { ?>
						<a target='_blank' href='https://mappresspro.com/account/' target='_blank'><?php _e("Your account", 'mappress-google-maps-for-wordpress');?></a>
					<?php } else { ?>
						<a class='button button-primary' href='https://mappresspro.com/mappress' target='_blank'><?php _e('Upgrade to MapPress Pro', 'mappress-google-maps-for-wordpress');?></a>
					<?php } ?>
				</div>
			</div>
			<div id="mapp-options-settings"></div>
		</div>
		<?php
	}

	static function support_page() {
		$options = Mappress::$options;
		$initial_state = array(
			'apiKey' => $options->apiKey,
			'engine' => $options->engine,
			'isOpen' => (isset($_REQUEST['wizard']) && $_REQUEST['wizard']) ? true : false,
			'mapbox' => $options->mapbox,
		);
		?>
		<script>var mappress_support_state=<?php echo json_encode($initial_state); ?>;</script>
		<div class='mapp-options'>
			<div class='mapp-options-header'>
				<div class='mapp-options-header-version'>
					<h1><?php _e('MapPress', 'mappress-google-maps-for-wordpress'); ?></h1>
					<?php echo Mappress::$version; ?>
				</div>
				<div class='mapp-options-header-links'>
					<a target='_blank' href='https://mappresspro.com/mappress/mappress-documentation'><?php _e('Get help', 'mappress-google-maps-for-wordpress');?></a>
					<a target='_blank' href='https://mappresspro.com/whats-new/'><?php _e("What's new", 'mappress-google-maps-for-wordpress');?></a>
					<?php if (Mappress::$pro) { ?>
						<a target='_blank' href='https://mappresspro.com/account/' target='_blank'><?php _e("Your account", 'mappress-google-maps-for-wordpress');?></a>
					<?php } else { ?>
						<a class='button button-primary' href='https://mappresspro.com/mappress' target='_blank'><?php _e('Upgrade to MapPress Pro', 'mappress-google-maps-for-wordpress');?></a>
					<?php } ?>
				</div>
			</div>
			<div id="mapp-support-page"></div>
		</div>
		<?php
	}
}
?>