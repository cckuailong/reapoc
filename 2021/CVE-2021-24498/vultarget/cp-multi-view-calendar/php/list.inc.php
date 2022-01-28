<?php
//$dc_subjects = array("title 1","title 2","title 3","title 4");
//$dc_locations = array("location 1","location 2","location 3","location 4");

// NOTE: See instructions at https://wordpress.dwbooster.com/demos/multi-view/18-lists-for-location-and-title.html

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


global $arrayJS_list;
$arrayJS_list = 'var dc_subjects = ';
if (isset($dc_subjects) && is_array($dc_subjects))
{
    $arrayJS_list .= ' new Array (';
    for ($i=0;$i<count($dc_subjects);$i++)
    {
        if ($i!=0)
            $arrayJS_list .= ', ';
        $arrayJS_list .= '"'.$dc_subjects[$i].'"';
    }
    $arrayJS_list .= ');';
}
else
    $arrayJS_list .= '"";';

$arrayJS_list .= 'var dc_locations = ';
if (isset($dc_locations) && is_array($dc_locations))
{
    $arrayJS_list .= ' new Array (';
    for ($i=0;$i<count($dc_locations);$i++)
    {
        if ($i!=0)
            $arrayJS_list .= ', ';
        $arrayJS_list .= '"'.$dc_locations[$i].'"';
    }
    $arrayJS_list .= ');';
}
else
    $arrayJS_list .= '"";';




?>