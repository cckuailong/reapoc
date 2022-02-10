<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED About Page Header
 * @since 1.3.2
 * @version 1.4
 */
function mycred_about_header() {

	$name = mycred_label();

?>
<style type="text/css">
#mycred-welcome {
    color: #555;
    padding-top: 110px;
}
#mycred-welcome .container {
    margin: 0 auto;
    max-width: 720px;
    padding: 0;
}
#mycred-welcome .intro {
    background-color: #fff;
    border: 2px solid #e1e1e1;
    border-radius: 2px;
    margin-bottom: 30px;
    position: relative;
    padding-top: 40px;
}
#mycred-welcome .intro .mycred-logo {
	background: url('<?php echo plugins_url( 'assets/images/mycred-icon.png', myCRED_THIS ); ?>') no-repeat center center; 
	background-size: 95px;
	display: block;
    margin: auto;
    box-shadow: none;

    background-color: #fff;
    border: 2px solid #e1e1e1;
    border-radius: 50%;
    height: 110px;
    width: 110px;
    padding: 18px 14px 0 14px;
    position: absolute;
    top: -58px;
    left: 50%;
    margin-left: -55px;
}
#mycred-welcome img {
    max-width: 100%;
    height: auto;
}
#mycred-welcome .block {
    padding: 40px;
}
#mycred-welcome h1 {
    color: #222;
    font-size: 24px;
    text-align: center;
    margin: 0 0 16px 0;
}
#mycred-welcome h6 {
    font-size: 16px;
    font-weight: 400;
    line-height: 1.6;
    text-align: center;
    margin: 0;
}
#mycred-welcome .intro .button-wrap {
    margin-top: 25px;
}

#mycred-welcome .button-wrap {
    max-width: 590px;
    margin: 0 auto 0 auto;
}
.mycred-clear:before {
    content: " ";
    display: table;
}
#mycred-welcome .button-wrap .left {
    float: left;
    width: 50%;
    padding-right: 20px;
}
#mycred-welcome .button-wrap .center {
    width: 50%;
    margin: 0 auto;
    padding-right: 20px;
}
.mycred-admin-page .mycred-btn-orange {
    background-color: #9852f1;
    color: #fff;
}

.mycred-admin-page .mycred-btn-lg {
    font-size: 16px;
    font-weight: 600;
    padding: 16px 28px;
}
.mycred-admin-page .mycred-btn-block {
    display: block;
    width: 100%;
}
.mycred-admin-page .mycred-btn {
    border: 1px;
    border-style: solid;
    border-radius: 3px;
    cursor: pointer;
    display: inline-block;
    margin: 0;
    text-decoration: none;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    box-shadow: none;
}
#mycred-welcome .button-wrap .right {
    float: right;
    width: 50%;
    padding-left: 20px;
}
.mycred-admin-page .mycred-btn-grey {
    background-color: #eee;
    border-color: #ccc;
    color: #666;
}
.mycred-clear:after {
    clear: both;
    content: " ";
    display: table;
}
#mycred-welcome .features {
    background-color: #fff;
    border: 2px solid #e1e1e1;
    border-bottom: 0;
    border-radius: 2px 2px 0 0;
    position: relative;
    padding-top: 20px;
    padding-bottom: 20px;
}
#mycred-welcome .features .feature-list {
    margin-top: 60px;
}
#mycred-welcome .features .feature-block.first {
    padding-right: 20px;
    clear: both;
}
#mycred-welcome .features .feature-block {
    float: left;
    width: 50%;
    padding-bottom: 35px;
    overflow: auto;
}
#mycred-welcome *, #mycred-welcome *::before, #mycred-welcome *::after {
    /* -webkit-box-sizing: border-box; */
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
#mycred-welcome .features .feature-block img {
    float: left;
    max-width: 46px;
}
#mycred-welcome .features .feature-block h5 {
    margin-left: 68px;
}

#mycred-welcome h5 {
    color: #222;
    font-size: 16px;
    margin: 0 0 8px 0;
}
#mycred-welcome .features .feature-block p {
    margin: 0;
    margin-left: 68px;
}

