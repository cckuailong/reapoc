<?php
if (!defined('ABSPATH')) die('No direct access allowed');

if (!class_exists('Updraft_Dashboard_News')) :
/**
 * Handles all stuffs related to Dashboard News
 */
class Updraft_Dashboard_News {
	
	/**
	 * dashboard news feed URL
	 *
	 * @var String
	 */
	private $feed_url;
	
	/**
	 * news page URL
	 *
	 * @var String
	 */
	private $link;
	
	/**
	 * various translations to use in the UI
	 *
	 * @var Array
	 */
	private $translations;
	
	/**
	 * slug to use, where needed
	 *
	 * @var String
	 */
	private $slug;
	
	/**
	 * Valid ajax callback pages
	 *
	 * @var Array
	 */
	private $valid_callback_pages;
	
	/**
	 * constructor of class Updraft_Dashboard_News
	 *
	 * @param String $feed_url	   - dashboard news feed URL
	 * @param String $link		   - web page URL
	 * @param Array  $translations - an array of translations, with keys: product_title, item_prefix, item_description, dismiss_confirm
	 */
	public function __construct($feed_url, $link, $translations) {
	
		$this->feed_url = $feed_url;
		$this->link = $link;
		$this->translations = $translations;
		$this->slug = sanitize_title($translations['product_title']);
		
		$dashboard_news_transient_name = $this->get_transient_name();
		add_filter('pre_set_transient_'.$dashboard_news_transient_name, array($this, 'pre_set_transient_for_dashboard_news'), 10);
		add_filter('transient_'.$dashboard_news_transient_name, array($this, 'transient_for_dashboard_news'), 10);
		
		add_action('wp_ajax_'.$this->slug.'_ajax_dismiss_dashboard_news', array($this, 'dismiss_dashboard_news'));

		if ('index.php' == $GLOBALS['pagenow'] && !get_user_meta(get_current_user_id(), $this->slug.'_dismiss_dashboard_news', true)) {
			add_action('admin_print_footer_scripts', array($this, 'admin_print_footer_scripts'));
		}
		add_action('wp_ajax_dashboard-widgets', array($this, 'wp_ajax_dashboard_widgets_low_priority'), 1);
		add_action('wp_ajax_dashboard-widgets', array($this, 'wp_ajax_dashboard_widgets_high_priority'), 20);
		
		$this->valid_callback_pages = array(
			'dashboard-user',
			'dashboard-network',
			'dashboard',
		);
	}
	
	/**
	 * Get the transient name
	 *
	 * @return String
	 */
	private function get_transient_name() {
		$locale = function_exists('get_user_locale') ? get_user_locale() : get_locale();
		global $wp_version;
		@include(ABSPATH.WPINC.'/version.php');// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		$dash_prefix = version_compare($wp_version, '4.8', '>=') ? 'dash_v2_' : 'dash_';
		return version_compare($wp_version, '4.3', '>=') ? $dash_prefix.md5('dashboard_primary_'.$locale) : 'dash_'.md5('dashboard_primary');
	}
	
	/**
	 * Filters a transient for dashboard news before its value is set
	 *
	 * @param String $value - New value of transient
	 * @return String  HTML of Wordpress News & Events same as $transient param
	 */
	public function pre_set_transient_for_dashboard_news($value) {
		if (!function_exists('wp_dashboard_primary_output')) return $value;
		// Not needed first if condition, because filter hook name have already transient name. It is for better checking
		if (!get_user_meta(get_current_user_id(), $this->slug.'_dismiss_dashboard_news', true)) {
			// Gets the news, when fetching WP news first time (transient cache does not exist)
			$this->get_dashboard_news_html();
		}
		return $value;
	}
	
	/**
	 * wp_ajax_dashboard-widgets ajax action handler with low priority
	 */
	public function wp_ajax_dashboard_widgets_low_priority() {
		
		if (!$this->do_ajax_dashboard_news()) return;
		
		add_filter('wp_die_ajax_handler', array($this, 'wp_die_ajax_handler'));
		
	}
	
	/**
	 * Dummy wp die handler
	 *
	 * @param String $callback_function Callable $function Callback function name
	 * @return String callable $function Callback function name
	 */
	public function wp_die_ajax_handler($callback_function) {
		// this condition is not required, but always better to double confirm
		if (!$this->do_ajax_dashboard_news()) return $callback_function;
		// Here, We can use __return_empty_string function name, but __return_empty_string is available since WP 3.7. Whereas __return_true function name available since WP 3.0
		return '__return_true';
	}
	
