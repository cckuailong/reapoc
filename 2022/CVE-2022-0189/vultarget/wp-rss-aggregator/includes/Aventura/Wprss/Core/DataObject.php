<?php

namespace Aventura\Wprss\Core;

/**
 * An object with dynamic getter and setter functionality, as well as array access.
 *
 * @since 4.8.1
 */
class DataObject implements \ArrayAccess, DataObjectInterface {

	/**
	 * Object attributes
	 *
     * @since 4.8.1
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Data changes flag (true after setData|unsetData call)
     * @since 4.8.1
	 * @var $_hasDataChange bool
	 */
	protected $_hasDataChanges = false;

	/**
	 * Original data that was loaded
	 *
     * @since 4.8.1
	 * @var array
	 */
	protected $_origData;

	/**
	 * Name of object id field
	 *
     * @since 4.8.1
	 * @var string
	 */
	protected $_idFieldName = null;

	/**
	 * Setter/Getter underscore transformation cache
	 *
     * @since 4.8.1
	 * @var array
	 */
	protected static $_underscoreCache = array();

	/**
	 * Object delete flag
	 *
     * @since 4.8.1
	 * @var boolean
	 */
	protected $_isDeleted = false;

	/**
	 * Constructor
	 *
	 * By default is looking for first argument as array and assignes it as object attributes
	 * This behaviour may change in child classes
	 *
     * @since 4.8.1
	 */
	public function __construct() {
		$args = func_get_args();
		if (empty($args[0])) {
			$args[0] = array();
		}
		$this->_data = $args[0];

		$this->_construct();
	}

	/**
	 * Internal constructor not depended on params. Can be used for object initialization.
     *
     * If you want to add some procedures that happen on instantiation,
     * you probably want to override this.
     * 
     * @since 4.8.1
	 */
	protected function _construct() {}

	/**
	 * Set _isDeleted flag value (if $isDeleted param is defined) and return current flag value
	 *
     * @since 4.8.1
	 * @param boolean $isDeleted
	 * @return boolean
	 */
	public function isDeleted($isDeleted=null)
	{
		$result = $this->_isDeleted;
		if (!is_null($isDeleted)) {
			$this->_isDeleted = $isDeleted;
		}
		return $result;
	}

	/**
	 * Get data change status
	 *
     * @since 4.8.1
	 * @return bool
	 */
	public function hasDataChanges()
	{
		return $this->_hasDataChanges;
	}

	/**
	 * set name of object id field
	 *
     * @since 4.8.1
	 * @param   string $name
	 * @return  Wp_Rss_SpinnerChief_Data_Object
	 */
	public function setIdFieldName($name)
	{
		$this->_idFieldName = $name;
		return $this;
	}

	/**
	 * Retrieve name of object id field
	 *
     * @since 4.8.1
	 * @param   string $name
	 * @return  Wp_Rss_SpinnerChief_Data_Object
	 */
	public function getIdFieldName()
	{
		return $this->_idFieldName;
	}

	/**
	 * Retrieve object id
	 *
     * @since 4.8.1
	 * @return mixed
	 */
	public function getId()
	{
		if ($this->getIdFieldName()) {
			return $this->_getData($this->getIdFieldName());
		}
		return $this->_getData('id');
	}

	/**
	 * Set object id field value
	 *
     * @since 4.8.1
	 * @param   mixed $value
	 * @return  Wp_Rss_SpinnerChief_Data_Object
	 */
	public function setId($value)
	{
		if ($this->getIdFieldName()) {
			$this->setData($this->getIdFieldName(), $value);
		} else {
			$this->setData('id', $value);
		}
		return $this;
	}

	/**
	 * Add data to the object.
	 *
	 * Retains previous data in the object.
	 *
     * @since 4.8.1
	 * @param array $arr
	 * @return Wp_Rss_SpinnerChief_Data_Object
	 */
	public function addData(array $arr)
	{
		foreach($arr as $index=>$value) {
			$this->setData($index, $value);
		}
		return $this;
	}

