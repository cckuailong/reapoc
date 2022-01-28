<?php

namespace ProfilePress\Core\Classes;

class FormShortcodeDefaults
{
    protected $form_type;

    public function __construct($form_type)
    {
        $this->form_type = str_replace('-', '_', $form_type);
    }

    public function get_structure()
    {
        $method = $this->form_type . '_structure';

        if ( ! method_exists($this, $method)) {
            return '';
        }

        return $this->$method();
    }

    public function get_css()
    {
        $method = $this->form_type . '_css';

        if ( ! method_exists($this, $method)) {
            return '';
        }

        return $this->$method();
    }

    public function success_message()
    {
        $method = $this->form_type . '_success_message';

        if ( ! method_exists($this, $method)) {
            return '';
        }

        return $this->$method();
    }

    public function user_profile_structure()
    {
        return <<<HTML
<h1>[profile-username]'s Profile</h1>

<div class="profile-avatar">
    <img class="avatar" src="[profile-avatar-url]">
</div>

<div class="profile-detail">

    <p>Username: [profile-username]</p>

    <p>Email: [profile-email]</p>

    <p>Website: [profile-website]</p>

    <p>First Name: [profile-first-name]</p>

    <p>Last Name: [profile-last-name]</p>

    <p>Gender: [profile-cpf key="gender"]</p>
</div>
HTML;

    }

    public function login_structure()
    {
        return <<<HTML
<div class="login-form">

    <p>[login-username placeholder="Username" class="login-name"]</p>

    <p>[login-password placeholder="Password" class="login-passkey"]</p>

    <p>[login-remember class="remember-me"] Remember Me</p>

    <p>[login-submit value="Sign In" class="login-name"]</p>

    [link-registration class="reg" label="Register Now"] | [link-lost-password class="lostp" label="Forgot Password?"]

</div>
HTML;

    }

    public function login_css()
    {
        return <<<CSS
/* css class for the login generated errors */

.profilepress-login-status {
    background-color: #34495e;
    color: #ffffff;
    border: medium none;
    border-radius: 4px;
    font-size: 15px;
    font-weight: normal;
    line-height: 1.4;
    padding: 8px 5px;
}

.profilepress-login-status a {
    color: #ea9629 !important;
}
CSS;

    }

    public function registration_structure()
    {
        return <<<HTML
<div class="reg-form">

    <p>
        <label for="id-username">Username</label>
        [reg-username id="id-username" placeholder="Username" class="registration-name"]
    </p>

    <p>
        <label for="id-password">Password</label>
        [reg-password id="id-password" placeholder="Password" class="registration-passkey"]
    </p>

    <p>
        <label for="id-email">Email Address</label>
        [reg-email id="id-email" placeholder="Email" class="reg-email"]
    </p>

    <p>
        <label for="id-website">Website</label>
        [reg-website class="reg-website" placeholder="Website" id="website-id" required]
    </p>

    <p>
        <label for="id-nickname">Nickname</label>
        [reg-nickname class="nickname" placeholder="Nickname" id="nickname-id"]
    </p>

    <p>
        <label for="id-firstname">First Name</label>
        [reg-first-name class="firstname" id="firstname-id" placeholder="First Name"]
    </p>

    <p>
        <label for="id-lastname">Last Name</label>
        [reg-last-name class="remember-me" id="lastname-id" placeholder="Last Name" required]
    </p>

    <p>
        [reg-submit value="Register" class="submit" id="submit-button"]
    </p>

    <p>
        Have an account? [link-login label="Login"]
    </p>

</div>
HTML;

    }

    public function registration_css()
    {
        return <<<CSS
/* css class for the registration form generated errors */

.profilepress-reg-status {
  border-radius: 6px;
  font-size: 17px;
  line-height: 1.471;
  padding: 10px 19px;
  background-color: #e74c3c;
  color: #ffffff;
  font-weight: normal;
  display: block;
  text-align: center;
  vertical-align: middle;
  margin: 5px 0;
}
CSS;

    }

    public function registration_success_message()
    {
        return '<div>Registration successful.</div>';
    }

    public function edit_profile_structure()
    {
        return <<<HTML
<div class="edit-profile">

    <p>
        <label for="id-username">Username</label>
        [edit-profile-username id="id-username" placeholder="username" class="edit-profile-name"]
    </p>

    <p>
        <label for="id-password">Password</label>
        [edit-profile-password id="id-password" placeholder="password" class="edit-profile-passkey"]
    </p>

    <p>
        <label for="id-email">Email Address</label>
        [edit-profile-email id="id-email" placeholder="Email" class="reg-email"]
    </p>

    <p>
        <label for="id-website">Website</label>
        [edit-profile-website class="reg-website" placeholder="Website" id="id-website"]
    </p>

    <p>
        <label for="id-nickname">Nickname</label>
        [edit-profile-nickname class="remember-me" placeholder="Nickname" id="id-nickname"]
    </p>

    <p>
        [pp-user-avatar class="user-avatar"]
    </p>
    [pp-remove-avatar-button label="Remove" class="removed"]

    <p>
        <label for="id-nickname">Profile Picture</label>
        [edit-profile-avatar class="avatar" placeholder="avatar" id="id-avatar"]
    </p>

    <p>
        <label for="id-display-name">Display Name</label>
        [edit-profile-display-name class="display-name" placeholder="Display Name" id="id-display-name"]
    </p>

    <p>
        <label for="id-firstname">First Name</label>
        [edit-profile-first-name class="remember-me" id="id-firstname"  placeholder="First Name"]
    </p>

    <p>
        <label for="id-lastname">Last Name</label>
        [edit-profile-last-name class="remember-me" id="id-lastname" placeholder="Last Name"]
    </p>

    <p>
        [edit-profile-submit value="Edit Profile" class="submit" id="submit-button"]
    </p>

</div>
HTML;

    }

    public function edit_profile_css()
    {
        return <<<CSS
/* css class for the edit profile generated errors */

.profilepress-edit-profile-status {
    background-color: #34495e;
    color: #ffffff;
    border: medium none;
    border-radius: 4px;
    font-size: 15px;
    line-height: 1.4;
    padding: 8px 5px;
  	font-weight: bold;
    margin:4px 1px;
}
CSS;

    }

    public function edit_profile_success_message()
    {
        return '<div>Changes saved.</div>';
    }

    public function password_reset_structure()
    {
        return <<<HTML
<div>

    <p>Please enter your username or email address.</p>
    <p>You will receive a link to create a new password via email.</p>

    <p>[user-login id="login" placeholder="Username or E-mail:" class="user-login"]</p>

    <p>[reset-submit value="Reset Password" class="submit" id="submit-button"]</p>

</div>
HTML;

    }

    public function password_reset_css()
    {
        return <<<CSS
/* css class for the password reset form generated errors */

.profilepress-reset-status {
    background-color: #27CCC0;
    color: #fff;
    padding: 1em 2em 1em 3.5em;
    margin: 0 0 2em;
}
CSS;

    }

    public function password_reset_handler()
    {
        return <<<HTML
<div class="pp-reset-password-form">
	<h3>Enter your new password below</h3>
	<label for="password1">New password<span class="req">*</span></label>
	[enter-password id="password1" required autocomplete="off"]

	<label for="password2">Re-enter new password<span class="req">*</span></label>
	[re-enter-password id="password2" required autocomplete="off"]

	[password-reset-submit class="pp-reset-button pp-reset-button-block" value="Save"]
</div>
HTML;

    }

    public function password_reset_success_message()
    {
        return '<div>Check your email for further instruction</div>';
    }
}