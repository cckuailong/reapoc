<?php
$current_user = wp_get_current_user();

$articles = [
    'setup' => [
        [
            'title' => 'How to Install',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/getting-started/how-to-install/',
        ],
        [
            'title' => 'License Activation',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/troubleshoot/license-activation/',
        ],
        [
            'title' => 'Shortcodes',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/getting-started/wpuf-shortcodes/',
        ],
        [
            'title' => 'User Dashboard',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/getting-started/user-dashboard/',
        ],
    ],
    'posting' => [
        [
            'title' => 'Creating Posting Forms',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/creating-posting-forms/',
        ],
        [
            'title' => 'Available Form Elements',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/form-elements/',
        ],
        [
            'title' => 'Creating Forms Using The Form Templates',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/form-templates/',
        ],
        [
            'title' => 'How to Allow Guest Posting',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/guest-posting/',
        ],
        [
            'title' => 'Setup Automatic Post Expiration',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/using-post-expiration-wp-user-frontend/',
        ],
        [
            'title' => 'How to create Multistep forms',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/how-to-add-multi-step-form/',
        ],
    ],
    'dashboard' => [
        [
            'title' => 'Setting up Frontend Dashboard for Users',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/frontend/configuring-dashboard-settings/',
        ],
        [
            'title' => 'Unified My Account Page',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/frontend/how-to-create-my-account-page/',
        ],
        [
            'title' => 'Showing meta fields in frontend',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/frontend/showing-meta-fields-in-frontend/',
        ],
    ],
    'settings' => [
        [
            'title' => 'General Options',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/settings/configuring-general-options/',
        ],
        [
            'title' => 'Dashboard Settings',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/settings/configuring-dashboard-settings/',
        ],
        [
            'title' => 'Login Registration Settings',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/settings/login-registration-settings/',
        ],
        [
            'title' => 'Payment Settings',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/settings/configuring-payment-settings/',
        ],
    ],
    'registration' => [
        [
            'title' => 'Creating Registration Form',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/registration-forms/',
        ],
        [
            'title' => 'Creating a Multistep Registration Form',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/creating-a-multistep-registration-form/',
        ],
        [
            'title' => 'Setting Up Confirmation Message',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/setup-confirmation-message/',
        ],
        [
            'title' => 'Paid Membership Registration',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/paid-membership-registration/',
        ],
        [
            'title' => 'Setting Up Email Verification for New Users',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/setting-up-email-verification-for-new-users/',
        ],
    ],
    'profile' => [
        [
            'title' => 'Creating a Profile Editing Form',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/wordpress-edit-user-profile-from-front-end/',
        ],
    ],
    'subscription' => [
        [
            'title' => 'Creating Subscription Packs',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/creating-subscription-packs/',
        ],
        [
            'title' => 'Payment & Gateway Settings',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/configuring-payment-settings/',
        ],
        [
            'title' => 'Setting Up Recurring Payment',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/setting-up-recurring-payment/',
        ],
        [
            'title' => 'Forcing Subscription Pack For Post Submission',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/forcing-subscription-pack-for-post-submission/',
        ],
        [
            'title' => 'How to Charge for Each Post Submission?',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/how-to-charge-for-each-post-submission/',
        ],
        [
            'title' => 'Creating Coupons',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/coupons/',
        ],
    ],

    'developer' => [
        [
            'title' => 'Action Hook Field',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/developer-docs/action-hook-field/',
        ],
        [
            'title' => 'Add a New Tab on My Account Page',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/developer-docs/add-a-new-tab-on-my-account-page/',
        ],
        [
            'title' => 'Insert/update checkbox or radio field data as serialize',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/developer-docs/insertupdate-checkbox-or-radio-field-data-as-serialize/',
        ],
        [
            'title' => 'Filters',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/developer-docs/filters/',
        ],
        [
            'title' => 'Actions',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/developer-docs/actions/',
        ],
        [
            'title' => 'Changelog',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/changelog/',
        ],
    ],
    'restriction' => [
        [
            'title' => 'Content Restriction for Logged in Users',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/content-restriction/content-restriction/',
        ],
        [
            'title' => 'Restricting Content by User Roles',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/content-restriction/restricting-content-by-user-roles/',
        ],
        [
            'title' => 'Restricting Contents for Different Subscription Packs',
            'link'  => 'https://wedevs.com/docs/wp-user-frontend-pro/content-restriction/restricting-contents-for-different-subscription-packs/',
        ],
    ],
];

