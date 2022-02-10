<?php

class ecwd_uninstall {

  public function __construct(){

    if(isset($_POST['ecwd_check_yes']) && $_POST['ecwd_check_yes'] === 'yes') {
      $this->uninstall();
    } else if(get_site_transient('ecwd_uninstall') === '1') {
        $this->deactivate_plugin();
    } else {
      $this->ecwd_display_uninstall_page();
    }

  }

  private function uninstall(){

    if(!wp_verify_nonce($_POST['ecwd_uninstall'], 'ecwd_uninstall')) {
      return;
    }

    $this->delete_options();
    $this->delete_posts();
    $this->delete_taxonomies();
    set_site_transient('ecwd_uninstall', '1', 12 * 60 * 60);

    die('<script>window.location.href="admin.php?page=ecwd_uninstall"</script>');
  }

  private function delete_options(){
    $options = $this->get_options();
    foreach($options['addons'] as $opt) {
      delete_option($opt);
    }

    foreach($options['calendar_options'] as $opt) {
      delete_option($opt);
    }
  }

  private function delete_posts(){

    $posts = get_posts(array(
        'numberposts' => -1,
        'post_type' => $this->get_post_types(),
        'post_status' => 'any'
      )
    );

    foreach($posts as $post) {
      wp_delete_post($post->ID, true);
    }

  }

  private function delete_taxonomies(){
    $terms = get_terms(array(
      'taxonomy' => $this->get_taxonomies(),
      'hide_empty' => false,
      'fields' => 'all',
      'hierarchical' => true,
      'child_of' => 0,
      'get' => 'all',
      'childless' => false
    ));


    foreach($terms as $term) {
      wp_delete_term($term->term_id, $term->taxonomy);
      if($term->taxonomy === "ecwd_event_category") {
        delete_option('ecwd_event_category_' . $term->term_id);
      }
    }

  }


