<?php
/**
 * Display for Event Custom Post Types
 */
if (!defined('ABSPATH')) {
  die('-1');
}

global $post;
global $wp;
global $ecwd_options;
global $wp_query;
$json_ld = array(
  '@context' => 'https://schema.org',
  '@type' => 'Event',
  'name' => $post->post_title,
  'description' => strip_tags(preg_replace('/\\r|\\n|(\[(.*)\])/i', "", $post->post_content)),
  'image' => get_the_post_thumbnail_url($post->ID),
  'eventAttendanceMode' => 'https://schema.org/MixedEventAttendanceMode',
  'eventStatus' => 'https://schema.org/EventScheduled'
);
if ( !empty($args['organizers']) ) {
  foreach ( $args['organizers'] as $organizer ) {
    $data['performer'][] = array(
      '@type' => 'Person',
      'name' => $organizer['name'],
      'description' => '',
      'telephone' => '',
      'sameAs' => '',
    );
  }
}

$post_id = $post->ID;
$meta = get_post_meta($post_id);

$date_format = 'Y-m-d';
$time_format = 'H:i';
$ecwd_social_icons = false;
if (isset($ecwd_options['date_format']) && $ecwd_options['date_format'] != '') {
  $date_format = $ecwd_options['date_format'];
}
if (isset($ecwd_options['time_format']) && $ecwd_options['time_format'] != '') {
  $time_format = $ecwd_options['time_format'];
}
$time_format .= (isset($ecwd_options['time_type']) ? ' ' . $ecwd_options['time_type'] : '');
if (isset($ecwd_options['time_type']) && $ecwd_options['time_type'] != '') {
  $time_format = str_replace('H', 'g', $time_format);
  $time_format = str_replace('h', 'g', $time_format);
}
if (isset($ecwd_options['social_icons']) && $ecwd_options['social_icons'] != '') {
  $ecwd_social_icons = $ecwd_options['social_icons'];
}
// Load up all post meta data


$ecwd_event = $post;
$ecwd_event_metas = get_post_meta($ecwd_event->ID, '', true);
if(isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'][0])) {
  $ecwd_event_date_from = $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'][0];
  $ecwd_event_date_to = $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'][0];

  $is_default_dates = false;
}else{
  $today = ECWD::ecwd_date('Y-m-d H:i');

  $ecwd_event_date_from = ECWD::ecwd_date('Y/m/d H:i', strtotime($today . "+1 days"));
  $ecwd_event_date_to = ECWD::ecwd_date('Y/m/d H:i', strtotime($ecwd_event_date_from . "+1 hour"));

  $is_default_dates = true;
}
if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'])) {
  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'] = array(0 => '');
}
if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'])) {
  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'] = array(0 => '');
}
if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'])) {
  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'] = array(0 => '');
}
if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'])) {
  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'] = array(0 => '');
}
if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'])) {
  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'] = array(0 => '');
}

$permalink = get_the_permalink($ecwd_event->ID);
$this_event = $events[$ecwd_event->ID] = new ECWD_Event($ecwd_event->ID, '', $ecwd_event->post_title, $ecwd_event->post_content, $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'][0], $permalink, $ecwd_event, '', $ecwd_event_metas);
$d = new ECWD_Display('');
if (isset($_GET['eventDate']) || isset($wp_query->query_vars['eventDate'])) {
  $start_time = ECWD::ecwd_date('H:i', strtotime($ecwd_event_date_from));
  $end_time = ECWD::ecwd_date('H:i', strtotime($ecwd_event_date_to));


  $ecwd_event_date_from = isset($_GET['eventDate']) ? sanitize_text_field($_GET['eventDate']) : $wp_query->query_vars['eventDate'];
  $eventdayslong = $d->dateDiff($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'][0]);
  $ecwd_event_date_to = ECWD::ecwd_date('Y-m-d', strtotime((ECWD::ecwd_date("Y-m-d", (strtotime($ecwd_event_date_from))) . " +" . ($eventdayslong) . " days")));
  $ecwd_event_date_from .= ' ' . $start_time;
  $ecwd_event_date_to .= ' ' . $end_time;
}


$ecwd_event_location = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'][0] : '';
$ecwd_event_latlong = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'][0] : '';
//$ecwd_event_zoom = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_map_zoom'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_map_zoom'][0] : '';
$ecwd_event_show_map = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_show_map'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_show_map'][0] : 0;
if ($ecwd_event_show_map == '') {
  $ecwd_event_show_map = 1;
}

$ecwd_event_organizers = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_organizers'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_organizers'][0] : '';


