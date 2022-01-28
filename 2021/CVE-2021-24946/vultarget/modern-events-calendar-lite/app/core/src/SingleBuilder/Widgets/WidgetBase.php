<?php

namespace MEC\SingleBuilder\Widgets;

use MEC\Singleton;

class WidgetBase extends Singleton{

	public $settings;
	public $is_editor_mode;

	public function __construct(){

		$this->settings 	  = $this->get_mec_settings();
		$this->is_editor_mode = $this->is_editor_mode();
	}

	/**
	 * Is editor mode
	 *
	 * @return boolean
	 */
	public function is_editor_mode(){

		return apply_filters( 'mec_single_builder_editor_mode', false );
	}

    /**
	 * @return WP_Post
	 */
	public function get_last_event(){

		global $MEC_Last_Event;
		if(!$MEC_Last_Event){

			$EventQuery = new \MEC\Events\EventsQuery([]);
			$MEC_Last_Event = $EventQuery->get_last_event('post');
		}

		return $MEC_Last_Event;
	}

    /**
	 * Get Event ID
	 *
	 * @return int|false
	 */
    public function get_event_id(){

        $editor_mode = $this->is_editor_mode();

        $event_id = false;
		if(is_single() && 'mec-events' === get_post_type()){

			$event_id = get_the_ID();
		}elseif( $editor_mode ){

			$last_event = $this->get_last_event();
			$event_id = isset($last_event['ID']) ? $last_event['ID'] : 0;
		}

		return apply_filters( 'mec_get_event_id_for_widget', $event_id, $editor_mode );
    }

    /**
	 * @param int|WP_Post $event
	 *
	 * @return Object
	 */
	public function get_event_detail($event){

		$event = new \MEC\Events\Event($event);
		return $event->get_detail();
	}

	public function get_mec_settings(){

		return \MEC\Settings\Settings::getInstance()->get_settings();
	}
}