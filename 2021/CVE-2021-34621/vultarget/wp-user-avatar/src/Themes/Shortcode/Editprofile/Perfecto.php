<?php

namespace ProfilePress\Core\Themes\Shortcode\Editprofile;

use ProfilePress\Core\Themes\Shortcode\EditProfileThemeInterface;

class Perfecto implements EditProfileThemeInterface
{
    public function get_name()
    {
        return 'Perfecto';
    }

    public function get_structure()
    {
        return <<<CODE
<div class="perfecto">
    <div class="perfecto-heading">Create an Account</div>
        [edit-profile-username class="perfecto-input" placeholder="Username"]
        [edit-profile-password class="perfecto-input" placeholder="Password"]
        [edit-profile-email class="perfecto-input" placeholder="Email"]
        [edit-profile-website class="perfecto-input" placeholder="Website"]
        [edit-profile-nickname class="perfecto-input" placeholder="Nickname"]
        [edit-profile-first-name class="perfecto-input" placeholder="First Name" required]
        [edit-profile-last-name class="perfecto-input" placeholder="Last Name" required]
        [edit-profile-submit class="perfecto-submit" value="Save Changes"]
</div>
CODE;
    }

    public function success_message()
    {
        return '<div class="profilepress-edit-profile-status">Changes saved</div>';
    }

    public function get_css()
    {
        return <<<CSS
/*  css class for the form generated errors */
.profilepress-edit-profile-status {
    background-color: #484c51;
    color: #ffffff;
    border-radius: 2px;
    font-size: 16px;
    font-weight: normal;
    line-height: 1.4;
    text-align: center;
    padding: 8px 5px;
    max-width: 400px;
    margin: 5px auto;
}

.perfecto, 
.perfecto * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.perfecto {
    margin: 5px auto;
    border: 2px solid #f0f0f0;
    background: #fff;
    max-width: 400px;
    padding: 30px;
    
    font-family: helvetica, arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-font-smoothing: antialiased;
    font-smoothing: antialiased;
    font-size: 14px;
    line-height: 24px;
}

.perfecto .perfecto-heading {
    font-size: 24px;
    line-height: 34px;
    margin-bottom: 20px;
    display: block;
    font-weight: 100;
    color: #555;
    text-align: center;
}

.perfecto input.perfecto-input {
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.125);
    
    padding: 12px 18px;
    width: 100%;
    box-sizing: border-box;
    outline: none;
    color: #555;
    font-size: 12px;
    line-height: 22px;
    font-family: helvetica, arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-font-smoothing: antialiased;
    font-smoothing: antialiased;
    border: 1px solid #ddd;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    margin-bottom: 10px;
    -webkit-transition: 0.4s;
    -moz-transition: 0.4s;
    transition: 0.4s;
}

.perfecto input.perfecto-input:disabled {
  background: #f4f4f4;
}

.perfecto input.perfecto-input:focus {
    border-color: #ccc;
    background: #fafafa;
    -webkit-box-shadow: inset 0px 1px 5px 0px #f0f0f0;
    -moz-box-shadow: inset 0px 1px 5px 0px #f0f0f0;
    box-shadow: inset 0px 1px 5px 0px #f0f0f0;
}

.perfecto input.perfecto-submit {
    padding: 12px 10px;
    width: 100%;
    box-sizing: border-box;
    border: 0;
    outline: none;
    margin-top: 20px;
    color: #fff;
    cursor: pointer;
    background: #196cd8;
    font-size: 12px;
    line-height: 21px;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-weight: bold;
    font-family: helvetica, arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-font-smoothing: antialiased;
    font-smoothing: antialiased;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    margin-bottom: 10px;
    -webkit-transition: 0.4s;
    -moz-transition: 0.4s;
    transition: 0.4s;
    text-shadow: none;
    box-shadow: none;
}

.perfecto input.perfecto-submit:hover {
    background: #155bb5;
}
CSS;

    }
}