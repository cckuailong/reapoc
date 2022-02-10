<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="cmplz-save" value="1">
	<?php if (isset($_GET['action']) && $_GET['action']==='new') {?>
		<input type="hidden" name="cmplz_add_new" value="1">
	<?php } ?>
	<?php wp_nonce_field( 'complianz_save', 'cmplz_nonce', true, true ) ?>
	<div class="cmplz-section-content">
		<div class="cmplz-scroll">
			{content}
		</div>
	</div>
</form>
