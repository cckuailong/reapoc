<div class="wpuf-fields">
    <textarea
        v-if="'no' === field.rich"
        :class="class_names('textareafield')"
        :placeholder="field.placeholder"
        :deault="field.default"
        :rows="field.rows"
        :cols="field.cols"
    >{{ field.default }}</textarea>

    <text-editor v-if="'no' !== field.rich" :default_text="field.default" :rich="field.rich"></text-editor>

    <span v-if="field.help" class="wpuf-help" v-html="field.help" />
</div>
