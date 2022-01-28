<?php

namespace ProfilePress\Core\Themes\Shortcode\Melange;

use ProfilePress\Core\Themes\Shortcode\MelangeThemeInterface;

class Lucid implements MelangeThemeInterface
{
    public function get_name()
    {
        return 'Lucid Account Tab Widget';
    }

    public function get_structure()
    {
        return <<<CODE
<div class="lucidContainer">
	<div class="multitab-section">
		<ul class="multitab-widget multitab-widget-content-tabs-id">
			<li class="multitab-tab"><a href="#lucidLogin">Log In</a></li>
			<li class="multitab-tab"><a href="#lucidRegistration">Register</a></li>
			<li class="multitab-tab"><a href="#lucidReset">Reset</a></li>
		</ul>
		<div class="multitab-widget-content multitab-widget-content-widget-id" id="lucidLogin">
      <span id="sidebartab1">
        [pp-login-form]
		[login-username placeholder="Username"]
		[login-password placeholder="Password"]
		[login-submit value="Log In"]
		[/pp-login-form]
      </span>
		</div>
		<div class="multitab-widget-content multitab-widget-content-widget-id" id="lucidRegistration">
      <span id="sidebartab2">
		[pp-registration-form]
		[reg-username placeholder="Username"]
		[reg-email placeholder="Email Address"]
		[reg-password placeholder="Password"]
		[reg-submit value="Register"]
		[/pp-registration-form]
      </span>
		</div>
		<div class="multitab-widget-content multitab-widget-content-widget-id" id="lucidReset">
      <span id="sidebartab3">
        [pp-password-reset-form]
		[user-login value="Username or Email"]
		[reset-submit value="Get New Password"]
		[/pp-password-reset-form]
      </span>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$(".multitab-widget-content-widget-id").hide();
		$("ul.multitab-widget-content-tabs-id li:first a").addClass("multitab-widget-current").show();
		$(".multitab-widget-content-widget-id:first").show();
		$("ul.multitab-widget-content-tabs-id li a").click(function () {
			$("ul.multitab-widget-content-tabs-id li a").removeClass("multitab-widget-current a");
			$(this).addClass("multitab-widget-current");
			$(".multitab-widget-content-widget-id").hide();
			var activeTab = $(this).attr("href");
			$(activeTab).fadeIn();
			return false;
		});
	});
</script>
CODE;

    }

    public function get_css()
    {
        return <<<CSS
@import url(https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap);

/* css class for login form generated errors */
.profilepress-login-status {
  border-radius: 5px;
    max-width: 350px;
    font-size: 15px;
    line-height: 1.471;
    padding: 10px;
    background-color: #e74c3c;
    color: #ffffff;
    font-weight: normal;
    display: block;
    vertical-align: middle;
    margin: 5px 0;
}

.profilepress-login-status a {
    color: #fff;
    text-decoration: underline;
}

/* css class for registration form generated errors */
.profilepress-reg-status {
  border-radius: 5px;
    max-width: 350px;
    font-size: 15px;
    line-height: 1.471;
    padding: 10px;
    background-color: #e74c3c;
    color: #ffffff;
    font-weight: normal;
    display: block;
    vertical-align: middle;
    margin: 5px 0;
}

.profilepress-reset-status a {
    color: #fff;
    text-decoration: underline;
}

/* css class for password reset form generated errors */
.profilepress-reset-status {
  border-radius: 5px;
    max-width: 350px;
    font-size: 15px;
    line-height: 1.471;
    padding: 10px;
    background-color: #e74c3c;
    color: #ffffff;
    font-weight: normal;
    display: block;
    vertical-align: middle;
    margin: 5px 0;
}

.profilepress-reset-status a {
    color: #fff;
    text-decoration: underline;
}

.lucidSuccess {
  border-radius: 5px;
  max-width: 350px;
  font-size: 15px;
  line-height: 1.471;
  padding: 10px;
  background-color: #2ecc71;
  color: #ffffff;
  font-weight: normal;
  display: block;
  margin-top: 5px;
  margin-bottom: 5px;
}

.lucidContainer {
  max-width: 350px;
  margin: 30px 0;
  padding: 0;
  box-shadow: 0 10px 5px -5px rgba(0, 0, 0, 0.1);
  font-family: 'Open Sans', sans-serif;
}

/* Multi Tab Sidebar */

.multitab-section {
  display: inline-block;
  text-transform: uppercase;
  width: 100%;
}

.multitab-section p {
  display: inline-block;
  background: #fff;
  text-transform: lowercase;
  font-size: 14px;
  padding: 20px;
  margin: 0;
}

.multitab-widget {
  list-style: none;
  margin: 0 0 10px;
  padding: 0
}

.multitab-widget li {
  list-style: none;
  padding: 0;
  margin: 0;
  float: left
}

.multitab-widget li a {
  background: #22a1c4;
  color: #fff;
  display: block;
  padding: 15px;
  font-size: 13px;
  text-decoration: none
}

.multitab-tab {
  width: 33.3%;
  text-align: center
}

.multitab-section h2,
.multitab-section h3,
.multitab-section h4,
.multitab-section h5,
.multitab-section h6 {
  display: none;
}

.multitab-widget li a.multitab-widget-current {
  padding-bottom: 20px;
  margin-top: -10px;
  background: #fff;
  color: #444;
  text-decoration: none;
  border-top: 5px solid #22a1c4;
  font-size: 14px;
  text-transform: capitalize
}

.multitab-widget-content {
  padding: 0 20px;
  border-bottom: 1px solid #efefef;
  border-left: 1px solid #efefef;
  border-right: 1px solid #efefef;
}


div.lucidContainer input[type="email"],
div.lucidContainer input[type="text"],
div.lucidContainer input[type="password"], div.lucidContainer select, div.lucidContainer textarea {
  width: 100%;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.125);
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  background: #fff;
  margin: 10px auto;
  border: 1px solid #ccc;
  padding: 10px;
  font-family: 'Open Sans', sans-serif;
  font-size: 95%;
  color: #555;
}


div.lucidContainer input[type="submit"] {
  width: 100%;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  margin: 10px auto;
  background: #3399cc;
  border: 0;
  padding: 4%;
  font-family: 'Open Sans', sans-serif;
  font-size: 100%;
  color: #fff;
  cursor: pointer;
  transition: background .3s;
  -webkit-transition: background .3s;
}

div.lucidContainer input[type="submit"]:hover {
  background: #2288bb;
}


CSS;

    }

    public function registration_success_message()
    {
        return '<div class="lucidSuccess">Registration Successful.</div>';
    }

    public function password_reset_success_message()
    {
        return '<div class="lucidSuccess">Check your email for further instruction.</div>';
    }

    public function edit_profile_success_message()
    {
        return '<div class="lucidSuccess">Profile successfully updated.</div>';
    }
}