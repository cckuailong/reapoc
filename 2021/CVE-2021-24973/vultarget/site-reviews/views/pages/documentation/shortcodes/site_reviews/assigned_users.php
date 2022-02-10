<p class="glsr-heading">assigned_users</p>
<p>Include the "assigned_users" option to limit reviews to those assigned to specific users. Accepted values are a <a href="https://wpklik.com/wordpress-tutorials/wordpress-user-id/">WordPress User ID</a>, username, or <code>user_id</code> which automatically uses the User ID of the logged in user. Separate multiple values with a comma.</p>
<p><span class="required">Important:</span> If you are using this shortcode together with the [site_reviews_summary] shortcode, make sure to set the same option value for both shortcodes.</p>
<p>The default assigned_users value is: <code>""</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews assigned_users="user_id"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews</span> <span class="attr-name">assigned_users</span>=<span class="attr-value">"user_id"</span><span class="tag">]</span></code></pre>
</div>
