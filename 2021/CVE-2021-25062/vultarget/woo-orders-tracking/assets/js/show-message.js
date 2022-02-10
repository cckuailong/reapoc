'use strict';
jQuery(document).ready(function ($) {
    $('body').append('<div class="villatheme-admin-show-message-message-container"></div>');
});

/**
 *
 * @param message
 * @param status
 * @param message_content
 * @param is_product_title
 * @param time_out
 * @returns {*}
 */
function villatheme_admin_show_message(message, status = '', message_content = '', is_product_title = false, time_out = 0) {
    var $message_field = jQuery('.villatheme-admin-show-message-message-container');
    if (message_content) {
        message_content = '<div class="villatheme-admin-show-message-message-content">' + message_content + '</div>';
    }
    var titleClass = 'villatheme-admin-show-message-message-title';
    if (is_product_title) {
        titleClass += ' villatheme-admin-show-message-message-product-title'
    }
    var $new_message = jQuery('<div class="villatheme-admin-show-message-message-item villatheme-admin-show-message-message-new-added-item"><div class="' + titleClass + '">' + message + '</div><span class="villatheme-admin-show-message-message-item-close dashicons dashicons-no-alt"></span>' + message_content + '</div>');
    $message_field.prepend($new_message);
    $new_message.addClass('villatheme-admin-show-message-message-' + status);
    var timeOut = villatheme_admin_show_message_timeout($new_message, time_out);
    if (timeOut.length > 0) {
        $new_message.on('mouseenter', function () {
            for (var i in timeOut) {
                clearTimeout(timeOut[i]);
            }
        });
        $new_message.on('mouseleave', function () {
            for (var i in timeOut) {
                clearTimeout(timeOut[i]);
            }
            timeOut = villatheme_admin_show_message_timeout($new_message, time_out);
        });
    }
    $new_message.find('.villatheme-admin-show-message-message-item-close').on('click', function () {
        if (timeOut.length > 0) {
            for (var i in timeOut) {
                clearTimeout(timeOut[i]);
            }
        }
        $new_message.addClass('villatheme-admin-show-message-message-new-added-item');
        setTimeout(function () {
            $new_message.remove();
        }, 300);
    });
    return $new_message;
}

function villatheme_admin_show_message_timeout($new_message, time_out) {
    var timeOut = [];
    setTimeout(function () {
        $new_message.removeClass('villatheme-admin-show-message-message-new-added-item');
        if (time_out > 0) {
            timeOut.push(setTimeout(function () {
                $new_message.addClass('villatheme-admin-show-message-message-new-added-item');
            }, time_out));
            timeOut.push(setTimeout(function () {
                $new_message.remove();
            }, (time_out + 300)));
        }
    }, 10);
    return timeOut;
}