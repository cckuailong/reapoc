<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

$_POST['secure-pass'] = isset($_POST['secure-pass']) ? DUPX_U::sanitize_text_field($_POST['secure-pass']) : '' ;
$_POST['secure-try']  = isset($_POST['secure-try'])  ? 1 : 0 ;
$_GET['debug']        = isset($_GET['debug']) ? $_GET['debug'] : 0;
$page_url = DUPX_HTTP::get_request_uri();
$page_err = 0;
$pass_hasher = new DUPX_PasswordHash(8, FALSE);
$pass_check  = $pass_hasher->CheckPassword(base64_encode($_POST['secure-pass']), $GLOBALS['DUPX_AC']->secure_pass);

//FORWARD: password not enabled
if (! $GLOBALS['DUPX_AC']->secure_on && ! $_GET['debug']) {
	DUPX_HTTP::post_with_html($page_url, array(
		'action_step' => '1',
		'csrf_token' => DUPX_CSRF::generate('step1'),
		'bootloader' => $GLOBALS['BOOTLOADER_NAME'],
		'archive' => $GLOBALS['FW_PACKAGE_PATH'],
	));
	exit;
}

//POSTBACK: valid password
if ($pass_check) {
	DUPX_HTTP::post_with_html($page_url,
		array(
			'action_step' => '1',
			'csrf_token' => DUPX_CSRF::generate('step1'),
			'secure-pass' => $_POST['secure-pass'],
			'bootloader' => $GLOBALS['BOOTLOADER_NAME'],
			'archive' => $GLOBALS['FW_PACKAGE_PATH'],
		));
	exit;
}

//ERROR: invalid password
if ($_POST['secure-try'] && ! $pass_check) {
	$page_err = 1;
}
?>

<!-- =========================================
VIEW: STEP 0 - PASSWORD -->
<form method="post" id="i1-pass-form" class="content-form"  data-parsley-validate="" autocomplete="off" >
    <input type="hidden" name="view" value="secure" />
    <input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('secure'); ?>">
    <input type="hidden" name="secure-try" value="1" />
    <input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
    <input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />

    <div class="hdr-main">
        Installer Password
    </div>

<?php if ($page_err) : ?>
        <div class="error-pane">
            <p>Invalid Password! Please try again...</p>
        </div>
<?php endif; ?>

    <div style="text-align: center">
        This file was password protected when it was created.   If you do not remember the password	check the details of the package on	the site where it was created or visit
        the online FAQ for <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-030-q" target="_blank">more details</a>.
        <br/><br/><br/>
    </div>

    <div class="i1-pass-area">
        <div class="i1-pass-data">
            <label for="secure-pass">&nbsp; Enter Password</label>
            <?php
            DUPX_U_Html::inputPasswordToggle('secure-pass', 'secure-pass', array(),
                array(
                'required' => 'required'
            ));
            ?>
            <br><br>
            <button type="button" name="secure-btn" id="secure-btn" class="default-btn" onclick="DUPX.checkPassword()">Submit</button>
        </div>
    </div>

</form>

<script>
    /**
     * Submits the password for validation
     */
    DUPX.checkPassword = function ()
    {
        var $form = $('#i1-pass-form');
        $form.parsley().validate();
        if (!$form.parsley().isValid()) {
            return;
        }
        $form.submit();
    }
</script>
<!-- END OF VIEW INIT 1 -->