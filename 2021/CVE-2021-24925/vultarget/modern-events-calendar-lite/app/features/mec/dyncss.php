<?php
/** no direct access **/
defined('MECEXEC') or die();

// Get MEC Style Options
$styling = $this->main->get_styling();

// colorskin
$color = '';

function mec_dyn_hex2rgb($cc)
{
	if($cc[0] == '#') $cc = substr($cc, 1);

	if(strlen($cc) == 6) list($r, $g, $b) = array($cc[0] . $cc[1], $cc[2] . $cc[3], $cc[4] . $cc[5]);
	elseif(strlen($cc) == 3) list($r, $g, $b) = array($cc[0] . $cc[0], $cc[1] . $cc[1], $cc[2] . $cc[2]);
	else return false;

	$r = ((!function_exists('ctype_xdigit') or (function_exists('ctype_xdigit') and ctype_xdigit($r))) ? hexdec($r) : NULL);
	$g = ((!function_exists('ctype_xdigit') or (function_exists('ctype_xdigit') and ctype_xdigit($g))) ? hexdec($g) : NULL);
	$b = ((!function_exists('ctype_xdigit') or (function_exists('ctype_xdigit') and ctype_xdigit($b))) ? hexdec($b) : NULL);

	if(is_null($r) or is_null($g) or is_null($b)) return false;
	else return array('red' => $r, 'green' => $g, 'blue' => $b);
}

if(isset($styling['color']) && $styling['color']) $color = $styling['color'];
elseif(isset($styling['mec_colorskin'])) $color = $styling['mec_colorskin'];

$rgb_color = '64,217,241';
if(!empty($color)) $rgb_color = mec_dyn_hex2rgb($color);

// Typography
$mec_h_fontfamily_arr = $mec_p_fontfamily_arr = $fonts_url = $mec_container_normal_width = $mec_container_large_width = '';

if(isset($styling['mec_h_fontfamily']) && $styling['mec_h_fontfamily'])
{
	$mec_h_fontfamily_arr = $styling['mec_h_fontfamily'];
	$mec_h_fontfamily_arr = str_replace("[", "", $mec_h_fontfamily_arr);
	$mec_h_fontfamily_arr = str_replace("]", "", $mec_h_fontfamily_arr);
	$mec_h_fontfamily_arr = explode(",", $mec_h_fontfamily_arr);
}

if(isset($styling['mec_p_fontfamily']) && $styling['mec_p_fontfamily'])
{
	$mec_p_fontfamily_arr = $styling['mec_p_fontfamily'];
	$mec_p_fontfamily_arr = str_replace("[", "", $mec_p_fontfamily_arr);
	$mec_p_fontfamily_arr = str_replace("]", "", $mec_p_fontfamily_arr);
	$mec_p_fontfamily_arr = explode(",", $mec_p_fontfamily_arr);
}

if((is_array($mec_h_fontfamily_arr) && $mec_h_fontfamily_arr) || (is_array($mec_p_fontfamily_arr) && $mec_p_fontfamily_arr))
{
	//Google font
	$font_families  = array();
	$subsets    	= 'latin,latin-ext';
	$variant_h		= '';
	$variant_p		= '';
	$mec_h_fontfamily_array = '';
	if ( is_array($mec_h_fontfamily_arr) && $mec_h_fontfamily_arr ) :
		foreach($mec_h_fontfamily_arr as $key=>$mec_h_fontfamily_array) {
			if($key != '0') $variant_h .= $mec_h_fontfamily_array .', ';
		}
    endif;

	if ( is_array($mec_p_fontfamily_arr) && $mec_p_fontfamily_arr ) :
		foreach($mec_p_fontfamily_arr as $key=>$mec_p_fontfamily_array) {
			if($key != '0') $variant_p .= $mec_h_fontfamily_array .', ';
		}
	endif;

	$font_families[] = !empty($mec_h_fontfamily_arr[0]) ? $mec_h_fontfamily_arr[0] . ':' . $variant_h : '';
	$font_families[] = !empty($mec_p_fontfamily_arr[0]) ? $mec_p_fontfamily_arr[0] . ':' . $variant_p : '';
    
	if($font_families)
    {
		$fonts_url = add_query_arg(array(
            'family'=>urlencode(implode('|', $font_families)),
            'subset'=>urlencode($subsets),
		), 'https://fonts.googleapis.com/css');
    }
}

if(isset($styling['container_normal_width']) && $styling['container_normal_width'])
{
	$mec_container_normal_width = trim( $styling['container_normal_width'] );
	if($mec_container_normal_width ) {
		if (is_numeric($mec_container_normal_width)) {
			$mec_container_normal_width .= 'px';
		}
	}
}

if(isset($styling['container_large_width']) && $styling['container_large_width'])
{
	$mec_container_large_width = trim( $styling['container_large_width'] );
	if($mec_container_large_width ) {
		if (is_numeric($mec_container_large_width)) {
			$mec_container_large_width .= 'px';
		}
	}
}
$title_color = $title_color_hover = $content_color = '';
if(isset($styling['title_color']) && $styling['title_color'])
{
	$title_color = $styling['title_color'];
}

if(isset($styling['title_color_hover']) && $styling['title_color_hover'])
{
	$title_color_hover = $styling['title_color_hover'];
}

if(isset($styling['content_color']) && $styling['content_color'])
{
	$content_color = $styling['content_color'];
}

ob_start();

if( isset($styling['disable_gfonts']) && !$styling['disable_gfonts']) {
	echo '
	.mec-wrap, .mec-wrap div:not([class^="elementor-"]), .lity-container, .mec-wrap h1, .mec-wrap h2, .mec-wrap h3, .mec-wrap h4, .mec-wrap h5, .mec-wrap h6, .entry-content .mec-wrap h1, .entry-content .mec-wrap h2, .entry-content .mec-wrap h3, .entry-content .mec-wrap h4, .entry-content .mec-wrap h5, .entry-content .mec-wrap h6, .mec-wrap .mec-totalcal-box input[type="submit"], .mec-wrap .mec-totalcal-box .mec-totalcal-view span, .mec-agenda-event-title a, .lity-content .mec-events-meta-group-booking select, .lity-content .mec-book-ticket-variation h5, .lity-content .mec-events-meta-group-booking input[type="number"], .lity-content .mec-events-meta-group-booking input[type="text"], .lity-content .mec-events-meta-group-booking input[type="email"],.mec-organizer-item a, .mec-single-event .mec-events-meta-group-booking ul.mec-book-tickets-container li.mec-book-ticket-container label 
	{ font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;}';

	echo '
	.mec-event-content p, .mec-search-bar-result .mec-event-detail
	{ font-family: Roboto, sans-serif;}';

	echo ' .mec-wrap .mec-totalcal-box input, .mec-wrap .mec-totalcal-box select, .mec-checkboxes-search .mec-searchbar-category-wrap, .mec-wrap .mec-totalcal-box .mec-totalcal-view span 
	{ font-family: "Roboto", Helvetica, Arial, sans-serif; }';

	echo '
	.mec-event-grid-modern .event-grid-modern-head .mec-event-day, .mec-event-list-minimal .mec-time-details, .mec-event-list-minimal .mec-event-detail, .mec-event-list-modern .mec-event-detail, .mec-event-grid-minimal .mec-time-details, .mec-event-grid-minimal .mec-event-detail, .mec-event-grid-simple .mec-event-detail, .mec-event-cover-modern .mec-event-place, .mec-event-cover-clean .mec-event-place, .mec-calendar .mec-event-article .mec-localtime-details div, .mec-calendar .mec-event-article .mec-event-detail, .mec-calendar.mec-calendar-daily .mec-calendar-d-top h2, .mec-calendar.mec-calendar-daily .mec-calendar-d-top h3, .mec-toggle-item-col .mec-event-day, .mec-weather-summary-temp 
	{ font-family: "Roboto", sans-serif; }';

	echo ' .mec-fes-form, .mec-fes-list, .mec-fes-form input, .mec-event-date .mec-tooltip .box, .mec-event-status .mec-tooltip .box, .ui-datepicker.ui-widget, .mec-fes-form button[type="submit"].mec-fes-sub-button, .mec-wrap .mec-timeline-events-container p, .mec-wrap .mec-timeline-events-container h4, .mec-wrap .mec-timeline-events-container div, .mec-wrap .mec-timeline-events-container a, .mec-wrap .mec-timeline-events-container span 
	{ font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important; }';

}

