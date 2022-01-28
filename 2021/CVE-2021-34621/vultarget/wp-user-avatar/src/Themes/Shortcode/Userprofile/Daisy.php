<?php

namespace ProfilePress\Core\Themes\Shortcode\Userprofile;


use ProfilePress\Core\Themes\Shortcode\ThemeInterface;

class Daisy implements ThemeInterface
{
    public function get_name()
    {
        return 'Daisy';
    }

    public function get_structure()
    {
        return <<<CODE
<div class="daisy-container">
    <div class="daisy-small-col-4">
        <div class="daisy-profile-image">
            <img src="[profile-avatar-url size=200]"/>
        </div>

        <div class="daisy-user-details">
            <div class="daisy-user-name">[profile-first-name] [profile-last-name]</div>
            <div class="daisy-user-url">[profile-website]</div>

            <div class="daisy-user-url">Member since: [profile-date-registered]</div>


            <div class="daisy-activity">
                <a href="mailto:[profile-email]"><span class="daisy-activity-button">MESSAGE<i class="fa fa-envelope"></i></span></a>
            </div>

            <div class="daisy-more-details">
                <div class="daisy-more-title"><i class="fa fa-edit"></i>Posts
                    <span class="daisy-more-count"> ([profile-post-count]) </span></div>
                <div class="daisy-more-title"><i class="fa fa-comments"></i>Comments
                    <span class="daisy-more-count"> ([profile-comment-count]) </span></div>

            </div>

        </div>

    </div>

    <div class="daisy-small-col-8">

        <div class="daisy-user-detail">
            <div class="daisy-details-header">Profile Information</div>

            <span class="daisy-user-header">Username</span>
            <span class="daisy-user-header-info">[profile-username]</span>

            <span class="daisy-user-header">Email Address</span>
            <span class="daisy-user-header-info">[profile-email]</span>

            <span class="daisy-user-header">First name</span>
            <span class="daisy-user-header-info">[profile-first-name]</span>

            <span class="daisy-user-header">Last name</span>
            <span class="daisy-user-header-info">[profile-last-name]</span>

            <span class="daisy-user-header">Nickname</span>
            <span class="daisy-user-header-info">[profile-nickname]</span>

            <span class="daisy-user-header">Website</span>
            <span class="daisy-user-header-info daisy-site-url">[profile-website]</span>

            <span class="daisy-user-header">Gender</span>
            <span class="daisy-user-header-info">[profile-cpf key=gender]</span>

            <span class="daisy-user-header">Biography</span>
            <span class="daisy-user-header-info">[profile-bio]</span>
        </div>
    </div>
</div>
CODE;

    }

