<div class="wpuf-fields">
    <input
        type="url"
        :class="class_names('url')"
        :placeholder="field.placeholder"
        :value="field.default"
        :size="field.size"
    >
    <span v-if="field.help" class="wpuf-help" v-html="field.help"/>
</div>
