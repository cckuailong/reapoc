<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

<strong><?php
	if (!empty($prefix)) echo $prefix.' ';
	echo $title;
?></strong>: 
<?php
	echo $text;

	if (isset($discount_code)) echo ' <b>' . $discount_code . '</b>';

// if (isset($text2)) {
// echo '</p><p>' . $text2 . '</p><p>';
// }
	
	if (!empty($button_link) && !empty($button_meta)) {
?>
<a class="updraft_notice_link" href="<?php esc_attr_e(apply_filters('updraftplus_com_link', $button_link));?>"><?php
if ('updraftcentral' ==$button_meta) {
	_e('Get UpdraftCentral', 'updraftplus');
} elseif ('review' == $button_meta) {
	_e('Review UpdraftPlus', 'updraftplus');
} elseif ('updraftplus' == $button_meta) {
	_e('Get Premium', 'updraftplus');
} elseif ('signup' == $button_meta) {
	_e('Sign up', 'updraftplus');
} elseif ('go_there' == $button_meta) {
	_e('Go there', 'updraftplus');
} else {
	_e('Read more', 'updraftplus');
}
?></a><br> <br>
	
	<?php }
