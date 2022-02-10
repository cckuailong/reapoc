<?php
namespace CustomFacebookFeed;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CFF_Parse{
	public static function get_link( $header_data ) {
		$link = isset( $header_data->link) ? $header_data->link : "https://facebook.com";
		return $link;
	}

	public static function get_cover_source( $header_data ) {
		$url = isset( $header_data->cover->source ) ? $header_data->cover->source : '';
		return $url;
	}

	public static function get_avatar( $header_data ) {
		$avatar = isset( $header_data->picture->data->url ) ? $header_data->picture->data->url : '';
		return $avatar;
	}

	public static function get_name( $header_data ) {
		$name = isset( $header_data->name ) ? $header_data->name : '';
		return $name;
	}

	public static function get_bio( $header_data ) {
		$about = isset( $header_data->about ) ? $header_data->about : '';
		return $about;
	}

	public static function get_likes( $header_data ) {
		$likes = isset( $header_data->fan_count ) ? $header_data->fan_count : '';
		return $likes;
	}

	public static function get_post_id( $post ) {
		if ( isset( $post->id ) ) {
			return $post->id;
		} elseif ( ! is_object( $post ) && isset( $post['id'] ) ) {
			return $post['id'];
		}
		return '';
	}

	public static function get_timestamp( $post ) {
		if ( isset( $post->start_time ) ) {
			return strtotime( $post->start_time );
		} elseif ( ! is_object( $post ) && isset( $post['start_time'] ) ) {
			return strtotime( $post['start_time'] );
		} elseif ( isset( $post->created_time ) ) {
			return strtotime( $post->created_time );
		} elseif ( ! is_object( $post ) && isset( $post['created_time'] ) ) {
			return strtotime( $post['created_time'] );
		}
		return '';
	}
	public static function get_message( $post ) {

		if ( isset( $post->message ) ) {
			return $post->message;
		} elseif ( ! is_object( $post ) && isset( $post['message'] ) ) {
			return $post['message'];
		} elseif ( isset( $post->description ) ) {
			return $post->description;
		} elseif ( ! is_object( $post ) && isset( $post['description'] ) ) {
			return $post['description'];
		}
		return '';
	}

	public static function get_status_type( $post ) {

		if ( isset( $post->status_type ) ) {
			return $post->status_type;
		} elseif ( ! is_object( $post ) && isset( $post['status_type'] ) ) {
			return $post['status_type'];
		} elseif ( isset( $post->start_time )
		           || (! is_object( $post ) && isset( $post['start_time'] )) ) {
			return 'event';
		} elseif ( isset( $post->images )
		           || (! is_object( $post ) && isset( $post['images'] ))) {
			return 'photo';
		} elseif ( isset( $post->format )
		           || (! is_object( $post ) && isset( $post['format'] ))) {
			return 'video';
		} elseif ( isset( $post->cover_photo )
		           || (! is_object( $post ) && isset( $post['cover_photo'] ))) {
			return 'album';
		}
		return '';
	}

	public static function get_event_name( $post ) {

		if ( isset( $post->name ) ) {
			return $post->name;
		} elseif ( ! is_object( $post ) && isset( $post['name'] ) ) {
			return $post['name'];
		}
		return '';
	}



	public static function get_permalink( $post ) {
		if ( isset( $post->start_time ) ) {
			return 'https://www.facebook.com/events/' . $post->id;
		} elseif ( ! is_object( $post ) && isset( $post['start_time'] ) ) {
			return 'https://www.facebook.com/events/' . $post['id'];
		} elseif ( isset( $post->id ) ) {
			return 'https://www.facebook.com/' . $post->id;
		} elseif ( ! is_object( $post ) && isset( $post['id'] ) ) {
			return 'https://www.facebook.com/' . $post['id'];
		} elseif ( isset( $post->link ) ) {
			return $post->link;
		} elseif ( ! is_object( $post ) && isset( $post['link'] ) ) {
			return $post['link'];
		}
		return 'https://www.facebook.com/';
	}

