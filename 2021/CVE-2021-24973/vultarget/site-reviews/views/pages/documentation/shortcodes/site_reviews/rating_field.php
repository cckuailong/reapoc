<p class="glsr-heading">rating_field</p>
<div class="components-notice is-info">
    <p class="components-notice__content">Custom rating fields can be added with the <a href="<?= glsr_admin_url('addons'); ?>">Review Forms</a> add-on.</p>
</div>
<p>Include the "rating_field" option to make the "rating" option apply to the value of a custom rating field. Use the custom rating Field Name as the value.</p>
<p>The default rating_field value is: <code>""</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews rating_field="sound_rating"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">rating_field</span>=<span class="attr-value">"sound_rating"</span><span class="tag">]</span></code></pre>
</div>
