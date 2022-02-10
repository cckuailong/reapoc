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
$notifications_box = $notifications_obj->get_admin_notifications ( '7', 'false' );

/**
 * Create product attribute object
 **/
$attributes_obj = new WooSEA_Attributes;
$attribute_dropdown = $attributes_obj->get_product_attributes();

/**
 * Update or get project configuration 
 */
if (array_key_exists('project_hash', $_GET)){
        $project = WooSEA_Update_Project::get_project_data(sanitize_text_field($_GET['project_hash']));
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_GET['channel_hash']));
	$count_mappings = count($project['attributes']);
        $manage_project = "yes";
} else {
        $project = WooSEA_Update_Project::update_project($_POST);
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_POST['channel_hash']));
}

/**
 * Determine next step in configuration flow
 **/
$step = 4;
if($channel_data['taxonomy'] != "none"){
	$step = 1;
}

/**
 * Create channel attribute object
 **/
require plugin_dir_path(__FILE__) . '../../classes/channels/class-'.$channel_data['fields'].'.php';
$obj = "WooSEA_".$channel_data['fields'];
$fields_obj = new $obj;
$attributes = $fields_obj->get_channel_attributes();	

/**
 * Add the Item Group ID attribute for product variations
 **/
if(isset($_POST['product_variations'])){
	$attributes["Detailed product description"]["Item group ID"]["format"] = "required";
	$attributes["Detailed product description"]["Item group ID"]["woo_suggest"] = "item_group_id";

	if ((isset($project[fields])) AND ($project[fields] == "google_shopping")){
		$attributes["Detailed product description"]["Item group ID"]["feed_name"] = "g:item_group_id";
	}
}
?>

	<div class="wrap">
		<div class="woo-product-feed-pro-form-style-2">
			<div class="woo-product-feed-pro-form-style-2-heading">Field mapping</div>

                	<div class="<?php _e($notifications_box['message_type']); ?>">
                        	<p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                	</div>

			<form action="" method="post">
			<table class="woo-product-feed-pro-table" id="woosea-fieldmapping-table" border="1">
				<thead>
            				<tr>
						<th></th>
						<th>Type mapping</th>
                				<th>
						<?php
							print "$channel_data[name] attributes";
						?>
						</th>
                				<th>Prefix</th>
                				<th>Value</th>
						<th>Suffix</th>
            				</tr>
        			</thead>
        
 				<tbody class="woo-product-feed-pro-body">
					<?php
	
					if (!isset($count_mappings)){	
						$c = 0;
						foreach($attributes as $row_key => $row_value){
							foreach($row_value as $row_k => $row_v){
								if ($row_v['format'] == "required"){
								?>
								<tr>
									<td><input type="hidden" name="attributes[<?php print "$c";?>][rowCount]" value="<?php print "$c";?>"><input type="checkbox" name="record" class="checkbox-field"></td>
                							<td><i>field mapping</i></td>
									<td>
										<select name="attributes[<?php print"$c"; ?>][attribute]" class="select-field">
										<?php
											foreach($attributes as $key => $value) {
												print "<optgroup label='$key'><strong>$key</strong>";
												foreach($value as $k => $v){
													if($v['feed_name'] == $row_v['feed_name']){
														if (array_key_exists('name',$v)){
															print "<option value='$v[feed_name]' selected>$k ($v[name])</option>";
														} else {
															print "<option value='$v[feed_name]' selected>$k</option>";
														}
													} else {
														if (array_key_exists('name',$v)){
															print "<option value='$v[feed_name]'>$k ($v[name])</option>";
														} else {
															print "<option value='$v[feed_name]'>$k</option>";
														}
													}
												}
											}
										?>
										</select>
									</td>
                							<td>
										<input type="text" name="attributes[<?php print "$c";?>][prefix]" class="input-field-medium">
									</td>
									<td>
										<select name="attributes[<?php print "$c";?>][mapfrom]" class="select-field">
										<option></option>
										<?php
											foreach($attribute_dropdown as $drop_key => $drop_value){
												if(array_key_exists("woo_suggest", $row_v) ){
													if($row_v['woo_suggest'] == $drop_key){
														print "<option value='$drop_key' selected>$drop_value</option>";
													} else {
														print "<option value='$drop_key'>$drop_value</option>";
													}
												} else {
													print "<option value='$drop_key'>$drop_value</option>";
												}
											}
										?>
										</select>
									</td>
                							<td>
										<input type="text" name="attributes[<?php print "$c";?>][suffix]" class="input-field-medium">
									</td>
								</tr>
								<?php
								$c++;
								}
							}
						}
					} else {
						for ($i = 0; $i < $count_mappings; $i++){
							if(isset($project['attributes'][$i]['prefix'])){
								$prefix = $project['attributes'][$i]['prefix'];
							}	
							if(isset($project['attributes'][$i]['suffix'])){
								$suffix = $project['attributes'][$i]['suffix'];
							}
							?>
							<tr>	
								<td><input type="hidden" name="attributes[<?php print "$i";?>][rowCount]" value="<?php print "$i";?>"><input type="checkbox" name="record" class="checkbox-field"></td>
						              	<td><i>field mapping</i></td>
								<td>
									<select name="attributes[<?php print"$i"; ?>][attribute]" class="select-field">
									<?php
										foreach($attributes as $key => $value) {
											print "<optgroup label='$key'><strong>$key</strong>";
											foreach($value as $k => $v){
												if ($project['attributes'][$i]['attribute'] == $v['feed_name']){
													print "<option value='$v[feed_name]' selected>$k ($v[name])</option>";
												} else {
													print "<option value='$v[feed_name]'>$k ($v[name])</option>";
												}
											}
										}
									?>
									</select>
								</td>
 								<td>
									<input type="text" name="attributes[<?php print "$i";?>][prefix]" class="input-field-medium" value="<?php print "$prefix";?>">
								</td>
								<td>
									<select name="attributes[<?php print "$i";?>][mapfrom]" class="select-field">
									<option></option>
									<?php
										foreach($attribute_dropdown as $drop_key => $drop_value){
											if($project['attributes'][$i]['mapfrom'] == $drop_key){
												print "<option value='$drop_key' selected>$drop_value</option>";
											} else {
												print "<option value='$drop_key'>$drop_value</option>";
											}
										}
									?>
									</select>
								</td>
                						<td>
									<input type="text" name="attributes[<?php print "$i";?>][suffix]" class="input-field-medium" value="<?php print "$suffix";?>">
								</td>
							</tr>
						<?php
						}					
					}
					?>
        			</tbody>
                                
				<tr>
					<td colspan="7">
                                        	<input type="hidden" id="channel_hash" name="channel_hash" value="<?php print "$project[channel_hash]";?>">
                                        	<?php
                                        	if(isset($manage_project)){
                                        	?>
							<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>">
        	        		                <input type="hidden" name="step" value="100">
                	               			<input type="button" class="delete-field-mapping" value="- Delete">&nbsp;<input type="button" class="add-field-mapping" value="+ Add field mapping">&nbsp;<input type="button" class="add-own-mapping" value="+ Add own mapping">&nbsp;<input type="submit" value="Save" />
	
						<?php
						} else {
						?>
							<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>">
                			                <input type="hidden" name="step" value="<?php print "$step";?>">
                               				<input type="button" class="delete-field-mapping" value="- Delete">&nbsp;<input type="button" class="add-field-mapping" value="+ Add field mapping">&nbsp;<input type="button" class="add-own-mapping" value="+ Add own mapping">&nbsp;<input type="submit" value="Save" />
						<?php
						}
						?>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
