<div class="sb-customizer-ctn cff-fb-fs" v-if="iscustomizerScreen">
	<?php include_once CFF_BUILDER_DIR . 'templates/sections/customizer/sidebar.php'; ?>
	<?php include_once CFF_BUILDER_DIR . 'templates/sections/customizer/preview.php'; ?>
</div>
<div v-html="feedStyleOutput != false ? feedStyleOutput : ''"></div>
<script type="text/x-template" id="cff-colorpicker-component">
	<input type="text" v-bind:value="color" placeholder="Select">
</script>