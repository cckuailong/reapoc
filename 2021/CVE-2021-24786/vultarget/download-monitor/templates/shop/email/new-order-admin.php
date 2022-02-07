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

        .dlm-order-table {
            width: 100%;
        }

        .dlm-order-table th, .dlm-order-table td {
            padding: 1em 0;
            border-bottom: 1px solid #c3c1bc;
            text-align: left;
        }
        .dlm-order-table th {
            width: 40%;
        }
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="500"
       style="font-family: Arial; font-size: 14px;">
    <tr>
        <td style="font-size: 16px; font-weight: bold; background-color: #459ac9; color: #fff; height: 50px; padding: 0 15px;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;border-top-left-radius: 5px;border-top-right-radius: 5px;">
            New %WEBSITE_NAME% order!
        </td>
    </tr>
    <tr>
        <td style="padding: 25px 15px;background: #fff;-webkit-border-bottom-right-radius: 5px;-webkit-border-bottom-left-radius: 5px;-moz-border-radius-bottomright: 5px;-moz-border-radius-bottomleft: 5px;border-bottom-right-radius: 5px;border-bottom-left-radius: 5px;">
            Great news! Your %WEBSITE_NAME% shop just received a new order!<br/>
            <br/>
            Here's an overview of the order:<br/>
            <br/>
            %ORDER_TABLE%
            <br/>
            Cheers,<br/>
            </br>
            <em>Your Download Monitor powered website</em>
        </td>
    </tr>
</table>
</body>
</html>