
(function addLangTabsToEditor() {
    const tabs = document.querySelector('.wp-editor-tabs');

    // if we have languages arrray
    if (tabs && translation.langs.length > 1) {
        // add editor button tabs with each language
        translation.langs.reverse().forEach((lang, i) => {
            let active = i === translation.langs.length - 1 ? ' active' : '';
            const button = `<button type="button" data-lang="${lang}" class="cmp-switch-editor-lang${active}">${lang.toUpperCase()}</button>`;
            tabs.insertAdjacentHTML('afterbegin', button);
        });

        const lang_buttons = document.querySelectorAll('.cmp-switch-editor-lang');
        const editorId = document.getElementById('niteoCS_body');
        let currentLang = translation.default;

        // set content of editor from language inputs 
        for (const lang_button of lang_buttons) {
            lang_button.addEventListener('click', function (event) {
                for (var item of lang_buttons) {
                    item.classList.remove('active');
                }
                event.target.classList.add('active');
                const newLang = event.target.dataset.lang;
                const newLangInput = document.querySelector(`#niteoCS_body_${newLang}`);
                if (document.getElementById('wp-niteoCS_body-wrap').classList.contains('tmce-active')) {
                    tinyMCE.activeEditor.setContent(newLangInput.value);
                } else {
                    editorId.value = newLangInput.value;
                }
                currentLang = newLang;
            })
        }

        // set language inputs from rich editor
        jQuery(document).on('tinymce-editor-setup', function (event, editor) {
            editor.on('focusout', function (e) {
                document.querySelector(`#niteoCS_body_${currentLang}`).value = editor.getContent();
            });
        });

        // set language inputs from text editor
        editorId.addEventListener('focusout', function (e) {
            document.querySelector(`#niteoCS_body_${currentLang}`).value = e.target.value;
        })

    }

})();

