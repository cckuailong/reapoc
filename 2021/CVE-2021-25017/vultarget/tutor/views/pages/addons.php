<?php
$current_page = tutor_utils()->avalue_dot('tab', $_GET);
$page_name = $current_page ? $current_page : 'addons';
?>

<div class="wrap plugin-install-tab-featured tutor-addons">
    <h1 class="wp-heading-inline"><?php _e('Tutor Add-ons'); ?></h1>

    <hr class="wp-header-end">

    <div class="wp-filter">
        <ul class="filter-links">
            <li class="tutor-available-addons <?php echo $page_name === 'addons' ? 'current' : ''; ?> "><a href="<?php echo admin_url('admin.php?page=tutor-addons') ?>" aria-current="page"><?php _e('Plugins', 'tutor'); ?></a> </li>
            <li class="tutor-available-themes <?php echo $page_name === 'themes' ? 'current' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=tutor-addons&tab=themes') ?>"><?php _e('Themes', 'tutor'); ?></a> </li>
        </ul>
    </div>

    <br class="clear">

    <form id="plugin-filter" method="post">
        <div class="wp-list-table widefat plugin-install">
			<?php
			$last_checked_time = tutor_utils()->avalue_dot('last_checked_time', $addons_themes_data);
			if ($last_checked_time){
				$last_checked_time = tutor_utils()->avalue_dot('last_checked_time', $addons_themes_data);
				$data = json_decode(tutor_utils()->avalue_dot('data', $addons_themes_data));

				if ($current_page === 'themes'){
					$addons = tutor_utils()->avalue_dot('theme', $data);
				}else{
					$addons = tutor_utils()->avalue_dot('addon', $data);
				}
				?>

                <p class="tutor-addons-last-checked-time">
                    <?php echo sprintf(__('Last checked %s ago, It will check again after %s from now') , human_time_diff($last_checked_time),  human_time_diff(tutor_time(), $last_checked_time + 6 * HOUR_IN_SECONDS) ); ?>
                </p>

                <div id="the-list">
					<?php
                    if (is_array($addons) && count($addons)) {
                        foreach ( $addons as $addon ) {
							?>
                            <div class="plugin-card plugin-card-akismet">
                                <div class="plugin-card-top">
                                    <div class="name column-name">
                                        <h3>
											<?php
											echo "<a href='{$addon->product_url}' target='_blank'>{$addon->product_name}</a>";
											if ( $addon->thumbnail ) {
												echo "<img src='{$addon->thumbnail}' class='plugin-icon' alt=''>";
											}
											?>
                                        </h3>
                                    </div>
                                    <div class="action-links">
                                        <ul class="plugin-action-buttons">
                                            <li><a href="<?php echo $addon->product_url; ?>" class="button button-primary activate-now"
                                                   target="_blank">  <?php _e( 'Buy Now', 'tutor' ); ?></a></li>

                                            <li>
												<?php
												echo '<span class="addon-regular-price"><del>' . $addon->regular_price . '</del></span>';
												echo '<span class="addon-current-price">' . $addon->price . '</span>';
												?>
                                            </li>

                                        </ul>
                                    </div>
                                    <div class="desc column-description">
										<?php echo $addon->short_description ? "<p>{$addon->short_description}</p>" : ''; ?>

                                        <p class="authors"><cite>By <a href="https://www.themeum.com" target="_blank">Themeum</a></cite></p>
                                    </div>
                                </div>
                                <div class="plugin-card-bottom">
									<?php
									if ( $addon->version ) {
										echo "<div class='plugin-version'> " . __( 'Version', 'tutor' ) . " : {$addon->version}</div>";
									}
									?>
                                    <!--
									<div class="column-updated">
										<strong>Last Updated:</strong> 4 months ago
									</div>-->
                                </div>
                            </div>
						<?php }
					}else{
	                    echo sprintf(__('No %s currently avaialable', 'tutor'), $page_name);
                    } ?>
                </div>
			<?php }else{
				echo sprintf(__('No %s currently avaialable', 'tutor'), $page_name);
			} ?>

        </div>
    </form>

    <span class="spinner"></span>
</div>