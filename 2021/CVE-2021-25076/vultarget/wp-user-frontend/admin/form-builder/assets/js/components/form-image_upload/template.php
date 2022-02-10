<div class="wpuf-fields">
    <div :id="'wpuf-img_label-' + field.id + '-upload-container'">
        <div class="wpuf-attachment-upload-filelist" data-type="file" data-required="yes">
            <a class="button file-selector wpuf_img_label_148" href="#">
                <template v-if="field.button_label === ''">
                    <?php _e( 'Select Image', 'wp-user-frontend' ); ?>
                </template>
                <template v-else>
                    {{ field.button_label }}
                </template>
            </a>
        </div>
    </div>

    <span v-if="field.help" class="wpuf-help" v-html="field.help"/>
</div>
