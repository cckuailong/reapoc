<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($title)?$title:'WordPress Download Manager'; ?></title>
    <?php wp_head(); ?>
    <style>
        body{
            background: transparent;
        }
        #wpdmlogin{
            border: 0 !important;background: transparent !important;box-shadow: none !important; margin: 0 !important;
        }
    </style>
</head>
<body>
<?php
the_post();
the_content();
?>
</body>
<?php wp_footer(); ?>
<?php if(is_user_logged_in() && isset($_GET['interim-login'])){ ?>
        <script>
            window.parent.document.getElementById('wp-auth-check-wrap').style.display = 'none';
        </script>
<?php } ?>
</html>
