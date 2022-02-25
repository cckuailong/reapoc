<?php


if(!function_exists('prsv_get'))
{
  /**
  * Return perfect survey instance or property
  *
  * @global type $ps
  *
  * @param string $property property | null (ps object)
  *
  * @return mixed
  */
  function prsv_get($property = null)
  {
    global $ps;

    return $property ? $ps->{$property} : $ps;
  }
}

if(!function_exists('prsv_get_default_editor'))
{
  function prsv_get_default_editor()
  {
    return PRSV_DEFAULT_EDITOR ;
  }
}

if(!function_exists('prsv_get_post_type_model'))
{
  /**
  * Post type model
  *
  * @global PerfectSurvey $ps
  *
  * @return PerfectSurveyPostTypeModel
  */
  function prsv_get_post_type_model()
  {
    return prsv_get('post_type_model');
  }

}

if(!function_exists('prsv_global_options_get'))
{
  /*
  * Return ps option by key
  *
  */
  function prsv_global_options_get($ps_parameter)
  {
    $ps_options = get_option(PRSV_GLOBAL_OPTION);
    if(isset($ps_options['ps_global_options']))
    {
      foreach ($ps_options['ps_global_options'] as $k => $a)
      {
        if($k == $ps_parameter) {
          return $a;
        }
      }
    }
  }
}

if(!function_exists('prsv_get_resources'))
{
  /**
  * Perfect Survey views
  *
  * @global PerfectSurvey $ps
  *
  * @return PerfectSurveyResources
  */
  function prsv_get_resources()
  {
    return prsv_get('resources');
  }
}

if(!function_exists('prsv_resource_render_backend'))
{
  /**
  * render backend page
  *
  * @param string $page  page path
  * @param array  $data  data
  *
  * @return string
  */
  function prsv_resource_render_backend($page, array $data = array())
  {
    return prsv_get_resources()->render_backend($page, $data);
  }
}

if(!function_exists('prsv_resource_render_frontend'))
{
  /**
  * render frontend page
  *
  * @param string $page  page path
  * @param array  $data  data
  *
  * @return string
  */
  function prsv_resource_render_frontend($page, array $data = array())
  {
    return prsv_get_resources()->render_frontend($page, $data);
  }
}


if(!function_exists('prsv_resource_include_backend'))
{
  /**
  * include backend page
  *
  * @param string $page  page path
  * @param array  $data  data
  *
  * @return boolean
  */
  function prsv_resource_include_backend($page, array $data = array())
  {
    return prsv_get_resources()->include_backend($page, $data);
  }
}

if(!function_exists('prsv_resource_include_frontend'))
{
  /**
  * include frontend page
  *
  * @param string $page  page path
  * @param array  $data  data
  *
  * @return boolean
  */
  function prsv_resource_include_frontend($page, array $data = array())
  {
    return prsv_get_resources()->include_frontend($page, $data);
  }
}