$ecwd_event_url = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'][0] : '';
$ecwd_event_url = ECWD::add_http($ecwd_event_url);
$ecwd_event_video = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_video'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_video'][0] : '';
$ecwd_all_day_event = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_all_day_event'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_all_day_event'][0] : 0;
$venue = '';
$venue_permalink = '';
$venue_post_id = isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_venue'][0]) ? $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_venue'][0] : 0;
if ($venue_post_id) {
  $venue_post = get_post($venue_post_id);
  if ($venue_post) {
    $venue = $venue_post->post_title;
    $venue_permalink = get_permalink($venue_post->ID);
  }
}

$venue_meta_template = '<div class="%s"><span>%s:</span><span>%s</span></div>';
$venue_meta_link_template = '<div class="%s"><span>%s:</span><a href="%s">%s</a></div>';

if (is_numeric($venue_post_id)) {
  $ecwd_venue_phone = esc_html(get_post_meta($venue_post_id, 'ecwd_venue_meta_phone', true));
  $ecwd_venue_website = esc_url(get_post_meta($venue_post_id, 'ecwd_venue_meta_website', true));
  $ecwd_event_zoom = esc_html(get_post_meta($venue_post_id, 'ecwd_map_zoom', true));
  $ecwd_venue_website = ECWD::add_http($ecwd_venue_website);
} else {
  $ecwd_venue_phone = "";
  $ecwd_venue_website = "";
  $ecwd_event_zoom = "";
}

if (!$ecwd_event_zoom) {
  $ecwd_event_zoom = (isset($ecwd_options['gmap_zoom'])) ? $ecwd_options['gmap_zoom'] : 17;
}

$organizers = array();
$ecwd_event_organizers = maybe_unserialize($ecwd_event_organizers);
if (is_array($ecwd_event_organizers) || is_object($ecwd_event_organizers)) {
  foreach ($ecwd_event_organizers as $ecwd_event_organizer) {
    $temp = get_post($ecwd_event_organizer, ARRAY_A);
    if ($temp !== null) {
      $organizers[] = $temp;
    }
  }
}

$category_and_tags = false;

if (isset($ecwd_options['category_and_tags']) && $ecwd_options['category_and_tags'] != '') {
  $category_and_tags = $ecwd_options['category_and_tags'];
}
$args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all');
$event_tags = wp_get_post_terms($post->ID, 'ecwd_event_tag', $args);
$event_categories = wp_get_post_terms($post->ID, 'ecwd_event_category', $args);

$default_calendar = get_option('ecwd_default_calendar');
$calendars_id = get_post_meta($post_id, 'ecwd_event_calendars', true);

