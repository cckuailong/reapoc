<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <style type="text/css">
        html, body {
            padding: 0;
            margin: 0;
            background: #F5F5F5;
        }

        body {
            padding: 25px;
        }

        .dlm-downloads-table {
            width: 100%;
        }

        .dlm-downloads-table th, .dlm-downloads-table td {
            padding: 1.5em 0;
            text-align: left;
        }

        .dlm-downloads-table th {
            padding: .5em 0;
            border-bottom: 2px solid #c3c1bc;
        }

        .dlm-downloads-table td {
            border-bottom: 1px solid #c3c1bc;
        }

        .dlm-download-button {
            display: inline-block;
            padding: 1em 1.5em;
            background-color: #eee;
            border-color: #eee;
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .dlm-th-name {
            width: 45%;
        }

        .dlm-downloads-table td.dlm-td-download-button {
            text-align: center;
        }
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="500"
       style="font-family: Arial; font-size: 14px;">
    <tr>
        <td style="font-size: 16px; font-weight: bold; background-color: #459ac9; color: #fff; height: 50px; padding: 0 15px;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;border-top-left-radius: 5px;border-top-right-radius: 5px;">
            Thanks for your order!
        </td>
    </tr>
    <tr>
        <td style="padding: 25px 15px;background: #fff;-webkit-border-bottom-right-radius: 5px;-webkit-border-bottom-left-radius: 5px;-moz-border-radius-bottomright: 5px;-moz-border-radius-bottomleft: 5px;border-bottom-right-radius: 5px;border-bottom-left-radius: 5px;">
            Hey %FIRST_NAME%,<br/>
            <br/>
            Thank you for your purchase, this email confirms your order.<br/>
            <br/>
            Here's an overview of your purchase:<br/>
            <br/>
            %DOWNLOADS_TABLE%
            <br/>
            Many thanks,<br/>
            </br>
            <em>Team %WEBSITE_NAME%</em>
        </td>
    </tr>
</table>
</body>
</html>