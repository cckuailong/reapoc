<div class="wpuf-fields">
    <input
        type="text"
        :class="class_names('textfield')"
        :placeholder="field.placeholder"
        :value="field.default"
        :size="field.size"
    >

    <span v-if="field.help" class="wpuf-help" v-html="field.help" />
</div>