	/**
	 * Overwrite data in the object.
	 *
	 * $key can be string or array.
	 * If $key is string, the attribute value will be overwritten by $value
	 *
	 * If $key is an array, it will overwrite all the data in the object.
	 *
     * @since 4.8.1
	 * @param string|array $key
	 * @param mixed $value
	 * @return Wp_Rss_SpinnerChief_Data_Object
	 */
	public function setData($key, $value=null)
	{
		$this->_hasDataChanges = true;
		// http://www.php.net/manual/en/function.is-array.php#48083
		if(is_array($key)) {
			$this->_data = $key;
		} else {
			$this->_data[$key] = $value;
		}
		return $this;
	}

	/**
	 * Unset data from the object.
	 *
	 * $key can be a string only. Array will be ignored.
	 *
     * @since 4.8.1
	 * @param string $key
	 * @return Wp_Rss_SpinnerChief_Data_Object
	 */
	public function unsetData($key=null)
	{
		$this->_hasDataChanges = true;
		if (is_null($key)) {
			$this->_data = array();
		} else {
			unset($this->_data[$key]);
		}
		return $this;
	}

	/**
	 * Retrieves data from the object
	 *
	 * If $key is empty will return all the data as an array
	 * Otherwise it will return value of the attribute specified by $key
	 *
	 * If $index is specified it will assume that attribute data is an array
	 * and retrieve corresponding member.
	 *
     * @since 4.8.1
	 * @param string $key
	 * @param string|int $index
	 * @return mixed
	 */
	public function getData($key='', $index=null)
	{
		if (''===$key) {
			return $this->_data;
		}

        if (is_array($key)) {
            return array_pick($this->_data, $key);
        }

		$default = null;

		// accept a/b/c as ['a']['b']['c']
		if (strpos($key,'/')) {
			$keyArr = explode('/', $key);
			$data = $this->_data;
			foreach ($keyArr as $i=>$k) {
				if ($k==='') {
					return $default;
				}
				if (is_array($data)) {
					if (!isset($data[$k])) {
						return $default;
					}
					$data = $data[$k];
				} elseif ($data instanceof Wp_Rss_SpinnerChief_Data_Object) {
					$data = $data->getData($k);
				} else {
					return $default;
				}
			}
			return $data;
		}

		// legacy functionality for $index
		if (isset($this->_data[$key])) {
			if (is_null($index)) {
				return $this->_data[$key];
			}

			$value = $this->_data[$key];
			if (is_array($value)) {
				//if (isset($value[$index]) && (!empty($value[$index]) || strlen($value[$index]) > 0)) {
				/**
				 * If we have any data, even if it empty - we should use it, anyway
				 */
				if (isset($value[$index])) {
					return $value[$index];
				}
				return null;
			} elseif (is_string($value)) {
				$arr = explode("\n", $value);
				return (isset($arr[$index]) && (!empty($arr[$index]) || strlen($arr[$index]) > 0)) ? $arr[$index] : null;
			} elseif ($value instanceof Wp_Rss_SpinnerChief_Data_Object) {
				return $value->getData($index);
			}
			return $default;
		}
		return $default;
	}

