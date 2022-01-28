<?php
abstract class tableUms {
    /**
     * ID column name
     */
    protected $_id ='';
    /**
     * Table name
     */
    protected $_table = '';
    /**
     * Array to store there fields for table
     */
    protected $_fields = array();
    /**
     * Alias for this table, make shure that it ia unique
     */
    protected $_alias = '';
    /**
     * Table to be joined
     */
    protected $_join = array();
    /**
     * Limit
     */
    protected $_limit = '';
    /**
     * Order BY
     */
    protected $_order = '';
    /**
     * Group BY
     */
    protected $_group = '';
    /**
     * Table errors data
     */
    protected $_errors = array();
	/**
	 * Escape data before action
	 */
	protected $_escape = false;

	protected $_limitFrom = '';
	protected $_limitTo = '';

    static public function getInstance($table = '') {
        static $instances = array();
		if(!$table) {
			throw new Exception('Unknown table ['. $table. ']');
		}
        if(!isset($instances[$table])) {
            $class = 'table'. strFirstUp($table). strFirstUp(UMS_CODE);
            if(class_exists($class))
                $instances[$table] = new $class();
            else
                $instances[$table] = NULL;  /*throw error must be here*/
        }
        return $instances[$table];
    }
    static public function _($table = '') {
        return self::getInstance($table);
    }
    public function innerJoin($table, $on) {
        $this->_join[] = 'INNER JOIN '. $table->getTable(). ' '. $table->alias(). ' ON '. $table->alias(). '.'. $table->getID(). ' = '. $this->_alias. '.'. $on;
        return $this;
    }
    public function leftJoin($table, $on) {
        if($this->haveField($on)) //If this table have such field - join on it
            $this->_join[] = 'LEFT JOIN '. $table->getTable(). ' '. $table->alias(). ' ON '. $table->alias(). '.'. $table->getID(). ' = '. $this->_alias. '.'. $on;
        else                     // else - let's join on field $on from other table
            $this->_join[] = 'LEFT JOIN '. $table->getTable(). ' '. $table->alias(). ' ON '. $table->alias(). '.'. $on. ' = '. $this->_alias. '.'. $this->getID();
        return $this;
    }
    public function arbitraryJoin($join) {
        $this->_join[] = $join;
    }
    public function haveField($field) {
        return isset($this->_fields[$field]);
    }
    public function addJoin($params = array('tbl' => '', 'a' => '', 'on' => '', 'joinOnID' => true, 'joinOn' => '')) {
        $params['joinOnID'] = isset($params['joinOnID']) ? $params['joinOnID'] : true;
        $params['joinOn'] = ($params['joinOnID'] && !isset($params['joinOn'])) ? $this->_id : $params['joinOn'];
        $this->_join[] = 'INNER JOIN '. $params['tbl']. ' '. $params['a']. ' ON '.$params['a'].'.'. $params['on'].' = '. $this->_alias. '.'. $params['joinOn'];
        return $this;
    }
    public function fillFromDB($id = 0, $where = '') {
        $res = $this;
        if($id)
            $data = $this->getById($id);
         elseif($where)
            $data = $this->get('*', $where);
         else
            $data = $this->getAll();

        if($data) {
            if($id) {
                foreach($data as $k => $v) {
                    if(isset($this->_fields[$k]))
                        $this->_fields[$k]->setValue($v, true);
                }
            } else {
                $res = array();
                foreach($data as $field) {
                    $row = array();
                    foreach($field as $k => $v) {
                        if(isset($this->_fields[$k])) {
                            $row[$k] = toeCreateObjUms('fieldUms', array(
                                    $this->_fields[$k]->name,
                                    $this->_fields[$k]->html,
                                    $this->_fields[$k]->type,
                                    $this->_fields[$k]->default,
                                    $this->_fields[$k]->label,
                                    $this->_fields[$k]->maxlen,
                                    $this->_fields[$k]->description
                                    ));
                            $row[$k]->setValue($v, true);
                        }
                    }
                    if(!empty($row))
                        $res[] = $row;
                }
            }
        }
        return $res;
    }
    /**
     * Return table name
     * @param bool $transform need to transform to standard WP tables view or not
     * @return string table name
     */
    public function getTable($transform = false) {
        if($transform)
            return dbUms::prepareQuery($this->_table);
        else
            return $this->_table;
    }
    public function setTable($table) {
        $this->_table = $talbe;
    }
    /**
     * Get name of ID column
     * @return string name of ID column
     */
    public function getID() {
        return $this->_id;
    }
    public function setID($id) {
        $this->_id = $id;
    }
    public function getAll($fields = '*') {
        return $this->get($fields);
    }
    public function getById($id, $fields = '*', $return = 'row') {
        $condition = 'WHERE '. $this->_alias. '.'. $this->_id. ' = "'. (int)$id. '"';
        return $this->get($fields, $condition, NULL, $return);
    }
    protected function _addJoin() {
        $res = '';
        if(!empty($this->_join)) {
            $res = ' '. implode(' ', $this->_join);
            $this->_join = array();
        }
        return $res;
    }
    /**
     * Add LIMIT to SQL
     */
    public function limit($limit = '') {
        if (is_numeric($limit)) {
            $this->_limit = $limit;
        } else {
            $this->_limit = '';
        }
		return $this;
    }
	public function setLimit($limit = '') {
        $this->_limit = $limit;
		return $this;
    }
	public function limitFrom($limit = '') {
        if (is_numeric($limit))
            $this->_limitFrom = (int)$limit;
		return $this;
    }
	public function limitTo($limit = '') {
        if (is_numeric($limit))
            $this->_limitTo = (int)$limit;
		return $this;
    }
    /**
     * Add ORDER BY to SQL
     *
     * @param mixed $fields
     */
    public function orderBy($fields){
        if (is_array($fields)) {
            $order = implode(',', $fields);
        } elseif ($fields != '') {
            $order = $fields;
        }
        $this->_order = $order;
		return $this;
    }
    /**
     * Add GROUP BY to SQL
     *
     * @param mixed $fields
     */
    public function groupBy($fields){
        if (is_array($fields)) {
            $group = implode(',', $fields);
        } elseif ($fields != '') {
            $group = $fields;
        }
        $this->_group = $group;
		return $this;
    }
    public function get($fields = '*', $where = '', $tables = '', $return = 'all') {
        if(!$tables) $tables = $this->_table. ' '. $this->_alias;
        if(strpos($this->_alias, $fields))
            $fields = $this->_alias. '.'. $fields;
        $query = 'SELECT '. $fields. ' FROM '. $tables;
        $query .= $this->_addJoin();
        if($where) {
            $where = trim($this->_getQueryString($where, 'AND'));
            if(!empty($where)) {
                if(!preg_match('/^WHERE/i', $where))
                    $where = 'WHERE '. $where;
                $query .= ' '. $where;
            }
        }
        if ($this->_group != '') {
            $query .= ' GROUP BY '.$this->_group;
            $this->_group = '';
        }
        if ($this->_order != '') {
            $query .= ' ORDER BY '.$this->_order;
            $this->_order = '';
        }
        if ($this->_limit != '') {
			if(is_numeric($this->_limit)) {
				$query .= ' LIMIT 0,'. $this->_limit;
			} else {
				$query .= ' LIMIT '. $this->_limit;
			}

            $this->_limit = '';
        } elseif($this->_limitFrom !== '' &&  $this->_limitTo !== '') {
			$query .= ' LIMIT '. $this->_limitFrom. ','. $this->_limitTo;
            $this->_limitFrom = '';
			$this->_limitTo = '';
		}
        return dbUms::get($query, $return);
    }
    public function store($data, $method = 'INSERT', $where = '') {
		$this->_clearErrors();
        $method = strtoupper($method);
		if($this->_escape) {
			$data = dbUms::escape($data);
		}
        $query = '';
        switch($method) {
            case 'INSERT':
                $query = 'INSERT INTO ';
                if(isset($data[$this->_id]) && empty($data[$this->_id]))
                    unset($data[$this->_id]);
                break;
            case 'UPDATE':
                $query = 'UPDATE ';
                break;
        }

        $fields = $this->_getQueryString($data, ',', true);

        if(empty($fields)) {
            $this->_addError(__('Nothing to update', UMS_LANG_CODE));
            return false;
        }

        $query .= $this->_table. ' SET '. $fields;

        if(!empty($this->_errors))
            return false;
        if($method == 'UPDATE' && !empty($where))
            $query .= ' WHERE '. $this->_getQueryString($where, 'AND');
        if(dbUms::query($query)) {
            if($method == 'INSERT')
                return dbUms::lastID();
            else
                return true;
        } else
			$this->_addError(UMS_TEST_MODE ? dbUms::getError() : __('Database error. Please contact your developer.', UMS_LANG_CODE));
        return false;
    }
    public function insert($data) {
        return $this->store($data);
    }
    public function update($data, $where) {
       /* if(is_array($where)) {
            foreach($where as $key => $val) {
                if(array_key_exists($key, $data)) {
                    unset($data[$key]);
                }
            }
        } else*/if(is_numeric($where)) {
            $where = array($this->_id => $where);
        }
        return $this->store($data, 'UPDATE', $where);
    }
    public function alias($alias = NULL) {
        if(!is_null($alias))
            $this->_alias = $alias;
        return $this->_alias;
    }
    /**
     * Delete record(s)
     * @param mixed $where condition to use in query, if numeric givven - use delete by ID column
     * @return query result
     */
    public function delete($where = '') {
        $q = 'DELETE FROM '. $this->_table;
        if($where) {
            if(is_numeric($where)) $where = array($this->_id => $where);
            $q .= ' WHERE '. $this->_getQueryString($where, 'AND');
        }
        return dbUms::query($q);
    }
    /**
     * Convert to database query
     * @param mixed $data if array given - convert it into string where key - is column name, value - database value to set;
     * if key == "additionalCondition" then we will just add value to string
     * if string givven - just return it without changes
     * @param string $delim delimiter to use in query, recommended - ',', 'AND', 'OR'
     * @return string query string
     */
    public function _getQueryString($data, $delim = ',', $validate = false) {
        $res = '';
        if(is_array($data) && !empty($data)) {
            foreach($data as $k => $v) {
                if(array_key_exists($k, $this->_fields) || $k == $this->_id) {
                    $val = $v;
                    if(isset($this->_fields[$k]) && $this->_fields[$k]->adapt['dbTo'])
                        $val = fieldAdapterUms::_($val, $this->_fields[$k]->adapt['dbTo'], fieldAdapterUms::DB);
                    if($validate) {
						if(isset($this->_fields[$k]) && is_object($this->_fields[$k])) {
                            $objForValidation = clone $this->_fields[$k];
                            $objForValidation->setValue($val);
                            if($errors = validatorUms::_($objForValidation)) {
                                $this->_addError($errors);
                            }
                        }
                    }
					if(isset($this->_fields[$k])) {
						switch($this->_fields[$k]->type) {
							case 'int':
							case 'tinyint':
								$res .= $k. ' = '. (int)$val. ' '. $delim. ' ';
								break;
							case 'float':
								$res .= $k. ' = '. (float)$val. ' '. $delim. ' ';
								break;
							case 'decimal':
								$res .= $k. ' = '. (double)$val. ' '. $delim. ' ';
								break;
							case 'free':    //Just set it as it is
								$res .= $k. ' = '. $val. ' '. $delim. ' ';
								break;
							default:
								$res .= $k. ' = \''. $val. '\' '. $delim. ' ';
								break;
						}
					} else {
						$res .= $k. ' = \''. $val. '\' '. $delim. ' ';
					}
                } elseif($k == 'additionalCondition') {    //just add some string to query
                    $res .= $v. ' '. $delim. ' ';
                }
            }
            $res = substr($res, 0, -(strlen($delim) + 1));
        } elseif(is_string($data)) {
            $res = $data;
        }
        return $res;
    }
    /**
     * Add new fieldUmsUms for children table (@see class field)
     * @param string $name name of a field
     * @param string $html html type of field (text, textarea, etc. @see html class)
     * @param string $type database type (int, varcahr, etc.)
     * @param mixed $default default value for this field
     * @return object $this - pointer to current object
     */
    protected function _addField($name, $html = 'text', $type = 'other', $default = '', $label = '', $maxlen = 0, $dbAdapt = '', $htmlAdapt = '', $description = '') {
        $this->_fields[$name] = toeCreateObjUms('fieldUms', array($name, $html, $type, $default, $label, $maxlen, $dbAdapt, $htmlAdapt, $description));
        return $this;
    }
	/**
	 * Public alias for _addField() method
	 */
	public function addField() {
		$args = func_get_args();
		return call_user_func_array(array($this, '_addField'), $args);
	}
    public function getFields() {
        return $this->_fields;
    }
    public function getField($name) {
        return $this->_fields[$name];
    }
    public function exists($value, $field = '') {
      if(!$field)
          $field = $this->_id;
      global $wpdb;
      $res = $wpdb->get_var(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_modules WHERE %1s = %s", $field, $value)
      );
      return $res;
    }
    protected function _addError($error) {
        if(is_array($error))
            $this->_errors = array_merge($this->_errors, $error);
        else
            $this->_errors[] = $error;
    }
    public function getErrors() {
        return $this->_errors;
    }
	protected function _clearErrors() {
		$this->_errors = array();
	}
    /**
     * Prepare data before send it to database
     */
    public function prepareInput($d = array()) {
        $ignore = isset($d['ignore']) ? $d['ignore'] : array();
        foreach($this->_fields as $key => $f) {
            if($f->type == 'tinyint') {
                if($d[$key] == 'true')
                    $d[$key] = 1;
                if(empty($d[$key]) && !in_array($key, $ignore)) {
                    $d[$key] = 0;
                }
            }
            if($f->type == 'date') {
                if(empty($d[$key]) && !in_array($key, $ignore)) {
                    $d[$key] = '0000-00-00';
                } elseif(!empty($d[$key])) {
                    $d[$key] = dbUms::timeToDate($d[$key]);
                }
            }
        }
        $d[$this->_id] = isset($d[$this->_id]) ? intval($d[$this->_id]) : 0;
        return $d;
    }
    /**
     * Prepare data after extracting it from database
     */
    public function prepareOutput($d = array()) {
        $ignore = isset($d['ignore']) ? $d['ignore'] : array();
        foreach($this->_fields as $key => $f) {
            switch($f->type) {
                case 'date':
                    if($d[$key] == '0000-00-00' || empty($d[$key]))
                        $d[$key] = '';
                    else {
                        $d[$key] = date(UMS_DATE_FORMAT, dbUms::dateToTime($d[$key]));
                    }
                    break;
                case 'int':
                case 'tinyint':
                    if($d[$key] == 'true')
                        $d[$key] = 1;
                    if($d[$key] == 'false')
                        $d[$key] = 0;
                    $d[$key] = (int) $d[$key];

                    break;
            }
        }
        $d[$this->_id] = isset($d[$this->_id]) ? intval($d[$this->_id]) : 0;
        return $d;
    }
    public function install($d = array()) {

    }
    public function uninstall($d = array()) {

    }
	public function activate() {

	}
    public function getLastInsertID() {

    }
    public function adaptHtml($val) {
        return htmlspecialchars($val);
    }
}
?>
