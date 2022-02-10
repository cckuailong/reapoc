<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_debug">
            <span class="title">Debug variables</span>
            <span class="badge code">glsr_debug()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_debug" class="inside">
        <pre><code class="language-php">/**
 * @param mixed ...$variable
 * @return void
 */
glsr_debug(...$variable);</code></pre>
        <p>This function prints one or more variables (strings, arrays, objects, etc.) to the screen in an easy-to-read format. You can include as many variables as you want separated by commas.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">glsr_debug($var1, $var2, $var3);

// OR:

apply_filters('glsr_debug', null, $var1, $var2, $var3);
</code></pre>
    </div>
</div>
