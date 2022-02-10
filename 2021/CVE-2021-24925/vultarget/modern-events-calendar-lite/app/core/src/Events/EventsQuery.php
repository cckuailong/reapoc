<?php

namespace MEC\Events;

use MEC\Singleton;



class EventsQuery extends Singleton{

    public function parse_args($q_args){

        $default = array(
            'post_type' => 'mec-events',
            'fields' => '',
            'posts_per_page' => get_option('posts_per_page',12),
            'post_status' => array('publish','pending','draft','future','private'),
            'meta_query' => array(
                'relation' => 'AND',
            ),
            'post__in' => array(),
            'meta_query' => array(),
        );

        if(is_numeric($q_args) && $q_args > 0){

            $q_args = array(
                'event_id' => $q_args
            );
        }

        $q_args = wp_parse_args( $q_args, $default );

        return $q_args;
    }

    public function get_tax_query($q_args){

        $taxonomies = array(
            'label',
            'category',
            'location',
            'organizer',
            'speaker',
            'event_type',
            'event_type_2',
        );

        $tax_query = array(
            'relation'=>'AND'
        );

        foreach($q_args as $tax => $v){

            if(!empty($v) && in_array($tax,$taxonomies)){

                $taxonomy = 'mec_'.$tax;
                $tax_query[$taxonomy.'_term_ids'] = array(
                    'taxonomy'=> $taxonomy,
                    'field'=>'term_id',
                    'terms'=> !is_array($v) ? explode(',', trim($v, ', ')) : (array)$v,
                );
            }
        }

        $tax_query = apply_filters('mec_map_tax_query', $tax_query,$q_args);

        return $tax_query;
    }

    public function get_events($q_args){

        $tax_query = array_merge_recursive(
            isset($q_args['tax_query']) ? $q_args['tax_query'] : array(),
            $this->get_tax_query($q_args)
        );
        $q_args['tax_query'] = $tax_query;

        $q_args = $this->parse_args($q_args);

        //event_ids start
        if(array_key_exists('event_id',$q_args) && !empty($q_args['event_id'])){

            $q_args['meta_query']['event_id'] = array(
                'key' => 'mec_event_id',
                'value' => $q_args['event_id'],
                'compare' => '=',
            );
        }

        if(array_key_exists('event_ids__in',$q_args)){

            $q_args['meta_query']['event_ids__in'] = array(
                'key' => 'mec_event_id',
                'value' => (array) $q_args['event_ids__in'],
                'compare' => 'IN',
            );
        }

        if(array_key_exists('event_ids__not_in',$q_args)){

            $q_args['meta_query']['event_ids__not_in'] = array(
                'key' => 'mec_event_id',
                'value' => (array) $q_args['event_ids__not_in'],
                'compare' => 'NOT IN',
            );
        }
        //event_ids end

        //other meta start
        if(array_key_exists('attendee_email',$q_args) && !empty($q_args['event_id'])){

            $q_args['meta_query']['attendee_email'] = array(
                'key' => 'mec_attendees',
                'value' => '"'.$q_args['attendee_email'].'"',
                'compare' => 'LIKE',
            );
        }

        if(array_key_exists('confirmed',$q_args) && !empty($q_args['confirmed'])){

            $q_args['meta_query']['confirmed'] = array(
                'key' => 'mec_confirmed',
                'value' => $q_args['confirmed'],
                'compare' => '=',
            );
        }

        if(array_key_exists('verified',$q_args) && !empty($q_args['confirmed'])){

            $q_args['meta_query']['verified'] = array(
                'key' => 'mec_verified',
                'value' => $q_args['verified'],
                'compare' => '=',
            );
        }
        //other meta end

        return get_posts($q_args);
    }

    public function get_events_ids($q_args){

        $default = array(
            'limit' => -1,
            'fields' => 'ids',
        );

        $q_args = wp_parse_args($q_args,$default);

        return $this->get_events($q_args);
    }

    public function get_last_event($return = 'post'){

		$query_args = $this->parse_args(
			array(
				'posts_per_page' => 1,
				'order' => 'DESC',
				'orderby' => 'ID'
			)
		);
		$events = get_posts($query_args);

		if(isset($events[0]) && !empty($events[0])){
			switch($return){
				case 'event':
                    $event = new Event($events[0]);
					return $event->get_detail();

					break;
				case 'post':
				default:

					return (array)$events[0];

				break;
			}
		}

		return false;
	}
}