#mycred-welcome p {
    font-size: 14px;
    margin: 0 0 20px 0;
}
#mycred-welcome .features .feature-block.last {
    padding-left: 20px;
}
#mycred-welcome .features .button-wrap {
    margin-top: 25px;
    text-align: center;
}
#mycred-welcome .upgrade-cta {
    background-color: #000;
    border: 2px solid #e1e1e1;
    border-top: 0;
    border-bottom: 0;
    color: #fff;
}
#mycred-welcome .upgrade-cta .left {
    float: left;
    width: 66.666666%;
    padding-right: 20px;
}
#mycred-welcome .upgrade-cta h2 {
    color: #fff;
    font-size: 20px;
    margin: 0 0 30px 0;
}
#mycred-welcome .upgrade-cta ul {
    display: -ms-flex;
    display: -webkit-flex;
    display: flex;
    -webkit-flex-wrap: wrap;
    flex-wrap: wrap;
    font-size: 15px;
    margin: 0;
    padding: 0;
}
#mycred-welcome .upgrade-cta ul li {
    display: block;
    width: 50%;
    margin: 0 0 8px 0;
    padding: 0;
}
#mycred-welcome .upgrade-cta ul li .dashicons {
    color: #2a9b39;
    margin-right: 5px;
}
.dashicons-yes:before {
    content: "\f147";
}
#mycred-welcome .upgrade-cta .right {
    float: right;
    width: 33.333333%;
    padding: 20px 0 0 20px;
    text-align: center;
}
#mycred-welcome .upgrade-cta .right h2 {
    text-align: center;
    margin: 0;
}

#mycred-welcome .upgrade-cta h2 {
    color: #fff;
    font-size: 20px;
    margin: 0 0 30px 0;
}
#mycred-welcome .upgrade-cta .right h2 span {
    display: inline-block;
    border-bottom: 1px solid #555;
    padding: 0 15px 12px;
}
#mycred-welcome .upgrade-cta .right .price {
    padding: 26px 0;
}
#mycred-welcome .upgrade-cta .right .price .amount {
    font-size: 48px;
    font-weight: 600;
    position: relative;
    display: inline-block;
}
#mycred-welcome .upgrade-cta .right .price .amount:before {
    content: '$';
    position: absolute;
    top: -8px;
    left: -16px;
    font-size: 18px;
}
#mycred-welcome .upgrade-cta .right .price .term {
    font-size: 12px;
    display: inline-block;
}
#mycred-welcome .testimonials {
    background-color: #fff;
    border: 2px solid #e1e1e1;
    border-top: 0;
    padding: 20px 0;
}
#mycred-welcome .testimonials .testimonial-block {
    margin: 50px 0 0 0;
}
#mycred-welcome .testimonials .testimonial-block img {
    float: left;
    max-width: 50px;
}
#mycred-welcome .testimonials .testimonial-block p {
    font-size: 14px;
    margin: 0 0 12px 95px;
}
#mycred-welcome .testimonials .testimonial-block p:last-of-type {
    margin-bottom: 0;
}
b, strong {
    font-weight: 600;
	font-style: italic;
}
#mycred-welcome .footer {
    background-color: #f9f9f9;
    border: 2px solid #e1e1e1;
    border-top: 0;
    border-radius: 0 0 2px 2px;
}
.mycred-admin-page .mycred-footer-btn {
    margin-left: 60%;
}
.clear {
    clear: both;
}
#mycred-welcome .mycred-change-log {
	padding: 32px;
	margin-top: 32px;
	background-color: #fff;
    border: 2px solid #e1e1e1;
    border-top: 0;
    padding: 20px 0;
}
#mycred-welcome .mycred-change-log ul{
	list-style: inside;
}
.members {
    padding-bottom: 25px;
}

</style>
<div class="mycred-admin-page">
<div id="mycred-welcome" class="lite">

			<div class="container">

				<div class="intro">

					<div class="mycred-logo">
					</div>

					<div class="block">
					<h1><?php printf( __( 'Welcome to %s %s', 'mycred' ), $name, myCRED_VERSION ); ?></h1>
						<h6>Thank you for choosing myCred - the most powerful points management system for WordPress. Build and manage a broad range of digital rewards, including points, ranks, and badges on your WordPress-powered website.
</h6>
					</div>
<?php

}

/**
 * myCRED About Page Footer
 * @since 1.3.2
 * @version 1.2
 */
function mycred_about_footer() {

?>
<?php 
if ( !is_mycred_ready() ) {
?>
<div class="footer">

<div class="block mycred-clear">

	<div class="button-wrap mycred-clear">
		<div class="left">
			<a href="<?php echo admin_url( 'plugins.php?page=' . MYCRED_SLUG . '-setup&mycred_tour_guide=1' ) ?>" id="first_setup" onclick="startTour()" class="mycred-btn mycred-btn-block mycred-btn-lg mycred-btn-orange mycred-footer-btn">
			Setup myCred
			</a>
		
		</div>
	</div>

</div>

</div><!-- /.footer -->
<?php
}
?>
<p style="margin: 15px; text-align: center;">A big Thank You to everyone who helped support myCred!</p>
	
<?php

}

