<?php
/**
 * Wishlists function class
 *
 * @since             1.5.0
 * @package           TInvWishlist\Wishlists
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Wishlists function class
 */
class TInvWL_Wishlist
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	private $table;
	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;
	/**
	 * User id
	 *
	 * @var integer
	 */
	public $user;
	/**
	 * Default share key for wishlist
	 *
	 * @var string
	 */
	public static $default_sharekey;
	/**
	 * Default name wishlist
	 *
	 * @var string
	 */
	private $default_name;
	/**
	 * Default privacy wishlist
	 *
	 * @var string
	 */
	private $default_privacy;
	/**
	 * Allowed privacy
	 *
	 * @var array
	 */
	private $privacy;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @global wpdb $wpdb
	 *
	 */
	function __construct($plugin_name = TINVWL_PREFIX)
	{
		global $wpdb;

		$this->_name = $plugin_name;
		$this->table = sprintf('%s%s_%s', $wpdb->prefix, $this->_name, 'lists');
		$this->default_name = apply_filters('tinvwl_default_wishlist_title', tinv_get_option('general', 'default_title'));
		$this->default_privacy = 'share';
		$this->privacy = array('public', 'share', 'private');
		if (!in_array($this->default_privacy, $this->privacy)) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$this->default_privacy = 'share';
		}
		$this->user = get_current_user_id();
	}

	/**
	 * Generate unique share key
	 *
	 * @return string
	 * @global wpdb $wpdb
	 */
	function unique_share_key()
	{
		global $wpdb;

		$share_key = substr(md5(date('r') . mt_rand(0, 3000)), 0, 6);
		$unique = false;
		while ($unique === false) {
			$unique = !$wpdb->get_var($wpdb->prepare("SELECT `ID` FROM `{$this->table}` WHERE `share_key` = %s", $share_key));
			$share_key = substr(md5(date('r') . mt_rand(0, 3000)), 0, 6);
		}

		return $share_key;
	}

	/**
	 * Get/Create default wishlist
	 *
	 * @param integer $user_id Can put 0.
	 *
	 * @return boolean|array
	 */
	function add_user_default($user_id = 0)
	{
		if (empty($user_id)) {
			$user_id = $this->user;
		}
		if (empty($user_id)) {
			return $this->add_sharekey_default();
		}

		if (!current_user_can('tinvwl_general_settings') && ($user_id != get_current_user_id())) {
			return false;
		}

		if ($wl = $this->get_by_user_default($user_id)) {
			return array_shift($wl);
		}

		$wl = $this->add('', 'default', $this->default_privacy, $user_id);
		if (is_array($wl) && array_key_exists('share_key', $wl)) {
			$this->set_sharekey($wl['share_key']);

			return $wl;
		}

		return false;
	}

	/**
	 * Get/Create default wishlist
	 *
	 * @param string $sharekey Can put empty.
	 *
	 * @return boolean|array
	 */
	function add_sharekey_default($sharekey = '')
	{
		if ($wl = $this->get_by_sharekey_default($sharekey)) {
			return array_shift($wl);
		}

		$wl = $this->add('', 'default', $this->default_privacy, 0);
		if (is_array($wl) && array_key_exists('share_key', $wl)) {
			$this->set_sharekey($wl['share_key']);

			return $wl;
		}

		return false;
	}

	/**
	 * Add wishlist
	 *
	 * @param mixed $data wishlist name or object.
	 * @param string $type List or default.
	 * @param string $status Public, Share, Private.
	 * @param integer $user_id Can put 0.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	function add($data, $type = 'list', $status = 'public', $user_id = 0)
	{
		$user_id = absint($user_id);
		if (empty($user_id)) {
			$user_id = $this->user;
		}

		$default = array(
			'author' => $user_id,
			'date' => current_time('Y-m-d H:i:s'),
			'status' => $this->default_privacy,
			'share_key' => $this->unique_share_key(),
			'title' => $this->default_name,
			'type' => 'list',
		);

		if (!is_array($data)) {
			$data = array(
				'title' => $data,
				'status' => $status,
				'type' => $type,
			);
		}
		$data = wp_parse_args($data, $default);
		$data = apply_filters('tinvwl_wishlist_add', $data);
		if (!in_array($data['status'], $this->privacy)) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$data['status'] = 'public';
		}
		global $wpdb;
		if ($wpdb->insert($this->table, $data)) { // @codingStandardsIgnoreLine WordPress.VIP.DirectDatabaseQuery.DirectQuery
			$data['ID'] = $wpdb->insert_id;

			/* Run a 3rd party code when a new wishlist created.
			 *
			 * @param array $data A wishlist data.
			 * */
			do_action('tinvwl_wishlist_created', $data);

			return $data;
		}

		return false;
	}

	/**
	 * Get default wishlist for user id
	 *
	 * @param integer $user_id Can put 0.
	 *
	 * @return array
	 */
	function get_by_user_default($user_id = 0)
	{
		$user_id = absint($user_id);
		if (empty($user_id)) {
			$user_id = $this->user;
		}

		$data = array(
			'author' => $user_id,
			'type' => 'default',
		);

		if (!current_user_can('tinvwl_general_settings') && (empty($this->user) || ($data['author'] != $this->user))) { // WPCS: loose comparison ok.
			$data['status'] = 'public';
		}

		return $this->get($data);
	}

	/**
	 * Get default wishlist for user id
	 *
	 * @param string $sharekey Can put empty.
	 *
	 * @return array
	 */
	function get_by_sharekey_default($sharekey = '')
	{
		if (empty($sharekey)) {
			$sharekey = $this->get_sharekey();
		}
		if (empty($sharekey)) {
			return array();
		}

		$data = array(
			'share_key' => $sharekey,
			'type' => 'default',
		);

		return $this->get($data);
	}

	/**
	 * Get wishlist for user id
	 *
	 * @param integer $user_id Can put 0.
	 * @param array $data Requset.
	 *
	 * @return array
	 */
	function get_by_user($user_id = 0, $data = array())
	{
		$user_id = absint($user_id);
		if (empty($user_id)) {
			$user_id = $this->user;
		}
		$this->add_user_default($user_id);
		$_data = array(
			'author' => $user_id,
		);

		if (!current_user_can('tinvwl_general_settings') && (empty($this->user) || ($_data['author'] != $this->user))) { // WPCS: loose comparison ok.
			$_data['status'] = 'public';
		}
		$data = tinv_array_merge($data, $_data);

		return $this->get($data);
	}

	/**
	 * Get wishlist by id
	 *
	 * @param integer $id id database wishlist.
	 *
	 * @return array
	 */
	function get_by_id($id)
	{
		$id = absint($id);
		if (empty($id)) {
			return null;
		}

		$wishlists = $this->get(array('ID' => $id));
		$wishlist = array_shift($wishlists);

		return $wishlist;
	}

	/**
	 * Get wishlist by share key
	 *
	 * @param string $share_key Share key.
	 *
	 * @return array
	 */
	function get_by_share_key($share_key)
	{
		if (!preg_match('/[a-f0-9]{6}/i', $share_key)) {
			return null;
		}
		$wishlists = $this->get(array('share_key' => $share_key));
		$wishlist = array_shift($wishlists);

		return $wishlist;
	}

	/**
	 * Get wishlist
	 *
	 * @param array $data Requset.
	 *
	 * @return array
	 * @global wpdb $wpdb
	 *
	 */
	function get($data = array())
	{
		global $wpdb;

		$default = array(
			'count' => 10,
			'field' => null,
			'offset' => 0,
			'order' => 'ASC',
			'order_by' => 'title',
			'sql' => '',
		);

		foreach ($default as $_k => $_v) {
			if (array_key_exists($_k, $data)) {
				$default[$_k] = $data[$_k];
				unset($data[$_k]);
			}
		}

		if (is_array($default['field'])) {
			$default['field'] = '`' . implode('`,`', $default['field']) . '`';
		} elseif (is_string($default['field'])) {
			$default['field'] = array('ID', 'type', $default['field']);
			$default['field'] = '`' . implode('`,`', $default['field']) . '`';
		} else {
			$default['field'] = '*';
		}
		$sql = "SELECT {$default[ 'field' ]} FROM `{$this->table}`";

		$where = '1';
		if (!empty($data) && is_array($data)) {
			foreach ($data as $f => $v) {
				$s = is_array($v) ? ' IN ' : '=';
				if (is_array($v)) {
					foreach ($v as $_f => $_v) {
						$v[$_f] = $wpdb->prepare('%s', $_v);
					}
					$v = implode(',', $v);
					$v = "($v)";
				} else {
					$v = $wpdb->prepare('%s', $v);
				}
				$data[$f] = sprintf('`%s`%s%s', $f, $s, $v);
			}
			$where = ' WHERE ' . implode(' AND ', $data);
			$sql .= $where;
		}
		$sql .= sprintf(' ORDER BY `%s` %s LIMIT %d,%d;', $default['order_by'], $default['order'], $default['offset'], $default['count']);

		if (!empty($default['sql'])) {
			$replacer = $replace = array();
			$replace[0] = '{table}';
			$replacer[0] = $this->table;
			$replace[1] = '{where}';
			$replacer[1] = $where;

			foreach ($default as $key => $value) {
				$i = count($replace);

				$replace[$i] = '{' . $key . '}';
				$replacer[$i] = $value;
			}

			$sql = str_replace($replace, $replacer, $default['sql']);
		}

		$wls = $wpdb->get_results($sql, ARRAY_A); // WPCS: db call ok; no-cache ok; unprepared SQL ok.

		if (empty($wls)) {
			return array();
		}

		foreach ($wls as $k => $wl) {
			$wl['ID'] = absint($wl['ID']);
			if (array_key_exists('author', $wl)) {
				$wl['author'] = absint($wl['author']);
			}
			if ('default' === $wl['type'] && empty($wl['title'])) {
				$wl['title'] = $this->default_name;
			}

			$wls[$k] = apply_filters('tinvwl_wishlist_get', $wl);
		}

		return $wls;
	}

	/**
	 * Update wishlist
	 *
	 * @param integer $id id database wishlist.
	 * @param mixed $data wishlist name or object.
	 * @param string $type List or default.
	 * @param string $status Public, Share, Private.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	function update($id, $data, $type = 'list', $status = 'public')
	{
		if (!is_array($data)) {
			$data = array(
				'title' => $data,
				'status' => $status,
				'type' => $type,
			);
		}
		$data = filter_var_array($data, apply_filters('tinvwl_wishlist_fields_update', array(
			'title' => FILTER_SANITIZE_STRING,
			'status' => FILTER_SANITIZE_STRING,
			'type' => FILTER_SANITIZE_STRING,
			'author' => FILTER_VALIDATE_INT,
		)));
		$data = array_filter($data);
		$data = apply_filters('tinvwl_wishlist_update', $data, $id);
		if (!array_key_exists('title', $data)) {
			$wishlist = $this->get_by_id($id);
			if ('default' === $wishlist['type']) {
				$data['title'] = '';
			}
		}
		global $wpdb;

		return false !== $wpdb->update($this->table, $data, array('ID' => $id)); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
	}

	/**
	 * Remove wishlist
	 *
	 * @param integer $id id database wishlist.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public function remove($id)
	{
		$id = absint($id);
		if (empty($id)) {
			return false;
		}
		global $wpdb;
		$result = $wpdb->delete($this->table, array('ID' => $id)); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		if (false !== $result) {
			do_action('tinvwl_wishlist_removed', $id);
			$wlp = new TInvWL_Product();
			$wlp->remove_product_from_wl($id);

			return true;
		}

		return false;
	}

	/**
	 * Set share key for default wishlist
	 *
	 * @param string $sharekey Sharekey for default wishlist.
	 *
	 * @return string
	 */
	function set_sharekey($sharekey = '')
	{
		global $tinvwl_wishlist_sharekey;

		if (!empty($sharekey)) {
			self::$default_sharekey = $tinvwl_wishlist_sharekey = $sharekey;
			@setcookie('tinv_wishlistkey', self::$default_sharekey, time() + 31 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
			set_transient('_tinvwl_update_wishlists_data', '1');
		}

		return self::$default_sharekey;
	}

	/**
	 * Get variable for default share key
	 *
	 * @return string
	 */
	function get_sharekey()
	{
		global $tinvwl_wishlist_sharekey;

		if (empty(self::$default_sharekey)) {
			self::$default_sharekey = $tinvwl_wishlist_sharekey = filter_input(INPUT_COOKIE, 'tinv_wishlistkey', FILTER_VALIDATE_REGEXP, array(
				'options' => array(
					'regexp' => '/^[A-Fa-f0-9]{6}$/',
					'default' => $tinvwl_wishlist_sharekey,
				),
			));
		}

		return self::$default_sharekey;
	}
}
