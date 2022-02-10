<?php
/**
 * Freemius Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\Freemius;

use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;

/**
 * FreemiusReviews Extension
 */
class FreemiusReviews extends Extension {
    /**
     * Instance of Freemius
     *
     * @var Freemius
     */
    use GetInstance;
    use Freemius;

    public $priority = 20;
    public $id       = 'freemius_reviews';
    public $types    = 'reviews';
    public $img      = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/freemius.png';
    public $doc_link = 'https://notificationx.com/docs/freemius-sales-notification/';
    public $module   = 'modules_freemius';
    public $is_pro   = true;

    /**
     * Initially Invoked when initialized.
     */
    public function __construct(){
        $this->title = __('Freemius', 'notificationx');
        $this->module_title = __('Freemius', 'notificationx');
        parent::__construct();
    }

}
