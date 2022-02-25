<?php if(!defined('ABSPATH')) exit; // Exit if accessed directlys

/**
* Description of PerfectSurveyDB
*
* @author andrea.namici
*/
class PerfectSurveyDB extends PerfectSurveyCore
{

  /**
  * Wordpress query manager
  *
  * @var WP_Query
  */
  public $wpdb;

  public function __construct()
  {
    parent::__construct();

    global $wpdb;/*@var $wpdb WP_Query*/
    $this->wpdb = $wpdb;
  }


  public function get_table_name($table)
  {
    return $this->wpdb->prefix . $table;
  }

  /**
  * Return plugin db information
  *
  * @return array
  */
  public function get_plugin_info()
  {
    $this->wpdb->suppress_errors = TRUE;
    $plugin_info = $this->wpdb->get_row('SELECT * FROM ' . $this->get_table_name('ps'), ARRAY_A);
    $this->wpdb->suppress_errors = FALSE;

    return $plugin_info;
  }

  /**
  * Filter data by sql table column's name
  *
  * @param string    $table         sql table
  * @param array     $data          column's value to store/update
  * @param boolean   $stripsplashes call wp function "stripslashes_deep" on data to bypass wp magic quote
  *
  * @return array
  */
  public function filter_data_by_table($table, array $data, $stripsplashes = TRUE)
  {
    $table_columns = $this->wpdb->get_col("DESC {$table}", 0);

    foreach($data as $field => $value)
    {
      if(!in_array($field, $table_columns))
      {
        unset($data[$field]);
      }
    }

    if($stripsplashes && function_exists('stripslashes_deep'))
    {
      $data = stripslashes_deep($data);
    }

    return $data;
  }

  /**
  * Execlute SQL file by name in configs path
  *
  * @param string $filename      filename without extension
  * @param array  $replacements  key => values substitutions for sql file like {{key}} => 'value'
  *
  * @return boolean
  */
  public function execute_sql_file($filename,array $replacements = array())
  {
    $sql_file_content = file_get_contents(PRSV_BASE_PATH_CONFIGS.'/' .$filename.'.sql');
    $sql_file_content = str_replace(array_merge($replacements, array('{{table_prefix}}','{{version}}', '{{post_type}}')),array($this->wpdb->prefix,PRSV_PLUGIN_VERSION, PRSV_POST_TYPE),$sql_file_content);
    
    if(!empty($sql_file_content))
    {
      $queries = explode(';$$$',$sql_file_content);

      foreach($queries as $query)
      {
        if(!empty($query) && strlen(trim($query)) > 0)
        {
          $this->wpdb->query($query);
        }
      }

      return true;
    }

    return false;
  }
}
