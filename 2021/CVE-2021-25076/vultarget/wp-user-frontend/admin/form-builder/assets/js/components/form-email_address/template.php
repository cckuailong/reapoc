<div class="wpuf-fields">
    <input
        type="email"
        :class="class_names('email')"
        :placeholder="field.placeholder"
        :value="field.default"
        :size="field.size"
    >
    <span v-if="field.help" class="wpuf-help" v-html="field.help" />
</div>
