<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="api-reviews">
            <span class="title">Reviews</span>
            <span class="badge code">/site-reviews/v1/reviews</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="api-reviews" class="inside">
        <h3>Schema</h3>
        <p>The schema defines all the fields that exist within a review record. Any response from these endpoints can be expected to contain the fields below unless one of the following conditions is true:</p>
        <ol>
            <li>The <code>_fields</code> query parameter is used.</li>
            <li>The <code>_rendered</code> query parameter is used.</li>
            <li>The schema field only appears in a specific context.</li>
        </ol>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Field</strong></th>
                        <th scope="col"><strong>Type</strong></th>
                        <th scope="col"><strong>Context</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>assigned_posts</strong></td>
                        <td>array</td>
                        <td>view, edit</td>
                        <td>The post IDs (of any public post type) assigned to the review.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_terms</strong></td>
                        <td>array</td>
                        <td>view, edit</td>
                        <td>The term IDs assigned to the review in the <code>site-reviews-category</code> taxonomy.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_users</strong></td>
                        <td>array</td>
                        <td>view, edit</td>
                        <td>The user IDs assigned to the review.</td>
                    </tr>
                    <tr>
                        <td><strong>author</strong></td>
                        <td>integer</td>
                        <td>view, edit</td>
                        <td>The ID for the author of the review Post Type.</td>
                    </tr>
                    <tr>
                        <td><strong>avatar</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>The avatar URL of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>content</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>The content field of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>custom</strong></td>
                        <td>object</td>
                        <td>view, edit</td>
                        <td>Custom field values.</td>
                    </tr>
                    <tr>
                        <td><strong>date</strong></td>
                        <td>string or null, datetime</td>
                        <td>view, edit</td>
                        <td>The date the review was published, in the site's timezone.</td>
                    </tr>
                    <tr>
                        <td><strong>date_gmt</strong></td>
                        <td>string or null, datetime</td>
                        <td>view, edit</td>
                        <td>The date the review was published, as GMT.</td>
                    </tr>
                    <tr>
                        <td><strong>email</strong></td>
                        <td>string or null</td>
                        <td>view, edit</td>
                        <td>The email field of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>id</strong></td>
                        <td>integer</td>
                        <td>view</td>
                        <td>Unique identifier for the review. (read only)</td>
                    </tr>
                    <tr>
                        <td><strong>ip_address</strong></td>
                        <td>string or null</td>
                        <td>view, edit</td>
                        <td>The IP address submitted with the review.</td>
                    </tr>
                    <tr>
                        <td><strong>is_approved</strong></td>
                        <td>boolean</td>
                        <td>view, edit</td>
                        <td>The approval status of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>is_modified</strong></td>
                        <td>boolean</td>
                        <td>view, edit</td>
                        <td>The modified status of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>is_pinned</strong></td>
                        <td>boolean</td>
                        <td>view, edit</td>
                        <td>The pinned status of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>meta</strong></td>
                        <td>object</td>
                        <td>view, edit</td>
                        <td>Meta fields of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>modified</strong></td>
                        <td>string or null, datetime</td>
                        <td>view, edit</td>
                        <td>The date the review was last modified, in the site's timezone.</td>
                    </tr>
                    <tr>
                        <td><strong>modified_gmt</strong></td>
                        <td>string or null, datetime</td>
                        <td>view, edit</td>
                        <td>The date the review was last modified, as GMT.</td>
                    </tr>
                    <tr>
                        <td><strong>name</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>The name field of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>rating</strong></td>
                        <td>integer</td>
                        <td>view, edit</td>
                        <td>The rating field of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>response</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>The response given for a review.</td>
                    </tr>
                    <tr>
                        <td><strong>status</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>A named Post Status for the review. One of: <code>publish</code>, <code>future</code>, <code>draft</code>, <code>pending</code>, <code>private</code></td>
                    </tr>
                    <tr>
                        <td><strong>terms</strong></td>
                        <td>boolean</td>
                        <td>view, edit</td>
                        <td>The accepted terms field of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>title</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>The title of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>type</strong></td>
                        <td>string or null</td>
                        <td>view, edit</td>
                        <td>The review type, default value is <code>local</code>.</td>
                    </tr>
                    <tr>
                        <td><strong>url</strong></td>
                        <td>string or null</td>
                        <td>view, edit</td>
                        <td>The external URL of the review when the review source is from a third-party.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Global Parameters</h3>
        <p>To instruct Site Reviews to return only a subset of the fields in a response, you may use the <code>_fields</code> query parameter. If for example you only need the ID, title, content and rating for a collection of reviews, you can restrict the response to only those properties with this fields query:</p>
        <pre><code class="language-bash">/site-reviews/v1/reviews?_fields=id,title,content,rating</code></pre>
        <p>To instruct Site Reviews to return the rendered HTML of the reviews in the response instead of an array of review values, you may use the <code>_rendered</code> query parameter. For example:</p>
        <pre><code class="language-bash">/site-reviews/v1/reviews?_rendered=1</code></pre>

        <h3>List Reviews</h3>
        <p>Query this endpoint to retrieve a collection of reviews. The response you receive can be controlled and filtered using the URL query parameters below.</p>
        <h4>Arguments</h4>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Parameter</strong></th>
                        <th scope="col"><strong>Default</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>after</strong></td>
                        <td></td>
                        <td>Limit result set to reviews published after a given ISO8601 compliant date.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_posts</strong></td>
                        <td></td>
                        <td>Limit result set to reviews assigned to specific posts of any public post type (IDs or slugs in the format of <code>post_type:slug</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_terms</strong></td>
                        <td></td>
                        <td>Limit result set to reviews assigned to specific terms in the <code>site-review-category</code> taxonomy (IDs or slugs).</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_users</strong></td>
                        <td></td>
                        <td>Limit result set to reviews assigned to specific users (IDs or usernames).</td>
                    </tr>
                    <tr>
                        <td><strong>before</strong></td>
                        <td></td>
                        <td>Limit result set to reviews published before a given ISO8601 compliant date.</td>
                    </tr>
                    <tr>
                        <td><strong>date</strong></td>
                        <td></td>
                        <td>Limit result set to reviews published on a given ISO8601 compliant date.</td>
                    </tr>
                    <tr>
                        <td><strong>email</strong></td>
                        <td></td>
                        <td>Limit result set to reviews containing a given email address.</td>
                    </tr>
                    <tr>
                        <td><strong>exclude</strong></td>
                        <td></td>
                        <td>Ensure result set excludes specific review IDs.</td>
                    </tr>
                    <tr>
                        <td><strong>include</strong></td>
                        <td></td>
                        <td>Limit result set to specific review IDs.</td>
                    </tr>
                    <tr>
                        <td><strong>ip_address</strong></td>
                        <td></td>
                        <td>Limit result set to reviews submitted from a given IP address.</td>
                    </tr>
                    <tr>
                        <td><strong>offset</strong></td>
                        <td>0</td>
                        <td>Offset the result set by a specific number of items.</td>
                    </tr>
                    <tr>
                        <td><strong>order</strong></td>
                        <td>desc</td>
                        <td>Order sort attribute ascending or descending. One of: <code>asc</code>, <code>desc</code></td>
                    </tr>
                    <tr>
                        <td><strong>orderby</strong></td>
                        <td>date</td>
                        <td>Sort collection by object attribute. One of: <code>author</code>, <code>date</code>, <code>date_gmt</code>, <code>ID</code>, <code>random</code>, <code>rating</code></td>
                    </tr>
                    <tr>
                        <td><strong>page</strong></td>
                        <td>1</td>
                        <td>Current page of the collection.</td>
                    </tr>
                    <tr>
                        <td><strong>per_page</strong></td>
                        <td>10</td>
                        <td>Maximum number of items to be returned in result set.</td>
                    </tr>
                    <tr>
                        <td><strong>rating</strong></td>
                        <td></td>
                        <td>Limit result set to reviews containing a given <em>minimum</em> rating.</td>
                    </tr>
                    <tr>
                        <td><strong>rendered</strong></td>
                        <td>0</td>
                        <td>Return a rendered result of the reviews and the corresponding pagination. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                    <tr>
                        <td><strong>status</strong></td>
                        <td>approved</td>
                        <td>Limit result set to reviews containing a given status. One of: <code>all</code>, <code>approved</code>, <code>unapproved</code></td>
                    </tr>
                    <tr>
                        <td><strong>terms</strong></td>
                        <td></td>
                        <td>Limit result set to reviews submitted with terms accepted. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                    <tr>
                        <td><strong>type</strong></td>
                        <td></td>
                        <td>Limit result set to reviews containing a given review type.</td>
                    </tr>
                    <tr>
                        <td><strong>user__in</strong></td>
                        <td></td>
                        <td>Limit result set to reviews authored by specific users (IDs or usernames).</td>
                    </tr>
                    <tr>
                        <td><strong>user__not_in</strong></td>
                        <td></td>
                        <td>Ensure result set excludes reviews authored by specific users (IDs or usernames).</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Response Headers</h4>
        <p>This endpoint includes additional headers in the response which provide the following information:</p>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Header</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>X-GLSR-Average</strong></td>
                        <td>The average rating of the total reviews.</td>
                    </tr>
                    <tr>
                        <td><strong>X-GLSR-Ranking</strong></td>
                        <td>The bayesian ranking of the total reviews.</td>
                    </tr>
                    <tr>
                        <td><strong>X-WP-Total</strong></td>
                        <td>The total number of reviews.</td>
                    </tr>
                    <tr>
                        <td><strong>X-WP-TotalPages</strong></td>
                        <td>The total number of review pages.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">GET /site-reviews/v1/reviews</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl https://example.com/wp-json/site-reviews/v1/reviews</code></pre>

        <h3>Retrieve a Review</h3>
        <p>Query this endpoint to retrieve a review.</p>
        <h4>Arguments</h4>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Parameter</strong></th>
                        <th scope="col"><strong>Default</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>context</strong></td>
                        <td>view</td>
                        <td>Scope under which the request is made; determines fields present in response. One of: <code>view</code>, <code>edit</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">GET /site-reviews/v1/reviews/&lt;id&gt;</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl https://example.com/wp-json/site-reviews/v1/reviews/&lt;id&gt;</code></pre>

        <h3>Create a Review</h3>
        <p>Query this endpoint to create a review.</p>
        <h4>Arguments</h4>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Parameter</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>assigned_posts</strong></td>
                        <td>The posts (of any public post type) assigned to the review. One or more IDs separated with commas. If you want to use slugs, they should be in the format of <code>post_type:slug</code>.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_terms</strong></td>
                        <td>The terms assigned to the review in the <code>site-reviews-category</code> taxonomy. One or more IDs or slugs separated with commas.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_users</strong></td>
                        <td>The users assigned to the review. One or more IDs or usernames separated with commas.</td>
                    </tr>
                    <tr>
                        <td><strong>avatar</strong></td>
                        <td>The avatar URL of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>content</strong></td>
                        <td>The content of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>date</strong></td>
                        <td>The date the review was published, in the site's timezone.</td>
                    </tr>
                    <tr>
                        <td><strong>email</strong></td>
                        <td>The email of the reviewer.</td>
                    </tr>
                    <tr>
                        <td><strong>ip_address</strong></td>
                        <td>The IP addess of the reviewer.</td>
                    </tr>
                    <tr>
                        <td><strong>is_pinned</strong></td>
                        <td>The pinned status of the review. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                    <tr>
                        <td><strong>name</strong></td>
                        <td>The name of the reviewer.</td>
                    </tr>
                    <tr>
                        <td><strong>rating</strong></td>
                        <td>The rating of the review. Value must be between 0-5 (unless the maximum rating has been changed).</td>
                    </tr>
                    <tr>
                        <td><strong>response</strong></td>
                        <td>The response given to the review.</td>
                    </tr>
                    <tr>
                        <td><strong>status</strong></td>
                        <td>A status of the review. One of: <code>approved</code>, <code>upapproved</code></td>
                    </tr>
                    <tr>
                        <td><strong>terms</strong></td>
                        <td>The accepted terms field of the review. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                    <tr>
                        <td><strong>title</strong></td>
                        <td>The title of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>type</strong></td>
                        <td>The review type. Default is: <code>local</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">POST /site-reviews/v1/reviews</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl -X POST https://example.com/wp-json/site-reviews/v1/reviews -d '{"rating":"5"}'</code></pre>

        <h3>Update a Review</h3>
        <p>Query this endpoint to update a review.</p>
        <h4>Arguments</h4>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Parameter</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>assigned_posts</strong></td>
                        <td>The posts (of any public post type) assigned to the review. One or more IDs separated with commas.  If you want to use slugs, they should be in the format of <code>post_type:slug</code>.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_terms</strong></td>
                        <td>The terms assigned to the review in the <code>site-reviews-category</code> taxonomy. One or more IDs or slugs separated with commas.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_users</strong></td>
                        <td>The users assigned to the review. One or more IDs or usernames separated with commas.</td>
                    </tr>
                    <tr>
                        <td><strong>avatar</strong></td>
                        <td>The avatar URL of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>content</strong></td>
                        <td>The content of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>date</strong></td>
                        <td>The date the review was published, in the site's timezone.</td>
                    </tr>
                    <tr>
                        <td><strong>email</strong></td>
                        <td>The email of the reviewer.</td>
                    </tr>
                    <tr>
                        <td><strong>ip_address</strong></td>
                        <td>The IP addess of the reviewer.</td>
                    </tr>
                    <tr>
                        <td><strong>is_pinned</strong></td>
                        <td>The pinned status of the review. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                    <tr>
                        <td><strong>name</strong></td>
                        <td>The name of the reviewer.</td>
                    </tr>
                    <tr>
                        <td><strong>rating</strong></td>
                        <td>The rating of the review. Value must be between 0-5 (unless the maximum rating has been changed).</td>
                    </tr>
                    <tr>
                        <td><strong>response</strong></td>
                        <td>The response given to the review.</td>
                    </tr>
                    <tr>
                        <td><strong>status</strong></td>
                        <td>A status of the review. One of: <code>approved</code>, <code>upapproved</code></td>
                    </tr>
                    <tr>
                        <td><strong>terms</strong></td>
                        <td>The accepted terms field of the review. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                    <tr>
                        <td><strong>title</strong></td>
                        <td>The title of the review.</td>
                    </tr>
                    <tr>
                        <td><strong>type</strong></td>
                        <td>The review type. Default is: <code>local</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">POST /site-reviews/v1/reviews/&lt;id&gt;</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl -X POST https://example.com/wp-json/site-reviews/v1/reviews/&lt;id&gt; -d '{"rating":"5"}'</code></pre>

        <h3>Delete a Review</h3>
        <p>Query this endpoint to delete a review.</p>
        <h4>Arguments</h4>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Parameter</strong></th>
                        <th scope="col"><strong>Default</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>force</strong></td>
                        <td>0</td>
                        <td>Whether to bypass Trash and force deletion. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">DELETE /site-reviews/v1/reviews/&lt;id&gt;</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl -X DELETE https://example.com/wp-json/site-reviews/v1/reviews/&lt;id&gt;</code></pre>
    </div>
</div>
