<?php

if(!function_exists('prsv_debug_print_r'))
{
  function prsv_debug_print_r($var)
  {
    echo "<div style=\"border: 2px solid red;display: block;padding: 12px;background-color: #feffe4;font-size: 14px;\">";
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    echo "</div>";
  }
}

if(!function_exists('prsv_debug_print_r_die'))
{
  function prsv_debug_print_r_die($var)
  {
    prsv_debug_print_r($var);
    exit(0);
  }
}

if(!function_exists('prsv_debug_dump'))
{
  function prsv_debug_dump($var)
  {
    echo "<div style=\"border: 2px solid red;display: block;padding: 12px;background-color: #feffe4;font-size: 14px;\">";
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    echo "</div>";
  }
}

if(!function_exists('prsv_debug_dump_die'))
{
  function prsv_debug_dump_die($var)
  {
    prsv_debug_dump($var);

    exit(0);
  }
}
