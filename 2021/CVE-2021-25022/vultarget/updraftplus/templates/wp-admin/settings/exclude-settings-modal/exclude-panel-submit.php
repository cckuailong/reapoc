<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>
<br>
<input type="button" class="updraft-exclude-submit button-primary" value="<?php echo empty($text_button) ? __('Add an exclusion rule', 'updraftplus') : $text_button;?>" data-panel="<?php echo esc_attr($panel);?>"/>