// render headings font familty
if($mec_h_fontfamily_arr): ?>
	/* == Custom Fonts For H Tag
		---------------- */
	.mec-hourly-schedule-speaker-name, .mec-events-meta-group-countdown .countdown-w span, .mec-single-event .mec-event-meta dt, .mec-hourly-schedule-speaker-job-title, .post-type-archive-mec-events h1, .mec-ticket-available-spots .mec-event-ticket-name, .tax-mec_category h1, .mec-wrap h1, .mec-wrap h2, .mec-wrap h3, .mec-wrap h4, .mec-wrap h5, .mec-wrap h6,.entry-content .mec-wrap h1, .entry-content .mec-wrap h2, .entry-content .mec-wrap h3,.entry-content  .mec-wrap h4, .entry-content .mec-wrap h5, .entry-content .mec-wrap h6
	{ font-family: '<?php echo $mec_h_fontfamily_arr[0]; ?>', Helvetica, Arial, sans-serif;}
<?php endif;

// render paragraph font familty
if($mec_p_fontfamily_arr): ?>
	/* == Custom Fonts For P Tag
		---------------- */
	.mec-single-event .mec-event-meta .mec-events-event-cost, .mec-event-data-fields .mec-event-data-field-item .mec-event-data-field-value, .mec-event-data-fields .mec-event-data-field-item .mec-event-data-field-name, .mec-wrap .info-msg div, .mec-wrap .mec-error div, .mec-wrap .mec-success div, .mec-wrap .warning-msg div, .mec-breadcrumbs .mec-current, .mec-events-meta-group-tags, .mec-single-event .mec-events-meta-group-booking .mec-event-ticket-available, .mec-single-modern .mec-single-event-bar>div dd, .mec-single-event .mec-event-meta dd, .mec-single-event .mec-event-meta dd a, .mec-next-occ-booking span, .mec-hourly-schedule-speaker-description, .mec-single-event .mec-speakers-details ul li .mec-speaker-job-title, .mec-single-event .mec-speakers-details ul li .mec-speaker-name, .mec-event-data-field-items, .mec-load-more-button, .mec-events-meta-group-tags a, .mec-events-button, .mec-wrap abbr, .mec-event-schedule-content dl dt, .mec-breadcrumbs a, .mec-breadcrumbs span .mec-event-content p, .mec-wrap p { font-family: '<?php echo $mec_p_fontfamily_arr[0]; ?>',sans-serif; font-weight:300;}
<?php endif;

