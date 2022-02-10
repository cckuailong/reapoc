<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC syncSchedule class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_syncSchedule extends MEC_base
{
    private $main;

    public function __construct()
    {
        $this->main = $this->getMain();
    }

    public function sync()
    {
        $ix = $this->main->get_ix_options();
        $notifications = $this->main->get_notifications();

        // To run crons by force
        $internal_cron_system = true;

        if(isset($ix['sync_g_import_auto']) and $ix['sync_g_import_auto'] == '1')
        {
            $sync_g_import = MEC_ABSPATH.'app'.DS.'crons'.DS.'g-import.php';
            include_once $sync_g_import;
        }

        if(isset($ix['sync_g_export_auto']) and $ix['sync_g_export_auto'] == '1')
        {
            $sync_g_export = MEC_ABSPATH.'app'.DS.'crons'.DS.'g-export.php';
            include_once $sync_g_export;
        }

        if(isset($ix['sync_meetup_import_auto']) and $ix['sync_meetup_import_auto'] == '1')
        {
            $sync_meetup_import = MEC_ABSPATH.'app'.DS.'crons'.DS.'meetup-import.php';
            include_once $sync_meetup_import;
        }
    }
}