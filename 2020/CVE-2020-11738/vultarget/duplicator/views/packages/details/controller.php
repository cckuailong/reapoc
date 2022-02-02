<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
DUP_Util::hasCapability('manage_options');
global $wpdb;

//COMMON HEADER DISPLAY
$current_tab = isset($_REQUEST['tab']) ? sanitize_text_field($_REQUEST['tab']) : 'detail';
$package_id  = isset($_REQUEST["id"])  ? sanitize_text_field($_REQUEST["id"]) : 0;

$package			= DUP_Package::getByID($package_id);
$err_found		    = ($package == null || $package->Status < 100);
$link_log			= "{$package->StoreURL}{$package->NameHash}.log";
$err_link_log		= "<a target='_blank' href='".esc_url($link_log)."' >" . esc_html__('package log', 'duplicator') . '</a>';
$err_link_faq		= '<a target="_blank" href="https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=pkg_details_faq">' . esc_html__('FAQ', 'duplicator') . '</a>';
$err_link_ticket	= '<a target="_blank" href="https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=pkg_details_resources#faq-resource">' . esc_html__('resources page', 'duplicator') . '</a>';
?>

<style>
    .narrow-input { width: 80px; }
    .wide-input {width: 400px; }
	 table.form-table tr td { padding-top: 25px; }
	 div.all-packages {float:right; margin-top: -35px; }
</style>

<div class="wrap">
    <?php
		duplicator_header(__("Package Details &raquo; {$package->Name}", 'duplicator'));
	?>

	<?php if ($err_found) :?>
	<div class="error">
		<p>
			<?php echo esc_html__('This package contains an error.  Please review the ', 'duplicator') . $err_link_log .  esc_html__(' for details.', 'duplicator'); ?>
			<?php echo esc_html__('For help visit the ', 'duplicator') . $err_link_faq . esc_html__(' and ', 'duplicator') . $err_link_ticket; ?>
		</p>
	</div>
	<?php endif; ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=duplicator&action=detail&tab=detail&id=<?php echo absint($package_id); ?>" class="nav-tab <?php echo ($current_tab == 'detail') ? 'nav-tab-active' : '' ?>">
			<?php esc_html_e('Details', 'duplicator'); ?>
		</a>
		<a href="?page=duplicator&action=detail&tab=transfer&id=<?php echo absint($package_id); ?>" class="nav-tab <?php echo ($current_tab == 'transfer') ? 'nav-tab-active' : '' ?>">
			<?php esc_html_e('Transfer', 'duplicator'); ?>
		</a>
    </h2>
	<div class="all-packages"><a href="?page=duplicator" class="button"><i class="fa fa-archive fa-sm"></i> <?php esc_html_e('Packages', 'duplicator'); ?></a></div>

    <?php
    switch ($current_tab) {
        case 'detail': include('detail.php');
            break;
		case 'transfer': include('transfer.php');
            break;
    }
    ?>
</div>
