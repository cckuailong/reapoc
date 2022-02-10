<?php
/**
 * Activation/Deactivation plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Activation/Deactivation plugin class
 */
class TInvWL_Activator
{

	/**
	 * Plugin name
	 *
	 * @var string Using defined constant.
	 */
	static $_name = TINVWL_PREFIX;

	/**
	 * Prefix database
	 *
	 * @see wpdb
	 * @var string
	 */
	static $wpdb_prefix;

	/**
	 * Database version
	 *
	 * @var string
	 */
	static $_version = TINVWL_FVERSION;

	/**
	 * Current installed database version
	 *
	 * @var string
	 */
	static $_prev;

	/**
	 * Regular expression for sorting database version function
	 *
	 * @var string
	 */
	const REGEXP = '/^database_/i';

	/**
	 * Method activation plugin.
	 */
	public static function activate()
	{
		if (self::update()) {
			return false;
		}
		if (is_null(get_option(self::$_name . '_db_ver', null))) {
			TInvWL_WizardSetup::setup();
		}
		self::database();
		self::load_data();
		TInvWL_Public_TInvWL::update_rewrite_rules();
	}

	/**
	 * Method deactivation plugin.
	 */
	public static function uninstall()
	{
		self::database('destroy');
		self::unload_data();
		self::remove_capabities();
	}

	/**
	 * Method update plugin.
	 */
	public static function update()
	{
		$current_version = get_option(self::$_name . '_db_ver', self::$_version);
		if (version_compare(self::$_version, $current_version, 'gt')) {
			self::database('upgrade', $current_version);
			self::upgrade_data();
			TInvWL_Public_TInvWL::update_rewrite_rules();

			return true;
		}

		return false;
	}

	/**
	 * Require function database and apply method
	 *
	 * @param string $action create|destroy.
	 * @param string $prev Current installed version.
	 */
	private static function database($action = 'create', $prev = 'f.0')
	{

		$activator = new TInvWL_Activator();
		$lists = get_class_methods($activator);
		unset($activator);

		self::$_prev = $prev;
		$lists = array_filter($lists, array(__CLASS__, 'filter_database'));
		uasort($lists, array(__CLASS__, 'sort_database'));

		$tables = array();
		foreach ($lists as $method) {
			$tables = self::merge_database($tables, $method);
		}
		if (!empty($tables) && is_array($tables)) {
			self::$action($tables);
		}
	}

	/**
	 * Merge table with upgrade attributes
	 *
	 * @param array $tables1 Collecting tables.
	 * @param string $method Method for get new tables.
	 *
	 * @return array
	 */
	public static function merge_database($tables1, $method)
	{
		$tables2 = self::$method();
		foreach ((array)$tables2 as $name => $table) {
			if (empty($table)) {
				continue;
			}
			if (array_key_exists('upgrade', $table)) {
				$_upgrade = $table['upgrade'];
				$table['upgrade'] = array();
				$table['upgrade'][self::pre_database($method)] = $_upgrade;
			}
			if (array_key_exists($name, $tables1)) {
				if (!array_key_exists('upgrade', $tables1[$name])) {
					$tables1[$name]['upgrade'] = array();
				}
				if (!array_key_exists('upgrade', $table)) {
					$table['upgrade'] = array();
				}
				$table['upgrade'] = tinv_array_merge($tables1[$name]['upgrade'], $table['upgrade']);
			}
			if (empty($table['field'])) {
				$tables1[$name]['upgrade'] = $table['upgrade'];
			} else {
				$tables1[$name] = $table;
			}
		}

		return $tables1;
	}

	/**
	 * Filter methods for creating database
	 *
	 * @param string $method Method name from this class.
	 *
	 * @return boolean
	 */
	public static function filter_database($method)
	{
		if (!preg_match(self::REGEXP, $method)) {
			return false;
		}
		if (version_compare(self::$_prev, self::pre_database($method), 'ge')) {
			return false;
		}

		return version_compare(self::$_version, self::pre_database($method), 'ge');
	}

	/**
	 * Sort methods for creating database
	 *
	 * @param string $method1 Method name first from this class.
	 * @param string $method2 Method name second from this class.
	 *
	 * @return type
	 */
	public static function sort_database($method1, $method2)
	{
		return version_compare(self::pre_database($method1), self::pre_database($method2));
	}

