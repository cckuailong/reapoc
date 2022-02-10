<?php
/**
 * Provide a admin area view for the create legal pages.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Wplegalpages
 * @subpackage Wplegalpages/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$lp_obj        = new WP_Legal_Pages();
$baseurl       = esc_url( get_bloginfo( 'url' ) );
$privacy       = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/privacy.html' );
$lp_pro_active = get_option( '_lp_pro_active' );
?>
<div class="wrap">
	<?php
	if ( '1' !== $lp_pro_active ) :
		?>
	<div style="">
		<div style="line-height: 2.4em;" class='wplegalpages-pro-promotion'>
			<a href="https://club.wpeka.com/product/wplegalpages/?utm_source=plugin-banner&utm_campaign=wplegalpages&utm_content=upgrade-to-pro" target="_blank">
				<img alt="Upgrade to Pro" src="<?php echo esc_attr( WPL_LITE_PLUGIN_URL ) . 'admin/images/upgrade-to-pro.jpg'; ?>">
			</a>
		</div>
	</div>
	<div style="clear:both;"></div>
		<?php
	endif;
	$disable_settings_warning = get_option( 'wplegalpages_disable_settings_warning' );
	if ( ! $disable_settings_warning ) {
		?>
		<div id="wplegalpages_settings_warning" class="notice notice-warning is-dismissible wplegalpages_settings_warning">
			<div>
				<p>Please make sure the website information is correct at the <a href="<?php echo esc_url_raw( admin_url() . "admin.php?page=legal-pages" ); ?>">Settings page</a> before you create a legal page.</p>
			</div> 
			<div>
				<button id="wplegal_pages_settings_warning_disable" class="button-primary">Do not show again</button>
			</div>
		</div>
		<?php
	}
	if ( ! empty( $_POST ) && isset( $_POST['lp-submit'] ) && 'Publish' === $_POST['lp-submit'] ) :
		check_admin_referer( 'lp-submit-create-page' );
		$page_title    = isset( $_POST['lp-title'] ) ? sanitize_text_field( wp_unslash( $_POST['lp-title'] ) ) : '';
		$content       = isset( $_POST['lp-content'] ) ? wp_kses_post( wp_unslash( $_POST['lp-content'] ) ) : '';
		$template_lang = isset( $_POST['template_language'] ) ? sanitize_text_field( wp_unslash( $_POST['template_language'] ) ) : '';
		$post_args     = array(
			'post_title'   => apply_filters( 'the_title', $page_title ),
			'post_content' => $content,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_author'  => 1,
		);
		$pid           = wp_insert_post( $post_args );
		update_post_meta( $pid, 'is_legal', 'yes' );
		$url = get_permalink( $pid );
		?>
	<div id="message">
		<p><span class="label label-success myAlert"><?php esc_attr_e( 'Page Successfully Created. You can view your page as a normal Page in Pages Menu.', 'wplegalpages' ); ?> </span></p>
		<p><a href="<?php echo esc_url_raw( get_admin_url() ); ?>/post.php?post=<?php echo esc_attr( $pid ); ?>&action=edit"><?php esc_attr_e( 'Edit', 'wplegalpages' ); ?></a> | <a href="<?php echo esc_url( $url ); ?>"><?php esc_attr_e( 'View', 'wplegalpages' ); ?></a></p>
	</div>
		<?php
	endif;
	$current_page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
	$lptype       = isset( $_REQUEST['lp-type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lp-type'] ) ) : '';
	$template     = isset( $_REQUEST['lp-template'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lp-template'] ) ) : '';
	if ( ! isset( $template_lang ) || empty( $template_lang ) ) {
		$template_lang = isset( $_REQUEST['lp-template-lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lp-template-lang'] ) ) : 'eng';
	}
	$general  = get_option( 'wpgattack_general' );
	$checked  = 'checked="checked"';
	$selected = 'selected="selected"';
	?>

<?php
	global $wpdb;
	$post_tbl      = $wpdb->prefix . 'posts';
	$postmeta_tbl  = $wpdb->prefix . 'postmeta';
	$countof_pages = $wpdb->get_results( $wpdb->prepare( 'SELECT count(meta_id) as cntPages FROM ' . $post_tbl . ' as ptbl, ' . $postmeta_tbl . ' as pmtbl WHERE ptbl.ID = pmtbl.post_id and ptbl.post_status=%s AND pmtbl.meta_key =  %s', array( 'publish', 'is_legal' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

	$max_limit = 15;
	$max_limit = apply_filters( 'wplegalpages_pages_limit', $max_limit );
if ( $countof_pages[0]->cntPages < $max_limit ) {
	?>

<div class="postbox ">
	<h3 class="hndle myLabel-head"  style="cursor:pointer; padding:7px 10px; font-size:20px;"> <?php esc_attr_e( 'Create Page :', 'wplegalpages' ); ?></h3>
	<div id="lp_generalid">

	<p>&nbsp;&nbsp</p>
		<form name="terms" method="post" enctype="multipart/form-data">
	<?php
	if ( ! empty( $template ) ) {

		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * from ' . $lp_obj->tablename . ' where id=%d', array( $template ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	}
	?>
			<p><input type="text" class="form-control myText" name="lp-title" id="lp-title"
		<?php if ( isset( $row->title ) ) { ?>
				value="<?php echo esc_attr( $row->title ); ?>"
			<?php } else { ?>
				value="<?php esc_attr_e( 'Privacy Policy', 'wplegalpages' ); ?>"
			<?php } ?>
				/></p>
			<p>
			<div id="poststuff">
				<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" >
				<?php
				$content    = isset( $row->content ) ? $row->content : $privacy;
				$lp_find    = array( '[Domain]', '[Business Name]', '[Phone]', '[Street]', '[City, State, Zip code]', '[Country]', '[Email]', '[Address]', '[Niche]' );
				$lp_general = get_option( 'lp_general' );
				$content    = str_replace( $lp_find, $lp_general, stripslashes( $content ) );

				// Last updated date shown in editor.
				$date    = gmdate( get_option( 'date_format' ) );
				$content = str_replace( '[Last Updated]', $date, stripslashes( $content ) );

				if ( ! shortcode_exists( 'wpl_cookie_details' ) ) {
					$content = str_replace( '[wpl_cookie_details]', '', stripslashes( $content ) );
				}
				$content   = apply_filters( 'wplegalpages_shortcode_content', $content );
				$editor_id = 'lp-content';
				$args      = array();
				wp_editor( stripslashes( html_entity_decode( $content ) ), 'content', $args );
				?>
				</div>
					<script type="text/javascript">

					function sp_content_save(){
						var obj = document.getElementById('lp-content');

						var content = document.getElementById('content');
						console.log(content);
						tinyMCE.triggerSave(0,1);
						obj.value = content.value;
					}


					</script>
					<textarea id="lp-content" name="lp-content" value="5" style="display:none" rows="10"></textarea>
			</div></p>
			<p>
				<?php
				if ( function_exists( 'wp_nonce_field' ) ) {
					wp_nonce_field( 'lp-submit-create-page' );
				}
				?>
			<input type="submit"  class="btn btn-primary mybtn" onclick="sp_content_save();" name="lp-submit" value="Publish" />
			</p>

		</form>
	</div>
<div class="lp_generalid_right_wraper" style="min-height:900px;">
	<div id="lp_generalid_right" class="postbox ">
		<h3 class="hndle"  style="cursor:pointer; padding:0px 10px 12px 10px; font-size:20px;"> <?php esc_attr_e( 'Choose Template', 'wplegalpages' ); ?> </h3>
		<div class="wplegal-choose-template">
		<select name="template_language" class="wplegal-template-language">
			<option value="eng" 
			<?php
			if ( 'eng' === $template_lang ) {
				echo 'selected';}
			?>
			>English</option>
			<option value="fr" 
			<?php
			if ( 'fr' === $template_lang ) {
				echo 'selected';}
			?>
			>French</option>
			<option value="de" 
			<?php
			if ( 'de' === $template_lang ) {
				echo 'selected';}
			?>
			>German</option>
		</select>
		</div>
		<ul class="wplegal-templates">
		<?php

		$result        = $wpdb->get_results( $wpdb->prepare( 'select * from ' . $lp_obj->tablename . ' where `is_active`=%d ORDER BY `id`', array( 1 ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$tooltip_texts = array(
			'kCjTeYOZxB'    => 'If you collect any personal data from users',
			'1r4X6y8tssz0j' => 'To limit your liability to copyright infringement claims',
			'n1bmPjZ6Xj'    => 'If you want to protect your business',
			'JRevVk8nkP'    => 'If you are collecting personal data and have California based users',
			'B8wltvJ4cB'    => "Use when you don't want your users to proceed without agreeing to website terms",
			'wOHnKlLcmo'    => 'If you have California based users and want to give them clarity on disclosure of personal information.',
			'J5GdjXkOYs'    => 'To limit your legal liability if your website is promoting strategies and programs to help users make money',
			'Xq8I33kdBD'    => 'To limit your legal liability and keep your users informed',
			'ICdlpogo8O'    => 'Use this if your website displays user reviews or endorsements',
			'HCdw9KSLn8'    => 'Use this policy to inform the users about the terms and conditions for linking to your website and disclaimers for external linking.',
			'R4CiGI3sJ4'    => 'Suitable for most eCommerce businesses which need a standard policy',
			'SVwyhB4wbf'    => 'For downloadable, licensed, or subscription based services. Eg- Software, Music, e-Books',
			'sfjX0CxRCV'    => 'For physical items which needs shipping like apparels, shoes, books etc',
			'hFrxQomrZM'    => 'For easily perishable items like food, that have a short lifespan',
			'NCknfH8jrd'    => 'If your store doesn’t give any refunds',
			'uxygs19AsJ'    => 'For a legal contract between you and your affiliates who promote your products/services',
			'9nEm1Jy29P'    => 'If you are an individual/business that uses email for advertising or promoting something',
			'dbLy6a8FAx'    => 'If you have a website and have affiliate partners, you should declare your website’s compliance with the Federal Trade Commission policies',
			'3ILrb9ARfX'    => 'To comply with Amazon’s affiliate program requirements (if you promote products listed on amazon)',
			'EdNSxwT2eB'    => 'To notify users if your website or blog is using any Double Click Dart Cookie',
			'GsnkrA9R91'    => 'If your website links to other external websites, you can use this to ensure that the external links are in compliance with the applicable laws.',
			'TwiV64Z4y1'    => 'To inform your audience about your affiliate relationships with brands, products, or companies that you publicly recommend',
			'Q9ytZuRIgJ'    => 'If you are collecting any personal data from your page through a call to action (such as email addresses for your mailing list),',
			'J2tfsnhta5'    => 'To display your contact and mailing information',
			'3PjAe6pJUc'    => 'Use when you have comments enabled on your blog',
			'Kp726GRpYC'    => 'To inform users about the cookies active on your website that track user data',
			'EfjpLEnTzv'    => 'Use when you have visitors from the EU & are using cookies on your website',
			'LuXcsW5oIn'    => 'To protect confidential and proprietary information displayed on your website',
			'EfjpLEnTzv'    => 'Use when you have visitors from the EU & are using cookies on your website',
			'RLlofiRSgd'    => 'To imply that the information on your website is not intended to be a substitute for professional medical advice, diagnosis, or treatment',
			'5o3hglUfDr'    => 'If you are collecting personal information on your website from children below 13 years in age',
			'52ahHjKsVH'    => 'If you are using an email newsletter service and collect personal information like email id from your subscribers',
			'6x5434Xdu7'    => 'Use when your website collects personal information and has visitors from the EU.',
		);

		$refund_start = false;
		$refund_end   = false;

		foreach ( $result as $ras ) {

			if ( get_option( 'wplegalpages_pro_version' ) && version_compare( get_option( 'wplegalpages_pro_version' ), '8.1.8' ) >= 0 ) {
				if ( ! $refund_start && strpos( $ras->title, 'Refund' ) !== false ) {
					$_GET['page'] = 'returns_refunds_policy';
					if ( 'eng' === $template_lang ) :
						?>
					<li class="wplegal-template-eng wplegal-accordian" id="wplegal_accordian"><span><a class="myLink" > Returns and Refunds Policy </a></span></li>
					<?php else : ?> 
					<li class="wplegal-template-eng wplegal-accordian" style="display:none;" id="wplegal_accordian"><span><a class="myLink" > Returns and Refunds Policy </a></span></li>
						<?php
					endif;
					?>
					<div class="wplegal-refund-list" id="wplegal_refund_list" style="display : none">
					<?php
					if ( 'eng' === $template_lang ) :
					?>
						<li class="wplegal-template-fr"><span><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>index.php?page=wplegal-wizard#/">Use Guided Wizard
									<span class="wplegal-tooltip"><span class="dashicons dashicons-info"></span>
										<span class="wplegal-tooltiptext">Easy Q&A wizard to help you customize your policy as per your business</span>
									</span>
								</a></span></li>
					<?php else : ?> 
						<li class="wplegal-template-fr" style="display:none;"><span><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>index.php?page=wplegal-wizard#/">Use Guided Wizard
									<span class="wplegal-tooltip"><span class="dashicons dashicons-info"></span>
										<span class="wplegal-tooltiptext">Easy Q&A wizard to help you customize your policy as per your business</span>
									</span>
								</a></span></li>			
					<?php
					endif;
					$refund_start = true;
				}

				if ( $refund_start && ! $refund_end && strpos( $ras->title, 'Refund' ) === false ) {
					?>
					</div>
					<?php
					$refund_end = true;
				}

				if ( strpos( $ras->title, 'Returns and Refunds Policy' ) !== false ) {
					$ras->title = str_replace( 'Returns and Refunds Policy:', '', $ras->title );
				}

				if ( strpos( $ras->title, 'FR' ) !== false ) {
					if ( 'fr' === $template_lang ) :
						?>
						<li class="wplegal-template-fr"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=fr&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php else : ?>
						<li style="display:none;" class="wplegal-template-fr"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=fr&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php endif; ?>
				<?php } elseif ( strpos( $ras->title, 'DE' ) !== false ) { ?>
					<?php if ( 'de' === $template_lang ) : ?>
						<li class="wplegal-template-de"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=de&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php else : ?>
						<li style="display:none;" class="wplegal-template-de"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=de&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php endif; ?>
				<?php } else { ?>
					<?php if ( 'eng' === $template_lang ) : ?>
						<li class="wplegal-template-eng "><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink " href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=eng&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?>
							<?php
							if ( array_key_exists( $ras->contentfor, $tooltip_texts ) ) {
								?>
								<span class="wplegal-tooltip"><span class="dashicons dashicons-info"></span>
									<span class="wplegal-tooltiptext"><?php echo esc_attr( $tooltip_texts[ $ras->contentfor ] ); ?></span>
								</span>
							</a></span></li>
								<?php
							}
							?>
					<?php else : ?>
						<li style="display:none;" class="wplegal-template-eng"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=eng&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php endif; ?>
					<?php
				}
			} else {
				if ( strpos( $ras->title, 'FR' ) !== false ) {
					if ( 'fr' === $template_lang ) :
						?>
						<li class="wplegal-template-fr"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=fr&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php else : ?>
						<li style="display:none;" class="wplegal-template-fr"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=fr&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php endif; ?>
				<?php } elseif ( strpos( $ras->title, 'DE' ) !== false ) { ?>
					<?php if ( 'de' === $template_lang ) : ?>
						<li class="wplegal-template-de"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=de&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php else : ?>
						<li style="display:none;" class="wplegal-template-de"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=de&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php endif; ?>
				<?php } else { ?>
					<?php if ( 'eng' === $template_lang ) : ?>
						<li class="wplegal-template-eng "><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink " href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=eng&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?></a></span></li>
					<?php else : ?>
						<li style="display:none;" class="wplegal-template-eng"><span id="legalpages<?php echo esc_attr( $ras->id ); ?>"><a class="myLink" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_attr( $current_page ); ?>&lp-template-lang=eng&lp-type=<?php echo esc_attr( $lptype ); ?>&lp-template=<?php echo esc_attr( $ras->id ); ?>"><?php echo esc_attr( $ras->title ); ?> &raquo;</a></span></li>
					<?php endif; ?>
					<?php
				}
			}
		}
		?>

		</ul>
	</div>
		<?php if ( '1' !== $lp_pro_active ) : ?>
		<div id="lp_generalid_right">
			<a href="https://club.wpeka.com/product/wplegalpages/?utm_source=plugin&utm_campaign=wplegalpages&utm_content=upgrade-to-pro-for-all-templates" style="text-decoration:none;padding-left:20px;" target="_blank">
			<?php esc_attr_e( 'Upgrade to Pro for All templates', 'wplegalpages' ); ?>
			</a>
		</div>

		<div id="lp_generalid_right" class="postbox ">
			<h3 class="hndle"  style="padding:0px 10px 12px 10px; font-size:20px;"> <?php esc_attr_e( 'WPLegalPages Pro Templates', 'wplegalpages' ); ?> </h3><br/>
			<ul>
				<li>Terms of use <strong>(forced agreement - don't allow your users to proceed without agreeing to your terms)</strong></li>
				<li>Linking policy template</li>
				<li>About Us</li>
				<li>External links policy template</li>
				<li>Refund policy template</li>
				<li>Affiliate disclosure template</li>
				<li>Cookies Policy</li>
				<li>Privacy Policy template</li>
				<li>Affiliate agreement template</li>
				<li>FB privacy policy template</li>
				<li>Earnings Disclaimer template</li>
				<li>Antispam template</li>
				<li>Double dart cookie template</li>
				<li>Disclaimer template (English, French, German)</li>
				<li>FTC statement template</li>
				<li>Medical disclaimer template</li>
				<li>Testimonials disclosure template</li>
				<li>Amazon affiliate template</li>
				<li>DMCA policy</li>
				<li>California Privacy Rights</li>
				<li>Blog Comment Policy</li>
				<li>Children's Online Privacy Protection Act</li>
				<li>Digital Products Refund Policy</li>
				<li>Newsletter Subscription and Disclaimer template</li>
				<li>Confidentiality Discloser template</li>
				<li>Return Refund Policy template</li>
				<li>GDPR Cookie Policy template</li>
				<li>GDPR Privacy Policy template (English, French, German)</li>
			</ul>
		</div>
	<?php endif; ?>
</div>
<?php } else { ?>
		<div id="message" class="updated">
			<p><?php esc_attr_e( 'You are exceeding the limit of creating 15 legal pages.', 'wplegalpages' ); ?></p>
		</div>
<?php } ?>
</div>
