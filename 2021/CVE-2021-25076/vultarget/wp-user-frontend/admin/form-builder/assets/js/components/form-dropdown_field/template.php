<div class="wpuf-fields">
    <select
        :class="class_names('select_lbl')"
    >
        <option v-if="field.first" value="">{{ field.first }}</option>

        <option
            v-if="has_options"
            v-for="(label, val) in field.options"
            :value="label"
            :selected="is_selected(label)"
        >{{ label }}</option>
    </select>

    <span v-if="field.help" class="wpuf-help" v-html="field.help"> </span>
</div>
