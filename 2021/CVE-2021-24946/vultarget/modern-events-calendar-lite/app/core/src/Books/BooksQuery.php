<?php

namespace MEC\Books;

use MEC\Singleton;

/**
 * Version 1.0.0
 */
class BooksQuery extends Singleton{

    public function get_books($q_args){

        $default = array(
            'post_type' => 'mec-books',
            'fields' => '',
            'limit' => -1,
            'post_status' => array('publish','pending','draft','future','private'),
            'meta_query' => array(
                'relation' => 'AND',
            )
        );

        $q_args = wp_parse_args($q_args,$default);

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

    public function get_books_ids($q_args){

        $default = array(
            'limit' => -1,
            'fields' => 'ids',
        );

        $q_args = wp_parse_args($q_args,$default);

        return $this->get_books($q_args);
    }
}