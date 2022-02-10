<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-change-summary-field-order">
            <span class="title">How do I change the order of the reviews summary fields?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-change-summary-field-order" class="inside">
        <p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
        <p>The <code>reviews-summary.php</code> template determines how the reviews summary is displayed.</p>
        <p>The first thing you will need to do (if you haven't already) is create a folder in your theme called <code>site-reviews</code>. Once you have done this, <strong>copy</strong> over the <code>reviews-summary.php</code> file from the "templates" directory in the Site Reviews plugin to this new folder. If you have done this correctly, the path to the template file in your theme should look something like this:</p>
        <p><code>/wp-content/themes/your-theme/site-reviews/reviews-summary.php</code></p>
        <p>Finally, open the template file you copied over into a text editer, it will look something like this:</p>
        <pre><code class="language-html">&lt;div class="glsr-summary-wrap"&gt;
    &lt;div class="{{ class }}" id="{{ id }}"&gt;
        {{ rating }}
        {{ stars }}
        {{ text }}
        {{ percentages }}
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        <p>Now simply rearrange the summary fields into the order you want (you can also remove the fields that you don't want) and then save the template.</p>
    </div>
</div>
