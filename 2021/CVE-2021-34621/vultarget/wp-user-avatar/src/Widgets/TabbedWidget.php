<?php

namespace ProfilePress\Core\Widgets;


class TabbedWidget extends \WP_Widget
{
    public $widget_status;

    /**
     * Register widget with WordPress.
     */
    function __construct()
    {
        parent::__construct(
            'pp_tabbed_widget', // Base ID
            esc_html__('ProfilePress Tabbed Widget', 'wp-user-avatar'), // Name
            array(
                'description' => esc_html__('A tabbed login, registration and lost password widget', 'wp-user-avatar'),
            ),
            array('width' => 400, 'height' => 350)// Args
        );

        add_action('wp_footer', [$this, 'script']);

        add_action('wp', [$this, 'process_form']);
    }

    public function process_form()
    {
        if (isset($_POST['tabbed_login_submit'])) {
            $this->widget_status = @TabbedWidgetDependency::login(
                trim($_POST['tabbed-login-name']),
                $_POST['tabbed-login-password'],
                sanitize_text_field($_POST['tabbed-login-remember-me'])
            );
        }

        if (isset($_POST['tabbed_reset_passkey'])) {
            $this->widget_status = @TabbedWidgetDependency::retrieve_password_process($_POST['tabbed-user-login']);
        }

        if (isset($_POST['tabbed_reg_submit'])) {
            $this->widget_status = @TabbedWidgetDependency::registration(
                $_POST['tabbed-reg-username'],
                $_POST['tabbed-reg-password'],
                $_POST['tabbed-reg-email']
            );
        }
    }

    public function script()
    {
        ?>
        <script>
            if (typeof jQuery !== 'undefined') {
                (function ($) {
                    $('.pp-tab-widget').on('click', 'li a', function (e) {
                        e.preventDefault();
                        var $tab = $(this),
                            href = $tab.attr('href');

                        $('.pp-active').removeClass('pp-active');
                        $tab.addClass('pp-active');

                        $('.pp-show')
                            .removeClass('pp-show')
                            .addClass('pp-hide')
                            .hide();

                        $(href)
                            .removeClass('pp-hide')
                            .addClass('pp-show')
                            .hide()
                            .fadeIn(550);
                    });
                })(jQuery);
            }
        </script>
        <?php
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);

        $processing_label         = apply_filters('ppress_tab_widget_processing_label', esc_html__('Processing', 'wp-user-avatar'));
        $login_btn_label          = esc_html__('Log In', 'wp-user-avatar');
        $signup_btn_label         = esc_html__('Sign Up', 'wp-user-avatar');
        $password_reset_btn_label = esc_html__('Get New Password', 'wp-user-avatar');

        echo '<style>';
        echo $instance['tabbed_css'];
        echo '</style>';

