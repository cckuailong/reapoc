<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-query-by-rank">
            <span class="title">How do I order pages with assigned reviews by rating or ranking?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-query-by-rank" class="inside">
        <p>Site Reviews provides two meta keys that can be used for sorting pages that have reviews assigned to them.</p>
        <p>The <code>_glsr_average</code> meta key contains the average rating of the page.</p>
        <p>The <code>_glsr_ranking</code> meta key contains the page rank determined by a bayesian ranking algorithm (the exact same way that films are ranked on IMDB). To understand why sorting by rank may be preferable to sorting by average rating, please see: <a target="_blank" href="https://imgs.xkcd.com/comics/tornadoguard.png">The problem with averaging star ratings</a>.</p>
        <p>Here is an example of how you can use these meta keys in a custom WP_Query. In this example, we will sort all pages by rank (highest to lowest) and regardless of whether or not they have reviews assigned to them:</p>
        <pre><code class="language-php">$query = new WP_Query([
    'meta_query' => [
        'relation' => 'OR',
        ['key' => '_glsr_ranking', 'compare' => 'NOT EXISTS'], // this comes first!
        ['key' => '_glsr_ranking', 'compare' => 'EXISTS'],
    ],
    'order' => 'DESC',
    'orderby' => 'meta_value_num',
    'post_status' => 'publish',
    'post_type' => 'page', // change this as needed
    'posts_per_page' => 10, // change this as needed
]);</code></pre>
        <p>If you would like to only query pages that actually have reviews assigned to them, you can do this instead:</p>
        <pre><code class="language-php">$query = new WP_Query([
    'meta_query' => [
        ['key' => '_glsr_ranking', 'compare' => '>', 'value' => 0],
    ],
    'order' => 'DESC',
    'orderby' => 'meta_value_num',
    'post_status' => 'publish',
    'post_type' => 'page', // change this as needed
    'posts_per_page' => 10, // change this as needed
]);</code></pre>
        <p>Once you have your custom query, you use it in your WordPress theme template like this:</p>
        <pre><code class="language-php">if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $average = sprintf('%s (average rating: %s)',
            get_the_title(),
            get_post_meta($post->ID, '_glsr_average', true)
        );
        $ranking = sprintf('%s (ranking: %s)',
            get_the_title(),
            get_post_meta($post->ID, '_glsr_ranking', true)
        );
        apply_filters('glsr_debug', 'Site Reviews is not installed', $average, $ranking);
    }
    wp_reset_postdata();
}</code></pre>
        <p>To learn more about <code>WP_Query</code> and how to use it in your theme templates, please refer to the <a target="_blank" href="https://developer.wordpress.org/themes/basics/the-loop/">WordPress Theme Handbook</a>.</p>
    </div>
</div>
