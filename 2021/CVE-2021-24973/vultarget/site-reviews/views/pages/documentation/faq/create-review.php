<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-create-review">
            <span class="title">How do I create a review with PHP?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-create-review" class="inside">
        <p>Site Reviews provides a <code><a href="<?= glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_create_review">glsr_create_review()</a></code> helper function to easily create a review.</p>
        <p>Here is an example:</p>
        <pre><code class="language-php">if (function_exists('glsr_create_review')) {
    $review = glsr_create_review([
        'rating' => 5,
        'title' => 'Fantastic plugin!',
        'content' => 'This is my review.',
        'name' => 'Jane Doe',
        'email' => 'jane@doe.com',
        'date' => '2020-06-13',
    ]);
}
</code></pre>
    </div>
</div>
