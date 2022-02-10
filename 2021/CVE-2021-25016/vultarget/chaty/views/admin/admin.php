<?php
if (!defined('ABSPATH')) {
    exit;
}
$is_pro = $this->is_pro();
if (!$is_pro) {
    if (get_option('cht_position') == 'custom') {
        update_option('cht_position', 'right');
    }
    $social = get_option('cht_numb_slug');
    $social = explode(",", $social);
    $social = array_splice($social, 0, 3);
    $social = implode(',', $social);
    update_option('cht_numb_slug', $social);
    if (get_option('cht_custom_color') != '') {
        update_option('cht_custom_color', '');
        update_option('cht_color', '#A886CD');
    }
}
$cht_license_key = get_option('cht_license_key');
$pro_class = (!$is_pro && $cht_license_key !== "")?"none_pro":"";
?>
<h2></h2>
<div class="container <?php esc_attr_e($pro_class) ?>" dir="ltr">
    <header class="header">
        <img src="<?php echo esc_url(CHT_PLUGIN_URL.'admin/assets/images/logo.svg'); ?>" alt="Chaty" class="logo">
        <?php settings_errors(); ?>
        <div class="ml-auto">
            <a class="btn-white" href="<?php echo esc_url(admin_url("admin.php?page=chaty-upgrade")) ?>">
                <?php esc_attr_e('Create New Widget', CHT_OPT); ?>
            </a>
            <a target="_blank" class="btn-red" href="<?php echo esc_url($this->getUpgradeMenuItemUrl()); ?>">
                <?php esc_attr_e('Upgrade Now', CHT_OPT); ?>
                <svg width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.4674 7.42523L11.8646 0.128021C11.7548 0.128021 11.6449 0 11.4252 0C11.3154 0 11.0956 0 10.9858 0.128021L9.44777 1.92032C9.22806 2.17636 9.22806 2.56042 9.33791 2.81647L11.7548 6.017H0.549289C0.219716 6.017 0 6.27304 0 6.6571V9.21753C0 9.60159 0.219716 9.85763 0.549289 9.85763H11.8646L9.44777 13.0582C9.22806 13.3142 9.22806 13.6983 9.44777 13.9543L11.0956 15.6186C11.2055 15.7466 11.3154 15.7466 11.4252 15.7466C11.5351 15.7466 11.7548 15.6186 11.8646 15.4906L17.4674 8.19336C17.5772 8.06534 17.5772 7.68127 17.4674 7.42523Z" transform="translate(0.701416 18.3653) rotate(-90)" fill="white"/>
                </svg>
            </a>
        </div>
    </header>

    <main class="main">
        <form id="cht-form" action="options.php" method="POST" enctype="multipart/form-data">
            <?php
            settings_fields($this->plugin_slug);

            /* Social channel list section */
            require_once 'channels-section.php';

            /* Customize widget section */
            require_once 'customize-widget-section.php';

            /* Customize widget section */
            require_once 'trigger-and-target.php';

            /* Widget launch section */
            require_once 'launch-section.php';

            /* form submit button */
            submit_button(null, null, null, false);
            ?>
        </form>
    </main>

    <?php require_once 'help.php'; ?>
</div>
<?php require_once 'popup.php'; ?>
