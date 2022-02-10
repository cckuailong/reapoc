<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-readme">
            <span class="title">Read me first!</span>
            <span class="badge important code">Important</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-readme" class="inside">
        <p>Hooks (also known as <a href="https://developer.wordpress.org/plugins/hooks/">Actions and Filters</a>) are used to make changes to the plugin without directly editing the plugin.</p>
        <p>If you need to use a hook to customise the plugin, I recommend one of two options:</p>
        <ol>
            <li>Use them with the <a href="<?= admin_url('plugin-install.php?s=ver3&tab=search&type=author'); ?>">Code Snippets</a> plugin</li>
            <li>Use them in the <span class="code">functions.php</span> file of a <strong>Child Theme</strong>.</li>
        </ol>
        <p>A child theme allows you to change small aspects of your site’s appearance yet still preserve your theme’s look and functionality. Using a child theme lets you upgrade the parent theme without affecting the customizations you’ve made to your website.</p>
        <p>To generate a child theme for your theme, you can either follow the directions in the <a href="https://developer.wordpress.org/themes/advanced-topics/child-themes/">WordPress handbook</a>, or use a child theme generator plugin such as <a href="<?= admin_url('plugin-install.php?s=essentialthemes&tab=search&type=author'); ?>">Child Theme X</a>.</p>
    </div>
</div>