	/**
	 * Convert method name to version
	 *
	 * @param string $method Method name from this class.
	 *
	 * @return string
	 */
	public static function pre_database($method)
	{
		$method = preg_replace(self::REGEXP, '', $method);
		$method = str_replace('_', '.', $method);

		return $method;
	}

	/**
	 * Creation SQL request for creation table
	 *
	 * @param string $name Name Table.
	 * @param array $table Structured array table.
	 *            array    'field'        Array fields.
	 *            string    'charset'    Charset table.
	 *            string    'key'        Primary key.
	 *            string    'index'       Custom index.
	 *            string    'post'        Custom elements in format SQL.
	 *
	 * @return string
	 */
	public static function table($name, $table)
	{
		$name = self::$wpdb_prefix . self::$_name . '_' . $name;
		$fields = $table['field'];
		$index = (isset($table['index'])) ? $table['index'] : null;
		$table = filter_var_array($table, array(
			'charset' => FILTER_SANITIZE_STRING,
			'key' => FILTER_SANITIZE_STRING,
			'post' => FILTER_DEFAULT,
		));

		$table['charset'] = (empty($table['charset'])) ? 'utf8' : $table['charset'];
		$table['charset'] = sprintf('DEFAULT CHARSET=%s', $table['charset']);

		$keys = array_keys($fields);
		if (!in_array($table['key'], $keys)) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$table['key'] = null;
		}
		$t = self::column_database();
		foreach ($fields as $key => $flags) {
			if (is_string($flags)) {
				if (array_key_exists($flags, $t)) {
					$flags = $t[$flags];
				} else {
					$flags = $t['text'];
				}
			}
			$fields[$key] = $flags;
		}
		if (empty($table['key'])) {
			foreach ($fields as $key => $flags) {
				if (array_key_exists(4, $flags)) {
					if ($flags[4]) {
						$table['key'] = $key;
						break;
					}
				}
			}
			if (empty($table['key'])) {
				foreach ($keys as $key) {
					if (preg_match('/id$/i', $key)) {
						$table['key'] = $key;
						break;
					}
				}
			}
		}
		unset($keys);
		if (empty($table['key'])) {
			$table['key'] = '';
		} else {
			$table['key'] = sprintf(', PRIMARY KEY (`%s`)', $table['key']);
		}

		if (empty($table['post'])) {
			$table['post'] = '';
		} else {
			$table['post'] = ', ' . $table['post'];
		}
		$indexes = '';
		if ($index) {
			foreach ($index as $index_name => $columns) {
				$indexes = sprintf(', INDEX %s (%s)', $index_name, $columns);
			}
		}

		foreach ($fields as $key => $format) {
			$fields[$key] = self::field($key, $format);
		}
		$fields = implode(', ', $fields);

		$sql = sprintf('CREATE TABLE IF NOT EXISTS `%s` ( %s%s%s%s) %s; ', $name, $fields, $table['key'], $indexes, $table['post'], $table['charset']);

