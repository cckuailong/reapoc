<?php if(!defined('ABSPATH')) exit;

/**
* Core class of Perfect Survey Plugin
*/
abstract class PerfectSurveyCore
{

  public static $autoload_registered = false;

  public function __construct()
  {
    spl_autoload_register(array($this,'__autoload'));
    static::$autoload_registered = true;
  }

  /**
  * Register autoload
  *
  * @param string $className class to load
  *
  * @return $this
  */
  protected function __autoload($className)
  {
    $classPath = PRSV_BASE_PATH_CLASSES . '/' . $className. '.php';

    if(file_exists($classPath))
    {
      require_once $classPath;
    }

    return $this;
  }


  protected  function wp_init()
  {
    return $this;
  }


  /**
  * Register wp actions whith object publics method's
  *
  * @param String $wp_action wp action prefix
  *
  * @return PerfectSurveyCore
  */
  protected function wp_actions_register($wp_action)
  {
    $reflectionObject = new ReflectionClass(get_called_class());
    $publicMethods    = $reflectionObject->getMethods(ReflectionMethod::IS_PUBLIC);

    if(!empty($publicMethods))
    {
      foreach($publicMethods as $publicMethod)/*@var $publicMethod ReflectionMethod*/
      {
        $wp_action_name = $wp_action.'_'.$publicMethod->getName();
        add_action($wp_action_name, array($this, $publicMethod->getName()));
      }
    }

    return $this;
  }

  protected function nounce_check()
  {        
      
//    if(!PRSV_NOUNCE_FIELD_NAME)
//    {
//      return true;
//    }
//
//    if(prsv_input_post(PRSV_NOUNCE_FIELD_NAME))
//    {
//      return check_admin_referer(PRSV_NOUNCE_FIELD_NAME, PRSV_NOUNCE_FIELD_VALUE);
//    }

    return true;
  }


  protected function check_save_post_type($post_id)
  {
    $post        = get_post($post_id);

    if(!$post)
    {
      return false;
    }

    $is_revision = wp_is_post_revision($post_id);

    if ($post->post_type != PRSV_POST_TYPE || $is_revision)
    {
      return false;
    }

    return $this->nounce_check();
  }
}