// render colorskin
if($color && $color != '#40d9f1'): ?>
	/* == TextColors
		---------------- */
	.mec-event-grid-minimal .mec-modal-booking-button:hover, .mec-events-timeline-wrap .mec-organizer-item a, .mec-events-timeline-wrap .mec-organizer-item:after, .mec-events-timeline-wrap .mec-shortcode-organizers i, .mec-timeline-event .mec-modal-booking-button, .mec-wrap .mec-map-lightbox-wp.mec-event-list-classic .mec-event-date, .mec-timetable-t2-col .mec-modal-booking-button:hover, .mec-event-container-classic .mec-modal-booking-button:hover, .mec-calendar-events-side .mec-modal-booking-button:hover, .mec-event-grid-yearly  .mec-modal-booking-button, .mec-events-agenda .mec-modal-booking-button, .mec-event-grid-simple .mec-modal-booking-button, .mec-event-list-minimal  .mec-modal-booking-button:hover, .mec-timeline-month-divider,  .mec-wrap.colorskin-custom .mec-totalcal-box .mec-totalcal-view span:hover,.mec-wrap.colorskin-custom .mec-calendar.mec-event-calendar-classic .mec-selected-day,.mec-wrap.colorskin-custom .mec-color, .mec-wrap.colorskin-custom .mec-event-sharing-wrap .mec-event-sharing > li:hover a, .mec-wrap.colorskin-custom .mec-color-hover:hover, .mec-wrap.colorskin-custom .mec-color-before *:before ,.mec-wrap.colorskin-custom .mec-widget .mec-event-grid-classic.owl-carousel .owl-nav i,.mec-wrap.colorskin-custom .mec-event-list-classic a.magicmore:hover,.mec-wrap.colorskin-custom .mec-event-grid-simple:hover .mec-event-title,.mec-wrap.colorskin-custom .mec-single-event .mec-event-meta dd.mec-events-event-categories:before,.mec-wrap.colorskin-custom .mec-single-event-date:before,.mec-wrap.colorskin-custom .mec-single-event-time:before,.mec-wrap.colorskin-custom .mec-events-meta-group.mec-events-meta-group-venue:before,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-previous-month i,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-next-month:hover,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-previous-month:hover,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-next-month:hover,.mec-wrap.colorskin-custom .mec-calendar.mec-event-calendar-classic dt.mec-selected-day:hover,.mec-wrap.colorskin-custom .mec-infowindow-wp h5 a:hover, .colorskin-custom .mec-events-meta-group-countdown .mec-end-counts h3,.mec-calendar .mec-calendar-side .mec-next-month i,.mec-wrap .mec-totalcal-box i,.mec-calendar .mec-event-article .mec-event-title a:hover,.mec-attendees-list-details .mec-attendee-profile-link a:hover,.mec-wrap.colorskin-custom .mec-next-event-details li i, .mec-next-event-details i:before, .mec-marker-infowindow-wp .mec-marker-infowindow-count, .mec-next-event-details a,.mec-wrap.colorskin-custom .mec-events-masonry-cats a.mec-masonry-cat-selected,.lity .mec-color,.lity .mec-color-before :before,.lity .mec-color-hover:hover,.lity .mec-wrap .mec-color,.lity .mec-wrap .mec-color-before :before,.lity .mec-wrap .mec-color-hover:hover,.leaflet-popup-content .mec-color,.leaflet-popup-content .mec-color-before :before,.leaflet-popup-content .mec-color-hover:hover,.leaflet-popup-content .mec-wrap .mec-color,.leaflet-popup-content .mec-wrap .mec-color-before :before,.leaflet-popup-content .mec-wrap .mec-color-hover:hover, .mec-calendar.mec-calendar-daily .mec-calendar-d-table .mec-daily-view-day.mec-daily-view-day-active.mec-color, .mec-map-boxshow div .mec-map-view-event-detail.mec-event-detail i,.mec-map-boxshow div .mec-map-view-event-detail.mec-event-detail:hover,.mec-map-boxshow .mec-color,.mec-map-boxshow .mec-color-before :before,.mec-map-boxshow .mec-color-hover:hover,.mec-map-boxshow .mec-wrap .mec-color,.mec-map-boxshow .mec-wrap .mec-color-before :before,.mec-map-boxshow .mec-wrap .mec-color-hover:hover, .mec-choosen-time-message, .mec-booking-calendar-month-navigation .mec-next-month:hover, .mec-booking-calendar-month-navigation .mec-previous-month:hover, .mec-yearly-view-wrap .mec-agenda-event-title a:hover, .mec-yearly-view-wrap .mec-yearly-title-sec .mec-next-year i, .mec-yearly-view-wrap .mec-yearly-title-sec .mec-previous-year i, .mec-yearly-view-wrap .mec-yearly-title-sec .mec-next-year:hover, .mec-yearly-view-wrap .mec-yearly-title-sec .mec-previous-year:hover, .mec-av-spot .mec-av-spot-head .mec-av-spot-box span, .mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-previous-month:hover .mec-load-month-link, .mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-next-month:hover .mec-load-month-link, .mec-yearly-view-wrap .mec-yearly-title-sec .mec-previous-year:hover .mec-load-month-link, .mec-yearly-view-wrap .mec-yearly-title-sec .mec-next-year:hover .mec-load-month-link, .mec-skin-list-events-container .mec-data-fields-tooltip .mec-data-fields-tooltip-box ul .mec-event-data-field-item a, .mec-booking-shortcode .mec-event-ticket-name, .mec-booking-shortcode .mec-event-ticket-price, .mec-booking-shortcode .mec-ticket-variation-name, .mec-booking-shortcode .mec-ticket-variation-price, .mec-booking-shortcode label, .mec-booking-shortcode .nice-select, .mec-booking-shortcode input, .mec-booking-shortcode span.mec-book-price-detail-description, .mec-booking-shortcode .mec-ticket-name, .mec-booking-shortcode label.wn-checkbox-label
	{color: <?php echo $color; ?>}

	/* == Backgrounds
		----------------- */
	.mec-skin-carousel-container .mec-event-footer-carousel-type3 .mec-modal-booking-button:hover, .mec-wrap.colorskin-custom .mec-event-sharing .mec-event-share:hover .event-sharing-icon,.mec-wrap.colorskin-custom .mec-event-grid-clean .mec-event-date,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing > li:hover a i,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing .mec-event-share:hover .mec-event-sharing-icon,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing li:hover a i,.mec-wrap.colorskin-custom .mec-calendar:not(.mec-event-calendar-classic) .mec-selected-day,.mec-wrap.colorskin-custom .mec-calendar .mec-selected-day:hover,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-row  dt.mec-has-event:hover,.mec-wrap.colorskin-custom .mec-calendar .mec-has-event:after, .mec-wrap.colorskin-custom .mec-bg-color, .mec-wrap.colorskin-custom .mec-bg-color-hover:hover, .colorskin-custom .mec-event-sharing-wrap:hover > li, .mec-wrap.colorskin-custom .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.mec-wrap .flip-clock-wrapper ul li a div div.inn,.mec-wrap .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.event-carousel-type1-head .mec-event-date-carousel,.mec-event-countdown-style3 .mec-event-date,#wrap .mec-wrap article.mec-event-countdown-style1,.mec-event-countdown-style1 .mec-event-countdown-part3 a.mec-event-button,.mec-wrap .mec-event-countdown-style2,.mec-map-get-direction-btn-cnt input[type="submit"],.mec-booking button,span.mec-marker-wrap,.mec-wrap.colorskin-custom .mec-timeline-events-container .mec-timeline-event-date:before, .mec-has-event-for-booking.mec-active .mec-calendar-novel-selected-day, .mec-booking-tooltip.multiple-time .mec-booking-calendar-date.mec-active, .mec-booking-tooltip.multiple-time .mec-booking-calendar-date:hover, .mec-ongoing-normal-label, .mec-calendar .mec-has-event:after, .mec-event-list-modern .mec-event-sharing li:hover .telegram
	{background-color: <?php echo $color; ?>;}

	.mec-booking-tooltip.multiple-time .mec-booking-calendar-date:hover, .mec-calendar-day.mec-active .mec-booking-tooltip.multiple-time .mec-booking-calendar-date.mec-active
	{ background-color: <?php echo $color; ?>;}

	/* == BorderColors
		------------------ */
	.mec-skin-carousel-container .mec-event-footer-carousel-type3 .mec-modal-booking-button:hover, .mec-timeline-month-divider, .mec-wrap.colorskin-custom .mec-single-event .mec-speakers-details ul li .mec-speaker-avatar a:hover img,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing > li:hover a i,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing .mec-event-share:hover .mec-event-sharing-icon,.mec-wrap.colorskin-custom .mec-event-list-standard .mec-month-divider span:before,.mec-wrap.colorskin-custom .mec-single-event .mec-social-single:before,.mec-wrap.colorskin-custom .mec-single-event .mec-frontbox-title:before,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-events-side .mec-table-side-day, .mec-wrap.colorskin-custom .mec-border-color, .mec-wrap.colorskin-custom .mec-border-color-hover:hover, .colorskin-custom .mec-single-event .mec-frontbox-title:before, .colorskin-custom .mec-single-event .mec-wrap-checkout h4:before, .colorskin-custom .mec-single-event .mec-events-meta-group-booking form > h4:before, .mec-wrap.colorskin-custom .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.mec-wrap .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.event-carousel-type1-head .mec-event-date-carousel:after,.mec-wrap.colorskin-custom .mec-events-masonry-cats a.mec-masonry-cat-selected, .mec-marker-infowindow-wp .mec-marker-infowindow-count, .mec-wrap.colorskin-custom .mec-events-masonry-cats a:hover, .mec-has-event-for-booking .mec-calendar-novel-selected-day, .mec-booking-tooltip.multiple-time .mec-booking-calendar-date.mec-active, .mec-booking-tooltip.multiple-time .mec-booking-calendar-date:hover, .mec-virtual-event-history h3:before, .mec-booking-tooltip.multiple-time .mec-booking-calendar-date:hover, .mec-calendar-day.mec-active .mec-booking-tooltip.multiple-time .mec-booking-calendar-date.mec-active, .mec-rsvp-form-box form > h4:before, .mec-wrap .mec-box-title::before, .mec-box-title::before  
	{border-color: <?php echo $color; ?>;}

	.mec-wrap.colorskin-custom .mec-event-countdown-style3 .mec-event-date:after,.mec-wrap.colorskin-custom .mec-month-divider span:before, .mec-calendar.mec-event-container-simple dl dt.mec-selected-day, .mec-calendar.mec-event-container-simple dl dt.mec-selected-day:hover
	{border-bottom-color:<?php echo $color; ?>;}

	.mec-wrap.colorskin-custom  article.mec-event-countdown-style1 .mec-event-countdown-part2:after
	{border-color: transparent transparent transparent <?php echo $color; ?>;}

	/* == BoxShadow
		------------------ */
	.mec-wrap.colorskin-custom .mec-box-shadow-color { box-shadow: 0 4px 22px -7px <?php echo $color; ?>;}


	/* == Timeline View
		------------------ */
	.mec-events-timeline-wrap .mec-shortcode-organizers, .mec-timeline-event .mec-modal-booking-button, .mec-events-timeline-wrap:before, .mec-wrap.colorskin-custom .mec-timeline-event-local-time, .mec-wrap.colorskin-custom .mec-timeline-event-time ,.mec-wrap.colorskin-custom .mec-timeline-event-location,.mec-choosen-time-message { background: rgba(<?php echo $rgb_color['red']; ?>,<?php echo $rgb_color['green']; ?>,<?php echo $rgb_color['blue']; ?>,.11);}

	.mec-wrap.colorskin-custom .mec-timeline-events-container .mec-timeline-event-date:after
	{ background: rgba(<?php echo $rgb_color['red']; ?>,<?php echo $rgb_color['green']; ?>,<?php echo $rgb_color['blue']; ?>,.3);}

	/* == Booking Shortcode
		------------------ */
	.mec-booking-shortcode button 
	{ box-shadow: 0 2px 2px rgba(<?php echo $rgb_color['red']; ?> <?php echo $rgb_color['green']; ?> <?php echo $rgb_color['blue']; ?> / 27%);}

	.mec-booking-shortcode button.mec-book-form-back-button
	{ background-color: rgba(<?php echo $rgb_color['red']; ?> <?php echo $rgb_color['green']; ?> <?php echo $rgb_color['blue']; ?> / 40%);}

	.mec-events-meta-group-booking-shortcode
	{ background: rgba(<?php echo $rgb_color['red']; ?>,<?php echo $rgb_color['green']; ?>,<?php echo $rgb_color['blue']; ?>,.14);}

	.mec-booking-shortcode label.wn-checkbox-label, .mec-booking-shortcode .nice-select,.mec-booking-shortcode input, .mec-booking-shortcode .mec-book-form-gateway-label input[type=radio]:before, .mec-booking-shortcode input[type=radio]:checked:before, .mec-booking-shortcode ul.mec-book-price-details li, .mec-booking-shortcode ul.mec-book-price-details
	{ border-color: rgba(<?php echo $rgb_color['red']; ?> <?php echo $rgb_color['green']; ?> <?php echo $rgb_color['blue']; ?> / 27%) !important;}
	
	.mec-booking-shortcode input::-webkit-input-placeholder,.mec-booking-shortcode textarea::-webkit-input-placeholder
	{color: <?php echo $color; ?>}

	.mec-booking-shortcode input::-moz-placeholder,.mec-booking-shortcode textarea::-moz-placeholder
	{color: <?php echo $color; ?>}

	.mec-booking-shortcode input:-ms-input-placeholder,.mec-booking-shortcode textarea:-ms-input-placeholder 
	{color: <?php echo $color; ?>}

	.mec-booking-shortcode input:-moz-placeholder,.mec-booking-shortcode textarea:-moz-placeholder 
	{color: <?php echo $color; ?>}

	.mec-booking-shortcode label.wn-checkbox-label:after, .mec-booking-shortcode label.wn-checkbox-label:before, .mec-booking-shortcode input[type=radio]:checked:after
	{background-color: <?php echo $color; ?>}


