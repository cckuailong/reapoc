<div class="dlm_mb_shop_product_information">

	<?php do_action( 'dlm_mb_product_information_start', $product->get_id(), $product ); ?>

    <p class="dlm_shop_field_row">
        <label class="dlm_shop_field_label"><?php printf( __( 'Price (%s)', 'download-monitor' ), \Never5\DownloadMonitor\Shop\Services\Services::get()->service( 'currency' )->get_currency_symbol() ); ?></label>
        <span class="dlm_shop_field_input">
        <input type="text" name="_dlm_price" value="<?php echo $price; ?>" class="dlm_shop_input" >
        </span>
    </p>

    <p class="dlm_shop_field_row">
        <label class="dlm_shop_field_label"><?php printf( __( 'Downloads', 'download-monitor' ), \Never5\DownloadMonitor\Shop\Services\Services::get()->service( 'currency' )->get_currency_symbol() ); ?></label>
        <span class="dlm_shop_field_input">

            <select id="dlm_downloads" name="_dlm_downloads[]"
                    multiple="true"
                    data-placeholder="<?php _e( 'Select Downloads&hellip;', 'download-monitor' ); ?>"
                    class="dlm-select-ext dlm_shop_input">
                <?php if ( ! empty( $downloads ) ) : ?>
	                <?php foreach ( $downloads as $download ) : ?>
                        <option value="<?php echo esc_attr( $download->get_id() ); ?>" <?php selected( in_array( $download->get_id(), $current_download_ids ), true ); ?>><?php echo esc_html( $download->get_title() ); ?></option>
	                <?php endforeach; ?>
                <?php endif; ?>
            </select>

        </span>
    </p>

	<?php /*
    <p>
        <label><?php _e( 'Taxable', 'download-monitor' ); ?></label>
        <input type="checkbox" name="_dlm_taxable" value="1" <?php checked( true, $taxable ); ?>/>
    </p>

    <p>
        <label><?php _e( 'Tax Class', 'download-monitor' ); ?></label>
        <select name="_dlm_tax_class">
			<?php
			$classes = \Never5\DownloadMonitor\Shop\Services\Services::get()->service( 'tax_class_manager' )->get_tax_rates();
			if ( count( $classes ) > 0 ) {
				foreach ( $classes as $class ) {
					echo "<option value='" . $class . "'" . selected( $tax_class, $class ) . ">" . $class . " " . __( 'rate', 'download-monitor' ) . "</option>";
				}
			}
			?>
        </select>
    </p>
 */ ?>

	<?php do_action( 'dlm_mb_product_information_end', $product->get_id(), $product ); ?>

</div>
