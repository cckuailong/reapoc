<div class="wpuf-fields">
    <ul :class="['wpuf-fields-list', ('yes' === field.inline) ? 'wpuf-list-inline' : '']">
        <li v-if="has_options" v-for="(label, val) in field.options">
            <label>
                <input
                    type="checkbox"
                    :value="val"
                    :checked="is_selected(val)"
                    :class="class_names('checkbox_btns')"
                > {{ label }}
            </label>
        </li>
    </ul>

    <span v-if="field.help" class="wpuf-help" v-html="field.help" />
</div>