	/**
	 * Get value from _data array without parse key
	 *
     * @since 4.8.1
	 * @param   string $key
	 * @return  mixed
	 */
	protected function _getData($key)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : null;
	}

	/**
	 * Set object data with calling setter method
	 *
     * @since 4.8.1
	 * @param string $key
	 * @param mixed $args
	 * @return Wp_Rss_SpinnerChief_Data_Object
	 */
	public function setDataUsingMethod($key, $args=array())
	{
		$method = 'set'.$this->_camelize($key);
		$this->$method($args);
		return $this;
	}

	/**
	 * Get object data by key with calling getter method
	 *
     * @since 4.8.1
	 * @param string $key
	 * @param mixed $args
	 * @return mixed
	 */
	public function getDataUsingMethod($key, $args=null)
	{
		$method = 'get'.$this->_camelize($key);
		return $this->$method($args);
	}

	/**
	 * Fast get data or set default if value is not available
	 *
     * @since 4.8.1
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getDataSetDefault($key, $default)
	{
		if (!isset($this->_data[$key])) {
			$this->_data[$key] = $default;
		}
		return $this->_data[$key];
	}

	/**
	 * If $key is empty, checks whether there's any data in the object
	 * Otherwise checks if the specified attribute is set.
	 *
     * @since 4.8.1
	 * @param string $key
	 * @return boolean
	 */
	public function hasData($key='')
	{
		if (empty($key) || !is_string($key)) {
			return !empty($this->_data);
		}
		return array_key_exists($key, $this->_data);
	}

	/**
	 * Convert object attributes to array
	 *
     * @since 4.8.1
	 * @param  array $arrAttributes array of required attributes
	 * @return array
	 */
	public function __toArray(array $arrAttributes = array())
	{
		if (empty($arrAttributes)) {
			return $this->_data;
		}

		$arrRes = array();
		foreach ($arrAttributes as $attribute) {
			if (isset($this->_data[$attribute])) {
				$arrRes[$attribute] = $this->_data[$attribute];
			}
			else {
				$arrRes[$attribute] = null;
			}
		}
		return $arrRes;
	}

	/**
	 * Public wrapper for __toArray
	 *
     * @since 4.8.1
	 * @param array $arrAttributes
	 * @return array
	 */
	public function toArray(array $arrAttributes = array())
	{
		return $this->__toArray($arrAttributes);
	}

	/**
	 * Set required array elements
	 *
     * @since 4.8.1
	 * @param   array $arr
	 * @param   array $elements
	 * @return  array
	 */
	protected function _prepareArray(&$arr, array $elements=array())
	{
		foreach ($elements as $element) {
			if (!isset($arr[$element])) {
				$arr[$element] = null;
			}
		}
		return $arr;
	}

	/**
	 * Convert object attributes to XML
	 *
     * @since 4.8.1
	 * @param  array $arrAttributes array of required attributes
	 * @param string $rootName name of the root element
	 * @return string
	 */
	protected function __toXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag=false, $addCdata=true)
	{
		$xml = '';
		if ($addOpenTag) {
			$xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		}
		if (!empty($rootName)) {
			$xml.= '<'.$rootName.'>'."\n";
		}
		$xmlModel = new Varien_Simplexml_Element('<node></node>');
		$arrData = $this->toArray($arrAttributes);
		foreach ($arrData as $fieldName => $fieldValue) {
			if ($addCdata === true) {
				$fieldValue = "<![CDATA[$fieldValue]]>";
			} else {
				$fieldValue = $xmlModel->xmlentities($fieldValue);
			}
			$xml.= "<$fieldName>$fieldValue</$fieldName>"."\n";
		}
		if (!empty($rootName)) {
			$xml.= '</'.$rootName.'>'."\n";
		}
		return $xml;
	}

	/**
	 * Public wrapper for __toXml
	 *
     * @since 4.8.1
	 * @param array $arrAttributes
	 * @param string $rootName
	 * @return string
	 */
	public function toXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag=false, $addCdata=true)
	{
		return $this->__toXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
	}

	/**
	 * Convert object attributes to JSON
	 *
     * @since 4.8.1
	 * @param  array $arrAttributes array of required attributes
	 * @return string
	 */
	protected function __toJson(array $arrAttributes = array())
	{
		$arrData = $this->toArray($arrAttributes);
		$json = Zend_Json::encode($arrData);
		return $json;
	}

	/**
	 * Public wrapper for __toJson
	 *
     * @since 4.8.1
	 * @param array $arrAttributes
	 * @return string
	 */
	public function toJson(array $arrAttributes = array())
	{
		return $this->__toJson($arrAttributes);
	}

	/**
	 * Convert object attributes to string
	 *
     * @since 4.8.1
	 * @param  array  $arrAttributes array of required attributes
	 * @param  string $valueSeparator
	 * @return string
	 */