        if ( ! is_user_logged_in() && ! empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if ( ! is_user_logged_in()) {
            echo $args['before_widget'];
            if (isset($this->widget_status)) echo '<div class="pp-tab-status">', $this->widget_status, '</div>';
            ?>
            <div class="pp-tab-widget-form">
                <ul class="pp-tab-widget">
                    <li>
                        <a href="#pp-login" class="pp-active"><?php echo apply_filters('ppress_tab_login_text', esc_html__('Login', 'wp-user-avatar')); ?></a>
                    </li>
                    <li>
                        <a href="#pp-register"><?php echo apply_filters('ppress_tab_register_text', esc_html__('Register', 'wp-user-avatar')); ?></a>
                    </li>
                    <li>
                        <a href="#pp-reset"><?php echo apply_filters('ppress_tab_password_reset_text', esc_html__('Forgot?', 'wp-user-avatar')); ?></a>
                    </li>
                </ul>
                <div id="pp-login" class="pp-form-action pp-show">
                    <div class="heading"><?php echo isset($instance['login_text']) ? $instance['login_text'] : 'Have an account?'; ?></div>
                    <form data-pp-form-submit="login" method="post" action="<?php echo esc_url_raw($_SERVER['REQUEST_URI']); ?>">
                        <ul class="tab-widget" style="list-style: none">
                            <li>
                                <?php
                                $login_username_email_restrict_settings = ppress_get_setting('login_username_email_restrict');

                                $login_placeholder = esc_html__('Username', 'wp-user-avatar');

                                if ( ! empty($login_username_email_restrict_settings) && $login_username_email_restrict_settings == 'both') {
                                    $login_placeholder = esc_html__('Username or Email', 'wp-user-avatar');
                                }

                                if ( ! empty($login_username_email_restrict_settings) && $login_username_email_restrict_settings == 'email') {
                                    $login_placeholder = esc_html__('Email Address', 'wp-user-avatar');
                                }
                                ?>
                                <input type="hidden" name="is-pp-tab-widget" value="true">
                                <input type="text" name="tabbed-login-name" value="<?php echo(isset($_POST['tabbed-login-name']) ? $_POST['tabbed-login-name'] : ''); ?>" placeholder="<?php echo $login_placeholder; ?>" required/>
                            </li>
                            <li>
                                <input name="tabbed-login-password" value="<?php echo(isset($_POST['tabbed-login-password']) ? $_POST['tabbed-login-password'] : ''); ?>" type="password" placeholder="Password" required/>
                            </li>
                            <li class="remember-login">
                                <input id="remember-login" name="tabbed-login-remember-me" type="checkbox" value="true">
                                <label for="remember-login" class="css-label lite-cyan-check"><?= esc_html__('Remember Me', 'wp-user-avatar'); ?></label>
                            </li>
                            <li>
                                <input data-pp-submit-label="<?= $login_btn_label ?>" data-pp-processing-label="<?= $processing_label ?>" name="tabbed_login_submit" type="submit" value="<?= $login_btn_label ?>" class="tb-button"/>
                            </li>
                        </ul>
                    </form>
                </div>
                <!--/#login.pp-form-action-->
                <div id="pp-register" class="pp-form-action pp-hide">
                    <div class="heading"><?php echo isset($instance['reg_text']) ? $instance['reg_text'] : 'Don\'t have an account?'; ?></div>

                    <div class="tab-widget">
                        <form data-pp-form-submit="signup" method="post" action="<?php echo esc_url_raw($_SERVER['REQUEST_URI']); ?>">
                            <ul class="tab-widget" style="list-style: none">
                                <li>
                                    <input type="hidden" name="is-pp-tab-widget" value="true">
                                    <input type="text" name="tabbed-reg-username" placeholder="Username" value="<?php echo(isset($_POST['tabbed-reg-username']) ? $_POST['tabbed-reg-username'] : ''); ?>" required/>
                                </li>
                                <li>
                                    <input type="email" name="tabbed-reg-email" placeholder="Email" value="<?php echo(isset($_POST['tabbed-reg-email']) ? $_POST['tabbed-reg-email'] : ''); ?>" required/>
                                </li>
                                <li>
                                    <input type="password" name="tabbed-reg-password" placeholder="Password" value="<?php echo(isset($_POST['tabbed-reg-password']) ? $_POST['tabbed-reg-password'] : ''); ?>" required/>
                                </li>
                                <li>
                                    <input data-pp-submit-label="<?= $signup_btn_label ?>" data-pp-processing-label="<?= $processing_label ?>" name="tabbed_reg_submit" type="submit" value="<?= $signup_btn_label ?>" class="tb-button"/>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>

                <div id="pp-reset" class="pp-form-action pp-hide">
                    <div class="heading">
                        <?php echo isset($instance['lostp_text']) ? $instance['lostp_text'] : 'Forgot Password?'; ?>
                    </div>

                    <div class="tab-widget">
                        <form data-pp-form-submit="passwordreset" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                            <ul class="tab-widget" style="list-style: none">
                                <li>
                                    <input name="tabbed-user-login" value="<?php echo(isset($_POST['tabbed-user-login']) ? $_POST['tabbed-user-login'] : ''); ?>" type="text" placeholder="Username or E-mail:" required/>
                                    <input type="hidden" name="is-pp-tab-widget" value="true">
                                </li>
                                <li>
                                    <input data-pp-submit-label="<?= $password_reset_btn_label ?>" data-pp-processing-label="<?= $processing_label ?>" name="tabbed_reset_passkey" type="submit" value="<?= $password_reset_btn_label ?>" class="tb-button"/>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            echo $args['after_widget'];

            return;
        }

        $user_data = wp_get_current_user();
        echo $args['before_widget'];
        ?>

        <div class="pp-tabbed-user-panel">
            <?php
            echo '<a href="' . ppress_profile_url() . '"><div class="pp-tab-widget-avatar">';
            echo get_avatar($user_data->ID, 500);
            echo '</div></a>';
            ?>
            <h3 class="pp-tabbed-user-panel-title"><?php printf(__('Welcome %s', 'wp-user-avatar'), ucfirst($user_data->display_name)); ?></h3>
            <br/>
            <p>
                <a class="pp-tabbed-btn pp-tabbed-btn-inverse" href="<?php echo ppress_edit_profile_url(); ?>"><?php _e('Edit your profile', 'wp-user-avatar'); ?></a>
            </p>
            <p>
                <a class="pp-tabbed-btn pp-tabbed-btn-inverse" href="<?php echo wp_logout_url(); ?>"><?php _e('Log out', 'wp-user-avatar'); ?></a>
            </p>
        </div>
        <?php

        echo $args['after_widget'];
    }


    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = esc_html__('Login / Sign up', 'wp-user-avatar');
        }

        if (isset($instance['login_text'])) {
            $login_text = $instance['login_text'];
        } else {
            $login_text = esc_html__('Have an account?', 'wp-user-avatar');
        }

        if (isset($instance['reg_text'])) {
            $reg_text = $instance['reg_text'];
        } else {
            $reg_text = esc_html__('Don\'t have an account?', 'wp-user-avatar');
        }

        if (isset($instance['lostp_text'])) {
            $lostp_text = $instance['lostp_text'];
        } else {
            $lostp_text = esc_html__('Forgot Password?', 'wp-user-avatar');
        }

        $auto_login_after_reg = ppress_get_setting('set_auto_login_after_reg', 'off');

        if (isset($instance['auto_login_after_reg'])) {
            $auto_login_after_reg = $instance['auto_login_after_reg'];
        }

        $tabbed_css = isset($instance['tabbed_css']) ? $instance['tabbed_css'] : <<<CSS