if(!function_exists('prsv_get_row_css'))
{
  /*
  * Return css rules with parameter option
  *
  * @param $array
  * @return array()
  */
  function prsv_get_row_css($class, $array)
  {
    echo $class.'{';
      foreach ($array as $rules => $r)
      {
        echo $rules.':'.prsv_global_options_get($r[0]).$r[1].';';
      }
      echo '}';
    }
  }

  if(!function_exists('prsv_empty_str'))
  {
    /**
    * Check for empty string allowing for a value of `0`
    *
    * @param $str string
    *
    * @return bool
    */
    function prsv_empty_str($str)
    {
      return !isset($str) || $str === "";
    }
  }

  if(!function_exists('prsv_plugin_url'))
  {
    /**
    * get plugin resource url
    *
    * @param string $url url
    *
    * @return string
    */
    function prsv_plugin_url($url)
    {
      return plugins_url(PRSV_NAMING . '/' . $url);
    }
  }

  if(!function_exists('prsv_register_css'))
  {
    /**
    * Register css
    *
    * @param string $name  css file name
    * @param string $url   css file url
    */
    function prsv_register_css($name, $url = null)
    {
      wp_register_style($name, prsv_plugin_url($url));
      return wp_enqueue_style($name);
    }
  }

  if(!function_exists('prsv_register_js'))
  {
    /**
    * Register js
    *
    * @param string $name      js file name
    * @param string $url       js file url
    * @param mixed  $relavite  if path is relative
    * @param array  $deps      an array of registered script handles this script depends on.
    * @param mixed  $ver       string specifying script
    *
    * @return mixed
    */
    function prsv_register_js($name, $url = null, $relative = true, $deps = array(), $version = false, $footer = false)
    {
      $url = $relative ? prsv_plugin_url($url) : $url;

      return wp_enqueue_script($name, $url, array(), $version, $footer);
    }
  }

  if(!function_exists('prsv_get_ip'))
  {
    /**
    * Return remote address
    *
    * @return string
    */
    function prsv_get_ip($anonymize = false)
    {
      $ipAddress = $_SERVER['REMOTE_ADDR'];
      
      if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
      {
        $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
      }

      if($anonymize && function_exists('wp_privacy_anonymize_ip'))
      {
        $ipAddress = wp_privacy_anonymize_ip($ipAddress);
      }

      return $ipAddress;
    }
  }

  if(!function_exists('prsv_set_cookie_id'))
  {
     /**
      * Set survery cookie id value
      *
      * @param string $value value, default NULL (PerfectSurveyPostTypeModel::current_cookie_id)
      *
      * @return string
      */
      function prsv_set_cookie_id($value = null)
      {
          $name  = prsv_get('post_type')->current_cookie_name;
          $value = $value ? $value : prsv_get('post_type')->current_cookie_id;
          $exp   = prsv_get('post_type')->current_cookie_exp;          
          setcookie($name,$value,$exp, COOKIEPATH, COOKIE_DOMAIN) ;
          return $value;
      }
  }

  if(!function_exists('prsv_get_cookie_id'))
  {
     /**
      * Get survery cookie id value
      * @return string
      */
      function prsv_get_cookie_id()
      {
        return prsv_get('post_type')->current_cookie_name ? prsv_input_cookie(prsv_get('post_type')->current_cookie_name) : ''; 
      }
  }

  if(!function_exists('prsv_colors_palette'))
  {
    /**
    * Return colors palette
    *
    * @return array
    */
    function prsv_colors_palette() {
      return '#1862ab';
    }
  }

  if(!function_exists('psrv_return_dankon')) {
    function psrv_return_dankon(string $value) {

      return PRSV_URL_WEBSITE.$value;

    }
  }


  if(!function_exists('prsv_dankon_amiko')) {
    function prsv_dankon_amiko() {

      $class = "survey_";

      return '<a class="'.$class.'psrv_copy" target="_blank" href="https://'.psrv_return_dankon('.com').'">'.PRSV_URL_EDITOR_DANKO.' with '.PRSV_PLUGIN_NAME.'</a>';

    }
  }

  if(!function_exists('prsv_return_graph'))
  {
    /**
    * Return graph array data
    *
    * @return array
    */
    function prsv_return_graph($values = array(), $answers = array()) {
      
      $sum = array_sum($values);
      $res = $sum != 0 ? 100 / $sum : 0;

      $graph = [];

      $c = 0;
      foreach($values as $key => $v) {
        $width = $v * $res;
        if($width != 0) {
          $graph[$key] = [$v, $answers[$key], $width];
          $c++;
        }

      }

      return $graph;

    }
  }

  if(!function_exists('prsv_is_answer_my'))
  {
    function prsv_is_answer_my(array $question, array $answer_value, array $question_data, $deb = false)
    {
      if(!$question_data){
        return false;
      }

      foreach($question_data as $q_data)
      {
         if(!empty($answer_value['answer_id']) && !empty($answer_value['answer_value_id']) && $answer_value['answer_id'] == $q_data['answer_id'] && $answer_value['answer_value_id'] == $q_data['answer_value_id'])
         {
           return true;
         }
         
         if(!empty($answer_value['answer_id']) && empty($answer_value['answer_value_id']) && empty($answer_value['value']) && $answer_value['answer_id'] == $q_data['answer_id'])
         {
           return true;
         }

         if(!empty($answer_value['value']) && $answer_value['value'] == $q_data['value'])
         {
           return true;
         }
      }

      return false;
    }
  }
