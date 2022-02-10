<p class="glsr-heading">schema</p>
<div class="components-notice is-warning">
    <p class="components-notice__content">This option should only be used once on a page to avoid duplicate schemas! Keep this in mind if you are using more than one <code>[site_reviews]</code> and/or <code>[site_reviews_summary]</code> shortcodes on the same page as both shortcodes allow you to use the schema option.</p>
</div>
<p>Include the "schema" option to enable the aggregate rating and review schema for your reviews in Google. The difference between this and the schema option in the [site_reviews_summary] shortcode is that this one generates both the aggregate ratings schema and the review schema for each individual review that is visible on the page, while the other only generates the aggregate ratings schema.</p>
<p>The default schema value is: <code>"false"</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews schema="true"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">schema</span>=<span class="attr-value">"true"</span><span class="tag">]</span></code></pre>
</div>
