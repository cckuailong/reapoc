<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Booking Calendar class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_bookingcalendar extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;

    /**
     * @var MEC_book
     */
    public $book;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC Factory
        $this->factory = $this->getFactory();

        // MEC Main
        $this->main = $this->getMain();

        // MEC Book
        $this->book = $this->getBook();
    }

    /**
     * Initialize User Events Feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        $this->factory->action('wp_ajax_mec_booking_calendar_load_month', array($this, 'load_month'));
        $this->factory->action('wp_ajax_nopriv_mec_booking_calendar_load_month', array($this, 'load_month'));
    }

    public function display_calendar($event, $uniqueid, $start = NULL)
    {
        $path = MEC::import('app.features.booking.calendar_novel', true, true);

        // Generate Month
        ob_start();
        include $path;
        return ob_get_clean();
    }

    /**
     * Load month for AJAX requert
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function load_month()
    {
        // Request
        $request = $this->getRequest();

        // Render
        $render = $this->getRender();

        $event_id = $request->getVar('event_id');
        $uniqueid = $request->getVar('uniqueid');
        $year = $request->getVar('year');
        $month = $request->getVar('month');

        // Start Date
        $start = $year.'-'.$month.'-01';
        if(strtotime($start) < current_time('timestamp')) $start = current_time('Y-m-d');

        // End Date
        $end = date('Y-m-t', strtotime($start));

        $rendered = $render->data($event_id, '');

        $data = new stdClass();
        $data->ID = $event_id;
        $data->data = $rendered;

        // Get Event Dates
        $records = $this->getDB()->select("SELECT * FROM `#__mec_dates` WHERE `post_id`='".$event_id."' AND ((`dstart` <= '".$start."' AND `dend` >= '".$end."') OR (`dstart` <= '".$start."' AND `dend` >= '".$start."' AND `dend` <= '".$end."') OR (`dstart` >= '".$start."' AND `dend` <= '".$end."') OR (`dstart` >= '".$start."' AND `dstart` <= '".$end."' AND `dend` >= '".$end."'))", 'loadAssocList');

        $dates = array();
        foreach($records as $record)
        {
            $dates[] = array(
                'start' => array(
                    'date' => $record['dstart'],
                    'hour' => date('g', $record['tstart']),
                    'minutes' => date('i', $record['tstart']),
                    'ampm' => date('A', $record['tstart']),
                    'timestamp' => $record['tstart'],
                ),
                'end' => array(
                    'date' => $record['dend'],
                    'hour' => date('g', $record['tend']),
                    'minutes' => date('i', $record['tend']),
                    'ampm' => date('A', $record['tend']),
                    'timestamp' => $record['tend'],
                ),
                'allday' => ((isset($data->data->meta) and isset($data->data->meta->mec_allday)) ? $data->data->meta->mec_allday : 0),
                'hide_time' => ((isset($data->data->meta) and isset($data->data->meta->mec_hide_time)) ? $data->data->meta->mec_hide_time : 0),
                'past' => $this->main->is_past($record['dstart'], $start),
            );
        }

        if(!count($dates))
        {
            $dates = array(
                array(
                    'fake' => true,
                    'start' => array(
                        'date' => $start
                    ),
                    'end' => array(
                        'date' => $start
                    ),
                )
            );
        }

        $data->dates = $dates;
        $data->date = isset($data->dates[0]) ? $data->dates[0] : array();

        echo json_encode(array('html' => $this->display_calendar($data, $uniqueid, $start)));
        exit;
    }
}