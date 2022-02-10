<p class="glsr-heading">labels</p>
<p>The "labels" option allows you to enter custom labels for the percentage bar levels (from high to low), each level should be separated with a comma. However, rather than using this option to change the labels it's recommended to instead create a custom translation for them in the <code><a href="<?= glsr_admin_url('settings', 'translations'); ?>">Settings &rarr; Translations</a></code> page.</p>
<p>The default labels value is: <code>"Excellent,Very good,Average,Poor,Terrible"</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews_summary labels="5 star,4 star,3 star,2 star,1 star"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">labels</span>=<span class="attr-value">"5 star,4 star,3 star,2 star,1 star"</span><span class="tag">]</span></code></pre>
</div>
