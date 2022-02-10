<style>
	/* Welcome Screen */

	#wpuf-welcome{
		font-family: "Open Sans", sans-serif;
	}

	#wpuf-welcome .container{
		margin: 0 auto;
		max-width: 700px;
		padding: 0;
	}

	#wpuf-welcome .intro{
		margin-bottom: 60px;
	}

	#wpuf-welcome .header {
		display: flex;
		flex-wrap: wrap;
		align-content: center;
		margin: 50px 0 40px;
	}
	#wpuf-welcome .header > *{
		width: 50%;
	}
	#wpuf-welcome .header .text{
		text-align: right;

	}
	#wpuf-welcome .header .text h1 {
		color: #647182;
		font-size: 23px;
		line-height: 1.2em;
		margin-right: 26px;
	}

	#wpuf-welcome .video-block{
		background-color: #fff;
		border: 1px solid rgb(224, 233, 236);
		box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.07);
	}
	#wpuf-welcome .video-block .play-video{
		display: block;
		position: relative;
	}
	#wpuf-welcome .video-block .play-video:before {
		position: absolute;
		 font-family: dashicons;
		content: "\f522";
		color: #fff;
		background-color: #0c5bdf;
		border-radius: 50%;
		width: 69px;
		height: 69px;
		font-size: 30px;
		text-align: center;
		line-height: 69px;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
		background-image: -moz-linear-gradient( 145deg, rgb(12,88,213) 0%, rgb(13,98,244) 99%);
		background-image: -webkit-linear-gradient( 145deg, rgb(12,88,213) 0%, rgb(13,98,244) 99%);
		background-image: -ms-linear-gradient( 145deg, rgb(12,88,213) 0%, rgb(13,98,244) 99%);
		box-shadow: 0px 20px 38px 0px rgba(94, 152, 239, 0.77);
	}
	#wpuf-welcome .video-block .play-video:hover:before {
		background-image: -moz-linear-gradient( 145deg, rgb(13, 185, 159) 0%, rgb(128, 185, 6) 99%);
		background-image: -webkit-linear-gradient( 145deg, rgb(13, 185, 159) 0%, rgb(128, 185, 6) 99%);
		background-image: -ms-linear-gradient( 145deg, rgb(13, 185, 159) 0%, rgb(128, 185, 6) 99%);
	}
	#wpuf-welcome .video-block .play-video img{
		max-width: 100%;
	}

	#wpuf-welcome .action-block{
		text-align: center;
		padding: 18px 20px 22px;
	}

	#wpuf-welcome .wpuf-btn {
		cursor: pointer;
		display: inline-block;
		text-decoration: none;
		text-align: center;
		vertical-align: middle;
		white-space: nowrap;
		font-size: 16px;
		font-weight: 400;
		padding: 16px 28px;
		margin: 8px;
		border-radius: 3px;
		border: 1px solid rgb(204, 204, 204);
		box-shadow: 0px 1px 0px 0px rgba(204, 204, 204, 0.004);
		transition: all .2s;
	}

	#wpuf-welcome .wpuf-btn.primary {
		background-color: #0085ba;
		border-color: #006799;
		color: #fff;
	}
	#wpuf-welcome .wpuf-btn.primary:hover{
		background-color: #0473a9;
	}

	#wpuf-welcome .wpuf-btn.default {
		background-color: #fff;
		color: #000;
	}
	#wpuf-welcome .wpuf-btn.default:hover{
		background-color: #EFEFEF;
	}

	#wpuf-welcome .features-section h1{
		color: #000;
		font-size: 33px;
		line-height: 1.2em;
		text-align: center;
		margin-bottom: 40px;
	}
	#wpuf-welcome .features-section .section, .upgrade-section .section{
		padding: 30px;
		border-radius: 3px;
		border: 1px solid rgb(224, 233, 236);
		background-color: #fff;
		box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.07);
		overflow: hidden;
	}

	#wpuf-welcome .features-section .features-block{

	}

	#wpuf-welcome .features-section .features-block h2 {
		border: 2px solid #eff2f7;
		border-radius: 3px;
		background-color: rgb(246, 248, 251);
		color: #000;
		font-size: 23px;
		line-height: 1.2em;
		font-weight: 400;
		padding: 15px;
		margin: 20px 0 10px 0;
	}

	#wpuf-welcome .features-section .features-block h2 img {
		display: inline-block;
		vertical-align: middle;
		margin-right: 10px;
	}

	#wpuf-welcome .features-section .features-list {
		display: flex;
		flex-wrap: wrap;
	}
	#wpuf-welcome .features-section .features-list > *{
		width: 48%;
		margin-bottom: 25px;
		margin-top: 25px;
	}
	#wpuf-welcome .features-section .features-list > .feature-block:nth-child(even) {
		margin-left: 2%;
	}
	#wpuf-welcome .features-section .features-list > .feature-block:nth-child(odd) {
		margin-right: 2%;
	}

	#wpuf-welcome .features-section .features-list img {
		float: left;
		max-width: 60px;
	}
	#wpuf-welcome .features-section .features-list h5 {
		margin: 0 0 8px 80px;
		font-size: 16px;
		line-height: 1.2em;
		font-weight: 600;
		color: #23282d;
	}
	#wpuf-welcome .features-section .features-list p {
		margin: 0px 0px 0px 80px;
		font-size: 14px;
	}

	#wpuf-welcome .upgrade-section{
		margin-top: 35px;
	}
	#wpuf-welcome .upgrade-section h2{
		color: #242e3c;
		font-size: 30px;
		text-align: center;
		line-height: 1.2em;
		margin-bottom: 87px;
	}

	#wpuf-welcome .upgrade-section .left {
		float: left;
		width: 60%;
		padding-right: 2%;
		margin-bottom: 50px;
	}
	#wpuf-welcome .upgrade-section .right {
		float: right;
		width: 28%;
		margin-left: 2%;
		text-align: center;
		border: 1px solid #e8f4f3;
		border-radius: 5px;
		background-color: rgb(255, 255, 255);
		box-shadow: 0px 20px 50px 0px rgba(8, 101, 67, 0.15);
		position: relative;
		padding: 3%;
		margin-bottom: 20px;
	}

	#wpuf-welcome .upgrade-section ul {
		display: -ms-flex;
		display: -webkit-flex;
		display: flex;
		-webkit-flex-wrap: wrap;
		flex-wrap: wrap;
		font-size: 15px;
		margin: 0;
		padding: 0;
	}
	#wpuf-welcome .upgrade-section ul li {
		display: block;
		width: 50%;
		margin: 0 0 15px 0;
		padding: 0;
	}
	#wpuf-welcome .upgrade-section ul li .dashicons {
		color: #fff;
		background-color: #1abc9c;
		border-radius: 50%;
		height: 20px;
		width: 20px;
		line-height: 20px;
		margin-right: 5px;
		text-align: center;
		display: inline-block;
	}

	#wpuf-welcome .upgrade-section .right .price{
		font-size:35px;
		color: #fff;
		display: block;
		width: 87px;
		height: 87px;
		text-align: center;
		border: 10px solid #fff;
		border-radius: 50%;
		line-height: 80px;
		position: absolute;
		top: -50px;
		left: 50%;
		transform: translateX(-50%);
		text-shadow: 1.026px 2.819px 10px rgba(3, 96, 16, 0.23);
		background-image: -moz-linear-gradient( 90deg, rgb(126,213,0) 0%, rgb(0,191,141) 100%);
		background-image: -webkit-linear-gradient( 90deg, rgb(126,213,0) 0%, rgb(0,191,141) 100%);
		background-image: -ms-linear-gradient( 90deg, rgb(126,213,0) 0%, rgb(0,191,141) 100%);
		box-shadow: 0.698px 19.988px 50px 0px rgba(14, 157, 34, 0.26);

	}
	#wpuf-welcome .upgrade-section .right .price sup {
		font-size: 17px;
	}
	#wpuf-welcome .upgrade-section .right .term{
		display: block;
		margin-top: 60px;
		margin-bottom: 15px;
		color: #8e9297;
		font-size: 14px;
	}

	#wpuf-welcome .footer {
		background-color: #3d566e;
		margin-top: 35px;
		border-radius: 3px;
	}

	@media (max-width: 1190px) {
		#wpuf-welcome .container{
			max-width: 95%;
		}
	}
	@media (max-width: 768px) {
		#wpuf-welcome .header{
			display: block;
			text-align: center;
		}

		#wpuf-welcome .header > * {
			width: 100%;
		}
		#wpuf-welcome .header .text {
			text-align: center;
		}
		#wpuf-welcome .header .text h1{
			margin:25px;
		}
		#wpuf-welcome .features-section .features-list{
			display: block;
		}
		#wpuf-welcome .features-section .features-list > *{
			width: 100%;
			margin-left: 0 !important;
			margin-right: 0 !important;
			clear: both;
			overflow: hidden;
		}
		#wpuf-welcome .upgrade-section .left{
			float: none;
			width: 100%;
		}
		#wpuf-welcome .upgrade-section .right{
			float: none;
			margin: 90px 0 0;
			width: 94%;
		}
	}

	@media (max-width: 600px) {
		#wpuf-welcome .features-section .features-list img{
			max-width: 45px;
		}
		#wpuf-welcome .features-section .features-list h5{
			margin: 0 0 10px 60px;
		}
		#wpuf-welcome .features-section .features-list p{
			margin: 0px 0px 0px 60px;
		}
		#wpuf-welcome .features-section .features-block h2, .features-section .features-list{
			margin-bottom: 50px;
		}
		#wpuf-welcome .upgrade-section ul{
			display: block;
		}
		#wpuf-welcome .upgrade-section ul li{
			width: 100%;
		}

		#wpuf-welcome .features-section .features-block h2{
			font-size: 17px;
			line-height: 1.2;
			text-align: center;
		}
		#wpuf-welcome .features-section .features-block h2 img{
			display: block;
			margin: 0 auto 10px;
		}
	}



    .wpuf-welcome-modal {
        background: #fff;
        position: fixed;
        top: 5%;
        bottom: 5%;
        right: 10%;
        left: 10%;
        display: none;
        box-shadow: 0 1px 20px 5px rgba(0, 0, 0, 0.1);
        z-index: 160000;
    }

    .wpuf-welcome-modal .video-wrap {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        padding-top: 25px;
        height: 0;
    }

    .wpuf-welcome-modal .video-wrap iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .wpuf-welcome-modal .learn-more {
        position: absolute;
        bottom: 0;
        right: 10px;
        background: #fff;
        padding: 10px;
        border-radius: 3px;
    }

    .wpuf-welcome-modal a.close {
        position: absolute;
        top: 20px;
        right: -60px;
        font: 300 1.71429em "dashicons" !important;
        content: '\f335';
        display: inline-block;
        padding: 10px 20px 0 20px;
        z-index: 5;
        text-decoration: none;
        height: 40px;
        cursor: pointer;
        background: #000;
        color: #fff;
        border-radius: 50%;
    }

    .wpuf-welcome-modal-backdrop {
        position: fixed;
        z-index: 159999;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        min-height: 360px;
        background: #000;
        opacity: .7;
        display: none;
    }

    .wpuf-welcome-modal.show,
    .wpuf-welcome-modal-backdrop.show {
        display: block;
    }