<?php endif;

// Render Container Width
if($mec_container_normal_width): ?>
@media only screen and (min-width: 1281px) {
	.mec-container,
    body [id*="mec_skin_"].mec-fluent-wrap {
        width: <?php echo $mec_container_normal_width; ?> !important;
        max-width: <?php echo $mec_container_normal_width; ?> !important;
    }
}
<?php endif;


if($mec_container_large_width): ?>
@media only screen and (min-width: 1600px) {
	.mec-container,
    body [id*="mec_skin_"].mec-fluent-wrap {
        width: <?php echo $mec_container_large_width; ?> !important;
        max-width: <?php echo $mec_container_large_width; ?> !important;
    }
}
<?php endif;

if($title_color): ?>
.mec-wrap h1 a, .mec-wrap h2 a, .mec-wrap h3 a, .mec-wrap h4 a, .mec-wrap h5 a, .mec-wrap h6 a,.entry-content .mec-wrap h1 a, .entry-content .mec-wrap h2 a, .entry-content .mec-wrap h3 a,.entry-content  .mec-wrap h4 a, .entry-content .mec-wrap h5 a, .entry-content .mec-wrap h6 a {
	color: <?php echo $title_color; ?> !important;
}
<?php endif;

if($title_color_hover): ?>
.mec-wrap.colorskin-custom h1 a:hover, .mec-wrap.colorskin-custom h2 a:hover, .mec-wrap.colorskin-custom h3 a:hover, .mec-wrap.colorskin-custom h4 a:hover, .mec-wrap.colorskin-custom h5 a:hover, .mec-wrap.colorskin-custom h6 a:hover,.entry-content .mec-wrap.colorskin-custom h1 a:hover, .entry-content .mec-wrap.colorskin-custom h2 a:hover, .entry-content .mec-wrap.colorskin-custom h3 a:hover,.entry-content  .mec-wrap.colorskin-custom h4 a:hover, .entry-content .mec-wrap.colorskin-custom h5 a:hover, .entry-content .mec-wrap.colorskin-custom h6 a:hover {
	color: <?php echo $title_color_hover; ?> !important;
}
<?php endif;

if($content_color): ?>
.mec-wrap.colorskin-custom .mec-event-description {
	color: <?php echo $content_color; ?>;
}
<?php endif;

if (isset($styling['disable_fluent_height_limitation']) && $styling['disable_fluent_height_limitation']) {
	?>
	.mec-fluent-wrap.mec-skin-list-wrap .mec-calendar,
	.mec-fluent-wrap .mec-skin-weekly-view-events-container,
	.mec-fluent-wrap .mec-daily-view-events-left-side,
	.mec-fluent-wrap .mec-daily-view-events-right-side,
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-calendar-sec,
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-agenda-sec,
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-calendar,
	.mec-fluent-wrap.mec-skin-tile-container .mec-calendar,
	.mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda-wrap {
		max-height: unset !important;
	}
	<?php
}

/**
 * 
 * Frontend Event Submission Layout Color Styles
 * 
 */