$back_link = null;
$back_link_text = __('View Calendar', 'event-calendar-wd');
if (!empty($calendars_id)) {
  if ($default_calendar !== false && in_array($default_calendar, $calendars_id)) {
    $back_link = get_permalink($default_calendar);
  } else {
    $back_link = get_permalink(array_shift($calendars_id));
  }
} else {
  if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
    if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == parse_url(site_url(), PHP_URL_HOST)) {
      $back_link = $_SERVER['HTTP_REFERER'];
      $back_link_text = __('Back', 'event-calendar-wd');
    }
  }
}
?>
<div class="ecwd-event ecwd-single-event">
  <?php  if ( ! post_password_required( $post ) ) { ?>
	  <?php if ($back_link !== null) { ?>
		<a id="ecwd_back_link" href="<?php echo $back_link; ?>"><?php echo $back_link_text; ?></a>
	  <?php } ?>
	  <div class="event-detalis">
		  <div class="ecwd-event-details">
		   <div class="event-detalis-date">
        <label class="ecwd-event-date-info" title="<?php _e('Date', 'event-calendar-wd'); ?>"></label>
        <span class="ecwd-event-date">
        <?php
          $json_ld['startDate'] = $ecwd_event_date_from;
          $json_ld['endDate'] = $ecwd_event_date_to;
          echo ECWD::get_ecwd_event_date_view($ecwd_event_date_from, $ecwd_event_date_to, $ecwd_all_day_event);
        ?>
        </span>
		  </div>
		  <?php
		  if (isset($ecwd_options['show_repeat_rate']) && !$is_default_dates) {
			$repeat_rate_text = $d->get_repeat_rate($post_id, '', $date_format);
			if ($repeat_rate_text != '') { ?>
			  <div class="ecwd_repeat_rate_text">
				  <span><?php echo $d->get_repeat_rate($post_id, '', $date_format); ?></span>
			  </div>
			<?php }
		  }

		  if ($ecwd_event_url) { ?>
			<div class="ecwd-url">
			  <a href="<?php echo $ecwd_event_url; ?>" target="_blank">
				<label class="ecwd-event-url-info" title="<?php _e('Url', 'event-calendar-wd'); ?>"></label>
				<?php echo $ecwd_event_url; ?>
			  </a>
			</div>
		  <?php }
		  if (count($organizers) > 0) { ?>
		    <div class="event-detalis-org">
			    <label class="ecwd-event-org-info" title="<?php _e('Organizers', 'event-calendar-wd'); ?>"></label>
			    <?php
          if (count($organizers) > 1) {
			      foreach ($organizers as $organizer) {
              $json_ld['performer'][] = array(
                "@type" => "Person",
                'name' => $organizer['post_title'],
                'sameAs' => get_permalink($organizer['ID'])
              );
			        ?>
              <span><a href="<?php echo get_permalink($organizer['ID']) ?>"><?php echo $organizer['post_title'] ?></a></span>
            <?php
			      }
			    }
          else {
				    $organizer = $organizers[0];
            ?>
              <span><a href="<?php echo get_permalink($organizer['ID']) ?>"><?php echo $organizer['post_title'] ?></a></span>
            <?php
            $organizer_phone = get_post_meta($organizer['ID'], 'ecwd_organizer_meta_phone', true);
            $organizer_website = get_post_meta($organizer['ID'], 'ecwd_organizer_meta_website', true);
            $organizer_website = ECWD::add_http($organizer_website);
            $json_ld['performer'] = array(
              "@type" => "Person",
              'name' => $organizer['post_title'],
              'telephone' => $organizer_phone,
              'sameAs' => get_permalink($organizer['ID'])
            );
            if (!empty($organizer_phone)) { ?>
              <div class="ecwd_organizer_phone">
                <span><?php _e('Phone', 'event-calendar-wd'); ?>:</span>
                <span><?php echo $organizer_phone; ?></span>
              </div>
            <?php }
            if (!empty($organizer_website)) { ?>
              <div class="ecwd_organizer_website">
                <span><?php _e('Website', 'event-calendar-wd'); ?>:</span>
                <a href="<?php echo esc_url($organizer_website); ?>"><?php echo esc_html($organizer_website); ?></a>
              </div>
              <?php
            }
          }
        ?>
      </div>
		  <?php } ?>
      <div class="event-venue">
      <?php if ($venue_post_id) { ?>
          <label class="ecwd-venue-info" title="<?php _e('Venue', 'event-calendar-wd'); ?>"></label>
          <span>
            <?php
            if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
              $venue_permalink = add_query_arg('venue', '1', $venue_permalink);
            }
            ?>
            <a href="<?php echo $venue_permalink ?>"><?php echo $venue; ?></a>
          </span>
          <?php
          if (!empty($ecwd_venue_phone)) {
            echo sprintf($venue_meta_template, "ecwd_venue_phone", __('Phone', 'event-calendar-wd'), $ecwd_venue_phone);
          }
          if (!empty($ecwd_venue_website)) {
            echo sprintf($venue_meta_link_template, "ecwd_venue_website", __('Website', 'event-calendar-wd'), $ecwd_venue_website, $ecwd_venue_website);
          }
          if (!empty($ecwd_event_location)) {
          ?>
            <div class="address">
              <span><?php _e('Address:', 'event-calendar-wd'); ?></span>
              <span><?php echo $ecwd_event_location; ?></span>
            </div>
          <?php
          }
          $json_ld['location'] = array(
            '@type' => 'Place',
            'name' => $venue,
            'address' => array(
              '@type' => 'PostalAddress',
              'telephone' => $ecwd_venue_phone,
              'streetAddress' => $ecwd_event_location
            ),
          );
        }
        else if ($ecwd_event_location) { ?>
          <label class="ecwd-venue-info" title="<?php _e('Location', 'event-calendar-wd'); ?>"></label>
          <span class="address">
            <?php echo $ecwd_event_location; ?>
          </span>
          <?php
            $json_ld['location'] = array(
              '@type' => 'Place',
              'name' => 'Address',
              'address' => array(
              '@type' => 'PostalAddress',
              'streetAddress' => $ecwd_event_location
            ),
          );
        } ?>
		  </div>
		  <?php do_action('ecwd_view_ext'); ?>
		  </div>
	  </div>
	  <?php if ($ecwd_social_icons) { ?>
		<div class="ecwd-social">
		  <span class="share-links">
			  <a href="https://twitter.com/intent/tweet?text=<?php echo get_permalink($post_id) ?>"
				 class="ecwd-twitter"
				 target="_blank" data-original-title="Tweet It">
				  <span class="visuallyhidden">Twitter</span></a>
			  <a href="https://www.facebook.com/sharer.php?u=<?php echo get_permalink($post_id) ?>"
				 class="ecwd-facebook"
				 target="_blank" data-original-title="Share on Facebook">
				  <span class="visuallyhidden">Facebook</span></a>
		  </span>
		</div>
	  <?php }

	  if ($ecwd_event_show_map == 1 && $ecwd_event_latlong) {
		$map_events = array();
		$url_for_google_map = '';
		$map_events[0]['latlong'] = explode(',', $ecwd_event_latlong);
		if ($ecwd_event_location != '') {
		  $map_events[0]['location'] = $ecwd_event_location;

		  $url_for_google_map .= 'https://www.google.com/maps/place/';
		  $url_for_google_map .= urlencode($ecwd_event_location) . '/';
		  $url_for_google_map .= '@' . $ecwd_event_latlong;;
		}

		$map_events[0]['zoom'] = $ecwd_event_zoom;
		$map_events[0]['infow'] = '<div class="ecwd_map_event">';
		$map_events[0]['infow'] .= '<span class="location">' . $ecwd_event_location . '</span>';
		$map_events[0]['infow'] .= '</div>';
		$map_events[0]['infow'] .= '<div class="event-detalis-date">
		                              <label class="ecwd-event-date-info" title="' . __('Date', 'event-calendar-wd') . '"></label>
		                              <span class="ecwd-event-date">'. ECWD::get_ecwd_event_date_view($ecwd_event_date_from, $ecwd_event_date_to, $ecwd_all_day_event) . '</span>';
		$map_events[0]['infow'] .= '</div>';
		$map_events[0]['google_map_url'] = $url_for_google_map;
    $map_events[0]['name'] = $post->post_title;

		$markers = json_encode($map_events);
		?>
		<div class="ecwd-show-map">
		  <div class="ecwd_map_div">
		  </div>
		  <textarea class="hidden ecwd_markers" style="display: none;"><?php echo $markers; ?></textarea>
		</div>
	  <?php } ?>
	  <div class="clear"></div>

	  <?php if (!empty($ecwd_event_video)) { ?>
		<div class="ecwd-event-video">
		  <?php
		  if (strpos($ecwd_event_video, 'youtube') > 0) {
			parse_str(parse_url($ecwd_event_video, PHP_URL_QUERY), $video_array_of_vars);
			if (isset($video_array_of_vars['v']) && $video_array_of_vars['v']) {
			  ?>
			  <object data="https://www.youtube.com/v/<?php echo $video_array_of_vars['v'] ?>"
					  type="application/x-shockwave-flash" width="400" height="300">
				<param name="src"
					   value="https://www.youtube.com/v/<?php echo $video_array_of_vars['v'] ?>"/>
			  </object>
			  <?php
			}
		  } elseif (strpos($ecwd_event_video, 'vimeo') > 0) {
			$videoID = explode('/', $ecwd_event_video);
			$videoID = $videoID[count($videoID) - 1];
			if ($videoID) {
			  ?>
			  <iframe
				src="https://player.vimeo.com/video/<?php echo $videoID; ?>?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff"
				width="" height="" frameborder="0" webkitAllowFullScreen mozallowfullscreen
				allowFullScreen></iframe>
			  <?php
			}
		  }
		  ?>
		</div>
	  <?php }  ?>
	  <div class="entry-content-event"><?php echo wpautop($post->post_content); ?></div>
	  <!-- Categories and tags -->
	  <?php  if ($category_and_tags == 1) { ?>
		<div class="event_cageory_and_tags">

		  <?php if (!empty($event_categories)) { ?>
			<ul class="event_categories">
			  <?php foreach ($event_categories as $category) {
				$metas = get_option("ecwd_event_category_$category->term_id");
				?>
				<li class="event_category event-details-title">
				  <?php if ($metas['color']) { ?>
					<span class="event-metalabel" style="background:<?php echo $metas['color']; ?>"></span>
					<span class="event_catgeory_name">
					  <a href="<?php echo get_category_link($category); ?>"
						 style="color:<?php echo $metas['color']; ?>">
						<?php echo $category->name; ?>
					  </a></span>
				  <?php } else { ?>
					<span class="event_catgeory_name">
					  <a href="<?php echo get_category_link($category); ?>">
						<?php echo $category->name; ?>
					  </a>
					</span>
				  <?php } ?>
				</li>
			  <?php } ?>
			</ul>
		  <?php }

		  if (!empty($event_tags)) { ?>
			<ul class="event_tags">
			  <?php foreach ($event_tags as $tag) { ?>
				<li class="event_tag">
				  <span class="event_tag_name">
					<a href="<?php echo get_tag_link($tag); ?>">#<?php echo $tag->name; ?> </a>
				  </span>
				</li>
				<?php
			  } ?>
			</ul>
		  <?php } ?>
		</div>
	  <?php } ?>
	  <!--	END Categories and tags -->

	  <?php
	  do_action('ecwd_tickets_events_single_meta', '', $ecwd_event_date_to);
	  do_action('ecwd_single_event_page_links', $ecwd_event, $ecwd_event_metas, array('start_date' => $ecwd_event_date_from, 'end_date' => $ecwd_event_date_to));
	  ?>
	  <?php
	  if (!isset($ecwd_options['related_events']) || $ecwd_options['related_events'] == 1) {
		$post_cats = wp_get_post_terms($post_id, ECWD_PLUGIN_PREFIX . '_event_category');
		$cat_ids = wp_list_pluck($post_cats, 'term_id');
		$post_tags = wp_get_post_terms($post_id, ECWD_PLUGIN_PREFIX . '_event_tag');
		$tag_ids = wp_list_pluck($post_tags, 'term_id');
		$events = array();
		$today = ECWD::ecwd_date('Y-m-d');

		$args = array(
		  'numberposts' => -1,
		  'post_type' => ECWD_PLUGIN_PREFIX . '_event',
		  'tax_query' => array(
			array(
			  'taxonomy' => ECWD_PLUGIN_PREFIX . '_event_category',
			  'terms' => $cat_ids,
			  'field' => 'term_id',
			)
		  ),
		  'orderby' => 'meta_value',
		  'order' => 'ASC'
		);
		$ecwd_events_by_cats = get_posts($args);
		$args = array(
		  'numberposts' => -1,
		  'post_type' => ECWD_PLUGIN_PREFIX . '_event',
		  'tax_query' => array(
			array(
			  'taxonomy' => ECWD_PLUGIN_PREFIX . '_event_tag',
			  'terms' => $tag_ids,
			  'field' => 'term_id',
			),
		  ),
		  'orderby' => 'meta_value',
		  'order' => 'ASC'
		);

		$ecwd_events_by_tags = get_posts($args);
		$ecwd_events = array_merge($ecwd_events_by_tags, $ecwd_events_by_cats);
		$ecwd_events = array_map("unserialize", array_unique(array_map("serialize", $ecwd_events)));
		wp_reset_postdata();
		wp_reset_query();

		foreach ($ecwd_events as $ecwd_event) {
		  if ($ecwd_event->ID != $post_id) {
			$term_metas = '';
			$categories = get_the_terms($ecwd_event->ID, ECWD_PLUGIN_PREFIX . '_event_category');
			if (is_array($categories)) {
			  foreach ($categories as $category) {
				$term_metas = get_option("ecwd_event_category_$category->term_id");
				$term_metas['id'] = $category->term_id;
				$term_metas['name'] = $category->name;
				$term_metas['slug'] = $category->slug;
			  }
			}
			$ecwd_event_metas = get_post_meta($ecwd_event->ID, '', true);
			$ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'] = array(0 => '');
			if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'])) {
			  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'] = array(0 => '');
			}
			if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'])) {
			  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'] = array(0 => '');
			}
			if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'])) {
			  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'] = array(0 => '');
			}
			if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'])) {
			  $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'] = array(0 => '');
			}

			$permalink = get_permalink($ecwd_event->ID);
			$events[$ecwd_event->ID] = new ECWD_Event($ecwd_event->ID, 0, $ecwd_event->post_title, $ecwd_event->post_content, $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'][0], $permalink, $ecwd_event, $term_metas, $ecwd_event_metas);
		  }
		}
		$d = new ECWD_Display(0, '', '', $today);
		$start_date = ECWD::ecwd_date('Y-m-d');
		$end_date = ECWD::ecwd_date('Y-m-d', strtotime("+1 year", strtotime($start_date)));
		$events = $d->get_event_days($events, 1, $start_date, $end_date);
		$events = $d->events_unique($events);
		do_action('ecwd_show_related_events', $events, true);
	  }
	} else {
	 echo get_the_password_form();
}  ?>
</div>
<script id="ecwd_script_handler" type="text/javascript">
   if (typeof ecwd_js_init_call == "object") {
        ecwd_js_init_call = new ecwd_js_init();
        ecwd_js_init_call.showMap();
   }
</script>
<script id="ecwd_ld_json" type="application/ld+json"><?php echo json_encode($json_ld); ?></script>