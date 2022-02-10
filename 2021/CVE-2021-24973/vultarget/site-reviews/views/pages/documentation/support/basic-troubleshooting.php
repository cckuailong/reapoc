<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-basic-troubleshooting">
            <span class="title">Basic Troubleshooting Steps</span>
            <span class="badge code important">Do this first</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-basic-troubleshooting" class="inside">
        <h3>1. Make sure you are using the latest version of Site Reviews.</h3>
        <p>Site Reviews is updated frequently with bug patches, security updates, improvements, and new features. If you are not using the latest version and are experiencing problems, chances are good that your problem has already been addressed in the latest version.</p>
        <h3>2. Run the repair tools.</h3>
        <p>If you have recently upgraded to a new major version of Site Reviews and your reviews have stopped working, you may need to run the <code><a data-expand="#tools-migrate-plugin" href="<?= glsr_admin_url('tools', 'general'); ?>">Migrate Plugin</a></code> and <code><a data-expand="#tools-reset-assigned-meta" href="<?= glsr_admin_url('tools', 'general'); ?>">Reset Assigned Meta Values</a></code> tools. Normally you should see a notice on the WordPress Dashboard and on the Site Reviews admin pages when the database settings need upgrading; however, if you are experiencing problems with your reviews after updating Site Reviews, you can also run these tools manually.</p>
        <h3>3. Temporarily switch to an official WordPress Theme.</h3>
        <p>Try switching to an official WordPress Theme (i.e. Twenty Twenty) and then see if you are still experiencing problems with the plugin. If this fixes the problem then there is a compatibility issue with your theme.</p>
        <h3>4. Temporarily deactivate all of your plugins.</h3>
        <p>If switching to an official WordPress theme did not fix anything, the final thing to try is to deactivate all of your plugins except for Site Reviews. If this fixes the problem then there is a compatibility issue with one of your plugins.</p>
        <p>To find out which plugin is incompatible with Site Reviews you will need to reactivate your plugins one-by-one until you find the plugin that is causing the problem. If you think that you’ve found the culprit, deactivate it and continue to test the rest of your plugins. Hopefully you won’t find any more but it’s always better to make sure.</p>
        <div class="components-notice is-info">
            <p class="components-notice__content">If you find an incompatible theme or plugin, please <code><a data-expand="#support-contact-support" href="<?= glsr_admin_url('documentation', 'support'); ?>">contact support</a></code> so we can fix it.</p>
        </div>
    </div>
</div>
