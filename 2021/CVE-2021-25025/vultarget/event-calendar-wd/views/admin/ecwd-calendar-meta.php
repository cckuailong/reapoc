<?php
/**
 * Display for Calendar post metas
 */
global $post;
$post_id = $post->ID;
// Load up all post meta data
$ecwd_calendar_description = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_description', true);
$ecwd_calendar_id = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_id', true);

$ecwd_calendar_default_year = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_default_year', true);
$ecwd_calendar_default_month = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_default_month', true);
$ecwd_calendar_theme = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_theme', true);
$ecwd_calendar_12_hour_time_format = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX.'_calendar_12_hour_time_format', true);
$ecwd_calendar_default_theme_color = isset($_GET['post']) ? 'calendar' : 'calendar_grey';
$ecwd_calendar_default_theme_color = (isset($ecwd_calendar_theme) && $ecwd_calendar_theme != '') ? $ecwd_calendar_theme : $ecwd_calendar_default_theme_color;

?>

<table class="form-table">
    <?php if($post->post_status!=='auto-draft'){?>
        <tr>
            <th></th>
            <td><a href="#" id="ecwd_preview_add_event"><?php _e('Preview', 'event-calendar-wd'); ?> / <?php _e('Add Event', 'event-calendar-wd'); ?></a></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Calendar Shortcode', 'event-calendar-wd'); ?></th>
            <td>
                <code>[ecwd id="<?php echo $post_id; ?>"]</code>
                <p class="description">
                    <?php _e('Copy and paste this shortcode to display this Calendar event on any post or page.', 'event-calendar-wd'); ?>
                </p>
                <div id="ecwd_preview_add_event_popup" class="hidden">

                    <div class="event_cal_add">
                        <div class='ecwd_popup_head'>
                            <div class='ecwd_popup_title'><h4>Add Event</h4></div>
                            <div class='event_cal_add_close'><i class='fa fa-times'></i></div>
                        </div>
                        <div class="ecwd_popup_body">                            
                            <span>Dates:</span>
                            <span class="ecwd-dates">

                            </span>
                            <span>Title:</span>
                                <input type="text" name="ecwd_event_name"  id="ecwd_event_name"/>
                                <span class="ecwd_error"></span>
                                <input type="hidden" id="ecwd_event_date_from" name="ecwd_event_date_from" />
                                <input type="hidden" id="ecwd_event_date_to" name="ecwd_event_date_to" />

                                <span id="add_event_to_cal" class="add_event_to_cal"> Save</span>
                                <span class="ecwd_notification"> </span>
                            </div>
                        </div>
                    <?php echo ecwd_print_calendar($post_id, 'full', array(), false, false, array(), true); ?>
                    </div>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e('Events', 'event-calendar-wd'); ?></th>
            <td>
              <div data-new_event_url="<?php echo get_admin_url() ?>post-new.php?post_type=ecwd_event&cal_id=<?php echo $post_id; ?>" class="ecwd-events">
                    <?php if ($events) { ?>
                        <?php foreach ($events as $event) { ?>
                            <span class="ecwd-calendar-event"> <span><?php echo esc_html($event->post_title); ?></span>
                            <input type="hidden" name="ecwd-calendar-event-id[]" value="<?php echo $event->ID; ?>"/>
                            <span class="ecwd-calendar-event-edit"><a href="post.php?post=<?php echo $event->ID; ?>&action=edit" target="_blank">e</a></span>
                            <span class="ecwd-calendar-event-delete">x</span>
                        </span>
                        <?php } ?>
                    <?php } ?>
                </div>

              <span class="ecwd-calendar-event-add">
                    <?php if ($excluded_events) { ?>

                <a class="ecwd_events_popup_button" data-new_event_url="<?php echo get_admin_url() ?>post-new.php?post_type=ecwd_event&cal_id=<?php echo $post_id; ?>" href="#ecwd_event_list_popup"><?php _e('Select Events from the list', 'event-calendar-wd'); ?></a>
                        <a class="ecwd_events_popup_button" data-new_event_url="<?php echo get_admin_url() ?>post-new.php?post_type=ecwd_event&cal_id=<?php echo $post_id; ?>" href="#ecwd_event_list_popup"><span class="add_event_plus">+</span></a>
                </span>
              <?php } else { ?>
                <a href="<?php echo get_admin_url() ?>post-new.php?post_type=ecwd_event&cal_id=<?php echo $post_id; ?>" target="_blank"><?php _e('Create more events', 'event-calendar-wd'); ?></a>
                <a href="<?php echo get_admin_url() ?>post-new.php?post_type=ecwd_event&cal_id=<?php echo $post_id; ?>" target="_blank"><span class="add_event_plus">+</span></a></span>
              <?php } ?>

                <div id="ecwd_add_event_to_calendar" class="ecwd_add_event_to_calendar" style="display: none">
                        <?php if ($excluded_events) { ?>
                            <?php foreach ($excluded_events as $event) { ?>
                                <span class="ecwd-calendar-event"><span><?php echo $event->post_title; ?></span>
                                <input type="hidden" class="ecwd-calendar-excluded-event-id"
                                       value="<?php echo $event->ID; ?>"/>
                                <span class="ecwd-calendar-event-edit hidden"><a href="post.php?post=<?php echo $event->ID; ?>&action=edit" target="_blank">e</a></span>
                                <span class="ecwd-calendar-event-add">+</span>
                            </span>
                            <?php } ?>
                        <?php } ?>
                    </div>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <th scope="row"><?php _e('Theme', 'event-calendar-wd'); ?></th>
        <td>
          <select name="ecwd_calendar_theme">
            <option value="<?php echo 'calendar'; ?>" <?php echo $ecwd_calendar_default_theme_color == "calendar" ? 'selected="selected"' : ''; ?> >Default Blue</option>
            <option value="<?php echo 'calendar_grey'; ?>" <?php echo !isset($ecwd_calendar_theme) || $ecwd_calendar_default_theme_color == "calendar_grey" ? 'selected="selected"' : ''; ?> >Grey</option>
            <option disabled><?php _e('Green (Jade)','event-calendar-wd')?></option>
            <option disabled><?php _e('Greenish-Blue (Teal)','event-calendar-wd')?></option>
            <option disabled><?php _e('Red (Crimson)','event-calendar-wd')?></option>
            <option disabled><?php _e('Orange (Gamboge)','event-calendar-wd')?></option>
            <option disabled><?php _e('Saddle Brown','event-calendar-wd')?></option>
          </select>
          <p class="description">
            <a href="https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin" target="_blank"><?php _e('Upgrade to Premium for more themes.', 'event-calendar-wd'); ?></a>
          </p>
          <!--<a href="<?php //echo admin_url('admin.php?page=ecwd_themes');?>"><?php //_e('Default', 'ecwd');?></a> <sup style="color: #ba281e;">pro</sup>-->
        </td>
    </tr>

  <div id="ecwd_event_list_popup" class="ecwd_event_list_popup mfp-hide">

    <img class="ecwd_event_list_popup_loader" src="<?php echo ECWD_URL;?>/assets/loading.gif">
    <button class="button button-primary button-large ecwd_add_events ecwd_add_events_button">Add</button>
  </div>
</table>