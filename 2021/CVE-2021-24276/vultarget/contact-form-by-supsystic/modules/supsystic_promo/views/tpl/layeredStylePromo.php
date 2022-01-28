<div class="cfsFormOptRow">
	<label>
		<a target="_blank" href="<?php echo $this->promoLink?>" class="sup-promolink-input">
			<?php echo htmlCfs::checkbox('layered_style_promo', array(
				'checked' => 1,
				//'attrs' => 'disabled="disabled"',
			))?>
			<?php _e('Enable Layered Form Style', CFS_LANG_CODE)?>
		</a>
		<a target="_blank" class="button" style="margin-top: -8px;" href="<?php echo $this->promoLink?>"><?php _e('Available in PRO', CFS_LANG_CODE)?></a>
	</label>
	<div class="description"><?php _e('By default all Forms have modal style: it appears on user screen over the whole site. Layered style allows you to show your Form - on selected position: top, bottom, etc. and not over your site - but right near your content.', CFS_LANG_CODE)?></div>
</div>
<span>
	<div class="cfsFormOptRow">
		<span class="cfsOptLabel"><?php _e('Select position for your Form', CFS_LANG_CODE)?></span>
		<br style="clear: both;" />
		<div id="cfsLayeredSelectPosShell">
			<div class="cfsLayeredPosCell" style="width: 30%;" data-pos="top_left"><span class="cfsLayeredPosCellContent"><?php _e('Top Left', CFS_LANG_CODE)?></span></div>
			<div class="cfsLayeredPosCell" style="width: 40%;" data-pos="top"><span class="cfsLayeredPosCellContent"><?php _e('Top', CFS_LANG_CODE)?></span></div>
			<div class="cfsLayeredPosCell" style="width: 30%;" data-pos="top_right"><span class="cfsLayeredPosCellContent"><?php _e('Top Right', CFS_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
			<div class="cfsLayeredPosCell" style="width: 30%;" data-pos="center_left"><span class="cfsLayeredPosCellContent"><?php _e('Center Left', CFS_LANG_CODE)?></span></div>
			<div class="cfsLayeredPosCell" style="width: 40%;" data-pos="center"><span class="cfsLayeredPosCellContent"><?php _e('Center', CFS_LANG_CODE)?></span></div>
			<div class="cfsLayeredPosCell" style="width: 30%;" data-pos="center_right"><span class="cfsLayeredPosCellContent"><?php _e('Center Right', CFS_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
			<div class="cfsLayeredPosCell" style="width: 30%;" data-pos="bottom_left"><span class="cfsLayeredPosCellContent"><?php _e('Bottom Left', CFS_LANG_CODE)?></span></div>
			<div class="cfsLayeredPosCell" style="width: 40%;" data-pos="bottom"><span class="cfsLayeredPosCellContent"><?php _e('Bottom', CFS_LANG_CODE)?></span></div>
			<div class="cfsLayeredPosCell" style="width: 30%;" data-pos="bottom_right"><span class="cfsLayeredPosCellContent"><?php _e('Bottom Right', CFS_LANG_CODE)?></span></div>
			<br style="clear: both;"/>
		</div>
		<?php echo htmlCfs::hidden('params[tpl][layered_pos]')?>
	</div>
</span>
<style type="text/css">
	#cfsLayeredSelectPosShell {
		max-width: 560px;
		height: 380px;
	}
	.cfsLayeredPosCell {
		float: left;
		cursor: pointer;
		height: 33.33%;
		text-align: center;
		vertical-align: middle;
		line-height: 110px;
	}
	.cfsLayeredPosCellContent {
		border: 1px solid #a5b6b2;
		margin: 5px;
		display: block;
		font-weight: bold;
		box-shadow: -3px -3px 6px #a5b6b2 inset;
		color: #739b92;
	}
	.cfsLayeredPosCellContent:hover, .cfsLayeredPosCell.active .cfsLayeredPosCellContent {
		background-color: #e7f5f6; /*rgba(165, 182, 178, 0.3);*/
		color: #00575d;
	}
</style>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var proExplainContent = jQuery('#cfsLayeredProExplainWnd').dialog({
			modal:    true
		,	autoOpen: false
		,	width: 460
		,	height: 180
		});
		jQuery('.cfsLayeredPosCell').click(function(){
			proExplainContent.dialog('open');
		});
	});
</script>
<!--PRO explanation Wnd-->
<div id="cfsLayeredProExplainWnd" style="display: none;" title="<?php _e('Improve Free version', CFS_LANG_CODE)?>">
	<p>
		<?php printf(__('This functionality and more - is available in PRO version. <a class="button button-primary" target="_blank" href="%s">Get it</a> today for 29$', CFS_LANG_CODE), $this->promoLink)?>
	</p>
</div>