  private function ecwd_display_uninstall_page(){
    $taxanomies = $this->get_taxonomies();
    $options = $this->get_options();
    $options = $options['calendar_options'];
    $post_types = $this->get_post_types();

    ?>

      <style>
          .goodbye-text {
              font-size: 16px;
              font-weight: bold;
              background: #fff;
              padding: 15px;
              line-height: 22px;
          }

          .goodbye-text a {
              font-size: 15px;
          }

          table.widefat {
              margin-bottom: 8px;
          }
      </style>
      <form class="sc_form" method="post" action="" style="width:99%;">
        <?php wp_nonce_field('ecwd_uninstall', 'ecwd_uninstall'); ?>
          <div class="wrap">
              <h2><?php echo __('Uninstall Event Calendar WD', 'event-calendar-wd'); ?></h2>
              <div class="goodbye-text">
                  Before uninstalling the plugin, please Contact our
                  <a href="https://help.10web.io/hc/en-us/requests/new" target="_blank">support team</a>. We'll do
                  our best to help you out with your issue. We value each and every user and value what’s right for our
                  users in everything we do.<br>
                  However, if anyway you have made a decision to uninstall the plugin, please take a minute to
                  <a href="https://help.10web.io/hc/en-us/requests/new" target="_blank">Contact us</a> and tell what
                  you didn't like for our plugins further improvement and development. Thank you !!!
              </div>
              <p>
                <?php echo __('Deactivating Event Calendar WD plugin does not remove any data that may have been created. To completely remove this plugin, you can uninstall it here. Uninstalling Event Calendar WD will also remove the data of its extensions.', 'event-calendar-wd'); ?>
              </p>
              <p style="color: red;">
                  <strong><?php echo __('WARNING:', 'event-calendar-wd'); ?></strong>
                <?php echo __("Once uninstalled, this can't be undone. You should use a Database Backup plugin of WordPress to back up all the data first.", 'event-calendar-wd'); ?>
              </p>
              <p style="color: red">
                  <strong><?php echo __('The following Database Tables will be deleted:', 'event-calendar-wd'); ?></strong>
              </p>
              <table class="widefat">
                  <thead>
                  <tr>
                      <th><?php echo __('Posts of these types', 'event-calendar-wd'); ?></th>
                  </tr>
                  </thead>
                  <tr>
                      <td valign="top">
                          <ol>
                            <?php foreach($post_types as $post_type) {

                              if($post_type !== 'ecwd_subscribe_page' || $post_type !== 'ecwd_countdown_theme') {
                                echo "<li>" . $post_type . "</li>";
                              }

                            } ?>
                          </ol>
                      </td>
                  </tr>
              </table>
              <table class="widefat">
                  <thead>
                  <tr>
                      <th><?php echo __('Terms of these taxonomies', 'event-calendar-wd'); ?></th>
                  </tr>
                  </thead>
                  <tr>
                      <td valign="top">
                          <ol>
                            <?php
                            foreach($taxanomies as $tax) {

                              echo "<li>" . $tax . "</li>";

                            } ?>
                          </ol>
                      </td>
                  </tr>
              </table>
              <table class="widefat">
                  <thead>
                  <tr>
                      <th><?php echo __('Database options', 'event-calendar-wd'); ?></th>
                  </tr>
                  </thead>
                  <tr>
                      <td valign="top">
                          <ol>
                            <?php
                            foreach($options as $option) {

                              echo "<li>" . $option . "</li>";

                            } ?>
                          </ol>
                      </td>
                  </tr>
              </table>
              <p style="text-align: center;">
                <?php echo __('Do you really want to uninstall Event Calendar WD ?', 'event-calendar-wd'); ?>
              </p>
              <p style="text-align: center;">
                  <input type="checkbox" name="ecwd_check_yes" id="ecwd_check_yes" value="yes"/>&nbsp;
                  <label for="ecwd_check_yes"><?php echo __('Yes', 'event-calendar-wd'); ?></label>
              </p>
              <p style="text-align: center;">
                  <a id="ecwd_uninstall_btn" href="#" class="button-primary">UNINSTALL</a>
              </p>
          </div>
      </form>
      <script>
          jQuery(document).ready(function () {
              jQuery('#ecwd_uninstall_btn').on('click', function (e) {
                  e.preventDefault();

                  if (jQuery('#ecwd_check_yes').prop('checked') === false) {
                      return false;
                  }

                  var text = '<?php echo addslashes(__('You are About to Uninstall Event Calendar WD from WordPress. This Action Is Not Reversible.', 'event-calendar-wd')); ?>';
                  if (confirm(text)) {
                      jQuery(this).closest('form').submit();
                  } else {
                      return false;
                  }
              });
          });
      </script>
  <?php }

  private function deactivate_plugin(){

    /*DEACTIVATION POPUP*/
    wp_enqueue_script('ecwd-deactivate-popup', ECWD_URL . '/wd/assets/js/deactivate_popup.js', array(), ECWD_VERSION, true);
    $admin_data = wp_get_current_user();

    wp_localize_script('ecwd-deactivate-popup', 'ecwdWDDeactivateVars', array(
      "prefix" => "ecwd",
      "deactivate_class" => 'ecwd_deactivate_link',
      "email" => $admin_data->data->user_email,
      "plugin_wd_url" => "https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin",
    ));
    wp_enqueue_style('ecwd_deactivate-css', ECWD_URL . '/wd/assets/css/deactivate_popup.css', array(), ECWD_VERSION);


    if(!class_exists("TenWebLibConfig")) {
      include_once(ECWD_DIR . "/wd/config.php");
    }

    if(!class_exists("TenWebLibDeactivate")) {
      include_once(ECWD_DIR . "/wd/includes/deactivate.php");
    }
    $config = new TenWebLibConfig();

    global $ecwd_wd_freemius_config;
    $config->set_options($ecwd_wd_freemius_config);
    $deactivate_reasons = new TenWebLibDeactivate($config);
    //$deactivate_reasons->add_deactivation_feedback_dialog_box();
    $deactivate_reasons->submit_and_deactivate();


    ?>
      <style>
          div.error {
              display: none !important;
          }
      </style>
      <div class="wrap">
          <h2><?php echo __('Uninstall Event Calendar WD', 'event-calendar-wd'); ?></h2>
          <p>
              <strong>
                  <a class="ecwd_deactivate_link" data-uninstall="1"
                     href="<?php echo $this->get_deactivate_url(); ?>"><?php echo __('Click Here', 'event-calendar-wd'); ?></a> <?php echo __('To Finish the Uninstallation and Event Calendar WD will be Deactivated Automatically.', 'event-calendar-wd'); ?>
              </strong>
          </p>
      </div>
      <script>
          /*DEACTIVATION POPUP*/
          jQuery(document).ready(function () {
              wdReady("ecwd");
          });
      </script>
  <?php }