    public function get_css()
    {
        return <<<CSS
    @import url(https://fonts.googleapis.com/css?family=Roboto:400,500,700|raleway:700|Open+Sans:400,700);
    @import url(https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css);

    .daisy-container * {
        box-sizing: border-box;
    }

    .daisy-details-header {
        font-size: 24px;
        font-family: 'raleway', sans-serif;
        font-weight: 700;
        color: #444;
        padding-top: 20px;
    }

    .daisy-user-detail a {
        text-decoration: none !important;
        outline: none;
        box-shadow: none !important;
    }

    .daisy-user-detail li {
        list-style: none !important;
    }

    .daisy-user-header {
        display: block;
        margin: 0 0 8px;
        border-bottom: 2px solid #eee;
        padding-bottom: 4px;
        font: 15px roboto !important;
        font-weight: 500 !important;
        color: #555;
        padding-top: 15px;
    }

    .daisy-user-header-info.daisy-site-url {
        color: #3ba1da;
    }

    .daisy-user-detail li {
        list-style: none !important;
        font-family: roboto, sans-serif;
        font-size: 15px;
    }

    .daisy-small-col-4 {
        position: relative;
        float: left;
        width: 26%;
        margin-left: 20px;
        margin-right: 20px;
        border: 2px solid #eee;
    }

    .daisy-small-col-8 {
        position: relative;
        float: left;
        width: 58%;
        margin-left: 20px !important;
        margin-right: 20px !important;
        background: #FFF none repeat scroll 0 0;
        padding: 20px;
        border: 2px solid #eee;
    }

    .daisy-activity a {
        text-decoration: none;
    }

    .daisy-activity .fa {
        padding-left: 5px;
    }

    .daisy-user-name-in-bio {
        font-family: raleway, sans-serif;
        font-size: 24px;
        color: #444;
        border-bottom: 2px solid #ECF0F1;
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .daisy-more-details {
        list-style: none;
        font-family: roboto, sans-serif !important;
        line-height: 1.5;
        padding-top: 30px;
        color: #555;
    }

    .daisy-more-details .fa {
        padding-right: 5px;
    }

    .daisy-more-count {
        color: #16A085;
    }

    .daisy-container {
        margin-top: 10px;
        font-size: 16px;
    }

    .daisy-profile-image img {
        width: 100%;
        height: 250px;
    }

    .daisy-user-details {
        padding: 30px 20px;
    }

    .daisy-activity {
        padding-top: 10px;
    }

    .daisy-activity {
        margin-top: 20px;
        border-top: 2px solid #ECF0F1 !important;
        padding-top: 20px;
    }

    .daisy-activity-button {
        padding: 8px 10px;
        border: medium none !important;
        background: #16A085 none repeat scroll 0 0;
        color: #FFF !important;
        font-family: roboto, sans-serif !important;
        font-size: 13px;
        font-weight: 700;
    }

    .daisy-user-name {
        font-family: roboto, sans-serif;
        font-size: 24px;
        color: #555;
        font-weight: 700;
        padding-bottom: 5px;
    }

    .daisy-user-url {
        font-family: roboto, sans-serif !important;
        color: #ccc;
        font-weight: 400;
        font-size: 14px;
        padding-top: 5px;
    }

    .daisy-small-col-4 {
        background: white !important;
    }

    @media only screen and (max-width: 780px) {

        .daisy-small-col-4 {
            position: relative;
            float: none;
            width: 33% !important;
            margin: 0 auto !important;
            text-align: center;
            background: white;
        }

        .daisy-user-name {
            font-family: roboto, sans-serif;
            font-size: 18px;
            color: #555;
            font-weight: 700;
            padding-bottom: 5px;
        }

        .daisy-user-details {
            padding: 20px 20px;
        }

        .daisy-profile-image img {
            width: 100%;
            height: 200px;
        }

        .daisy-small-col-8 {
            position: relative;
            width: 98%;
            background: transparent none repeat scroll 0 0% !important;
            padding: 10px;
            text-align: center;
        }

    }

    @media only screen and (max-width: 980px) {

        .daisy-container {
            padding-left: 3%;
            padding-right: 3%;
        }

        .daisy-small-col-4 {
            position: relative;
            float: left;
            width: 22%;
            margin: 0 auto !important;
        }

        .daisy-small-col-8 {
            position: relative;
            float: left;
            width: 53%;
            margin-left: 20px !important;
            margin-right: 20px !important;
            background: #FFF !important;
            padding: 20px;
        }

        .daisy-activity-button {
            padding: 8px 5px;
            border: medium none !important;
            background: #16A085 none repeat scroll 0 0;
            color: #FFF !important;
            font-family: roboto, sans-serif !important;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .daisy-small-col-4 {
            position: relative;
            float: left;
            width: 35%;
            margin: 0 auto !important;
        }

    }

    @media only screen and (min-width: 900px) {

        .daisy-container {
            padding-top: 50px;
            padding-left: 50px;
            padding-right: 50px;
        }

        .daisy-activity-button {
            padding: 10px;
            border: medium none !important;
            background: #16A085 none repeat scroll 0 0;
            color: #FFF !important;
            font-family: roboto, sans-serif !important;
            font-size: 11px;
            font-weight: 700;
        }
    }

    @media only screen and (max-width: 660px) {
        .daisy-small-col-4 {
            position: relative;
            float: none;
            width: 100% !important;
            margin: 0 auto !important;
        }

        .daisy-small-col-8 {
            position: relative;
            float: left;
            width: 100%;
            margin-left: 0 !important;
            margin-right: 0 !important;
            background: none !important;
            padding: 0 !important;
        }

        .daisy-small-col-8 {
            position: relative;
            float: left;
            width: 100%;
            background: white !important;
            margin: 20px 0;
            padding: 4px 11px !important;
        }

        .daisy-user-name-in-bio {
            font-size: 20px !important;
            color: #555 !important;
        }

        .daisy-profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 100px;
        }

        .daisy-profile-image {
            padding-top: 20px;
        }

    }

    @media only screen and (min-width: 1200px) {

        .daisy-small-col-8 {
            position: relative;
            float: left;
            width: 58%;
            margin-left: 20px !important;
            margin-right: 20px !important;
            background: #FFF none repeat scroll 0 0;
            padding: 30px 0 30px 50px;
        }

        .daisy-small-col-4 {
            position: relative;
            float: left;
            width: 22%;
            margin-left: 20px;
            margin-right: 20px;
        }
    }
CSS;

    }
}