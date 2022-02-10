<div class="wpuf-text-editor">

    <div class="wp-core-ui wp-editor-wrap tmce-active">
        <link rel="stylesheet" :href="site_url + 'wp-includes/css/editor.css'" type="text/css" media="all">
        <link rel="stylesheet" :href="site_url + 'wp-includes/js/tinymce/skins/lightgray/skin.min.css'" type="text/css" media="all">

        <div class="wp-editor-container">
            <div class="mce-tinymce mce-container mce-panel" style="visibility: hidden; border-width: 1px;">
                <div class="mce-container-body mce-stack-layout">
                    <div class="mce-toolbar-grp mce-container mce-panel mce-stack-layout-item">
                        <div class="mce-container-body mce-stack-layout">
                            <div class="mce-container mce-toolbar mce-stack-layout-item">
                                <div class="mce-container-body mce-flow-layout">
                                    <div class="mce-container mce-flow-layout-item mce-btn-group">
                                        <div>
                                            <div v-if="is_full" class="mce-widget mce-btn mce-menubtn mce-fixed-width mce-listbox mce-btn-has-text"><button type="button"><span class="mce-txt">Paragraph</span> <i class="mce-caret"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-bold"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-italic"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-bullist"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-numlist"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-blockquote"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-alignleft"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-aligncenter"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-alignright"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-link"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-unlink"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-wp_more"></i></button></div>
                                            <div class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-fullscreen"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-wp_adv"></i></button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mce-container mce-toolbar mce-stack-layout-item">
                                <div class="mce-container-body mce-flow-layout">
                                    <div class="mce-container mce-flow-layout-item mce-btn-group">
                                        <div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-strikethrough"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-hr"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn mce-colorbutton"><button type="button"><i class="mce-ico mce-i-forecolor"></i><span class="mce-preview"></span></button><button type="button" class="mce-open"> <i class="mce-caret"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-pastetext"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-removeformat"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-charmap"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-outdent"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-indent"></i></button></div>
                                            <div class="mce-widget mce-btn mce-disabled"><button type="button"><i class="mce-ico mce-i-undo"></i></button></div>
                                            <div class="mce-widget mce-btn mce-disabled"><button type="button"><i class="mce-ico mce-i-redo"></i></button></div>
                                            <div v-if="is_full" class="mce-widget mce-btn"><button type="button"><i class="mce-ico mce-i-wp_help"></i></button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mce-edit-area mce-container mce-panel mce-stack-layout-item" style="border-width: 1px 0px 0px;">
                        <div style="width: 100%; height: 150px; display: block;">{{default_text}}</div><!-- iframe replacement div -->
                    </div>
                    <div class="mce-statusbar mce-container mce-panel mce-stack-layout-item" style="border-width: 1px 0px 0px;">
                        <div class="mce-container-body mce-flow-layout">
                            <div class="mce-path mce-flow-layout-item">
                                <div class="mce-path-item" data-index="0" aria-level="0">p</div>
                            </div>
                            <div class="mce-flow-layout-item mce-resizehandle"><i class="mce-ico mce-i-resize"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
