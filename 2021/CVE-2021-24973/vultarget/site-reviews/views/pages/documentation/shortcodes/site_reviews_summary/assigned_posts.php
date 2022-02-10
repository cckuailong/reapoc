<p class="glsr-heading">assigned_posts</p>
<p>Include the "assigned_posts" option to limit reviews to those assigned to specific posts, pages, or other public post types. Accepted values are a <a href="https://wpklik.com/wordpress-tutorials/how-to-quickly-find-your-wordpress-page-and-post-id/">WordPress Post ID</a>, <code>post_id</code> which automatically uses the Post ID of the current page, <code>parent_id</code> which automatically uses the Post ID of the parent page, or the page slug entered in the format of <code>post_type:slug</code>. Separate multiple values with a comma.</p>
<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews] shortcode, make sure to set the same option value for both shortcodes.</p>
<p>The default assigned_posts value is: <code>""</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews_summary assigned_posts="post_id"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">assigned_posts</span>=<span class="attr-value">"post_id"</span><span class="tag">]</span></code></pre>
</div>
