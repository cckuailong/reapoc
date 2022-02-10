<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('init', 'wpedon_button_media_buttons_init');

function wpedon_button_media_buttons_init() {
	global $pagenow, $typenow;

	// add media button for editor page
	if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) && $typenow != 'download' ) {
		
		add_action('admin_footer', 'wpedon_button_add_inline_popup_content');
		add_action('media_buttons', 'wpedon_button_add_my_media_button', 20);
		
		// button
		function wpedon_button_add_my_media_button() {
			echo '<a href="#TB_inline?width=600&height=500&inlineId=wpedon_popup_container" title="Insert a PayPal Donation Button" id="insert-my-media" class="button thickbox">PayPal Donation Button</a>';
		}
		
		// popup
		function wpedon_button_add_inline_popup_content() {
		?>
		
			
		<script type="text/javascript">
			function wpedon_button_InsertShortcode() {
			
				var id = document.getElementById("wpedon_button_id").value;
				var wpedon_alignmentc = document.getElementById("wpedon_align");
				var wpedon_alignmentb = wpedon_alignmentc.options[wpedon_alignmentc.selectedIndex].value;
				
				if(id == "No buttons found.") { alert("Error: Please select an existing button from the dropdown or make a new one."); return false; }
				if(id == "") { alert("Error: Please select an existing button from the dropdown or make a new one."); return false; }
				
				if(wpedon_alignmentb == "none") { var wpedon_alignment = ""; } else { var wpedon_alignment = ' align="' + wpedon_alignmentb + '"'; };
				
				window.send_to_editor('[wpedon id="' + id + '"' + wpedon_alignment + ']');
				
				document.getElementById("wpedon_button_id").value = "";
				wpedon_alignmentc.selectedIndex = null;
			}
		</script>

		
		<div id="wpedon_popup_container" style="display:none;">
		
		
			<h2>Insert a PayPal Donation Button</h2>

			<table><tr><td>
			
			Choose an existing button: </td></tr><tr><td>
			<select id="wpedon_button_id" name="wpedon_button_id">
				<?php
				$args = array('post_type' => 'wpplugin_don_button','posts_per_page' => -1);

				$posts = get_posts($args);

				$count = "0";
				
				if (isset($posts)) {
					
					foreach ($posts as $post) {

						$id = $posts[$count]->ID;
						$post_title = $posts[$count]->post_title;
						$price = get_post_meta($id,'wpedon_button_price',true);
						$sku = get_post_meta($id,'wpedon_button_id',true);

                        printf('<option value="%d">Name: %s - Amount: %s - ID: %s</option>',
                            $id,
                            esc_html($post_title),
                            esc_html($price),
                            esc_html($sku)
                        );

						$count++;
					}
				}
				else {
					echo "<option>No buttons found.</option>";
				}
				
				?>
			</select>
			</td></tr><tr><td>
			Make a new button: <a target="_blank" href="admin.php?page=wpedon_buttons&action=new">here</a><br />
			Manage existing buttons: <a target="_blank" href="admin.php?page=wpedon_buttons">here</a>
			
			</td></tr><tr><td>
			<br />
			</td></tr><tr><td>
			
			Alignment: </td></tr><tr><td>
			<select id="wpedon_align" name="wpedon_align" style="width:100%;max-width:190px;">
			<option value="none"></option>
			<option value="left">Left</option>
			<option value="center">Center</option>
			<option value="right">Right</option>
			</select> </td></tr><tr><td>Optional
			
			</td></tr><tr><td>
			<br />
			</td></tr><tr><td>
			
			<input type="button" id="wpedon-paypal-insert" class="button-primary" onclick="wpedon_button_InsertShortcode();" value="Insert Button">		
			
			</td></tr></table>
		</div>
		<?php
		}
	}
}