  private function get_post_types(){
    $post_types = array('ecwd_event', 'ecwd_organizer', 'ecwd_venue', 'ecwd_calendar', 'ecwd_theme', 'ecwd_subscribe_page', 'ecwd_countdown_theme');
    return $post_types;
  }

  private function get_taxonomies(){
    return array('ecwd_event_category', 'ecwd_event_tag');
  }

  private function get_options(){
    $options = array(
      'calendar_options' => array(
        'ecwd_setup_default_themes',
        'ecwd_themes_files_created',
        'ecwd_settings_events',
        'ecwd_version',
        'ecwd_default_calendar',
        'ecwd_grey_theme_id',
//        'ecwd_admin_notice',
        'ecwd_scripts_key',
        'ecwd_settings_general',
        'ecwd_slug_changed',
        'ecwd_single_slug',
        'ecwd_slug',
        'ecwd_cpt_setup',
        'ecwd_not_writable_warning',
        'ecwd_subscribe_done',
        'ecwd_config',
        'widget_ecwd_widget',
        'ecwd_settings',
        'ecwd_settings_category_archive',
        'ecwd_settings_custom_css',
        'ecwd_settings_google_map',
        'ecwd_old_events',
        'ecwd_event_category_children',
        'widget_ecwd_events_widget',
      ),
      'addons' => array(
        'tickets_ids',
        'ecwd_ticketing_email_template',
        'spider_categories',
        'spider_calendars',
        'ecwd_old_calendars',
        'ecwd_old_venues',
        'ecwd_old_organizers',
        'ecwd_old_categories',
        'ecwd_old_tags',
        'ecwd_send_mail_notification',
        'ecwd_subscribe_pages_ids',
        'ecwd_settings_ecwd_subscribe',
        'ecwd_wait_subscribers_data',
        'ecwd_subscribers_data',
        'ecwd_subscribe_notice',
        'ecwd_activate_subscriber_mail_template',
        'ecwd_subscribe_mail_template',
        'ecwd_cancle_event_mail_template',
        'ecwd_countdown_setup_default_theme',
        'ecwd_countdown_cpt_setup',
        'ecwd_fb_import',
        'ecwd_gcal_import',
        'ecwd_ical_import',
        'widget_ecwd_events_filter_widget',
        'ecwd_settings_fb',
        'ecwd_settings_gcal',
        'ecwd_settings_ical',
        'ecwd_settings_af',
        'ecwd_settings_filter_settings',
        'ecwd_settings_export',
      )

    );
    return $options;
  }

  private function get_deactivate_url(){
    $deactivate_url =
      add_query_arg(
        array(
          'action' => 'deactivate',
          'plugin' => plugin_basename(ECWD_PLUGIN_MAIN_FILE),
          '_wpnonce' => wp_create_nonce('deactivate-plugin_' . plugin_basename(ECWD_PLUGIN_MAIN_FILE))
        ),
        admin_url('plugins.php')
      );
    return $deactivate_url;
  }
}


?>

