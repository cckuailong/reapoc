<?php


namespace WPDM\Package;


class FileList
{


    /**
     * @usage Callback function for [file_list] tag
     * @param $file
     * @param bool|false $play_only
     * @return string
     */
    public static function table($file, $play_only = false)
    {

        return "";

    }


    /**
     * @usage Callback function for [file_list_extended] tag
     * @param $file
     * @return string
     * @usage Generate file list with preview
     */
    public static function box($file, $w = 88, $h = 88, $cols = 3)
    {

         return "";

    }

    /**
     * @usage Callback function for [file_list] tag
     * @param $file
     * @param bool|false $play_only
     * @return string
     */
    public static function premium($file, $play_only = false)
    {

        return "";

    }

    /**
     * @usage Callback function for [image_gallery_WxHxC] tag
     * @param $file
     * @return string
     * @usage Generate file list with preview
     */
    public static function imageGallery($file, $w = 400, $h = 400, $cols = 3)
    {

         return "";

    }


}