/**
 * Print related articles
 *
 * @param array $articles
 *
 * @return void
 */
function wpuf_help_related_articles( $articles ) {
    ?>
    <h2><?php esc_html_e( 'Related Articles:', 'wp-user-frontend' ); ?></h2>

    <ul class="related-articles">
    <?php
        foreach ( $articles as $article ) {
            ?>
            <li>
                <span class="dashicons dashicons-media-text"></span>
                <a href="<?php echo  esc_attr( trailingslashit( $article['link'] ) ); ?>?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=<?php echo esc_attr( $article['title'] ); ?>" target="_blank"><?php echo esc_attr( $article['title'] ); ?></a>
            </li>
            <?php
        } ?>
    </ul>
    <?php
}
?>

<div class="wrap wpuf-help-page">
    <h1><?php esc_html_e( 'General Help Questions', 'wp-user-frontend' ); ?> <a href="https://wedevs.com/docs/wp-user-frontend-pro/?utm_source=wpuf-help-page&utm_medium=button-primary&utm_campaign=view-all-docs" target="_blank" class="page-title-action"><span class="dashicons dashicons-external" style="margin-top: 8px;"></span> <?php esc_html_e( 'View all Documentations', 'wp-user-frontend' ); ?></a></h1>

    <form class="wpuf-subscribe-box" id="wpuf-form-subscribe" action="https://wedevs.us16.list-manage.com/subscribe/post-json?u=66e606cfe0af264974258f030&id=0d176bb256&c=?" method="get">

        <div class="text-wrap">
            <h3><?php esc_html_e( 'Subscribe to Our Newsletter', 'wp-user-frontend' ); ?></h3>
            <p>
                <?php echo wp_kses_post( __( 'Subscribe to our newsletter for regular <strong>tips</strong>, <strong>offers</strong> and <strong>news updates</strong>.', 'wp-user-frontend' ) ); ?>
            </p>
        </div>

        <div class="form-wrap">
            <div class="fname">
                <label for="fname"><?php esc_html_e( 'First Name', 'wp-user-frontend' ); ?></label>
                <input type="text" name="FNAME" id="fname" class="regular-text" value="<?php echo esc_attr( $current_user->first_name ); ?>" required>
            </div>

            <div class="email">
                <label for="email"><?php esc_html_e( 'Email', 'wp-user-frontend' ); ?></label>
                <input type="email" name="EMAIL" id="email" class="regular-text" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>
            </div>

            <div class="submit-btn">
                <input type="hidden" name="group[3555][1]" value="1">
                <input type="submit" class="button button-primary" value="<?php echo esc_attr( __( 'Subscribe', 'wp-user-frontend' ) ); ?>">
            </div>
        </div>
    </form>

    <div class="wpuf-help-tabbed">
        <nav>
            <ul>
                <li class="tab-current">
                    <a href="#setup">
                        <span class="dashicons dashicons-admin-home"></span>
                        <label><?php esc_html_e( 'Plugin Setup', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
                <li>
                    <a href="#frontend-posting">
                        <span class="dashicons dashicons-media-text"></span>
                        <label><?php esc_html_e( 'Frontend Posting', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
                <li>
                    <a href="#frontend-dashboard">
                        <span class="dashicons dashicons-dashboard"></span>
                        <label><?php esc_html_e( 'Frontend Dashboard', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
                <li>
                    <a href="#user-registration">
                        <span class="dashicons dashicons-admin-users"></span>
                        <label><?php esc_html_e( 'User Registration', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
                <li>
                    <a href="#login-page">
                        <span class="dashicons dashicons-lock"></span>
                        <label><?php esc_html_e( 'User Login', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
                <li>
                    <a href="#profile-editing">
                        <span class="dashicons dashicons-edit"></span>
                        <label><?php esc_html_e( 'Profile Editing', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
                <li>
                    <a href="#subscription-payment">
                        <span class="dashicons dashicons-cart"></span>
                        <label><?php esc_html_e( 'Subscription &amp; Payment', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
                <li>
                    <a href="#content-restriction">
                        <span class="dashicons dashicons-unlock"></span>
                        <label><?php esc_html_e( 'Content Restriction', 'wp-user-frontend' ); ?></label>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="nav-content">
            <section id="setup" class="content-current">
                <h2>Plugin Setup Guide</h2>

                <p>Setting up WP User Frontend is very easy. Here are few things that you should consider.</p>

                <ol>
                    <li><strong>Install WPUF Pages</strong> with a single click. Check your admin dashboard for a message to install WPUF required pages.</li>
                    <li>You can create amazing frontend posting forms with more than 20 useful form fields. </li>
                    <li>Posting the forms in the frontend is also very easy. All you have to do is <strong>put the shortcode</strong> of your form to a page. </li>
                    <li>Building registration &amp; profile editing forms has never been easier, thanks to WP User Frontend. <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpuf-profile-forms' ) ); ?>" target="_blank">Build registration &amp; profile forms</a> on the go with simple steps.</li>
                    <li>Add customized <strong>login forms</strong> using simple shortcodes and override default WordPress login and registration.</li>
                    <li>Create customized <strong>subscription forms</strong> and let your users buy with multiple payment gateways.</li>
                    <li><strong>Enable guest posting</strong> and earn from each posts without any difficulties. </li>
                </ol>

                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/getting-started/how-to-install/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=how-to-install" target="_blank"><?php esc_html_e( 'Learn More About Installation', 'wp-user-frontend' ); ?></a>

                <?php wpuf_help_related_articles( $articles['setup'] ); ?>
            </section>
            <section id="frontend-posting">
                <h2>Frontend Posting</h2>

                <p>Posting Forms are used to <strong>create new</strong> blog posts, WooCommerce Products, Directory Listing Entries etc. You can create any custom post type from the front using this feature. You just need to create a form with necessary fields and embed the form in a page and your users will be able to create posts from frontend in no time.</p>
                <p>To create a posting form, go to <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpuf-post-forms' ) ); ?>" target="_blank">Post Forms</a> → Add Form and start building your ultimate frontend posting forms. </p>
                <p>After building your forms, <strong>use the shortcodes</strong> on any new page or post and publish them before sharing.</p>

                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=frontend-posting" target="_blank"><?php esc_html_e( 'Learn More About Frontend Posting', 'wp-user-frontend' ); ?></a>

                <?php wpuf_help_related_articles( $articles['posting'] ); ?>
            </section>
            <section id="frontend-dashboard">
                <h2>Frontend Dashboard</h2>

                <p>WP User Frontend generates <strong>Frontend Dashboard</strong> and <strong>My Account</strong> page for all your users. Using these pages, they can get a list of their posts and subscriptions directly at frontend. They can also customize the details of their profile. You don’t need to give them access to the backend at all!</p>
                <p>To crate this page, <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>" target="_blank">create a new page</a>, put a title and simply copy-paste the following shortcode: <code>[wpuf_dashboard]</code>. Alternatively, there is an unified <a href="https://wedevs.com/docs/wp-user-frontend-pro/frontend/how-to-create-my-account-page/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=unified-my-account-page" target="_blank">my account page</a> as well. Finally, hit the publish button and you are done.</p>

                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/frontend/configuring-dashboard-settings/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=frontend-dashboard" target="_blank"><?php esc_html_e( 'Learn More About Frontend Dashboard', 'wp-user-frontend' ); ?></a>

                <?php wpuf_help_related_articles( $articles['dashboard'] ); ?>
            </section>
            <section id="user-registration">
                <h2>User Registration</h2>

                <p>You can create as many registration forms as you want and assign them to different user roles. Creating Registration forms are easy. Navigate to <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpuf-profile-forms' ) ); ?>" target="_blank">Registration Forms</a>.</p>
                <p>You can create new forms just you would create posts in WordPress.</p>

                <ol>
                    <li>Give your form a name and click on Form Elements on the right sidebar.</li>
                    <li>The form elements will appear to the Form Editor tab with some options.</li>
                </ol>

                <p>From settings you can –</p>

                <ul>
                    <li>Assign New User Roles</li>
                    <li>Can redirect to any custom page or same page with successful message</li>
                </ul>

                <h3>Showing Registration Form</h3>

                <ul>
                    <li>By using short-code you can show your registration form into any page or post</li>
                    <li>You will get different short-codes for each registration forms separately.</li>
                </ul>


                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=registration-profile-forms" target="_blank"><?php esc_html_e( 'Learn More About Registration', 'wp-user-frontend' ); ?></a>

                <?php wpuf_help_related_articles( $articles['registration'] ); ?>
            </section>
            <section id="login-page">
                <h2>Login Page</h2>

                <p>WP User Frontend Automatically creates important pages when you install it for the first time. You can also create login forms manually. </p>

                <p>Navigate to <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpuf-settings' ) ); ?>" target="_blank">Settings</a> → <strong>Login/Registration</strong> tab.
                In this page, you will find several useful settings related to WPUF login. You can override default registration and login forms with WPUF login &amp; registration feature if you want. To do this, check the <strong>Login/Registration override option</strong>.</p>

                <p>You can also specify the login page. WPUF automatically adds the default login page that it has created. If you manually create one, use the following shortcode – <code>[wpuf-login]</code></p>

                <p>Simply, create a new page and put the above shortcode. Finally, publish the page and add it to the Login Page option in the settings.</p>

                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/user-login/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=learn-more-login" target="_blank"><?php esc_html_e( 'Learn More About Login', 'wp-user-frontend' ); ?></a>
            </section>
            <section id="profile-editing">
                <h2>Creating a Profile Editing Form</h2>

                <p>When you are making a registration form, you get two shortcodes:
                For embedding the registration form: this is something like <code>[wpuf_profile type=&quot;registration&quot; id=&quot;3573&quot;]</code></p>

                <p>For profile edit page: this is something like <code>[wpuf_profile type=&quot;profile&quot; id=&quot;3573&quot;]</code></p>

                <p>You already know that how to make a registration form in WP User Frontend Pro and embed that into a page. The very same process is for creating the profile edit page.</p>

                <h2>How to get the shortcode</h2>

                <p>We assume that you already have created a registration form. If not you can use the default registration form, that was created automatically while installing the plugin.
                So to get the shortcode, navigate to <strong>User Frontend</strong> → <strong>Registration Forms</strong> and you will be able to see the shortcodes on the right side of your screen.</p>

                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=registration-profile-forms" target="_blank"><?php esc_html_e( 'Learn More About Profile Editing', 'wp-user-frontend' ); ?></a>

                <?php wpuf_help_related_articles( $articles['profile'] ); ?>
            </section>
            <section id="subscription-payment">
                <h2>Subscription Payment</h2>

                <p>WP User Frontend allows you to create as many subscription packs you want. Simply, navigate to - WP-Admin → User Frontend → Subscription → Add Subscription</p>

                <ol>
                    <li>Enter your subscription name and pack description.</li>
                    <li>Include the billing amount and the validity of the pack. You can choose day, week, month or year in case of expiry.</li>
                    <li>You can enable post expiration if you want to expire post after a certain amount of time. To do so check the Enable Post Expiration box.</li>
                    <li>This will enable some new settings. You have to specify post expiration time and the post status after the post expires.</li>
                    <li>You can also notify users when a post expires. To do so, check the Send Mail option.</li>
                    <li>Now, enter the message you want to send the user in the Post Expiration Message field.</li>
                    <li>You can specify the number of posts you are giving away with this subscription pack. If you want to provide unlimited posts, enter ‘-1’ in the number of posts field.</li>
                    <li>You can also set the number of pages and custom CSS. For unlimited value, enter ‘-1’.</li>
                    <li>WPUF offers you recurring payment while creating a Subscription pack. Enable this option if you want to set recurring payment for this pack. It will provide you some new options for the recurring payment.</li>
                    <li>Now, select the billing cycle.</li>
                    <li>You can also stop the billing cycle if you want. If you don’t want to stop the cycle select Never.</li>
                    <li>To enable trial period, check the Trial box. You can set the trial amount to be paid by the user for trial period.</li>
                    <li>Now, specify the trial period. Enter number of days, week, month or year.</li>
                    <li>You can also enable post number rollback. If enabled, number of posts will be restored if the post is deleted.</li>
                    <li>Finally, click on the publish button to create the subscription pack.</li>
                </ol>
                <h2>Subscription Packs on Frontend</h2>
                <p>To view the created subscription packs on frontend, visit the Subscription page.</p>

                <p>Short-code for creating the Subscription page – <code>[wpuf_sub_pack]</code>.</p>
                <h2>Payment &amp; Gateway Settings</h2>
                <p>Post subscription and payment system is a module where you can add paid posting system with WP User Frontend. You can introduce two types of payment system. Pay per post and subscription pack based.</p>


                <h2>Pay Per Post</h2>


                <p>With this you can introduce pay per post feature where users pay to publish their posts each post. When pay per post is enabled from “Settings → Payments → Charge for posting“, users see a notice right before the post creation form in frontend about payment. When the submits a post, the post status gets pending and he is redirected to the payment page (to setup the payment page, create a Page Payment and select the page at “Settings → Payments → Payment Page“. No shortcode is needed). Currently by default PayPal is only supported gateway. Upon selecting PayPal, he is redirected to PayPal for payment. After successful payment he is redirected back to the site and the post gets published.</p>


                <h2>Subscription Pack</h2>


                <p>There is an another option for charged posting. With this feature, you can create unlimited subscription pack. In each pack, you can configure the number of posts, validity date and the cost.</p>
                <p>When a user buys a subscription package, he gets to create some posts (e.g. 10) in X days (e.g: 30 days). If he crosses the number of posts or the validity date, he can’t post again. You can force the user to buy a pack before posting “Settings → Payments → Force pack purchase“.</p>
                <p>To show the subscription packs in a page, you can use the shortcode: <code>[wpuf_sub_pack]</code>. To show the user subscription info: <code>[wpuf_sub_info]</code>. The info will show the user about his pack’s remaining post count and expiration date of his pack.</p>


                <h2>Payment Gateway</h2>

                <p>Currently only PayPal basic gateway is supported. The plugin is extension aware, that means other gateways can be integrated.</p>


                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=subscription-payment" target="_blank"><?php esc_html_e( 'Learn More About Payments', 'wp-user-frontend' ); ?></a>

                <?php wpuf_help_related_articles( $articles['subscription'] ); ?>
            </section>
            <section id="content-restriction">
                <h2>Content Restriction</h2>

                <p>To set content restriction for a certain form, navigate to <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" target="_blank">Pages</a></strong></p>

                <ol>
                    <li>Now, select the page that has the shortcode of the selected form.</li>
                    <li>Scroll down and you will find the <strong>WPUF Content Restriction</strong> settings.</li>
                    <li>You can set the form visible to three types of people: <strong>Everyone</strong>, <strong>Logged in users only</strong> or <strong>Subscription users only</strong></li>
                    <li>You can also set <strong>subscription plans</strong> for the form. For this, check the box of relevant subscription pack.</li>
                    <li>Finally, update the page.</li>
                </ol>

                <a class="button button-primary button-large" href="https://wedevs.com/docs/wp-user-frontend-pro/content-restriction/?utm_source=wpuf-help-page&utm_medium=help-links&utm_campaign=wpuf-help&utm_term=content-restriction" target="_blank"><?php esc_html_e( 'Learn More About Content Restriction', 'wp-user-frontend' ); ?></a>

                <?php wpuf_help_related_articles( $articles['restriction'] ); ?>
            </section>
        </div>
    </div>

    <div class="help-blocks">
        <div class="help-block">
            <img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/help/like.svg" alt="<?php esc_attr_e( 'Like The Plugin?', 'wp-user-frontend' ); ?>">

            <h3><?php esc_html_e( 'Like The Plugin?', 'wp-user-frontend' ); ?></h3>

            <p><?php esc_html_e( 'Your Review is very important to us as it helps us to grow more.', 'wp-user-frontend' ); ?></p>

            <a target="_blank" class="button button-primary" href="https://wordpress.org/support/plugin/wp-user-frontend/reviews/?rate=5#new-post"><?php esc_html_e( 'Review Us on WP.org', 'wp-user-frontend' ); ?></a>
        </div>

        <div class="help-block">
            <img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/help/bugs.svg" alt="<?php esc_attr_e( 'Found Any Bugs?', 'wp-user-frontend' ); ?>">

            <h3><?php esc_html_e( 'Found Any Bugs?', 'wp-user-frontend' ); ?></h3>

            <p><?php esc_html_e( 'Report any Bug that you Discovered, Get Instant Solutions.', 'wp-user-frontend' ); ?></p>

            <a target="_blank" class="button button-primary" href="https://github.com/weDevsOfficial/wp-user-frontend/?utm_source=wpuf-help-page&utm_medium=help-block&utm_campaign=found-bugs"><?php esc_html_e( 'Report to GitHub', 'wp-user-frontend' ); ?></a>
        </div>

        <div class="help-block">
            <img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/help/support.svg" alt="<?php esc_attr_e( 'Need Any Assistance?', 'wp-user-frontend' ); ?>">

            <h3><?php esc_html_e( 'Need Any Assistance?', 'wp-user-frontend' ); ?></h3>

            <p><?php esc_html_e( 'Our EXPERT Support Team is always ready to Help you out.', 'wp-user-frontend' ); ?></p>

            <a target="_blank" class="button button-primary" href="https://wedevs.com/account/tickets/?utm_source=wpuf-help-page&utm_medium=help-block&utm_campaign=need-assistance"><?php esc_html_e( 'Contact Support', 'wp-user-frontend' ); ?></a>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(function($) {
        var tabs = $('.wpuf-help-tabbed > nav > ul > li' ),
            items = $('.wpuf-help-tabbed .nav-content > section');

        tabs.first().addClass('tab-current');
        items.first().addClass('content-current');

        tabs.on('click', 'a', function(event) {
            event.preventDefault();

            var self = $(this);

            tabs.removeClass('tab-current');
            self.parent('li').addClass('tab-current');

            $.each(items, function(index, val) {
                var element = $(val);

                if ( '#' + element.attr( 'id' ) === self.attr('href') ) {
                    element.addClass('content-current');
                } else {
                    element.removeClass('content-current');
                }
            });
        });

        $('#wpuf-form-subscribe').submit(function(e) {
            e.preventDefault();

            var form = $(this);

            form.find('input[type="submit"]').prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'GET',
                dataType: 'json',
                cache: false,
                contentType: "application/json; charset=utf-8",
            })
            .done(function(data) {
                // console.log(data);

                if (data.result != "success") {
                    // do something
                }
            })
            .fail(function() {
                // console.log("error");
            })
            .always(function(response) {
                $('.form-wrap', form).html( '<div class="thank-you">' + response.msg + '</div>' );
            });
        });
    });
</script>
