<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 6/6/20 06:35
 */
if (!defined("ABSPATH")) die();
$category_shortcode = wpdm_valueof($scparams, 'catsc', ['validate' => 'int']);
$toolbar_file = $category_shortcode === 1  ? "category-shortcode-toolbar.php" : "packages-shortcode-toolbar.php";

/*
tbgrid - Toolbar grid layout setting variable, 12 grid layout distribute amonng search bar, order by dropdown, order dropdown, and filter button
$sr - Search bar width
$ob - Orderby dropdown width
$od - Order dropdown width
$bt - Button area width
*/
list($sr, $ob, $od, $bt) = [6,2,2,2];
if(isset($scparams['tbgrid'])){
    list($sr, $ob, $od, $bt) = explode(",", $scparams['tbgrid']);
}

?>
<div class='w3eden'>
    <div class='<?php echo $css_class; ?>'>

        <?php include \WPDM\__\Template::locate($toolbar_file, __DIR__); ?>

        <div id="content_<?php echo $scid; ?>">
            <?php echo $html ?>
            <?php echo $pagination ?>
        </div>

        <div style='clear:both'></div>
    </div>
</div>
