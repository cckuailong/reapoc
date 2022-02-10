<?php
namespace WPDM\MediaLibrary;

class MediaHandler
{

    function __construct()
    {

        add_filter('media_upload_tabs', array($this, 'mediaTab'));
        add_filter( 'media_upload_wpdmpromedia', array($this, 'mediaUpload') );

    }

    function mediaTab($tabs)
    {
        $newtab = array('wpdmpromedia' => __('Download Manager', 'wppmpro'));
        return array_merge($tabs, $newtab);
    }


    function mediaUpload()
    {
        $errors = array();
        if (!empty($_POST)) {
            $return = media_upload_form_handler();

            if (is_string($return))
                return $return;
            if (is_array($return))
                $errors = $return;
        }

        wp_iframe( array($this, 'mediaForm'), $errors);
    }

    function mediaForm($errors){
        //include wpdm_tpl_path('media-tab.php');
        echo "Coming Soon...";
    }


}
