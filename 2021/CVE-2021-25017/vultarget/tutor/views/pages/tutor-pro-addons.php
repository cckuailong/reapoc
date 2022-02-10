<div class="wrap">
    <div class="tutor-addons-list">
        <h3 class="addon-list-heading"><?php _e('Available Pro Addons', 'tutor'); ?></h3>
        <br class="clear">
		<?php
		$addons = apply_filters('tutor_pro_addons_lists_for_display', array());

		if (is_array($addons) && count($addons)){
			?>
            <div class="wp-list-table widefat plugin-install">
                <div id="the-list">
					<?php

					foreach ( $addons as $basName => $addon ) {
						$addonConfig = tutor_utils()->get_addon_config($basName);

						$addons_path = trailingslashit(tutor()->path."assets/addons/{$basName}");
						$addons_url = trailingslashit(tutor()->url."assets/addons/{$basName}");

						$thumbnailURL =  tutor()->url.'assets/images/tutor-plugin.png';

						if (file_exists($addons_path.'thumbnail.png') ){
							$thumbnailURL = $addons_url.'thumbnail.png';
                        }elseif (file_exists($addons_path.'thumbnail.jpg') ){
							$thumbnailURL = $addons_url.'thumbnail.jpg';
						}elseif (file_exists($addons_path.'thumbnail.svg')){
							$thumbnailURL = $addons_url.'thumbnail.svg';
						}

						?>
                        <div class="plugin-card plugin-card-akismet">
                            <div class="plugin-card-top">
                                <div class="name column-name">
                                    <h3>
										<?php
										echo $addon['name'];
										echo "<img src='{$thumbnailURL}' class='plugin-icon' alt=''>";
										?>
                                    </h3>
                                </div>
                                <div class="action-links">
                                    <ul class="plugin-action-buttons">
                                        <li>
                                            <a href="https://www.themeum.com/product/tutor-lms/?utm_source=tutor&utm_medium=addons_lists&utm_campaign=tutor_addons_lists"
                                               class="addon-buynow-link" target="_blank">Buy Now</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="desc column-description">
                                    <p><?php echo $addon['description']; ?></p>
                                    <p class="authors"><cite>By <a href="https://www.themeum.com/?utm_source=tutor&utm_medium=addons_lists&utm_campaign=tutor_addons_lists" target="_blank">Themeum</a></cite></p>
                                </div>
                            </div>
                            <div class="plugin-card-bottom">
								<?php
                                echo "<div class='plugin-version'> " . __( 'Version', 'tutor' ) . " : ".TUTOR_VERSION." </div>";
								?>
                            </div>
                        </div>
					<?php }
					?>
                </div>
            </div>

            <br class="clear">
			<?php
		}
		?>
    </div>
</div>