<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-readme">
            <span class="title">Read me first!</span>
            <span class="badge important code">Important</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-readme" class="inside">
        <p>The problem with using plugin-specific functions is that they only exist when the plugin is active. When the plugin is disabled, any helper functions that have been used will throw a PHP error unless you have used a <a href="https://php.net/manual/en/function.function-exists.php">function_exists</a> check.</p>
        <p>Site Reviews provides an alternative way of using these functions which is much safer:</p>
        <pre><code class="language-php">/**
 * @param string $function_name (required) This is the name of the function you want to use
 * @param mixed $fallback (required) This value is returned when the function does not exist
 * @param mixed ...$args (optional) These are the arguments (one or more) required by the function
 * @return mixed
 */
apply_filters($function_name, $fallback, ...$args);</code></pre>
        <p>All functions listed here can be used in this way!</p>
        <p>The benefit of using this method is that you don't have to include a <a href="https://php.net/manual/en/function.function-exists.php">function_exists</a> check, and you can also provide a fallback value that is returned if the plugin is not available or active.</p>
        <p>For example:</p>
        <pre><code class="language-php">$reviews = apply_filters('glsr_get_reviews', [], [
    'assigned_posts' => 'post_id',
]);</code></pre>
        <p>Is the same as:</p>
        <pre><code class="language-php">$reviews = [];
if (function_exists('glsr_get_reviews')) {
    $reviews = glsr_get_reviews([
        'assigned_posts' => 'post_id',
    ]);
}</code></pre>
    </div>
</div>
