<?php
if (!defined("ABSPATH")) {
    exit();
}
if (!$isRafExists) {
    ?>
    <li><img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/demo.png"); ?>" style="vertical-align:bottom;" /> &nbsp; <?php esc_html_e("Report and Flagging", "wpdiscuz"); ?></li>
    <?php
}