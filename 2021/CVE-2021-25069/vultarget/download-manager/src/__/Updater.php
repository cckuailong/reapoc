<?php


namespace WPDM\__;


class Updater
{
    function __construct()
    {

    }

    function getLatestVersions()
    {
        $latest = get_option('wpdm_latest', []);
        $latest = (array)$latest;
        return $latest;
    }


}
