<?php

namespace ProfilePress\Core\Themes\Shortcode\Userprofile;


use ProfilePress\Core\Themes\Shortcode\ThemeInterface;

class Dixon implements ThemeInterface
{
    public function get_name()
    {
        return 'Dixon';
    }

    public function get_structure()
    {
        $asset_url = PPRESS_ASSETS_URL . '/images/dixon-fe-profile';

        return <<<CODE
<div class="dixon-wraper">
    <div class="dixon-header">
        <div class="dixon-container">
            <div class="dixon-img">
			<div class="dixon-avatar">
                <img src="[profile-avatar-url]" />
				</div>
            </div>
            <div class="dixon-text"> <span>[profile-username] </span>
                <br />Profile</div>
            <div class="dixon-icon">
                <ul>
                    <li> <a href="[profile-cpf key=facebook]"><img src="{$asset_url}/facebook.png" /> </a>
                    </li>
                    <li> <a href="[profile-cpf key=twitter]"><img src="{$asset_url}/twitter.png" /> </a>
                    </li>
                    <li> <a href="[profile-cpf key=linkedin]"><img src="{$asset_url}/linkedin.png"/> </a>
                    </li>
                </ul>
                </div>
        </div>
    </div>
    <div class="dixon-container">
        <div style="text-align: center;">
            <h3>  PROFILE DETAILS </h3>
        </div>
        <div class="dixon-section">
            <div class="dixon-section-name">
                <span>First name</span>
            </div>
            <div class="dixon-section-address">
                <span>[profile-first-name]</span>
            </div>
            <div class="dixon-section-name">
                <span>Last name</span>
            </div>
            <div class="dixon-section-address">
                <span>[profile-last-name]</span>
            </div>
            <div class="dixon-section-name">
                <span>Nickname</span>
            </div>
            <div class="dixon-section-address">
                <span>[profile-nickname]</span>
            </div>
            <div class="dixon-section-name">
                <span>Gender</span>
            </div>
            <div class="dixon-section-address">
                <span>Male</span>
            </div>
            <div class="dixon-section-name">
                <span>Website</span>
            </div>
            <div class="dixon-section-address">
                <span><a href="[profile-website]">[profile-website]</a></span>
            </div>
            <div class="dixon-section-name">
                <span>Bio</span>
            </div>
            <div class="dixon-section-address">
                <span>[profile-bio]</span>
            </div>
        </div>
    </div>
</div>
CODE;

    }

    public function get_css()
    {
        $asset_url = PPRESS_ASSETS_URL . '/images/dixon-fe-profile';

        return <<<CSS
@import url(https://fonts.googleapis.com/css?family=Lato:300,400,600,700|Raleway:300,400,600,700&display=swap);

.dixon-wraper {
    width: 100%;
    max-width: 800px;
	background:#ececec;
}

.dixon-wraper p {
    margin: 0 0 5px 0 !important;
    padding: 0 !important;
}

.dixon-section-name {
    float:left;
    color:#222222;
}
.dixon-section-address {
    margin-left:30%;
    color: #6b6969;
}
.dixon-container {
    margin:auto;
    width:100%;
	padding-bottom: 10px;
}
.dixon-section-name > span, .dixon-section-address > span {
    padding-bottom:20px;
	margin:0;
  	display: block;
}

.dixon-section-name > a, .dixon-section-address > a {
    text-decoration:none;
}

.dixon-container h3 {
    margin:30px 0;
    font-family:Lato, sans-serif;
    color:#196783;
    font-size:28px;
}
.dixon-header {
    background:url({$asset_url}/bg.jpg);
    width:100%;
    height:60%;
    border-bottom:3px solid #a7bdc8;
	padding-bottom: 5px;
}
.dixon-aside {
    margin-top:30px;
}
.dixon-section {
    background:#fff;
    padding:40px 20px 10px;
    border-top:4px solid red;
	margin: 0 10px;
}
.dixon-img {
    padding-top:40px;
    position:relative;
    float:left;
	width: 50%;
}
.dixon-icon > ul > li img {
    box-shadow: none;
}

.dixon-text {
    text-align:center;
    font-size:30px;
    color:#fff;
    padding-top:50px;
    line-height:40px;
    font-family:Raleway, Sans-serif;
    text-transform:uppercase;
}


.dixon-icon ul li::before {
    content: none;
}

.dixon-icon li {
    display:inline-block;
    text-align:center;
}
.dixon-icon {
    text-align:center;
    margin-top:15px;
}
.dixon-text span {
    border-bottom:1px solid #52edc7;
    padding-bottom:4px;
}
.dixon-aside img {
    width:190px;
    margin-top:-30px;
}
.dixon-center-text {
    color:#6b6969;
}
.dixon-section-address span a {
    color:#24758e;
}
.dixon-light {
    margin-left:10px;
}
.dixon-img-left {
    margin-left:5px;
}
.dixon-aside-text {
    border-bottom: 1px solid #949798;
    border-top: 1px solid #949798;
    padding:25px 0;
    margin-top: 50px;
}

.dixon-avatar > img {
    border: 3px solid #ffffff;
    border-radius: 50%;
    margin: 10px;
    padding: 4px;
    width: 70%;
    height: auto;
    float: left;
}

CSS;

    }
}