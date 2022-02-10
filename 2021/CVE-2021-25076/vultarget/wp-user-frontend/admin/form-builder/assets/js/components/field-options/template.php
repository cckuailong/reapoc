<div class="wpuf-form-builder-field-options">
    <div v-if="!parseInt(editing_field_id)" class="options-fileds-section text-center">
        <p>
            <span class="loader"></span>
        </p>
    </div>

    <div v-else>
        <div class="option-fields-section">
            <h3 class="section-title clearfix" @click="show_basic_settings = !show_basic_settings">
                {{ form_field_type_title }} <i :class="[show_basic_settings ? 'fa fa-angle-down' : 'fa fa-angle-right']"></i>
            </h3>

            <transition name="slide-fade">
                <div v-show="show_basic_settings" class="option-field-section-fields">
                    <component
                        v-for="option_field in basic_settings"
                        :key="option_field.name"
                        :is="'field-' + option_field.type"
                        :option_field="option_field"
                        :editing_form_field="editing_form_field"
                    ></component>
                </div>
            </transition>
        </div>


        <div v-if="advanced_settings.length" class="option-fields-section">
            <h3 class="section-title" @click="show_advanced_settings = !show_advanced_settings">
                {{ i18n.advanced_options }}  <i :class="[show_advanced_settings ? 'fa fa-angle-down' : 'fa fa-angle-right']"></i>
            </h3>

            <transition name="slide-fade">
                <div v-show="show_advanced_settings" class="option-field-section-fields">
                    <component
                        v-for="option_field in advanced_settings"
                        :key="option_field.name"
                        :is="'field-' + option_field.type"
                        :option_field="option_field"
                        :editing_form_field="editing_form_field"
                    ></component>
                </div>
            </transition>
        </div>

        <?php do_action( 'wpuf_builder_field_options' ); ?>
    </div>

</div>
