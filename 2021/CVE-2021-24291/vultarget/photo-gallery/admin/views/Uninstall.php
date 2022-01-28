<?php

class UninstallView_bwg extends AdminView_bwg {
  /**
   * Display page.
   *
   * @param $params
   */
  public function display($params = array()) {
    ob_start();
    echo $this->body($params);
    // Pass the content to form.
    $form_attr = array(
      'id' => BWG()->prefix . '_uninstall',
      'name' => BWG()->prefix . '_uninstall',
      'class' => BWG()->prefix . '_uninstall wd-form',
      'action' => add_query_arg(array( 'page' => 'uninstall_' . BWG()->prefix ), 'admin.php'),
    );
    echo $this->form(ob_get_clean(), $form_attr);
  }

  /**
   * Generate page body.
   *
   * @param $params
   */
  public function body( $params = array() ) {
    ?>
    <div class="goodbye-text">
      <?php
      $support_team = '<a href="https://10web.io/contact-us/' . BWG()->utm_source . '" target="_blank">' . __('support team', BWG()->prefix) . '</a>';
      $contact_us = '<a href="https://10web.io/contact-us/' . BWG()->utm_source . '" target="_blank">' . __('Contact us', BWG()->prefix) . '</a>';
      echo sprintf(__("Before uninstalling the plugin, please Contact our %s. We'll do our best to help you out with your issue. We value each and every user and value what's right for our users in everything we do.<br />
      However, if anyway you have made a decision to uninstall the plugin, please take a minute to %s and tell what you didn't like for our plugins further improvement and development. Thank you !!!", BWG()->prefix), $support_team, $contact_us); ?>
    </div>
    <p>
      <?php echo sprintf(__("Deactivating %s plugin does not remove any data that may have been created. To completely remove this plugin, you can uninstall it here.", BWG()->prefix), BWG()->nicename); ?>
    </p>
    <p class="wd-red">
      <strong><?php _e("WARNING:", BWG()->prefix); ?></strong>
      <?php _e("Once uninstalled, this can't be undone. You should use a Database Backup plugin of WordPress to back up all the data first.", BWG()->prefix); ?>
    </p>
    <p class="wd-red">
      <strong><?php _e("The following Database Tables will be deleted:", BWG()->prefix); ?></strong>
    </p>
    <table class="widefat">
      <thead>
        <tr>
          <th><?php _e("Database Tables", BWG()->prefix); ?></th>
        </tr>
      </thead>
      <tr>
        <td>
          <?php
          foreach ( $params['tables'] as $table ) {
            ?>
            <p><?php echo $table; ?></p>
            <?php
          }
          ?>
        </td>
      </tr>
      <tfoot>
        <tr>
          <th>
            <input type="checkbox" name="bwg_delete_files" id="bwg_delete_files" class="wd-vertical-middle" />
            <label for="bwg_delete_files">&nbsp;<?php _e("Delete the folder containing uploaded images.", BWG()->prefix); ?></label>
          </th>
        </tr>
      </tfoot>
    </table>
    <p class="wd-text-center">
      <?php echo sprintf(__("Do you really want to uninstall %s?", BWG()->prefix), BWG()->nicename); ?>
    </p>
    <p class="wd-text-center">
      <input type="checkbox" name="Photo Gallery" id="check_yes" value="yes" />
      <label for="check_yes"><?php _e("Yes", BWG()->prefix); ?></label>
    </p>
    <p class="wd-text-center">
    <?php
    $buttons = array(
      'save' => array(
        'title' => __('UNINSTALL', BWG()->prefix),
        'value' => 'uninstall',
        'onclick' => 'if (check_yes.checked && confirm(\'' . addslashes(sprintf(__("You are About to Uninstall %s from WordPress. This Action Is Not Reversible.", BWG()->prefix), BWG()->nicename)) . '\')) {spider_set_input_value(\'task\', \'uninstall\');} else {return false;}',
        'class' => 'button-primary',
      ),
    );
    echo $this->buttons($buttons, TRUE);
    ?>
    </p>
    <?php
  }
}
