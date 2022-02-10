<input type='hidden' name='catalog-id' value='<?php echo $this->catalog->ID; ?>' />
<input type='hidden' name='catalog-excluded-views' value='<?php echo implode( ',', $this->excluded_views ); ?>' />
<input type='hidden' name='catalog-current-page' value='<?php echo $this->current_page; ?>' />
<input type='hidden' name='catalog-max-page' value='<?php echo $this->max_pages; ?>' />
<input type='hidden' name='catalog-product-per-page' value='<?php echo $this->products_per_page; ?>' />
<input type='hidden' name='catalog-default-search-text' value='<?php _e( 'Search...', 'ultimate-product-catalogue' ); ?>' />
<input type='hidden' name='catalog-base-url' value='<?php echo $this->ajax_url; ?>' />