// Main Color
$fes_main_color = '#40d9f1';
if (isset($styling['fes_color']) && $styling['fes_color']) {
	$fes_main_color = $styling['fes_color'];
	list($fes_main_color_r, $fes_main_color_g, $fes_main_color_b) = sscanf($fes_main_color, "#%02x%02x%02x");
	?>
	/* FES Main Color  */
	.mec-fes-form #mec_bfixed_form_field_types .button:before, .mec-fes-form #mec_reg_form_field_types .button:before, .mec-fes-form #mec_bfixed_form_field_types .button, .mec-fes-form #mec_reg_form_field_types .button, .mec-fes-form #mec_meta_box_tickets_form [id^=mec_ticket_row] .mec_add_price_date_button, .mec-fes-form .mec-meta-box-fields h4, .mec-fes-form .html-active .switch-html, .mec-fes-form .tmce-active .switch-tmce, .mec-fes-form .wp-editor-tabs .wp-switch-editor:active, .mec-fes-form .mec-form-row .button:not(.wp-color-result), .mec-fes-form .mec-title span.mec-dashicons, .mec-fes-form .mec-form-row .quicktags-toolbar input.button.button-small, .mec-fes-list ul li a:hover, .mec-fes-form input[type=file], .mec-fes-form .mec-attendees-wrapper .mec-attendees-list .mec-booking-attendees-tooltip:before {
		color: <?php echo $fes_main_color; ?>;
	}

	.mec-fes-form #mec_reg_form_field_types .button.red:before, .mec-fes-form #mec_reg_form_field_types .button.red {
		border-color: #ffd2dd;
		color: #ea6485;
	}

	.mec-fes-form #mec_reg_form_field_types .button.red:hover, .mec-fes-form #mec_reg_form_field_types .button.red:before, .mec-fes-form #mec_reg_form_field_types .button:hover, .mec-fes-form #mec_bfixed_form_field_types .button:hover:before, .mec-fes-form #mec_reg_form_field_types .button:hover:before, .mec-fes-form #mec_bfixed_form_field_types .button:hover, .mec-fes-form .mec-form-row .button:not(.wp-color-result):hover {
		color: #fff;
	}

	.mec-fes-form #mec_reg_form_field_types .button.red:hover, .mec-fes-form #mec_reg_form_field_types .button:hover, .mec-fes-list ul li .mec-fes-event-export a:hover, .mec-fes-list ul li .mec-fes-event-view a:hover, .mec-fes-form button[type=submit].mec-fes-sub-button, .mec-fes-form .mec-form-row .button:not(.wp-color-result):hover {
		background: <?php echo $fes_main_color; ?>;
	}

	.mec-fes-form #mec_reg_form_field_types .button.red:hover, .mec-fes-form #mec_bfixed_form_fields input[type=checkbox]:hover, .mec-fes-form #mec_bfixed_form_fields input[type=radio]:hover, .mec-fes-form #mec_reg_form_fields input[type=checkbox]:hover, .mec-fes-form #mec_reg_form_fields input[type=radio]:hover, .mec-fes-form input[type=checkbox]:hover, .mec-fes-form input[type=radio]:hover, .mec-fes-form #mec_reg_form_field_types .button:hover, .mec-fes-form .mec-form-row .button:not(.wp-color-result):hover, .mec-fes-list ul li .mec-fes-event-export a:hover, .mec-fes-list ul li .mec-fes-event-view a:hover, .mec-fes-form input[type=file], .mec-fes-form .mec-attendees-wrapper .mec-attendees-list .w-clearfix:first-child {
		border-color: <?php echo $fes_main_color; ?>;
	}

	.mec-fes-form button[type=submit].mec-fes-sub-button {
		box-shadow: 0 2px 8px -4px <?php echo $fes_main_color; ?>;
	}

	.mec-fes-form button[type=submit].mec-fes-sub-button:hover {
		box-shadow: 0 2px 12px -2px <?php echo $fes_main_color; ?>;
	}

	.mec-fes-form, .mec-fes-list, .mec-fes-form .html-active .switch-html, .mec-fes-form .tmce-active .switch-tmce, .mec-fes-form .wp-editor-tabs .wp-switch-editor:active, .mec-fes-form .mec-attendees-wrapper .mec-attendees-list .w-clearfix {
		background: rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.12)'; ?>;
	}

	.mec-fes-form .mec-meta-box-fields h4, .mec-fes-form .quicktags-toolbar, .mec-fes-form div.mce-toolbar-grp {
		background: rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.23)'; ?>;
	}

	.mec-fes-form ul#mec_bfixed_form_fields li, .mec-fes-form ul#mec_reg_form_fields li, .mec-fes-form ul#mec_bfixed_form_fields li, .mec-fes-form ul#mec_reg_form_fields li {
		background: rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.03)'; ?>;
	}

	.mec-fes-form .mce-toolbar .mce-btn-group .mce-btn.mce-listbox, .mec-fes-form ul#mec_bfixed_form_fields li, .mec-fes-form ul#mec_reg_form_fields li, .mec-fes-form ul#mec_bfixed_form_fields li, .mec-fes-form ul#mec_reg_form_fields li, .mec-fes-form #mec_bfixed_form_fields input[type=checkbox], .mec-fes-form #mec_bfixed_form_fields input[type=radio], .mec-fes-form #mec_reg_form_fields input[type=checkbox], .mec-fes-form #mec_reg_form_fields input[type=radio], .mec-fes-form input[type=checkbox], .mec-fes-form input[type=radio], .mec-fes-form #mec-event-data input[type=date], .mec-fes-form input[type=email], .mec-fes-form input[type=number], .mec-fes-form input[type=password], .mec-fes-form input[type=tel], .mec-fes-form input[type=text], .mec-fes-form input[type=url], .mec-fes-form select, .mec-fes-form textarea, .mec-fes-list ul li, .mec-fes-form .quicktags-toolbar, .mec-fes-form div.mce-toolbar-grp, .mec-fes-form .mce-tinymce.mce-container.mce-panel, .mec-fes-form #mec_meta_box_tickets_form [id^=mec_ticket_row] .mec_add_price_date_button, .mec-fes-form #mec_bfixed_form_field_types .button, .mec-fes-form #mec_reg_form_field_types .button, .mec-fes-form .mec-meta-box-fields, .mec-fes-form .wp-editor-tabs .wp-switch-editor, .mec-fes-form .mec-form-row .button:not(.wp-color-result) {
		border-color: rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.3)'; ?>;
	}

	.mec-fes-form #mec-event-data input[type=date], .mec-fes-form input[type=email], .mec-fes-form input[type=number], .mec-fes-form input[type=password], .mec-fes-form input[type=tel], .mec-fes-form input[type=text], .mec-fes-form input[type=url], .mec-fes-form select, .mec-fes-form textarea {
		box-shadow: 0 2px 5px rgb<?php echo '(' . $fes_main_color_r . ' ' . $fes_main_color_g . ' ' . $fes_main_color_b . ' / ' . '7%)'; ?> inset;
	}

	.mec-fes-list ul li, .mec-fes-form .mec-form-row .button:not(.wp-color-result) {
		box-shadow: 0 2px 6px -4px rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.2)'; ?>;
	}

	.mec-fes-form #mec_bfixed_form_field_types .button, .mec-fes-form #mec_reg_form_field_types .button, .mec-fes-form .mec-meta-box-fields {
		box-shadow: 0 2px 6px -3px rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.2)'; ?>;
	}

	.mec-fes-form #mec_meta_box_tickets_form [id^=mec_ticket_row] .mec_add_price_date_button, .mec-fes-form .mce-tinymce.mce-container.mce-panel, .mec-fes-form .mec-form-row .button:not(.wp-color-result):hover {
		box-shadow: 0 2px 6px -3px <?php echo $fes_main_color; ?>;
	}

	.mec-fes-form .quicktags-toolbar, .mec-fes-form div.mce-toolbar-grp {
		box-shadow: 0 1px 0 1px rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.2)'; ?>;
	}

	.mec-fes-form #mec_bfixed_form_fields input[type=checkbox], .mec-fes-form #mec_bfixed_form_fields input[type=radio], .mec-fes-form #mec_reg_form_fields input[type=checkbox], .mec-fes-form #mec_reg_form_fields input[type=radio], .mec-fes-form input[type=checkbox], .mec-fes-form input[type=radio] {
		box-shadow: 0 1px 3px -1px rgba<?php echo '(' . $fes_main_color_r . ', ' . $fes_main_color_g . ', ' . $fes_main_color_b . ', ' . '0.2)'; ?>;
	}

	.mec-fes-form #mec_bfixed_form_fields input[type=checkbox]:checked, .mec-fes-form #mec_bfixed_form_fields input[type=radio]:checked, .mec-fes-form #mec_reg_form_fields input[type=checkbox]:checked, .mec-fes-form #mec_reg_form_fields input[type=radio]:checked, .mec-fes-form .mec-form-row input[type=checkbox]:checked, .mec-fes-form .mec-form-row input[type=radio]:checked {
		box-shadow: 0 1px 6px -2px <?php echo $fes_main_color; ?>;
    	border-color: <?php echo $fes_main_color; ?>;
    	background: <?php echo $fes_main_color; ?> !important;
	}

	.mec-fes-form .mec-available-color-row span.color-selected {
		box-shadow: 0 0 0 2px <?php echo $fes_main_color; ?>, 0 2px 8px -1px <?php echo $fes_main_color; ?>;
	}

	<?php
}


