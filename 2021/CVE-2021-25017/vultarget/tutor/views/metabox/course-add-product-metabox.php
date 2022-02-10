<?php
/**
 * Add product metabox
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @since v.1.0.0
 */


$_tutor_course_price_type = tutils()->price_type();

?>

<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for="">
			<?php _e('Select product', 'tutor'); ?> <br />
            <p class="text-muted">(<?php _e('When selling the course', 'tutor'); ?>)</p>
        </label>
    </div>
    <div class="tutor-option-field">
		<?php
		$products = tutor_utils()->get_wc_products_db();
		$product_id = tutor_utils()->get_course_product_id();
		?>

        <select name="_tutor_course_product_id" class="tutor_select2" style="min-width: 300px;">
            <option value="-1"><?php _e('Select a Product'); ?></option>
			<?php
			foreach ($products as $product){
			    if ($product->ID == $product_id){
				    echo "<option value='{$product->ID}' ".selected($product->ID, $product_id)." >{$product->post_title}</option>";
			    }
			    $usedProduct = tutor_utils()->product_belongs_with_course($product->ID);
			    if ( ! $usedProduct){
				    echo "<option value='{$product->ID}' ".selected($product->ID, $product_id)." >{$product->post_title}</option>";
			    }
			}
			?>
        </select>

        <p class="desc">
            <a href="<?php echo get_edit_post_link($product_id); ?>" target="_blank"><?php _e('Edit attached Product', 'tutor'); ?></a> <br />
			<?php _e("Select a product if you want to sell your course. The sale will be handled by your preferred monetization option. (WooCommerce, EDD, Paid Memberships Pro)", 'tutor'); ?>
        </p>
    </div>
</div>


<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for="">
			<?php _e('Course Type', 'tutor'); ?> <br />
        </label>
    </div>
    <div class="tutor-option-field">

        <label>
            <input id="tutor_course_price_type_pro" type="radio" name="tutor_course_price_type" value="paid" <?php checked($_tutor_course_price_type, 'paid'); ?> >
			<?php _e('Paid', 'tutor'); ?>
        </label>
        <label>
            <input type="radio" name="tutor_course_price_type" value="free"  <?php $_tutor_course_price_type ? checked($_tutor_course_price_type, 'free') : checked('true', 'true'); ?> >
			<?php _e('Free', 'tutor'); ?>
        </label>
    </div>
</div>
