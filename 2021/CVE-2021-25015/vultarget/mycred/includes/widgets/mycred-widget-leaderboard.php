<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Widget: Leaderboard
 * @since 0.1
 * @version 1.3.2
 */
if ( ! class_exists( 'myCRED_Widget_Leaderboard' ) ) :
    class myCRED_Widget_Leaderboard extends WP_Widget {
 
        /**
         * Construct
         */
        public function __construct() {
 
            parent::__construct(
                'mycred_widget_list',
                sprintf( __( '(%s) Leaderboard', 'mycred' ), mycred_label( true ) ),
                array(
                    'classname'   => 'widget-mycred-list',
                    'description' => __( 'Leaderboard based on instances or balances.', 'mycred' )
                )
            );
 
        }
 
        /**
         * Widget Output
         */
        public function widget( $args, $instance ) {
            
            $instance['title']         = isset( $instance['title'] )         ? $instance['title']         : 'Leaderboard';
            $instance['type']          = isset( $instance['type'] )          ? $instance['type']          : MYCRED_DEFAULT_TYPE_KEY;
            $instance['based_on']      = isset( $instance['based_on'] )      ? $instance['based_on']      : 'balance';
            $instance['total']         = isset( $instance['total'] )         ? $instance['total']         : 0;
            $instance['number']        = isset( $instance['number'] )        ? $instance['number']        : 5;
            $instance['show_visitors'] = isset( $instance['show_visitors'] ) ? $instance['show_visitors'] : 0;
            $instance['row_layout']    = isset( $instance['row_layout'] )    ? $instance['row_layout']    : '<span>#%position%</span> <span>%user_profile_link%</span> <span>%cred_f%</span>';
            $instance['offset']        = isset( $instance['offset'] )        ? $instance['offset']        : 0;
            $instance['order']         = isset( $instance['order'] )         ? $instance['order']         : 'DESC';
            $instance['current']       = isset( $instance['current'] )       ? $instance['current']       : 0;
            $instance['timeframe']     = isset( $instance['timeframe'] )     ? $instance['timeframe']     : '';
            $instance['exclude']     = isset( $instance['exclude'] )     ? $instance['exclude']     : '';
            $instance['wrap']          = isset( $instance['wrap'] )          ? $instance['wrap']          : 'li';
            $instance['nothing']       = isset( $instance['nothing'] )       ? $instance['nothing']       : 'Leaderboard is empty';
            $instance['exclude_zero']  = isset( $instance['exclude_zero'] )  ? $instance['exclude_zero']  : 1;
            
            extract( $args, EXTR_SKIP );
 
            // Check if we want to show this to visitors
            if ( (! isset($instance['show_visitors']) || ! $instance['show_visitors']) && ! is_user_logged_in() ) return;
 
            if ( ! isset( $instance['type'] ) || empty( $instance['type'] ) )
                $instance['type'] = MYCRED_DEFAULT_TYPE_KEY;
 
            $mycred = mycred( $instance['type'] );
 
            // Get Rankings
            $args = array(
                'number'   => $instance['number'],
                'template' => $instance['row_layout'],
                'type'     => $instance['type'],
                'based_on' => $instance['based_on'],
                'total' => $instance['total'],
                'timeframe' => $instance['timeframe'],
                'wrap' => $instance['wrap'],
                'nothing' => $instance['nothing'],
                'exclude_zero' => $instance['exclude_zero'],
                'exclude' => $instance['exclude']
            );
 
            if ( isset( $instance['order'] ) )
                $args['order'] = $instance['order'];
 
            if ( isset( $instance['offset'] ) )
                $args['offset'] = $instance['offset'];
 
            if ( isset( $instance['current'] ) )
                $args['current'] = 1;
 
            echo $before_widget;
 
            // Title
            if ( ! empty( $instance['title'] ) )
                echo $before_title . $mycred->template_tags_general( $instance['title'] ) . $after_title;
 
            echo mycred_render_shortcode_leaderboard( $args );
 
            // Footer
            echo $after_widget;
 
        }
 
        /**
         * Outputs the options form on admin
         */
        public function form( $instance ) {
 
            // Defaults
            $title         = isset( $instance['title'] )         ? $instance['title']         : 'Leaderboard';
            $type          = isset( $instance['type'] )          ? $instance['type']          : MYCRED_DEFAULT_TYPE_KEY;
            $based_on      = isset( $instance['based_on'] )      ? $instance['based_on']      : 'balance';
            $total         = isset( $instance['total'] )         ? $instance['total']         : 0;
 
            $number        = isset( $instance['number'] )        ? $instance['number']        : 5;
            $show_visitors = isset( $instance['show_visitors'] ) ? $instance['show_visitors'] : 0;
            $row_layout    = isset( $instance['row_layout'] )    ? $instance['row_layout']    : '<span>#%position%</span> <span>%user_profile_link%</span> <span>%cred_f%</span>';
            $offset        = isset( $instance['offset'] )        ? $instance['offset']        : 0;
            $order         = isset( $instance['order'] )         ? $instance['order']         : 'DESC';
            $current       = isset( $instance['current'] )       ? $instance['current']       : 0;
            $timeframe     = isset( $instance['timeframe'] )     ? $instance['timeframe']     : '';
            $wrap          = isset( $instance['wrap'] )          ? $instance['wrap']          : 'li';
            $nothing       = isset( $instance['nothing'] )       ? $instance['nothing']       : 'Leaderboard is empty';
            $exclude_zero  = isset( $instance['exclude_zero'] )  ? $instance['exclude_zero']  : 1;
            $exclude     = isset( $instance['exclude'] )     ? $instance['exclude']     : '';
            $mycred        = mycred( $type );
            $mycred_types  = mycred_get_types();
 
?>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
</p>
 
<?php if ( count( $mycred_types ) > 1 ) : ?>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _e( 'Point Type', 'mycred' ); ?>:</label>
    <?php mycred_types_select_from_dropdown( $this->get_field_name( 'type' ), $this->get_field_id( 'type' ), $type ); ?>
</p>
<?php else : ?>
    <?php mycred_types_select_from_dropdown( $this->get_field_name( 'type' ), $this->get_field_id( 'type' ), $type ); ?>
<?php endif; ?>
 
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'based_on' ) ); ?>"><?php _e( 'Based On', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'based_on' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'based_on' ) ); ?>" type="text" value="<?php echo esc_attr( $based_on ); ?>" class="widefat" />
    <small><?php _e( 'Use "balance" to base the leaderboard on your users current balances or use a specific reference.', 'mycred' ); ?> <a href="http://codex.mycred.me/chapter-vi/log-references/" target="_blank"><?php _e( 'Reference Guide', 'mycred' ); ?></a></small>
