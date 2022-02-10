<?php
/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return _e( 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="woo-product-feed-pro-ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!','woo-product-feed-pro' );
}
add_filter('admin_footer_text', 'my_footer_text');
$add_manipulation_support = get_option ('add_manipulation_support');
$host = $_SERVER['HTTP_HOST'];

/**
 * Create notification object
 */
$notifications_obj = new WooSEA_Get_Admin_Notifications;
$notifications_box = $notifications_obj->get_admin_notifications ( '15', 'false' );

/**
 * Create product attribute object
 */
$attributes_obj = new WooSEA_Attributes;
$attributes = $attributes_obj->get_product_attributes();

/**
 * Update or get project configuration 
 */
$nonce = wp_create_nonce( 'woosea_ajax_nonce' );

/**
 * Update or get project configuration 
 */
if (array_key_exists('project_hash', $_GET)){
        $project = WooSEA_Update_Project::get_project_data(sanitize_text_field($_GET['project_hash']));
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_GET['channel_hash']));
        $count_rules = 0;
	if(isset($project['field_manipulation'])){
		$count_rules = count($project['field_manipulation']);
	}
	$manage_project = "yes";
} else {
        $project = WooSEA_Update_Project::update_project($_POST);
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_POST['channel_hash']));
	$count_rules = 0;
}
?>
	<div class="wrap">
		<div class="woo-product-feed-pro-form-style-2">
			<div class="woo-product-feed-pro-form-style-2-heading"><?php _e( 'Product data manipulation','woo-product-feed-pro' );?></div>

                	<div class="<?php _e($notifications_box['message_type']); ?>">
                        	<p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                	</div>
			<form id="fieldmanipulation" method="post">
			<input name="nonce_manipulation_mapping" id="nonce_manipulation_mapping" class="nonce_manipulation_mapping" value="<?php print "$nonce";?>" type="hidden">

			<table class="woo-product-feed-pro-table" id="woosea-ajax-table" border="1">
				<thead>
            				<tr>
                				<th></th>
						<th><?php _e( 'Product type','woo-product-feed-pro' );?></th>
						<th><?php _e( 'Field','woo-product-feed-pro' );?></th>
						<th><?php _e( 'Becomes','woo-product-feed-pro' );?></th>
						<th></th>
            				</tr>
        			</thead>
				
				<tbody class="woo-product-feed-pro-body">
				<?php
				if(isset($project['field_manipulation'])){

					$product_types = array(
						"all" => "Simple and variable",
						"simple" => "Simple",
						"variable" => "Variable"
					);

                                	foreach ($project['field_manipulation'] as $manipulation_key => $manipulation_array){
					?>
						<tr class="rowCount">
                                                	<td valign="top">
								<input type="hidden" name="field_manipulation[<?php print "$manipulation_key";?>][rowCount]" value="<?php print "$manipulation_key";?>"><input type="checkbox" name="record" class="checkbox-field">
							</td>
			                               	<td valign="top">
             							<select name="field_manipulation[<?php print "$manipulation_key";?>][product_type]" class="select-field">
                                                                	<?php
                                                                        foreach ($product_types as $k => $v){
                                                                        	if (isset($project['field_manipulation'][$manipulation_key]['product_type']) AND ($project['field_manipulation'][$manipulation_key]['product_type'] == $k)){
                                                                                	print "<option value=\"$k\" selected>$v</option>";
                                                                              	} else {
                                                                                        print "<option value=\"$k\">$v</option>";
                                                                              	}
                                                                        }
                                                                        ?>
                                                              	</select>
							</td>
							<td valign="top">
                                                        	<select name="field_manipulation[<?php print "$manipulation_key";?>][attribute]" class="select-field">
                                                                	<?php
                                                                        foreach ($attributes as $k => $v){
                                                                        	if (isset($project['field_manipulation'][$manipulation_key]['attribute']) AND ($project['field_manipulation'][$manipulation_key]['attribute'] == $k)){
                                                                                	print "<option value=\"$k\" selected>$v</option>";
                                                                              	} else {
                                                                                        print "<option value=\"$k\">$v</option>";
                                                                              	}
                                                                        }
                                                                        ?>
                                                              	</select>
                                                   	</td>
							<td valign="top" class="becomes_fields_<?php print "$manipulation_key";?>">
								<?php
                                                                        foreach ($project['field_manipulation'][$manipulation_key]['becomes'] as $k => $v){
										print "<select name=\"field_manipulation[$manipulation_key][becomes][$k][attribute]\" class=\"select_field\">";
										foreach ($attributes as $ak => $av){
                                                                        		if (isset($project['field_manipulation'][$manipulation_key]['becomes'][$k]['attribute']) AND ($project['field_manipulation'][$manipulation_key]['becomes'][$k]['attribute'] == $ak)){
                                                                                		print "<option value=\"$ak\" selected>$av</option>";
                                                                              		} else {
                                                                                        	print "<option value=\"$ak\">$av</option>";
                                                                              		}
                                                                        	}
										print "</select>";
										print "</br>";
									}
								?>
							</td>
							<td>
								<span class="dashicons dashicons-plus field_extra field_manipulation_extra_<?php print"$manipulation_key";?>" style="display: inline-block;" title="Add an attribute to this field"></span>
							</td>
						</tr>
					<?php
					}
				}
				?>
				</tbody>      				
				
				<tbody>

				<tr class="rules-buttons">
					<td colspan="8">
                                                <input type="hidden" id="channel_hash" name="channel_hash" value="<?php print "$project[channel_hash]";?>">
						<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>">
                		                <input type="hidden" name="woosea_page" value="field_manipulation">
                		                <input type="hidden" name="step" value="100">
                       	       			<input type="button" class="delete-row" value="- Delete">&nbsp;<input type="button" class="add-field-manipulation" value="+ Add field manipulation">&nbsp;<input type="submit" id="savebutton" value="Save">
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
