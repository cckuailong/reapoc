<div class="panel-field-opt panel-field-opt-radio">
    <label v-if="option_field.title">
        {{ option_field.title }} <help-text v-if="option_field.help_text" :text="option_field.help_text"></help-text>
    </label>

    <ul :class="[option_field.inline ? 'list-inline' : '']">
        <li v-for="(option, key) in option_field.options">
            <label>
                <input type="radio" :value="key" v-model="selected"> {{ option }}
            </label>
        </li>
    </ul>

    <div v-if="'logged_in' === selected" class="condiotional-logic-container">

    	<?php $roles = get_editable_roles(); ?>

    	<ul>
			<?php
                foreach ( $roles as $role => $value ) {
                    $role_name = $value['name'];

                    $output  = '<li>';
                    $output .= "<label><input type='checkbox' v-model='choices' value='{$role}'> {$role_name} </label>";
                    $output .= '</li>';

                    echo $output;
                }
            ?>
	    </ul>
    </div>

    <div v-if="'subscribed_users' === selected" class="condiotional-logic-container">

    	<ul>
    		<?php

                if ( class_exists( 'WPUF_Subscription' ) ) {
                    $subscriptions  = WPUF_Subscription::init()->get_subscriptions();

                    if ( $subscriptions ) {
                        foreach ( $subscriptions as $pack ) {
                            $output  = '<li>';
                            $output .= "<label><input type='checkbox' v-model='choices' value='{$pack->ID}' > {$pack->post_title} </label>";
                            $output .= '</li>';

                            echo $output;
                        }
                    } else {
                        _e( 'No subscription plan found.', 'wp-user-frontend' );
                    }
                }
            ?>
    	</ul>

    </div>
</div>