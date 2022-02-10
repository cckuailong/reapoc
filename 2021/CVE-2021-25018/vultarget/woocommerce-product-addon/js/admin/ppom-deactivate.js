/**
 * Getting user response when deactivate plugin
 * */
"use strict"
jQuery(function($){
    
    var modal = $('#ppom-deactivate-modal');
                            var deactivateLink = '';


                            $('#the-list').on('click', 'a.ppom-deactivate-link', function (e) {
                                e.preventDefault();
                                modal.addClass('modal-active');
                                deactivateLink = $(this).attr('href');
                                modal.find('a.dont-bother-me').attr('href', deactivateLink).css('float', 'left');
                            });

                            $('#ppom-deactivate-modal').on('click', 'a.review-and-deactivate', function (e) {
                                e.preventDefault();
                                window.open("https://wordpress.org/support/plugin/woocommerce-product-addon/reviews/#new-post");
                                window.location.href = deactivateLink;
                            });
                            modal.on('click', 'button.pipe-model-cancel', function (e) {
                                e.preventDefault();
                                modal.removeClass('modal-active');
                            });
                            modal.on('click', 'input[type="radio"]', function () {
                                var parent = $(this).parents('li:first');
                                modal.find('.reason-input').remove();
                                var inputType = parent.data('type'),
                                        inputPlaceholder = parent.data('placeholder');
                                if ('reviewhtml' === inputType) {
                                    var reasonInputHtml = '<div class="reviewlink"><a href="#" target="_blank" class="review-and-deactivate">Deactivate and leave a review<span class="xa-pipe-rating-link"> &#9733;&#9733;&#9733;&#9733;&#9733; </span></a></div>';
                                } else {
                                    var reasonInputHtml = '<div class="reason-input">' + (('text' === inputType) ? '<input type="text" class="input-text" size="40" />' : '<textarea rows="5" cols="45"></textarea>') + '</div>';
                                }
                                if (inputType !== '') {
                                    parent.append($(reasonInputHtml));
                                    parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
                                }
                            });

                            modal.on('click', 'button.pipe-model-submit', function (e) {
                                e.preventDefault();
                                var button = $(this);
                                if (button.hasClass('disabled')) {
                                    return;
                                }
                                var $radio = $('input[type="radio"]:checked', modal);
                                var $selected_reason = $radio.parents('li:first'),
                                        $input = $selected_reason.find('textarea, input[type="text"]');

                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    data: {
                                        action: 'pipe_submit_uninstall_reason',
                                        reason_id: (0 === $radio.length) ? 'none' : $radio.val(),
                                        reason_info: (0 !== $input.length) ? $input.val().trim() : ''
                                    },
                                    beforeSend: function () {
                                        button.addClass('disabled');
                                        button.text('Processing...');
                                    },
                                    complete: function (resp) {
                                        window.location.href = deactivateLink;
                                    }
                                });
                            });
});