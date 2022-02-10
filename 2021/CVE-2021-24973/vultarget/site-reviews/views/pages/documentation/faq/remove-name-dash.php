<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-remove-name-dash">
            <span class="title">How do I remove the dash in front of the author's name?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-remove-name-dash" class="inside">
        <p>A "dash" character appears in front of an author's name if you have chosen to disable avatars in the settings (or possibly also if you changed the order of the review fields). If you want to remove the dash, simply use the following custom CSS. If your theme does not allow you to add custom CSS, you can use a plugin such as <a href="https://wordpress.org/plugins/simple-custom-css/">Simple Custom CSS</a>.</p>
        <pre><code class="language-css">.glsr-review-author::before {
    display: none !important;
}</code></pre>
    </div>
</div>
