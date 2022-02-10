<?php if ( ! defined( 'ABSPATH' ) ) exit; 


if (function_exists('ulpb_available_pro_widgets')) {
	?>

	<script type="text/javascript" src="<?php echo(ULPB_PLUGIN_URL.'/js/Chart.js'); ?>"></script>
	<div id="">
	<div style="padding: 0 12.5%;">
		<select class="analyticsDateRange" style="display: inline-block; margin: 15px 15px;">
			<option value="7">Last 7 Days</option>
			<option value="30">Last 30 Days</option>
			<option value="60">Last 60 Days</option>
			<option value="100">Last 100 Days</option>
			<option value="300">Last 300 Days</option>
		</select>

		<div id="resetAnalyticsBtn" class="resetAnalyticsBtn" style="margin: 15px -92px; display: inline-block;"> Reset Analytics </div>
		<p class="analyticsDeleted"></p>
	</div>

	<div id="mainAnalyticsContainer">
	</div>


	</div>

	<?php	
}else{
	?>
	<div  class="abTestNotice" style="max-width: 1200px;"> 
	    <i class='fa fa-circle-o-notch'></i> 
	   	Did you know You can View analytics with premium plan :   
	    <a href='https://pluginops.com/page-builder/?ref=analytics' target='_blank'> Click here to order</a>
	</div>
	<?php
}
?>
