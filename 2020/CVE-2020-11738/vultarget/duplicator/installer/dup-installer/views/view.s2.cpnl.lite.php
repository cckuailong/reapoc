<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

?>

<div class="s2-gopro">
    <h2>cPanel Connectivity</h2>

<?php if (DUPX_U::isURLActive("https://{$_SERVER['HTTP_HOST']}", 2083)): ?>
	<div class='s2-cpanel-login'>
		<b>Login to this server's cPanel</b><br/>
		<a href="<?php echo DUPX_U::esc_url('https://'.$_SERVER['SERVER_NAME'].':2083'); ?>" target="cpanel" style="color:#fff">[<?php echo DUPX_U::esc_html($_SERVER['SERVER_NAME']); ?>:2083]</a>
	</div>
<?php else : ?>
	<div class='s2-cpanel-off'>
		<b>This server does not appear to support cPanel!</b><br/>
		Consider <a href="https://snapcreek.com/wordpress-hosting/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_install_no_cpanel&utm_campaign=duplicator_pro" target="cpanel" style="color:#fff;font-weight:bold">upgrading</a> to a host that does.<br/>
	</div>
<?php endif; ?>


    <div style="text-align: center; font-size: 14px">
        Want <span style="font-style: italic;">even easier</span> installs?  
        <a target="_blank" href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&amp;utm_medium=wordpress_plugin&amp;utm_content=free_install_step2&amp;utm_campaign=duplicator_pro"><b>Duplicator Pro</b></a>
        allows the following <b>right from the installer:</b>
    </div>
    <ul>
        <li>Directly login to cPanel</li>
        <li>Instantly create new databases &amp; users</li>
        <li>Preview and select existing databases  &amp; users</li>
    </ul>
    <small>
        Note: Hosts that support cPanel provide remote access to server resources, allowing operations such as direct database and user creation.
        Since the <a target="_blank" href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_install_cpanel_note&utm_campaign=duplicator_pro">Duplicator Pro</a>
        installer can directly access cPanel, it dramatically speeds up your workflow.
    </small>
</div>