</p>
 
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'total' ) ); ?>"><?php _e( 'Total', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'total' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'total' ) ); ?>" type="text" value="<?php echo esc_attr( $total ); ?>" class="widefat" />
    <small><?php _e( 'When showing a leaderboard based on balances, you can select to use users total balance (1) instead of their current balance (0).', 'mycred' ); ?> </small>
</p>
 
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'wrap' ) ); ?>"><?php _e( 'Wrap', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'wrap' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wrap' ) ); ?>" type="text" value="<?php echo esc_attr( $wrap ); ?>" class="widefat" />
    <small><?php _e( 'The wrapping element to use for the list. By default the leaderboard renders an organized list (ol) and each item uses a list element (li).', 'mycred' ); ?> </small>
</p>
 
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'nothing' ) ); ?>"><?php _e( 'Nothing', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'nothing' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'nothing' ) ); ?>" type="text" value="<?php echo esc_attr( $nothing ); ?>" class="widefat" />
    <small><?php _e( 'The message to show users when the leaderboard is empty.', 'mycred' ); ?></small>
</p>
 
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_zero' ) ); ?>"><?php _e( 'Exclude Zero', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'exclude_zero' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_zero' ) ); ?>" type="text" value="<?php echo esc_attr( $exclude_zero ); ?>" class="widefat" />
    <small><?php _e( 'Option to filter out users with zero balances / results. Use 1 to enable and 0 to disable.', 'mycred' ); ?> </small>
