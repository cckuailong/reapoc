<?php
/** no direct access **/
defined('MECEXEC') or die();

// MEC Skins
$skins = $this->main->get_skins();

// MEC Events
$events = $this->main->get_events();

// MEC Settings
$settings = $this->main->get_settings();
$wizard_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
$main_page = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '';
?>
<div id="mec_popup_shortcode" class="lity-hide">
    <div class="mec-steps-container">
        <img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/mec-logo.svg'; ?>" />
        <ul>
            <li class="mec-step mec-step-1"><span>1</span></li>
            <li class="mec-step mec-step-2"><span>2</span></li>
            <li class="mec-step mec-step-3"><span>3</span></li>
            <li class="mec-step mec-step-4"><span>4</span></li>
            <li class="mec-step mec-step-5"><span>5</span></li>
            <li class="mec-step mec-step-6"><span>6</span></li>
        </ul>
    </div>
    <div class="mec-steps-panel">
        <div id="mec_popup_shortcode_form">
            <div class="mec-steps-content-container">
                <div class="mec-steps-header">
                    <div class="mec-steps-header-userinfo">
                        <?php $user = wp_get_current_user(); ?>
                        <span class="mec-steps-header-img"><img src="<?php echo esc_url( get_avatar_url( $user->ID ) ); ?>" /></span>
                        <span class="mec-steps-header-name"><?php echo $user->display_name ; ?></span>
                        <span class="mec-steps-header-add-text"><?php esc_html_e('Adding a Shortcode...', 'modern-events-calendar-lite') ?></span>
                    </div>
                    <div class="mec-steps-header-settings">
                        <a href="<?php echo admin_url( 'admin.php?page=MEC-settings' ); ?>"><i class="mec-sl-settings"></i><?php esc_html_e('Settings', 'modern-events-calendar-lite'); ?></a>
                    </div>
                </div>
                <div class="mec-steps-content mec-steps-content-1">
                    <?php wp_nonce_field('mec_shortcode_popup', '_mecnonce'); ?>
                    <input type="text" name="shortcode[name]" placeholder="<?php esc_attr_e('Shortcode Name', 'modern-events-calendar-lite'); ?>" id="mec_shortcode_name">
                    <p class="popup-sh-name-required"><?php esc_html_e('Shortcode name is required', 'modern-events-calendar-lite'); ?></p>
                </div>
                <div class="mec-steps-content mec-steps-content-2">
                    <ul>
                        <?php foreach($skins as $skin=>$name): ?>
                        <li>
                            <label>
                                <div class="mec-step-popup-skin-img">
                                    <img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins'; ?>/<?php echo str_replace('_view', '', $skin); ?>.svg" />
                                </div>
                                <div class="mec-step-popup-skin-text">
                                    <?php echo $name; ?>
                                    <input type="radio" class="mec-skins" name="shortcode[skin]" value="<?php echo $skin; ?>">
                                </div>
                            </label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="mec-steps-content mec-steps-content-3">
                    <div class="mec-styles-wrapper">
                        <div class="mec-skin-styles mec-styles-list">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/list-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/list-minimal.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="minimal">
                                <div><?php _e('Minimal', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/list-modern.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="modern">
                                <div><?php _e('Modern', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/list-standard.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="standard">
                                <div><?php _e('Standard', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/list-toggle.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="accordion">
                                <div><?php _e('Accordion', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/list-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-grid">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-clean.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="clean">
                                <div><?php _e('Clean', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-minimal.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="minimal">
                                <div><?php _e('Minimal', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-modern.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="modern">
                                <div><?php _e('Modern', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-simple.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="simple">
                                <div><?php _e('Simple', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-colorful.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="colorful">
                                <div><?php _e('Colorful', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-novel.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="novel">
                                <div><?php _e('Novel', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/grid-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-agenda">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/agenda-clean.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="clean">
                                <div><?php _e('Clean', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/agenda-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-full_calendar">
                            <h3><?php _e('Monthly Style', 'modern-events-calendar-lite'); ?></h3>
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-clean.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="clean">
                                <div><?php _e('Clean', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-novel.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="novel">
                                <div><?php _e('Novel', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-simple.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="simple">
                                <div><?php _e('Simple', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-yearly_view">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/yearly-modern.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="modern">
                                <div><?php _e('Modern', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/yearly-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-monthly_view">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-clean.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="clean">
                                <div><?php _e('Clean', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-modern.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="modern">
                                <div><?php _e('Modern', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-novel.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="novel">
                                <div><?php _e('Novel', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-simple.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="simple">
                                <div><?php _e('Simple', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/monthly-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-map">
                        </div>
                        <div class="mec-skin-styles mec-styles-daily_view">
                        <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/daily-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/daily-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-weekly_view">
                        <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/weekly-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/weekly-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-timetable">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/timetable-modern.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="modern">
                                <div><?php _e('Modern', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/timetable-clean.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="clean">
                                <div><?php _e('Clean', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/timetable-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-masonry">
                        <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/masonry-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/masonry-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-cover">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/cover-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/cover-clean.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="clean">
                                <div><?php _e('Clean', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/cover-modern.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="modern">
                                <div><?php _e('Modern', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/cover-fluent-type1.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent-type1">
                                <div><?php _e('Fluent Type 1', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/cover-fluent-type2.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent-type2">
                                <div><?php _e('Fluent Type 2', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/cover-fluent-type3.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent-type3">
                                <div><?php _e('Fluent Type 3', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/cover-fluent-type4.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent-type4">
                                <div><?php _e('Fluent Type 4', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-countdown">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/countdown-type1.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="style1">
                                <div><?php _e('Style 1', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/countdown-type2.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="style2">
                                <div><?php _e('Style 2', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/countdown-type3.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="style3">
                                <div><?php _e('Style 3', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/countdown-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-available_spot">
                        <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/available-spot-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/available-spot-fluent-type1.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent-type1">
                                <div><?php _e('Fluent Type 1', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/available-spot-fluent-type2.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent-type2">
                                <div><?php _e('Fluent Type 2', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-carousel">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/carousel-type1.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="type1">
                                <div><?php _e('Type 1', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/carousel-type2.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="type2">
                                <div><?php _e('Type 2', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/carousel-type3.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="type3">
                                <div><?php _e('Type 3', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/carousel-type4.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="type4">
                                <div><?php _e('Type 4', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/carousel-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-slider">
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/slider-type1.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="t1">
                                <div><?php _e('Type 1', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/slider-type2.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="t2">
                                <div><?php _e('Type 2', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/slider-type3.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="t3">
                                <div><?php _e('Type 3', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/slider-type4.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="t4">
                                <div><?php _e('Type 4', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/slider-type5.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="t5">
                                <div><?php _e('Type 5', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/slider-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-timeline">
                        </div>
                        <div class="mec-skin-styles mec-styles-tile">
                        <?php if ( is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' ) ) { ?>
                            <label class="active">
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/tile-classic.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="classic">
                                <div><?php _e('Classic', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <label>
                                <span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/skins/popup/tile-fluent.jpg'; ?>" /></span>
                                <input type="radio" name="shortcode[style]" class="mec-styles" value="fluent">
                                <div><?php _e('Fluent', 'modern-events-calendar-lite'); ?></div>
                            </label>
                            <?php } ?>
                        </div>
                        <div class="mec-skin-styles mec-styles-custom">
                        <?php
                        $args = array(
                            'post_type'   => 'mec_designer',
                            'post_status' => 'publish',
                            'order'       => 'DESC',
                        );
                        $styles = new WP_Query($args);
                        ?>
                        <h3 for="mec_shortcode_custom_style"><?php _e('Select Style', 'modern-events-calendar-lite'); ?></h3>
                        <select class="mec-col-4 wn-mec-select" name="shortcode[custom_style]" id="mec_skin_custom_style">
                            <?php foreach($styles->get_posts() as $post): ?>
                            <option value="<?php echo esc_attr($post->ID) ?>" <?php isset($sk_options_custom['style']) ? selected($sk_options_custom['style'], $post->ID, true) : ''; ?> ><?php echo esc_html($post->post_title); ?></option>';
                            <?php endforeach; ?>
                        </select>
                        </div>
                    </div>
                </div>
                <div class="mec-steps-content mec-steps-content-4">
                    <div class="mec-multiple-skin-options">
                        <h3><?php _e('Single Event Display Method', 'modern-events-calendar-lite'); ?></h3>
                        <div>
                            <label>
                                <input type="radio" name="shortcode[sed]" value="0" checked>
                                <?php _e('Current Window', 'modern-events-calendar-lite'); ?>
                            </label>
                            <label class="active">
                                <input type="radio" name="shortcode[sed]" value="new" checked>
                                <?php _e('New Window', 'modern-events-calendar-lite'); ?>
                            </label>
                            <label>
                                <input type="radio" name="shortcode[sed]" value="m1">
                                <?php _e('Modal Popup', 'modern-events-calendar-lite'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="mec-single-skin-options">
                        <h3 for="mec_shortcode_event_id"><?php _e('Select Event', 'modern-events-calendar-lite'); ?></h3>
                        <select name="shortcode[event]" id="mec_shortcode_event_id" class="mec_shortcode_event_id wn-mec-select-popup">
                            <?php foreach($events as $event): ?>
                            <option value="<?php echo $event->ID; ?>" <?php if(isset($sk_options_cover['event_id']) and $sk_options_cover['event_id'] == $event->ID) echo 'selected="selected"'; ?>><?php echo $event->post_title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="wns-be-group-tab mec-steps-content mec-steps-content-5">
                    <div class="mec-switcher" id="mec_show_past_events_wrapper">
                        <div>
                            <label for="mec_show_past_events"><?php _e('Include Expired Events', 'modern-events-calendar-lite'); ?></label>
                            <p class="description"><?php _e('You have ability to include past/expired events if you like so it will show upcoming and expired events based on start date that you selected.', 'modern-events-calendar-lite'); ?></p>
                        </div>
                        <div>
                            <input type="hidden" name="shortcode[show_past_events]" value="0" />
                            <input type="checkbox" name="shortcode[show_past_events]" class="mec-checkbox-toggle" id="mec_show_past_events" value="1" checked>
                            <label for="mec_show_past_events"></label>
                        </div>
                    </div>
                    <div class="mec-switcher" id="mec_show_only_past_events_wrapper">
                        <div>
                            <label for="mec_show_only_past_events"><?php _e('Show Only Expired Events', 'modern-events-calendar-lite'); ?></label>
                            <p class="description" style="color: red;"><?php echo sprintf(__('It shows %s expired/past events.', 'modern-events-calendar-lite'), '<strong>'.__('only', 'modern-events-calendar-lite').'</strong>'); ?></p>
                        </div>
                        <div>
                            <input type="hidden" name="shortcode[show_only_past_events]" value="0" />
                            <input type="checkbox" name="shortcode[show_only_past_events]" class="mec-checkbox-toggle" id="mec_show_only_past_events" value="1">
                            <label for="mec_show_only_past_events"></label>
                        </div>
                    </div>
                    <div class="mec-switcher" id="mec_show_only_ongoing_events_wrapper">
                        <div>
                            <label for="mec_show_only_ongoing_events"><?php _e('Show Only Ongoing Events', 'modern-events-calendar-lite'); ?></label>
                            <p class="description"><?php _e('It shows only ongoing events on List and Grid skins.', 'modern-events-calendar-lite'); ?></p>
                        </div>
                        <div>
                            <input type="hidden" name="shortcode[show_only_ongoing_events]" value="0" />
                            <input type="checkbox" name="shortcode[show_only_ongoing_events]" class="mec-checkbox-toggle" id="mec_show_only_ongoing_events" value="1">
                            <label for="mec_show_only_ongoing_events"></label>
                        </div>
                    </div>
                </div>
                <div class="mec-steps-content mec-steps-content-6">
                    <div class="mec-steps-6-loading"><div class="mec-loader"></div></div>
                    <div class="mec-steps-6-results">
                        <div class="mec-popup-shortcode">
                            <h3><?php _e('Your Shortcode', 'modern-events-calendar-lite'); ?></h3>
                            <div class="mec-popup-shortcode-code">
                                <code></code>
                                <button type="button" class="mec-button-copy"><?php _e('Copy', 'modern-events-calendar-lite'); ?></button>
                            </div>
                        </div>
                        <p class="description"><?php _e('Put this shortcode into your desired page.', 'modern-events-calendar-lite'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mec-next-previous-buttons">
            <button class="mec-button-prev"><?php _e('Prev', 'modern-events-calendar-lite'); ?><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-prev-icon.svg'; ?>" /></button>
            <button class="mec-button-next"><?php _e('Next', 'modern-events-calendar-lite'); ?><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-next-icon.svg'; ?>" /></button>
            <button class="mec-button-new"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-new-shortcode-plus.svg'; ?>" /><?php _e('New Shortcode', 'modern-events-calendar-lite'); ?></button>
        </div>
    </div>
</div>
<?php if(!isset($settings['sh_as_popup']) || (isset($settings['sh_as_popup']) && $settings['sh_as_popup'] == '1') or $wizard_page == "MEC-wizard" ) : ?>

<?php if (is_plugin_active( 'mec-fluent-layouts/mec-fluent-layouts.php' )) { $fluent = 'fluent-view-activated'; } else { $fluent = 'deactivate'; } ?>
<script type="text/javascript">
jQuery(document).ready(function()
{
    var redirect = true;
    var current_step;
    var current_skin;
    var current_style;

    var $shortcode_wrap = jQuery("#mec_popup_shortcode");
    var $sh_prev = $shortcode_wrap.find('.mec-button-prev');
    var $sh_next = $shortcode_wrap.find('.mec-button-next');
    var $sh_new = $shortcode_wrap.find('.mec-button-new');
    var $sh_copy = $shortcode_wrap.find('.mec-button-copy');
    var $sh_steps = $shortcode_wrap.find('.mec-step');
    var $sh_steps_content = $shortcode_wrap.find('.mec-steps-content');
    var $sh_skins = $shortcode_wrap.find('.mec-skins');
    var $sh_name = $shortcode_wrap.find('#mec_shortcode_name');
    var $sh_main_container = $shortcode_wrap.find('.mec-steps-panel');

    if(jQuery('.mec_shortcode_event_id').length > 0) jQuery('.mec_shortcode_event_id').niceSelect();
    jQuery(".mec-steps-content.mec-steps-content-2 ul").niceScroll({
        autohidemode: false,
        cursorcolor:"#C7EBFB",
        cursorwidth: "4px",
        cursorborder: "none",
        railpadding: { top: 17, right: 0, left: 0, bottom: 0 },
        scrollbarid: 'mec-select-skin-popup-scrollbar'
    });
    jQuery(".mec-steps-content.mec-steps-content-3 .mec-skin-styles").niceScroll({
        autohidemode: false,
        cursorcolor:"#C7EBFB",
        cursorwidth: "4px",
        cursorborder: "none",
        railpadding: { top: 17, right: 0, left: 0, bottom: 0 },
        scrollbarid: 'mec-select-type-popup-scrollbar'
    });

    // Add Shortcode Button
    jQuery('.mec-wizard-open-popup.add-shortcode,.wrap .page-title-action').on('click', function(e)
    {
        e.preventDefault();

        if(jQuery(".mec-wizard-open-popup.add-shortcode").length > 0 ) 
        {
            jQuery(".mec-wizard-open-popup.add-shortcode").addClass("active")
            jQuery(".mec-wizard-open-popup.add-event").removeClass("active")
            jQuery(".mec-wizard-open-popup.mec-settings").removeClass("active")
            jQuery(".mec-wizard-starter-video a").removeClass("active")
        }

        // Open Lightbox
        lity('#mec_popup_shortcode');

        // Do Step
        mec_shortcode_step(1, 'next');
    });

    // Lightbox Open
    jQuery(document).on('lity:open', function(event, instance)
    {
        <?php if ($main_page == "mec_calendars") { ?>
            jQuery('.lity').removeClass('mec-add-event-popup');
            jQuery('.lity').addClass('mec-add-shortcode-popup');
        <?php } ?>

        if ( jQuery(".mec-wizard-open-popup.add-shortcode").hasClass("active") ) {
            jQuery('.lity').addClass('mec-add-shortcode-popup');
        }

        jQuery('body').css('overflow', 'hidden');
        jQuery('.lity-wrap').removeAttr('data-lity-close');
    });

    // Lightbox Close
    jQuery(document).on('lity:close', function(event, instance)
    {
        <?php if ( $wizard_page != "MEC-wizard" ) { ?>
            if(redirect) window.location.href = "<?php echo admin_url('post-new.php?post_type='.$post_type); ?>";
        <?php } ?>
        jQuery("#mec-select-type-popup-scrollbar .nicescroll-cursors").css('z-index', '-1');
        jQuery(".mec-steps-content.mec-steps-content-3 .mec-skin-styles").getNiceScroll().hide();
        jQuery("#mec-select-skin-popup-scrollbar .nicescroll-cursors").css('z-index', '-1');
        jQuery(".mec-steps-content.mec-steps-content-2 ul").getNiceScroll().hide();
    });

    // Previous
    $sh_prev.on('click', function()
    {
        var new_step = parseInt(current_step)-1;
        if(new_step <= 0) new_step = 1;

        mec_shortcode_step(new_step, 'prev');
    });

    // Next
    $sh_next.on('click', function()
    {
        var new_step = parseInt(current_step)+1;
        if(new_step > 7) new_step = 7;

        mec_shortcode_step(new_step, 'next');
    });

    // New
    $sh_new.on('click', function()
    {
        $sh_name.val('');
        mec_shortcode_step(1, 'next');
    });

    // Copy
    $sh_copy.on('click', function()
    {
        var $temp = jQuery("<input>");
        jQuery("body").append($temp);

        $temp.val(jQuery('.mec-popup-shortcode code').text()).select();

        document.execCommand("copy");
        $temp.remove();
    });

    // Skin Changed
    $sh_skins.on('change', function(e)
    {
        e.preventDefault();
        var skin = jQuery(this).val();

        jQuery('.mec-skin-styles').hide();
        jQuery('.mec-styles-'+skin).show();

        if(skin === 'list' || skin === 'grid') jQuery('#mec_show_only_ongoing_events_wrapper').show();
        else jQuery('#mec_show_only_ongoing_events_wrapper').hide();

        if(skin === 'map') jQuery('#mec_show_only_past_events_wrapper').hide();
        else jQuery('#mec_show_only_past_events_wrapper').show();
        
    });

    // on Submit of Shortcode Name
    $sh_name.keyup(function(e)
    {
        if(e.keyCode === 13)
        {
            mec_shortcode_step(2, 'next');
        }
    });

    // Step 2 - Select skin
    jQuery('.mec-steps-content.mec-steps-content-2 ul li:first-of-type').addClass('active');
    jQuery('.mec-steps-content.mec-steps-content-2 ul li').on('click', function (e) {
        e.preventDefault();
        jQuery('.mec-steps-content.mec-steps-content-2 ul li .mec-skins').prop('checked', false);
        jQuery('.mec-steps-content.mec-steps-content-2 ul li').removeClass('active');
        jQuery(this).addClass('active');
        jQuery(this).find('.mec-skins').prop('checked', true).trigger('change'); 
    });

    // Step 3 - Select skin type
    jQuery('.mec-skin-styles label').on('click', function (e) {
        e.preventDefault();
        jQuery('.mec-skin-styles label input').prop('checked', false);
        jQuery('.mec-skin-styles label').removeClass('active');
        jQuery(this).addClass('active');
        jQuery(this).find('input').prop('checked', true).trigger('change');
    });

    // Step 4 - change target link
    jQuery('.mec-steps-content.mec-steps-content-4 label:first-of-type').addClass('active');
    jQuery('.mec-steps-content.mec-steps-content-4 label input').on('change', function () {
        jQuery('.mec-steps-content.mec-steps-content-4 label').removeClass('active');
        jQuery(this).parent().addClass('active');
    });

    // Do Step
    function mec_shortcode_step(step, type)
    {
        current_skin = jQuery('.mec-skins:checked').val();

        if ( '<?php echo $fluent;?>' == 'fluent-view-activated' ) { 
                if(step === 3 && (
                current_skin === 'map' ||
                current_skin === 'timeline'
            ))
            {
                if(type === 'next') step = 4;
                else step = 2;
            }
        } else { // Skip Style Step
                if(step === 3 && (
                current_skin === 'map' ||
                current_skin === 'daily_view' ||
                current_skin === 'weekly_view' ||
                current_skin === 'masonry' ||
                current_skin === 'available_spot' ||
                current_skin === 'timeline' ||
                current_skin === 'tile'
            ))
            {
                if(type === 'next') step = 4;
                else step = 2;
            }
        }

        // Skip Single Event Display Step
        if(step === 4 && (
            current_skin === 'map' ||
            current_skin === 'carousel' ||
            current_skin === 'slider'
        ))
        {
            if(type === 'next') step = 5;
            else
            {
                if(current_skin === 'map') step = 2;
                else step = 3;
            }
        }

        // Skip Dates Step
        if(step === 5 && (
            current_skin === 'available_spot' ||
            current_skin === 'countdown' ||
            current_skin === 'cover'
        ))
        {
            if(type === 'next') step = 6;
            else step = 4;
        }

        // Validation
        if(step === 2)
        {
            var name = $sh_name.val();
            if(name === '')
            {
                $sh_name.addClass('mec-required').focus();
                jQuery('.popup-sh-name-required').show();
                return false;
            }

            if(!current_skin) jQuery('.mec-skins:first').attr('checked', true).trigger('change');
        }
        else if(step === 3)
        {
            current_style = jQuery('.mec-styles-'+current_skin+' .mec-styles:checked').val();
            if(!current_style) jQuery('.mec-styles-'+current_skin+' .mec-styles:first').attr('checked', true);
        }

        current_step = step;

        // Buttons
        $sh_prev.show();
        $sh_next.show();

        if(step === 1)
        {
            $sh_prev.hide();
            $sh_new.hide();
        }
        else if(step === 6)
        {
            $sh_prev.hide();
            $sh_next.hide();
        }

        // Disable Redirection
        redirect = (step !== 6);

        // Steps Bar
        $sh_steps.removeClass('mec-step-passed');
        for(var i = 1; i <= step; i++) jQuery('.mec-step-'+i).addClass('mec-step-passed');

        // Content
        $sh_steps_content.hide();
        $sh_steps_content.removeClass('mec-steps-content-active');
        jQuery('.mec-steps-content-'+step).addClass('mec-steps-content-active').show();
        jQuery('.mec-steps-content-container').removeClass('mec-steps-content-1 mec-steps-content-2 mec-steps-content-3 mec-steps-content-4 mec-steps-content-5 mec-steps-content-6').addClass('mec-steps-content-'+step);
        

        // Save Shortcode
        if(step === 6) return mec_shortcode_save();

        if(step === 4 && (
            current_skin === 'cover' ||
            current_skin === 'countdown' ||
            current_skin === 'available_spot'
        ))
        {
            jQuery('.mec-steps-content-4 .mec-single-skin-options').show();
            jQuery('.mec-steps-content-4 .mec-multiple-skin-options').hide();
        }
        else
        {
            jQuery('.mec-steps-content-4 .mec-single-skin-options').hide();
            jQuery('.mec-steps-content-4 .mec-multiple-skin-options').show();
        }

        if ( step === 2 ) {
            jQuery(".mec-steps-content-container.mec-steps-content-2 .mec-steps-content.mec-steps-content-2 ul").getNiceScroll().resize();
            jQuery("#mec-select-skin-popup-scrollbar .nicescroll-cursors").css('z-index', '9999');
            jQuery(".mec-steps-content.mec-steps-content-3 .mec-skin-styles").getNiceScroll().hide();
            jQuery("#mec-select-type-popup-scrollbar .nicescroll-cursors").css('z-index', '9');
        } else if ( step === 3 ){
            jQuery(".mec-steps-content-container.mec-steps-content-3 .mec-steps-content.mec-steps-content-3 .mec-skin-styles").getNiceScroll().resize();
            jQuery("#mec-select-type-popup-scrollbar .nicescroll-cursors").css('z-index', '9999');
            jQuery(".mec-steps-content.mec-steps-content-2 ul").getNiceScroll().hide();
            jQuery("#mec-select-skin-popup-scrollbar .nicescroll-cursors").css('z-index', '9');
        } else {
            jQuery(".nicescroll-cursors").css('z-index', '9');
        }

    }


    function mec_shortcode_save()
    {
        // Show Loading
        jQuery(".mec-steps-6-loading").show();
        jQuery(".mec-steps-6-results").hide();

        var form = jQuery("#mec_popup_shortcode_form :input").serialize();
        jQuery.ajax(
        {
            type: "POST",
            url: ajaxurl,
            data: "action=mec_popup_shortcode&"+form,
            dataType: "json",
            success: function(data)
            {
                if(data.success)
                {
                    jQuery(".mec-popup-shortcode code").html('[MEC id="'+data.id+'"]');

                    jQuery(".mec-steps-6-loading").hide();

                    jQuery(".mec-steps-6-results").show();
                    $sh_new.show();
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
            }
        });
    }
});
</script>
<?php endif; ?>