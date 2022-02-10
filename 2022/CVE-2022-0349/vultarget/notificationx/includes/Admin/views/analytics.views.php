<div class="nx-analytics-header-counter-wrapper <?php echo esc_attr( $class ); ?>">
    <div class="nx-header-analytics-counter-wrapper">
        <div>
            <div class="nx-header-analytics-counter">
                <a href="<?php echo esc_url( $views_link );?> ">
                    <span class="nx-counter-icon">
                        <img src="<?php echo esc_url( self::ASSET_URL . 'images/analytics/views-icon.png' ); ?>" alt="<?php esc_html_e( 'Total Views', 'notificationx' ); ?>">
                    </span>
                    <div>
                        <span class="nx-counter-number"><?php echo esc_html( $views ); ?></span>
                        <span class="nx-counter-label"><?php esc_html_e( 'Total Views', 'notificationx' ); ?></span>
                    </div>
                </a>
            </div>
        </div>
        <div>
            <div class="nx-header-analytics-counter">
                <a href="<?php echo esc_url( $clicks_link );?> ">
                    <span class="nx-counter-icon">
                        <img src="<?php echo esc_url( self::ASSET_URL . 'images/analytics/clicks-icon.png' ); ?>" alt="<?php esc_html_e( 'Total Clicks', 'notificationx' ); ?>">
                    </span>
                    <div>
                        <span class="nx-counter-number"><?php echo esc_html( $clicks ); ?></span>
                        <span class="nx-counter-label"><?php esc_html_e( 'Total Clicks', 'notificationx' ); ?></span>
                    </div>
                </a>
            </div>
        </div>
        <div>
            <div class="nx-header-analytics-counter">
                <a href="<?php echo esc_url( $ctr_link );?> ">
                    <span class="nx-counter-icon">
                        <img src="<?php echo esc_url( self::ASSET_URL . 'images/analytics/ctr-icon.png' ); ?>" alt="<?php esc_html_e( 'Click-Through-Rate', 'notificationx' ); ?>">
                    </span>
                    <div>
                        <span class="nx-counter-number"><?php echo esc_html( round( $ctr, 2 ) ); ?>%</span>
                        <span class="nx-counter-label"><?php esc_html_e( 'Click-Through-Rate', 'notificationx' ); ?></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
