<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
    .chaty-hidden {
        overflow: hidden;
    }
    .chaty-popup-overlay .chaty-internal-message {
        margin: 3px 0 3px 22px;
        display: none;
    }
    .chaty-reason-input {
        margin: 3px 0 3px 22px;
        display: none;
    }
    .chaty-reason-input input[type="text"] {
        width: 100%;
        display: block;
    }
    .chaty-popup-overlay {
        background: rgba(0, 0, 0, .8);
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 1000;
        overflow: auto;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease-in-out :
    }
    .chaty-popup-overlay.chaty-active {
        opacity: 1;
        visibility: visible;
    }
    .chaty-serveypanel {
        width: 600px;
        background: #fff;
        margin: 65px auto 0;
    }
    .chaty-popup-header {
        background: #f1f1f1;
        padding: 20px;
        border-bottom: 1px solid #ccc;
    }
    .chaty-popup-header h2 {
        margin: 0;
    }
    .chaty-popup-body {
        padding: 10px 20px;
    }
    .chaty-popup-footer {
        background: #f9f3f3;
        padding: 10px 20px;
        border-top: 1px solid #ccc;
    }
    .chaty-popup-footer:after {
        content: "";
        display: table;
        clear: both;
    }
    .action-btns {
        float: right;
    }
    .chaty-anonymous {
        display: none;
    }
    .attention, .error-message {
        color: red;
        font-weight: 600;
        display: none;
    }
    .chaty-spinner {
        display: none;
    }
    .chaty-spinner img {
        margin-top: 3px;
    }
    .chaty-hidden-input {
        padding: 10px 0 0;
        display: none;
    }
    .chaty-hidden-input input[type='text'] {
        padding: 0 10px;
        width: 100%;
        height: 26px;
        line-height: 26px;
    }
    .chaty--popup-overlay textarea {
        padding: 10px;
        width: 100%;
        height: 100px;
        margin: 0 0 15px 0;
    }
    span.chaty-error-message {
        color: #dd0000;
        font-weight: 600;
    }
    .chaty-popup-body h3 {
        line-height: 24px;
    }
    .chaty-popup-body textarea {
        width: 100%;
        height: 80px;
    }
    .chaty--popup-overlay .form-control input {
        width: 100%;
        margin: 0 0 15px 0;
    }
    .chaty-serveypanel .form-control input {
        width: 100%;
        margin: 0 0 15px 0;
    }
</style>
<!-- modal for plugin deactivation popup -->
<div class="chaty-popup-overlay">
    <div class="chaty-serveypanel">
        <!-- form start -->
        <form action="#" method="post" id="chaty-deactivate-form">
            <div class="chaty-popup-header">
                <h2><?php esc_attr_e('Quick feedback about Chaty', CHT_OPT); ?> 🙏</h2>
            </div>
            <div class="chaty-popup-body">
                <h3><?php esc_attr_e('Your feedback will help us improve the product, please tell us why did you decide to deactivate Chaty :)', CHT_OPT); ?></h3>
                <div class="form-control">
                    <input type="email" value="<?php echo get_option( 'admin_email' ) ?>" placeholder="<?php echo _e("Email address", CHT_OPT) ?>" id="chaty-deactivation-email_id">
                </div>
                <div class="form-control">
                    <label></label>
                    <textarea placeholder="<?php esc_attr_e("Your comment", CHT_OPT) ?>" id="chaty-deactivation-comment"></textarea>
                </div>
            </div>
            <div class="chaty-popup-footer">
                <label class="chaty-anonymous">
                    <input type="checkbox"/><?php esc_attr_e('Anonymous feedback', CHT_OPT); ?>
                </label>
                <input type="button" class="button button-secondary button-skip chaty-popup-skip-feedback" value="Skip &amp; Deactivate">
                <div class="action-btns">
                    <span class="chaty-spinner"><img src="<?php echo esc_url(admin_url('/images/spinner.gif')); ?>" alt=""></span>
                    <input type="submit" class="button button-secondary button-deactivate chaty-popup-allow-deactivate" value="Submit &amp; Deactivate" disabled="disabled">
                    <a href="#" class="button button-primary chaty-popup-button-close"><?php esc_attr_e('Cancel', CHT_OPT); ?></a>
                </div>
            </div>
        </form>
        <!-- form end -->
    </div>
</div>
<script>
    (function ($) {
        $(function () {
            var chatyPluginSlug = 'chaty';
            // Code to fire when the DOM is ready.
            $(document).on('click', 'tr[data-slug="' + chatyPluginSlug + '"] .deactivate', function (e) {
                e.preventDefault();
                $('.chaty-popup-overlay').addClass('chaty-active');
                $('body').addClass('chaty-hidden');
            });
            $(document).on('click', '.chaty-popup-button-close', function () {
                close_popup();
            });
            $(document).on('click', ".chaty-serveypanel,tr[data-slug='" + chatyPluginSlug + "'] .deactivate", function (e) {
                e.stopPropagation();
            });
            $(document).click(function () {
                close_popup();
            });
            $('.chaty-reason label').on('click', function () {
                $(".chaty-hidden-input").hide();
                jQuery(".chaty-error-message").remove();
                if ($(this).find('input[type="radio"]').is(':checked')) {
                    $(this).closest("li").find('.chaty-hidden-input').show();
                }
            });
            $(document).on("keyup", "#chaty-deactivation-comment", function(){
                if($.trim($(this).val()) == "") {
                    $(".chaty-popup-allow-deactivate").attr("disabled", true);
                } else {
                    $(".chaty-popup-allow-deactivate").attr("disabled", false);
                }
            });
            $('input[type="radio"][name="chaty-selected-reason"]').on('click', function (event) {
                $(".chaty-popup-allow-deactivate").removeAttr('disabled');
            });
            $(document).on('submit', '#chaty-deactivate-form', function (event) {
                event.preventDefault();
                _reason = "";
                if(jQuery.trim(jQuery("#chaty-deactivation-comment").val()) == "") {
                    jQuery("#alt_plugin").after("<span class='chaty-error-message'>Please provide your feedback</span>");
                    return false;
                } else {
                    _reason = jQuery.trim(jQuery("#chaty-deactivation-comment").val());
                }
                jQuery('[name="chaty-selected-reason"]:checked').val();
                var email_id = jQuery.trim(jQuery("#chaty-deactivation-email_id").val());
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'chaty_plugin_deactivate',
                        reason: _reason,
                        email_id: email_id,
                        nonce: '<?php esc_attr_e(wp_create_nonce("chaty_deactivate_nonce")) ?>'
                    },
                    beforeSend: function () {
                        $(".chaty-spinner").show();
                        $(".chaty-popup-allow-deactivate").attr("disabled", "disabled");
                    }
                }).done(function () {
                    $(".chaty-spinner").hide();
                    $(".chaty-popup-allow-deactivate").removeAttr("disabled");
                    window.location.href = $("tr[data-slug='" + chatyPluginSlug + "'] .deactivate a").attr('href');
                });
            });
            $('.chaty-popup-skip-feedback').on('click', function (e) {
                window.location.href = $("tr[data-slug='" + chatyPluginSlug + "'] .deactivate a").attr('href');
            });
            function close_popup() {
                $('.chaty-popup-overlay').removeClass('chaty-active');
                $('#chaty-deactivate-form').trigger("reset");
                $(".chaty-popup-allow-deactivate").attr('disabled', 'disabled');
                $(".chaty-reason-input").hide();
                $('body').removeClass('chaty-hidden');
                $('.message.error-message').hide();
            }
        });
    })(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
</script>