/**
 * 
 * Fluent-view Layout Color Styles
 * 
 */
// Main Color
$fluent_main_color = '#ade7ff';
if (isset($styling['fluent_main_color']) && $styling['fluent_main_color']) {
	$fluent_main_color = $styling['fluent_main_color'];
	list($fluent_main_color_r, $fluent_main_color_g, $fluent_main_color_b) = sscanf($fluent_main_color, "#%02x%02x%02x");
	?>
	/* MAIN COLOR */
	.mec-more-events-icon, .mec-single-fluent-wrap .mec-next-event-details a, .mec-wrap.colorskin-custom .mec-color-before *:before, .mec-single-fluent-wrap .mec-marker-infowindow-wp .mec-marker-infowindow-count, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .nice-select .list li, .mec-single-fluent-wrap .mec-events-meta-group-booking .nice-select .list li, .mec-single-fluent-wrap .mec-single-event-organizer dd i, .mec-single-fluent-wrap .mec-single-event-additional-organizers dd i, .mec-single-fluent-wrap .mec-next-event-details i:before, .mec-single-fluent-wrap .mec-next-event-details i:before, .mec-single-fluent-wrap .mec-single-event-location i, .mec-single-fluent-wrap .mec-single-event-organizer dd.mec-organizer-description:before, .mec-single-fluent-wrap .mec-single-event-additional-organizers dd.mec-organizer-description:before, .mec-single-fluent-wrap .mec-event-schedule-content dl dt.mec-schedule-time:before, .mec-single-fluent-wrap .mec-event-schedule-content dl dt.mec-schedule-time:before,  .mec-single-fluent-wrap .mec-single-event-bar>div i, .mec-single-fluent-wrap .mec-single-event-category a, .mec-fluent-wrap .mec-daily-view-events-left-side .mec-daily-view-events-item>span.mec-time, .mec-fluent-wrap .mec-daily-view-events-left-side .mec-daily-view-events-item>span.mec-time-end, .mec-fluent-wrap .mec-calendar.mec-calendar-daily .mec-calendar-d-table.mec-date-labels-container span, .mec-fluent-wrap .mec-calendar .mec-week-events-container dl>span, .mec-fluent-current-time-text, .mec-fluent-wrap.mec-timetable-wrap .mec-cell .mec-time, .mec-fluent-wrap.mec-skin-masonry-container .mec-events-masonry-cats a:hover, .mec-fluent-wrap.mec-skin-masonry-container .mec-events-masonry-cats a.mec-masonry-cat-selected, .mec-fluent-wrap .mec-date-details i:before, .mec-fluent-wrap .mec-event-location i:before, .mec-fluent-wrap .mec-event-carousel-type2 .owl-next i, .mec-fluent-wrap .mec-event-carousel-type2 .owl-prev i, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-next i, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-prev i, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-next, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-prev, .mec-fluent-wrap .mec-date-wrap i, .mec-fluent-wrap .mec-calendar.mec-yearly-calendar .mec-calendar-table-head dl dt:first-letter, .mec-event-sharing-wrap .mec-event-sharing li:hover a, .mec-fluent-wrap .mec-agenda-event>i, .mec-fluent-wrap .mec-totalcal-box .nice-select:after, .mec-fluent-wrap .mec-totalcal-box .mec-totalcal-view span, .mec-fluent-wrap .mec-totalcal-box input, .mec-fluent-wrap .mec-totalcal-box select, .mec-fluent-wrap .mec-totalcal-box .nice-select, .mec-fluent-wrap .mec-totalcal-box .nice-select .list li, .mec-fluent-wrap .mec-text-input-search i, .mec-fluent-wrap .mec-event-location i, .mec-fluent-wrap .mec-event-article .mec-event-title a:hover, .mec-fluent-wrap .mec-date-details:before, .mec-fluent-wrap .mec-time-details:before, .mec-fluent-wrap .mec-venue-details:before, .mec-fluent-wrap .mec-price-details i:before, .mec-fluent-wrap .mec-available-tickets-details i:before, .mec-fluent-wrap .mec-booking-button, .mec-single-fluent-wrap .mec-local-time-details li:first-child:before, .mec-single-fluent-wrap .mec-local-time-details li:nth-of-type(2):before, .mec-single-fluent-wrap .mec-local-time-details li:last-child:before {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]::-webkit-input-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]::-moz-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]:-ms-input-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]:-moz-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt.mec-table-nullday, .mec-single-fluent-body .lity-content input::-moz-placeholder, .mec-single-fluent-body .lity-content textarea::-moz-placeholder, .mec-single-fluent-wrap input::-moz-placeholder, .mec-single-fluent-wrap textarea::-moz-placeholder {
		color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	.mec-fluent-wrap .mec-event-sharing-wrap .mec-event-social-icon i,
	.mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt:hover {
		color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.8)'; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-event-sharing-wrap:hover li a, .mec-single-fluent-wrap .mec-booking-button, .mec-single-fluent-wrap .mec-booking-button, .mec-single-fluent-wrap .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button {
		color: #fff !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button:hover {
		color: <?php echo $fluent_main_color; ?> !important;
	}

	/* BORDER COLOR */
	/* list view */
	.mec-fluent-wrap.mec-skin-list-wrap .mec-event-article {
		border-top-color: <?php echo $fluent_main_color; ?> !important;
		border-left-color: <?php echo $fluent_main_color; ?> !important;
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}
	/* list view */
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-event-article .mec-event-content {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
		border-left-color: <?php echo $fluent_main_color; ?> !important;
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-event-article .mec-event-image {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
		border-left-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top, .mec-single-fluent-wrap .mec-next-event-details a:hover, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .nice-select, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="date"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="email"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="number"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="password"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="tel"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="text"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking select, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking textarea, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .StripeElement, .mec-single-fluent-wrap .mec-events-meta-group-booking .nice-select, .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="date"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="email"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="number"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="password"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="tel"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="text"], .mec-single-fluent-wrap .mec-events-meta-group-booking select, .mec-single-fluent-wrap .mec-events-meta-group-booking textarea, .mec-single-fluent-wrap .mec-events-meta-group-booking .StripeElement, .mec-single-fluent-wrap .mec-event-schedule-content dl:before, .mec-single-fluent-wrap .mec-event-schedule-content dl:first-of-type:after, .mec-single-fluent-wrap .mec-event-schedule-content dl, .mec-single-fluent-wrap .mec-event-export-module.mec-frontbox .mec-event-exporting .mec-export-details ul li a:hover, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic .mec-calendar-table-head, .mec-fluent-wrap .mec-yearly-view-wrap .mec-year-container, .mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda-wrap, .mec-fluent-wrap .mec-totalcal-box .mec-totalcal-view span, .mec-fluent-wrap .mec-totalcal-box input, .mec-fluent-wrap .mec-totalcal-box select, .mec-fluent-wrap .mec-totalcal-box .nice-select, .mec-fluent-wrap .mec-load-more-button:hover, .mec-fluent-wrap .mec-booking-button, .mec-fluent-wrap .mec-skin-monthly-view-month-navigator-container, .mec-fluent-wrap .mec-calendar-a-month, .mec-fluent-wrap .mec-yearly-title-sec, .mec-fluent-wrap .mec-filter-content, .mec-fluent-wrap i.mec-filter-icon, .mec-fluent-wrap .mec-text-input-search input[type="search"], .mec-fluent-wrap .mec-event-sharing-wrap .mec-event-sharing, .mec-fluent-wrap .mec-load-month, .mec-fluent-wrap .mec-load-year{
		border-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-current-time-first, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-load-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt:first-of-type {
		border-left-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-current-time-last, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt:last-of-type {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-more-events, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl:last-of-type dt, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view .mec-fluent-more-views-content:before, .mec-fluent-wrap .mec-filter-content:before {
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-event-sharing-wrap .mec-event-sharing:before {
		border-color: <?php echo $fluent_main_color; ?> transparent transparent transparent  !important;
	}
	.mec-fluent-wrap.mec-timetable-wrap .mec-cell, .mec-fluent-wrap .mec-event-meta {
		border-left-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap .mec-daily-view-events-left-side, .mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-calendar-sec {
		border-right-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap.mec-events-agenda-container .mec-agenda-events-wrap {
		border-left-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap dt .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap.mec-skin-slider-container .mec-slider-t1 .mec-slider-t1-content, .mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda {
		border-top-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
		border-bottom-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar.mec-calendar-daily .mec-calendar-d-table, .mec-fluent-wrap.mec-timetable-wrap .mec-ttt2-title, .mec-fluent-wrap.mec-timetable-wrap .mec-cell, .mec-fluent-wrap.mec-skin-countdown-container .mec-date-wrap {
		border-bottom-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap .mec-skin-daily-view-events-container, .mec-single-fluent-wrap .mec-event-social li.mec-event-social-icon a, .mec-single-fluent-wrap .mec-marker-infowindow-wp .mec-marker-infowindow-count, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="radio"]:before, .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="radio"]:before, .mec-single-fluent-wrap .mec-events-meta-group-countdown .countdown-w .block-w, .mec-single-fluent-wrap .mec-events-meta-group-booking .nice-select, .mec-single-fluent-wrap .mec-next-event-details a, .mec-single-fluent-wrap .mec-events-meta-group, .mec-single-fluent-wrap .mec-events-meta-group-tags a, .mec-single-fluent-wrap .mec-event-export-module.mec-frontbox .mec-event-exporting .mec-export-details ul li a, .mec-fluent-wrap .mec-skin-weekly-view-events-container, .mec-fluent-wrap .mec-calendar .mec-week-events-container dt, .mec-fluent-wrap.mec-timetable-wrap .mec-timetable-t2-wrap, .mec-fluent-wrap .mec-event-countdown li, .mec-fluent-wrap .mec-event-countdown-style3 .mec-event-countdown li, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt, .mec-fluent-wrap .mec-yearly-view-wrap .mec-agenda-event, .mec-fluent-wrap .mec-yearly-view-wrap .mec-calendar.mec-yearly-calendar, .mec-fluent-wrap .mec-load-more-button, .mec-fluent-wrap .mec-totalcal-box .nice-select .list, .mec-fluent-wrap .mec-filter-content i {
		border-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box .nice-select:after {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}

	/* BOXSHADOW */
	.mec-fluent-wrap .mec-totalcal-box .nice-select .list, .mec-single-fluent-wrap .mec-booking-button, .mec-single-fluent-wrap .mec-events-meta-group-tags a:hover, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking button, .mec-fluent-wrap.mec-single-fluent-wrap .mec-events-meta-group-booking button {
		box-shadow: 0 2px 5px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.2)'; ?> !important;
	}
	.mec-fluent-wrap .mec-booking-button:hover, .mec-fluent-wrap .mec-load-more-button:hover, .mec-fluent-bg-wrap .mec-fluent-wrap article .mec-booking-button:hover {
		box-shadow: 0 4px 10px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.2)'; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-event-article, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .nice-select .list, .mec-single-fluent-wrap .mec-events-meta-group-booking .nice-select .list, .mec-single-fluent-wrap .mec-next-event-details a:hover, .mec-single-fluent-wrap .mec-event-export-module.mec-frontbox .mec-event-exporting .mec-export-details ul li a:hover {
		box-shadow: 0 4px 10px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.7)'; ?> !important;
	}
	.mec-fluent-wrap .mec-skin-daily-view-events-container, .mec-fluent-wrap.mec-timetable-wrap .mec-timetable-t2-wrap, .mec-fluent-wrap .mec-calendar-side .mec-calendar-table, .mec-fluent-wrap .mec-yearly-view-wrap .mec-year-container, .mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda-wrap {
		box-shadow: 0 5px 33px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-agenda-event {
		box-shadow: 0 1px 6px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.05)'; ?> !important;
	}

	/* BACKGROUND */
	/* filter options */
	.nicescroll-cursors, .mec-single-fluent-wrap .mec-related-event-post .mec-date-wrap, .mec-single-fluent-wrap .mec-events-meta-group, .mec-single-fluent-wrap .mec-next-event-details a:hover, .mec-single-fluent-wrap .mec-events-meta-group-tags a:hover, .mec-single-fluent-wrap .mec-event-export-module.mec-frontbox .mec-event-exporting .mec-export-details ul li a:hover, .mec-fluent-wrap dt .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap.mec-skin-countdown-container .mec-date-wrap, .mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-agenda-sec, .mec-fluent-wrap .mec-calendar-daily .mec-calendar-day-events, .mec-fluent-wrap .mec-totalcal-box .nice-select .list li:hover, .mec-fluent-wrap .mec-totalcal-box .nice-select .list li.focus {
		background-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap h5.mec-more-events-header, .mec-fluent-current-time, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking button, .mec-fluent-wrap.mec-single-fluent-wrap .mec-events-meta-group-booking button {
		background-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-agenda-events-wrap {
		background-color: transparent !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-date-wrap i, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-date-wrap i, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-date-wrap i {
		background-color: #fff !important;
	}
	.mec-single-fluent-body .lity-content .mec-events-meta-group-booking button:hover, .mec-fluent-wrap.mec-single-fluent-wrap .mec-events-meta-group-booking button:hover {
		background-color: #000 !important;
	}
	.mec-single-fluent-body .lity-content .mec-events-meta-group-booking, .mec-single-fluent-wrap .mec-events-meta-group-booking, .mec-fluent-wrap.mec-skin-cover-container .mec-date-wrap i, .mec-fluent-wrap.mec-skin-carousel-container .mec-event-carousel-type2 .owl-next:hover, .mec-fluent-wrap.mec-skin-carousel-container .mec-event-carousel-type2 .owl-prev:hover, .mec-fluent-wrap.mec-skin-slider-container .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-next:hover, .mec-fluent-wrap.mec-skin-slider-container .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-prev:hover, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top dt.active, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view span.mec-fluent-more-views-icon.active, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected, .mec-fluent-wrap i.mec-filter-icon.active, .mec-fluent-wrap .mec-filter-content i {
		background-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	<?php
}
// Bold Color - Second
$fluent_bold_color = '#00acf8';
if (isset($styling['fluent_bold_color']) && $styling['fluent_bold_color']) {
	$fluent_bold_color = $styling['fluent_bold_color'];
	?>
	/* MAIN BOLD COLOR - SECOND COLOR */
	.mec-fluent-wrap .mec-daily-view-events-left-side h5.mec-daily-today-title span:first-child, .mec-single-fluent-wrap .mec-events-meta-group-tags .mec-event-footer a:hover, .mec-single-fluent-wrap .mec-event-social li.mec-event-social-icon a:hover, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="radio"]:checked:after, .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="radio"]:checked:after, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .mec-book-ticket-container>h4 .mec-ticket-name, .mec-single-fluent-wrap .mec-events-meta-group-booking .mec-book-ticket-container>h4 .mec-ticket-name, .mec-single-fluent-wrap .mec-events-meta-group-booking .nice-select, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .nice-select, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="date"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="email"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="number"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="password"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="tel"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking input[type="text"], .mec-single-fluent-body .lity-content .mec-events-meta-group-booking select, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking textarea, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .StripeElement, .mec-single-fluent-wrap .mec-events-meta-group-booking .nice-select, .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="date"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="email"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="number"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="password"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="tel"], .mec-single-fluent-wrap .mec-events-meta-group-booking input[type="text"], .mec-single-fluent-wrap .mec-events-meta-group-booking select, .mec-single-fluent-wrap .mec-events-meta-group-booking textarea, .mec-single-fluent-wrap .mec-events-meta-group-booking .StripeElement, .mec-single-fluent-wrap .mec-events-meta-group-countdown, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking h5 span, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking label, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking label.wn-checkbox-label, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .mec-event-ticket-name, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .mec-event-ticket-available, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .mec-book-reg-field-p p, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .mec-gateway-comment, .mec-single-fluent-wrap .mec-events-meta-group-booking h5 span, .mec-single-fluent-wrap .mec-events-meta-group-booking label, .mec-single-fluent-wrap .mec-events-meta-group-booking label.wn-checkbox-label, .mec-single-fluent-wrap .mec-events-meta-group-booking .mec-event-ticket-name, .mec-single-fluent-wrap .mec-events-meta-group-booking .mec-event-ticket-available, .mec-single-fluent-wrap .mec-events-meta-group-booking .mec-book-reg-field-p p, .mec-single-fluent-wrap .mec-events-meta-group-booking .mec-gateway-comment, .mec-single-fluent-wrap .mec-related-event-post .mec-date-wrap span.mec-event-day-num, .mec-single-fluent-wrap .mec-single-event-category a:hover,.mec-fluent-wrap.mec-skin-available-spot-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-cover-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-countdown-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-carousel-container .event-carousel-type2-head .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-slider-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-masonry-container .mec-masonry .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top dt.active, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic .mec-calendar-table-head dt.active, .mec-fluent-wrap .mec-color, .mec-fluent-wrap a:hover, .mec-wrap .mec-color-hover:hover, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected, .mec-fluent-wrap .mec-booking-button, .mec-fluent-wrap .mec-load-more-button, .mec-fluent-wrap .mec-load-month i, .mec-fluent-wrap .mec-load-year i, .mec-fluent-wrap i.mec-filter-icon, .mec-fluent-wrap .mec-filter-content i, .mec-fluent-wrap .mec-event-sharing-wrap>li:first-of-type i,	.mec-fluent-wrap .mec-available-tickets-details span.mec-available-tickets-number {
		color: <?php echo $fluent_bold_color; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-sharing-wrap>li:first-of-type i, .mec-single-fluent-wrap .mec-single-event-bar .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 span.mec-event-day-num, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-event-sharing-wrap:hover li:first-child a {
		color: #fff !important;
	}

	/* BORDER BOLD COLOR - SECOND COLOR */
	.mec-fluent-wrap.mec-skin-carousel-container .mec-owl-theme .owl-dots .owl-dot.active span, .mec-single-fluent-wrap .mec-event-social li.mec-event-social-icon a:hover, .mec-fluent-wrap .mec-load-month, .mec-fluent-wrap .mec-load-year, .mec-single-fluent-body .lity-content .mec-events-meta-group-booking .mec-book-available-tickets-details>.mec-book-available-tickets-details-header, .mec-single-fluent-wrap .mec-events-meta-group-booking .mec-book-available-tickets-details>.mec-book-available-tickets-details-header {
		border-color: <?php echo $fluent_bold_color; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar .mec-daily-view-day.mec-has-event:after, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button, .mec-fluent-wrap .mec-event-cover-fluent-type2 .mec-event-sharing-wrap:hover>li:first-child, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-date-wrap, .mec-fluent-wrap.mec-skin-carousel-container .mec-owl-theme .owl-dots .owl-dot.active span {
		background-color: <?php echo $fluent_bold_color; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button:hover {
		background-color: #fff !important;
	}
	<?php
}

// Background Hover Color
$fluent_bg_hover_color = '#ebf9ff';
if (isset($styling['fluent_bg_hover_color']) && $styling['fluent_bg_hover_color']) {
	$fluent_bg_hover_color = $styling['fluent_bg_hover_color'];
	?>
	/* BACKGROUND COLOR */
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-calendar.mec-yearly-calendar .mec-has-event:after, .mec-fluent-wrap .mec-load-more-button:hover, .mec-fluent-wrap .mec-load-month:hover, .mec-fluent-wrap .mec-load-year:hover, .mec-fluent-wrap .mec-booking-button:hover {
		background-color: <?php echo $fluent_bg_hover_color; ?> !important;
	}
	<?php
}

// Background Color
$fluent_bg_color = '#ade7ff';
if (isset($styling['fluent_bg_color']) && $styling['fluent_bg_color']) {
	$fluent_bg_color = $styling['fluent_bg_color'];
	?>
	/* BACKGROUND COLOR */
	.mec-fluent-wrap {
		background-color: <?php echo $fluent_bg_color; ?> !important;
	}
	<?php
}

// Second Background Color
$fluent_second_bg_color = '#d6eef9';
if (isset($styling['fluent_second_bg_color']) && $styling['fluent_second_bg_color']) {
	$fluent_second_bg_color = $styling['fluent_second_bg_color'];
	?>
	/* BACKGROUND COLOR */
	.mec-fluent-wrap.mec-skin-masonry-container .mec-masonry .mec-date-wrap, .mec-single-fluent-wrap .mec-event-social li.mec-event-social-icon a:hover, .mec-fluent-wrap .mec-filter-content {
		background-color: <?php echo $fluent_second_bg_color; ?> !important;
	}

	.mec-fluent-wrap .mec-filter-content:after {
		border-bottom-color:<?php echo $fluent_second_bg_color; ?> !important;
	}
	<?php
}

// get render content
$out = '';
$out = ob_get_clean();

// minify css
$out = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $out);
$out = str_replace(array("\r\n", "\r", "\n", "\t", '    '), '', $out);

update_option('mec_gfont', $fonts_url);
update_option('mec_dyncss', $out);