</p>
 
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_visitors' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>" value="1"<?php checked( $show_visitors, 1 ); ?> class="checkbox" /> <?php _e( 'Visible to non-members', 'mycred' ); ?></label>
</p>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of users', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo absint( $number ); ?>" size="3" class="widefat" />
</p>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'row_layout' ) ); ?>"><?php _e( 'Row layout', 'mycred' ); ?>:</label>
    <textarea name="<?php echo esc_attr( $this->get_field_name( 'row_layout' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'row_layout' ) ); ?>" rows="3" cols="20" class="widefat"><?php echo esc_attr( $row_layout ); ?></textarea>
    <small><?php echo $mycred->available_template_tags( array( 'general', 'balance' ) ); ?></small>
</p>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>"><?php _e( 'Offset', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'offset' ) ); ?>" type="text" value="<?php echo absint( $offset ); ?>" size="3" class="widefat" />
    <small><?php _e( 'Optional offset of order. Use zero to return the first in the list.', 'mycred' ); ?></small>
</p>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php _e( 'Order', 'mycred' ); ?>:</label> 
    <select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
<?php
 
            $options = array(
                'ASC'  => __( 'Ascending', 'mycred' ),
                'DESC' => __( 'Descending', 'mycred' )
            );
 
            foreach ( $options as $value => $label ) {
                echo '<option value="' . $value . '"';
                if ( $order == $value ) echo ' selected="selected"';
                echo '>' . $label . '</option>';
            }
 
?>
    </select>
</p>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'current' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'current' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'current' ) ); ?>" value="1"<?php checked( $current, 1 ); ?> class="checkbox" />  <?php _e( 'Append current users position', 'mycred' ); ?></label><br />
    <small><?php _e( 'If the current user is not in this leaderboard, you can select to append them at the end with their current position.', 'mycred' ); ?></small>
</p>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>"><?php _e( 'Timeframe', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'timeframe' ) ); ?>" type="text" value="<?php echo esc_attr( $timeframe ); ?>" size="3" class="widefat" />
    <small><?php _e( 'Option to limit the leaderboard based on a specific timeframe. Leave empty if not used.', 'mycred' ); ?></small>
</p>
<p class="myCRED-widget-field">
    <label for="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>"><?php _e( 'Exclude Users', 'mycred' ); ?>:</label>
    <input id="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>" size="3" class="widefat" />
    <small><?php _e( 'Option to exclude users from leaderboard based on a specific role or id. Leave empty if not used. Use comma seperated values for Role or ID', 'mycred' ); ?></small>
</p>
<?php
 
        }
 
        /**
         * Processes widget options to be saved
         */
        public function update( $new_instance, $old_instance ) {
 
            $instance                  = $old_instance;
 
            $instance['number']        = absint( $new_instance['number'] );
            $instance['title']         = wp_kses_post( $new_instance['title'] );
            $instance['type']          = sanitize_key( $new_instance['type'] );
            $instance['based_on']      = sanitize_key( $new_instance['based_on'] );
            $instance['total']         = sanitize_key( $new_instance['total'] );
            $instance['show_visitors'] = ( isset( $new_instance['show_visitors'] ) ) ? 1 : 0;
            $instance['row_layout']    = wp_kses_post( $new_instance['row_layout'] );
            $instance['offset']        = sanitize_text_field( $new_instance['offset'] );
            $instance['order']         = sanitize_text_field( $new_instance['order'] );
            $instance['current']       = ( isset( $new_instance['current'] ) ) ? 1 : 0;
            $instance['timeframe']     = sanitize_text_field( $new_instance['timeframe'] );
            $instance['wrap']          = sanitize_text_field( $new_instance['wrap'] );
            $instance['nothing']       = sanitize_text_field( $new_instance['nothing'] );
            $instance['exclude_zero']  = sanitize_text_field( $new_instance['exclude_zero'] );
            $instance['exclude']  = sanitize_text_field( $new_instance['exclude'] );
 
            mycred_flush_widget_cache( 'mycred_widget_list' );
 
            return $instance;
 
        }
 
    }
endif;