	public static function get_from_link( $post ) {
		if ( isset( $post->from->link ) ) {
			return $post->from->link;
		} elseif ( ! is_object( $post ) && isset( $post['from']['link'] ) ) {
			return $post['from']['link'];
		} elseif ( isset( $post->owner->link ) ) {
			return $post->owner->link;
		} elseif ( ! is_object( $post ) && isset( $post['owner']['link'] ) ) {
			return $post['owner']['link'];
		}
		return 'https://www.facebook.com/';
	}

	public static function get_item_title( $data ) {
		$title = '';
		if ( isset( $data->name ) ) {
			$title = $data->name;
		} elseif ( ! is_object( $data ) && isset( $data['name'] ) ) {
			$title = $data['name'];
		}
		return $title;
	}

	public static function get_attachments( $post ) {

		if ( isset( $post->attachments ) ) {
			return $post->attachments->data;
		} elseif ( ! is_object( $post ) && isset( $post['attachments'] ) ) {
			return $post['attachments']['data'];
		}
		return '';
	}
	public static function get_sub_attachments( $post ) {

		if ( isset( $post->attachments ) && isset( $post->attachments->data[0]->subattachments ) ) {
			return $post->attachments->data[0]->subattachments->data ;
		} elseif ( ! is_object( $post ) && isset( $post['attachments']['data'][0]['subattachments'] ) ) {
			return $post['attachments']['data'][0]['subattachments']['data'];
		} elseif ( isset( $post->subattachments ) ) {
			return $post->subattachments->data ;
		} elseif ( ! is_object( $post ) && isset( $post['subattachments'] ) ) {
			return $post['subattachments']['data'];
		}else {
			return array();
		}
	}

	public static function get_sub_attachment_type( $sub_attachment ) {

		if ( isset( $sub_attachment->type ) ) {
			return $sub_attachment->type;
		} elseif ( ! is_object( $sub_attachment ) && isset( $sub_attachment['type'] ) ) {
			return $sub_attachment['type'];
		}
		return '';
	}


	public static function get_attachment_title( $attachment ) {

		if ( isset( $attachment->title ) ) {
			return $attachment->title;
		} elseif ( ! is_object( $attachment ) && isset( $attachment['title'] ) ) {
			return $attachment['title'];
		}
		return '';
	}

	public static function get_attachment_description( $attachment ) {

		if ( isset( $attachment->description ) ) {
			return $attachment->description;
		} elseif ( ! is_object( $attachment ) && isset( $attachment['description'] ) ) {
			return $attachment['description'];
		}
		return '';
	}

	public static function get_attachment_unshimmed_url( $attachment ) {

		if ( isset( $attachment->unshimmed_url ) ) {
			return $attachment->unshimmed_url;
		} elseif ( ! is_object( $attachment ) && isset( $attachment['unshimmed_url'] ) ) {
			return $attachment['unshimmed_url'];
		}
		return '';
	}

	public static function get_event_start_time( $event ) {
		$time = '';
		$timezone = 'UTC';

		if ( isset( $event->start_time ) ) {
			$time = $event->start_time;
			$timezone = isset( $event->timezone ) ? $event->timezone : 'UTC';
		} elseif ( ! is_object( $event ) &&  isset( $event['start_time'] ) ) {
			$time = $event['start_time'];
			$timezone = isset( $event['timezone'] ) ? $event['timezone'] : 'UTC';
		}

		$timestamp = CFF_Utils::cff_set_timezone( strtotime( $time ), $timezone );

		return $timestamp;
	}

	public static function get_event_end_time( $event ) {
		$time = '';
		$timezone = 'UTC';

		if ( isset( $event->end_time ) ) {
			$time = $event->end_time;
			$timezone = isset( $event->timezone ) ? $event->timezone : 'UTC';
		} elseif ( ! is_object( $event ) &&  isset( $event['end_time'] ) ) {
			$time = $event['end_time'];
			$timezone = isset( $event['timezone'] ) ? $event['timezone'] : 'UTC';
		}

		$timestamp = CFF_Utils::cff_set_timezone( strtotime( $time ), $timezone );

		return $timestamp;
	}