	/**
	 * wp_ajax_dashboard-widgets ajax action handler with high priority
	 */
	public function wp_ajax_dashboard_widgets_high_priority() {
		
		if (!$this->do_ajax_dashboard_news()) return;
		
		remove_filter('wp_die_ajax_handler', array($this, 'wp_die_ajax_handler'));
		echo $this->get_dashboard_news_html();
		wp_die();
	}
	
	/**
	 * Check whether valid ajax for dashboard news or not
	 *
	 * @return Boolean True if an ajax for the WP dashboard news
	 */
	private function do_ajax_dashboard_news() {
		$ajax_callback_page = !empty($_GET['pagenow']) ? $_GET['pagenow'] : '';
		return (in_array($ajax_callback_page, $this->valid_callback_pages) && !empty($_GET['widget']) && 'dashboard_primary' == $_GET['widget']);
	}
	
	/**
	 * Filters a transient for dashboard news when getting transient value
	 *
	 * @param String $value - New value of transient
	 * @return String - HTML of Wordpress News & Events
	 */
	public function transient_for_dashboard_news($value) {
		if (!function_exists('wp_dashboard_primary_output')) return $value;
		// Not needed first if condition, because filter hook name have already transient name. It is for better checking
		if (!get_user_meta(get_current_user_id(), $this->slug.'_dismiss_dashboard_news', true) && !empty($value)) {
			return $value.$this->get_dashboard_news_html();
		}
		return $value;
	}
	
	/**
	 * get dashboard news html
	 *
	 * @return String - the resulting message
	 */
	private function get_dashboard_news_html() {
	
		$cache_key = $this->slug.'_dashboard_news';
		if (false !== ($output = get_transient($cache_key))) return $output;

		$feeds = array(
			$this->slug => array(
				'link' => $this->link,
				'url' => $this->feed_url,
				'title' => $this->translations['product_title'],
				'items' => apply_filters($this->slug.'_dashboard_news_items_count', 2),
				'show_summary' => 0,
				'show_author' => 0,
				'show_date' => 0,
			)
		);
		ob_start();
		wp_dashboard_primary_output('dashboard_primary', $feeds);
		$original_formatted_news = ob_get_clean();
		$formatted_news = preg_replace('/<a(.+?)>(.+?)<\/a>/i', "<a$1>".$this->translations['item_prefix'].": $2</a>", $original_formatted_news);
		$formatted_news = str_replace('<li>', '<li class="'.$this->slug.'_dashboard_news_item">'.'<a href="'.UpdraftPlus::get_current_clean_url().'" class="dashicons dashicons-no-alt" title="'.esc_attr($this->translations['dismiss_tooltip']).'" onClick="'.$this->slug.'_dismiss_dashboard_news(); return false;" style="float: right; box-shadow: none; margin-left: 5px;"></a>', $formatted_news);
		set_transient($this->slug.'_dashboard_news', $formatted_news, 43200); // 12 hours

		return $formatted_news;
	}
	
	/**
	 * Prints javascripts in admin footer
	 */
	public function admin_print_footer_scripts() {
		?>
		<script>
		function <?php echo $this->slug; ?>_dismiss_dashboard_news() {
			if (confirm("<?php echo esc_js($this->translations['dismiss_confirm']); ?>")) {
				jQuery.ajax({
					url: '<?php echo admin_url('admin-ajax.php');?>',
					data : {
						action: '<?php echo $this->slug; ?>_ajax_dismiss_dashboard_news',
						nonce : '<?php echo wp_create_nonce($this->slug.'-dismiss-news-nonce');?>'
					},
					success: function(response) {
						jQuery('.<?php echo $this->slug; ?>_dashboard_news_item').slideUp('slow');
					},
					error: function(response, status, error_code) {
						console.log("<?php echo $this->slug; ?>_dismiss_dashboard_news: error: "+status+" ("+error_code+")");
						console.log(response);
					}
				});
			}
		}
		</script>
		<?php
	}
	
	/**
	 * Dismiss dashboard news
	 */
	public function dismiss_dashboard_news() {
		$nonce = empty($_REQUEST['nonce']) ? '' : $_REQUEST['nonce'];
		if (!wp_verify_nonce($nonce, $this->slug.'-dismiss-news-nonce')) die('Security check.');
		
		update_user_meta(get_current_user_id(), $this->slug.'_dismiss_dashboard_news', true);
		die();
	}
}
endif;
