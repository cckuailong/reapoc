<?php

namespace ProfilePress\Core\Themes\Shortcode\Login;


use ProfilePress\Core\Themes\Shortcode\LoginThemeInterface;

class Fzbuk implements LoginThemeInterface
{
    public function get_name()
    {
        return 'Fzbuk';
    }

    public function get_structure()
    {
        return <<<CODE
<div class="fzbuk-login-form-wrap">

	<h1>Sign In</h1>

	<div class="fzbuk-login-form">

		<label>
			[login-username placeholder="Username" id="fzbuk-username"]
		</label>

		<label>
			[login-password placeholder="Password" id="fzbuk-password"]
		</label>

		[login-submit value="Login"]

	</div>
	<h5>[link-lost-password class="lostp" label="Forgot Password?"]</h5>
</div>
CODE;

    }

    public function get_css()
    {
        return <<<CSS
@import url(https://fonts.googleapis.com/css?family=Lato:300,400,600,700&display=swap);

/*  css class for the form generated errors */
.profilepress-login-status {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  color: #fff;
  background: #5170ad;
  background: -moz-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, #5170ad), color-stop(100%, #355493));
  background: -webkit-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: -o-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: -ms-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: radial-gradient(ellipse at center, #5170ad 0%, #355493 100%);
  border: 1px solid #2d416d;
  box-shadow: 0 1px #5670A4 inset, 0 0 10px 5px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  position: relative;
  max-width: 360px;
  width: 100%;
  text-align: center;
  margin: 10px auto;
  padding: 10px;
}

.profilepress-login-status a {
color: #ea9629 !important;
font-weight:bold;
}

.fzbuk-login-form label {
  display: block !important;
  margin: 0 !important;
}

.fzbuk-login-form-wrap {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  font-family: "Lato", sans-serif;
  background: #5170ad;
  background: -moz-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, #5170ad), color-stop(100%, #355493));
  background: -webkit-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: -o-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: -ms-radial-gradient(center, ellipse cover, #5170ad 0%, #355493 100%);
  background: radial-gradient(ellipse at center, #5170ad 0%, #355493 100%);
  border: 1px solid #2d416d;
  box-shadow: 0 1px #5670A4 inset, 0 0 10px 5px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  position: relative;
  max-width: 360px;
  width: 100%;
  margin: 10px auto;
  padding: 50px 30px 0 30px;
  text-align: center;
}
.fzbuk-login-form-wrap:before {
  display: block;
  content: '';
  width: 58px;
  height: 19px;
  top: 10px;
  left: 10px;
  position: absolute;
}
.fzbuk-login-form-wrap > h1 {
  margin: 0 0 50px 0;
  padding: 0;
  font-size: 26px;
  color: #fff;
}
.fzbuk-login-form-wrap > h5 {
  color: #303030;
  margin-top: 40px;
}
.fzbuk-login-form-wrap > h5 > a {
  font-size: 14px;
  color: #fff;
  text-decoration: none;
  font-weight: 400;
}

.fzbuk-login-form input[type="text"], .fzbuk-login-form input[type="password"] {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  width: 100% !important;
  border: 1px solid #314d89;
  outline: none !important;
  padding: 12px 20px !important;
  color: #afafaf !important;
  font-weight: 400 !important;
  font-family: "Lato", sans-serif !important;
  cursor: text !important;
}
.fzbuk-login-form input[type="text"] {
  border-bottom: none;
  border-radius: 4px 4px 0 0;
  padding-bottom: 13px;
  box-shadow: 0 -1px 0 #E0E0E0 inset, 0 1px 2px rgba(0, 0, 0, 0.23) inset;
}
.fzbuk-login-form input[type="password"] {
  border-top: none;
  border-radius: 0 0 4px 4px;
  box-shadow: 0 -1px 2px rgba(0, 0, 0, 0.23) inset, 0 1px 2px rgba(255, 255, 255, 0.1);
}
.fzbuk-login-form input[type="submit"] {
  font-family: "Lato", sans-serif;
  background: #e0e0e0;
  background: -moz-linear-gradient(top, #e0e0e0 0%, #cecece 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #e0e0e0), color-stop(100%, #cecece));
  background: -webkit-linear-gradient(top, #e0e0e0 0%, #cecece 100%);
  background: -o-linear-gradient(top, #e0e0e0 0%, #cecece 100%);
  background: -ms-linear-gradient(top, #e0e0e0 0%, #cecece 100%);
  background: linear-gradient(to bottom, #e0e0e0 0%, #cecece 100%);
  display: block;
  margin: 20px auto 0 auto;
  width: 100%;
  border: none;
  border-radius: 3px;
  padding: 8px;
  font-size: 16px;
  color: #636363;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.45);
  font-weight: 700;
  box-shadow: 0 1px 3px 1px rgba(0, 0, 0, 0.17), 0 1px 0 rgba(255, 255, 255, 0.36) inset;
}
.fzbuk-login-form input[type="submit"]:hover {
  background: #DDD;
}
.fzbuk-login-form input[type="submit"]:active {
  padding-top: 9px;
  padding-bottom: 7px;
  background: #C9C9C9;
}

::-webkit-input-placeholder {
    color:    #999;
}
:-moz-placeholder {
    color:    #999;
}
::-moz-placeholder {
    color:    #999;
}
:-ms-input-placeholder {
    color:    #999;
}
CSS;

    }
}