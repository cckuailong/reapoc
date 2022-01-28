<?php
/** no direct access **/
defined('MECEXEC') or die();

// MEC Settings
$settings = $this->get_settings();

// The module is disabled
if(!isset($settings['speakers_status']) or (isset($settings['speakers_status']) and !$settings['speakers_status'])) return;

// Event Speakers
$speakers = (isset($event->data->speakers) and is_array($event->data->speakers)) ? $event->data->speakers : array();

// No Speaker
if(!count($speakers)) return false;
?>
<div class="mec-speakers-details mec-frontbox" id="mec_speakers_details">
    <h3 class="mec-speakers mec-frontbox-title"><?php if (count($speakers) == 1) : echo $this->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite')); else: echo $this->m('taxonomy_speakers', __('Speakers', 'modern-events-calendar-lite')); endif; ?></h3>
    <ul>
        <?php foreach($speakers as $speaker): ?>
        <li>
            <div class="mec-speaker-avatar">
                <a class="mec-color-hover" href="#mec-speaker-info-<?php echo $event->ID.'-'.$speaker['id']; ?>">
                    <?php if(isset($speaker['thumbnail']) and trim($speaker['thumbnail'])): ?>
                        <?php if (class_exists('MEC_Fluent\Core\pluginBase\MecFluent') && (isset($settings['single_single_style']) and $settings['single_single_style'] == 'fluent')) { ?>
                            <img class="mec-border-color-hover" src="<?php echo esc_url(MEC_Fluent\Core\pluginBase\MecFluent::generateCustomThumbnailURL($speaker['thumbnail'], 60, 60, true)); ?>" alt="<?php echo $speaker['name']; ?>">
                        <?php } else { ?>
                            <img class="mec-border-color-hover" src="<?php echo $speaker['thumbnail']; ?>" alt="<?php echo $speaker['name']; ?>">
                        <?php } ?>
                    <?php endif; ?>
                    <span class="mec-speaker-name"><?php echo $speaker['name']; ?></span>
                    <?php if(isset($speaker['job_title']) and trim($speaker['job_title'])): ?>
                    <span class="mec-speaker-job-title"><?php echo $speaker['job_title']; ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="mec-speaker-info lity-hide mec-hourly-schedule-speaker-info" id="mec-speaker-info-<?php echo $event->ID.'-'.$speaker['id']; ?>">
                <!-- Speaker Thumbnail -->
                <?php if($thumbnail = trim(get_term_meta($speaker['id'], 'thumbnail', true))): ?>
                <div class="mec-hourly-schedule-speaker-thumbnail">
                    <img src="<?php echo $thumbnail; ?>" alt="<?php echo $speaker['name']; ?>">
                </div>
                <?php endif; ?>
                <div class="mec-hourly-schedule-speaker-details">
                    <!-- Speaker Name -->
                    <div class="mec-hourly-schedule-speaker-name">
                        <?php echo $speaker['name']; ?>
                    </div>
                    <!-- Speaker Job Title -->
                    <?php if($job_title = trim(get_term_meta($speaker['id'], 'job_title', true))): ?>
                    <div class="mec-hourly-schedule-speaker-job-title mec-color">
                        <?php echo $speaker['job_title']; ?>
                    </div>
                    <?php endif; ?>
                    <div class="mec-hourly-schedule-speaker-contact-information">
                        <!-- Speaker Telephone -->
                        <?php if($tel = trim(get_term_meta($speaker['id'], 'tel', true))): ?>
                            <a href="tel:<?php echo $tel; ?>"><i class="mec-fa-phone"></i></a>
                        <?php endif; ?>
                        <!-- Speaker Email -->
                        <?php if($email = trim(get_term_meta($speaker['id'], 'email', true))): ?>
                            <a href="mailto:<?php echo $email; ?>" target="_blank"><i class="mec-fa-envelope"></i></a>
                        <?php endif; ?>
                        <!-- Speaker Website page -->
                        <?php if($website = trim(get_term_meta($speaker['id'], 'website', true))): ?>
                        <a href="<?php echo $website; ?>" target="_blank"><i class="mec-fa-external-link-square"></i></a>
                        <?php endif; ?>
                        <!-- Speaker Facebook page -->
                        <?php if($facebook = trim(get_term_meta($speaker['id'], 'facebook', true))): ?>
                        <a href="<?php echo $facebook; ?>" target="_blank"><i class="mec-fa-facebook"></i></a>
                        <?php endif; ?>
                        <!-- Speaker Twitter -->
                        <?php if($twitter = trim(get_term_meta($speaker['id'], 'twitter', true))): ?>
                        <a href="<?php echo $twitter; ?>" target="_blank"><i class="mec-fa-twitter"></i></a>
                        <?php endif; ?>
                        <!-- Speaker Instagram -->
                        <?php if($instagram = trim(get_term_meta($speaker['id'], 'instagram', true))): ?>
                        <a href="<?php echo $instagram; ?>" target="_blank"><i class="mec-fa-instagram"></i></a>
                        <?php endif; ?>
                        <!-- Speaker LinkedIn -->
                        <?php if($linkedin = trim(get_term_meta($speaker['id'], 'linkedin', true))): ?>
                        <a href="<?php echo $linkedin; ?>" target="_blank"><i class="mec-fa-linkedin"></i></a>
                        <?php endif; ?>
                    </div>
                    <!-- Speaker Description -->
                    <?php $desc = trim(term_description($speaker['id'])); if(!empty($desc)): ?>
                    <div class="mec-hourly-schedule-speaker-description">
                        <?php echo $desc; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>