<?php
if (!defined("ABSPATH")) {
    exit();
}
if (!$isUcmExists) {
    ?>
    <li><img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/demo.png"); ?>" style="vertical-align:bottom;" /> &nbsp; <?php esc_html_e("Mentioning", "wpdiscuz"); ?></li>
    <?php
}