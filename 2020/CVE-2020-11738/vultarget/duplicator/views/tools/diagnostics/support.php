<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<style>
    div.dup-support-all {font-size:13px; line-height:20px}
    div.dup-support-txts-links {width:100%;font-size:14px; font-weight:bold; line-height:26px; text-align:center}
    div.dup-support-hlp-area {width:375px; height:160px; float:left; border:1px solid #dfdfdf; border-radius:4px; margin:10px; line-height:18px;box-shadow: 0 8px 6px -6px #ccc;}
    table.dup-support-hlp-hdrs {border-collapse:collapse; width:100%; border-bottom:1px solid #dfdfdf}
    table.dup-support-hlp-hdrs {background-color:#efefef;}
    div.dup-support-hlp-hdrs {
        font-weight:bold; font-size:17px; height: 35px; padding:5px 5px 5px 10px;
        background-image:-ms-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
        background-image:-moz-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
        background-image:-o-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
        background-image:-webkit-gradient(linear, left top, left bottom, color-stop(0, #FFFFFF), color-stop(1, #DEDEDE));
        background-image:-webkit-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
        background-image:linear-gradient(to bottom, #FFFFFF 0%, #DEDEDE 100%);
    }
    div.dup-support-hlp-hdrs div {padding:5px; margin:4px 20px 0px -20px;  text-align: center;}
    div.dup-support-hlp-txt{padding:10px 4px 4px 4px; text-align:center}
</style>


<div class="wrap dup-wrap dup-support-all">

    <div style="width:800px; margin:auto; margin-top: 20px">
        <table>
            <tr>
                <td style="width:70px"><i class="fa fa-question-circle fa-5x"></i></td>
                <td valign="top" style="padding-top:10px; font-size:13px">
					<?php
					esc_html_e("Migrating WordPress is a complex process and the logic to make all the magic happen smoothly may not work quickly with every site.  With over 30,000 plugins and a very complex server eco-system some migrations may run into issues.  This is why the Duplicator includes a detailed knowledgebase that can help with many common issues.  Resources to additional support, approved hosting, and alternatives to fit your needs can be found below.",
						'duplicator');
					?>
                </td>
            </tr>
        </table>
        <br/><br/>

        <!-- HELP LINKS -->
        <div class="dup-support-hlp-area">
            <div class="dup-support-hlp-hdrs">
                <i class="fas fa-cube fa-2x fa-pull-left"></i>
                <div><?php esc_html_e('Knowledgebase', 'duplicator') ?></div>
            </div>
            <div class="dup-support-hlp-txt">
<?php esc_html_e('Complete Online Documentation', 'duplicator'); ?><br/>
                <select id="dup-support-kb-lnks" style="margin-top:18px; font-size:16px; min-width: 170px">
                    <option> <?php esc_html_e('Choose A Section', 'duplicator') ?> </option>
                    <option value="https://snapcreek.com/duplicator/docs/quick-start/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_qs"><?php esc_html_e('Quick Start',
	'duplicator') ?></option>
                    <option value="https://snapcreek.com/duplicator/docs/guide/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_guide"><?php esc_html_e('User Guide',
	'duplicator') ?></option>
                    <option value="https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_FAQs"><?php esc_html_e('FAQs',
	'duplicator') ?></option>
                    <option value="https://snapcreek.com/duplicator/docs/changelog/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_changelog&lite"><?php esc_html_e('Change Log',
	'duplicator') ?></option>
                </select>
            </div>
        </div>

        <!-- ONLINE SUPPORT -->
        <div class="dup-support-hlp-area">
            <div class="dup-support-hlp-hdrs">
                <i class="far fa-lightbulb fa-2x fa-pull-left"></i>
                <div><?php esc_html_e('Online Support', 'duplicator') ?></div>
            </div>
            <div class="dup-support-hlp-txt">
<?php esc_html_e("Get Help From IT Professionals", 'duplicator'); ?>
                <br/>
                <div class="dup-support-txts-links" style="margin:10px 0 10px 0">
                    <button class="button  button-primary button-large" onclick="Duplicator.OpenSupportWindow();return false;">
<?php esc_html_e('Get Support!', 'duplicator') ?>
					</button> <br/>
                </div>
				<small>Pro Users <a href="https://snapcreek.com/ticket?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_prousers_here" target="_blank">Support Here</a></small>
            </div>
        </div>
        <br style="clear:both" /><br/><br/>


        <!-- APPROVED HOSTING -->
        <div class="dup-support-hlp-area">

            <div class="dup-support-hlp-hdrs">
                <i class="fa fa-bolt fa-sm fa-2x fa-pull-left"></i>
                <div><?php esc_html_e('Approved Hosting', 'duplicator') ?></div>
            </div>
            <div class="dup-support-hlp-txt">
<?php esc_html_e('Servers That Work With Duplicator', 'duplicator'); ?>
                <br/><br/>
                <div class="dup-support-txts-links">
                    <button class="button button-primary button-large" onclick="window.open('https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_servers#faq-resource-040-q', 'litg');"><?php esc_html_e('Trusted Providers!',
	'duplicator') ?></button> &nbsp;
                </div>
            </div>
        </div>

        <!-- ALTERNATIVES -->
        <div class="dup-support-hlp-area">

            <div class="dup-support-hlp-hdrs">
                <i class="fas fa-code-branch fa-2x fa-pull-left"></i>
                <div><?php esc_html_e('Alternatives', 'duplicator') ?></div>
            </div>
            <div class="dup-support-hlp-txt">
<?php esc_html_e('Other Commercial Resources', 'duplicator'); ?>
                <br/><br/>
                <div class="dup-support-txts-links">
                    <button class="button button-primary button-large" onclick="window.open('https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_pro_sln#faq-resource-050-q', 'litg');"><?php esc_html_e('Pro Solutions!',
	'duplicator') ?></button> &nbsp;
                </div>
            </div>
        </div>
    </div>
</div><br/><br/><br/><br/>

<script>
	jQuery(document).ready(function ($) {

		Duplicator.OpenSupportWindow = function () {
			var url = 'https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=support_window#faq-resource';
			window.open(url, 'litg');
		}

		//ATTACHED EVENTS
		jQuery('#dup-support-kb-lnks').change(function () {
			if (jQuery(this).val() != "null")
				window.open(jQuery(this).val())
		});

	});
</script>