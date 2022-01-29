<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_print_submission.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?php _e("SUBMISSION'S PDF",'custom-registration-form-builder-with-submission-manager') ?></title>
        <style>
            * {
                box-sizing: border-box;
            }

            div {
                outline: 0px solid cyan;
            }

            .rmagic  a {
/*                font-family: 'freeserif','Roboto', 'Helvetica',sans-serif;*/
                text-transform: uppercase;
                color: #ff6c6c;
                text-decoration: none;
            }

            .rmagic {

/*                font-family: 'freeserif','Roboto', 'helvetica',sans-serif;*/
                display: block;
                float: left;
                width: 100%;
               /* margin: 5%;
                margin-left: 7%;*/
                color: rgb(125,125,125);
                font-size: 14px;
                margin: 0px;

            }

            .rmagic sup {
                text-transform: uppercase;
                color: #ff6c6c;
            }

            .rmagic span.rm-red {
                text-transform: uppercase;
                color: #ff6c6c;
            }

            .rmagic .rm-buttonarea {
                width: 100%;
                display: block;
                float: left;
                padding: 15px;
                margin-top: 25px;
            }

            .rmagic input[type=submit] {
                display: inline-block;
                float: left;
                color: #ffffff;
                text-transform: uppercase;
                font-size: 14px;
                border: none;
                background: #ff6c6c;
                padding: 10px 25px 10px 25px;
                transition: 0.1s;
                border-radius: 4px;
            }



            .rmagic input[type=submit]:hover {
                background-color: rgb(245,245,245);
                color: #ff6c6c;
            }

            .rmagic .cancel {
/*                font-family: 'freeserif','Roboto', 'Arial', serif;*/
                display: inline-block;
                float: left;
                color: rgb(200,200,200);
                font-size: 14px;
                text-transform: uppercase;
                padding: 10px 25px 10px 25px;
                margin-right: 20px;
                border-radius: 4px;
                transition: 0.3s;
            }

            .rmagic .cancel:hover {
                color: #ff6c6c;
            }

            .rmagic .rm-submission, .rmagic .rm-invites {margin-top: 25px;}
            .rm-submission-field-row {border-bottom: 1px dotted rgb(240,240,240);}

            .rmagic .rm-submission, .rm-submission-field-row {
                display: block;
                background-color: #fffffe;
                float: left;
                width: 100%;
                padding: 0px;
            }
             .rm-submission-field-row { display: table;}

            .rmagic .rm-submission-label, .rm-submission-value {
                display: inline-block;
                float: left;
            }

            .rmagic .rm-submission-label {font-weight: bold; width: 20%; text-transform: uppercase; font-size: 12px;}
            .rmagic .rm-submission-value {width: 80%;}
            
            .rmagic .rm-submission-label,
            .rmagic .rm-submission-value { display: table-cell;height:50px;}
            
            .rmagic .rm-submission-field-row .rm-submission-attachment {
                display: inline-block;
                float: left;
                padding: 10px;
                background-color: rgb(250,250,250);
                border: 2px dashed rgb(240,240,240);
                width: 120px;
                margin:0 10px 10px 0;
            }

            .rmagic .rm-submission-field-row .rm-submission-attachment img {
                float: left;
                display: block;
                width: 100px;
                max-height: 100px;
                height: auto;
            }

            .rmagic .rm-submission-attachment-field {
                display: block;
                float: left;
                font-size: 12px;
                width: 100px;
                text-align: center;
                padding: 5px 0 0 0;
                text-overflow: ellipsis;
                overflow: hidden;

            }

            .rmagic .rm-submission-note {
                border-left: 4px solid red;
                padding: 10px;
                margin-top: 10px;
                display: block;
                width: 100%;
                float: left;
                background: #fffffe;
            }

            .rmagic .rm-submission-note-text {
                background-image: url(rm-submission-note.png);
                background-repeat: no-repeat;
                padding-left: 25px;
                display: block;
                width: 100%;
                float: left;
                font-style: italic;
            }

            .rmagic .rm-submission-note-attribute {
                font-size: 10px;
                padding: 10px;
                text-transform: uppercase;
                display: block;
                width: 100%;
                float: left;
                text-align: right;
                color: rgb(175,175,175);
            }

            .rmagic .rm-submission-note-attribute a {
                padding-right: 10px;
                font-size: 14px;
            }

            .rmagic .rmtitle {
/*                font-family: 'freeserif','Titillium Web', 'Verdana', sans-serif;*/
                display: block;
                float: left;
                padding: 20px;
                width:70%;
                font-size:24px;
                color:#94cdc9;
                margin-bottom: 10px;
                text-overflow: ellipsis;
                text-transform: uppercase;
                background-color: #fffffe;
            }

        </style>
    </head>
    <body>
        <div class="rmagic">
            <div class="rmtitle"><?php echo $data->form_name; ?></div>            
            <table class="rm-submission">

                <?php
                if ($data->form_is_unique_token)
                {
                    ?>
                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_UNIQUE_TOKEN_SHORT'); ?> :</td>
                        <td class="rm-submission-value"><?php echo $data->submission->get_unique_token(); ?></td>
                    </tr>
                    <?php
                }
                ?>

                <tr class="rm-submission-field-row">
                    <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_ENTRY_ID'); ?> :</td>
                    <td class="rm-submission-value"><?php echo $data->submission->get_submission_id(); ?></td>
                </tr>

                <tr class="rm-submission-field-row">
                    <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_ENTRY_TYPE'); ?> :</td>
                    <td class="rm-submission-value"><?php echo $data->form_type; ?></td>
                </tr>
                <?php
                if ($data->form_type_status == "1" && !empty($data->user))
                {
                    $user_roles_dd = RM_Utilities::user_role_dropdown();
                    ?>
                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_USER_NAME'); ?> :</td>
                        <td class="rm-submission-value"><?php echo $data->user->display_name; ?></td>
                    </tr>

                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_USER_ROLES'); ?> :</td>
                        <td class="rm-submission-value">
                            <?php
                            if(isset($data->user->roles[0],$user_roles_dd[$data->user->roles[0]]))
                                echo $user_roles_dd[$data->user->roles[0]];
                            else
                                echo "<i>".RM_UI_Strings::get('MSG_USER_ROLE_NOT_ASSIGNED')."</i>";
                            ?>
                        </td>
                    </tr>

                    <?php
                }
                ?>
                <?php
                $submission_data = $data->submission->get_data();
                if (is_array($submission_data) || $submission_data)
                    foreach ($submission_data as $field_id => $sub):
                        
                        $sub_key = $sub->label;
                        $sub_data = $sub->value;
                        if(!isset($sub->type)){
                                $sub->type = '';
                            }
                        if((in_array($sub->type, RM_Utilities::pdf_excluded_widgets())))
                                continue;     
                        
                        ?>

                        <!--submission row block-->
                        <?php if(!empty($sub_data)){ ?>
                        <tr class="rm-submission-field-row">
                            <td class="rm-submission-label"><?php echo $sub_key; ?> :</td>
                            <td class="rm-submission-value">
                            <?php
                            //if submitted data is array print it in more than one row.

                            if (is_array($sub_data))
                            {

                                $i = 0;

                                //If submitted data is a file.

                                if (isset($sub_data['rm_field_type']) && $sub_data['rm_field_type'] == 'File')
                                {
                                    unset($sub_data['rm_field_type']);
                                    ?>                                    

                                        <?php
                                    foreach ($sub_data as $sub)
                                    {
                                        $att_path = get_attached_file($sub);
                                        ?>
                                        <div class="rm-submission-attachment">
                                            <?php $attachment_data= wp_get_attachment_link($sub, 'thumbnail', false, true, false); $attachment_data= str_replace("'", '"', $attachment_data); echo $attachment_data;?>
                                            <div class="rm-submission-attachment-field">
                                                <a href="<?php echo wp_get_attachment_url($sub); ?>">
                                                    <?php echo basename($att_path); ?>
                                                </a>  
                                            </div>
                                        </div>

                                        <?php
                            }
                            ?>
                            
                            <?php
                        } else
                        {
                            $sub = implode(', ', $sub_data);
                            echo $sub;
                        }
                        } else
                        {
                             echo $sub_data; 
                              
                        }
                        ?>
                        </td>
                            </tr><!-- End of one submission block-->
                            <?php
                            }
                    endforeach;

                if ($data->payment)
                {
                    ?>
                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_INVOICE'); ?> :</td>
                        <td class="rm-submission-value"><?php if (isset($data->payment->invoice)) echo $data->payment->invoice; ?></td>
                    </tr>
                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_TAXATION_ID'); ?> :</td>
                        <td class="rm-submission-value"><?php if (isset($data->payment->txn_id)) echo $data->payment->txn_id; ?></td>
                    </tr>
                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_STATUS_PAYMENT'); ?> :</td>
                        <td class="rm-submission-value"><?php if (isset($data->payment->status)) echo $data->payment->status; ?></td>
                    </tr>
                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_PAID_AMOUNT'); ?> :</td>
                        <td class="rm-submission-value"><?php if (isset($data->payment->total_amount)) echo $data->payment->total_amount; ?></td>
                    </tr>
                    <tr class="rm-submission-field-row">
                        <td class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_DATE_OF_PAYMENT'); ?> :</td>
                        <td class="rm-submission-value"><?php if (isset($data->payment->posted_date)) echo RM_Utilities::localize_time($data->payment->posted_date, get_option('date_format')); ?></td>
                    </tr>
                    <?php
                }
                ?>

            </table>
        </div>
    </body>
</html>

<?php } ?>