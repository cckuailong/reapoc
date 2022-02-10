<div class="panel-field-opt panel-field-opt-select">
    <label v-if="option_field.title">
        {{ option_field.title }} <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>
    </label>

    <select
        :class="['term-list-selector']"
        v-model="value"
        multiple
    >
        <option v-for="(option, key) in option_field.options" :value="key">{{ option }}</option>
    </select>
</div>
