<div class="wpuf-fields">
    <div class="wp-media-buttons" v-if="field.insert_image == 'yes'">
        <button type="button" class="button insert-media add_media" data-editor="content">
            <span class="dashicons dashicons-admin-media insert-photo-icon"></span> <?php _e( 'Insert Photo', 'wp-user-frontend' ); ?>
        </button>
    </div>
    <br v-if="field.insert_image == 'yes'" />

    <textarea
        v-if="'no' === field.rich"
        :class="class_names('textareafield')"
        :placeholder="field.placeholder"
        :default_text="field.default"
        :rows="field.rows"
        :cols="field.cols"
    >{{ field.default }}</textarea>

    <text-editor v-if="'no' !== field.rich" :rich="field.rich" :default_text="field.default"></text-editor>

    <span v-if="field.help" class="wpuf-help" v-html="field.help" />
</div>
