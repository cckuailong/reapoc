<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC DataBase class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_db extends MEC_base
{
    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
    }
    
    /**
     * Runs any query
     * @author Webnus <info@webnus.biz>
     * @param string $query
     * @param string $type
     * @return mixed
     */
	public function q($query, $type = '')
	{
		// Apply DB prefix
		$query = $this->_prefix($query);
		
		// Converts query type to lowercase
		$type = strtolower($type);
		
		// Calls select function if query type is select
		if($type == 'select') return $this->select($query);
        
		// Get WordPress DB object
		$database = $this->get_DBO();
		
        // If query type is insert, return the insert id
		if($type == 'insert')
		{
			$database->query($query);
			return $database->insert_id;
		}
		
        // Run the query and return the result
		return $database->query($query);
		
	}
    
    /**
     * Returns records count of a query
     * @author Webnus <info@webnus.biz>
     * @param string $query
     * @param string $table
     * @return int
     */
	public function num($query, $table = '')
	{
        // If table is filled, generate the query
		if(trim($table) != '') $query = "SELECT COUNT(*) FROM `#__$table`";
		
		// Apply DB prefix
		$query = $this->_prefix($query);
		
		// Get WordPress Db object
		$database = $this->get_DBO();
		return $database->get_var($query);
	}
    
    /**
     * Selects records from Database
     * @author Webnus <info@webnus.biz>
     * @param string $query
     * @param string $result
     * @return mixed
     */
	public function select($query, $result = 'loadObjectList')
	{
		// Apply DB prefix
		$query = $this->_prefix($query);
		
		// Get WordPress DB object
		$database = $this->get_DBO();
		
		if($result == 'loadObjectList') return $database->get_results($query, OBJECT_K);
		elseif($result == 'loadObject') return $database->get_row($query, OBJECT);
		elseif($result == 'loadAssocList') return $database->get_results($query, ARRAY_A);
		elseif($result == 'loadAssoc') return $database->get_row($query, ARRAY_A);
		elseif($result == 'loadResult') return $database->get_var($query);
        elseif($result == 'loadColumn') return $database->get_col($query);
		else return $database->get_results($query, OBJECT_K);
	}
    
    /**
     * Get a record from Database
     * @author Webnus <info@webnus.biz>
     * @param string|array $selects
     * @param string $table
     * @param string $field
     * @param string $value
     * @param boolean $return_object
     * @param string $condition
     * @return mixed
     */
	public function get($selects, $table, $field, $value, $return_object = true, $condition = '')
	{
		$fields = '';
		
		if(is_array($selects))
		{
			foreach($selects as $select) $fields .= '`'.$select.'`,';
			$fields = trim($fields, ' ,');
		}
		else
		{
			$fields = $selects;
		}
		
        // Generate the condition
		if(trim($condition) == '') $condition = "`$field`='$value'";
        
        // Generate the query
		$query = "SELECT $fields FROM `#__$table` WHERE $condition";
		
		// Apply DB prefix
		$query = $this->_prefix($query);
		
		// Get WordPress DB object
		$database = $this->get_DBO();
		
		if($selects != '*' and !is_array($selects)) return $database->get_var($query);
		elseif($return_object)
		{
			return $database->get_row($query);
		}
		elseif(!$return_object)
		{
			return $database->get_row($query, ARRAY_A);
		}
		else
		{
			return $database->get_row($query);
		}
	}

    public function columns($table = 'mec_dates', $column = NULL)
    {
        if(trim($table) == '') return false;

        $query = "SHOW COLUMNS FROM `#__".$table."`";
        $results = $this->q($query, "select");

        $columns = array();
        foreach($results as $key=>$result) $columns[] = $result->Field;

        if(trim($column) and in_array($column, $columns)) return true;
        elseif(trim($column)) return false;

        return $columns;
    }
	
    /**
     * Apply WordPress table prefix on queries
     * @author Webnus <info@webnus.biz>
     * @param string $query
     * @return string
     */
	public function _prefix($query)
	{
        // Get WordPress DB object
		$wpdb = $this->get_DBO();

		$charset = $wpdb->charset;
		if(!trim($charset)) $charset = 'utf8';

		$collate = $wpdb->collate;
        if(!trim($collate))
        {
            $charset = 'utf8';
            $collate = 'utf8_unicode_ci';
        }

        $query = str_replace('#__blogs', $wpdb->base_prefix.'blogs', $query);
		$query = str_replace('#__', $wpdb->prefix, $query);
		$query = str_replace('[:CHARSET:]', $charset, $query);
		$query = str_replace('[:COLLATE:]', $collate, $query);

        return $query;
	}

    public function escape($parameter)
    {
        $database = $this->get_DBO();
        global $wp_version;

        if(is_array($parameter))
        {
            $return_data = array();
            foreach($parameter as $key=>$value)
            {
                $return_data[$key] = $this->escape($value);
            }
        }
        else
        {
            if(version_compare($wp_version, '3.6', '<')) $return_data = $database->escape($parameter);
            else $return_data = esc_sql($parameter);
        }

        return $return_data;
    }

    public function prepare($query, ...$args)
    {
        // Get WordPress DB object
        $database = $this->get_DBO();

        return $database->prepare($query, $args);
    }
    
    /**
     * Returns WordPres DB Object
     * @author Webnus <info@webnus.biz>
     * @global wpdb $wpdb
     * @return wpdb
     */
	public function get_DBO()
	{
		global $wpdb;
		return $wpdb;
	}
}