</style>
<div id="wpuf-welcome" class="lite">

	<div class="container">
		<div class="intro">
			<div class="header">
				<div class="text">
					<h1>Welcome to</h1>
				</div>

				<div class="logo">
					<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/wpuf-logo.png" alt="WP User Frontend Pro">
				</div>
			</div>
			<div class="video-block" id="wpuf-welcome-prompt">
				<a href="#" class="play-video learn-more" title="Watch how to create your first form" data-tube="NJvjy9WFyAM">
					<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/welcome-video.png" alt="Watch how to create your first form" class="video-thumbnail">
				</a>
				<div class="action-block">
					<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wpuf-post-forms' ) ); ?>" class="wpuf-btn primary"><?php esc_html_e( 'Create Your First Form', 'wp-user-frontend' ); ?></a>
					<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wpuf-support' ) ); ?>" class="wpuf-btn default"><?php esc_html_e( 'Read the Full Guide', 'wp-user-frontend' ); ?></a>
				</div>
			</div>
			<div class="wpuf-welcome-modal" id="wpuf-welcome-modal">
	            <a class="close">
	                &times;
	                <span class="screen-reader-text">Close modal window</span>
	            </a>
	            <div class="video-wrap">
	                <iframe id="wpuf-welcome-modal-iframe" width="1280" height="720" src="" frameborder="0" allowfullscreen></iframe>
	            </div>
	        </div>
	        <div class="wpuf-welcome-modal-backdrop" id="wpuf-welcome-modal-backdrop"></div>
		</div><!-- /.intro -->

		<div class="features-section">

			<h1>What’s Next...</h1>

			<div class="section">
				<div class="features-block">
					<h2><img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/frontend-posting.png" alt="Frontend Posting"> Frontend Posting</h2>

					<div class="features-list">
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Form-Builder.svg">
							<h5>Post Form Builder</h5>
							<p><a href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/creating-posting-forms/" target="_blank">Design your forms</a> with drag & Drop builder with live preview</p>
						</div>

						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Support.svg">
							<h5>Custom Field Support</h5>
							<p>Build exclusive submission forms with <a href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/form-elements/" target="_blank">30+ custom field types</a>.</p>
						</div>
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Post-Taxonomies.svg">
							<h5>Post Types & Taxonomies</h5>
							<p>Unleash your creativity with <a href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/different-custom-post-type-submission-2/" target="_blank">custom post types</a> & taxonomies</p>
						</div>
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Guest-Posting.svg">
							<h5>Guest Posting</h5>
							<p>Allow your guests to post from the frontend with full capabilities. <a href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/guest-posting/" target="_blank">Learn more</a>.</p>
						</div>
					</div>
				</div>
				<div class="features-block">
					<h2><img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Profile-Builder.png" alt="Frontend Posting"> Registration &amp; Profile Builder</h2>

					<div class="features-list">
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Registration-form.svg">
							<h5>Registration form builder</h5>
							<p>Create <a href="https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/registration-forms/" target="_blank">frontend registration forms</a> with powerful builder using shortcodes.</p>
						</div>

						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Profile-Builder.svg">
							<h5>User Profile Builder</h5>
							<p>Use shortcodes to publish frontend profile page and <a href="https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/wordpress-edit-user-profile-from-front-end/" target="_blank">profile edit</a> page.</p>
						</div>
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/My-Account.svg">
							<h5>My Account on Frontend</h5>
							<p>Use shortcodes to generate frontend <a href="https://wedevs.com/docs/wp-user-frontend-pro/frontend/how-to-create-my-account-page/" target="_blank">my account</a> pages.</p>
						</div>
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Create-Database.svg">
							<h5>Login Page</h5>
							<p>Create themed login and registration page for a unified user experience for the user.</p>
						</div>
					</div>
				</div>
				<div class="features-block">
					<h2><img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Subscriptions.png" alt="Subscriptions"> Subscriptions</h2>

					<div class="features-list">
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/User.svg">
							<h5>Charge User for Posting</h5>
							<p><a href="https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/" target="_blank">Accept payments</a> from multiple gateways for post submissions</p>
						</div>

						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/pay-per-post.svg">
							<h5>Pay-per-post on Forms</h5>
							<p>Earn from <a href="https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/how-to-charge-for-each-post-submission/" target="_blank">each guest post</a> with different subscription packs</p>
						</div>
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Content-Locking.svg">
							<h5>Content Locking</h5>
							<p><a href="https://wedevs.com/docs/wp-user-frontend-pro/content-restriction/" target="_blank">Lock high value &amp; quality contents</a> for your subscribed users</p>
						</div>
						<div class="feature-block">
							<img src="<?php echo esc_url( WPUF_ASSET_URI ); ?>/images/welcome/Subscription-Signup.svg">
							<h5>Subscription Signup</h5>
							<p>Build a membership site where users can <a href="https://wedevs.com/docs/wp-user-frontend-pro/registration-profile-forms/paid-membership-registration/" target="_blank">signup with a subscription plan</a>.</p>
						</div>
					</div>
				</div>

				<div class="action-block">
					<a href="" class="wpuf-btn primary" rel="noopener noreferrer" target="_blank">See All Features</a>
				</div>
			</div>

		</div><!-- /.features-section -->

		<div class="upgrade-section">
			<div class="section">
				<h2>Upgrade to PRO</h2>

				<div class="left">
					<ul>
						<li><span class="dashicons dashicons-yes"></span> Unlock More Fields</li>
						<li><span class="dashicons dashicons-yes"></span> Registration Forms</li>
						<li><span class="dashicons dashicons-yes"></span> Content Restriction</li>
						<li><span class="dashicons dashicons-yes"></span> Menu Restriction</li>
						<li><span class="dashicons dashicons-yes"></span> Email Notification</li>
						<li><span class="dashicons dashicons-yes"></span> Discount Coupons</li>
						<li><span class="dashicons dashicons-yes"></span> Custom Post Types</li>
						<li><span class="dashicons dashicons-yes"></span> Multistep Form</li>
						<li><span class="dashicons dashicons-yes"></span> Stripe Payment</li>
						<li><span class="dashicons dashicons-yes"></span> Much More...</li>
					</ul>
				</div>

				<div class="right">
					<span class="price"><sup>$</sup>49</span>
					<span class="term">Per Year</span>
					<a href="https://wedevs.com/wp-user-frontend-pro/pricing/" class="wpuf-btn primary" target="_blank">Upgrade Now</a>
				</div>

			</div>

		</div><!-- /.upgrade-section -->

		<div class="footer">
			<div class="action-block">
				<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wpuf-post-forms' ) ); ?>" class="wpuf-btn primary"><?php esc_html_e( 'Create Your First Form', 'wp-user-frontend' ); ?></a>
				<a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=wpuf-support' ) ); ?>" class="wpuf-btn default"><?php esc_html_e( 'Read the Full Guide', 'wp-user-frontend' ); ?></a>
			</div>
		</div><!-- /.footer -->

	</div><!-- /.container -->
	<script type="text/javascript">
        (function ($) {
            var wrapper = $('#wpuf-welcome-prompt'),
                modal = $('#wpuf-welcome-modal'),
                modalBackdrop = $('#wpuf-welcome-modal-backdrop'),
                iframe = $('#wpuf-welcome-modal-iframe');

            wrapper.on('click', 'a.learn-more', function(e) {
                e.preventDefault();

                modal.addClass('show');
                modalBackdrop.addClass('show');

                iframe.attr( 'src', 'https://www.youtube.com/embed/rzxdIN8ZMYc?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;hd=1' );
            });

            $('body').on('click', '.wpuf-welcome-modal a.close', function(e) {
                e.preventDefault();

                console.log('close modal');

                modal.removeClass('show');
                modalBackdrop.removeClass('show');

                iframe.attr( 'src', '' );
            });
        })(jQuery);
    </script>
</div>
