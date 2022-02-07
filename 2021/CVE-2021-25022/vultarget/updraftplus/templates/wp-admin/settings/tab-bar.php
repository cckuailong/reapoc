<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

<h2 class="nav-tab-wrapper">
<?php
foreach ($main_tabs as $tab_slug => $tab_label) {
	$tab_slug_as_attr = esc_attr(sanitize_title($tab_slug));
?>
	<a class="nav-tab <?php if ($tabflag == $tab_slug) echo 'nav-tab-active'; ?>" id="updraft-navtab-<?php echo $tab_slug_as_attr;?>" href="<?php echo UpdraftPlus::get_current_clean_url();?>#updraft-navtab-<?php echo $tab_slug_as_attr;?>-content" ><?php echo $tab_label;?></a>
<?php
}
?>
</h2>