//    public function __toString(array $arrAttributes = array(), $valueSeparator=',')
//    {
//        $arrData = $this->toArray($arrAttributes);
//        return implode($valueSeparator, $arrData);
//    }

	/**
	 * Public wrapper for __toString
	 *
	 * Will use $format as an template and substitute {{key}} for attributes
	 *
     * @since 4.8.1
	 * @param string $format
	 * @return string
	 */
	public function toString($format='')
	{
		if (empty($format)) {
			$str = implode(', ', $this->getData());
		} else {
			preg_match_all('/\{\{([a-z0-9_]+)\}\}/is', $format, $matches);
			foreach ($matches[1] as $var) {
				$format = str_replace('{{'.$var.'}}', $this->getData($var), $format);
			}
			$str = $format;
		}
		return $str;
	}

	/**
	 * Set/Get attribute wrapper
	 *
     * @since 4.8.1
	 * @param   string $method
	 * @param   array $args
	 * @return  mixed
	 */
	public function __call( $method, $args ) {
		switch (substr($method, 0, 3)) {
			case 'get' :
				$key = $this->_underscore(substr($method,3));
				$data = $this->getData($key, isset($args[0]) ? $args[0] : null);
				return $data;

			case 'set' :
				$key = $this->_underscore(substr($method,3));
				$result = $this->setData($key, isset($args[0]) ? $args[0] : null);
				return $result;

			case 'uns' :
				$key = $this->_underscore(substr($method,3));
				$result = $this->unsetData($key);
				return $result;

			case 'has' :
				$key = $this->_underscore(substr($method,3));
				return isset($this->_data[$key]);
		}
		throw new Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
	}

	/**
	 * Attribute getter (deprecated)
	 *
     * @since 4.8.1
	 * @param string $var
	 * @return mixed
	 */

	public function __get($var)
	{
		$var = $this->_underscore($var);
		return $this->getData($var);
	}

	/**
	 * Attribute setter (deprecated)
	 *
     * @since 4.8.1
	 * @param string $var
	 * @param mixed $value
	 */
	public function __set($var, $value)
	{
		$var = $this->_underscore($var);
		$this->setData($var, $value);
	}

	/**
	 * checks whether the object is empty
	 *
     * @since 4.8.1
	 * @return boolean
	 */
	public function isEmpty()
	{
		if (empty($this->_data)) {
			return true;
		}
		return false;
	}

	/**
	 * Converts field names for setters and geters
	 *
	 * $this->setMyField($value) === $this->setData('my_field', $value)
	 * Uses cache to eliminate unneccessary preg_replace
	 *
     * @since 4.8.1
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name)
	{
		if (isset(self::$_underscoreCache[$name])) {
			return self::$_underscoreCache[$name];
		}
		#Varien_Profiler::start('underscore');
		$result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
		#Varien_Profiler::stop('underscore');
		self::$_underscoreCache[$name] = $result;
		return $result;
	}

	protected function _camelize($name)
	{
		return uc_words($name, '');
	}

	/**
	 * serialize object attributes
	 *
     * @since 4.8.1
	 * @param   array $attributes
	 * @param   string $valueSeparator
	 * @param   string $fieldSeparator
	 * @param   string $quote
	 * @return  string
	 */
	public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"')
	{
		$res  = '';
		$data = array();
		if (empty($attributes)) {
			$attributes = array_keys($this->_data);
		}

		foreach ($this->_data as $key => $value) {
			if (in_array($key, $attributes)) {
				$data[] = $key . $valueSeparator . $quote . $value . $quote;
			}
		}
		$res = implode($fieldSeparator, $data);
		return $res;
	}

	/**
	 * Get object loaded data (original data)
	 *
     * @since 4.8.1
	 * @param string $key
	 * @return mixed
	 */
	public function getOrigData($key=null)
	{
		if (is_null($key)) {
			return $this->_origData;
		}
		return isset($this->_origData[$key]) ? $this->_origData[$key] : null;
	}

	/**
	 * Initialize object original data
	 *
     * @since 4.8.1
	 * @param string $key
	 * @param mixed $data
	 * @return Wp_Rss_SpinnerChief_Data_Object
	 */
	public function setOrigData($key=null, $data=null)
	{
		if (is_null($key)) {
			$this->_origData = $this->_data;
		} else {
			$this->_origData[$key] = $data;
		}
		return $this;
	}

	/**
	 * Compare object data with original data
	 *
     * @since 4.8.1
	 * @param string $field
	 * @return boolean
	 */
	public function dataHasChangedFor($field)
	{
		$newData = $this->getData($field);
		$origData = $this->getOrigData($field);
		return $newData!=$origData;
	}

	/**
	 * Clears data changes status
	 *
     * @since 4.8.1
	 * @param boolean $value
	 * @return Wp_Rss_SpinnerChief_Data_Object
	 */
	public function setDataChanges($value)
	{
		$this->_hasDataChanges = (bool)$value;
		return $this;
	}

	/**
	 * Present object data as string in debug mode
	 *
     * @since 4.8.1
	 * @param mixed $data
	 * @param array $objects
	 * @return string
	 */
	public function debugSelf($data=null, &$objects=array())
	{
		if (is_null($data)) {
			$hash = spl_object_hash($this);
			if (!empty($objects[$hash])) {
				return '*** RECURSION ***';
			}
			$objects[$hash] = true;
			$data = $this->getData();
		}
		$debug = array();
		foreach ($data as $key=>$value) {
			if (is_scalar($value)) {
				$debug[$key] = $value;
			} elseif (is_array($value)) {
				$debug[$key] = $this->debug($value, $objects);
			} elseif ($value instanceof Wp_Rss_SpinnerChief_Data_Object) {
				$debug[$key.' ('.get_class($value).')'] = $value->debug(null, $objects);
			}
		}
		return $debug;
	}

	/**
	 * Implementation of ArrayAccess::offsetSet()
	 *
     * @since 4.8.1
	 * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}

	/**
	 * Implementation of ArrayAccess::offsetExists()
	 *
     * @since 4.8.1
	 * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}

	/**
	 * Implementation of ArrayAccess::offsetUnset()
	 *
     * @since 4.8.1
	 * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}

	/**
	 * Implementation of ArrayAccess::offsetGet()
	 *
     * @since 4.8.1
	 * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}


	/**
	 * Enter description here...
	 *
     * @since 4.8.1
	 * @param string $field
	 * @return boolean
	 */
	public function isDirty($field=null)
	{
		if (empty($this->_dirty)) {
			return false;
		}
		if (is_null($field)) {
			return true;
		}
		return isset($this->_dirty[$field]);
	}

	/**
	 * Enter description here...
	 *
         * @since 4.8.1
	 * @param string $field
	 * @param boolean $flag
	 * @return Wp_Rss_SpinnerChief_Data_Object
	 */
	public function flagDirty($field, $flag=true)
	{
		if (is_null($field)) {
			foreach ($this->getData() as $field=>$value) {
				$this->flagDirty($field, $flag);
			}
		} else {
			if ($flag) {
				$this->_dirty[$field] = true;
			} else {
				unset($this->_dirty[$field]);
			}
		}
		return $this;
	}

        /**
         * Gets data for keys that are specified.
         *
         * The second parameter inverts the check.
         *
         * @since 4.9
         * @param string|array $keys A key or an array of keys to match.
         * @param bool $exclude If true, returns elements which are not
         *  specified.
         * @return array An array with elements that match the
         *  specified keys.
         */
        public function getDataForKeys($keys, $exclude = false)
        {
            $keys = array_flip((array) $keys);
            return $exclude
                    ? array_diff_key($this->_data, $keys)
                    : array_intersect_key($this->_data, $keys);
        }
}


/**
 * Tiny function to enhance functionality of ucwords
 *
 * Will capitalize first letters and convert separators if needed
 *
 * @since 4.8.1
 * @param string $str
 * @param string $destSep
 * @param string $srcSep
 * @return string
 */
function uc_words($str, $destSep='_', $srcSep='_') {
	return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
}
