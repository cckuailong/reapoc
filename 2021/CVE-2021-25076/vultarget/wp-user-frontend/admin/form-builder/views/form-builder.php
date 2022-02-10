<form id="wpuf-form-builder" class="wpuf-form-builder-<?php echo esc_attr( $form_type ); ?>" method="post" action="" @submit.prevent="save_form_builder" v-cloak>
    <fieldset :class="[is_form_saving ? 'disabled' : '']" :disabled="is_form_saving">
        <h2 class="nav-tab-wrapper">
            <a href="#wpuf-form-builder-container" class="nav-tab nav-tab-active">
                <?php esc_html_e( 'Form Editor', 'wp-user-frontend' ); ?>
            </a>

            <a href="#wpuf-form-builder-settings" class="nav-tab">
                <?php esc_html_e( 'Settings', 'wp-user-frontend' ); ?>
            </a>

            <?php do_action( "wpuf-form-builder-tabs-{$form_type}" ); ?>

            <span class="pull-right">
                <a :href="'<?php echo get_wpuf_preview_page(); ?>?wpuf_preview=1&form_id=' + post.ID" target="_blank" class="button"><span class="dashicons dashicons-visibility" style="padding-top: 3px;"></span> <?php esc_html_e( 'Preview', 'wp-user-frontend' ); ?></a>

                <button v-if="!is_form_saving" type="button" class="button button-primary" @click="save_form_builder">
                    <?php esc_html_e( 'Save Form', 'wp-user-frontend' ); ?>
                </button>

                <button v-else type="button" class="button button-primary button-ajax-working" disabled>
                    <span class="loader"></span> <?php esc_html_e( 'Saving Form Data', 'wp-user-frontend' ); ?>
                </button>
            </span>
            <span id="wpuf-toggle-field-options"><?php esc_html_e( 'Add Fields', 'wp-user-frontend' ); ?></span>
            <span id="wpuf-toggle-show-form"><?php esc_html_e( 'Show Form', 'wp-user-frontend' ); ?></span>
        </h2>

        <div class="tab-contents">
            <div id="wpuf-form-builder-container" class="group active">
                <div id="builder-stage">
                    <header class="clearfix">
                        <span v-if="!post_title_editing" class="form-title" @click.prevent="post_title_editing = true">{{ post.post_title }}</span>

                        <span v-show="post_title_editing">
                            <input type="text" v-model="post.post_title" name="post_title" />
                            <button type="button" class="button button-small" style="margin-top: 13px;" @click.prevent="post_title_editing = false"><i class="fa fa-check"></i></button>
                        </span>

                        <i :class="(is_form_switcher ? 'fa fa-angle-up' : 'fa fa-angle-down') + ' form-switcher-arrow'" @click.prevent="switch_form"></i>
                        <?php
                            $form_id = isset( $_GET['id'] ) ? intval( wp_unslash( $_GET['id'] ) ) : 0;

                        if ( count( $shortcodes ) > 1 && isset( $shortcodes[0]['type'] ) ) {
                            foreach ( $shortcodes as $shortcode ) {
                                /* translators: %s: form id */
                                printf( "<span class=\"form-id\" title=\"%s\" data-clipboard-text='%s'><i class=\"fa fa-clipboard\" aria-hidden=\"true\"></i> %s: #{{ post.ID }}</span>", sprintf( esc_html( __( 'Click to copy %s shortcode', 'wp-user-frontend' ) ), esc_attr( $shortcode['type'] ) ), sprintf( '[%s type="%s" id="%s"]', esc_attr( $shortcode['name'] ), esc_attr( $shortcode['type'] ), esc_attr( $form_id ) ), esc_attr( ucwords( $shortcode['type'] ) ), esc_attr( $shortcode['type'] ) );
                            }
                        } else {
                            printf( "<span class=\"form-id\" title=\"%s\" data-clipboard-text='%s'><i class=\"fa fa-clipboard\" aria-hidden=\"true\"></i> #{{ post.ID }}</span>", esc_html( __( 'Click to copy shortcode', 'wp-user-frontend' ) ), '[' . esc_attr( $shortcodes[0]['name'] ) . ' id="' . esc_attr( $form_id ) . '"]' );
                        }
                        ?>
                    </header>

                    <ul v-if="is_form_switcher" class="form-switcher-content">
                        <?php
                        foreach ( $forms as $form ) {
                            ?>
                                <li><a class="<?php echo ( (int) $form->ID === $_GET['id'] ) ? 'active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wpuf-' . $form_type . '-forms&action=edit&id=' . $form->ID ) ); ?>"><?php echo esc_html( $form->post_title ); ?></a></li>
                            <?php
                        }
                        ?>
                    </ul>

                    <section>
                        <div id="form-preview">
                            <builder-stage></builder-stage>
                        </div>
                    </section>
                </div><!-- #builder-stage -->

                <div id="builder-form-fields">
                    <header>
                        <ul class="clearfix">
                            <li :class="['form-fields' === current_panel ? 'active' : '']">
                                <a href="#add-fields" @click.prevent="set_current_panel('form-fields')">
                                    <?php esc_html_e( 'Add Fields', 'wp-user-frontend' ); ?>
                                </a>
                            </li>

                            <li :class="['field-options' === current_panel ? 'active' : '', !form_fields_count ? 'disabled' : '']">
                                <a href="#field-options" @click.prevent="set_current_panel('field-options')">
                                    <?php esc_html_e( 'Field Options', 'wp-user-frontend' ); ?>
                                </a>
                            </li>
                        </ul>
                    </header>

                    <section>
                        <div class="wpuf-form-builder-panel">
                            <component :is="current_panel"></component>
                        </div>
                    </section>
                </div><!-- #builder-form-fields -->
            </div><!-- #wpuf-form-builder-container -->

            <div id="wpuf-form-builder-settings" class="group clearfix">
                <fieldset>
                    <h2 id="wpuf-form-builder-settings-tabs" class="nav-tab-wrapper">
                        <?php do_action( "wpuf-form-builder-settings-tabs-{$form_type}" ); ?>
                    </h2><!-- #wpuf-form-builder-settings-tabs -->

                    <div id="wpuf-form-builder-settings-contents" class="tab-contents">
                        <?php do_action( "wpuf-form-builder-settings-tab-contents-{$form_type}" ); ?>
                    </div><!-- #wpuf-form-builder-settings-contents -->
                </fieldset>
            </div><!-- #wpuf-form-builder-settings -->

            <?php do_action( "wpuf-form-builder-tab-contents-{$form_type}" ); ?>
        </div>

        <?php if ( ! empty( $form_settings_key ) ) { ?>
            <input type="hidden" name="form_settings_key" value="<?php echo esc_attr( $form_settings_key ); ?>">
        <?php } ?>

        <?php wp_nonce_field( 'wpuf_form_builder_save_form', 'wpuf_form_builder_nonce' ); ?>

        <input type="hidden" name="wpuf_form_id" value="<?php echo esc_attr( $form_id ); ?>">
    </fieldset>
</form><!-- #wpuf-form-builder -->
