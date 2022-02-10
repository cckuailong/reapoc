<?php

namespace MEC\SingleBuilder;

use MEC\SingleBuilder\Widgets\WidgetBase;
use MEC\Singleton;

class SingleBuilder extends Singleton {

    public function get_event_id(){

        return WidgetBase::getInstance()->get_event_id();
    }

    /**
     *  Get html of widget
     *
     * @param string $widget
     * @return string
     */
    public function output( $widget, $event_id = 0, $atts = array() ){

        $html = '';
        switch( $widget ){

            case 'title':
            case 'simple-header':

                $html = Widgets\SimpleHeader\SimpleHeader::getInstance()->output( $event_id, $atts );

                break;

            case 'content':

                $html = Widgets\Content\Content::getInstance()->output( $event_id, $atts );

                break;

            case 'featured-image':
            case 'thumbnail':

                $html = Widgets\FeaturedImage\FeaturedImage::getInstance()->output( $event_id, $atts );

                break;

            case 'event-breadcrumbs':

                $html = Widgets\EventBreadcrumbs\EventBreadcrumbs::getInstance()->output( $event_id, $atts );

                break;

            case 'event-cancellation-reason':

                $html = Widgets\EventCancellationReason\EventCancellationReason::getInstance()->output( $event_id, $atts );

                break;

            case 'event-attendees':

                $html = Widgets\EventAttendees\EventAttendees::getInstance()->output( $event_id, $atts );

                break;
            case 'booking-form':

                $html = Widgets\BookingForm\BookingForm::getInstance()->output( $event_id, $atts );

                break;
            case 'rsvp-form':

                $html = Widgets\RSVPForm\RSVPForm::getInstance()->output( $event_id, $atts );

                break;

            case 'event-category':

                $html = Widgets\EventCategories\EventCategories::getInstance()->output( $event_id, $atts );

                break;

            case 'event-cost':

                $html = Widgets\EventCost\EventCost::getInstance()->output( $event_id, $atts );

                break;

            case 'event-countdown':

                $html = Widgets\EventCountdown\EventCountdown::getInstance()->output( $event_id, $atts );

                break;

            case 'event-data':

                $html = Widgets\EventData\EventData::getInstance()->output( $event_id, $atts );

                break;

            case 'event-date':

                $html = Widgets\EventDate\EventDate::getInstance()->output( $event_id, $atts );

                break;

            case 'event-export':

                $html = Widgets\EventExport\EventExport::getInstance()->output( $event_id, $atts );

                break;
            case 'event-googlemap':

                $html = Widgets\EventGoogleMap\EventGoogleMap::getInstance()->output( $event_id, $atts );

                break;

            case 'event-hourly-schedule':

                $html = Widgets\EventHourlySchedule\EventHourlySchedule::getInstance()->output( $event_id, $atts );

                break;

            case 'event-labels':

                $html = Widgets\EventLabels\EventLabels::getInstance()->output( $event_id, $atts );

                break;
            case 'event-local-time':

                $html = Widgets\EventLocalTime\EventLocalTime::getInstance()->output( $event_id, $atts );

                break;
            case 'event-locations':

                $html = Widgets\EventLocations\EventLocations::getInstance()->output( $event_id, $atts );

                break;
            case 'event-more-info':

                $html = Widgets\EventMoreInfo\EventMoreInfo::getInstance()->output( $event_id, $atts );

                break;
            case 'event-next-occurrences':

                $html = Widgets\EventNextOccurrences\EventNextOccurrences::getInstance()->output( $event_id, $atts );

                break;
            case 'event-next-previous':

                $html = Widgets\EventNextPrevious\EventNextPrevious::getInstance()->output( $event_id, $atts );

                break;

            case 'event-organizers':

                $html = Widgets\EventOrganizers\EventOrganizers::getInstance()->output( $event_id, $atts );

                break;
            case 'event-qr-code':

                $html = Widgets\EventQrCode\EventQrCode::getInstance()->output( $event_id, $atts );

                break;
            case 'event-register-button':

                $html = Widgets\EventRegisterButton\EventRegisterButton::getInstance()->output( $event_id, $atts );

                break;
            case 'event-related':

                $html = Widgets\EventRelated\EventRelated::getInstance()->output( $event_id, $atts );

                break;
            case 'event-social-share':

                $html = Widgets\EventSocialShare\EventSocialShare::getInstance()->output( $event_id, $atts );

                break;
            case 'event-speakers':

                $html = Widgets\EventSpeakers\EventSpeakers::getInstance()->output( $event_id, $atts );

                break;
            case 'event-tags':

                $html = Widgets\EventTags\EventTags::getInstance()->output( $event_id, $atts );

                break;
            case 'event-time':

                $html = Widgets\EventTime\EventTime::getInstance()->output( $event_id, $atts );

                break;
            case 'event-weather':

                $html = Widgets\EventWeather\EventWeather::getInstance()->output( $event_id, $atts );

                break;
        }

        return $html;
    }
}