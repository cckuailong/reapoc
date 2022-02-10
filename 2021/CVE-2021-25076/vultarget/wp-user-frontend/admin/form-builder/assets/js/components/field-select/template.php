<div class="panel-field-opt panel-field-opt-select">
    <label v-if="option_field.title">
        {{ option_field.title }} <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>
    </label>

    <select class="opt-select-element" v-model="value">
        <option value=""><?php _e( 'Select an option', 'wp-user-frontend' ); ?></option>
        <option v-for="(option, key) in option_field.options" :value="key">{{ option }}</option>
    </select>
</div>
