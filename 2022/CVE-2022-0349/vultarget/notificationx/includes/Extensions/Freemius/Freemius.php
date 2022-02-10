<?php

/**
 * Freemius Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\Freemius;

/**
 * Common functionality for Freemius.
 */
trait Freemius
{

    public $module_priority = 12;

    public function doc(){
        return sprintf(__('<p>Make sure that you have <a target="_blank" href="%1$s">created & signed in to Freemius account</a> to use its campaign & product sales data. For further assistance, check out our step by step <a target="_blank" href="%2$s">documentation</a>.</p>
		<p>🎦 <a target="_blank" href="%3$s">Watch video tutorial</a> to learn quickly</p>
		<p>👉 NotificationX <a target="_blank" href="%4$s">Integration with Freemius</a></p>', 'notificationx'),
        'https://dashboard.freemius.com/login/',
        'https://notificationx.com/docs/freemius-sales-notification/',
        'https://youtu.be/0uANsOSFmtw',
        'https://notificationx.com/integrations/freemius/'
        );
    }
}
