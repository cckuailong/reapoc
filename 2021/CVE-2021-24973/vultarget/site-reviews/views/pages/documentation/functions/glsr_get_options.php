<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_get_options">
            <span class="title">Get all plugin settings</span>
            <span class="badge code">glsr_get_options()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_get_options" class="inside">
        <pre><code class="language-php">/**
 * @return array
 */
glsr_get_options();</code></pre>
        <p>This function returns an array of all of the plugin settings.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">$pluginSettings = glsr_get_options();

// OR:

$pluginSettings = apply_filters('glsr_get_options', []);</code></pre>
        <p><strong>Helpful Tip:</strong></p>
        <p>You can use the <code><a href="<?= glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_debug">glsr_debug</a></code> helper function to print the settings array to the screen:</p>
        <pre><code class="language-php">glsr_debug($pluginSettings);</code></pre>
    </div>
</div>
