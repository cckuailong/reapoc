<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 6/6/20 06:35
 */
if (!defined("ABSPATH")) die();
?>
<div class='w3eden'>
    <div class='<?php echo $css_class; ?>'>
        <?php include \WPDM\__\Template::locate("category-shortcode-toolbar.php", __DIR__); ?>

        <div id="content_<?php echo $scid; ?>">
            <?php echo $cimg ?>
            <?php echo $subcats ?>
            <?php echo $html ?>
            <?php echo $pagination ?>
        </div>

        <div style='clear:both'></div>
    </div>
</div>