	public static function get_event_location_name( $event ) {
		if ( isset( $event->place->name ) ) {
			return $event->place->name;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['name'] ) ) {
			return $event['place']['name'];
		}
		return '';
	}

	public static function get_event_street( $event ) {
		if ( isset( $event->place->location->street ) ) {
			return $event->place->location->street;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['street'] ) ) {
			return $event['place']['location']['street'];
		}
		return '';
	}

	public static function get_event_state( $event ) {
		if ( isset( $event->place->location->state ) ) {
			return $event->place->location->state;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['state'] ) ) {
			return $event['place']['location']['state'];
		}
		return '';
	}

	public static function get_event_city( $event ) {
		if ( isset( $event->place->location->city ) ) {
			return $event->place->location->city;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['city'] ) ) {
			return $event['place']['location']['city'];
		}
		return '';
	}

	public static function get_event_zip( $event ) {
		if ( isset( $event->place->location->zip ) ) {
			return $event->place->location->zip;
		} elseif ( ! is_object( $event ) &&  isset( $event['place']['location']['zip'] ) ) {
			return $event['place']['location']['zip'];
		}
		return '';
	}

	public static function get_event_strings( $event ) {

		if ( is_array( $event ) ) {
			$event = (object) $event;
		}

		return $event;
		//Only create posts for the amount of posts specified
		// if ( $i == $show_posts ) break;
		isset($event->id) ? $id = $event->id : $id = '';
		//Object ID
		( !empty($event->object_id) ) ? $object_id = $event->object_id : $object_id = '';

		isset($event->name) ? $event_name = $event->name : $event_name = '';
		isset($event->attending_count) ? $attending_count = $event->attending_count : $attending_count = '';

		//Picture source
		$cff_no_event_img = false;
		if( isset($event->cover) ){
			$pic_big = $event->cover->source;
		} else {
			$cff_no_event_img = true;
			$pic_big = plugins_url( '/assets/img/event-image.png' , dirname(__FILE__) );
			$pic_big_lightbox = plugins_url( '/assets/img/event-image-cover.png' , dirname(__FILE__) );
		}
		$crop_event_pic = false;

		isset($event->start_time) ? $start_time = $event->start_time : $start_time = '';
		isset($event->end_time) ? $end_time = $event->end_time : $end_time = '';
		isset($event->timezone) ? $timezone = $event->timezone : $timezone = '';

		//Venue
		isset($event->place->location->latitude) ? $venue_latitude = $event->place->location->latitude : $venue_latitude = '';
		isset($event->place->location->longitude) ? $venue_longitude = $event->place->location->longitude : $venue_longitude = '';
		isset($event->place->location->city) ? $venue_city = $event->place->location->city : $venue_city = '';
		isset($event->place->location->state) ? $venue_state = $event->place->location->state : $venue_state = '';
		isset($event->place->location->country ) ? $venue_country = htmlentities($event->place->location->country, ENT_QUOTES, 'UTF-8') : $venue_country = '';
		isset($event->place->id) ? $venue_id = $event->place->id : $venue_id = '';
		$venue_link = 'https://facebook.com/' . $venue_id;
		isset($event->place->location->street) ? $venue_street = $event->place->location->street : $venue_street = '';
		isset($event->place->location->zip) ? $venue_zip = $event->place->location->zip : $venue_zip = '';
		isset($event->place->name) ? $location = $event->place->name : $location = '';

		isset($event->description) ? $description = $event->description : $description = '';
		$event_link = 'https://facebook.com/events/' . $id;
		isset($event->ticket_uri) ? $ticket_uri = htmlentities($event->ticket_uri, ENT_QUOTES, 'UTF-8') : $ticket_uri = '';

		//Interested in/going
		isset($event->interested_count) ? $interested_count = $event->interested_count : $interested_count = '';
		isset($event->attending_count) ? $attending_count = $event->attending_count : $attending_count = '';

		$cff_buy_tickets_text = false;

		//Event date
		$event_time = $start_time;

		//If timezone migration is enabled then remove last 5 characters
		if ( strlen($event_time) == 24 ) $event_time = substr($event_time, 0, -5);
		$cff_event_address_string = $location . $venue_street . $venue_city . $venue_state . $venue_zip;

		//Encode these after the filtering is done
		$event_name = htmlentities($event_name, ENT_QUOTES, 'UTF-8');
		$location = htmlentities($location, ENT_QUOTES, 'UTF-8');
		$venue_street = htmlentities($venue_street, ENT_QUOTES, 'UTF-8');
		$venue_city = htmlentities($venue_city, ENT_QUOTES, 'UTF-8');
		$venue_state = htmlentities($venue_state, ENT_QUOTES, 'UTF-8');
		$venue_zip = htmlentities($venue_zip, ENT_QUOTES, 'UTF-8');
		$description = htmlentities($description, ENT_QUOTES, 'UTF-8');

		//Recurring events time
		$cur_time = strtotime(date('Y-m-d'));
		$cff_multiple_date_count = 0;
		$event_time_item_id = '';

		if( isset($event->event_times) ){

			//Set time diff to be really high initially so the time difference comparison will be less than it
			$event_time_diff = 99999999999;
			$event_time_arr = array();

			foreach ( $event->event_times as $event_time_item){
				$event_item_time = $event_time_item->start_time;
				//If timezone migration is enabled then remove last 5 characters
				if ( strlen($event_item_time) == 24 ) $event_item_time = substr($event_item_time, 0, -5);
				$event_item_time = strtotime($event_item_time);

				if( $event_item_time > $cur_time ){
					//Find smallest diff between start_time and current time
					if( abs( $event_item_time - $cur_time ) < $event_time_diff ){
						$event_time_diff = abs( $event_item_time - $cur_time );

						//Use the start and end times from this "event_times" item
						$event_time = $event_time_item->start_time;
						//If timezone migration is enabled then remove last 5 characters
						if ( strlen($event_time) == 24 ) $event_time = substr($event_time, 0, -5);

						if( isset($event_time_item->end_time) ) $end_time = $event_time_item->end_time;
					}
					$cff_multiple_date_count++;

					//Create a custom array from the event times so I can sort them and loop through below
					$event_time_arr = CFF_Utils::cff_array_push_assoc(
						$event_time_arr,
						$event_item_time,
						array(
							'id' => $event_time_item->id,
							'end_time' => $event_time_item->end_time
						)
					);
				} //End if

			} //End for loop

			//Convert to unix
			$event_time = strtotime($event_time);

			//If timezone migration is enabled then remove last 5 characters
			if ( strlen($event_time) == 24 ) $event_time = substr($event_time, 0, -5);

			//-1 to account for date already being displayed
			$cff_multiple_date_count--;

			//Sort the array by date so they're shown chronologically
			ksort($event_time_arr);
		} else {
			$event_time = strtotime($event_time);
			// $event_time = $event_time;
		}

		//If timezone migration is enabled then remove last 5 characters from end time
		if ( strlen($end_time) == 24 ) $end_time = substr($end_time, 0, -5);
		/*
				 $event_name = htmlentities($event_name, ENT_QUOTES, 'UTF-8');
				$location = htmlentities($location, ENT_QUOTES, 'UTF-8');
				$venue_street = htmlentities($venue_street, ENT_QUOTES, 'UTF-8');
				$venue_city = htmlentities($venue_city, ENT_QUOTES, 'UTF-8');
				$venue_state = htmlentities($venue_state, ENT_QUOTES, 'UTF-8');
				$venue_zip = htmlentities($venue_zip, ENT_QUOTES, 'UTF-8');
				$description = htmlentities($description, ENT_QUOTES, 'UTF-8');
		 */
		$return = array(
			'start' => $event_time,
			'end' => $end_time,
			'location' => $location,
			'venue' => array(
				'street' => $venue_street,
				'city' => $venue_city,
				'state' => $venue_state,
				'zip' => $venue_zip

			),
			'description' => $description
		);

		return $return;
	}

	public static function get_from_id( $post ) {
		if ( is_object( $post ) && isset( $post->from->id ) ) {
			return $post->from->id;
		} elseif ( ! is_object( $post ) && isset( $post['from']['id'] ) ) {
			return $post['from']['id'];
		}

		return 0;
	}
}