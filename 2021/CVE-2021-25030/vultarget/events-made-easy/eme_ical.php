<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eme_esc_ical($prefix,$value='', $keep_html=0) {
   $value = preg_replace('/"/', '', $value);
   $value = preg_replace('/\\\\/', '\\\\', $value);
   $value = preg_replace('/\r\n|\n/', '\\n', $value);
   $value = preg_replace('/(;|\,)/', '\\\${1}', $value);
   // ical line length  is max 75, and folding adds an extra space at the beginning, so
   // the max is then 74, but let's take 70 to be sure
   $linelength=70;
   if (!empty($value)) {
	   $firstpart = mb_substr($value, 0, $linelength-mb_strlen($prefix));
	   $rest = mb_substr($value, $linelength-mb_strlen($prefix));
	   $value = $firstpart;
	   if (!empty($rest)) {
		   $value .= "\r\n ". join("\r\n ",eme_str_split_unicode($rest,$linelength));
	   }
   }
   if (!$keep_html)
      return $prefix.apply_filters('eme_text', $value)."\r\n";
   else
      return $prefix.$value."\r\n";
}

function eme_ical_single_event($event, $title_format, $description_format, $location_format) {
   global $eme_timezone;
   $title = eme_replace_event_placeholders ( $title_format, $event, "text");
   $description = eme_replace_event_placeholders ( $description_format, $event, "text");
   $html_description = eme_replace_event_placeholders($description_format, $event, "html");

   $event_link = eme_event_url($event);
   $contact = eme_get_contact($event);
   $contact_email = $contact->user_email;
   $contact_name = $contact->display_name;

   $startstring=new ExpressiveDate($event['event_start'],$eme_timezone);
   // in case of all day, we need the day alone, so we'll get it before switching to GMT since that might change the day
   $allday_dtstartdate=$startstring->format("Ymd");
   // now switch to GMT
   $startstring->setTimezone("GMT");
   $dtstartdate=$startstring->format("Ymd");
   $dtstarthour=$startstring->format("His");
   $dtstart=$dtstartdate."T".$dtstarthour."Z";
   // we'll use localtime, so no "Z"
   //$dtstart=$dtstartdate."T".$dtstarthour;
   if (eme_is_empty_datetime($event['event_end']))
      $event['event_end'] = $event['event_start'];
   $endstring=new ExpressiveDate($event['event_end'],$eme_timezone);
   // in case of all day, we need the day alone, so we'll get it before switching to GMT since that might change the day
   // an 'all day' event is flagged as starting at the beginning of one day and lasting until the beginning of the next
   // so it is the same as adding "T000000" as time spec to the start/end datestring
   // But since it "ends" at the beginning of the next day, we should add 24 hours, otherwise the event ends one day too soon
   $allday_dtenddate=$endstring->copy()->addOneDay()->format("Ymd");
   // now switch to GMT
   $endstring->setTimezone("GMT");
   $dtenddate=$endstring->format("Ymd");
   $dtendhour=$endstring->format("His");
   $dtend=$dtenddate."T".$dtendhour."Z";
   // we'll use localtime, so no "Z"
   //$dtend=$dtenddate."T".$dtendhour;
   $tzstring = get_option('timezone_string');
   if (get_option('eme_ical_quote_tzid'))
	$tzstring="'".$tzstring."'";

   $res = eme_esc_ical("BEGIN:VEVENT");
   //DTSTAMP must be in UTC format, so adding "Z" as well
   $res .= eme_esc_ical("DTSTAMP:" ,gmdate('Ymd').'T'. gmdate('His') . "Z");
   if ($event['event_properties']['all_day']) {
      // ical standard for an all day event: specify only the day, meaning
      // an 'all day' event is flagged as starting at the beginning of one day and lasting until the beginning of the next
      // so it is the same as adding "T000000" as time spec to the start/end datestring
      // But since it "ends" at the beginning of the next day, we should add 24 hours, otherwise the event ends one day too soon
      $res .= eme_esc_ical("DTSTART;VALUE=DATE:$allday_dtstartdate");
      $res .= eme_esc_ical("DTEND;VALUE=DATE:$allday_dtenddate");
   } else {
      // $res .= eme_esc_ical("DTSTART;TZID=$tzstring:$dtstart");
      // $res .= eme_esc_ical("DTEND;TZID=$tzstring:$dtend");
      // GMT now
      $res .= eme_esc_ical("DTSTART:$dtstart");
      $res .= eme_esc_ical("DTEND:$dtend");
   }
   $res .= eme_esc_ical("UID:","$dtstart-$dtend-".$event['event_id']."@".$_SERVER['SERVER_NAME']);
   // ORGANIZER not needed since ical is on a single user's calendar, not a group
   // $res .= eme_esc_ical("ORGANIZER;CN=$contact_name:MAILTO:",$contact_email);
   $res .= eme_esc_ical("SUMMARY:",$title);
   $res .= eme_esc_ical("DESCRIPTION:",$description);
   $res .= eme_esc_ical("X-ALT-DESC;FMTTYPE=text/html:",$html_description,1);
   $res .= eme_esc_ical("URL:",$event_link);
   $res .= eme_esc_ical("ATTACH:",$event_link);
   if ($event['event_image_id']) {
      $thumb_array = image_downsize( $event['event_image_id'], get_option('eme_thumbnail_size') );
      $thumb_url = $thumb_array[0];
      $res .= eme_esc_ical("ATTACH:",$thumb_url);
   }
   if (isset($event['location_id']) && $event['location_id']) {
      $location = eme_get_location($event['location_id']);
      if (!empty($location)) {
	      $location_txt = eme_replace_locations_placeholders ( $location_format, $location,"text");
	      $res .= eme_esc_ical("LOCATION:",$location_txt);
      }
   }
   if (has_filter('eme_ical_filter'))
         $res=apply_filters('eme_ical_filter',$res);
   // make sure the END comes last, even after the filter
   $res .= eme_esc_ical("END:VEVENT");

   return $res;
}

