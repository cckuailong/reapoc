<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

<div class="updraft_advert_bottom">
	<div class="updraft_advert_content_right">
		<h4 class="updraft_advert_heading">
			<?php
				if (!empty($prefix)) echo $prefix.' ';
				echo $title;
			?>
		</h4>
		<p>
			<?php
				echo $text;

				if (isset($discount_code)) echo ' <b>' . $discount_code . '</b>';
				
				if (!empty($button_link) && !empty($button_meta)) {
			?>
			<a class="updraft_notice_link" href="<?php esc_attr_e(apply_filters('updraftplus_com_link', $button_link));?>"><?php
					if ('updraftcentral' == $button_meta) {
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
				?></a>
			<?php
				}
			?>
		</p>
	</div>
	<div class="clear"></div>
</div>
