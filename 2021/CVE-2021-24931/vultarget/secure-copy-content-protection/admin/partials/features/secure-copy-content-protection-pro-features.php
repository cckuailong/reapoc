<div class="copy_protection_wrap container-fluid">
    <h1 class="wp-heading-inline">
		<?php echo __(esc_html(get_admin_page_title()), $this->plugin_name); ?>
    </h1>

    <div id="features" class="only_pro">
        <div class="copy_protection_container form-group row">
            <div class="ays-sccp-features-wrap" style="width: 100%">
                <div class="comparison">
                    <table>
                        <thead>
                        <tr>
                            <th class="tl tl2"></th>
                            <th class="product"
                                style="background:#69C7F1; border-top-left-radius: 5px; border-left:0px;">
                                <span style="display: block"><?php echo __('Personal', $this->plugin_name) ?></span>
                                <img src="<?php echo SCCP_ADMIN_URL . '/images/avatars/personal_avatar.png'; ?>"
                                     alt="Free" title="Free" width="100"/>
                            </th>
                            <th class="product" style="background:#69C7F1;">
                                <span style="display: block"><?php echo __('Business', $this->plugin_name) ?></span>
                                <img src="<?php echo SCCP_ADMIN_URL . '/images/avatars/business_avatar.png'; ?>"
                                     alt="Business" title="Business" width="100"/>
                            </th>
                            <th class="product"
                                style="border-top-right-radius: 5px; border-right:0px; background:#69C7F1;">
                                <span style="display: block"><?php echo __('Developer', $this->plugin_name) ?></span>
                                <img src="<?php echo SCCP_ADMIN_URL . '/images/avatars/pro_avatar.png'; ?>"
                                     alt="Developer" title="Developer" width="100"/>
                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="price-info">
                                <div class="price-now"><span><?php echo __('Free', $this->plugin_name) ?></span></div>
                            </th>
                            <th class="price-info">
                                <div class="price-now">
                                    <span>$29</span>
                                </div>
                            </th>
                            <th class="price-info">
                                <div class="price-now">
                                    <span>$89</span>
                                </div>       
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Support for', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Support for', $this->plugin_name) ?></td>
                            <td><?php echo __('1 site', $this->plugin_name) ?></td>
                            <td><?php echo __('5 site', $this->plugin_name) ?></td>
                            <td><?php echo __('Unlimited sites', $this->plugin_name) ?></td>
                        </tr>    
                        <tr>
                            <td> </td>
                            <td colspan="3"><?php echo __('Upgrade for', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Upgrade for', $this->plugin_name) ?></td>
                            <td><?php echo __('1 months', $this->plugin_name) ?></td>
                            <td><?php echo __('12 months', $this->plugin_name) ?></td>
                            <td><?php echo __('Lifetime', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Support for', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Support for', $this->plugin_name) ?></td>
                            <td><?php echo __('1 months', $this->plugin_name) ?></td>
                            <td><?php echo __('12 months', $this->plugin_name) ?></td>
                            <td><?php echo __('Lifetime', $this->plugin_name) ?></td>
                        </tr>                                            
                        <tr>
                            <td> </td>
                            <td colspan="3"><?php echo __('Usage for lifetime', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Usage for lifetime', $this->plugin_name) ?></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td colspan="3"><?php echo __('Content Protection', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Content Protection', $this->plugin_name) ?></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td colspan="3"><?php echo __('Disable right-click', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Disable right-click', $this->plugin_name) ?></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td colspan="3"><?php echo __('Style settings', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Style settings', $this->plugin_name) ?></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td colspan="3"><?php echo __('Block content with password', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Block content with password', $this->plugin_name) ?></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td colspan="3"><?php echo __('Subscribe to view content', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Subscribe to view content', $this->plugin_name) ?></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Mailchimp integration', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Mailchimp integration', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Export results', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Export results', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Block by IP', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Block by IP', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Block by Country', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Block by Country', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>                        
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Front/back blocker', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Front/back blocker', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Block Rest api', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Block Rest api', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Protection by user roles', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Protection by user roles', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Protection by post/post type', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Protection by post/post type', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Watermark images', $this->plugin_name) ?></td>
                        </tr>
                        <tr class="compare-row">
                            <td><?php echo __('Watermark images', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"><?php echo __('Paid content via PayPal', $this->plugin_name) ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Paid content via PayPal', $this->plugin_name) ?></td>
                            <td><span>–</span></td>
                            <td><span>–</span></td>
                            <td><i class="ays_fa ays_fa_check"></i></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4"></td>
                        </tr>
                        <tr class="compare-row">
                            <td></td>
                            <td><a href="https://wordpress.org/plugins/secure-copy-content-protection/"
                                   class="price-buy"><?php echo __('Download', $this->plugin_name) ?><span
                                            class="hide-mobile"></span></a></td>
                            <td><a href="https://ays-pro.com/index.php/wordpress/secure-copy-content-protection"
                                   class="price-buy"><?php echo __('Buy now', $this->plugin_name) ?><span
                                            class="hide-mobile"></span></a></td>
                            <td><a href="https://ays-pro.com/index.php/wordpress/secure-copy-content-protection"
                                   class="price-buy"><?php echo __('Buy now', $this->plugin_name) ?><span
                                            class="hide-mobile"></span></a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>