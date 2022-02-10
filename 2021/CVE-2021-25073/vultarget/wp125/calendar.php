<?php

include '../../../wp-blog-header.php';
header("Content-type: text/plain");

echo "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Matt Harzewski/WP125//NONSGML v1.0//EN\n\n";

$adtable_name = $wpdb->prefix . "wp125_ads";
$ads = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status != '0' AND end_date != '00/00/0000' ORDER BY id DESC", OBJECT);

if ($ads) {
	foreach ($ads as $ads) {
		echo "BEGIN:VEVENT\n";
		echo "DTSTART;TZOFFSETTO=".date("O", strtotime($ads->end_date)).":".date("Ymd\This", strtotime($ads->end_date))."\n";
		echo "DTEND;TZOFFSETTO=".date("O", strtotime($ads->end_date)).":".date("Ymd\This", strtotime($ads->end_date))."\n";
		echo "SUMMARY: Ad \"".$ads->name."\" ends.\n";
		echo "BEGIN:VALARM\n";
		echo "TRIGGER:-PT30M\n";
		echo "ACTION:DISPLAY\n";
		echo "DESCRIPTION: Ad \"".$ads->name."\" ends.\n";
		echo "END:VALARM\n";
		echo "END:VEVENT\n\n";
	}
}

echo "END:VCALENDAR";

?>