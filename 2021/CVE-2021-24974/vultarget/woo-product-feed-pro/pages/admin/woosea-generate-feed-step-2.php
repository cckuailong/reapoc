<?php
/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="woo-product-feed-pro-ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!';
}
add_filter('admin_footer_text', 'my_footer_text');

/**
 * Create notification object
 */
$notifications_obj = new WooSEA_Get_Admin_Notifications;
$notifications_box = $notifications_obj->get_admin_notifications ( '2', 'false' );

/**
 * Create product attribute object
 */
$attributes_obj = new WooSEA_Attributes;
$attributes = $attributes_obj->get_product_attributes();

/**
 * Update or get project configuration 
 */
if (array_key_exists('project_hash', $_GET)){
        $project = WooSEA_Update_Project::get_project_data(sanitize_text_field($_GET['project_hash']));
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_GET['channel_hash']));
        $manage_project = "yes";
	$standard_attributes = array();

	foreach ($project['attributes'] as $k => $v){
		$value = $attributes[$k];
		$standard_attributes[$k] = $value;	
	}
} else {
        $project = WooSEA_Update_Project::update_project($_POST);
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_POST['channel_hash']));
	$standard_attributes = $attributes_obj->get_standard_attributes($project);
	
	// Product variations will need to have the Item group ID attribute in the product feed
	if(isset($_POST['product_variations'])){
		$standard_attributes["item_group_id"] = "Item group ID (obliged)";
	}
}
$left_attributes = array_diff($attributes, $standard_attributes);

/**
 * Determine next step in configuration flow
 */
$step = 4;
if($channel_data['taxonomy'] != "none"){
	$step = 1;
}
?>
<div class="wrap">
	<div class="woo-product-feed-pro-form-style-2">

	<table class="woo-product-feed-pro-table">	
		<tbody class="woo-product-feed-pro-body">
		<div class="woo-product-feed-pro-form-style-2-heading">Attribute picking</div>

                        <div class="<?php _e($notifications_box['message_type']); ?>">
                                <p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                        </div>		

			<tr>
				<td><strong>All active attributes:</strong></td>
				<td><strong>Export these attributes to product feed:</strong></td>
			</tr>
			<tr>
				<td><br/>
					<div class="woo-product-feed-pro-textarea">
						<ul id="woo-product-feed-pro-sortable1" class="connectedSortable">
  							<?php
							foreach ($left_attributes as $key=>$value) {
								$display_value = ucfirst($value);
								print "<li class=\"ui-state-default\">$display_value <span style=\"float:right;\" class=\"ui-icon ui-icon-arrow-4\"></span><input type=\"hidden\" name=\"attributes[$key]\" value=\"true\"/></li>";
							}
							?>
						</ul>
					</div> 
				<br/></td>
				<td><br/>
					<form action="" method="post">
					<div class="woo-product-feed-pro-textarea">
						<ul id="woo-product-feed-pro-sortable2" class="connectedSortable">
							<?php
							foreach ($standard_attributes as $key=>$value) {
								$display_value = ucfirst($value);
								print "<li class=\"ui-state-default\">$display_value <span style=\"float:right;\" class=\"ui-icon ui-icon-arrow-4\"></span><input type=\"hidden\" name=\"attributes[$key]\" value=\"true\"/></li>";
							}
							?>
						</ul>
					</div>	
				<br/></td>
			</tr>			
			<tr>
				<td colspan="2">
                                	<input type="hidden" id="channel_hash" name="channel_hash" value="<?php print "$project[channel_hash]";?>">
                                	<?php
                                        if(isset($manage_project)){
                                        ?>
                                        	<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>"> 
						<input type="hidden" name="step" value="100">
				        	<input type="submit" value="Save" />
					<?php
					} else {
					?>
                                        	<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>"> 
						<input type="hidden" name="step" value="<?php print "$step";?>">
				        	<input type="submit" value="Continue" />
					<?
					}
					?>
				</td>
			</tr>
		</form>
		</tbody>
	</table>
	</div>
</div>
