<style>
    .mystickyelements--hidden {
        overflow: hidden;
    }

    .mystickyelements--popup-overlay .mystickyelements--internal-message {
        margin: 3px 0 3px 22px;
        display: none;
    }

    .mystickyelements--reason-input {
        margin: 3px 0 3px 22px;
        display: none;
    }

    .mystickyelements--reason-input input[type="text"] {
        width: 100%;
        display: block;
    }

    .mystickyelements--popup-overlay {
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

    .mystickyelements--popup-overlay.mystickyelements--active {
        opacity: 1;
        visibility: visible;
    }

    .mystickyelements--serveypanel {
        width: 600px;
        background: #fff;
        margin: 65px auto 0;
    }

    .mystickyelements--popup-header {
        background: #f1f1f1;
        padding: 20px;
        border-bottom: 1px solid #ccc;
    }

    .mystickyelements--popup-header h2 {
        margin: 0;
    }

    .mystickyelements--popup-body {
        padding: 10px 20px;
    }

    .mystickyelements--popup-footer {
        background: #f9f3f3;
        padding: 10px 20px;
        border-top: 1px solid #ccc;
    }

    .mystickyelements--popup-footer:after {
        content: "";
        display: table;
        clear: both;
    }

    .action-btns {
        float: right;
    }

    .mystickyelements--anonymous {
        display: none;
    }

    .attention, .error-message {
        color: red;
        font-weight: 600;
        display: none;
    }

    .mystickyelements--spinner {
        display: none;
    }

    .mystickyelements--spinner img {
        margin-top: 3px;
    }

    .mystickyelements--hidden-input {
        padding: 10px 0 0;
        display: none;
    }
    .mystickyelements--popup-body textarea {
        padding: 10px;
        width: 100%;
        height: 100px;
        margin: 0 0 10px 0;
    }

    span.mystickyelements--error-message {
        color: #dd0000;
        font-weight: 600;
    }
    .mystickyelements--popup-body h3 {
        line-height: 24px;
    }
    .mystickyelements--popup-overlay .form-control input {
        width: 100%;
        margin: 0 0 15px 0;
    }
</style>

<div class="mystickyelements--popup-overlay">
    <div class="mystickyelements--serveypanel">
        <form action="#" method="post" id="mystickyelements--deactivate-form">
            <div class="mystickyelements--popup-header">
                <h2><?php _e('Quick feedback about My Sticky Elements', "mystickyelements"); ?> 🙏</h2>
            </div>
            <div class="mystickyelements--popup-body">
                <h3><?php _e('Your feedback will help us improve the product, please tell us why did you decide to deactivate My Sticky Elements :)', "mystickyelements"); ?></h3>
                <div class="form-control">
                    <input type="email" value="<?php echo get_option( 'admin_email' ) ?>" placeholder="<?php echo _e("Email address", "mystickyelements") ?>" id="mystickyelements-deactivation-email_id">
                </div>
                <div class="form-control">                    
                    <textarea placeholder="<?php echo _e("Your comment", "mystickyelements") ?>" id="mystickyelements-deactivation-comment"></textarea>
                </div>
            </div>
            <div class="mystickyelements--popup-footer">
                <label class="mystickyelements--anonymous">
                    <input type="checkbox"/><?php _e('Anonymous feedback', "mystickyelements"); ?>
                </label>
                <input type="button" class="button button-secondary button-skip mystickyelements--popup-skip-feedback" value="Skip &amp; Deactivate">
                <div class="action-btns">
                    <span class="mystickyelements--spinner"><img src="<?php echo admin_url('/images/spinner.gif'); ?>" alt=""></span>
                    <input type="submit" class="button button-secondary button-deactivate mystickyelements--popup-allow-deactivate" value="Submit &amp; Deactivate" disabled="disabled">
                    <a href="#" class="button button-primary mystickyelements--popup-button-close"><?php _e('Cancel', "mystickyelements"); ?></a>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    (function ($) {

        $(function () {

            var pluginSlug = 'mystickyelements';
            // Code to fire when the DOM is ready.

            $(document).on('click', 'tr[data-slug="' + pluginSlug + '"] .deactivate', function (e) {
                e.preventDefault();

                $('.mystickyelements--popup-overlay').addClass('mystickyelements--active');
                $('body').addClass('mystickyelements--hidden');
            });
            $(document).on('click', '.mystickyelements--popup-button-close', function () {
                close_popup();
            });
            $(document).on('click', ".mystickyelements--serveypanel,tr[data-slug='" + pluginSlug + "'] .deactivate", function (e) {
                e.stopPropagation();
            });

            $(document).on( 'click', function () {
                close_popup();
            });
            $('.mystickyelements--reason label').on('click', function () {
                $(".mystickyelements--hidden-input").hide();
                jQuery(".mystickyelements--error-message").remove();
                if ($(this).find('input[type="radio"]').is(':checked')) {
                    $(this).closest("li").find('.mystickyelements--hidden-input').show();
                }
            });
            $(document).on("keyup", "#mystickyelements-deactivation-comment", function(){
                if($.trim($(this).val()) == "") {
                    $(".mystickyelements--popup-allow-deactivate").attr("disabled", true);
                } else {
                    $(".mystickyelements--popup-allow-deactivate").attr("disabled", false);
                }
            });
            $('input[type="radio"][name="mystickyelements--selected-reason"]').on('click', function (event) {
                $(".mystickyelements--popup-allow-deactivate").removeAttr('disabled');
            });
            $(document).on('submit', '#mystickyelements--deactivate-form', function (event) {
                event.preventDefault();
                _reason = "";
                if(jQuery.trim(jQuery("#mystickyelements-deactivation-comment").val()) == "") {
                    jQuery("#alt_plugin").after("<span class='mystickyelements--error-message'>Please provide your feedback</span>");
                    return false;
                } else {
                    _reason = jQuery.trim(jQuery("#mystickyelements-deactivation-comment").val());
                }

                jQuery('[name="mystickyelements--selected-reason"]:checked').val();

                var email_id = jQuery.trim(jQuery("#mystickyelements-deactivation-email_id").val());

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'mystickyelements_plugin_deactivate',
                        reason: _reason,
                        email_id: email_id,
                        nonce: '<?php echo wp_create_nonce("mystickyelements_deactivate_nonce") ?>'
                    },
                    beforeSend: function () {
                        $(".mystickyelements--spinner").show();
                        $(".mystickyelements--popup-allow-deactivate").attr("disabled", "disabled");
                    }
                }).done(function (status) {
                    $(".mystickyelements--spinner").hide();
                    $(".mystickyelements--popup-allow-deactivate").removeAttr("disabled");
                    window.location.href = $("tr[data-slug='" + pluginSlug + "'] .deactivate a").attr('href');
                });
            });

            $('.mystickyelements--popup-skip-feedback').on('click', function (e) {
                window.location.href = $("tr[data-slug='" + pluginSlug + "'] .deactivate a").attr('href');
            })

            function close_popup() {
                $('.mystickyelements--popup-overlay').removeClass('mystickyelements--active');
                $('#mystickyelements--deactivate-form').trigger("reset");
                $(".mystickyelements--popup-allow-deactivate").attr('disabled', 'disabled');
                $(".mystickyelements--reason-input").hide();
                $('body').removeClass('mystickyelements--hidden');
                $('.message.error-message').hide();
            }
        });

    })(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
</script>
