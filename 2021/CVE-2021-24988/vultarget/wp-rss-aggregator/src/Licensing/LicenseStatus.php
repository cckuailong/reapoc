<?php

namespace RebelCode\Wpra\Core\Licensing;

/**
 * An enum-style class for license statuses.
 *
 * @since 4.13
 */
abstract class LicenseStatus
{
    /**
     * License status for when a license key is valid and active.
     *
     * @since 4.13
     */
    const VALID = 'valid';

    /**
     * License status for when a license key is valid but inactive.
     *
     * @since 4.13
     */
    const INACTIVE = 'inactive';

    /**
     * License status for when a license is invalid and (consequently) inactive.
     *
     * @since 4.13
     */
    const INVALID = 'invalid';

    /**
     * License status for when a license key is valid but inactive for the current site.
     *
     * @since 4.13
     */
    const SITE_INACTIVE = 'site_inactive';

    /**
     * License status for when a license key has expired and is thus invalid and inactive.
     *
     * @since 4.13
     */
    const EXPIRED = 'expired';
}
