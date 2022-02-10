<div class="panel-field-opt panel-field-opt-text">
    <div>
        {{ option_field.title }} <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>
        <ul class="pull-right list-inline field-option-actions">
            <li>
                <label>
                    <input
                        type="checkbox"
                        v-model="show_value"
                    /><?php esc_attr_e( 'Show values', 'wp-user-frontend' ); ?>
                </label>
            </li>
            <li>
                <label>
                    <input
                        type="checkbox"
                        v-model="sync_value"
                    /><?php esc_attr_e( 'Sync values', 'wp-user-frontend' ); ?>
                </label>
                <help-text placement="left" text="<?php esc_attr_e( 'When enabled, option values will update according to their labels.', 'wp-user-frontend' ); ?>" />
            </li>
        </ul>
    </div>

    <ul :class="['option-field-option-chooser', show_value ? 'show-value' : '']">
        <li class="clearfix margin-0 header">
            <div class="selector">&nbsp;</div>

            <div class="sort-handler">&nbsp;</div>

            <div class="label">
                <?php esc_attr_e( 'Label', 'wp-user-frontend' ); ?>
                <help-text placement="left" text="<?php esc_attr_e( 'Do not use & or other special character for option label', 'wp-user-frontend' ); ?>" />
            </div>

            <div v-if="show_value" class="value">
                <?php esc_attr_e( 'Value', 'wp-user-frontend' ); ?>
            </div>

            <div class="action-buttons">&nbsp;</div>
        </li>
    </ul>

    <ul :class="['option-field-option-chooser margin-0', show_value ? 'show-value' : '']">
        <li v-for="(option, index) in options" :key="option.id" :data-index="index" class="clearfix option-field-option">
            <div class="selector">
                <input
                    v-if="option_field.is_multiple"
                    type="checkbox"
                    :value="option.value"
                    v-model="selected"
                >
                <input
                    v-else
                    type="radio"
                    :value="option.value"
                    v-model="selected"
                    class="option-chooser-radio"
                >
            </div>

            <div class="sort-handler">
                <i class="fa fa-bars"></i>
            </div>

            <div class="label">
                <input type="text" v-model="option.label" @input="set_option_label(index, option.label)">
            </div>

            <div v-if="show_value" class="value">
                <input type="text" v-model="option.value">
            </div>

            <div class="action-buttons clearfix">
                <i class="fa fa-minus-circle" @click="delete_option(index)"></i>
            </div>
        </li>
        <li>
            <div class="plus-buttons clearfix" @click="add_option">
                <i class="fa fa-plus-circle"></i>
            </div>
        </li>
    </ul>

    <a v-if="!option_field.is_multiple && selected" href="#clear" @click.prevent="clear_selection"><?php esc_attr_e( 'Clear Selection', 'wp-user-frontend' ); ?></a>
</div>
