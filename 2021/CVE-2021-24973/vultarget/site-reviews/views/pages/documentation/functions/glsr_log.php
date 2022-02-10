<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_log">
            <span class="title">Log variables to the plugin console</span>
            <span class="badge code">glsr_log()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_log" class="inside">
        <pre><code class="language-php">/**
 * @param null|mixed $var
 * @return \GeminiLabs\SiteReviews\Modules\Console
 */
glsr_log($var = null);</code></pre>
        <p>This chainable function logs a variable (strings, arrays, objects, etc.) to the plugin console with a default logging level of "debug".</p>
        <p>There are eight available logging levels: <code>debug</code>, <code>info</code>, <code>notice</code>, <code>warning</code>, <code>error</code>, <code>critical</code>, <code>alert</code>, and <code>emergency</code>. Since glsr_log() returns an instance of the Console class, you can chain as many logging levels together as needed.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">glsr_log($var1);
glsr_log()->warning($var2);
glsr_log()->error($var4)->debug($var5);

// OR:

apply_filters('glsr_log', null, $var1);</code></pre>
    <p>Logged entries will be found in the <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code>.</p>
    </div>
</div>
