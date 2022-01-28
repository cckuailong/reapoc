import $ from 'jquery';

import MainView from './views/main-layout';
import MetaboxView from './views/metabox';
import SidebarView from './views/sidebar-layout';
import FormSettings from "./collections/form-settings";
import Util from './util';

let App = {

    titleShit() {
        $('#title').each(function () {
            let input = $(this), prompt = $('#' + this.id + '-prompt-text');

            if ('' === this.value) {
                prompt.removeClass('screen-reader-text');
            }

            prompt.click(function () {
                $(this).addClass('screen-reader-text');
                input.focus();
            });

            input.blur(function () {
                if ('' === this.value) {
                    prompt.removeClass('screen-reader-text');
                }
            });

            input.focus(function () {
                prompt.addClass('screen-reader-text');
            });
        });
    },

    metabox_handle_shit() {
        // handle metabox toggling.
        $(document).on('click', '.pp-metabox-handle', function (e) {

            e.preventDefault();

            let _this = $(this),
                cache = $('.pp-metabox-handle'),
                parent = $(this).parents('.pp-postbox-wrap');

            // cleanup.
            cache.parents('.pp-postbox-wrap').find('.postbox-header').removeClass('postbox').removeClass('closed');
            // close all boxes except for the one being toggled
            cache.not(_this).parents('.pp-postbox-wrap').addClass('closed');

            if (parent.hasClass('closed')) {
                parent.removeClass('closed');
            } else {
                parent.addClass('closed');
            }
        });

        $('#pp-form-builder-standard-fields .pp-metabox-handle').click();
    },

    init() {

        let formSettings = new FormSettings(pp_form_builder_fields_settings);
        window.ppFormSettings = formSettings;

        formSettings.comparator = 'sortID';

        const mainLayout = new MainView({
            collection: formSettings
        });

        mainLayout.render();

        const sidebarLayout = new SidebarView({
            collection: formSettings
        });

        sidebarLayout.render();

        $(document).on('click', '.pp-form-save-changes', function (e) {
            e.preventDefault();
            Util.save_changes(window.ppFormSettings);
        });

        new MetaboxView();
    }
};

$(function () {

    let cache = $('.pp-dnd-form-builder-wrap');

    if (cache.length === 0) return;

    App.init();
    App.titleShit();
    App.metabox_handle_shit();

    setTimeout(function () {
        cache.css('opacity', '1');
    }, 500);
});