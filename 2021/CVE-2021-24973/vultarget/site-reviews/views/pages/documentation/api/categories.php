<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="api-categories">
            <span class="title">Categories</span>
            <span class="badge code">/site-reviews/v1/categories</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="api-categories" class="inside">
        <h3>Schema</h3>
        <p>The schema defines all the fields that exist within a category record. Any response from these endpoints can be expected to contain the fields below unless the <code>_fields</code> query parameter is used or the schema field only appears in a specific context.</p>
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
                        <td><strong>count</strong></td>
                        <td>integer</td>
                        <td>view, edit</td>
                        <td>Number of published reviews for the term. (read only)</td>
                    </tr>
                    <tr>
                        <td><strong>description</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>HTML description of the term.</td>
                    </tr>
                    <tr>
                        <td><strong>id</strong></td>
                        <td>integer</td>
                        <td>view, edit</td>
                        <td>Unique identifier for the term. (read only)</td>
                    </tr>
                    <tr>
                        <td><strong>meta</strong></td>
                        <td>object</td>
                        <td>view, edit</td>
                        <td>Meta fields.</td>
                    </tr>
                    <tr>
                        <td><strong>name</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>HTML title for the term.</td>
                    </tr>
                    <tr>
                        <td><strong>slug</strong></td>
                        <td>string</td>
                        <td>view, edit</td>
                        <td>An alphanumeric identifier for the term unique to its type.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Global Parameters</h3>
        <p>To instruct Site Reviews to return only a subset of the fields in a response, you may use the <code>_fields</code> query parameter. If for example you only need the ID and name for a collection of categories, you can restrict the response to only those properties with this fields query:</p>
        <pre><code class="language-bash">/site-reviews/v1/categories?_fields=id,name</code></pre>

        <h3>List Categories</h3>
        <p>Query this endpoint to retrieve a collection of review categories. The response you receive can be controlled and filtered using the URL query parameters below.</p>
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
                    <tr>
                        <td><strong>exclude</strong></td>
                        <td></td>
                        <td>Ensure result set excludes specific IDs.</td>
                    </tr>
                    <tr>
                        <td><strong>hide_empty</strong></td>
                        <td></td>
                        <td>Whether to hide terms not assigned to any reviews.</td>
                    </tr>
                    <tr>
                        <td><strong>include</strong></td>
                        <td></td>
                        <td>Limit result set to specific IDs.</td>
                    </tr>
                    <tr>
                        <td><strong>order</strong></td>
                        <td>asc</td>
                        <td>Order sort attribute ascending or descending. One of: <code>asc</code>, <code>desc</code></td>
                    </tr>
                    <tr>
                        <td><strong>orderby</strong></td>
                        <td>name</td>
                        <td>Sort collection by term attribute. One of: <code>id</code>, <code>include</code>, <code>name</code>, <code>slug</code>, <code>include_slugs</code>, <code>term_group</code>, <code>description</code>, <code>count</code></td>
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
                        <td><strong>post</strong></td>
                        <td></td>
                        <td>Limit result set to terms assigned to a specific review Post ID.</td>
                    </tr>
                    <tr>
                        <td><strong>search</strong></td>
                        <td></td>
                        <td>Limit results to those matching a string.</td>
                    </tr>
                    <tr>
                        <td><strong>slug</strong></td>
                        <td></td>
                        <td>Limit result set to terms with one or more specific slugs.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">GET /site-reviews/v1/categories</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl https://example.com/wp-json/site-reviews/v1/categories</code></pre>

        <h3>Retrieve a Category</h3>
        <p>Query this endpoint to retrieve a review category.</p>
        <h4>Definition</h4>
        <pre><code class="language-bash">GET /site-reviews/v1/categories/&lt;id&gt;</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl https://example.com/wp-json/site-reviews/v1/categories/&lt;id&gt;</code></pre>

        <h3>Create a Category</h3>
        <p>Query this endpoint to create a review category.</p>
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
                        <td><strong>description</strong></td>
                        <td>HTML description of the term.</td>
                    </tr>
                    <tr>
                        <td><strong>meta</strong></td>
                        <td>Meta fields.</td>
                    </tr>
                    <tr>
                        <td><strong>name</strong></td>
                        <td>HTML title for the term. (required)</td>
                    </tr>
                    <tr>
                        <td><strong>slug</strong></td>
                        <td>An alphanumeric identifier for the term unique to its type.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">POST /site-reviews/v1/categories</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl -X POST https://example.com/wp-json/site-reviews/v1/categories -d '{"name":"My Name"}'</code></pre>

        <h3>Update a Category</h3>
        <p>Query this endpoint to update a review category.</p>
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
                        <td><strong>description</strong></td>
                        <td>HTML description of the term.</td>
                    </tr>
                    <tr>
                        <td><strong>meta</strong></td>
                        <td>Meta fields.</td>
                    </tr>
                    <tr>
                        <td><strong>name</strong></td>
                        <td>HTML title for the term.</td>
                    </tr>
                    <tr>
                        <td><strong>slug</strong></td>
                        <td>An alphanumeric identifier for the term unique to its type.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">POST /site-reviews/v1/categories/&lt;id&gt;</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl -X POST https://example.com/wp-json/site-reviews/v1/categories/&lt;id&gt; -d '{"name":"My New Name"}'</code></pre>

        <h3>Delete a Category</h3>
        <p>Query this endpoint to delete a review category.</p>
        <h4>Definition</h4>
        <pre><code class="language-bash">DELETE /site-reviews/v1/categories/&lt;id&gt;</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl -X DELETE https://example.com/wp-json/site-reviews/v1/categories/&lt;id&gt;</code></pre>
    </div>
</div>