/**
 * About myCRED Page
 * @since 1.3.2
 * @version 1.4
 */
function mycred_about_page() {

?>
<div class="mycred-welcome">
	<div class="wrap mycred_about_container" id="mycred-about-wrap">
		<div class="mycred-intro">
			<?php 

			$name = mycred_label();

			mycred_about_header(); 

			?>
	
				<img src="<?php echo plugins_url( 'assets/images/about/welcome.png', myCRED_THIS ); ?>" alt="Welcome" class="video-thumbnail">

			<div class="block">
				
				<div class="button-wrap mycred-clear">
					<div class="left">
						<?php 
						if ( !is_mycred_ready() ) {
						?>
							<a href="<?php echo admin_url( 'plugins.php?page=' . MYCRED_SLUG . '-setup&mycred_tour_guide=1' ) ?>" id="first_setup" onclick="startTour()" class="mycred-btn mycred-btn-block mycred-btn-lg mycred-btn-orange">
								Setup myCred
							</a>
						<?php
						}
						?>
					</div>
					<div class="<?php echo ( is_mycred_ready() ? 'center' : 'right' ); ?>">
						<a href="https://codex.mycred.me/" class="mycred-btn mycred-btn-block mycred-btn-lg mycred-btn-grey" target="_blank" rel="noopener noreferrer">
							Documentation
						</a>
					</div>
				</div>
			</div>

		</div><!-- /.intro -->

			<div class="features">

				<div class="block">

					<h1>myCred Features &amp; Addons</h1>

					<div class="feature-list mycred-clear">

						<div class="feature-block first">
							<img src="<?php echo plugins_url( 'assets/images/about/account.png', myCRED_THIS ); ?>">
							<h5>Account History</h5>
							<p>A central log records your user's points whenever they perform an action.</p>
						</div>

						<div class="feature-block last">
							<img src="<?php echo plugins_url( 'assets/images/about/points-management.png', myCRED_THIS ); ?>">
							<h5>Points Management</h5>
							<p>Adjust your user's balance by adding or removing points with or without a log entry.</p>
						</div>

						<div class="feature-block first">
							<img src="<?php echo plugins_url( 'assets/images/about/automatic-points.png', myCRED_THIS ); ?>">
							<h5>Automatic Points</h5>
							<p>Automatically award or deduct points from your user’s balance.</p>
						</div>

						<div class="feature-block last">
							<img src="<?php echo plugins_url( 'assets/images/about/multi-points.png', myCRED_THIS ); ?>">
							<h5>Multiple Point Types</h5>
							<p>Create multiple point types through the admin area to manage things easily.</p>
						</div>

						<div class="feature-block first">
							<img src="<?php echo plugins_url( 'assets/images/about/convert-points.png', myCRED_THIS ); ?>">
							<h5>Convert Points to Cash</h5>
							<p>Users can redeem myCred points for real money.</p>
						</div>

						<div class="feature-block last">
							<img src="<?php echo plugins_url( 'assets/images/about/multi-site-support.png', myCRED_THIS ); ?>">
							<h5>Multi-Site Support</h5>
							<p>Choose between using a unique myCred installation or centralize balances across your network.</p>
						</div>

						<div class="feature-block first">
							<img src="<?php echo plugins_url( 'assets/images/about/leaderboards.png', myCRED_THIS ); ?>">
							<h5>Leaderboards</h5>
							<p>Generate leaderboards based on your user’s balance or points history.</p>
						</div>

						<div class="feature-block last">
							<img src="<?php echo plugins_url( 'assets/images/about/badges.png', myCRED_THIS ); ?>">
							<h5>Badges</h5>
							<p>Award badges to your users based on their points history.</p>
						</div>

						<div class="feature-block first">
							<img src="<?php echo plugins_url( 'assets/images/about/buy-points.png', myCRED_THIS ); ?>">
							<h5>Buy Points</h5>
							<p>Users can purchase points using real money using popular payment gateways.</p>
						</div>

						<div class="feature-block last">
							<img src="<?php echo plugins_url( 'assets/images/about/sell-content.png', myCRED_THIS ); ?>">
							<h5>Sell Content</h5>
							<p>Sell access to the content field of posts/pages or custom post types on your website.</p>
						</div>

					</div>

					<div class="button-wrap">
						<a href="https://mycred.me/store/" class="mycred-btn mycred-btn-lg mycred-btn-grey" rel="noopener noreferrer" target="_blank">
							More Addons
						</a>
					</div>

				</div>

			</div><!-- /.features -->

			<div class="upgrade-cta upgrade">

				<div class="block mycred-clear">

					<div class="left">
						<h2>Join the myCred Membership Club</h2>
						<div class="members">Join the myCred membership club today and take advantage of premium services that include priority customer support and hundreds of add-ons at a super-saver price – Save 30% on all 3-year plans!</div>
						<ul>
							<li><span class="dashicons dashicons-yes"></span> Bundle Addons</li>
							<li><span class="dashicons dashicons-yes"></span> Master License Key</li>
							<li><span class="dashicons dashicons-yes"></span> Instant Access</li>
							<li><span class="dashicons dashicons-yes"></span> Priority Support</li>
							<li><span class="dashicons dashicons-yes"></span> Multiple Sites Support</li>
							<li><span class="dashicons dashicons-yes"></span> Annually Billed Packages</li>
							<li><span class="dashicons dashicons-yes"></span> Bulk Discounted Prices</li>
							<li><span class="dashicons dashicons-yes"></span> One-click Activation</li>
						</ul>
					</div>

					<div class="right">
						<h2><span>STARTER</span></h2>
						<div class="price">
							<span class="amount">149</span><br>
							<span class="term">per year</span>
						</div>
						<a href="https://mycred.me/membership/" rel="noopener noreferrer" target="_blank" class="mycred-btn mycred-btn-block mycred-btn-lg mycred-btn-orange mycred-upgrade-modal">
							Membership Plans
						</a>
					</div>
				</div>
			</div><!-- upgrade-cta -->
			<div class="mycred-change-log">
				<div class="block">
					<h2>Change Log</h2>
					<ul>
						<li><strong>NEW</strong> - Introduced a "Bulk Assign" tool for awarding/revoking points, badges, and ranks.</li>
						<li><strong>NEW</strong> - Introduced a new feature "Exclude by user role" admin can exclude any user role from the specific point type.</li>
						<li><strong>NEW</strong> - Introduced a timeframe attribute in [mycred_my_balance_converted] shortcode. Now you can show users converted balance of a given timeframe today, yesterday, this week, this month, last month.</li>
						<li><strong>FIX</strong> - Sometimes user's ranks were not updating automatically.</li>
						<li><strong>FIX</strong> - Membership page layout issues.</li>
						<li><strong>FIX</strong> - Points conversion not working when using arrows to increase or decrease the amount in [mycred_cashcred] shortcode.</li>
						<li><strong>FIX</strong> - buyCred payment dialog couldn’t load on the subsite.</li>
						<li><strong>FIX</strong> - After deleting the badge their data will keep existing on the user's meta.</li>
						<li><strong>FIX</strong> - "this-week" value not working in [mycred_leaderboard] timeframe attribute when the week starts other than "Monday" in your WordPress setting.</li>
					</ul>
				</div>
			</div><!-- /.mycred-change-log -->
			<div class="testimonials upgrade">

				<div class="block">

					<h1>Testimonials</h1>

					<div class="testimonial-block mycred-clear">
						<img src="<?php echo plugins_url( 'assets/images/about/56826.png', myCRED_THIS ); ?>">
						<p>myCred is pretty solid WordPress plugin. You can do almost anything with it.	myCred offers a great developer codex along with hooks, and filters. The versatile collection of addons is just amazing.</p>
						<p><strong>Wooegg</strong></p>
					</div>

					<div class="testimonial-block mycred-clear">
						<img src="<?php echo plugins_url( 'assets/images/about/56826.png', myCRED_THIS ); ?>">
						<p>MyCred might be free but the add-ons it offers are absolutely incredible! myCred is the best points system for WordPress, period.</p>
						<p><strong>Rongenius</strong></p>
					</div>
					<div class="testimonial-block mycred-clear">
						<img src="<?php echo plugins_url( 'assets/images/about/56826.png', myCRED_THIS ); ?>">
						<p>myCred is highly optimized and there are a lot of functions and short codes available to customize its structure. Special congratulations to its creators!</p>
						<p><strong>Miladesmaili</strong></p>
					</div>

				</div>

			</div><!-- /.testimonials -->


			<?php mycred_about_footer(); ?>

	</div><!-- /.container -->
</div><!-- /#mycred-welcome -->
<?php

}
