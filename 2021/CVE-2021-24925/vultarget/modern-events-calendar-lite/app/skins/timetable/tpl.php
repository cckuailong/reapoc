<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_timetable $this */

do_action('mec_start_skin', $this->id);
do_action('mec_timetable_skin_head');

if($this->style == 'clean') include MEC::import('app.skins.timetable.clean', true, true);
elseif($this->style == 'classic') include MEC::import('app.skins.timetable.classic', true, true);
else include MEC::import('app.skins.timetable.modern', true, true);