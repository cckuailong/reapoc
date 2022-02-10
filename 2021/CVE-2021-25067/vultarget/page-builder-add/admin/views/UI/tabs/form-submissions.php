<?php if ( ! defined( 'ABSPATH' ) ) exit;

if (function_exists('ulpb_available_pro_widgets') ) {
}else {
	?>
	<div  class="abTestNotice" style=""> 
	    <i class='fa fa-circle-o-notch'></i> 
	   	Did you know You can View, Download, Export & Sync your form submissions with your favorite  email marketing services :   
	    <a href='https://pluginops.com/page-builder/?ref=formSubmissions' target='_blank'> Click here to order</a>
	</div>
	<?php
}

?>


<?php

	if (function_exists('popb_formBuilder_database_renderFormDataTable')) {
	  echo popb_formBuilder_database_renderFormDataTable($postId);
	}else{
		echo "<h1> Please get Form Builder Database extension to access all the submissions. </h1>";
	}

?>