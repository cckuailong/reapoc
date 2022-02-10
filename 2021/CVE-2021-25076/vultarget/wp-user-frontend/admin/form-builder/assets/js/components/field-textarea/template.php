<div class="panel-field-opt panel-field-opt-textarea">
    <label>
        {{ option_field.title }} <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>

        <textarea :rows="option_field.rows || 5" v-model="value"></textarea>
    </label>
</div>
