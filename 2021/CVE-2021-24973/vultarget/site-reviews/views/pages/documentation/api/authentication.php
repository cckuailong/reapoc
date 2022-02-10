<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="api-authentication">
            <span class="title">Authentication</span>
            <span class="badge important code">Important</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="api-authentication" class="inside">
        <p>The Site Reviews API requires authentication in order to access the endpoints. You may use the built-in Application Password feature of WordPress together with a <a href="https://ec.haxx.se/http/http-auth">Basic Auth</a> authentication flow. Application Passwords are specific the the user; they cannot be used for traditional logins to your website and they can be revoked at any time.</p>
        <p>To learn more about Application Passwords, please see the <a href="https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/">Application Passwords: Integration Guide</a>.</p>

        <h3>Getting Credentials</h3>
        <p>From the Edit User page, you can generate new, and view or revoke existing Application Passwords.</p>
        <p>Application passwords can be used with or without the spaces — if included, spaces will just be stripped out before the password is hashed and verified.

        <h3>Using Credentials</h3>
        <p>The credentials can be passed along to REST API requests served over <code>https://</code> using <a href="https://ec.haxx.se/http/http-auth">Basic Auth</a> / <a href="https://tools.ietf.org/html/rfc7617">RFC 7617</a>, which is nearly ubiquitous in its availability — <a href="https://ec.haxx.se/http/http-auth">here’s the documentation for how to use it with cURL</a>.</p>
        <p>For a simple command-line script example, swap out USERNAME, PASSWORD, and HOSTNAME with their respective values (where PASSWORD is the user's generated Application Password):</p>
        <pre><code class="language-bash">curl --user "USERNAME:PASSWORD" https://HOSTNAME/wp-json/site-reviews/v1/reviews</code></pre>

        <h3>Other Authentication Methods</h3>
        <p>Since the Site Reviews API is built on top of the WordPress REST API, you should be able to use any plugin which provides alternative modes of authentication that work from remote applications. Some example plugins are <a href="https://wordpress.org/plugins/rest-api-oauth1/">OAuth 1.0a Server</a> and <a href="https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/">JSON Web Tokens</a>.</p>
    </div>
</div>
