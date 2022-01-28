(function($) {
    var id;
    wp.media.wpUserAvatar = {

        get: function() {
            return wp.media.view.settings.post.wpUserAvatarId
        },
        set: function(a) {
            var b = wp.media.view.settings;
            b.post.wpUserAvatarId = a;
            b.post.wpUserAvatarSrc = $('div.attachment-info').find('img').attr('src');
            if (b.post.wpUserAvatarId && b.post.wpUserAvatarSrc) {
                $('#wp-user-avatar-is-dirty').val('true');
                $('#wp-user-avatar'+id).val(b.post.wpUserAvatarId);
                $('#wpua-images'+id+', #wpua-undo-button'+id).show();
                $('#wpua-preview'+id).find('img').attr('src', b.post.wpUserAvatarSrc).removeAttr('height', "");
                $('#wpua-remove-button'+id+', #wpua-thumbnail'+id).hide();
                $('#wp_user_avatar_radio').trigger('click')
            }
            wp.media.wpUserAvatar.frame().close()
        },
        frame: function() {
            if (this._frame) {
                return this._frame
            }
            this._frame = wp.media({
                library: {
                    type: 'image'
                },
                multiple: false,
                title: $('#wpua-add'+id).data('title')
            });
            this._frame.on('open', function() {
                var a = $('#wp-user-avatar'+id).val();
                if (a == "") {
                    $('div.media-router').find('a:first').trigger('click')
                } else {
                    var b = this.state().get('selection');
                    attachment = wp.media.attachment(a);
                    attachment.fetch();
                    b.add(attachment ? [attachment] : [])
                }
            }, this._frame);
            this._frame.state('library').on('select', this.select);
            return this._frame
        },
        select: function(a) {
            selection = this.get('selection').single();
            wp.media.wpUserAvatar.set(selection ? selection.id : -1)
        },
        init: function() {
            $('body').on('click', '#wpua-add', function(e) {
                e.preventDefault();
                e.stopPropagation();
                id='';
                wp.media.wpUserAvatar.frame().open()
            })
            $('body').on('click', '#wpua-add-existing', function(e) {
                e.preventDefault();
                e.stopPropagation();
                id='-existing';
                wp.media.wpUserAvatar.frame().open()
            })
        }
    }
})(jQuery);
jQuery(function($) {
    if (typeof(wp) != 'undefined') {
        wp.media.wpUserAvatar.init()
    }
    $('#your-profile').attr('enctype', 'multipart/form-data');
    var a = $('#wp-user-avatar').val();
    var b = $('#wpua-preview').find('img').attr('src');
    $('body').on('click', '#wpua-remove', function(e) {
        e.preventDefault();
        $('#wpua-original').remove();
        $('#wpua-remove-button, #wpua-thumbnail').hide();
        $('#wpua-preview').find('img:first').hide();
        $('#wpua-preview').prepend('<img id="wpua-original" />');
        $('#wpua-original').attr('src', wpua_custom.avatar_thumb);
        $('#wp-user-avatar').val("");
        $('#wpua-original, #wpua-undo-button').show();
        $('#wp_user_avatar_radio').trigger('click')
    });
    $('body').on('click', '#wpua-undo', function(e) {
        e.preventDefault();
        $('#wpua-original').remove();
        $('#wpua-images').removeAttr('style');
        $('#wpua-undo-button').hide();
        $('#wpua-remove-button, #wpua-thumbnail').show();
        $('#wpua-preview').find('img:first').attr('src', b).show();
        $('#wp-user-avatar').val(a);
        $('#wp_user_avatar_radio').trigger('click')
    })
});
jQuery(function($) {
    if (typeof(wp) != 'undefined') {
        wp.media.wpUserAvatar.init()
    }
    $('#your-profile').attr('enctype', 'multipart/form-data');
    var a = $('#wp-user-avatar-existing').val();
    var b = $('#wpua-preview-existing').find('img').attr('src');
    $('#wpua-undo-button-existing').hide();
    $('body').on('click', '#wpua-remove-existing', function(e) {
        e.preventDefault();
        $('#wpua-original-existing').remove();
        $('#wpua-remove-button-existing, #wpua-thumbnail-existing').hide();
        $('#wpua-preview-existing').find('img:first').hide();
        $('#wpua-preview-existing').prepend('<img id="wpua-original-existing" />');
        $('#wpua-original-existing').attr('src', wpua_custom.avatar_thumb);
        $('#wp-user-avatar-existing').val("");
        $('#wp-user-avatar-is-dirty').val('true');
        $('#wpua-original-existing, #wpua-undo-button-existing').show();
        $('#wp_user_avatar_radio').trigger('click')
    });
    $('body').on('click', '#wpua-undo-existing', function(e) {
        e.preventDefault();
        $('#wpua-original-existing').remove();
        $('#wpua-images-existing').removeAttr('style');
        $('#wpua-undo-button-existing').hide();
        $('#wpua-remove-button-existing, #wpua-thumbnail-existing').show();
        $('#wpua-preview-existing').find('img:first').attr('src', b).show();
        $('#wp-user-avatar-existing').val(a);
        $('#wp_user_avatar_radio').trigger('click')
    })
});