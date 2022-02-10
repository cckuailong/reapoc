<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-plugin-templates">
            <span class="title">How do I use the plugin templates in my theme?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-plugin-templates" class="inside">
        <p>Site Reviews uses a custom templating system which makes it easy to customize the HTML of the widgets and shortcodes to meet your needs.</p>
        <ol>
            <li>Create a folder in your theme called "site-reviews".</li>
            <li>Copy the template files that you would like to customise from <code>/wp-content/plugins/site-reviews/templates/</code> into this new folder, keeping the subdirectories the same.</li>
            <li>Open the template files that you copied over in a text editor and make your changes.</li>
        </ol>
        <p>For example:</p>
        <p><code>/wp-content/plugins/site-reviews/templates/form/field.php</code><br><br>
            Would be copied here:<br><br>
            <code>/wp-content/themes/&lt;your-child-theme&gt;/site-reviews/form/field.php</code>
        </p>
        <pre><code class="language-html">wp-content/plugins/site-reviews/templates
├── emails                  This folder contains the email template files
│   └── default.php         This is the default template for HTML emails
├── form                    This folder contains the template files for the form fields
│   ├── field.php           This template displays the field. To target a specific field type, append the type with an underscore (i.e. field_email.php, field_textarea.php)
│   ├── field_checkbox.php  This template displays the field used for one or more checkboxes
│   ├── field_radio.php     This template displays the field used for one or more radios
│   ├── field_select.php    This template displays the field used for a select
│   ├── field_toggle.php    This template displays the field used for one or more toggle switches
│   ├── response.php        This template displays the form submission response
│   ├── submit-button.php   This template displays the submit button
│   ├── type-checkbox.php   This template is used by the field_checkbox.php template to display each checkbox
│   ├── type-radio.php      This template is used by the field_radio.php template to display each radio
│   └── type-toggle.php     This template is used by the field_toggle.php template to display each toggle switch
├── rating                  This folder contains the template files for the stars
│   ├── empty-star.php      This template displays the empty star
│   ├── full-star.php       This template displays the full star
│   ├── half-star.php       This template displays the half star
│   └── stars.php           This template displays the combined stars
├── login-register.php      This template displays the login/register message
├── notification.php        This template contains the default message contents of the notification email
├── pagination.php          This template displays the review pagination
├── review.php              This template displays a single review
├── reviews-form.php        This template displays the review form
├── reviews-summary.php     This template displays the review summary
└── reviews.php             This template displays the reviews</code></pre>
    </div>
</div>
