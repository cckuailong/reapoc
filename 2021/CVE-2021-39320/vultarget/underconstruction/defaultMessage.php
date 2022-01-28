<?php 
/*
 This file is part of underConstruction.
 underConstruction is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 underConstruction is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with underConstruction.  If not, see <http://www.gnu.org/licenses/>.
 */

function displayDefaultComingSoonPage()
{
    displayComingSoonPage(trim(get_bloginfo('title')).' is coming soon', get_bloginfo('url'), 'is coming soon');
}

function displayComingSoonPage($title, $headerText, $bodyText)
{
    
?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>
            <?php echo $title; ?>
        </title>
        <style type="text/css">
            
            .headerText {
                width: 550px;
                margin-top: 10%;
                margin-right: auto;
                margin-left: auto;
                font-size: 28px;
                font-weight: normal;
                display: block;
                text-align: center;
            }
            
            .bodyText {
                width: 550px;
                margin-top: 15px;
                margin-right: auto;
                margin-left: auto;
                font-size: 14px;
                font-weight: normal;
                display: block;
                text-align: center;
            }
            
            body {
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
                background-color: #222222;
                color: #FFF;
                font-family: Arial, Helvetica, sans-serif;
            }
        </style>
    </head>
    <body>
        <span class="headerText">
            <?php echo $headerText; ?>
        </span>
        <br/>
        <span class="bodyText">
            <?php echo $bodyText; ?>
        </span>
    </body>
</html>
<?php 
}
/* EOF */
?>