function eme_ical_link($justurl = 0, $echo = 0, $text = "ICAL", $category = "", $location_id="", $scope="future", $author='',$contact_person='', $notcategory = "") {
   $language = eme_detect_lang();

   if (strpos ( $justurl, "=" )) {
      // allows the use of arguments without breaking the legacy code
      $defaults = array ('justurl' => 0, 'echo' => $echo, 'text' => $text, 'scope' => $scope, 'category' => $category, 'author' => $author, 'contact_person' => $contact_person, 'location_id' => $location_id, 'notcategory' => $notcategory );

      $r = wp_parse_args ( $justurl, $defaults );
      extract ( $r );
   }
   $echo = ($echo==="true" || $echo==="1") ? true : $echo;
   $justurl = ($justurl==="true" || $justurl==="1") ? true : $justurl;
   $echo = ($echo==="false" || $echo==="0") ? false : $echo;
   $justurl = ($justurl==="false" || $justurl==="0") ? false : $justurl;

   if ($text == '')
      $text = "ICAL";
   $url = site_url ("/?eme_ical=public");
   if (!empty($location_id))
      $url = add_query_arg( array( 'location_id' => $location_id ), $url );
   if (!empty($category))
      $url = add_query_arg( array( 'category' => $category ), $url );
   if (!empty($notcategory))
      $url = add_query_arg( array( 'notcategory' => $notcategory ), $url );
   if (!empty($scope))
      $url = add_query_arg( array( 'scope' => $scope ), $url );
   if (!empty($author))
      $url = add_query_arg( array( 'author' => $author ), $url );
   if (!empty($contact_person))
      $url = add_query_arg( array( 'contact_person' => $contact_person ), $url );
   if (!empty($language))
      $url = add_query_arg( array( 'lang' => $language ), $url );

   $link = "<a href='$url'>$text</a>";

   if ($justurl)
      $result = $url;
   else
      $result = $link;
   if ($echo)
      echo $result;
   else
      return $result;
}

