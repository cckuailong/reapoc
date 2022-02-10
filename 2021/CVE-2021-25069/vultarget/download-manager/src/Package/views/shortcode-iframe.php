<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/16/18
 * Time: 12:33 AM
 */


$pid = wpdm_query_var('__wpdmxp');
//setup_postdata($post);
//$pack = new \WPDM\Package();
//$pack->Prepare(get_the_ID());

?>
<!DOCTYPE html>
<html style="background: transparent">
<head>
    <title>Download <?php the_title(); ?></title>
    <script>
        var wpdm_url = <?php echo  json_encode(WPDM()->wpdm_urls) ?>;
    </script>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/font-awesome/css/font-awesome.min.css" />
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.form.min.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/js/front.js"></script>
    <?php if((isset($pack->packageData['form_lock']) && $pack->packageData['form_lock'] == 1)  || $pack->packageData['base_price'] > 0) wp_head(); ?>
    <style>
        html, body{
            overflow: visible;
            height: 100%;
            width: 100%;
            padding: 0;
            margin: 0;
            font-family: 'Josefin Sans', sans-serif;
            font-weight: 300;
            font-size: 10pt;
        }
        h4.modal-title{
            font-family: 'Josefin Sans', sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555555;
            font-size: 11pt;
            display: inline-block;
        }

        .w3eden label{
            font-weight: 400;
        }
        img{
            max-width: 100%;
        }
        .modal-backdrop{
            background: rgba(0,0,0,0.5);
        }


        .modal.fade{
            opacity:1;
        }
        .modal.fade .modal-dialog {
            -webkit-transform: translate(0);
            -moz-transform: translate(0);
            transform: translate(0);
        }

        .modal {
            text-align: center;
            padding: 0!important;
        }

        .wpdm-social-lock.btn {
            display: block;
            width: 100%;
        }

        @media (min-width: 768px) {
            .modal:before {
                content: '';
                display: inline-block;
                height: 100%;
                vertical-align: middle;
                margin-right: -4px;
            }

            .modal-dialog {
                display: inline-block;
                text-align: left;
                vertical-align: middle;
            }

            .wpdm-social-lock.btn {
                display: inline-block;
                width: 47%;
            }
        }

        @-moz-keyframes spin {
            from { -moz-transform: rotate(0deg); }
            to { -moz-transform: rotate(360deg); }
        }
        @-webkit-keyframes spin {
            from { -webkit-transform: rotate(0deg); }
            to { -webkit-transform: rotate(360deg); }
        }
        @keyframes spin {
            from {transform:rotate(0deg);}
            to {transform:rotate(360deg);}
        }
        .spin{
            -webkit-animation-name: spin;
            -webkit-animation-duration: 2000ms;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-timing-function: linear;
            -moz-animation-name: spin;
            -moz-animation-duration: 2000ms;
            -moz-animation-iteration-count: infinite;
            -moz-animation-timing-function: linear;
            -ms-animation-name: spin;
            -ms-animation-duration: 2000ms;
            -ms-animation-iteration-count: infinite;
            -ms-animation-timing-function: linear;

            animation-name: spin;
            animation-duration: 2000ms;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
            display: inline-block;
        }


        .w3eden .card-default {
            border-radius: 3px;
            margin-top: 10px !important;
        }
        .w3eden .card-default:last-child{
            margin-bottom: 0 !important;
        }
        .w3eden .card-default .card-header{
            letter-spacing: 0.5px;
            font-weight: 600;
            background-color: #f6f8f9;
        }

        .w3eden .card-default .card-footer{
            background-color: #fafafa;
        }

        .btn{
            outline: none !important;
        }
        .w3eden .card{
            margin-bottom: 0;
        }
        .w3eden .modal-header{
            border: 0;
        }
        .w3eden .modal-content{
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
            border: 0;
            border-radius: 6px;
            background: rgb(255,255,255);
            background: -moz-linear-gradient(-45deg,  rgba(255,255,255,1) 0%, rgba(243,243,243,1) 50%, rgba(237,237,237,1) 51%, rgba(255,255,255,1) 100%);
            background: -webkit-linear-gradient(-45deg,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%);
            background: linear-gradient(135deg,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=1 );
            overflow: hidden;
            max-width: 100%;
        }
        .w3eden .modal-body{
            max-height:  calc(100vh - 210px);
            overflow-y: auto;
            padding-top: 0 !important;
        }


        .w3eden .input-group-lg .input-group-btn .btn{
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
        }
        .w3eden .wpforms-field-medium{
            max-width: 100% !important;
            width: 100% !important;
        }

        .w3eden .input-group.input-group-lg .input-group-btn .btn {
            font-size: 11pt !important;
        }



    </style>
    <?php do_action("wpdm_shortcode_iframe_head"); ?>
</head>
<body class="w3eden" style="background: transparent">

<?php
echo do_shortcode("[wpdm_package id='{$pid}']");
?>

<script>

    jQuery(function ($) {

        $('a').each(function () {
            $(this).attr('target', '_blank');
        });

        $('body').on('click','a', function () {
            $(this).attr('target', '_blank');
        });


        //window.parent.document.wpdm_adjust_frame_height("<?php echo $_REQUEST['frameid']; ?>", $(document).height());
        //window.parent.document.getElementById("<?php echo $_REQUEST['frameid']; ?>").style.height = $(document).height()+"px";
        //window.parent.document.getElementById("<?php echo $_REQUEST['frameid']; ?>").height = $(document).height()+"px";


    });

    function showModal() {
        jQuery('#wpdm-locks').modal('show');
    }
    showModal();
</script>
<div style="display: none">
<?php  if((isset($pack->packageData['form_lock']) && $pack->packageData['form_lock'] == 1)  || $pack->packageData['base_price'] > 0) wp_footer(); ?>
<?php do_action("wpdm_shortcode_iframe_footer"); ?>
</div>
</body>
</html>
