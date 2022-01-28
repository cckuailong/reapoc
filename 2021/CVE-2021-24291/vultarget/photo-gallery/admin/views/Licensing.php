<?php

/**
 * Class LicensingView_bwg
 */
class LicensingView_bwg {
  public function __construct() {
    wp_enqueue_style(BWG()->prefix . '_licensing');
    wp_enqueue_style(BWG()->prefix . '_tables');
  }

  public function display() {
    ?>
    <div id="featurs_tables">
      <div id="featurs_table1">
        <span>WordPress 3.4+ <?php _e("ready", BWG()->prefix); ?></span>
        <span>SEO-<?php _e("friendly", BWG()->prefix); ?></span>
        <span><?php _e("Responsive Design and Layout", BWG()->prefix); ?></span>
        <span><?php _e("5 Standard Gallery/Album Views", BWG()->prefix); ?></span>
        <span><?php _e("Watermarking/ Advertising Possibility", BWG()->prefix); ?></span>
        <span><?php _e("Basic Tag Cloud Widget", BWG()->prefix); ?></span>
        <span><?php _e("Image Download", BWG()->prefix); ?></span>
        <span><?php _e("Photo Gallery Slideshow Widget", BWG()->prefix); ?></span>
        <span><?php _e("Photo Gallery Widget", BWG()->prefix); ?></span>
        <span><?php _e("Slideshow/Lightbox Effects", BWG()->prefix); ?></span>
        <span><?php _e("Possibility of Editing/Creating New Themes", BWG()->prefix); ?></span>
        <span><?php _e("10 Pro Gallery/Album Views", BWG()->prefix); ?></span>
        <span><?php _e("Image Commenting", BWG()->prefix); ?></span>
        <span><?php _e("Image Social Sharing", BWG()->prefix); ?></span>
        <span><?php _e("Photo Gallery Tags Cloud Widget", BWG()->prefix); ?></span>
        <span><?php _e("Instagram Integration", BWG()->prefix); ?></span>
        <span>AddThis <?php _e("Integration", BWG()->prefix); ?></span>
        <span><?php _e("Add-ons Support", BWG()->prefix); ?></span>
      </div>
      <div id="featurs_table2">
        <span style="padding-top: 18px;height: 39px;"><?php _e("Free", BWG()->prefix); ?></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span>1</span>
        <span class="no"></span>
        <span class="no"></span>
        <span class="no"></span>
        <span class="no"></span>
        <span class="no"></span>
        <span class="no"></span>
        <span class="no"></span>
        <span class="no"></span>
      </div>
      <div id="featurs_table3">
        <span><?php _e("Pro Version", BWG()->prefix); ?></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span>15</span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
        <span class="yes"></span>
      </div>
    </div>
    <div style="float: left; clear: both;">
      <p><?php _e("After purchasing the commercial version follow these steps:", BWG()->prefix); ?></p>
      <ol>
        <li><?php _e("Deactivate Photo Gallery plugin.", BWG()->prefix); ?></li>
        <li><?php _e("Delete Photo Gallery plugin.", BWG()->prefix); ?></li>
        <li><?php _e("Install the downloaded commercial version of the plugin.", BWG()->prefix); ?></li>
      </ol>
    </div>
    <?php
  }
}
