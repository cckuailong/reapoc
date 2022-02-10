<?php if (!defined('ABSPATH')) { exit; } ?>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins" />
<div class="chaty-new-widget-wrap">
    <h2 class="text-center"><?php esc_attr_e( 'Create a new Chaty widgets for your website. What can you use it for?', CHT_OPT ); ?></h2>
    <div class="chaty-new-widget-row">
        <div class="chaty-features">
            <ul>
                <li>
                    <div class="chaty-feature">
                        <div class="chaty-feature-top">
                            <img src="<?php echo CHT_PLUGIN_URL ?>/admin/assets/images/pro-devices.png" />
                        </div>
                        <div class="feature-title">Create separate designs for desktop and mobile</div>
                        <div class="feature-description">E.g. the mobile version can have a bigger widget, in a different color and a different position</div>
                    </div>
                </li>
                <li>
                    <div class="chaty-feature">
                        <div class="chaty-feature-top">
                            <img src="<?php echo CHT_PLUGIN_URL ?>/admin/assets/images/pro-language.png" />
                        </div>
                        <div class="feature-title">Do you have a multi-language website or WPML plugin installed?</div>
                        <div class="feature-description">You can show different form and buttons based on URL (E.g. WhatsApp message to a French number and call your French phone number)</div>
                    </div>
                </li>
                <li>
                    <div class="chaty-feature">
                        <div class="chaty-feature-top">
                            <img src="<?php echo CHT_PLUGIN_URL ?>/admin/assets/images/pro-widget.png" />
                        </div>
                        <div class="feature-description"><b>Show separate widgets for different products</b> on your website (e.g. you can show the Facebook Messenger channel for products in the yourdomain.com/high-end/* category)</div>
                    </div>
                </li>
                <li>
                    <div class="chaty-feature second">
                        <div class="chaty-feature-top">
                            <img src="<?php echo CHT_PLUGIN_URL ?>/admin/assets/images/pro-page.png" />
                        </div>
                        <div class="feature-title">Display different channels for your landing pages</div>
                        <div class="feature-description">This way you can track the results better and have the right person assign to the relevant channel.</div>
                    </div>
                </li>
                <li>
                    <div class="chaty-feature second">
                        <div class="chaty-feature-top">
                            <img src="<?php echo CHT_PLUGIN_URL ?>/admin/assets/images/pro-support.png" />
                        </div>
                        <div class="feature-title">Show one widget on your support and contact pages,</div>
                        <div class="feature-description"> and a different widget on your sales pages.</div>
                    </div>
                </li>
                <li>
                    <div class="chaty-feature second">
                        <div class="chaty-feature-top">
                            <img src="<?php echo CHT_PLUGIN_URL ?>/admin/assets/images/pro-chat.png" />
                        </div>
                        <div class="feature-title">Display different call-to-action messages</div>
                        <div class="feature-description">for different pages on your website or separate call-to-action messages for mobile and desktop</div>
                    </div>
                </li>
            </ul>
            <div class="clear clearfix"></div>
        </div>
        <a href="<?php echo esc_url($this->getUpgradeMenuItemUrl()); ?>" class="new-upgrade-button">Upgrade to Pro</a>
    </div>
</div>