function eme_ical_link_shortcode($atts) {
   extract ( shortcode_atts ( array ('justurl' => 0, 'text' => 'ICAL', 'category' => '', 'location_id' =>'', 'scope' => 'future', 'author' => '', 'contact_person' => '', 'notcategory' => ''  ), $atts ) );

   $justurl = ($justurl==="true" || $justurl==="1") ? true : $justurl;
   $justurl = ($justurl==="false" || $justurl==="0") ? false : $justurl;
   $result = eme_ical_link ( $justurl,0,$text,$category,$location_id, $scope,$author,$contact_person,$notcategory );
   return $result;
}

function eme_ical_single() {
   eme_nocache_headers();
   header("Content-type: text/calendar; charset=utf-8");
   header("Content-Disposition: inline; filename=eme_single.ics");

   echo "BEGIN:VCALENDAR\r\n";
   echo "VERSION:2.0\r\n";
   echo "METHOD:PUBLISH\r\n";
   echo "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n";
   $event=eme_get_event(intval($_GET ['event_id']));
   if (!empty($event)) {
	   $title_format = get_option('eme_ical_title_format' );
	   $description_format = get_option('eme_ical_description_format');
	   $location_format = get_option('eme_ical_location_format');
	   echo eme_ical_single_event($event,$title_format,$description_format,$location_format);
   }
   echo "END:VCALENDAR\r\n";
}

function eme_ical() {
   eme_nocache_headers();
   header("Content-type: text/calendar; charset=utf-8");
   header("Content-Disposition: inline; filename=eme_public.ics");

   echo "BEGIN:VCALENDAR\r\n";
   echo "VERSION:2.0\r\n";
   echo "METHOD:PUBLISH\r\n";
   echo "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n";
   $title_format = get_option('eme_ical_title_format' );
   $description_format = get_option('eme_ical_description_format');
   $location_format = get_option('eme_ical_location_format');
   $location_id = isset( $_GET['location_id'] ) ? eme_sanitize_request(urldecode($_GET['location_id'])) : '';
   $category = isset( $_GET['category'] ) ? eme_sanitize_request(urldecode($_GET['category'])) : '';
   $notcategory = isset( $_GET['notcategory'] ) ? eme_sanitize_request(urldecode($_GET['notcategory'])) : '';
   $scope = isset( $_GET['scope'] ) ? eme_sanitize_request(urldecode($_GET['scope'])) : '';
   $author = isset( $_GET['author'] ) ? eme_sanitize_request(urldecode($_GET['author'])) : '';
   $contact_person = isset( $_GET['contact_person'] ) ? eme_sanitize_request(urldecode($_GET['contact_person'])) : '';
   $events = eme_get_events ( 0,$scope,"ASC",0,$location_id,$category, $author, $contact_person, 1, $notcategory);
   foreach ( $events as $event ) {
      echo eme_ical_single_event($event,$title_format,$description_format,$location_format);
   }
   echo "END:VCALENDAR\r\n";
}

function eme_sitemap() {
   eme_nocache_headers();
   header("Content-type: text/xml; charset=utf-8");
   header("Content-Disposition: inline; filename=eme_public.xml");
   echo '<?xml version="1.0" encoding="UTF-8"?>'."\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'."\n";
   $events = eme_get_events(5000, "all", "DESC");
   if (!empty($events)) {
        foreach ($events as $event) {
                // Build Sitemap Elements
                $locurl = eme_event_url($event);
                // Format the date - also in case some EME Events have 0000-00-00 date format, manually add 1st Jan 2012
                if(strtotime($event['modif_date']) > strtotime('2010-01-01 00:00')){
                        $lastmod = date('Y-m-d', strtotime($event['modif_date']));
                } else {
                        $lastmod = date('Y-m-d', strtotime('2010-01-01 00:00'));
                }
                // Make future events higher priority
                if(strtotime($event['event_start']) > strtotime('today')){
                        $priority = 0.9;
                        $changefreq = "daily";
                } else {
                        $priority = 0.3;
                        $changefreq = "monthly";
                }
                // Concatenate List of URLs
                echo "<url>\n<loc>$locurl</loc>\n<lastmod>$lastmod</lastmod>\n<changefreq>$changefreq</changefreq>\n<priority>$priority</priority>\n</url>\n";
        }
   }
   echo "</urlset>";
}

?>