.pp-tab-status {
    background: rgba(247, 245, 231, 0.7);
    padding: 10px 8px;
    margin: 0;
    color: #141412;
    border-radius: 5px;
    max-width: 350px;
}

.pp-tab-status a {
    color: #bc360a !important;
}

.pp-tab-widget-form {
    background: #edeff1;
    padding-bottom: 20px;
    margin: 10px auto;
    width: 100%;
    max-width: 350px;
    position: relative;
    font-family: Helvetica, Arial, sans-serif;
}

.pp-tab-widget-form li.remember-login {
    margin-bottom: 10px;
}

.pp-tab-widget-form .pp-tab-widget {
    color: #fff !important;
    background: #2f4154;
    height: 40px;
    margin: 0;
    padding: 0;
    list-style-type: none;
    width: 100%;
    position: relative;
    display: block;
    margin-bottom: 20px;
}

.pp-tab-widget-form .pp-tab-widget li {
    color: #fff !important;
    width: 30%;
    display: block;
    float: left;
    margin: 0;
    padding: 0;
}

.pp-tab-widget-form ul li:before {
    content: none  !important;
}

.pp-tab-widget-form .pp-tab-widget a {
    color: #fff !important;
    background: #2f4154;
    display: block;
    float: left;
    text-decoration: none;
    font-size: 16px;
    padding: 5px 6px;

}

.pp-tab-widget-form .pp-tab-widget li:last-child a {
    border-right: none;
    width: 90%;
    padding-left: 0;
    padding-right: 0;
    text-align: center;
}

.pp-tab-widget-form ul.pp-tab-widget {
    margin-left: 0 !important;
}


.pp-tab-widget-form .pp-tab-widget a.pp-active {
    border-top: 4px solid #1abc9c;
    padding: 3px 6px;
    border-right: none;
    -webkit-transition: all 0.5s linear;
    -moz-transition: all 0.5s linear;
    transition: all 0.5s linear;
}

.pp-tab-widget-form .pp-tab-widget a.focus {
    color: #2f4154 !important;
    outline: none !important;
}

.pp-tab-widget-form .pp-form-action {
    padding: 0 20px;
    position: relative;
}

.pp-tab-widget-form .pp-form-action h1 {
    font-size: 22px;
    font-weight: 500;
    margin: 0;
    padding-bottom: 10px;
}

.pp-tab-widget-form .pp-form-action .heading {
    font-size: 22px;
    font-weight: 500;
    margin: 0;
    padding-bottom: 10px;
}

.pp-tab-widget-form .pp-form-action p {
    font-size: 12px;
    padding-bottom: 10px;
    line-height: 25px;
}

