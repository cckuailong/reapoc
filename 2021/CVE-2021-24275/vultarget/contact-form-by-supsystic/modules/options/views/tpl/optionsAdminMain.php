<style type="text/css">
	.cfsAdminMainLeftSide {
		width: 56%;
		float: left;
	}
	.cfsAdminMainRightSide {
		width: <?php echo (empty($this->optsDisplayOnMainPage) ? 100 : 40)?>%;
		float: left;
		text-align: center;
	}
	#cfsMainOccupancy {
		box-shadow: none !important;
	}
</style>
<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<?php _e('Main page Go here!!!!', CFS_LANG_CODE)?>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>