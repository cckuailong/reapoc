<?php

namespace ProfilePress\Core\Classes;

class WelcomeEmailAfterSignup
{
    private $wp_user;

    private $password;

    public function __construct($user_id, $password)
    {
        $this->wp_user  = get_userdata($user_id);
        $this->password = $password;

        $this->send_welcome_mail();
    }

    /**
     * Format the email message and replace placeholders with real values
     */
    public function parse_placeholders($content)
    {
        $search = apply_filters('ppress_welcome_message_placeholder_search', [
            '{{username}}',
            '{{password}}',
            '{{email}}',
            '{{site_title}}',
            '{{first_name}}',
            '{{last_name}}',
            '{{password_reset_link}}',
            '{{login_link}}'
        ]);

        $replace = apply_filters('ppress_welcome_message_placeholder_replace', [
            $this->wp_user->user_login,
            empty($this->password) ? esc_html__('[Your Password]', 'wp-user-avatar') : $this->password,
            $this->wp_user->email,
            ppress_site_title(),
            $this->wp_user->first_name,
            $this->wp_user->last_name,
            ppress_generate_password_reset_url($this->wp_user->user_login),
            ppress_login_url()
        ]);

        return str_replace($search, $replace, $content);
    }

    public function send_welcome_mail()
    {
        $subject = ppress_get_setting('welcome_message_email_subject', sprintf(esc_html__('Welcome To %s', 'wp-user-avatar'), ppress_site_title()), true);
        $subject = $this->parse_placeholders($subject);

        $message = apply_filters(
            'ppress_welcome_message_raw_content',
            ppress_get_setting('welcome_message_email_content', ppress_welcome_msg_content_default(), true)
        );

        $message = $this->parse_placeholders($message);

        $a = ppress_send_email($this->wp_user->user_email, $subject, $message);
    }
}