.pp-tab-widget-form .tab-widget input[type=text],
.pp-tab-widget-form .tab-widget input[type=email],
.pp-tab-widget-form .tab-widget input[type=password] {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    width: 100%;
    height: 40px;
    margin-bottom: 10px;
    padding-left: 15px;
    background: #fff;
    border: none;
    color: #6d7680;
    outline: none;
}

.pp-tab-widget-form .pp-show {
    display: block;
}

.pp-tab-widget-form .pp-hide {
    display: none;
}

.pp-tab-widget-form input.tb-button {
    border: none;
    display: block;
    background: #136899;
    height: 40px;
    width: 100%;
    color: #ffffff;
    text-align: center;
    border-radius: 5px;
    -webkit-transition: all 0.15s linear;
    -moz-transition: all 0.15s linear;
    transition: all 0.15s linear;
}

.pp-tab-widget-form input.tb-button:hover {
    background: #1e75aa;
}

.pp-tab-widget-form input.tb-button:active {
    background: #136899;
}

.pp-tab-widget-avatar img {
    display: block;
    border-radius: 50%;
    height: 190px;
    margin: 0 auto 10px !important;
    padding: 2px;
    text-align: center;
    width: 190px;
    float: none !important;
}

.pp-tabbed-user-panel {
    border-radius: 6px;
    text-align: center;
}

.pp-tabbed-user-panel-title {
    font-size: 20px;
    margin: 0;
}

.pp-tabbed-user-panel p {
    font-size: 15px;
    margin-bottom: 23px;
}

.pp-tabbed-btn {
    border: none;
    font-size: 15px;
    font-weight: 400;
    line-height: 1.4;
    border-radius: 4px;
    padding: 10px 15px;
    -webkit-font-smoothing: subpixel-antialiased;
    -webkit-transition: border .25s linear,color .25s linear,background-color .25s linear;
    transition: border .25s linear,color .25s linear,background-color .25s linear;
}

.pp-tabbed-btn-inverse {
    color: #fff!important;
    background-color: #34495e;
}
CSS;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>


        <p>
            <label for="<?php echo $this->get_field_id('login_text'); ?>"><?php _e('Login text:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('login_text'); ?>" name="<?php echo $this->get_field_name('login_text'); ?>" type="text" value="<?php echo esc_attr($login_text); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('reg_text'); ?>"><?php _e('Registration text:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('reg_text'); ?>" name="<?php echo $this->get_field_name('reg_text'); ?>" type="text" value="<?php echo esc_attr($reg_text); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('lostp_text'); ?>"><?php _e('Lost-password text:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('lostp_text'); ?>" name="<?php echo $this->get_field_name('lostp_text'); ?>" type="text" value="<?php echo esc_attr($lostp_text); ?>">
        </p>

        <p>
            <input class="widefat" id="<?php echo $this->get_field_id('auto_login_after_reg'); ?>" name="<?php echo $this->get_field_name('auto_login_after_reg'); ?>" type="checkbox" value="on" <?php checked($auto_login_after_reg, 'on'); ?>>
            <label for="<?php echo $this->get_field_id('auto_login_after_reg'); ?>"><?php _e('Automatically login user after successful registration'); ?></label>

        </p>

        <p>
            <label
                    for="<?php echo $this->get_field_id('tabbed_css'); ?>"><?php _e('Widget CSS:'); ?></label>
            <textarea name="<?php echo $this->get_field_name('tabbed_css'); ?>" id="<?php echo $this->get_field_id('tabbed_css'); ?>" cols="20" rows="16" class="widefat"><?php echo esc_textarea($tabbed_css); ?></textarea>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance                         = array();
        $instance['title']                = ( ! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['login_text']           = ( ! empty($new_instance['login_text'])) ? strip_tags($new_instance['login_text']) : '';
        $instance['reg_text']             = ( ! empty($new_instance['reg_text'])) ? strip_tags($new_instance['reg_text']) : '';
        $instance['lostp_text']           = ( ! empty($new_instance['lostp_text'])) ? strip_tags($new_instance['lostp_text']) : '';
        $instance['auto_login_after_reg'] = ( ! empty($new_instance['auto_login_after_reg'])) ? strip_tags($new_instance['auto_login_after_reg']) : '';
        $instance['tabbed_css']           = ( ! empty($new_instance['tabbed_css'])) ? strip_tags($new_instance['tabbed_css']) : '';

        return $instance;
    }

}