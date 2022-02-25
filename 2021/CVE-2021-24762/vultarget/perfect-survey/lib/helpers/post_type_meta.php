<?php

if(!function_exists('prsv_post_meta_get_all'))
{
  /**
  * Get all post post_meta
  *
  * @global PerfectSurveyConfigs $ps_configs
  *
  * @param int $ID  the post ID, default null, current post id
  *
  * @return array
  */
  function prsv_post_meta_get_all($ID = null)
  {
    global $ps;/*@var $ps PerfectSurvey*/
    return $ps->post_type_meta->get_all($ID);
  }
}


if(!function_exists('prsv_post_meta_get'))
{
  /**
  * Get single post meta
  *
  * @global PerfectSurveyConfigs $ps_configs
  *
  * @param  string    $name      meta name
  * @param  mixed     $default   default value, default FALSE
  * @param  int       $ID        the post ID, default null, current post id
  *
  * @return mixed
  */
  function prsv_post_meta_get($name,  $default = false, $ID = null)
  {
    global $ps;/*@var $ps PerfectSurvey*/

    return $ps->post_type_meta->get($name, $default, $ID);
  }
}
