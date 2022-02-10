<?php
/*
** PPOM Existing Meta Template
*/

/* 
**========== Direct access not allowed =========== 
*/ 
if( ! defined('ABSPATH') ) die('Not Allowed');

$all_forms = PPOM() -> get_product_meta_all();
?>

<div class="wrapper">
	<h2 class="ppom-heading-style"><?php _e( 'PPOM Meta List', "ppom"); ?></h2>
</div>


<div class="ppom-existing-meta-wrapper">
	
	<form method="post" action="admin-post.php" enctype="multipart/form-data">
		<input type="hidden" name="action" value="ppom_export_meta" />

		<div class="ppom-product-table-header">
			
			<span class="ppom-product-count-span"><?php _e( 'Select', "ppom"); ?> "<span id="selected_products_count">0</span>"<?php _e( ' PPOM Meta', "ppom"); ?></span>
			<button class="btn btn-danger" id="ppom_delete_selected_products_btn"><?php _e( 'Delete', 'ppom' ) ?></button>
			<button class="btn btn-yellow" id="export_selected_products_btn"><?php _e( 'Export', "ppom"); ?></button>
			<span class="pull-right"><strong><?php echo count($all_forms); ?> <?php _e( 'Items', 'ppom' ); ?></strong></span>
			<span class="clear"></span>
		</div>
		<div class="table-responsive">
			<table id="ppom-meta-table" class="table">
				<thead>
					<tr class="ppom-thead-bg">
						<th class="ppom-checkboxe-style">
							<label>
								<input type="checkbox" name="allselected" id="ppom-all-select-products-head-btn">
								<span></span>
							</label>
						</th>
						<th><?php _e('Meta ID', "ppom")?></th>
						<th><?php _e('Name', "ppom")?></th>
						<th><?php _e('Meta', "ppom")?></th>
						<th><?php _e('Select Products', "ppom")?></th>
						<th><?php _e('Actions', "ppom")?></th>
					</tr>
				</thead>
				<tfoot>
					<tr class="ppom-thead-bg">
						<th class="ppom-checkboxe-style">
							<label>
								<input type="checkbox" name="allselected" id="ppom-all-select-products-foot-btn">
								<span></span>
							</label>
						</th>
						<th><?php _e('Meta ID', "ppom")?></th>
						<th><?php _e('Name', "ppom")?></th>
						<th><?php _e('Meta', "ppom")?></th>
						<th><?php _e('Select Products', "ppom")?></th>
						<th><?php _e('Actions', "ppom")?></th>
					</tr>
				</tfoot>
				
				<?php 
				
				foreach ($all_forms as $productmeta){
				
					$url_edit     = add_query_arg(array('productmeta_id'=> $productmeta ->productmeta_id, 'do_meta'=>'edit'));
					$url_clone    = add_query_arg(array('productmeta_id'=> $productmeta ->productmeta_id, 'do_meta'=>'clone'));
					$url_products = admin_url( 'edit.php?post_type=product', (is_ssl() ? 'https' : 'http') );
					$product_link = '<a href="'.esc_url($url_products).'">'.__('Products', "ppom").'</a>';
					?>
					<tr>
						<td class="ppom-meta-table-checkbox-mr ppom-checkboxe-style">
		                	<label>
								<input class="ppom_product_checkbox" type="checkbox" name="ppom_meta[]" value="<?php echo esc_attr($productmeta ->productmeta_id); ?>">
								<span></span>
							</label>
		                </td>

						<td><?php echo $productmeta ->productmeta_id; ?></td>
						<td>
							<a href="<?php echo esc_url($url_edit); ?>">
								<?php echo stripcslashes($productmeta -> productmeta_name)?>
							</a>
						</td>
						<td><?php echo ppom_admin_simplify_meta($productmeta -> the_meta)?></td>
						<td>
							<a class="btn btn-primary ppom-products-modal" data-ppom_id="<?php echo esc_attr($productmeta ->productmeta_id); ?>" data-formmodal-id="ppom-product-modal"><?php _e('Attach to Products', "ppom")?></a>
						</td>
						<td class="ppom-admin-meta-actions-colunm">
							<a id="del-file-<?php echo esc_attr($productmeta -> productmeta_id); ?>" href="#" class="button button-sm ppom-delete-single-product" data-product-id="<?php echo esc_attr($productmeta -> productmeta_id); ?>"><span class="dashicons dashicons-no"></span></a>
							<a href="<?php echo esc_url($url_edit); ?>" title="<?php _e('Edit', "ppom")?>" class="button"><span class="dashicons dashicons-edit"></span></a>
							<a href="<?php echo esc_url($url_clone); ?>" title="<?php _e('Clone', "ppom")?>" class="button"><span class="dashicons dashicons-image-rotate-right"></span></a>
						</td>
					</tr>
				<?php 
				}
				?>
			</table>
		</div>
	</form>
</div>

<!-- Product Modal -->
<div id="ppom-product-modal" class="ppom-modal-box" style="display: none;">
	<form id="ppom-product-form">		
		<input type="hidden" name="action" value="ppom_attach_ppoms"/>
        <input type="hidden" name="ppom_id" id="ppom_id">
	    
	    <header> 
	        <h3><?php _e('WooCommerce Products', "ppom");?></h3>
	    </header>

	    <div class="ppom-modal-body">

	    </div>
	    
	    <footer>
	    	<button type="button" class="btn btn-default close-model ppom-js-modal-close"><?php _e('Close' , 'ppom-addon-pdf'); ?></button>
	    	<button type="submit" class="btn btn-info"><?php _e('Save', "ppom"); ?></button>
	    </footer>
	</form>
</div>