		return $sql;
	}

	/**
	 * Created SQL field
	 *
	 * @param string $name Name field.
	 * @param array $newformat Structure field.
	 *            string            0    Data Types column.
	 *            integer|string    1    Length or Size column.
	 *            boolean            2    is NULL?
	 *            integer|string    3    Specifies a default value for a column.
	 *            boolean            4    is AUTO_INCREMENT?.
	 *
	 * @return string
	 */
	public static function field($name, $newformat)
	{
		$format = array('TEXT', null, false, null, false);

		foreach (array_keys($format) as $key) {
			if (array_key_exists($key, $newformat)) {
				$format[$key] = $newformat[$key];
			}
		}

		if (!is_null($format[1])) {
			$format[1] = sprintf('(%s)', $format[1]);
		}

		$format[2] = (filter_var($format[2], FILTER_VALIDATE_BOOLEAN) ? '' : 'NOT ') . 'NULL';

		if (!is_null($format[3])) {
			if (!in_array($format[3], array('CURRENT_TIMESTAMP'))) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
				$format[3] = is_string($format[3]) ? "'" . $format[3] . "'" : $format[3];
			}
			$format[3] = sprintf('DEFAULT %s', $format[3]);
		}

		$format[4] = (filter_var($format[4], FILTER_VALIDATE_BOOLEAN) ? 'AUTO_INCREMENT' : null);

		array_unshift($format, '`' . $name . '`');
		$format = array_filter($format);

		return implode(' ', $format);
	}

	/**
	 * Created tables from array
	 *
	 * @param array $tables Array tables.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function create($tables)
	{
		global $wpdb;
		self::$wpdb_prefix = $wpdb->prefix;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		foreach ($tables as $name => $table) {
			if (array_key_exists('field', $table) && !empty($table['field'])) {
				$table = self::table($name, $table);
				$tables[$name] = dbDelta($table);
			}
		}

		add_option(self::$_name . '_db_ver', self::$_version);

		return true;
	}

	/**
	 * Upgrade tables from array
	 *
	 * @param array $tables Array tables.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade($tables)
	{

		self::create($tables);

		foreach ($tables as $name => $table) {
			self::upgrade_action($name, $table);
		}

		update_option(self::$_name . '_db_ver', self::$_version);

		return true;
	}

	/**
	 * Get columns for exist table.
	 *
	 * @param string $name Table name.
	 *
	 * @return array
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_get_columns($name)
	{
		global $wpdb;

		$_fields = array();
		$fields = $wpdb->get_results("DESCRIBE `{$name}`", ARRAY_A); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		foreach ($fields as $field) {
			$_fields[$field['Field']] = $field;
		}

		return $_fields;
	}

	/**
	 * Apply upgrade action
	 *
	 * @param string $name Name Table.
	 * @param array $table Structured array table.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action($name, $table)
	{

		if (!array_key_exists('upgrade', $table)) {
			return false;
		}

		$t = self::column_database();
		foreach ($table['field'] as $key => $flags) {
			if (is_string($flags)) {
				if (array_key_exists($flags, $t)) {
					$flags = $t[$flags];
				} else {
					$flags = $t['text'];
				}
				$table['field'][$key] = $flags;
			}
		}
		$name = self::$wpdb_prefix . self::$_name . '_' . $name;
		$upgrades = (array)$table['upgrade'];
		foreach ($upgrades as $ver_upgrades) {
			foreach ($ver_upgrades as $upgrade) {
				$action = $upgrade;
				if (is_array($upgrade)) {
					$action = $upgrade['action'];
					unset($upgrade['action']);
				}
				if (!is_string($action)) {
					continue;
				}
				$action = preg_replace('/[^a-z0-9_]/i', '', (string)$action);
				if (empty($action)) {
					continue;
				}

				if (method_exists(__CLASS__, __FUNCTION__ . '_' . $action)) {
					call_user_func(array(__CLASS__, __FUNCTION__ . '_' . $action), $name, $table, $upgrade);
				}
			}
		}
	}

	/**
	 * Apply upgrade action
	 * Truncate table
	 *
	 * @param string $name Table name.
	 *
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_truncate($name)
	{
		global $wpdb;
		$wpdb->query("TRUNCATE `{$name}`"); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
	}

	/**
	 * Apply upgrade action
	 * Drop table
	 *
	 * @param string $name Table name.
	 *
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_drop($name)
	{
		global $wpdb;
		$sql = sprintf('DROP TABLE IF EXISTS `%s`;', $name);
		$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
	}

	/**
	 * Apply upgrade action
	 * Rename table
	 *
	 * @param string $name Table name.
	 * @param array $table Not used.
	 * @param array $upgrade Upgrade fields.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_rename_table($name, $table, $upgrade)
	{
		global $wpdb;
		if (!array_key_exists('from', $upgrade)) {
			return false;
		}
		$_name = self::$wpdb_prefix . self::$_name . '_' . $upgrade['from'];

		$_t_name = $wpdb->get_var("SHOW TABLES LIKE '{$_name}'"); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		if ($_t_name == $_name) { // WPCS: loose comparison ok.
			self::upgrade_action_drop($name);
		}
		$wpdb->query(sprintf('RENAME TABLE `%s` TO `%s`;', $_name, $name)); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
	}

	/**
	 * Apply upgrade action
	 * Update fields table
	 *
	 * @param string $name Table name.
	 * @param array $table Table array.
	 *
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_update_fields($name, $table)
	{
		global $wpdb;

		$_fields = self::upgrade_get_columns($name);
		$fields = $table['field'];

		// Search excess fields.
		foreach ($_fields as $field => $attr) {
			if (!array_key_exists($field, $fields)) {
				$sql = sprintf('ALTER TABLE `%s` DROP COLUMN `%s`;', $name, $field);
				$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
			}
		}

		$prev_field = '';

		foreach ($fields as $field => $attr) {
			$attr = self::field($field, $attr);

			$sql = sprintf('ALTER TABLE `%s` MODIFY %s;', $name, $attr);
			if (!array_key_exists($field, $_fields)) {
				$_prev_field = empty($prev_field) ? '' : " AFTER `{$prev_field}`";
				$sql = sprintf('ALTER TABLE `%s` ADD %s;', $name, $attr . $_prev_field);
			}
			$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.

			$prev_field = $field;
		}
	}

	/**
	 * Apply upgrade action
	 * Add table index
	 *
	 * @param string $name Table name.
	 * @param array $table Table array.
	 *
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_add_index($name, $table)
	{
		global $wpdb;

		$indexes = $table['index'];

		foreach ($indexes as $index => $columns) {

			$sql = sprintf('ALTER TABLE `%s` ADD INDEX %s (%s);', $name, $index, $columns);

			$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.

		}
	}

	/**
	 * Apply upgrade action
	 * Update field table
	 *
	 * @param string $name Table name.
	 * @param array $table Table array.
	 * @param array $upgrade Upgrade fields.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_update_field($name, $table, $upgrade)
	{
		global $wpdb;
		if (!array_key_exists('field', $upgrade)) {
			return false;
		}
		$name_field = $upgrade['field'];

		$_fields = self::upgrade_get_columns($name);
		$fields = $table['field'];

		$sql = '';

		if (array_key_exists($name_field, $fields)) {
			$prev_field = '';
			foreach ($fields as $field => $attr) {
				if ($name_field == $field) { // WPCS: loose comparison ok.
					break;
				}
				$prev_field = $field;
			}

			$attr = self::field($name_field, $fields[$name_field]);

			$sql = sprintf('ALTER TABLE `%s` MODIFY %s;', $name, $attr);
			if (!array_key_exists($name_field, $_fields)) {
				$_prev_field = empty($prev_field) ? '' : " AFTER `{$prev_field}`";
				$sql = sprintf('ALTER TABLE `%s` ADD %s;', $name, $attr . $_prev_field);
			}
		} else {
			if (!array_key_exists($name_field, $fields)) {
				$sql = sprintf('ALTER TABLE `%s` DROP COLUMN `%s`;', $name, $name_field);
			}
		}
		if (!empty($sql)) {
			$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}
	}

	/**
	 * Apply upgrade action
	 * Rename field.
	 *
	 * @param string $name Table name.
	 * @param array $table Table array.
	 * @param array $upgrade Upgrade fields.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_rename_field($name, $table, $upgrade)
	{
		global $wpdb;
		if (!array_key_exists('from', $upgrade) || !array_key_exists('to', $upgrade)) {
			return false;
		}
		$old_field = $upgrade['from'];
		$new_field = $upgrade['to'];

		$_fields = self::upgrade_get_columns($name);
		$fields = $table['field'];

		if (!array_key_exists($old_field, $_fields) || !array_key_exists($new_field, $fields)) {
			return false;
		}

		if (array_key_exists($new_field, $_fields)) {
			$sql = sprintf('ALTER TABLE `%s` DROP COLUMN `%s`;', $name, $new_field);
			$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}

		$attr = self::field($new_field, $fields[$new_field]);

		$sql = sprintf('ALTER TABLE `%s` CHANGE `%s` %s;', $name, $old_field, $attr);
		$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.

		return true;
	}

	/**
	 * Apply upgrade action
	 * Use sql.
	 *
	 * @param string $name Table name.
	 * @param array $table Not used.
	 * @param array $upgrade Upgrade fields.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function upgrade_action_sql($name, $table, $upgrade)
	{
		global $wpdb;
		if (!array_key_exists('sql', $upgrade)) {
			return false;
		}
		$name = self::$wpdb_prefix . self::$_name . '_' . $name;

		$wpdb->query(str_replace('{table_name}', $name, $upgrade['sql'])); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
	}

	/**
	 * Destroy tables from array
	 *
	 * @param type $tables Array tables.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 *
	 */
	public static function destroy($tables)
	{
		global $wpdb;
		self::$wpdb_prefix = $wpdb->prefix;

		foreach ($tables as $name => $table) {
			$table = self::$wpdb_prefix . self::$_name . '_' . $name;
			$sql = sprintf('DROP TABLE IF EXISTS `%s`;', $table);
			$wpdb->query($sql); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}

		delete_option(self::$_name . '_db_ver');

		return true;
	}

	/**
	 * Predefined fields attributes
	 *
	 * @return array
	 */
	public static function column_database()
	{
		/**
		 * Array format for fields
		 *
		 * @param string            TYPE
		 * @param integer|string    SIZE
		 * @param boolean            NULL
		 * @param integer|string    DEFAULT
		 * @param boolean            AUTO_INCREMENT
		 */
		return array(
			'first++' => array('BIGINT', null, false, null, true),
			'int_0' => array('BIGINT', null, false, 0),
			'int_1' => array('BIGINT', null, false, 1),
			'text' => array('TEXT', null, true, null),
			'longtext' => array('LONGTEXT'),
			'date' => array('DATETIME', null, false, '0000-00-00 00:00:00'),
			'bool' => array('TINYINT', 1, false, 1),
		);
	}

	/**
	 * Database
	 *
	 * @return array
	 * @since             1.0.0
	 */
	private static function database_1_0_0()
	{
		$t = array(
			'status' => array('VARCHAR', 20, false, 'public'),
			'type' => array('VARCHAR', 20, false, 'list'),
			'key_elem' => array('VARCHAR', 45),
			'key_user' => array('TINYINT', 1, false, 7),
		);

		return array(
			'lists' => array(
				'field' => array(
					'ID' => 'first++',
					'author' => 'int_0',
					'date' => 'date',
					'title' => 'text',
					'status' => $t['status'],
					'type' => $t['type'],
					'share_key' => $t['key_elem'],
				),
			),
			'items' => array(
				'field' => array(
					'ID' => 'first++',
					'wishlist_id' => 'int_0',
					'product_id' => 'int_0',
					'variation_id' => 'int_0',
					'author' => 'int_0',
					'date' => 'date',
					'quantity' => 'int_1',
					'price' => $t['key_elem'],
					'in_stock' => 'bool',
				),
			),
		);
	}

	/**
	 * Database
	 *
	 * @return array
	 * @since             1.5.0
	 */
	private static function database_1_5_0()
	{
		return array(
			'items' => array(
				'field' => array(
					'ID' => 'first++',
					'wishlist_id' => 'int_0',
					'product_id' => 'int_0',
					'variation_id' => 'int_0',
					'formdata' => 'text',
					'author' => 'int_0',
					'date' => 'date',
					'quantity' => 'int_1',
					'price' => array('VARCHAR', 255),
					'in_stock' => 'bool',
				),
				'upgrade' => array(
					array(
						'action' => 'update_fields',
					),
				),
			),
		);
	}

	/**
	 * Database
	 *
	 * @return array
	 * @since             1.8.13
	 */
	private static function database_1_8_13()
	{
		$t = array(
			'status' => array('VARCHAR', 20, false, 'public'),
			'type' => array('VARCHAR', 20, false, 'list'),
			'key_elem' => array('VARCHAR', 45),
			'key_user' => array('TINYINT', 1, false, 7),
		);

		return array(
			'lists' => array(
				'field' => array(
					'ID' => 'first++',
					'author' => 'int_0',
					'date' => 'date',
					'title' => 'text',
					'status' => $t['status'],
					'type' => $t['type'],
					'share_key' => $t['key_elem'],
				),
				'upgrade' => array(
					array(
						'action' => 'update_fields',
					),
				),
			),
			'items' => array(
				'field' => array(
					'ID' => 'first++',
					'wishlist_id' => 'int_0',
					'product_id' => 'int_0',
					'variation_id' => 'int_0',
					'formdata' => 'text',
					'author' => 'int_0',
					'date' => 'date',
					'quantity' => 'int_1',
					'price' => array('VARCHAR', 255),
					'in_stock' => 'bool',
				),
				'upgrade' => array(
					array(
						'action' => 'update_fields',
					),
				),
			),
		);
	}

	/**
	 * Database
	 *
	 * @return array
	 * @since             1.9.16
	 */
	private static function database_1_9_16()
	{

		return array(
			'items' => array(
				'field' => array(
					'ID' => 'first++',
					'wishlist_id' => 'int_0',
					'product_id' => 'int_0',
					'variation_id' => 'int_0',
					'formdata' => 'text',
					'author' => 'int_0',
					'date' => 'date',
					'quantity' => 'int_1',
					'price' => array('VARCHAR', 255, false, 0),
					'in_stock' => 'bool',
				),
				'upgrade' => array(
					array(
						'action' => 'update_fields',
					),
				),
			),
		);
	}

	/**
	 * Database
	 *
	 * @return array
	 * @since             1.10.0
	 */
	private static function database_1_10_0()
	{

		return array(
			'analytics' => array(
				'field' => array(
					'ID' => array('VARCHAR', 32),
					'wishlist_id' => 'int_0',
					'product_id' => 'int_0',
					'variation_id' => 'int_0',
					'visite_author' => 'int_0',
					'visite' => 'int_0',
					'click_author' => 'int_0',
					'click' => 'int_0',
					'cart' => 'int_0',
					'sell_of_wishlist' => 'int_0',
					'sell_as_gift' => 'int_0',
				),
				'key' => 'ID',
				'index' => array(
					'unique_product' => 'wishlist_id, product_id, variation_id',
				),
			),
		);
	}

	/**
	 * Set localisation
	 */
	private static function set_locale()
	{
		$locale = apply_filters('plugin_locale', get_locale(), TINVWL_DOMAIN);
		$mofile = sprintf('%1$s-%2$s.mo', TINVWL_DOMAIN, $locale);
		$mofiles = array();

		$mofiles[] = WP_LANG_DIR . DIRECTORY_SEPARATOR . basename(TINVWL_PATH) . DIRECTORY_SEPARATOR . $mofile;
		$mofiles[] = WP_LANG_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $mofile;
		$mofiles[] = TINVWL_PATH . 'languages' . DIRECTORY_SEPARATOR . $mofile;
		foreach ($mofiles as $mofile) {
			if (file_exists($mofile) && load_textdomain(TINVWL_DOMAIN, $mofile)) {
				return;
			}
		}

		load_plugin_textdomain(TINVWL_DOMAIN, false, basename(TINVWL_PATH) . DIRECTORY_SEPARATOR . 'languages');
	}

	/**
	 * Load default data
	 */
	public static function load_data()
	{
		self::set_locale();
		$settings = tinv_get_option_defaults('all');
		foreach ($settings as $setting => $array) {
			add_option(sprintf('%s-%s', self::$_name, $setting), $array);
		}
	}

	/**
	 * Upgrade default data
	 */
	public static function upgrade_data()
	{
		self::set_locale();
		$settings = tinv_get_option_defaults('all');
		foreach ($settings as $setting => $array) {
			$_array = get_option(sprintf('%s-%s', self::$_name, $setting));
			if (false === $_array) {
				add_option(sprintf('%s-%s', self::$_name, $setting), $array);
			} else {
				$need_upgrade = false;
				foreach ((array)$array as $key => $value) {
					if (!array_key_exists($key, (array)$_array)) {
						$_array[$key] = $value;
						$need_upgrade = true;
					}
				}
				if ($need_upgrade) {
					update_option(sprintf('%s-%s', self::$_name, $setting), $_array);
				}
			}
		}
	}

	/**
	 * Unload default data
	 */
	public static function unload_data()
	{
		$settings = array_keys(tinv_get_option_defaults('all'));
		foreach ($settings as $setting) {
			delete_option(sprintf('%s-%s', self::$_name, $setting));
		}
		delete_option(self::$_name . '_ver');
		delete_option(self::$_name . '_wizard');
	}

	/**
	 * Remove 'administrator' role capabilities
	 */
	public static function remove_capabities()
	{
		global $wp_roles;

		$role = $wp_roles->get_role('administrator');
		if ($role) {
			foreach ($role->capabilities as $key => $value) {
				if (strpos($key, self::$_name) === 0) {
					$wp_roles->remove_cap('administrator', $key);
				}
			}
		}
	}
}
