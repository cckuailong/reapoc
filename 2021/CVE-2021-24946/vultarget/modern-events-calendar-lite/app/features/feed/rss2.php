<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_feed $this */

header('Content-Type: '.feed_content_type('rss2').'; charset='.get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
do_action('rss_tag_pre', 'rss2');
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:mec="http://webnus.net/rss/mec/"
    xmlns:media="http://search.yahoo.com/mrss/"
	<?php do_action('rss2_ns'); ?>
    <?php do_action('mec_rss2_ns'); ?>>
<channel>
	<title><?php wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url'); ?></link>
	<description><?php bloginfo_rss("description"); ?></description>
	<lastBuildDate><?php echo $this->main->mysql2date('D, d M Y H:i:s O', get_lastpostmodified('GMT'), wp_timezone()); ?></lastBuildDate>
	<language><?php bloginfo_rss('language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters('rss_update_period', 'hourly'); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters('rss_update_frequency', 1); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
    
    <?php foreach($this->events as $date=>$events): foreach($events as $event): ?>
    <?php
        $timezone = $this->main->get_timezone($event);
        $tz = ($timezone ? new DateTimeZone($timezone) : NULL);

        $cost = (isset($event->data->meta) and isset($event->data->meta['mec_cost']) and trim($event->data->meta['mec_cost'])) ? $event->data->meta['mec_cost'] : '';
        if(isset($event->date) and isset($event->date['start']) and isset($event->date['start']['timestamp'])) $cost = MEC_feature_occurrences::param($event->ID, $event->date['start']['timestamp'], 'cost', $cost);

        $location_id = $this->main->get_master_location_id($event);
    ?>
    <item>
		<title><?php echo $this->feed->title($event->ID); ?></title>
		<link><?php echo $this->main->get_event_date_permalink($event, $event->date['start']['date']); ?></link>
        
        <?php if(get_comments_number($event->ID) or comments_open($event->ID)): ?>
		<comments><?php $this->feed->comments_link_feed($event->ID); ?></comments>
        <?php endif; ?>

        <pubDate><?php echo $this->main->mysql2date('D, d M Y H:i:s O', $event->date['start']['date'].' '.$event->data->time['start'], $tz); ?></pubDate>
		<dc:creator><![CDATA[<?php $this->feed->author($event->data->post->post_author); ?>]]></dc:creator>

		<guid isPermaLink="false"><?php the_guid($event->ID); ?></guid>

        <description><![CDATA[<?php echo $this->feed->excerpt($event->ID); ?>]]></description>

        <?php if(!get_option('rss_use_excerpt')): $content = $this->feed->content($event->ID, 'rss2'); ?>
        <content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
        <?php endif; ?>

        <?php if(get_comments_number($event->ID) or comments_open($event->ID)): ?>
		<wfw:commentRss><?php echo esc_url(get_post_comments_feed_link($event->ID, 'rss2')); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number($event->ID); ?></slash:comments>
        <?php endif; ?>

        <?php if(has_post_thumbnail($event->ID) and (!isset($this->settings['include_image_in_feed']) or (isset($this->settings['include_image_in_feed']) and !$this->settings['include_image_in_feed']))): $thumbnail_ID = get_post_thumbnail_id($event->ID); $thumbnail = wp_get_attachment_image_src($thumbnail_ID, 'large'); ?>
        <media:content medium="image" url="<?php echo $thumbnail[0]; ?>" width="<?php echo $thumbnail[1]; ?>" height="<?php echo $thumbnail[2]; ?>" />
        <?php endif; ?>

        <mec:startDate><?php echo $date; ?></mec:startDate>
        <?php if(isset($event->data) and isset($event->data->time) and isset($event->data->time['start'])): ?>
        <mec:startHour><?php echo $event->data->time['start']; ?></mec:startHour>
        <?php endif; ?>

        <mec:endDate><?php echo $this->main->get_end_date_by_occurrence($event->ID, $date); ?></mec:endDate>
        <?php if(isset($event->data) and isset($event->data->time) and isset($event->data->time['end'])): ?>
        <mec:endHour><?php echo $event->data->time['end']; ?></mec:endHour>
        <?php endif; ?>

        <?php if($location_id and $location = $this->main->get_location_data($location_id) and count($location)): ?>
        <mec:location><?php echo $location['address']; ?></mec:location>
        <?php endif; ?>

        <?php if($cost): ?>
        <mec:cost><?php echo (is_numeric($cost) ? $this->main->render_price($cost, $event->ID) : $cost); ?></mec:cost>
        <?php endif; ?>

        <?php if(isset($event->data->categories) and is_array($event->data->categories) and count($event->data->categories)): ?>
        <mec:category><?php $categories = ''; foreach($event->data->categories as $category) $categories .= $category['name'].', '; echo trim($categories, ', ') ?></mec:category>
        <?php endif; ?>

        <?php $this->feed->enclosure_rss($event->ID); ?>
        <?php do_action('rss2_item'); ?>
        <?php do_action('mec_rss2_item'); ?>
	</item>
    <?php endforeach; endforeach; ?>

</channel>
</rss>