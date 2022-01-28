<?php

namespace ProfilePress\Core\Themes\Shortcode\Editprofile;

use ProfilePress\Core\Themes\Shortcode\EditProfileThemeInterface;

class Boson implements EditProfileThemeInterface
{
    public function get_name()
    {
        return 'Boson';
    }

    public function success_message()
    {
        return '<div class="profilepress-edit-profile-status">Changes saved</div>';
    }

    public function get_structure()
    {
        return <<<CODE
    <div class="boson-container">
        <div class="edit-profile">
            <h1>Edit Profile</h1>
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

            <p class="avatar">
                [pp-user-avatar]
                [pp-remove-avatar-button label="Delete" class="removed"]
            </p>

            <p>
                <label for="id-avatar">Profile Picture</label>
                [edit-profile-avatar id="id-avatar"]
            </p>

            <p>
                [pp-user-cover-image] <br><br>
                [pp-remove-cover-image-button label="Delete" class="removed"]
            </p>
          
            <p>
                <label for="cover-image">Cover Image</label>
                [edit-profile-cover-image id="cover-image"]
            </p>

            <p>
                <label for="display-name">Display Name</label>
                [edit-profile-display-name class="display-name" placeholder="Display Name" id="display-name"]
            </p>

            <p>
                <label for="id-firstname">First Name</label>
                [edit-profile-first-name class="remember-me" id="id-firstname"  placeholder="First Name"]
            </p>

            <p>
                <label for="id-lastname">Last Name</label>
                [edit-profile-last-name class="remember-me" id="id-lastname" placeholder="Last Name"]
            </p>

            <p style="text-align:center">
                [edit-profile-submit value="Save Profile"]
            </p>

        </div>
    </div>
CODE;

    }

    public function get_css()
    {
        return <<<CSS
/*  css class for the form generated errors */
.profilepress-edit-profile-status {
	color: #555;
    font-size: 15px;
    font-weight: bold;
    margin: 10px;
    box-sizing: border-box;
    max-width: 350px;
    width: 100%;
    text-align: center;
}
/* Boson form CSS */

.boson-container {
    box-sizing: border-box;
    max-width: 400px;
    width: 100%;
}
.boson-container a {
    color: #527881;
    text-decoration: underline;
    text-align: center;
}
.boson-container .edit-profile {
    position: relative;
    padding: 20px 20px 20px;
    box-sizing: border-box;
    width: 100%;
    background: white;
    border-radius: 3px;
    -webkit-box-shadow: 0 0 200px rgba(255, 255, 255, 0.5), 0 1px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0 0 200px rgba(255, 255, 255, 0.5), 0 1px 2px rgba(0, 0, 0, 0.3);
}
.boson-container > .edit-profile h1 {
    margin: -20px -20px 21px;
    line-height: 40px;
    font-size: 15px;
    font-weight: bold;
    color: #555;
    text-align: center;
    text-shadow: 0 1px white;
    background: #f3f3f3;
    border-bottom: 1px solid #cfcfcf;
    border-radius: 3px 3px 0 0;
    background-image: -webkit-linear-gradient(top, whiteffd, #eef2f5);
    background-image: -moz-linear-gradient(top, whiteffd, #eef2f5);
    background-image: -o-linear-gradient(top, whiteffd, #eef2f5);
    background-image: linear-gradient(to bottom, whiteffd, #eef2f5);
    -webkit-box-shadow: 0 1px whitesmoke;
    box-shadow: 0 1px whitesmoke;
}
.boson-container > .edit-profile p {
    margin: 20px 0;
    font-size: 16px;
    color: #555;
}
.boson-container > .edit-profile input[type=text], .boson-container > .edit-profile input[type=email], .boson-container > .edit-profile input[type=password], .boson-container > .edit-profile select {
    width: 100%;
}

.boson-container > .edit-profile p.submit {
    text-align: center;
}
:-moz-placeholder {
    color: #c9c9c9 !important;
    font-size: 13px;
}
::-webkit-input-placeholder {
    color: #ccc;
    font-size: 13px;
}
.boson-container > .edit-profile input[type=text], .boson-container > .edit-profile input[type=email], .boson-container > .edit-profile input[type=password], .boson-container > .edit-profile select {
    margin: 5px;
    padding: 0 10px;
    height: 34px;
    color: #404040;
    background: white;
    border: 1px solid;
    border-color: #c4c4c4 #d1d1d1 #d4d4d4;
    border-radius: 2px;
    outline: 5px solid #eff4f7;
    -moz-outline-radius: 3px;
    -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.12);
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.12);
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
.boson-container > .edit-profile input[type=text]:focus, .boson-container > .edit-profile input[type=email]:focus, .boson-container > .edit-profile select:focus, .boson-container > .edit-profile input[type=password]:focus {
    border-color: #7dc9e2;
    outline-color: #dceefc;
    outline-offset: 0;
}

.boson-container > .edit-profile input[type=submit], .boson-container > .edit-profile button.removed {
    cursor: pointer;
    padding: 1px 18px;
    height: 30px;
    font-size: 12px;
    font-weight: bold;
    color: #527881;
    text-shadow: 0 1px #e3f1f1;
    background: #cde5ef;
    border: 1px solid;
    border-color: #b4ccce #b3c0c8 #9eb9c2;
    border-radius: 16px;
    outline: 0;
    -webkit-box-sizing: content-box;
    -moz-box-sizing: content-box;
    box-sizing: content-box;
    background-image: -webkit-linear-gradient(top, #edf5f8, #cde5ef);
    background-image: -moz-linear-gradient(top, #edf5f8, #cde5ef);
    background-image: -o-linear-gradient(top, #edf5f8, #cde5ef);
    background-image: linear-gradient(to bottom, #edf5f8, #cde5ef);
    -webkit-box-shadow: inset 0 1px white, 0 1px 2px rgba(0, 0, 0, 0.15);
    box-shadow: inset 0 1px white, 0 1px 2px rgba(0, 0, 0, 0.15);
}
.boson-container > .edit-profile input[type=submit]:active {
    background: #cde5ef;
    border-color: #9eb9c2 #b3c0c8 #b4ccce;
    -webkit-box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.2);
    box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.2);
}


.boson-container > .edit-profile button.removed {
    width: auto;
}

.boson-container > .edit-profile input[type=submit] {
    width: 80%;
}


.boson-container .avatar img {
    border-radius: 50%;
    display: block;
    height: 190px;
    margin: 0 auto 10px;
    padding: 2px;
    text-align: center;
    width: 190px;
}
CSS;

    }
}