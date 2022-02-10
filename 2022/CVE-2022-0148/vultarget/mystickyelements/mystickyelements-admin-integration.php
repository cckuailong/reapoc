<?php
global $wp_version ;
?>
<div class="mystickyelement-new-widget-wrap">
	<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins" />	
	<h2 class="text-center mystickyelement-integrate-title-main"><?php esc_html_e( 'Upgrade to Pro and connect your My Sticky Elements form to the following platforms to automatically receive leads', 'mystickyelements' ); ?></h2>
	<div class="mystickyelement-new-widget-row">
		<div class="mystickyelement-features">
			<ul>
				<li>
					<div class="elements-int-container mystickyelement-feature">
						<div class="mystickyelement-feature-top">
							<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/mailchimp.png" />
						</div>
						<div class="feature-title">Connect your forms to MailChimp</div>
						<div id="elements-int-container-content feature-description">
							<p>
							<a href="#" class="integrate-element-form button-primary  ">
								<?php echo 'Connect';?>
							</a>
							</p>
						</div>
					</div>
					<div class="mystickyelement-integration-button">
						<a href="<?php echo esc_url(admin_url("admin.php?page=my-sticky-elements-upgrade")); ?>" class="new-upgrade-button" target="blank">Upgrade to Pro</a>
					</div>
				</li>
				<li>
					<div class="elements-int-container mystickyelement-feature">
						<div class="mystickyelement-feature-top">
							<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/mailpoet.png" />
						</div>
						<div class="feature-title">Connect your forms to MailPoet</div>
						<div id="elements-int-container-content feature-description">
							<?php							
							$admin_message = '';
							$activation_url = '#';
								
							$admin_message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Connect' ) ) . '</p>';
							
							echo $admin_message;
							
							?>			
						</div>
					</div>
					<div class="mystickyelement-integration-button">
						<a href="<?php echo esc_url(admin_url("admin.php?page=my-sticky-elements-upgrade")); ?>" class="new-upgrade-button" target="blank">Upgrade to Pro</a>
					</div>
				</li>
			</ul>
			<div class="clear clearfix"></div>
		</div>
        <div class="mystickyelement-integration-upgrade-button">
		    <a href="<?php echo esc_url(admin_url("admin.php?page=my-sticky-elements-upgrade")); ?>" class="new-upgrade-button" target="blank">Upgrade to Pro</a>
        </div>
	</div>	
</div>

<style>
*, ::after, ::before {
    box-sizing: border-box;
}
/*New Widget Page css*/
.mystickyelement-new-widget-wrap {
	background: #fff;
	padding: 30px;
	margin: 20px auto 0 auto;
	width: 100%;
	font-family: Poppins;
	line-height: 20px;
}
.mystickyelement-features {
	padding-top: 40px;
	max-width: 776px;
	margin: 0 auto;
}
.mystickyelement-new-widget-wrap h2 {
	font-style: normal;
	font-weight: 600;
	font-size: 20px;
	line-height: 30px;
	color: #1e1e1e;
	margin: 0;
	text-align: center;
}
.mystickyelement-new-widget-wrap h2.mystickyelement-integrate-title-main {
	font-style: normal;
	font-weight: 500;
	font-size: 18px;
	line-height: 1.5;
	color: #1E1E1E;
	margin: 0 auto;
	max-width: 530px;
	position: relative;
	padding-bottom: 30px;
}
.mystickyelement-new-widget-wrap h2.mystickyelement-integrate-title-main::after {
	content: "";
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	width: 158px;
	height: 1px;
	background-color: #3C85F7;
	margin: 0 auto;
}
.mystickyelement-features ul {
    margin: 0;
    padding: 0;
}
.mystickyelement-features ul li {
    margin: 0;
    width: 50%;
    float: left;
    padding: 10px;
	position: relative;
}
.mystickyelement-feature {
	background: #fff;
	border-radius: 10px;
	padding: 60px 20px 10px 20px;
	height: 100%;
	position: relative;
	box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.06), 0px 4px 6px rgba(0, 0, 0, 0.1);
}
.mystickyelement-feature-top {
	width: 73px;
	height: 73px;
	border-radius: 50%;
	position: absolute;
	left: 0;
	right: 0;
	margin: 0 auto;
	top: -25px;
	background: #fff;
	z-index: 11;
	padding: 10px;
	box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.06), 0px 1px 3px rgba(0, 0, 0, 0.1);
}
.feature-title {
	font-style: normal;
	font-weight: 400;
	font-size: 16px;
	line-height: 18px;
	color: #64748B;
	margin-bottom: 15px;
	text-align: center;
}
.mystickyelement-feature.second {
    min-height: 155px;
}
.feature-description {
    font-family: Poppins;
    font-style: normal;
    font-weight: normal;
    font-size: 13px;
    line-height: 18px;
    color: #1E1E1E;
}
a.new-upgrade-button {
    height: 40px;
    background: #605DEC;
    border-radius: 100px;
    border: solid 1px #605DEC;
    display: inline-block;
    text-align: center;
    color: #fff;
    line-height: 40px;
    margin: 10px 0 10px 10px;
    padding: 0 25px;
    text-decoration: none;
    text-transform: uppercase;
}
a.new-demo-button {
    height: 40px;
    color: #605DEC;
    border: solid 1px #605DEC;
    border-radius: 100px;
    display: inline-block;
    text-align: center;
    background: #fff;
    line-height: 40px;
    margin: 10px 0 10px 10px;
    padding: 0 25px;
    text-decoration: none;
    width: 165px;
}
.mystickyelement-feature.analytics {
    min-height: 115px;
}
.mystickyelement-feature-top img {
    width: 100%;
    height: auto;
}

.mystickyelement-features ul li:hover .mystickyelement-integration-button{
	display: block;
}
.mystickyelement-integration-button {
	display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    z-index: 9;
}
.mystickyelement-feature input[type="text"] {
	border: 1px solid #E2E8F0;
	color: #9CA3AF;
	font-size: 12px;
}
.mystickyelement-feature .button-primary {
	border: 1px solid #3C85F7;
	background-color: transparent;
	color: #3C85F7;
	padding: 5px 17px;
	line-height: 1;
	border-radius: 2px;
}
.mystickyelement-feature a.button-primary {
	padding-top: 7px;
}
.mystickyelement-feature .button-primary.btn-connected {
	border-color: #057A55;
	color: #057A55;
}
.mystickyelement-feature .button-primary.btn-disconnected {
	color: #B91C1C;
	border: 0;
	padding: 0;
	background-color: transparent;
}
.mystickyelement-integration-upgrade-button {
	text-align: center;
}

</style>