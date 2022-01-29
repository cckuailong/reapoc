<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class works as a repository of all the string resources used in product UI
 * for easy translation and management. 
 *
 * @author CMSHelplive
 */
class RM_UI_Strings {

    public static function get($identifier) {

        switch ($identifier) {

             case 'LABEL_ALLOWED_MULTI_FILES':
                return __('Allow Uploading Multiple Files', 'custom-registration-form-builder-with-submission-manager');

            case 'BROWSER_JS_DISABLED':
                return __('Note: It looks like JavaScript is disabled in your browser. Some elements of this form may require JavaScript to work properly. If you have trouble submitting the form, try enabling JavaScript momentarily and resubmit. JavaScript settings are usually found in Browser Settings or Browser Developer menu.', 'custom-registration-form-builder-with-submission-manager');
    
            case 'MSG_BUY_PRO':
                return __('This feature (and a lot more...) is part of Silver Edition package.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_INLINE':
                return sprintf(__('<span class="rm_buy_pro_inline">To unlock this feature (and many more), please upgrade <a href="%s" target="blank">Click here</a></span>', 'custom-registration-form-builder-with-submission-manager'),RM_Utilities::comparison_page_link());

            case 'MSG_BUY_PRO_INLINE_ATT':
                return sprintf(__('<span class="rm_buy_pro_inline">View and Download <b>form attachments</b> at a single place by upgrading <a href="%s" target="blank">Click here</a></span>', 'custom-registration-form-builder-with-submission-manager'),RM_Utilities::comparison_page_link());

            case 'MSG_BUY_PRO_BOTH_INLINE':
                return sprintf(__('<span class="rm_buy_pro_inline">To unlock this feature (and many more), please upgrade <a href="%s" target="blank">Click here</a></span>', 'custom-registration-form-builder-with-submission-manager'),RM_Utilities::comparison_page_link());

            case 'MSG_BUY_PRO_GOLD_MULTIPAGE':
                return __('Multi-page feature (and many more) is part of <b>Premium Edition</b> package', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_GOLD_EMBED':
                return __('Embed code feature (and many more) is part of Premium Edition package', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_PRICE_FIELDS':
                return __('Multiple price type fields (and a lot more...) is part of Silver Edition package.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_USER_ROLE':
                return __('You can assign user roles created here to registered users. Auto user role assignment through "Registration Form" or let user pick their role at the time of Registration (and a lot more...) is part of Silver Edition package.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_FIELDS':
                return __('File, repeatable, map, address type fields (and a lot more...) is part of Silver Edition package.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_SUB_MAN':
                return __('Export option (and a lot more...) is part of Silver Edition package.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_SUB_VIEW':
                return __('&quot;Print as PDF&quot; and &quot;Add note&quot; option (and a lot more...) is part of Silver Edition package.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BUY_PRO_ATT_BROWSER':
                return __('Attachments browser allows you to easily view and download attachments sent by the users through forms.<br>This feature (and a lot more...) is part of Silver Edition package.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FORM_SUB_MAN':
                return __('No Forms you have created yet.<br>Once you have created a form and submissions start coming, this area will show you a nice little table with all the submissions.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_UPGRADE_NOW':
                return __('UPGRADE NOW.', 'custom-registration-form-builder-with-submission-manager');
            case 'PH_USER_ROLE_DD':
                return __('Select User Role', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_NEW_FORM_PAGE':
                return __('New Registration Form', 'custom-registration-form-builder-with-submission-manager');

            case 'SUBTITLE_NEW_FORM_PAGE':
                return __('Some options in this form will only work after you have created custom fields.', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_EDIT_PAYPAL_FIELD_PAGE':
                return __('Edit Product', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_LOGIN_URL':
                return __('Login Box Page:', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_LOGIN_SUB':
                return __('login in', 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_OPTIONS_LOGIN_URL':
                return __(" The users will be asked to login on this page when they reach front-end submissions area in logged out state. Make sure login shortcode is pasted on the selected page.", 'custom-registration-form-builder-with-submission-manager');
                
            case 'TITLE_USER_EDIT_PAGE':
                return __('Edit User', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_NEW_PAYPAL_FIELD_PAGE':
                return __('New Product', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_ATTACHMENT_PAGE':
                return __('Attachments', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_SUBMISSION_MANAGER':
                return __('Inbox', 'custom-registration-form-builder-with-submission-manager');

            case 'HEADING_ADD_ROLE_FORM':
                return __('Add New Role', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TITLE':
                return __('Title', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_UNIQUE_TOKEN_SHORT':
                return __('Unique Token No.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NOTE_TEXT':
                return __('Note Text', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_OTHER':
                return sprintf(__("Allow users to input custom value. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htoptions');

            case 'MAIL_REGISTRAR_DEF_SUB':
                return __('Your Submission', 'custom-registration-form-builder-with-submission-manager');

            case 'MAIL_NEW_USER_DEF_SUB':
                return __('New User Registration', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_THEIR_ANS':
                return __('User Input', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FIELD_STAT_DATA':
                return __('No data recorded for this field to generate pie chart', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FIELD_LABEL':
                return __('Field Label', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NOTE_COLOR':
                return __('Note Color', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MY_SUBS':
                return __('Registrations', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MY_SUB':
                return __('Registration', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_OPT_IN_CB':
                return __('Show opt-in checkbox', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPT_IN_CB':
                return sprintf(__("Display a checkbox, allowing users to opt-in for subscription. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/mailchimp-integration-2/#htmcoptin');

            case 'LABEL_OPT_IN_CB_TEXT':
                return __('Opt-in checkbox text', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SUBMISSION_MATCHED':
                return __('No Submission matched your search.', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPT_IN_CB_TEXT':
                return sprintf(__("This text will appear with the opt-in checkbox. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/mailchimp-integration-2/#htmcoptintext');
            
            case 'LABEL_WORDPRESS_DEFAULT':
                 return __('Wordpress Default:', 'custom-registration-form-builder-with-submission-manager');
                 
             case 'LABEL_WORDPRESS_DEFAULT_EMAIL_To':
                 return __('To:', 'custom-registration-form-builder-with-submission-manager');
                 
             case 'LABEL_WORDPRESS_DEFAULT_EMAIL_MESSAGE':
                 return __('Message:', 'custom-registration-form-builder-with-submission-manager');
                 
            case 'HELP_OPTIONS_ENABLE_WORDPRESS_DEFAULT':
                 return __('Use the built in mail program that normally uses your host server to send emails.', 'custom-registration-form-builder-with-submission-manager');
 
             case 'HELP_OPTIONS_WORDPRESS_DEFAULT_EMAIL_To':
                 return __('To make sure your emails are working normally, please provide an email address for testing purposes. RegistrationMagic will try sending an e-mail to this address. Make sure it belongs to a monitored inbox, so that you can check the email once it arrives.', 'custom-registration-form-builder-with-submission-manager');
 
            case 'LABEL_WORDPRESS_DEFAULT_EMAIL_REQUIRED_MESSAGE':
                return __('Email cannot be left blank','custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_SMTP_FAIL_MESSAGE':
                 return sprintf(__('Failed!! <a href="%s" target="_blank"> Need Help? </a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/wordpress-registration-not-sending-email/');
                 
             case 'HELP_OPTIONS_WORDPRESS_DEFAULT_EMAIL_MESSAGE':
                 return __('Enter a message that will be used for test email.', 'custom-registration-form-builder-with-submission-manager');
             
             case 'LABEL_EMAIL_HANDLER':
                return __('Outgoing Emails Handler:', 'custom-registration-form-builder-with-submission-manager');
                
               case 'HELP_OPTIONS_ENABLE_SMTP':
                return __('Use an external SMTP (Google, Yahoo!, SendGrid etc) instead of local mail server, to deliver RegistrationMagic mails.','custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_OPTIONS_ENABLE_WORDPRESS_DEFAULT':
                return __('Use the built in mail program that normally uses your host server to send emails.', 'custom-registration-form-builder-with-submission-manager');
  
            case 'LABEL_WORDPRESS_DEFAULT_EMAIL_SUCCESS_MESSAGE':
                return sprintf(__('The test email has been successfully sent by WordPress. Please note this does NOT mean it has been delivered. Check the test email address inbox to double confirm. Make sure you also check your spam folder. If your mails are being marked as spam, you can use a third party spam diagnostic service like <a class="rm_mail_tester_link" href="%s" target="_blank">%s</a>', 'custom-registration-form-builder-with-submission-manager'),'https://www.mail-tester.com.','https://www.mail-tester.com.');
                
            case 'LABEL_WORDPRESS_DEFAULT_EMAIL_FAIL_MESSAGE':
                 return sprintf(__('Failed!! <a href="%s" target="_blank">Need Help?</a>','custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/wordpress-registration-not-sending-email/');
             
            case 'LABEL_SMTP_SUCCESS_MESSAGE':
                return sprintf(__('The test email has been successfully sent by SMTP relay using the details your provided. Please note this does NOT mean it has been delivered. Check the test email address inbox to double confirm. Make sure you also check your spam folder. If your mails are being marked as spam, you can use a third party spam diagnostic service like <a class="rm_mail_tester_link" href="%s" target="_blank">%s</a>', 'custom-registration-form-builder-with-submission-manager'),'https://www.mail-tester.com','https://www.mail-tester.com');
              
            case 'PH_NO_FORMS':
                return __('No Forms', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAY_HISTORY':
                return __('Payment History', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NOT_AUTHORIZED':
                return __('You are not authorized to view the contents of this page. Please log in to view the submissions.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_FORM_EXPIRY':
                return __('This Form has expired.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FIELDS':
                return __('This Form has no fields.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LOG_OFF':
                return __('Log Off', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PRINT':
                return __('Print', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_VISIBLE_FRONT':
                return __('Visible to User', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT':
                return __('Select', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BACK':
                return __('Back', 'custom-registration-form-builder-with-submission-manager');

            case 'SELECT_FIELD_MULTI_OPTION':
                return __("Select options", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_NOTE':
                return __('Add Note', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STATUS_PAYMENT':
                return __('Payment Status', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_SUBSCRIBE':
                return __('Subscribe for emails', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FAILED':
                return __('Failed', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_USER_PASS_NOT_SET':
                return __('User Password is not set.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAID_AMOUNT':
                return __('Paid Amount', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AMOUNT':
                return __('Amount', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_DATA_FOR_EMAIL':
                return __('No submission data for this email.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TXN_ID':
                return __('Transaction Id', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUPPORT_EMAIL_LINK':
                return __('Email', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PREVIOUS':
                return __('Prev', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NEXT':
                return __('Next', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FIRST':
                return __('First', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAST':
                return __('Last', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAYOUT':
                return __('Layout', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAYOUT_LABEL_LEFT':
                return __('Label left', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAYOUT_LABEL_TOP':
                return __('Label top', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAYOUT_TWO_COLUMNS':
                return __('Two columns', 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_NO_FORMS':
                return __('No forms.', 'custom-registration-form-builder-with-submission-manager');


            case 'MSG_DO_NOT_HAVE_ACCESS':
                return __('You do not have access to see this page.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DATE_OF_PAYMENT':
                return __('Date Of Payment', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_SUBMISSION_ID_FOR_EMAIL':
                return __('Invalid Submission Id', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_SUBMISSION_ID':
                return __('Invalid Submission Id', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_CUSTOM_FIELDS':
                return __('No custom field values available for this user.<br>This area displays fields marked by &quot;Add this field to User Account&quot;.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SUBMISSIONS_USER':
                return __('This user has not submitted any forms yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FORMS_ATTACHMENTS':
                return __('You have not created any form yet.<br>Once you have created a form and submissions start coming, this area will show all submitted attachments for the form.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_PAYMENTS_USER':
                return __('No payment records exist for this user.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_REGISTRATIONS':
                return __('Inbox', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_INVOICE':
                return __('Payment Invoice', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TAXATION_ID':
                return __('Payment TXN ID', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CREATED_BY':
                return __('Created By', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TYPES':
                return __('Types', 'custom-registration-form-builder-with-submission-manager');

            case 'NO_SUBMISSION_FOR_FORM':
                return __('No Submissions for this form yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TYPE':
                return __('Type', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_PRICE_FIELD':
                return sprintf(__("Please Enter a value greater than zero. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/add-product/#htprodprice');

            case 'HELP_PASSWORD_MIN_LENGTH':
                return __('Password must be at least 7 characters long.', 'custom-registration-form-builder-with-submission-manager');

            case 'FORM_ERR_INVALID':
                return __("%element% is invalid.", 'custom-registration-form-builder-with-submission-manager');

            case 'FORM_ERR_FILE_TYPE':
                return __("Invalid type of file uploaded in %element%.", 'custom-registration-form-builder-with-submission-manager');

            case 'FORM_ERR_INVALID_DATE':
                return __("%element% must contain a valid date.", 'custom-registration-form-builder-with-submission-manager');

            case 'FORM_ERR_INVALID_EMAIL':
                return __("%element% must contain a valid email address.", 'custom-registration-form-builder-with-submission-manager');

            case 'FORM_ERR_INVALID_NUMBER':
                return __("%element% must be numeric.", 'custom-registration-form-builder-with-submission-manager');

            case 'FORM_ERR_INVALID_REGEX':
                return __("%element% contains invalid charcters.", 'custom-registration-form-builder-with-submission-manager');

            case 'FORM_ERR_INVALID_URL':
                return __("%element% must contain a url (e.g. http://www.google.com).", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ROLE_DISPLAY_NAME':
                return __('Role Name', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_DESC':
                return __('Description', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NO_ATTACHMENTS':
                return __('No Attachments for this form yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CUSTOM_FIELD':
                return __('Details', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DOWNLOAD_ALL':
                return __('Download All', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DOWNLOAD':
                return __('Download', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SR':
                return __('Sr.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CREATE_WP_ACCOUNT':
                return __('Also create WP User account', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DO_ASGN_WP_USER_ROLE':
                return __('Assign WordPress User Role', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LET_USER_PICK':
                return __('Allow Users Choice of Roles', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_USER_ROLE_FIELD':
                return __('WP User Role Field Label', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ALLOW_WP_ROLE':
                return __('Allow Role Selection from', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ROLE':
                return __('Role', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CONTENT_ABOVE':
                return __('Content Above The Form', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUCC_MSG':
                return __('Success Message', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_UNIQUE_TOKEN':
                return __('Display a Unique Token Number', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_USER_REDIRECT':
                return __('Redirection', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAGE':
                return __('Page', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_URL':
                return __('URL', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AUTO_REPLY':
                return __('Auto-Reply the User', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AR_EMAIL_SUBJECT':
                return __('Subject', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AR_EMAIL_BODY':
                return __('Body', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMIT_BTN':
                return __('Submit Button Label', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMIT_BTN_COLOR':
                return __('Submit Button Label Color', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SUBMISSION_SUB_MAN':
                return __('No Submissions for this form yet.<br>Once submissions start coming, this area will show you a nice little table with all the submissions.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SUBMISSION_SUB_MAN_INTERVAL':
                return __('No Submissions during the period.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMIT_BTN_COLOR_BCK':
                return __('Submit Button Background Color', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AUTO_EXPIRE':
                return __('Limit Submissions', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EXPIRY':
                return __('Set Limitations - By Number/ By Date', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUB_LIMIT':
                return __('Limit by Number', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EXPIRY_DATE':
                return __('Limit by Date', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EXPIRY_MSG':
                return __('Message Content', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SAVE':
                return __('Save', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CANCEL':
                return __('Cancel', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CREATE_WP_ACCOUNT_DESC':
                return __('This will add Username and Password fields to this form', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_FORM_MANAGER':
                return __('All Forms', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_NEW':
                return __('New Form', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_NEW_FIELD':
                return __('Add Field', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DUPLICATE':
                return __('Duplicate', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FILTERS':
                return __('Filters', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TIME':
                return __('Time', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMISSIONS':
                return __('Submissions', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SEARCH':
                return __('Search', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BY_NAME':
                return __('By Name', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SORT':
                return __('Sort', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAST_AT':
                return __('Last at', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FIELDS':
                return __('Fields', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUCCESS_RATE':
                return __('Success rate', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAST_MODIFIED_BY':
                return __('Last modified by', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EDIT':
                return __('Edit', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EDITED_BY':
                return __('Edited By', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYER_NAME':
                return __('Payer name', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYER_EMAIL':
                return __('Payer email', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FORMS':
                return __('No Forms Yet', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FORMS_FUNNY':
                return __('No Forms Yet! Why not create one.', 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_SUBMIT_BTN_COLOR_BCK_DSC':
                return __('Does not works with Classic form style', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT_TYPE':
                return __('Field Type', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_NEW_FIELD_PAGE':
                return __('New Field', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LABEL':
                return __('Label', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PLACEHOLDER_TEXT':
                return __('Placeholder Text', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CSS_CLASS':
                return __('CSS Class Attribute', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MAX_LENGTH':
                return __('Maximum Length', 'custom-registration-form-builder-with-submission-manager');

            case 'TEXT_RULES':
                return __('Rules', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IS_REQUIRED':
                return __('Required Field', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_ON_USER_PAGE':
                return __('Display in Frontend User Area', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PARAGRAPF_TEXT':
                return __('Paragraph Text', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_OPTIONS':
                return __('Options', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DROPDOWN_OPTIONS_DSC':
                return __('Values seprated by comma ","', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DEFAULT_VALUE':
                return __('Default Value', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_COLUMNS':
                return __('Columns', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_VALUE':
                return __('Value', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ROWS':
                return __('Rows', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IS_READ_ONLY':
                return __('Is Read Only', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_T_AND_C':
                return __('Terms & Conditions', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FILE_TYPES':
                return __('Define Allowed File Types (file extensions. For example PDF|JPEG|XLS)', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PRICING_FIELD':
                return __('Select Product', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PRICE':
                return __('Price', 'custom-registration-form-builder-with-submission-manager');

            case 'VALUE_CLICK_TO_ADD':
                return __('Click to add more', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_EDIT_FORM_PAGE':
                return __('Edit Form', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_FORM_FIELD_PAGE':
                return __('Fields Manager', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_FIELD':
                return __('Add Field', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM':
                return __('Form', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_REMOVE':
                return __('Remove', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_COMMON_FIELDS':
                return __('Common Fields', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SPECIAL_FIELDS':
                return __('Special Fields', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PROFILE_FIELDS':
                return __('Profile Fields', 'custom-registration-form-builder-with-submission-manager');

            case 'PH_SELECT_A_FIELD':
                return __('Select A Field', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_TEXT':
                return __('Text', 'custom-registration-form-builder-with-submission-manager');

            case 'WIDGET_TYPE_PARAGRAPH':
                return __('Paragraph', 'custom-registration-form-builder-with-submission-manager');

            case 'WIDGET_TYPE_HEADING':
                return __('Heading', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_DROPDOWN':
                return __('Drop Down', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_RADIO':
                return __('Radio Button', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_TEXTAREA':
                return __('Textarea', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_CHECKBOX':
                return __('Checkbox', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_DATE':
                return __('Date', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DATE' :
                return __('Date', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_EMAIL':
                return __('Email', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_NUMBER':
                return __('Number', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_COUNTRY':
                return __('Country', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_TIMEZONE':
                return __('Timezone', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_T_AND_C':
                return __('T&C Checkbox', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_FILE':
                return __('File Upload', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_PRICE':
                return __('Add Product', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_REPEAT':
                return __('Repeatable Text', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_FNAME':
                return __('First Name', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_LNAME':
                return __('Last Name', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_BINFO':
                return __('Biographical Info', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DELETE':
                return __('Delete', 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_BIO':
                return __('Bio', 'custom-registration-form-builder-with-submission-manager');

            case 'NO_FIELDS_MSG':
                return __('No fields for this form yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'NO_PRICE_FIELDS_MSG':
                return __('You do not have any product yet. Select a Pricing Type above to start creating products.<br>These products can be later inserted into any form for accepting payment.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FORM_SELECTED':
                return __('No form selected', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_EDIT_FIELD_PAGE':
                return __('Edit Field', 'custom-registration-form-builder-with-submission-manager');
            
            case 'MSG_RM_PRODUCT_NOTICE':
                return __("To add this product to a form, choose <b>Product</b> field type when creating a new field. In the field's settings, you can select this product using dropdown.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD':
                return __('Add', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EMAIL':
                return __('Email', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STATUS':
                return __('Status', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NAME':
                return __('Name', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DEACTIVATED':
                return __('Deactivated', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTIVATED':
                return __('Activated', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MATCH_FIELD':
                return __('Match Field', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_CLICK_TO_ADD':
                return __('Click to add options', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_HEADING_TEXT':
                return __('Heading Text', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_FIELD_SELECTED':
                return __('No Field Selected', 'custom-registration-form-builder-with-submission-manager');

            case 'ALERT_DELETE_FORM':
                return __('You are going to delete this form(s). This will also delete all data assosiated with the form(s) including submissions and payment records. Users will not be deleted. Do you want to proceed?', 'custom-registration-form-builder-with-submission-manager');

            /* 9th March */
            case 'USER_MANAGER':
                return __('User Manager', 'custom-registration-form-builder-with-submission-manager');

            case 'NEW_USER':
                return __('New User', 'custom-registration-form-builder-with-submission-manager');

            case 'ACTIVATE':
                return __('Activate', 'custom-registration-form-builder-with-submission-manager');

            case 'DEACTIVATE':
                return __('Deactivate', 'custom-registration-form-builder-with-submission-manager');

            case 'IMAGE':
                return __('Image', 'custom-registration-form-builder-with-submission-manager');

            case 'FIRST_NAME':
                return __('First Name', 'custom-registration-form-builder-with-submission-manager');

            case 'LAST_NAME':
                return __('Last Name', 'custom-registration-form-builder-with-submission-manager');

            case 'DOB':
                return __('DOB', 'custom-registration-form-builder-with-submission-manager');

            case 'ACTION':
                return __('Action', 'custom-registration-form-builder-with-submission-manager');

            case 'VIEW':
                return __('View', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS':
                return __('Global Settings', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_GENERAL':
                return __('General Settings', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_GENERAL_EXCERPT':
                return __('Form look, Default pages, Attachment settings etc.', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_SECURITY':
                return __('Security', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_SECURITY_EXCERPT':
                return __('reCAPTCHA placement, Google reCAPTCHA keys', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_USER':
                return __('User Accounts', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_USER_EXCERPT':
                return __('Password behavior, Manual approvals etc.', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_EMAIL_NOTIFICATIONS':
                return __('Email Configuration', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_EMAIL_NOTIFICATIONS_EXCERPT':
                return __('Admin notifications, multiple email notifications, From email', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_EXTERNAL_INTEGRATIONS':
                return __('External Integrations', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_EXTERNAL_INTEGRATIONS_EXCERPT':
                return __('MailChimp (more coming soon!)', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_PAYMENT':
                return __('Payments', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYMENTS':
                return __('Payments', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYMENT':
                return __('Payment', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_TITLE':
                return __('Name', 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_PAYMENT_EXCERPT':
                return __('Currency, Symbol Position, Checkout Page etc.', 'custom-registration-form-builder-with-submission-manager');

            case 'SETTINGS':
                return __('Settings', 'custom-registration-form-builder-with-submission-manager');

            case 'SELECT_PAGE':
                return __('Select Page', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NOT_APPLICABLE_ABB':
                return __('N/A', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_STYLE':
                return __('Form Style:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CAPTURE_INFO':
                return __('Capture IP and Browser Info:', 'custom-registration-form-builder-with-submission-manager');

            case 'ALLOWED_FILE_TYPES_HELP':
                return __('(file extensions) (For example PDF|JPEG|XLS)', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ALLOWED_FILE_TYPES':
                return __('Allowed File Types', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_HIDE_TOOLBAR':
                return __('Hide WordPress Toolbar', 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_ENABLE_TOOLBAR_ADMIN':
                return __('Enable Toolbar for Admin(s)', 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_HIDE_TOOLBAR':
                return sprintf(__("Hides the top WordPress admin bar for logged in users. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#hthidewpbar');
                
            case 'HELP_ENABLE_TOOLBAR_ADMIN':
                return sprintf(__("Allows administrator users to retain the WordPress admin bar. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#hthidewpbar');
        
            case 'LABEL_DEFAULT_REGISTER_URL':
                return __('Default Registration Page', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AFTER_LOGIN_URL':
                return __('After Login Redirect User to:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ANTI_SPAM':
                return __('Security', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ENABLE_CAPTCHA':
                return __('Enable reCaptcha:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CAPTCHA_LANG':
                return __('reCAPTCHA Language:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CAPTCHA_AT_LOGIN':
                return __('reCAPTCHA under User Login:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SITE_KEY':
                return __('Site Key:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CAPTCHA_KEY':
                return __('Secret Key:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CAPTCHA_METHOD':
                return __('Request Method:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CAPTCHA_METHOD_HELP':
                return __('(Change this setting if your ReCaptcha is not working as expected.)', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AUTO_PASSWORD':
                return __('Auto Generated Password:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SEND_PASS_EMAIL':
                return __('Send Username and Password to the User through Email', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_REGISTER_APPROVAL':
                return __('WordPress Registration Auto Approval', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_USER_NOTIFICATION_FRONT_END':
                return __('Notify User about Submission Notes', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NOTIFICATIONS_TO_ADMIN':
                return __('Notify Site Admin', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ENABLE_SMTP':
                return __('User External SMTP', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SMTP_HOST':
                return __('SMTP Host', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SMTP_PORT':
                return __('SMTP Port', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SMTP_ENCTYPE':
                return __('Encryption Type', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SMTP_AUTH':
                return __('Authentication', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SMTP_TESTMAIL':
                return __('Email Address for Testing', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TEST':
                return __('Test', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_EMAIL':
                return __('Add Fields', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FROM_EMAIL':
                return __('From Email', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FROM_EMAIL_DISP_NAME':
                return __("Sender's Name", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_FORM':
                return __('Add Form', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FILTER_BY':
                return __('Filter by', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DISPLAYING_FOR':
                return __('Displaying for', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT_RESIPIENTS':
                return __('Select recipients from', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LOGIN_FACEBOOK_OPTION':
                return __('Enable Facebook Login', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FACEBOOK_APP_ID':
                return __('Facebook App ID:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FACEBOOK_SECRET':
                return __('Facebook App Secret', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MAILCHIMP_INTEGRATION':
                return __('Enable MailChimp Integration', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MAILCHIMP_API':
                return __('MailChimp API', 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_MAILCHIMP_DOUBLE_OPTIN':
                return __('Enable MailChimp Double Opt-In', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYMENT_PROCESSOR':
                return __('Payment Processor(s)', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TEST_MODE':
                return __('Enable Test Mode', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYPAL_EMAIL':
                return __('Email', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CURRENCY':
                return __('Default Currency', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYPAL_STYLE':
                return __('Page Style', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CURRENCY_SYMBOL':
                return __('Currency Symbol Position', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CURRENCY_SYMBOL_HELP':
                return sprintf(__("Choose position of the currency sign. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/payments/#htcurrencysymbol');

            case 'LABEL_RECIPIENTS_OPTION':
                return __('Define Recipients Manually', 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_FILE_FORMAT':
                return __('Uploaded files must be in allowed format.', 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_FILE_SIZE':
                return __('File is too large to upload.', 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_FILE_UPLOAD':
                return __('File upload was not successfull', 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_INVALID_RECAPTCHA':
                return __('The reCAPTCHA response provided was incorrect.  Please re-try.', 'custom-registration-form-builder-with-submission-manager');

            case 'OPTION_SELECT_LIST':
                return __('Select a List', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MAILCHIMP_LIST':
                return __('Choose MailChip List', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_USERNAME':
                return __('Username', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PASSWORD':
                return __('Password', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PASSWORD_AGAIN':
                return __('Enter password again', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_EMAIL_AGAIN':
                return __('Enter email again', 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_USERNAME_PH':
                return __('Enter Username', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PASSWORD_PH':
                return __('Enter Password', 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PASSWORD_PH_AGAIN':
                return __('Repeat your password', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_EMAIL_PH_AGAIN':
                return __('Repeat your email', 'custom-registration-form-builder-with-submission-manager');
                 
            case 'ERR_PW_MISMATCH':
                return __('Passwords do not match', 'custom-registration-form-builder-with-submission-manager');
                
            case 'ERR_EMAIL_MISMATCH':
                return __('Emails do not match', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NONE':
                return __('None', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CONFIRM_PASSWORD':
                return __('Confirm Password', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LOGIN':
                return __('Login', 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_REQUIRED':
                return __('is a required field.', 'custom-registration-form-builder-with-submission-manager');

            case 'LOGGED_STATUS':
                return __('You are already logged in.', 'custom-registration-form-builder-with-submission-manager');

            case 'RM_LOGIN_HELP':
                return __('To show login box on a page, you can use Shortcode [RM_Login], or you can select it from the dropdown just like any other form.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TODAY':
                return __('Today', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_YESTERDAY':
                return __('Yesterday', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_THIS_WEEK':
                return __('This Week', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LAST_WEEK':
                return __('Last Week', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_THIS_MONTH':
                return __('This Month', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_THIS_YEAR':
                return __('This Year', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PERIOD':
                return __('Specific Period', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTIVE':
                return __('Active', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PENDING':
                return __('Pending', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ROLE_AS':
                return __('Register As', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_REDIRECT_URL_INVALID':
                return __('After Submission redirect URL not given.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_REDIRECT_PAGE_INVALID':
                return __('After submission redirect Page not given.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_EXPIRY_LIMIT_INVALID':
                return __('Form expiry limit is invalid.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_EXPIRY_DATE_INVALID':
                return __('Form expiry date is invalid.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_FORM_EXPIRED':
                return __('<div class="form_expired">Form Expired</div>', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_EXPIRY_INVALID':
                return __('Please select a form expiration criterion (By Date, By Submissions etc.)', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_EXPIRY_BOTH_INVALID':
                return __('Please select both expiry criterion (By Date, By Submissions). ', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SUBMISSION':
                return __('Latest Submissions not available for this form.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SUBMISSION_FRONT':
                return __('You have not submitted any forms yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'USERNAME_EXISTS':
                return __("This user is already registered. Please try with different username or login.", 'custom-registration-form-builder-with-submission-manager');

            case 'P_FIELD_TYPE_FIXED':
                return __("Fixed", 'custom-registration-form-builder-with-submission-manager');

            case 'P_FIELD_TYPE_MULTISEL':
                return __("Multi Select", 'custom-registration-form-builder-with-submission-manager');

            case 'P_FIELD_TYPE_DROPDOWN':
                return __("DropDown", 'custom-registration-form-builder-with-submission-manager');

            case 'P_FIELD_TYPE_USERDEF':
                return __("User Defined", 'custom-registration-form-builder-with-submission-manager');

            case 'USEREMAIL_EXISTS':
                return __("This email is already associated with a user account. Please login to fill this form.", 'custom-registration-form-builder-with-submission-manager');

            case 'USER_EXISTS':
                return __("This user is already registered. Please try with different username or email.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CREATE_FORM':
                return __("Create New Form", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NEWFORM_NOTIFICATION':
                return __("New Form Notification", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_SUPPORT_PAGE':
                return __("Support, Feature Requests and Feedback", 'custom-registration-form-builder-with-submission-manager');

            case 'MAIL_BODY_NEW_USER_NOTIF':
                return sprintf(__("Your account has been successfully created on %s. You can now login using following credentials:<br>Username : %s<br>Password : %s", 'custom-registration-form-builder-with-submission-manager'),'{{SITE_NAME}}','{{USER_NAME}}','{{USER_PASS}}');

            case 'SUBTITLE_SUPPORT_PAGE':
                return __("For support, please fill in the support form with relevant details.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_DELETED':
                return __("Form deleted", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUPPORT_FORM':
                return __("SUPPORT FORM", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ROLE_NAME':
                return __("Role Key", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_USER_ROLES':
                return __("User Roles", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_ROLE':
                return __("Add Role", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EXPORT_ALL':
                return __("Export All", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_USEREMAIL':
                return __("User Email", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PERMISSION_LEVEL':
                return __("Inherit Permissions", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_CHAR':
                return __("Error: invalid chartacter!", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MAILCHIMP_MAP_EMAIL':
                return __("Map With MailChimp Email Field", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MAILCHIMP_MAP_FIRST_NAME':
                return __("Map With MailChimp First Name Field", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MAILCHIMP_MAP_LAST_NAME':
                return __("Map With MailChimp Last Name Field", 'custom-registration-form-builder-with-submission-manager');

            case 'SELECT_DEFAULT_OPTION':
                return __("Please select a value", 'custom-registration-form-builder-with-submission-manager');

            case 'MAILCHIMP_FIRST_NAME_ERROR':
                return __("Please select First Name field for mailchimp integration.", 'custom-registration-form-builder-with-submission-manager');

            case 'MAILCHIMP_LIST_ERROR':
                return __("Please select a mailchimp list.", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_PAYPAL_FIELD_PAGE':
                return __("Products", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_USER_MANAGER':
                return __("User Manager", 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_STAT_INSUFF_DATA':
                return __('Sorry, insufficient data captured for this form. Check back after few more submissions have been recorded or select another form from above dropdown.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IP':
                return __("Visitor IP", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMISSION_STATE':
                return __("Submission", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMITTED_ON':
                return __("Submitted on", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_VISITED_ON':
                return __("Visited on", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUCCESS':
                return __("Successful", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TIME_TAKEN':
                return __("Filling Time", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TIME_TAKEN_AVG':
                return __("Average Filling Time", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FAILURE_RATE':
                return __("Failure Rate", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMISSION_RATE':
                return __("Submission Rate", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TOTAL_VISITS':
                return __("Total Visits", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CONVERSION':
                return __("Conversion", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CONV_BY_BROWSER':
                return __("Browser wise Conversion", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_HITS':
                return __("Hits", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSERS_USED':
                return __("Browsers Used", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER':
                return __("Browser", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_OTHER':
                return __("Other", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_CHROME':
                return __("Chrome", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_IE':
            case 'LABEL_BROWSER_INTERNET EXPLORER':
                return __("Internet Explorer", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_FIREFOX':
                return __("Firefox", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_EDGE':
                return __("Edge", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_ANDROID':
                return __("Android", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_IPHONE':
                return __("iPhone", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_SAFARI':
                return __("Safari", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_OPERA':
                return __("Opera", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BROWSER_BLACKBERRY':
                return __("BlackBerry", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_RESET_STATS':
                return __("Reset All Stats", 'custom-registration-form-builder-with-submission-manager');

            case 'ALERT_STAT_RESET':
                return __("You are going to delete all stats for selected form. Do you want to proceed?", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_FORM_STAT_PAGE':
                return __("Form Analytics", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_FIELD_STAT_PAGE':
                return __("Field Analytics", 'custom-registration-form-builder-with-submission-manager');

            case 'ALERT_SUBMISSIOM_LIMIT':
                return __("To fight spam admin has fixed the maximum number of submissions for this form from a single device. You can resubmit after 24 hours or you can contact the admin to reset the limit.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUB_LIMIT_ANTISPAM':
                return __("Form Submission Limit for a Device", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUB_LIMIT_ANTISPAM_HELP':
                return sprintf(__("Limits how many times a form can be submitted from a device within a day. Helpful to prevent spams. Set it to zero(0) to disable this feature. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/global-overrides/#htdevicelimit');

            case 'LABEL_FAILED_SUBMISSIONS':
                return __("Not submitted", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BANNED_SUBMISSIONS':
                return __("Banned", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_AUTO_USER_ROLE_INVALID':
                return __("Please select either Automatically Assigned WP User Role or Pick user role manually.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ALL':
                return __("All", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_WP_ROLE_LABEL_INVALID':
                return __("WP User Role Field Label is required.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_ALLOWED_ROLES_INVALID':
                return __("Please select Allowed WP Roles for Users.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ENTRY_ID':
                return __("Submission ID", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ENTRY_TYPE':
                return __("Submission Type", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_USER_NAME':
                return __("User Name", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SEND':
                return __("Send", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_AUTO_REPLY_CONTENT_INVALID':
                return __("Auto reply email body is invalid.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_AUTO_REPLY_SUBJECT_INVALID':
                return __("Auto reply email subject is invalid", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_INVITES':
                return __("Bulk Email", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_QUEUE_IN_PROGRESS':
                return __("Queue in progress", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SENT':
                return __("Sent", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STARTED_ON':
                return __("Started on", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_QUEUE_RUNNING':
                return __("This form is already processing an email queue. You cannot add another queue, until this task is finished", 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_INVITE_NO_MAIL':
                return __("No email submissions found for this form.", 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_INVITE_NO_QUEUE':
                return __("No active queue. Select a form from dropdown to send emails.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_RESET':
                return __("Reset", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_ON_FORM':
                return __("Display Price", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_REDIRECTING_TO':
                return __("Redirecting you to", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_PAYMENT_SUCCESS':
                return __("Payment Successfull", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_PAYMENT_FAILED':
                return __("Payment Failed!", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_PAYMENT_PENDING':
                return __("Payment Pending.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_PAYMENT_CANCEL':
                return __("Transaction Cancelled", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_UNIQUE_TOKEN_EMAIL':
                return __("Unique Token", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DEFAULT_SELECT_OPTION':
                return __("Please select a value", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_REMEMBER':
                return __("Remember me", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_DASHBOARD_WIDGET':
                return __('Registration Activity', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_OTP_SUCCESS':
                return __("Success! an email with one time password (OTP) was sent to your email address.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_OTP':
                return __("One Time Password", 'custom-registration-form-builder-with-submission-manager');

            case 'OTP_MAIL':
                return __("Your One Time Password is ", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_EMAIL_NOT_EXIST':
                return __("Oops! We could not find this email address in our submissions database.", 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_EMAIL':
                return __("Invalid email address. Please provide email address in a valid format.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_AFTER_OTP_LOGIN':
                return __("You have successfully logged in using OTP.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_OTP':
                return __("The OTP you entered is invalid. Please enter correct OTP code from the email we sent you, or you can generate a new OTP.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NOTE_FROM_ADMIN':
                return __(" Admin added a note for you: <br><br>", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_TITLE':
                return sprintf(__("Name of your form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings/#htformtitle');

            case 'HELP_ADD_FORM_DESC':
                return sprintf(__('Not visible on front end by default. Can be displayed using MagicWidgets. <a target="_blank" class="rm-more" href="%s">More</a>', 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings/#htformdesc');

            case 'HELP_ADD_FORM_CREATE_WP_USER':
                return sprintf(__("Selecting this will register the user in WP Users area. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/accounts/#htcreateaccount');

            case 'HELP_ADD_FORM_WP_USER_ROLE_AUTO':
                return sprintf(__("WordPress User Role that will be assigned to the user. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/accounts/#htassignrole');

            case 'HELP_ADD_FORM_WP_USER_ROLE_PICK':
                return sprintf(__("Adds a new field to the form asking users to pick a role themselves. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/accounts/#htrolechoice');

            case 'HELP_ADD_FORM_ROLE_SELECTION_LABEL':
                return __("Label of the role selection field which will appear on the form.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_ALLOWED_USER_ROLE':
                return sprintf(__("Only the checked roles will appear for selection on the form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/accounts/#htroleselect');

            case 'HELP_ADD_FORM_CONTENT_ABOVE_FORM':
                return sprintf(__("Optional rich text content that will be displayed above the form fields. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings/#htcontent');

            case 'HELP_ADD_FORM_SUCCESS_MSG':
                return sprintf(__("Display a message after the form has been successfully submitted. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/post-submission/#htsuccessmessage');

            case 'HELP_ADD_FORM_UNIQUE_TOKEN':
                return sprintf(__("A Unique Token Number/ Unique ID is assigned to the submission and also emailed to the user if auto-reply is turned on. Token number is visible in the submission records. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/post-submission/#httokennumber');

            case 'HELP_ADD_FORM_REDIRECT_AFTER_SUB':
                return sprintf(__("Redirect the user to a new page after submission (and success message). <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/post-submission/#htredirection');

            case 'HELP_ADD_FORM_REDIRECT_PAGE':
                return __("Select the page to which user is redirected after form submission.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_REDIRECT_URL':
                return __("Enter the URL where the user is redirected after form submission.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_AUTO_RESPONDER':
                return sprintf(__("Turns on auto responder email for the form. After successful submission a customizable email is sent to the user. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/auto-responder/#htautoreply');

            case 'HELP_ADD_FORM_AUTO_RESP_SUB':
                return sprintf(__("Subject of the mail sent to the user. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/auto-responder/#htsubject');

            case 'HELP_ADD_FORM_AUTO_RESP_MSG':
                return sprintf(__("Content of the email to be sent to the user. You can use rich text and values the user submitted in the form for a more personalized message. If you are creating a new form, Add Fields drop down will be empty. You can come back after adding fields to the form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/auto-responder/#htbody');

            case 'HELP_ADD_FORM_SUB_BTN_LABEL':
                return __("Label for the button that will submit the form. Leave blank for default label.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_SUB_BTN_FG_COLOR':
                return __("Color of the text inside the submit button. Leave blank for default theme colors.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_SUB_BTN_BG_COLOR':
                return __("Color of the submit button. Leave blank for default theme colors.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FIELD_ICON_BG_ALPHA':
                return __("Container Opacity", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FIELD_ICON_BG_ALPHA':
                return sprintf(__("Change the opacity of icon's container. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htbacktransparent');

            case 'HELP_ADD_FORM_MC_LIST':
                return sprintf(__("Required for connecting the form to a MailChimp List. To make it work, please set MailChimp in Global Settings &#8594; <a target='blank' class='rm_help_link' href='%s'>External Integration</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'admin.php?page=rm_options_thirdparty','https://registrationmagic.com/knowledgebase/mailchimp-integration-2/#htmclist');

            case 'HELP_ADD_FORM_MC_EMAIL':
                return __("Choose the form field which will be connected to MailChimp’s email field.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_MC_FNAME':
                return __("Choose the form field which will be connected to MailChimp’s First Name field.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_MC_LNAME':
                return __("Choose the form field which will be connected to MailChimp’s Last Name field.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_AUTO_EXPIRE':
                return sprintf(__("Select this if you want to auto unpublished the form after required number of submissions or reaching a specific date. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/limits/#htlimit');

            case 'HELP_ADD_FORM_EXPIRE_BY':
                return sprintf(__("Select the parameter for limiting visibility of the form. You can hide the form for all users by selecting Submission count or Date parameter, or you can disallow submission for specific users by choosing Custom Status parameter. Submission and Date limits can also be displayed above the form by turning on the option in Global Settings.<a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/limits/#htsetlimit');

            case 'HELP_ADD_FORM_AUTO_EXP_SUB_LIMIT':
                return sprintf(__("The form will not be visible to the user after this number is reached. However, you can reset it later for extending restrictions. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/limits/#htlimitnum');

            case 'HELP_ADD_FORM_AUTO_EXP_TIME_LIMIT':
                return sprintf(__("The last date on which this form will be visible. It will no longer accept submissions after this date. However, you can reset it later for extending limits. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/limits/#htlimitdate');

            case 'HELP_ADD_FORM_AUTO_EXP_MSG':
                return sprintf(__("User will see this message when accessing the form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/limits/#htlimitaction');

            case 'HELP_ADD_FIELD_SELECT_TYPE':
                return __("Select  or change type of the field if not already selected.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_LABEL':
                return sprintf(__("Label of the field as it appears on forms and inside user accounts. This does not apply to fields without labels like Shortcode field. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htlabel');

            case 'HELP_ADD_FIELD_PLACEHOLDER':
                return sprintf(__("This will appear inside the input box before user starts entering a value. Do not confuse it with default value. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htplaceholder');

            case 'HELP_ADD_FIELD_CSS_CLASS':
                return sprintf(__("Apply a CSS Class defined in the theme CSS file or in Appearance &#8594; Editor. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htcssclass');

            case 'HELP_ADD_FIELD_MAX_LEN':
                return sprintf(__("Maximum Allowed length (characters) of the user submitted value. Leave blank for no limit. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htmaxlength');

            case 'HELP_ADD_FIELD_IS_REQUIRED':
                return sprintf(__("Make this field mandatory to be filled. Form will show user an error if he/ she tries to submit the form without filling this field. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htisreq');

            case 'HELP_ADD_FIELD_SHOW_ON_USERPAGE':
                return sprintf(__("Display's this field's value inside RegistrationMagic's User Manager area. It also displays the value on frontend User Account area created by RegistrationMagic's shortcode. Please note, RegistrationMagic's account area is different from WordPress' user page. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htaddtouser');

            case 'HELP_ADD_FIELD_PARA_TEXT':
                return __("The text you want the user to see.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_HEADING_TEXT':
                return __("The text you want the user to see as heading.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_OPTIONS_SORTABLE':
                return sprintf(__("Options for user to choose from. Drag and drop to arrange their order inside the list. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htoptions');

            case 'HELP_ADD_FIELD_DEF_VALUE':
                return __("This option will appear selected by default when form loads.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_COLS':
                return __("Width of the text area defined in terms of columns where each column is equivalent to one character.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_ROWS':
                return __("Height of the text area defined in terms of number of text lines.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_TnC_VAL':
                return sprintf(__("Paste your terms and conditions here. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#httandc');

            case 'HELP_ADD_FIELD_FILETYPE':
                return sprintf(__("Restricts the type of file allowed to be attached. If you leave it blank, extensions defined in Global Settings --> General will be used. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htallowedfiles');

            case 'HELP_ADD_FIELD_PRICE_FIELD':
                return sprintf(__("Select the product created in &quot;Products&quot; section of RegistrationMagic. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htproduct');

            case 'HELP_ADD_FIELD_OPTIONS_COMMASEP':
                return sprintf(__("Options for drop down list. Separate multiple values with a comma(,). <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htproduct');

            case 'HELP_ADD_FIELD_BDATE_RANGE':
                return __("Enable this to force selection of date of birth from a certain range.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_PRIMARY_FIELD_EMAIL':
                return __("This is primary email field. Type of this field can not be changed.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_GEN_THEME':
                return sprintf(__("Select visual style of your forms. Classic applies a set neutral tone which looks pleasing with all kinds of WordPress themes. Match My Theme will let forms pick visual elements automatically from your active WordPress theme. When this is selected, you can also override the design of individual forms in Form Dashboard --> Build --> Design. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htformstyle');

            case 'HELP_OPTIONS_GEN_LAYOUT':
                return sprintf(__("Select the position of field labels and columns for your forms. Two column layouts will work better with themes that offer wide content area. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htlayout');

            case 'HELP_OPTIONS_GEN_FILETYPES':
                return sprintf(__('Restrict the type of files allowed to be attached to your File type fields. You will need to define extension of the file types. For multiple extensions, seperate using pipe "|". <a target=\'_blank\' class=\'rm-more\' href=\'%s\'>More</a>', 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htfiletypes');

            case 'HELP_OPTIONS_GEN_FILE_MULTIPLE':
                return sprintf(__("Allows users to attach multiple files to your single file field. %s", 'custom-registration-form-builder-with-submission-manager'),"<a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/general-settings-2/#htuploadmultifile'>More</a>");

            case 'HELP_OPTIONS_GEN_REG_URL':
                return sprintf(__("Users will be automatically redirected to this page when clicking &quot;Register&quot; links on your site. Do make sure you have a registration form inserted inside the page you select. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htdefregpage');    
            
            case 'HELP_OPTIONS_POST_SUB_REDIR':
                return sprintf(__("Choose the page you want to redirect the user to after successful login. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htloginredirect');
                
            case 'HELP_OPTIONS_ASPM_ENABLE_CAPTCHA':
                return sprintf(__("Shows recaptcha above the submit button. It verifies if the user is human before accepting submission. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/global-overrides/#htrepatcha');

            case 'HELP_OPTIONS_ASPM_SITE_KEY':
                return sprintf(__("Required to make reCAPTCHA  work. You can generate site key from <a target='blank' class='rm_help_link' href='%s'>here</a>", 'custom-registration-form-builder-with-submission-manager'),'https://www.google.com/recaptcha/');

            case 'HELP_OPTIONS_ASPM_SECRET_KEY':
                return __("Required to make reCAPTCHA  work. It will be provided when you generate site key.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_USER_AUTOGEN':
                return sprintf(__("Creates and sends the users random password instead of allowing them to set one on the form. After selecting this, password field will not appear on the forms. %s", 'custom-registration-form-builder-with-submission-manager'),"<a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/user-accounts-2/#htautogenpass'>More</a>");

            case 'HELP_OPTIONS_USER_AUTOAPPROVAL':
                return sprintf(__("Automatically activates user accounts after submission. Uncheck it if you wish to manually activate every user individually. Manual Activations can be done through User Manager, or by clicking activation link in admin email notification or setting up an Automation Task. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/global-overrides/#htautoapprove');

            case 'HELP_OPTIONS_ARESP_NOTE_NOTIFS':
                return sprintf(__("Email notification will be send to the users if you add a User Note to one of their submissions. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htnotifyuser');

            case 'HELP_OPTIONS_ARESP_ADMIN_NOTIFS':
                return sprintf(__("An email notification will be sent to Admin of this site for every form submission. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htnotifyadmin');

            case 'HELP_OPTIONS_ARESP_RESPS':
                return sprintf(__("Add people who you want to receive notifications for form submissions. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htdefinemanual');

            case 'HELP_OPTIONS_ARESP_ENABLE_SMTP':
                return __("Whether to use an external SMTP (Google, Yahoo! etc) instead of local mail server", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_ARESP_SMTP_HOST':
                return sprintf(__("Specify host address for SMTP. For example, smtp.gmail.com . <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htsmtphost');

            case 'HELP_OPTIONS_ARESP_SMTP_PORT':
                return sprintf(__("Specify port number for SMTP. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htsmtpport');

            case 'HELP_OPTIONS_ARESP_SMTP_ENCTYPE':
                return sprintf(__("Specify the type of encryption used by your SMTP service provider. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htencryttype');

            case 'HELP_OPTIONS_ARESP_SMTP_AUTH':
                return sprintf(__("Please check this if authentication is required at SMTP server. Also, provide credential in the following fields. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htauthenticate');

            case 'HELP_OPTIONS_ARESP_FROM_DISP_NAME':
                return sprintf(__("A name to identify the sender. It will be shown as &quot;From: MY Blog &lt;me@myblog.com&gt;&quot;.Use tag {{user}} to dynamically pick name from the form, if it has profile fields for name; or if the user is already registered, from his/ her WP Profile. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htsendername');

            case 'HELP_OPTIONS_ARESP_FROM_EMAIL':
                return __("The reply-to email in the header of messages that user or admin receives.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_FB_ENABLE':
                return sprintf(__("Adds 'Login Using Facebook' button to the login form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/external-integrations/#htenablefb');

            case 'HELP_OPTIONS_THIRDPARTY_FB_SECRET':
                return __("To make Facebook login work, you’ll need an App Secret. It will be provided when you generate and App ID.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_FB_APPID':
                return sprintf(__("More information about Facebook App ID <a target='blank' class='rm_help_link' href='%s'>Here</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://developers.facebook.com/docs/apps/register','https://registrationmagic.com/knowledgebase/external-integrations/#htenablefb');

            case 'HELP_OPTIONS_THIRDPARTY_MC_ENABLE':
                return sprintf(__("This will allow you to fetch your MailChimp lists in Form Dashboard --> Integrate --> MailChimp and map selective fields to your MailChimp fields. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/mailchimp-integration-2/#htmcintegration');

            case 'HELP_OPTIONS_THIRDPARTY_MC_API':
                return sprintf(__("This will allow you to fetch your MailChimp lists in Form Dashboard --> Integrate --> MailChimp and map selective fields to your MailChimp fields. More information <a target='blank' class='rm_help_link' href='%s'>HERE</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'http://kb.mailchimp.com/accounts/management/about-api-keys','https://registrationmagic.com/knowledgebase/external-integrations/#htenablemailchimp');
                
            case 'HELP_OPTIONS_THIRDPARTY_MC_DBL_OPTIN':
                return sprintf(__("Enabling this option will add users to your MailChimp lists with a 'Pending' status. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/external-integrations/#htenablemailchimp');

            case 'HELP_OPTIONS_PYMNT_PROCESSOR':
                return sprintf(__("Select all the payment system(s) you want to use for accepting payments. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/payments/#htpayprocessor');

            case 'HELP_OPTIONS_PYMNT_TESTMODE':
                return sprintf(__("This will put RegistrationMagic payments on test mode. Useful for testing and troubleshooting payment system. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/payments/#htenabletestmode');

            case 'HELP_OPTIONS_PYMNT_PP_EMAIL':
                return sprintf(__("Your PayPal account email, to which the payments will be sent. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/payments/#htppemail');

            case 'HELP_OPTIONS_PYMNT_CURRENCY':
                return sprintf(__("Default Currency for accepting payments. Usually, this will be default currency in your PayPal,Stripe or Authorize.Net account. Please visit this %s to check currencies supported by Authorize.net. Make sure the currency you select is supported by the payment processor(s) you are using. Not all currencies work well with all payment processor. %s", 'custom-registration-form-builder-with-submission-manager'),"<a target='_blank' href='https://support.authorize.net/authkb/index?page=content&id=A414'>LINK</a>","<a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/payments/#htdefcurrency'>More</a>");

            case 'HELP_OPTIONS_PYMNT_PP_PAGESTYLE':
                return sprintf(__("Optional checkout page style. Checkout page styles are created in your PayPal account for customized checkout experience. More information %s", 'custom-registration-form-builder-with-submission-manager'),"<a target='_blank' href='https://www.paypal.com/ca/cgi-bin/webscr?cmd=p/pop/cowp_overview'>HERE</a>. <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/payments/#htpppagestyle'>More</a>");

            case 'HELP_ADD_PRICE_FIELD_LABEL':
                return sprintf(__("This name will not be visible when you will add product in a form. If you wish to show this name on the form, make sure while adding this product to a form you enter same field label as the product name. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/add-product/#htprodname');

            case 'HELP_ADD_PRICE_FIELD_SELECT_TYPE':
                return __("Define how the product will be priced.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_INVITES_SUB':
                return sprintf(__("Subject for the message you are sending to the users. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/bulk-email/#htsubject');

            case 'HELP_OPTIONS_INVITES_BODY':
                return sprintf(__("Content of the message your are sending to the users of selected form. You can use values from form fields filled by the users from &quot;Add Fields&quot; dropdown for personalized message. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/bulk-email/#htbody');

            //Admin menus
            case 'ADMIN_MENU_REG':
                return __("RegistrationMagic", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_NEWFORM':
                return __("New Form", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_NEWFORM_PT':
                return __("New Form", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTINGS':
                return __("Global Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SUBS':
                return __("Inbox", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_FORM_STATS':
                return __("Form Analytics", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_FIELD_STATS':
                return __("Field Analytics", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_PRICE':
                return __("Products", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_ATTS':
                return __("Attachments", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_INV':
                return __("Bulk Email", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_USERS':
                return __("User Manager", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_ROLES':
                return __("User Roles", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SUPPORT':
                return __("Support", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_GEN_PT':
                return __("General Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_AS_PT':
                return __("Security Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_UA_PT':
                return __("User Account Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_AR_PT':
                return __("Auto Responder Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_TP_PT':
                return __("Third Party Integration Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_PP_PT':
                return __("Payment Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_SAVE_PT':
                return __("Save Settings", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_ADD_NOTE_PT':
                return __("Add Note", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_MNG_FIELDS_PT':
                return __("Manage Form Fields", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_ADD_FIELD_PT':
                return __("Add Field", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_ADD_PP_FIELD_PT':
                return __("Add PayPal Field", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_PP_PROC_PT':
                return __("PayPal processing", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_ATT_DL_PT':
                return __("Attachment Download", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_VIEW_SUB_PT':
                return __("View Submission", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_USER_ROLE_DEL_PT':
                return __("User Role Delete", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_REG_PT':
                return __("Registrant", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_LOST_PASS':
                return __("Lost your password?", 'custom-registration-form-builder-with-submission-manager');

            case 'SUPPORT_PAGE_NOTICE':
                return sprintf(__("Note: If you wish to roll back to earlier version of RegistrationMagic due to broken upgrade, please <a href='%s'>go here</a>. You will need to deactivate or uninstall this version and reinstall version 2.5. No data will be lost. If you want to resolve any issue with version 3.0, please use one of the links below to contact support.", 'custom-registration-form-builder-with-submission-manager'),'http://registrationmagic.com/free/');

            case 'LABEL_MY_DETAILS':
                return __("Personal Details", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADMIN_NOTES':
                return __("Admin Notes", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_GEN_PROGRESS_BAR':
                return sprintf(__('Shows form filling status above the form when Limits are turned on. For example, "2 out 50 registrations complete" or "2 days to go before registration ends". <a target="_blank" class="rm-more" href="%s">More</a>', 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/global-overrides/#htexpirecount');

            case 'HELP_OPTIONS_GEN_PROGRESS_BAR':
                return sprintf(__('Shows form filling status above the form when Limits are turned on. For example, "2 out 50 registrations complete" or "2 days to go before registration ends". <a target="_blank" class="rm-more" href="%s">More</a>', 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/global-overrides/#htexpirecount');

            case 'MSG_REQUIRED_FIELD':
                return __("This is a required field", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_USER_SEND_PASS':
                return sprintf(__("Send users an email with their selected username and password after successful registration. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/user-accounts-2/#htuserpassemail');

            case 'MSG_CREATE_PRICE_FIELD':
                return __("First Create a product from Products > Add New", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EXPORT_TO_URL_CB':
                return __("Send Submitted Data to External URL", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EXPORT_URL':
                return __("URL", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SEND_SUB_TO_URL':
                return __("URL to the script on external server which will handle the data", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SEND_SUB_TO_URL_CB':
                return sprintf(__("Pushes submitted data to external server page using HTTP POST protocol. This could be useful for maintaining another database for submissions. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/post-submission/#htexternalurl');

            case 'ADMIN_SUBMENU_REG':
                return __("All Forms", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STRIPE_API_KEY' :
                return __("Stripe API Key", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STRIPE_PUBLISH_KEY' :
                return __("Stripe Publishable Key", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_PYMNT_STRP_API_KEY' :
                return sprintf(__("Secret and publishable keys are used to identify your Stripe account. You can grab the test and live API keys for your account under <a href='%s' target='blank'>Your Account > API Keys</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://dashboard.stripe.com/account/apikeys','https://registrationmagic.com/knowledgebase/payments/#htstripeapi');

            case 'SELECT_FIELD_FIRST_OPTION':
                return __("Select an option", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_CLICK_TO_REVIEW' :
                return __("Click here to review", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_LIKED_RM' :
                return __("Liked <span class='rm-brand'>RegistrationMagic </span>so far? Please rate it <span class='rm-bold'> 5 stars</span> on wordpress.org and help us keep it going!", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT_PAYMENT_METHOD':
                return __("Select a payment method", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STRIPE_CARD_NUMBER':
                return __("Card Number", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STRIPE_CARD_MONTH':
                return __("Month", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STRIPE_CARD_YEAR':
                return __("Year", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_STRIPE_CARD_CVC':
                return __("CVC/CVV", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CUSTOM_RANGE':
                return __("Specific Period", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CUSTOM_RANGE_FROM_DATE':
                return __("From", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CUSTOM_RANGE_UPTO_DATE':
                return __("Up to", 'custom-registration-form-builder-with-submission-manager');

            case 'CRIT_ERROR_TITLE':
                return __("Uh, oh! Looks like we've hit a road block", 'custom-registration-form-builder-with-submission-manager');

            case 'CRIT_ERROR_SUBTITLE':
                return __("Following requirement(s) are not met, I can not continue. :(", 'custom-registration-form-builder-with-submission-manager');

            case 'CRIT_ERR_XML':
                return __("PHP extension SimpleXML is not enabled on server.", 'custom-registration-form-builder-with-submission-manager');

            case 'CRIT_ERR_MCRYPT':
                return __("PHP extension mcrypt is not enabled on server.", 'custom-registration-form-builder-with-submission-manager');

            case 'CRIT_ERR_PHP_VERSION':
                return __("This plugin requires atleast php version 5.3. Older version found.", 'custom-registration-form-builder-with-submission-manager');

            case 'ERROR_NA_SEND_TO_URL_FEAT':
                return __("Feature not available. PHP extension CURL is not enabled on server.", 'custom-registration-form-builder-with-submission-manager');

            case 'RM_ERROR_EXTENSION_CURL':
                return __("PHP extension CURL is not enabled on server. Following features will not be available:<ul style=\"padding-left:25px;list-style-type:disc;margin-top:0px;\"><li>Facebook Integration</li><li>Mailchimp Integration</li><li>Stripe Payment</li><li>Export submission to external URL</li></ul>", 'custom-registration-form-builder-with-submission-manager');

            case 'RM_ERROR_EXTENSION_ZIP':
                return __("PHP extension ZIP is not enabled on server. Following features will not be available:<ul style=\"padding-left:25px;list-style-type:disc;margin-top:0px;\"><li>Downloading multiple attachments as zip</li></ul>", 'custom-registration-form-builder-with-submission-manager');


            case 'NEWSLETTER_SUB_MSG':
                return __("<span class='rm-newsletter-button'><a href='javascript:void(0)' onclick='handle_newsletter_subscription_click(\"" . self::get('MSG_NEWSLETTER_SUBMITTED') . "\")'> Click here</a></span> to keep up with breakthroughs and innovations we are bringing to WordPress registration system.", 'custom-registration-form-builder-with-submission-manager');

            case 'MAIL_ACTIVATE_USER_DEF_SUB':
                return __("Activate User", 'custom-registration-form-builder-with-submission-manager');

            case 'MAIL_NEW_USER1' :
                return __("A new user has been registered on {{SITE_NAME}}", 'custom-registration-form-builder-with-submission-manager');

            case 'MAIL_NEW_USER2' :
                return __("Please click on the button below to activate the user.", 'custom-registration-form-builder-with-submission-manager');

            case 'MAIL_NEW_USER3' :
                return __("If the above button is not working you can paste the following link to your browser", 'custom-registration-form-builder-with-submission-manager');

            case 'ACT_AJX_FAILED_DEL' :
                return __("Failed to upadte user information.Can not activate user", 'custom-registration-form-builder-with-submission-manager');

            case 'ACT_AJX_ACTIVATED' :
                return __("You have successfully activated the user.", 'custom-registration-form-builder-with-submission-manager');

            case 'ACT_AJX_ACTIVATED2' :
                return __("If the user is activated by mistake or you do not want to activate the user you can deactivate the user using dashboard.", 'custom-registration-form-builder-with-submission-manager');

            case 'ACT_AJX_ACTIVATE_FAIL' :
                return __("Unable to activate the user. Try activating the user using your dashboard.", 'custom-registration-form-builder-with-submission-manager');

            case 'ACT_AJX_NO_ACCESS' :
                return __("You are not authorized to perform this action.", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_MAP' :
                return __("Map", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ST_ADDRESS' :
                return __("Street Address", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADDR_CITY' :
                return __("City", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADDR_STATE' :
                return __("State", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADDR_COUNTRY' :
                return __("Country", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADDR_ZIP' :
                return __("Zip Code", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_ADDRESS' :
                return __("Address", 'custom-registration-form-builder-with-submission-manager');

            case 'PH_ENTER_ADDR':
                return __("Enter your address", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_GOOGLE_API_KEY':
                return __("Google Maps API Key", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_GGL_API':
                return sprintf(__("You will need a Google maps API Key to make 'Map' and 'Address' type fields work with Google Maps. To generate an API Key <a target='blank' class='rm_help_link' href='%s'>CLICK HERE</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend&keyType=CLIENT_SIDE&reusekey=true','https://registrationmagic.com/knowledgebase/external-integrations/#htenablegmaps');

            case 'MSG_FRONT_NO_GOOGLE_API_KEY':
                return __("No Google Maps API configured.Please set a valid API Key from RegistrationMagic &#8594; Global Settings &#8594; EXTERNAL INTEGRATION.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_RM_NO_API_NOTICE':
                return __("Google Maps API Keys are required for this field.Please make sure you have configured a valid API key in Global Settings <span>&#8594;</span> EXTERNAL INTEGRATION.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NEWSLETTER_SUBMITTED':
                return __("Congratulations! You have subscribed the newsletter successfully.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_USER_DELETED':
                return __("</em>User Deleted</em>", 'custom-registration-form-builder-with-submission-manager');

            case 'ERR_SESSION_DIR_NOT_WRITABLE':
                return __('Session directory is not writable, please contact your server support to enable write permission to following directory: <br>%s', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_GET_EMBED':
                return __('Get form embed code', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_BANNED':
                return __("Access Denied", 'custom-registration-form-builder-with-submission-manager');

            case 'MAIL_ACCOUNT_ACTIVATED' :
                return sprintf(__('Hi,<br/><br/> Thank you for registering with <a href="%s">%s</a>. Your account is now active.<br/><br/>Regards.', 'custom-registration-form-builder-with-submission-manager'),'{{SITE_URL}}','{{SITE_NAME}}');

            case 'MAIL_ACOOUNT_ACTIVATED_DEF_SUB' :
                return __('Account Activated', 'custom-registration-form-builder-with-submission-manager');

            case 'VALIDATION_ERROR_IP_ADDRESS':
                return __("Only numbers, dot(.) and wildcard(?) are allowed.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BAN_EMAIL_HELP':
                return sprintf(__("Enter Email Address to ban. Separated multiple addresses by empty space. Wildcard(* and ?) allowed. For example: joh*@gmail.com will ban all submissions done using gmail domain and start with 'joh'. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/security-settings-2/#htbanemail');
                
            case 'LABEL_BAN_IP_HELP':
                return sprintf(__("Enter IP Address to ban. Separated multiple addresses by empty space. Wildcard(?) allowed (for IPv4 addresses only). For example: 127.233.12?.01? will ban all IPs from 127.233.120.010 to 127.233.129.019. This also applies to login form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/security-settings-2/#htbanip');

            case 'LABEL_BAN_IP':
                return __("Banned IP Addresses from Accessing Forms", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BAN_EMAIL_HELP':
                return sprintf(__("Enter Email Address to ban. Separated multiple addresses by empty space. Wildcard(* and ?) allowed. For example: joh*@gmail.com will ban all submissions done using gmail domain and start with 'joh'. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/security-settings-2/#htbanemail');

            case 'LABEL_BAN_USERNAME':
                return __("Blacklisted/ Reserved Usernames", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BAN_USERNAME_HELP':
                return sprintf(__("User will not be able to register using these Usernames. Separate multiple usernames using space or new line (Enter). <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/security-settings-2/#htblacklist');

            case 'LABEL_BAN_USERNAME_MSG':
                return __("This username can not be used", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BAN_EMAIL':
                return __("Banned Email Addresses from Submitting Forms", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IS_REQUIRED_SCROLL':
                return __("Scrolling T&C is required", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_REQUIRED_SCROLL':
                return __("Force user to scroll past complete T&C before accepting.", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_CHECK_ABOVE_TC':
                return __("Show checkbox above T&C", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_CHECK_ABOVE_TC':
                return __("Enable this option to show the acceptance checkbox above the T&C textbox.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LOGOUT':
                return __("Logout", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_CONF':
                return __("Form Configuration", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_GEN_SETT':
                return __("General Settings", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_VIEW_SETT':
                return __("Edit Design", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_ACC_SETT':
                return __("Accounts", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_PST_SUB_SETT':
                return __("Post Submission", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_AUTO_RESP_SETT':
                return __("Auto Responder", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_LIM_SETT':
                return __("Limits", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_MC_SETT':
                return __("MailChimp", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_ACTRL_SETT':
                return __("Access Control", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_GEN_SETT_DESC':
                return __("Name, description and general content", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_VIEW_SETT_DESC':
                return __("Personalize this form and make it your own!", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_ACC_SETT_DESC':
                return __("Define user account and role behavior", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_PST_SUB_SETT_DESC':
                return __("Success message, redirections and external submissions", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_AUTO_RESP_SETT_DESC':
                return __("Define auto responder settings with mail merge", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_LIM_SETT_DESC':
                return __("Limit form submissions based specific conditions and message", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_F_MC_SETT_DESC':
                return __("MailChimp Integration with advanced field mapping", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_ACTRL_SETT_DESC':
                return __("Form access restrictions based on date, passphrase and role.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_MC_KEY_NO_SET':
                return __("Mailchimp is not configured to work with this form. Please enter a valid mailchimp API key in Global Settings&#10148;External Integration&#10148;Mailchimp Api Key", 'custom-registration-form-builder-with-submission-manager');
            case 'MSG_FS_NOT_AUTHORIZED' :
                return __("You are not authorized to see this page.", 'custom-registration-form-builder-with-submission-manager');
            case 'SELECT_FIELD' :
                return __("Select a field.", 'custom-registration-form-builder-with-submission-manager');
            case 'SELECT_LIST' :
                return __("Select a list.", 'custom-registration-form-builder-with-submission-manager');

            case 'NOTICE_SILVER_ACTIVATION':
                return __("RegistrationMagic Premium edition is already activated. Please disable Premium to activate Silver edition.", 'custom-registration-form-builder-with-submission-manager');

            case 'NOTICE_BASIC_ACTIVATION':
                return __("RegistrationMagic Premium edition is already activated. Please disable Premium to activate Basic edition.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_EXPIRED':
                return __("Expired", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_EXPIRES_ON':
                return __("Expires on", 'custom-registration-form-builder-with-submission-manager');

            case 'NOTE_DEFAULT_FORM':
                return __("To make this your default user registration form, Enable WordPress User Account Creation in Form Dashboard --> General Settings", 'custom-registration-form-builder-with-submission-manager');
   
            case 'LABEL_FORM_EXPIRES_IN':
                return __("Expires in %d days", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_HELP_TEXT':
                return __("Hover Text", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_HELP_TEXT':
                return sprintf(__("This is displayed inside a fade-in tooltip box to the users when they brings cursor above a field. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#hthelptext');

            case 'LABEL_ENABLE_PW_RESTRICTIONS':
                return __("Enable Password Rules", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_RESTRICTIONS':
                return __("Password Rules", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_RESTS_PWR_UC':
                return __("Must contain an uppercase letter", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_PHONE':
                return __("Phone Number", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_PASSWORD':
                return __("Masked Field", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_NICKNAME':
                return __("Nick Name", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_BDATE':
                return __("Birth Date", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_SEMAIL':
                return __("Secondary email", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_GENDER':
                return __("Gender", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_LANGUAGE':
                return __("Language", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_IS_REQUIRED_RANGE':
                return __("Limited range of birth date", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_IS_REQUIRED_MAX_RANGE':
                return __("Maximum Date", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_IS_REQUIRED_MIN_RANGE':
                return __("Minimum Date", 'custom-registration-form-builder-with-submission-manager');
            case 'TEXT_RANGE':
                return __("Range", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_SECEMAIL':
                return __("Secondary Email", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_FACEBOOK':
                return __("Facebook", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_TWITTER':
                return __("Twitter", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_GOOGLE':
                return __("Google+", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_INSTAGRAM':
                return __("Instagram", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_LINKED':
                return __("LinkedIn", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_YOUTUBE':
                return __("Youtube", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_VKONTACTE':
                return __("VKontacte", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_SKYPE':
                return __("Skype Id", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_SOUNDCLOUD':
                return __("SoundCloud", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_TIME':
                return __("Time", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_IMAGE':
                return __("Image upload", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_MOBILE':
                return __("Mobile Number", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_SHORTCODE':
                return __("Shortcode", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_DIVIDER':
                return __("Divider", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_SPACING':
                return __("Spacing", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_MULTI_DROP_DOWN':
                return __("Multi-Dropdown", 'custom-registration-form-builder-with-submission-manager');
            case 'FIELD_TYPE_RATING':
                return __("Rating", 'custom-registration-form-builder-with-submission-manager');
            case 'FACEBOOK_ERROR':
                return __("Incorrect Format of Facebook Url", 'custom-registration-form-builder-with-submission-manager');
            case 'TWITTER_ERROR':
                return __("Incorrect Format of twitter Url", 'custom-registration-form-builder-with-submission-manager');
            case 'PHONE_ERROR':
                return __("Incorrect Format of Phone Number", 'custom-registration-form-builder-with-submission-manager');
            case 'GOOGLE_ERROR':
                return __("Incorrect Format of Google plus Url", 'custom-registration-form-builder-with-submission-manager');
            case 'INSTAGRAM_ERROR':
                return __("Incorrect Format of Instagram Url", 'custom-registration-form-builder-with-submission-manager');
            case 'LINKED_ERROR':
                return __("Incorrect Format of LinkedIn Url", 'custom-registration-form-builder-with-submission-manager');
            case 'YOUTUBE_ERROR':
                return __("Incorrect Format of Youtube Url", 'custom-registration-form-builder-with-submission-manager');
            case 'VKONTACTE_ERROR':
                return __("Incorrect Format of Vkontacte Url", 'custom-registration-form-builder-with-submission-manager');
            case 'SKYPE_ERROR':
                return __("Incorrect Format of Skype Id", 'custom-registration-form-builder-with-submission-manager');
            case 'SOUNDCLOUD_ERROR':
                return __("Incorrect Format of Sound cloud url", 'custom-registration-form-builder-with-submission-manager');
            case 'MOBILE_ERROR':
                return __("Incorrect Format of Mobile Number", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_TIME_ZONE':
                return __("Timezone", 'custom-registration-form-builder-with-submission-manager');
            case 'HELP_ADD_FIELD_TIME_ZONE':
                return __("Timezone for the field.", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_SHORTCODE_TEXT':
                return __("Shortcodes", 'custom-registration-form-builder-with-submission-manager');
            case 'HELP_ADD_FIELD_SHORTCODE_TEXT':
                return sprintf(__("Enter Shortcode here. Only single shortcode is supported. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htshortcode');

            case 'FIELD_TYPE_WEBSITE':
                return __("Website", 'custom-registration-form-builder-with-submission-manager');
            case 'WEBSITE_ERROR':
                return __("Incorrect Format of Website Url", 'custom-registration-form-builder-with-submission-manager');
            case 'HELP_ADD_FIELD_IS_SHOW_ASTERIX':
                return __("Hide the red Asterisk(*) besides the label. Useful for marking required fields.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IS_SHOW_ASTERIX':
                return __("Hide Asterisk", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_RESTS_PWR_NUM':
                return __("Must contain a number", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_RESTS_PWR_SC':
                return __("Must contain a special character", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_RESTS_PWR_MINLEN':
                return __("Minimum length", 'custom-registration-form-builder-with-submission-manager');

            case 'ERR_TITLE_CSTM_PW':
                return __("Error: Password must follow these rules:", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_MINLEN_ERR':
                return __("Must not be shorter than %d characters", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_MAXLEN_ERR':
                return __("Must not be longer than %d characters", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PW_RESTS_PWR_MAXLEN':
                return __("Maximum length", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_CUSTOM_PW_RESTS':
                return sprintf(__("Force custom rules for password that user choose during registration. Does not applies on auto-generated passwords. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/security-settings-2/#htenablepassrules');

            case 'LABEL_RESET_PASS':
                return __("Reset Password", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_OLD_PASS':
                return __("Old Password", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NEW_PASS':
                return __("New Password", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NEW_PASS_AGAIN':
                return __("Confirm new password", 'custom-registration-form-builder-with-submission-manager');

            case 'ERR_PASS_DOES_NOT_MATCH':
                return __("New password can not be confirmed.", 'custom-registration-form-builder-with-submission-manager');

            case 'ERR_WRONG_PASS':
                return __("Password you have entered is incorrect.", 'custom-registration-form-builder-with-submission-manager');

            case 'PASS_RESET_SUCCESSFUL':
                return __("Your password has been reset successfully. Redirecting you to the login page...", 'custom-registration-form-builder-with-submission-manager');

            case 'ACCOUNT_NOT_ACTIVE_YET':
                return __("Your account is not active yet.", 'custom-registration-form-builder-with-submission-manager');

            case 'LOGIN_AGAIN_AFTER_RESET':
                return __("Please login again with your new password.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ERROR':
                return __("ERROR", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LOGIN_ERROR':
                return __("The password you entered is incorrect.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUB_PDF_HEADER_IMG':
                return __("Logo on Submission PDF Header", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUB_PDF_HEADER_TEXT':
                return __("Logo Text", 'custom-registration-form-builder-with-submission-manager');

            case 'SUB_PDF_HEADER_IMG_HELP':
                return sprintf(__("You can brand Submissions PDFs with your business logo. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htpdflogo');

            case 'SUB_PDF_HEADER_TEXT_HELP':
                return sprintf(__("Adds a line of text under your logo on Submission PDF header (use as a note or part of your branding). <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htlogotext');

            case 'LABEL_ACTRL_DATE_CB':
                return __("Enable date based form access control", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_PASS_CB':
                return __("Enable passphrase based form access control", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_ROLE_CB':
                return __("Enable user role based form access control", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FORM_ACTRL_DATE':
                return sprintf(__("User will be asked to input a date before accessing form. Useful for setting age based restrictions. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htaccesscontrol');

            case 'HELP_FORM_ACTRL_PASS':
                return sprintf(__("Users will be asked to enter a passphrase before accessing form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htenablepassphrase');

            case 'HELP_FORM_ACTRL_ROLE':
                return sprintf(__("Only users with specified roles will be able to view form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htenableuserrole');

            case 'HELP_FORM_ACTRL_DATE_QSTN':
                return sprintf(__("This question will be asked to user for entering a date. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htquestiondate');

            case 'HELP_FORM_ACTRL_PASS_QSTN':
                return sprintf(__("This question will be asked to user for entering passphrase. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htquestionpassphrase');

            case 'LABEL_ACTRL_DATE_QUESTION_DEF':
                return __("Enter your date of birth", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_PASS_QUESTION_DEF':
                return __("Enter the secret code", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FORM_ACTRL_DATE_QSTN':
                return sprintf(__("This question will be asked to user for entering a date. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htquestiondate');

            case 'HELP_FORM_ACTRL_PASS_QSTN':
                return sprintf(__("This question will be asked to user for entering passphrase. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htquestionpassphrase');

            case 'LABEL_ACTRL_DATE_TYPE':
                return __("Limit type", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_DATE_LLIMIT':
                return __("Lower limit", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_DATE_ULIMIT':
                return __("Upper limit", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_DATE_TYPE_DIFF':
                return __("Age limit", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_DATE_TYPE_DATE':
                return __("Absolute dates", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FORM_ACTRL_DATE_TYPE':
                return sprintf(__("Type of the limits. User entered date must fall into the given date range or age range. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htlimittype');

            case 'HELP_FORM_ACTRL_ROLE_ROLES':
                return sprintf(__("Only users with these roles will be able to access form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htselectuserrole');

            case 'LABEL_ACTRL_ROLE_ROLES':
                return __("Select User Roles", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_PASS_PASS':
                return __("Passphrase", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FORM_ACTRL_PASS_PASS':
                return sprintf(__("The passphrase/secret code that user must enter to access the form. Separate multiple passphrases with pipe (|). <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htpassphrase');

            case 'MSG_INVALID_ACTRL_DATE_TYPE':
                return __("Invalid date limit type.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_ACTRL_DATE_LIMIT':
                return __("Atleast one limit must be input", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_ACTRL_PASS_PASS':
                return __("Passphrase can not be empty", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_ACTRL_ROLES':
                return __("No user-roles selected.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTRL_FAIL_MSG':
                return __("Access Denied Note", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FORM_ACTRL_FAIL_MSG':
                return sprintf(__("If users are not authorised to access the form, they will see this message instead of the form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/progress-access-control/#htaccessdenied');

            case 'LABEL_ACTRL_FAIL_MSG_DEF':
                return __("Sorry, you are not authorised to access this content.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FIELD_ICON_CHANGE':
                return __("Change", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FIELD_ICON':
                return __("Label Icon", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FIELD_ICON':
                return sprintf(__("Display an icon before the label of this field. You can style the icon below. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#hticon');

            case 'LABEL_FIELD_ICON_FG_COLOR':
                return __("Icon color", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FIELD_ICON_FG_COLOR':
                return sprintf(__("Color of the icon. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#hticoncolor');

            case 'LABEL_FIELD_ICON_BG_COLOR':
                return __("Icon Container", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FIELD_ICON_BG_COLOR':
                return sprintf(__("Background color of the icon's container. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htbackcolor');

            case 'LABEL_FIELD_ICON_SHAPE':
                return __("Container Shape", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FIELD_ICON_CLOSE':
                return __("Close", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FIELD_ICON_SHAPE':
                return sprintf(__("Define shape of the icon's container. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htshape');

            case 'FIELD_ICON_SHAPE_SQUARE':
                return __("Square", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_ICON_SHAPE_ROUND':
                return __("Round", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_ICON_SHAPE_STICKER':
                return __("Sticker", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_CAN_NOT_SAVE_FS_VIEW_AJX':
                return __("No data to be saved.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CLICK_HERE':
                return __("Click Here", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_REGISTER':
                return __("Register", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FLOATING_ICON_BCK_COLOR':
                return __("Floating Icon Background Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_FLOATING_ICON':
                return __("Turn on MagicPopup System", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SHOW_FLOATING_ICON':
                return sprintf(__("Makes it easier for you to let your users to sign in, register and access their data WITHOUT going through process of setting up shortcodes and custom menu links! <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/magic-popup-button/#htmagicpopup');

            case 'HELP_FLOATING_ICON_BCK_COLOR':
                return __("Define accent of the front end buttons and panels. Match it to your theme or contrast it for better visibility. This can be edited live by visiting the front end!", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ICON':
                return __("Icon", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TEXT':
                return __("Text", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BOTH':
                return __("Both", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_FLOATING_BUTTON_AS':
                return __("Show floating button as", 'custom-registration-form-builder-with-submission-manager');

           
            case 'LABEL_FLOATING_BUTTON_TEXT':
                return __("Floating Button Text", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_FRONTEND':
                return __("Frontend", 'custom-registration-form-builder-with-submission-manager');

            case 'NO_DEFAULT_FORM':
                return __("No Registration form is selected.<br/>(Click on the star on form card to select)", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_PLEASE_LOGIN_FIRST':
                return __("Please login to view this page.", 'custom-registration-form-builder-with-submission-manager');

            case 'INFO_USERS_SELECTED_FOR_MAIL':
                return __('This Message will be sent to  <b>&nbsp%d users&nbsp</b> who have filled the form ', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AUTO_LOGIN':
                return __("Force Login after Registration", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_AUTO_LOGIN':
                return sprintf(__("User will be logged in automatically on next page refresh after successfull account creation. You may set up auto-redirect after submission. Note that it will work only if auto-approval is enabled. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/accounts/#htforcelogin');
            
            case 'TITLE_EDIT_NOTE_PAGE':
                return __("Edit Note", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_EDIT_NOTE_PAGE':
                return __("New Note", 'custom-registration-form-builder-with-submission-manager');


            case 'FIELD_HELP_TEXT_Textbox':
                return __('Simple single line text field.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_HTMLP':
                return __('This is a read only field which can be used to display formatted content inside the form. HTML is supported.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_HTMLH':
                return __('Large size read only text useful for creating custom headings.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Select':
                return __('Allows user to choose a value from multiple predefined options displayed as drop down list.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Radio':
                return __('Allows user to choose a value from multiple predefined options displayed as radio boxes.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Textarea':
                return __('This allows user to input multiple lines of text as value.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Checkbox':
                return __('Allows user to choose more than one value from multiple predefined options displayed as checkboxes.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_jQueryUIDate':
                return __('Allows users to pick a date from graphical calendar or enter manually.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Email':
                return __('An additional email field. Please note, primary email field always appears in the form and cannot be removed.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Number':
                return __('Allows user to input value in numbers.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Country':
                return __('A drop down list of all countries appears to the user for selection.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Timezone':
                return __('A drop down list of all time-zones appears to the user for selection.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Terms':
                return __('Useful for adding terms and conditions to the form. User must select the check box to continue with submission if you select “Is Required” below.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_File':
                return __('Display a field to the user for attaching files from his/ her computer to the form.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Price':
                return __('Adds product to the form. Products are separately defined in &quot;Products&quot; section of RegistrationMagic. This field type allows you to insert one of the products defined there.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Repeatable':
                return __('Allows user to add extra text field boxes to the form for submitting different values. Useful where a field requires multiple user input  values. ', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Map':
                return __('Displays a Map on the form with ability to search and mark an address.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Address':
                return __('Address field with various configuration options. Turning on Google Maps support will allow users to fetch and autofill address fields from Google Maps.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Fname':
                return __('This field is connected directly to WordPress’ User area First Name field. ', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Lname':
                return __('This field is connected directly to WordPress’ User area Last Name field. ', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_BInfo':
                return __('This field is connected directly to WordPress’ User area Bio field. It allows inserting multiple lines of text. ', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Phone':
                return __('Adds a phone number field.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Mobile':
                return __('Adds a Mobile number field', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Password':
                return __('Add a field that masks entered value like password.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Nickname':
                return __('A Nickname field bound to WordPress’ default User field with same name.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Bdate':
                return __('A speciality date field that records date of birth', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_SecEmail':
                return __('A secondary email field, it will displayed on the user profile page.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Gender':
                return __('Gender/ Sex selection radio box', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Language':
                return __('Adds a drop down language selection field with common languages as options', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Facebook':
                return __('A speciality URL field for asking Facebook Profile page', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Twitter':
                return __('A speciality URL field for asking Twitter page', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Google':
                return __('A speciality URL field for asking Google+ Profile page', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Linked':
                return __('A speciality URL field for asking LinkedIn Profile page', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Youtube':
                return __('A speciality URL field for asking YouTube Channel or Video page', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_VKontacte':
                return __('A speciality URL field for asking VKontacte page', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Instagram':
                return __('Asks User his/ her Instagram Profile', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Skype':
                return __('Asks User his/ her Skype ID', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_SoundCloud':
                return __('A speciality URL field for asking SoundClound URL', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Time':
                return __('A field for entering time', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Image':
                return __('A speciality file upload field optimized for image upload', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Shortcode':
                return __('You can use this field to enter a WordPress plugin shortcode. ShortCode will be parsed and rendered automatically inside the form.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Divider':
                return __('Divider for separating fields.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Spacing':
                return __('Useful for adding space between fields', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Multi-Dropdown':
                return __('A dropdown field with a twist. Users can now select more than one option.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_PRESENTATION':
                return __("Design", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Rating':
                return __('A rating field asking users to rate something. It displays a stars that can be clicked from 1 star to 5 stars.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_HELP_TEXT_Website':
                return __('A website URL field bound to WordPress’ default User field with same name.', 'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_HELP_TEXT_Privacy':
                return __('Specify the type of field to be displayed on the form.', 'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_HELP_TEXT_WCBillingPhone':
                return __('This is Woocommerce Billing Phone field. Type of this field can not be changed.', 'custom-registration-form-builder-with-submission-manager');
            ////////////////

            case 'LABEL_ADD_NEW_PRICE_FIELD':
                return __("New Product", 'custom-registration-form-builder-with-submission-manager');

            case 'MULTIPAGE_DEGRADE_WARNING':
                return __('This is a multi-page form created with Premium edition. Editing this form in this edition might cause issues!', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_WELCOME':
                return __("Welcome", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SWITCH':
                return __("Switch", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LIGHT':
                return __("Light", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DARK':
                return __("Dark", 'custom-registration-form-builder-with-submission-manager');

            case 'DISCLAIMER_FORM_VIEW_SETTING':
                return __("<b>Note: This is not a 100% accurate representation of how the form will appear on the front end.<br>Front end presentation is influenced by multiple factors including your theme’s CSS.</b>", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_FIELDS':
                return __("Custom Fields", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_FIELDS_DESC':
                return __("Add, edit or modify various custom fields in this form", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IMPORT':
                return __("Import", 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_CUSTOM':
                return __("Custom field", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_CONSTANT_CONTACT_OPTION':
                return __("Constant Contact Integration", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_ACTRL_CC_DESC':
                return __(" Constant Contact Integration with advanced field mapping.(Available in Premium bundle)", 'custom-registration-form-builder-with-submission-manager');
            case 'LABEL_AWEBER_OPTION':
                return __("Aweber", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AWEBER_OPTION_INTEGRATION':
                return __("Enable Aweber Integration", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_DROPBOX_OPTIONS_INTEGRATION':
                return __("Enable Dropbox Integration", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CONSTANT_CONTACT_OPTION_INTEGRATION':
                return sprintf(__("Enable Constant Contact Integration. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/external-integrations/#htenablecc');

            case 'LABEL_EXPORT':
                return __("Export", 'custom-registration-form-builder-with-submission-manager');

            case 'UPLOAD_XML':
                return __("Upload Rmagic.xml ", 'custom-registration-form-builder-with-submission-manager');

            case 'UPLOAD_XML_HELP':
                return __("Upload the backup Rmagic.xml file you had exported earlier, to import all contained data.", 'custom-registration-form-builder-with-submission-manager');

            case 'CC_ERROR':
                return __("<div class='rmnotice'>Oops!! Something went wrong.<ul><li>Possible causes:-</li><li>Couldn't access your  constant contact account with the details you have provided in Gloabal settings->External Integrations.</li><li>You have not created any list in your constant contact account.</li></ul></div>", 'custom-registration-form-builder-with-submission-manager');

            case 'AW_ERROR':
                return __("<div class='rmnotice'>Oops!! Something went wrong.<ul><li>Possible causes:-</li><li>Couldn't access your  aweber account with the details you have provided in Gloabal settings->External Integrations.</li><li>You have not created any list in your aweber account.</li></ul></div>", 'custom-registration-form-builder-with-submission-manager');

            case 'MC_ERROR':
                return __("<div class='rmnotice'>Oops!! Something went wrong.<ul><li>Possible causes:-</li><li>Couldn't access your  mailchimp account with the details you have provided in Gloabal settings->External Integrations.</li><li>You have not created any list in your mailchimp account.</li></ul></div>", 'custom-registration-form-builder-with-submission-manager');

            case 'RM_ERROR_EXTENSION_CURL_CC':
                return __("PHP extension CURL is not enabled on server.So Constant Contact will not work.", 'custom-registration-form-builder-with-submission-manager');

            case 'RM_ERROR_PHP_4.5':
                return __("Constant Contact requires PHP version 5.4+.Please upgrade your php version to use constant contact", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_ACTRL_AW_DESC':
                return __("Aweber Integration with basic field mapping.(Available in Premium bundle.)", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_GEN_PROGRESS_BAR_GOLD':
                return __("Shows form expiry status above the form when auto-expiry is turned on.(Available in Premium bundle)", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_YES':
                return __("Yes", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NO':
                return __("No", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DEFAULT':
                return __("Default", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUB_LIMIT_ANTISPAM_HELP_GOLD':
                return __("Limits how many times a form can be submitted from a device within a day. Helpful to prevent spams. Set it to zero(0) to disable this feature.(Part of GOLD Bundle)", 'custom-registration-form-builder-with-submission-manager');

            case 'PART_GOLD':
                return __("Part of GOLD Bundle", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AW_LIST':
                return __("Select Aweber list.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_AW_LIST':
                return __("Select the Aweber list in which you want add subscribers.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_FIELD':
                return __("Map you Aweber field with form field.", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPT_IN_CB_AW':
                return sprintf(__("Display a checkbox, allowing users to opt-in for subscription. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/mailchimp-integration-2/#htmcoptin');

            case 'HELP_ADD_FIELD_CC':
                return __("This will map the selected field to the corresponding constant contact field.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CC_LIST':
                return __("Constant Contact List", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_CC_LIST':
                return __("Select a Constant contact list", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_T_AND_C_CB_LABEL':
                return __("Checkbox Label", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_TnC_CB_LABEL':
                return sprintf(__('This will appear along with the checkbox. You might want to set it up to say something like "I accept". '."<a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/new-field/#htchecklabel');

            case 'LABEL_SOCIAL_FIELDS':
                return __('Social Fields', 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_DISPLAY_FIELDS':
                return __('Display Fields', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_MNAME':
                return __("Middle Name", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_COMPANY':
                return __("Company Name", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_JOB_TILE':
                return __("Job Title", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_WORK_PHONE':
                return __("Work Phone", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CELL_PHONE':
                return __("Cell Phone", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_HOME_PHONE':
                return __("Home Phone", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FAX':
                return __("Fax", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADDRESS':
                return __("Address", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CREATED_DATE':
                return __("Created date", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPT_IN_CB_CC':
                return __("This option will allow user to choose for Constant contact subscription.", 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SETTING_FAB_PT':
                return __("Magic Popup Button Setting", 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_FAB':
                return __("Magic Popup Button", 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_SETTINGS_FAB_EXCERPT':
                return __("One button to rule them all!", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT_FORM_TYPE':
                return __("Form Type", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_REG_FORM':
                return __("Enable WordPress User Account Creation", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_NON_REG_FORM':
                return __("Disable WordPress User Account Creation", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SELECT_FORM_TYPE_REG':
                return sprintf(__("For those who want to create WP User accounts after form submission or Manual Approval. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/general-settings/#htformtype");

            case 'HELP_SELECT_FORM_TYPE_NON_REG':
                return sprintf(__("For those who do not want to create WP User accounts with form submissions.Ideal for offline registration processes or using this form as simple contact/enquiry form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/general-settings/#htformtype");

            case 'LABEL_POST_EXP_ACTION':
                return __("Limit Action", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_POST_EXP_ACTION':
                return sprintf(__("What happens when user opens a form after reaching restriction limit? <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/limits/#htlimitaction");

            case 'LABEL_DISPLAY_MSG':
                return __("Display a message", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SWITCH_FORM':
                return __("Display another form", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT_FORM':
                return __("Select form", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_POST_EXP_FORM':
                return sprintf(__("Displays another form instead of original form after restriction limits are reached. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/limits/#htlimitaction");

            case 'LABEL_FAB_ICON':
                return __("Icon on MagicPopup Button", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FAB_ICON_BTN':
                return __("Select", 'custom-registration-form-builder-with-submission-manager');

            case 'TEXT_FAB_ICON_HELP':
                return sprintf(__("Display an image on MagicPopup Button instead of the default 'Users' icon. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/magic-popup-button/#htmpicon");

            case 'LABEL_HIDE_PREV_BUTTON':
                return __("Do not show &quot;Previous&quot; button:<br/>(For Multi-Step Registration Form Only)", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_HIDE_PREV_BUTTON':
                return __("Enabling this will remove previous button from multi-page forms, thus prohibiting user from navigating back to already filled pages without reloading the form.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IS_PAID_ROLE':
                return __("Sign-up Charge", 'custom-registration-form-builder-with-submission-manager');


            case 'HELP_IS_PAID_ROLE':
                return sprintf(__("User will be charged for signing up for this role. User will be redirected to checkout after submitting the form and role will be assigned on successful payment. (Make sure that you have configured payment option in Global Settings->Payments). <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/user-roles/#htsignupcharge");

            case 'LABEL_ROLE_PRICE':
                return __("Role Charges", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ROLE_PRICE':
                return __("This charge will be added to the form and user redirected to the payment when this role is auto assigned to the form.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FAB_ICON_BTN_REM':
                return __("Remove", 'custom-registration-form-builder-with-submission-manager');

            case 'TEXT_FROM':
                return __("From", 'custom-registration-form-builder-with-submission-manager');

            case 'NOTE_MAGIC_PANEL_STYLING':
                return __("Magic Panels can be styled by logging in as admin and visiting site front end.", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_LOGIN_SUCCESS':
                return __("You have logged in successfully.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBMISSION_ON_CARD':
                return __('Submission Badge count on Form Card', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SUBMISSION_ON_CARD':
                return sprintf(__("The number on form card badge will count based on this criteria. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/general-settings-2/#htbadgecount');

            case 'LABEL_BLOCK_EMAIL':
                return __("Block Email", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BLOCK_IP':
                return __("Block IP", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_FAB_LINK1':
                return __("Custom Link #1", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_FAB_LINK2':
                return __("Custom Link #2", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_FAB_LINK3':
                return __("Custom Link #3", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SHO_FAB_LINK':
                return sprintf(__("Add a custom link of your choice on the MagicPopup Menu. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/magic-popup-button/#htcustomlinks");

            case 'LABEL_FAB_LINK_TYPE':
                return __("Link Type", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_VISIBILITY':
                return __("Visible to", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FAB_URL_LABEL':
                return __("Label of the URL", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SEND_MESSAGE':
                return __("Send Message", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_RELATED':
                return __("Related", 'custom-registration-form-builder-with-submission-manager');

            case 'VALIDATION_REQUIRED':
                return __("This field is required.", 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_URL':
                return __("Please enter a valid URL.", 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_FORMAT':
                return __("Invalid Format.", 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_NUMBER':
                return __("Please enter a valid number.", 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_DIGITS':
                return __("Please enter only digits.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DEFAULT_STATE':
                return __("Default State.", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CHECKED':
                return __("Checked", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_UNCHECKED':
                return __("Unchecked", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_UNREAD':
                return __("Unread", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IS_FIELD_EDITABLE':
                return __("Allow Users to Edit this Field after Submission", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_LABEL_IS_FIELD_EDITABLE':
                return sprintf(__("If you have set up a frontend User area for your users and want them to login and edit the form submission after they have submitted it, you must turn this on. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/new-field/#hteditfield");

            case 'MSG_OPT_IN_DEFAULT_STATE':
                return sprintf(__("Default state of the opt in check box. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/mailpoet-integration/#htmpdefstate");

            case 'MSG_EDIT_SUBMISSION':
                return __("Edit This Submission", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_EDIT_YOUR_SUBMISSIONS':
                return __("Edit Your Submissions", 'custom-registration-form-builder-with-submission-manager');


            case 'LABEL_ALLOW_MULTILINE':
                return __("Allow Multiline", 'custom-registration-form-builder-with-submission-manager');

            case 'SUB_MESSAGE':
                return __("You've reached <span class='rm-submission-mark'>%d</span> submissions...", 'custom-registration-form-builder-with-submission-manager');

            case 'USER_MESSAGE':
                return __("You've reached <span class='rm-submission-mark'>%d</span>  users on your website...", 'custom-registration-form-builder-with-submission-manager');

            case 'REVIEW_MESSAGE_EVENT1':
                return __("You've reached <span class='rm-submission-mark'>10</span> submissions...", 'custom-registration-form-builder-with-submission-manager');

            case 'REVIEW_MESSAGE_EVENT2':
                return __("You've reached <span class='rm-submission-mark'>10</span> users...", 'custom-registration-form-builder-with-submission-manager');

            case 'REVIEW_MESSAGE_EVENT3':
                return __("You've reached <span class='rm-submission-mark'>100</span> submissions...", 'custom-registration-form-builder-with-submission-manager');

            case 'REVIEW_MESSAGE_EVENT4':
                return __("You've reached <span class='rm-submission-mark'>100</span> users...", 'custom-registration-form-builder-with-submission-manager');

            case 'REVIEW_MESSAGE_EVENT5':
                return __("You've reached <span class='rm-submission-mark'>1000</span> submissions...", 'custom-registration-form-builder-with-submission-manager');

            case 'REVIEW_MESSAGE_EVENT6':
                return __("You've reached <span class='rm-submission-mark'>1000</span> users...", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_ASTERIX':
                return __("Show Asterisk on Required Fields", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SHOW_ASTERIX':
                return sprintf(__("Show the red Asterisk(*) on top right side of the label. A common symbol for required fields. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/general-settings-2/#htasterisk");

            case 'LABEL_SHOW_PAYMENT_TAB':
                return __("Show Payment Tab", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_SUBMISSION_TAB':
                return __("Show Registrations Tab", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_DETAILS_TAB':
                return __("Show My Details Tab", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SHOW_SUBMISSION_TAB':
                return sprintf(__("Add Submissions tab to MagicPopup Menu. Users can check their form submissions by clicking on it. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/magic-popup-button/#htregtab");

            case 'HELP_SHOW_PAYMENT_TAB':
                return sprintf(__("Add Payments tab to MagicPopup Menu. Users can check their form payments by clicking on it. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/magic-popup-button/#htpaymenttab");

            case 'HELP_SHOW_DETAILS_TAB':
                return sprintf(__("Add User details tab to MagicPopup Menu. Users can check their user account page by clicking on it. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/magic-popup-button/#htdetailstab");

            case 'LABEL_VISITS':
                return __("Visits", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_GP_CLIENT_ID':
                return sprintf(__("More information about Google Client ID <a target='blank' class='rm_help_link' href='%s'>HERE</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://developers.google.com/identity/sign-in/web/devconsole-project","https://registrationmagic.com/knowledgebase/external-integrations/#htenablegoogle");

            case 'LABEL_LOGIN_LINKEDIN_OPTION':
                return __('Enable LinkedIn Login', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_LINKEDIN_ENABLE':
                return sprintf(__("Adds 'Login Using LinkedIn' button to the login form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/external-integrations/#htenablelinkedin");

            case 'LABEL_LIN_API_KEY':
                return __("LinkedIn API Key", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_LIN_API_KEY':
                return sprintf(__("More information about LinkedIn Client ID/ API Key <a target='blank' class='rm_help_link' href='%s'>HERE</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://developer.linkedin.com/support/faq","https://registrationmagic.com/knowledgebase/external-integrations/#htenablelinkedin");

            case 'LABEL_LOGIN_WINDOWS_OPTION':
                return __('Enable Microsoft Live Login', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_WINDOWS_ENABLE':
                return sprintf(__("Adds 'Login Using Microsoft' button to the login form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/external-integrations/#htenablemslive");

            case 'LABEL_WIN_CLIENT_ID':
                return __("Microsoft App/ Client ID", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_WIN_CLIENT_ID':
                return sprintf(__("More information about Microsoft App/ Client ID <a target='blank' class='rm_help_link' href='%s'>HERE</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://msdn.microsoft.com/en-in/library/bb676626.aspx","https://registrationmagic.com/knowledgebase/external-integrations/#htenablemslive");

            case 'FD_LABEL_ADD_NEW':
                return __("Add New", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_SWITCH_FORM':
                return __("Switch Form", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_PERMALINK':
                return __("Permalink", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_MSG_HOW_FORM_DOING':
                return __("How's your form <b>doing?</b>", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_INBOX':
                return __("Inbox", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_NOT_INSTALLED':
                return __("Not Installed", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_MSG_LOOK_AND_FEEL':
                return __("<b>Look and Feel</b> of your form", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_DESIGN':
                return __("Design", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_FORM_FIELDS':
                return __("Fields Manager", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_THINGS_CAN_DO_WITH_FORM':
                return __("<b>Things you can do</b> with form data", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_FINE_TUNE_FORM':
                return __("<b>Fine Tune</b> Your Form", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_LIMITED':
                return __("Limited", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_OVERRIDES_SETT':
                return __("Global Overrides", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_MULTISTEP_FORM':
                return __("Multi-Step Forms", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_COMINGSOON':
                return __("Coming Soon", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_ADD_APPS_TO_FORM':
                return __("<b>Add Apps</b> To Your Form", 'custom-registration-form-builder-with-submission-manager');

            case 'NAME_CONSTANT_CONTACT':
                return __("Constant Contact", 'custom-registration-form-builder-with-submission-manager');

            case 'NAME_WOOCOMMERCE':
                return __("WooCommerce", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_BADGE_NEW':
                return __("New", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_VIEW_ALL':
                return __("View All", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_FORM_SHORTCODE':
                return __("Shortcode", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_COPY':
                return __("Copy", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_FORM_VISIBILITY':
                return __("Visibility", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_FORM_CREATED_ON':
                return __("Created On", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_FORM_PAGES':
                return __("Pages", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_FORM_SUBMIT_BTN_LABEL':
                return __("Submit Label", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_VISITORS':
                return __("Visitors", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_DOWNLOAD_REGISTRATIONS':
                return __("Download Records", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_AVG_TIME':
                return __("Avg. Time", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_AUTORESPONDER':
                return __("Auto-Responder", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_WP_REG':
                return __("WP Registrations", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_REDIRECTION':
                return sprintf(__("Redirection. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/post-submission/#htredirection");

            case 'FD_LABEL_AUTO_APPROVAL':
                return __("Auto Approval", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_ISSUE_SUB_TOKEN':
                return __("Issue Token No", 'custom-registration-form-builder-with-submission-manager');

            case 'NAME_RECAPTCHA':
                return __("reCAPTCHA", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_FORM_TOGGLE_PH':
                return __("Select a Form", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_STATS':
                return __("Stats", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_STATUS':
                return __("Status", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_CONTENT':
                return __("Content", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_QCK_TOGGLE':
                return __("Quick Toggles", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_PUBLIC':
                return __("Public", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_LABEL_RESTRICTED':
                return __("Limited", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_LOGIN_TWITTER_OPTION':
                return __('Allow User to Login using Twitter:', 'custom-registration-form-builder-with-submission-manager');
                
            case 'FD_LABEL_MORE':
                return __("More", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_TWITTER_ENABLE':
                return sprintf(__("Adds 'Login Using Twitter' button to the login form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/external-integrations/#htenabletwitter");

            case 'LABEL_TW_CONSUMER_KEY':
                return __("Twitter Consumer Key", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TW_CONSUMER_SEC':
                return __("Twitter Consumer Secret", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_TW_CONSUMER_KEY':
                return sprintf(__("More information about Twitter Consumer Key <a target='blank' class='rm_help_link' href='%s'>HERE</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://apps.twitter.com/","https://registrationmagic.com/knowledgebase/external-integrations/#htenabletwitter");

            case 'HELP_OPTIONS_THIRDPARTY_TW_CONSUMER_SEC':
                return sprintf(__("More information about Twitter Consumer Secret <a target='blank' class='rm_help_link' href='%s'>HERE</a>. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://apps.twitter.com/","https://registrationmagic.com/knowledgebase/external-integrations/#htenabletwitter");


            case 'LABEL_LOGIN_INSTAGRAM_OPTION':
                return __('Allow Users to Login using Instagram', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_INSTAGRAM_ENABLE':
                return sprintf(__("Adds 'Login Using Instagram' button to the login form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/external-integrations/#htenableinsta");

            case 'LABEL_INS_CLIENT_ID':
                return __("Instagram Client ID", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_THIRDPARTY_INS_CLIENT_ID':
                return sprintf(__("To make Instagram login work, you&#39;ll need a Client ID. More information <a target='blank' class='rm_help_link' href='%s'>here</a>.", 'custom-registration-form-builder-with-submission-manager'),"https://www.instagram.com/developer/authentication/");

            case 'LABEL_MARK_ALL_READ':
                return __("Mark all read", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUBS_OVER_TIME':
                return __("Submissions over time", 'custom-registration-form-builder-with-submission-manager');

            case 'STAT_TIME_RANGES':
                return __("Last %d days", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT_TIMERANGE':
                return __("Show data for", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_GLOBAL_OVERRIDE_SETT':
                return __("Global Overrides", 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SUBMISSION_FD':
                return __('No Submissions for this form yet.<br>Once submissions start coming, this area will show the latest submissions.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADD_DEFAULT_FORM':
                return __("Add Default Form", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_CHANGE_DEFAULT_FORM':
                return __("Change Default Form", 'custom-registration-form-builder-with-submission-manager');

           
            case 'FD_LABEL_F_FIELDS':
                return __("Fields", 'custom-registration-form-builder-with-submission-manager');

            case 'GLOBAL_OVERRIDES_NOTE':
                return __('Global Overrides provide an easy way for power users to override default Global Settings on individual forms. Once you have turned on the override, corresponding Global Setting values will have no effect on this form. ', 'custom-registration-form-builder-with-submission-manager');

            case 'NO_EMBED_CODE':
                return __("Embed Code not available.", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_BASIC_DASHBOARD':
                return __("<b>Standard Edition Dashboard</b>", 'custom-registration-form-builder-with-submission-manager');

            case 'FD_TOGGLE_TOOLTIP':
                return __("To toggle this setting you need to configure it first. <a href='%s'>Click here </a>to configure now.</span>", 'custom-registration-form-builder-with-submission-manager');

            case 'DASHBOARD_WIDGET_TABLE_CAPTION':
                return __("Latest Submissions", 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_SENT_EMAILS_MANAGER':
                return __('Sent Emails', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SENT_EMAILS_MAN':
                return __('No sent emails yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SENT_EMAILS_USER':
                return __('No email has been sent to this user yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EMAIL_TO':
                return __('To', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EMAIL_SUB':
                return __('Subject', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EMAIL_BODY':
                return __('Content', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_EMAIL_SENT_ON':
                return __('Sent on', 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_SENT_MAILS':
                return __('Sent Emails', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SENT_EMAILS':
                return __('Sent Emails', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_INVALID_SENT_EMAIL_ID':
                return __('Invalid sent email id', 'custom-registration-form-builder-with-submission-manager');

            case 'SEND_MAIL':
                return __('Send a new email', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_REGISTERED_USERS':
                return __('No Users are registered yet.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SENT_EMAIL_USER':
                return __('No email sent yet', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SENT_EMAIL_MATCHED':
                return __('No sent email matched your search.', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_NO_SENT_EMAIL_INTERVAL':
                return __('No email sent during the period.', 'custom-registration-form-builder-with-submission-manager');

            case 'RM_SUB_LEFT_CAPTION' :
                return __('%s submission slots remain', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TOUR' :
                return __('Tour', 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_MAXLEN' :
                return __('Please enter no more than {0} characters.', 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_MINLEN' :
                return __('Please enter at least {0} characters.', 'custom-registration-form-builder-with-submission-manager');

            case 'INVALID_MAX' :
                return __('Please enter a value less than or equal to {0}.', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_AFTER_LOGOUT_URL' :
                return __('After Logout Redirect User to', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_OPTIONS_POST_LOGOUT_REDIR' :
                return sprintf(__("User will be redirected to this page after logging out. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/general-settings-2/#htlogoutredirect");

            case 'EXPIRY_DETAIL_BOTH' :
                return __('%1$d out of %2$d filled and %3$d days to go', 'custom-registration-form-builder-with-submission-manager');

            case 'EXPIRY_DETAIL_SUBS' :
                return __('%1$d out of %2$d filled', 'custom-registration-form-builder-with-submission-manager');

            case 'EXPIRY_DETAIL_DATE' :
                return __('%d days to go', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYPAL_TRANSACTION_LOG' :
                return __('Transaction log', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND' :
                return __('Legend', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_PAYMENT_PENDING' :
                return __('Payment Pending', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_PAYMENT_COMPLETED' :
                return __('Payment Completed', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_USER_BLOCKED' :
                return __('User Blocked', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_NOTES' :
                return __('Has Notes', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_MESSAGE' :
                return __('Messaged', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_ATTACHMENT' :
                return __('Has Attachment(s)', 'custom-registration-form-builder-with-submission-manager');

            case 'FE_FORM_TOTAL_PRICE' :
                return __('Total Price: %s', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SHOW_TOTAL_PRICE' :
                return __('Show total price on the form', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SHOW_TOTAL_PRICE' :
                return sprintf(__("Enables a real-time display of total amount when you have multiple products added to the form. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/general-settings/#httotalprice");

            case 'LABEL_DATE_FORMAT' :
                return __('Date format', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_DATEFORMAT' :
                return __('For a list of supported types please click <a %s>here</a>.', 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_PREMIUM' :
                return __('Premium', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ALLOW_QUANTITY' :
                return __('Allow Users to Specify Quantity', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_PRICE_FIELD_ALLOW_QUANTITY' :
                return sprintf(__("A quantity box will appear on the form allowing users to purchase more than one item. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/add-product/#htquantity");

            case 'LABEL_NEW_USER_EMAIL' :
                return __('New User Email Body', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_NU_EMAIL_MSG' :
                return sprintf(__("Content of the email to be sent to the newly created user. You can use rich text and values the user submitted in the form for a more personalized message. Use the code {{PASS_RESET_LINK}} to add password reset link within this email itself. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/email-templates/#htnewemailbody");

            case 'HELP_ADD_FORM_USER_ACTIVATED_MSG' :
                return sprintf(__("Content of the email to be sent to the activated user. You can use rich text and values the user submitted in the form for a more personalized message. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/email-templates/#htactemailbody");

            case 'HELP_ADD_FORM_ACTIVATE_USER_MSG' :
                return sprintf(__("Content of the email to be sent to admin with activation link. You can use rich text and values the user submitted in the form for a more personalized message. Please note that the sender email of this message will always be the admin email in your dashboard. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/email-templates/#htuseremailbody");

            case 'LABEL_USER_ACTIVATION_EMAIL' :
                return __('User Activation Email Body', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ACTIVATE_USER_EMAIL' :
                return __('Active User Email Body (To Admin)', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_ADMIN_NEW_SUBMISSION_EMAIL' :
                return __('New Submission Email Body (To Admin)', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FORM_ADMIN_NS_MSG' :
                return sprintf(__("Content of the email to be sent to admin on new submission. You can use rich text and values the user submitted in the form for a more personalized message. <a target='_blank' class='rm-more' href='%s'>More</a>"
                        . "<br><br><br><span class='submission-upgrade-title'><i class='material-icons'>&#xE8B3;</i><strong>User History</strong></span><br>Want to know what the user wrote to you earlier with contact form submission? Wish to have purchase history of your WooCommerce customer attached to a new support request? Need to see purchased downloads of Easy Digital Downloads buyer with form data?  Time to add some intelligence to your submission notifications. Introducing, message shortcodes which dynamically fetch user information from their history on your site and provide you with deeper user insights attached to the submitted content.", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-templates/#htsubmitemailbody');

            case 'LABEL_F_EMAIL_TEMPLATES_SETT':
                return __("Email Templates", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_FOPTIONS_ARESP_ADMIN_NOTIFS':
                return sprintf(__("An email notification will be sent to recipients of this form for every submission. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/global-overrides/#htsendnotice");

            case 'LABEL_FORM_NOTIFS_TO':
                return __('Send Notification To:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_INVOICE_SHORT':
                return __('Invoice', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_PAYMENT_REFUNDED' :
                return __('Payment Refunded', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_LEGEND_PAYMENT_CANCELED' :
                return __('Payment Canceled', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PAYMENT_DETAILS' :
                return __('Payment Details', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_SUB_ERROR_HEADER' :
                return __('Following error(s) were found:', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_F_EMAIL_TEMP_SETT':
                return __('Email Templates', 'custom-registration-form-builder-with-submission-manager');

            case 'ADMIN_MENU_FS_ET_PT':
                return __('Email Templates', 'custom-registration-form-builder-with-submission-manager');

            case 'MSG_USER_ROLE_NOT_ASSIGNED':
                return __("No role assigned", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_STARTER_GUIDE':
                return __("Starter Guide", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_DEMO':
                return __("Demo", 'custom-registration-form-builder-with-submission-manager');

            case 'CRON_DISABLED_WARNING_INVITATION':
                return sprintf(__('Wordpress cron is disabled. This feature will not work. <a target="__blank" href="%s">More info.</a>', 'custom-registration-form-builder-with-submission-manager'),"https://codex.wordpress.org/Editing_wp-config.php#Disable_Cron_and_Cron_Timeout");

            case 'LABEL_FIELD_SAVE':
                return __("Add to Form", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SELECT_PRICING_TYPE':
                return __('Product Pricing Type', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PRODUCT_NAME':
                return __('Name', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TOGGLE_FORM':
                return __('Toggle Form &rarr;', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SHOW_ON_FORM':
                return sprintf(__("Displays price on the form while user fills it. If turned off, user will be directly taken to checkout. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/add-product/#htproddisplayprice");

            case 'FIELD_HELP_TEXT_Hidden':
                return __('Standard hidden type html field.', 'custom-registration-form-builder-with-submission-manager');

            case 'FIELD_TYPE_HIDDEN':
                return __('Hidden Field', 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_ADD_FIELD_IS_UNIQUE':
                return sprintf(__("Mark this field as unique. No two users can submit same value for this field. Any subsequent attempt for submission with duplicate value will show a message. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/new-field/#htisunique");

            case 'LABEL_IS_UNIQUE':
                return __('Accept Only Unique Values', 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SUB_LIMIT_IND_USER':
                return __("Limit Submissions from a Single User", 'custom-registration-form-builder-with-submission-manager');

            case 'HELP_SUB_LIMIT_IND_USER':
                return sprintf(__("Limits how many times a form can be submitted by same user. Set it to zero(0) to disable this feature. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/general-settings/#htsubmitlimit");

            case 'ADV_FIELD_SETTINGS':
                return __('Advanced Settings', 'custom-registration-form-builder-with-submission-manager');

            case 'ICON_FIELD_SETTINGS':
                return __('Icon Settings', 'custom-registration-form-builder-with-submission-manager');

            case 'TITLE_FORMFLOW_CONFIG_PAGE':
                return __('Configuration Manager', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_FORMCARD_LINK_SETUP':
                return __('Fields', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_FORMCARD_LINK_MANAGE':
                return __('Settings', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FD_SEC_1_TITLE':
                return __('Build', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FD_SEC_2_TITLE':
                return __('Configure', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FD_SEC_3_TITLE':
                return __('Integrate', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FD_SEC_4_TITLE':
                return __('Publish', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FD_SEC_5_TITLE':
                return __('Manage', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FD_SEC_6_TITLE':
                return __('Analyze', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FD_SEC_7_TITLE':
                return __('Automate', 'custom-registration-form-builder-with-submission-manager');
                
             case 'LABEL_META_ADD':
                  return __('Associated User Meta Key','custom-registration-form-builder-with-submission-manager');
           
            case 'HELP_META_ADD':
                return __('Define the WordPress User Meta key where values of this field will be stored. Field values will be pre-filled when the form is opened, if currently logged in user has submitted another form in the past with same meta-keys. Please note - Some complex type fields like price fields do not support pre-filling.','custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_PUBLISH':
                return __("Publish", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_SHORTCODE':
                return __("Shortcode", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_HTML_CODE':
                return __("HTML Code", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_FORM_WIDGET':
                return __("Form Widget", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_USER_DIRECTORY':
                return __("User Directory", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_USER_AREA':
                return __("User Area", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_MAGIC_POPUP':
                return __("Magic PopUp", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_LANDING_PAGE':
                return __("Landing Page", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_LOGIN_BOX':
                return __("Login Box", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PUBLISH_OTP_WIDGET':
                return __("OTP Login", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_OUTBOX':
                return __("Outbox", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_REDIRECT_ADMIN_TO_DASH':
                return __("Always redirect admin users to dashboard", 'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_OPTIONS_GEN_REDIRECT_ADMIN_TO_DASH':
                return sprintf(__("If enabled, admin users will always be redirected to admin dashboard irrespective of page/url selected above. <a target='_blank' class='rm-more' href='%s'>More</a>", 'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/general-settings-2/#htredirectadmin");               
            
            case 'LABEL_SELECT_COUNTRY':
                return __("--Select Country--", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_ADD_CONDITION':
                return __ ('Conditions', 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_CONDITIONS':
                return __ ('Conditional Logic', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_CONTROLLING_FIELD':
                return __("Controlling Field", 'custom-registration-form-builder-with-submission-manager'); 
            
            case 'LABEL_OPERATOR':
                return __("Operator", 'custom-registration-form-builder-with-submission-manager'); 
                
            case 'LABEL_ADD_NEW_WIDGET':
                return __('Add Widget', 'custom-registration-form-builder-with-submission-manager');
            
            case 'ADMIN_MENU_ADD_WIDGET_PT':
                return __("Add Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_NEW_P_WIDGET_PAGE':
                return __("New Paragraph Widget", 'custom-registration-form-builder-with-submission-manager');
                
            case 'TITLE_NEW_H_WIDGET_PAGE':
                return __("New Heading Widget", 'custom-registration-form-builder-with-submission-manager');  
                
            case 'TITLE_NEW_SP_WIDGET_PAGE':
                return __("New Spacing Widget", 'custom-registration-form-builder-with-submission-manager');  
                
            case 'TITLE_NEW_DI_WIDGET_PAGE':
                return __("New Divider Widget", 'custom-registration-form-builder-with-submission-manager'); 
                
            case 'WIDGET_TYPE_RICHTEXT':
                return __("Rich Text", 'custom-registration-form-builder-with-submission-manager');    
            
            case 'TITLE_NEW_RT_WIDGET_PAGE':
                return __("New Rich Text Widget", 'custom-registration-form-builder-with-submission-manager');     
                 
            case 'LABEL_CONTENT':
                return __("Content", 'custom-registration-form-builder-with-submission-manager');
                
            case 'WIDGET_TYPE_DIVIDER':
                return __("Divider", 'custom-registration-form-builder-with-submission-manager'); 
                
            case 'WIDGET_TYPE_SPACING':
                return __("Spacing", 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_RT_CONTENT':
                return __('The text you want the user to see.', 'custom-registration-form-builder-with-submission-manager');
            
            case 'FIELD_HELP_TEXT_RICHTEXT':
                return __('Allows you to display richly formatted text inside your form.', 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_ADD_WIDGET_LABEL':
                return __("MagicWidgets labels do not appear on the form. MagicWidgets are not visible inside Submission page or Submission PDFs.", 'custom-registration-form-builder-with-submission-manager');
            
            case 'WIDGET_TYPE_LINK':
                return __("Link", 'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_HELP_TEXT_LINK':
                return __("Display link inside your form.", 'custom-registration-form-builder-with-submission-manager');
             
            case 'FIELD_HELP_TEXT_TIMER':
                return __("Allows you to display richly formatted text inside your form.", 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_ADD_WIDGET_LINK':
                return __("The clickable text that will be linked to specified URL or page.", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_LINK_SAME_WINDOW':
                return __("Open in same window", 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_WIDGET_LINK_SW':
                return __("Opens link in same window.", 'custom-registration-form-builder-with-submission-manager');
                
            case 'TITLE_LINK_WIDGET_PAGE':
                return __("Link Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'NOTE_DEFAULT_FORM':
                return __("To make a form Default WordPress Registration form, make sure it is configured to create WordPress User accounts on submission.", 'custom-registration-form-builder-with-submission-manager');
   
            case 'HELP_ADD_WIDGET_ANCHOR':
                return __("Dummy Text", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_ANCHOR_LINK':
                return __("Link", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_CHOOSE_PP':
                return __("Choose from Pages", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_YOUTUBE_WIDGET_PAGE':
                return __("YouTube Widget", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_VIDEO_URL':
                return __("Video URL", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_AUTO_PLAY':
                return __("Auto Play", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_REPEAT':
                return __("Repeat", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_RELATED_VIDEOS':
                return __("Related Videos", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_WIDTH':
                return __("Player Width", 'custom-registration-form-builder-with-submission-manager');
            case 'IFRAME_LABEL_WIDTH':
                return __("Iframe Width", 'custom-registration-form-builder-with-submission-manager');
              case 'IFRAME_LABEL_HEIGHT':
                return __("Iframe Height", 'custom-registration-form-builder-with-submission-manager');
              
                
            case 'TITLE_SP_WIDGET_PAGE':
                return __("Spacing Widget", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_HEIGHT':
                return __("Player Height", 'custom-registration-form-builder-with-submission-manager');
                
            case 'TITLE_LINK_WIDGET_PAGE':
                return __("Link Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_TIMER_WIDGET_PAGE':
                return __("Timer Widget", 'custom-registration-form-builder-with-submission-manager');
                
            case 'TITLE_RT_WIDGET_PAGE':
                return __("Rich Text Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_IF_WIDGET_PAGE':
                return __("Iframe Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_P_WIDGET_PAGE':
                return __("Paragraph Widget", 'custom-registration-form-builder-with-submission-manager');
                
             case 'TITLE_H_WIDGET_PAGE':
                return __("Heading Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_DI_WIDGET_PAGE':
                return __("Divider Widget", 'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_HELP_TEXT_YOUTUBE':
                 return __("Display a YouTube video in your form", 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_WI_VIDEO_URL':
                return sprintf(__("URL of the YouTube Video you wish to add to your form. For example, %s",'custom-registration-form-builder-with-submission-manager'),"https://www.youtube.com/watch?v=Eq9x-e3phHo");
           
            case 'HELP_FIELD_YT_WIDTH':
                 return __("Width of the YouTube Video. It can be set relative to the form in percentage (%) or in absolute pixels (px). For example, 100%, 50%, 350px etc.",'custom-registration-form-builder-with-submission-manager');
             
            case 'HELP_FIELD_YT_HEIGHT':
                 return __("Height of the YouTube Video. It can be set relative to the form in percentage (%) or in absolute pixels (px). For example, 100%, 50%, 350px etc.",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_WIDGET_YT_AUTOPLAY':  
                return __("Autoplays the video when the form first loads.",'custom-registration-form-builder-with-submission-manager');
            
             case 'HELP_WIDGET_YT_REPEAT':
                 return __("Loops the video after the first play through.",'custom-registration-form-builder-with-submission-manager');
 
             case 'HELP_WIDGET_YT_RELATED':
                 return __("Display a list of related videos after the video finishes. This will have no effect if you have turned on Repeat.",'custom-registration-form-builder-with-submission-manager');
 
             case 'LABEL_ANCHOR_TEXT':
                 return __("Anchor Text",'custom-registration-form-builder-with-submission-manager');
 
             case 'HELP_ADD_WIDGET_URL':
                 return __("URL for Image Hyperlink.",'custom-registration-form-builder-with-submission-manager');   
             
             case 'HELP_ADD_WIDGET_LINK':
                return __("The clickable text that will be linked to specified URL or page.", 'custom-registration-form-builder-with-submission-manager');
                 
             case 'HELP_ADD_WIDGET_ANCHOR_LINK':
                 return __("Hyperlink the Image.",'custom-registration-form-builder-with-submission-manager');
              
             case 'FIELD_HELP_TEXT_IFRAME':
                 return __("Display an external webpage in your form using iFrame",'custom-registration-form-builder-with-submission-manager');

             case 'HELP_ADD_WIDGET_PAGE':
                 return __("Select page which you want to link to the anchor text.",'custom-registration-form-builder-with-submission-manager');
             
             case 'HELP_IFRAME_URL':
                 return __("Enter the URL of the page which you wish to render inside the iFrame.",'custom-registration-form-builder-with-submission-manager');
             
             case 'HELP_FIELD_IF_WIDTH':
                 return __("Width of the frame. It can be set relative to the form in percentage (%) or in absolute pixels (px). For example, 100%, 50%, 350px etc.",'custom-registration-form-builder-with-submission-manager');
             
             case 'HELP_FIELD_IF_HEIGHT':
                 return __("Height of the frame. It can be set in percentage (%) or in absolute pixels (px). For example, 100%, 50%, 400px etc.",'custom-registration-form-builder-with-submission-manager');
             
             case 'HELP_ADD_FIELD_MULTI_LINE_TYPE':
                return sprintf(__("Allows user to add extra text area boxes to the form for submitting different values. Useful where a field requires multiple user input values. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/new-field/#htfieldtype");
            
             case 'WIDGET_TYPE_TIMER':
                return __("Timer", 'custom-registration-form-builder-with-submission-manager'); 
                 
             case 'LABEL_TEST_EMAIL':
                return __("Test Email",'custom-registration-form-builder-with-submission-manager');
                 
             case 'LABEL_USER_EMAIL_EXISTS':
                return __("User email already exists.",'custom-registration-form-builder-with-submission-manager');
               
             case 'LABEL_SENDERS_NAME':
                return __("Sender's Name",'custom-registration-form-builder-with-submission-manager');
               
            case 'LABEL_SENDERS_EMAIL':
                return __("Sender's Email",'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_USER_NOTIFICATIONS':
                return __("Submission Notification for User",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_ADMIN_NOTIFICATIONS':
                return __("Submission Notification for Admin",'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_OPTIONS_AR_US_FROM_EMAIL':
                 return sprintf(__("The sender's email address in the message header that the user will receive. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/email-notifications-2/#htfromemail");
            
            case 'HELP_OPTIONS_AR_AD_FROM_EMAIL':
                 return sprintf(__("The sender's email address in the message header that the admin will receive. You can also set this dynamically to the email of the user submitting the form by using tag {{useremail}}. This can be useful if you wish to forward submissions to a helpdesk or ticketing software. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/email-notifications-2/#htfromemail");
            
            case 'LABEL_ST_NUMBER':
                 return __("Street Number",'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_ROLE_KEY':
                return sprintf(__("Key value of the Role which will be saved in database. This will not be visible on front-end. This can be same as the Role name. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),"https://registrationmagic.com/knowledgebase/user-roles/#htrolekey"); 
                
            case 'HELP_ROLE_NAME':
                return sprintf(__("Name of the Role that will be displayed on the front-end and the dashboard. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/user-roles/#htrolename');
            
            case 'HELP_ROLE_PERMISSION':
                return sprintf(__("Inherit viewing and editing permissions for this custom Role from one of default WordPress roles. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/user-roles/#htinheritpermissions');
            
            case 'SMTP_TESTMAIL_HELP':
                return sprintf(__("Sends an email to this address for testing outing messages. <a target='_blank' class='rm-more' href='%s'>More</a>",'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/knowledgebase/email-notifications-2/#htemailfortest');
            
            case 'LABEL_GMAP_ADDRESS':
                return __("Start typing your address",'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_POWERED_GMAP':
                return __("Powered by GOOGLE MAPS",'custom-registration-form-builder-with-submission-manager');    
            
            case 'HELP_REG_ADD_ALL_COUNTRY':
                return __("Use this text area to enter countries you wish to show in the Countries dropdown. Comma separate country names, for example: Australia, Japan, Singapore",'custom-registration-form-builder-with-submission-manager');    
            
            case 'LABEL_PAYMENTS_GUIDE':
                return __("Payments Guide",'custom-registration-form-builder-with-submission-manager');    
            
            case 'LABEL_ACC_ACT_AUTO':
                 return __("Activate User Automatically",'custom-registration-form-builder-with-submission-manager');   
                
            case 'LABEL_ACC_ACT_MANUALLY':
                 return __("Deactivate User for Manual Approval",'custom-registration-form-builder-with-submission-manager');    
            
            case 'LABEL_ACC_ACT_BY_VERIFICATION':
                 return __("Send Verification Email",'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_ACC_ACT_METHOD':
                 return __("Select the action to be performed when a user submits a registration form for the first time. Content of the account verification email can be modified from Dashboard->Email Templates.",'custom-registration-form-builder-with-submission-manager');
               
            case 'LABEL_ACC_ACT_METHOD':
                 return __("Account Activation Method",'custom-registration-form-builder-with-submission-manager'); 
            
            case 'LABEL_NEW_USER_EMAIL_SUB' :
                return __('New User Email Subject', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_USER_ACTIVATION_EMAIL_SUB' :
                return __('User Activation Email Subject', 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_ADMIN_NEW_SUBMISSION_EMAIL_SUB' :
                return __('New Submission Email Subject (To Admin)', 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_ADD_FORM_NU_EMAIL_SUB' :
                return __("Subject of the email to be sent to the newly created user.", 'custom-registration-form-builder-with-submission-manager');
            
              
            case 'HELP_ADD_FORM_USER_ACTIVATED_SUB' :
                return __("Subject of the email to be sent to the activated user.", 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_ADD_FORM_ADMIN_NS_SUB' :
                return __("Subject of the email to be sent to admin on new submission.", 'custom-registration-form-builder-with-submission-manager');
             
            case 'LABEL_UPLOAD_FORM_TEMPLATE':
                return __('Upload Form Template','custom-registration-form-builder-with-submission-manager');  
            
             case 'TITLE_IMG_WIDGET_PAGE':
                return __("Image Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'FIELD_HELP_TEXT_IMAGEV':
                return __("Insert an image in your form",'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_CAPTION':
                return __("Caption",'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_LINK':
                return __("Link",'custom-registration-form-builder-with-submission-manager');
             
            case 'LABEL_EFFECTS':
                return __("Effects",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_BORDER_SHAPE':
                return __("Shape",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_DISPLAY_POP':
                return __("Display as pop up",'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_SIZE':
                return __("Size",'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_HELP_TEXT_PRICEV':
                return __("Display total price of products selected by user in the form",'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_HELP_TEXT_SUB_COUNDOWN':
                return __("If you have set form limits, you can display the limit status using this widget",'custom-registration-form-builder-with-submission-manager');    
            
            case 'TITLE_PRICE_WIDGET_PAGE':
                return __("Price Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_SUB_COUNT_WIDGET_PAGE':
                return __("Submission Coutdown Widget", 'custom-registration-form-builder-with-submission-manager');
                
            case 'TITLE_MAP_WIDGET_PAGE':
                return __("Map Widget", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_LAT':
                return __("Latitude", 'custom-registration-form-builder-with-submission-manager');    
                
            case 'LABEL_LONG':
                return __("Longitude", 'custom-registration-form-builder-with-submission-manager');    
            
            case 'LABEL_MAP_WIDTH':
                return __("Map Width", 'custom-registration-form-builder-with-submission-manager'); 
                
            case 'LABEL_ZOOM':
                return __("Zoom", 'custom-registration-form-builder-with-submission-manager'); 
            
            case 'TITLE_F_CHART_WIDGET_PAGE':
                return __("Form Data Chart", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_CHART_TYPE':
                return __("Chart Type", 'custom-registration-form-builder-with-submission-manager');
            
            case 'TITLE_F_DATA_WIDGET_PAGE':
                return __("Form Data", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_NUM_FORM_VIEWS':
                return __("Number of form views", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_TEXT_BEFORE':
                return __("Text Before", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_TEXT_AFTER':
                return __("Text After", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_NUM_SUB':
                return __("Display number of Submissions", 'custom-registration-form-builder-with-submission-manager');
             
            case 'LABEL_SUB_LIMITS':
                return __("Display Submission Limits", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_SUB_DATE_LIMITS':
                return __("Display Form Date Limits", 'custom-registration-form-builder-with-submission-manager'); 
                
            case 'LABEL_LS_RECEIVED':
                return __("Last Submission Received Time", 'custom-registration-form-builder-with-submission-manager');    
                
            case 'LABEL_SH_FONAME':
                return __("Form Name", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_SH_FONAME':
                return __("Form Description", 'custom-registration-form-builder-with-submission-manager');    
            
            case 'LABEL_DATE_TO_GO':
                return __("Date to go", 'custom-registration-form-builder-with-submission-manager'); 
                
             case 'LABEL_DAYS_TO_GO':
                return __("Days to go", 'custom-registration-form-builder-with-submission-manager');
             
            case 'TITLE_FEED_WIDGET_PAGE':
                return __("Registration Feed", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_CUSTOM_TEXT':
                return __("Custom Text", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_HIDE_DATE':
                return __("Hide Date", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_HIDE_COUNTRY':
                return __("Hide Country", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_MAX_ITEMS':
                return __("Max number of items to display", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_SHOW_GRAVATAR':
                return __("Show Gravatar", 'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_REGISTERED_ON':
                return __("registered on", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_SUBMITTED_ON':
                return __("Submitted on", 'custom-registration-form-builder-with-submission-manager');     
            
            case 'LABEL_UNREGISTERED_SUB':
                return __("Unregistered user submission", 'custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_IDENTIFICATION':
                return __('User Identification','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_IDENTIFICATION':
                return __('Define which keywords to use to identify users in registration feed.','custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_FEED_HIDE_DATE':
                return __('Do not display user registration date in registration feed.','custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_FEED_HIDE_COUNTRY':
                return __("Do not display registering user's country name or flag in the registration feed.",'custom-registration-form-builder-with-submission-manager');
                    
            case 'HELP_FEED_SHOW_GRAVATAR':
                return __('Display user avatars pulled from Gravatar in registration feed.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_FEED_MAX_ITEMS':
                return __('Define maximum no of recent registration records to be displayed in registration feed.','custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_IMG_SIZE':
                return __('Define Size of the Image.','custom-registration-form-builder-with-submission-manager');
             
            case 'HELP_IMG_EFFECTS':
                return __('Add Effects to the the Image.','custom-registration-form-builder-with-submission-manager');
             
            case 'HELP_IMG_BORDER_COLOR':
                return __('Define Border Color.','custom-registration-form-builder-with-submission-manager'); 
                
            case 'HELP_IMG_BORDER_WIDTH':
                return __('Define Border Width.','custom-registration-form-builder-with-submission-manager');    
             
            case 'HELP_IMG_BORDER_SHAPE':
                return __('Define Border Shape.','custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_IMG_BORDER_SHAPE':
                return __('Define Border Shape.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_WIDGET_IMG_POPUP':
                return __('Display the Image as Popup on Clicking.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_WIDGET_CAPTION':
                return __('Display Image Captions.','custom-registration-form-builder-with-submission-manager');    
                
            case 'HELP_WIDGET_TITLE':
                return __('Display Image Title.','custom-registration-form-builder-with-submission-manager'); 
            
            case 'HELP_NU_FORM_VIEWS':
                return __('Display number of times the form was viewed by site visitors.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_NU_VIEW_TEXT_BEFORE':
                return __("The text before the form view count. You can combine text before and after to render dynamic content like, 'The form was viewed 25 times' (this is just an example). The count will keep updating automatically",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_NU_VIEW_TEXT_AFTER':
                return __('The text after the form view count.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_NU_FORM_SUB':
                return __('Display the number of times this form was submitted.','custom-registration-form-builder-with-submission-manager');  
                
            case 'HELP_NU_SUB_TEXT_BEFORE':
                return __("The text before the form submission count. You can combine text before and after to render dynamic content like, 'A total of 25 users have registered so far.' (this is just an example). The count will keep updating automatically.",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_NU_SUB_TEXT_AFTER':
                return __('The text after the form submission count.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_FD_SUB_LIMITS':
                return __('Display form submission limits, if you have set them in Form Dashboard --> Limits.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_FD_SUB_TEXT_BEFORE':
                return __('Text before submission limit.','custom-registration-form-builder-with-submission-manager');    
                
            case 'HELP_FD_SUB_TEXT_AFTER':
                return __('Text after submission limit.','custom-registration-form-builder-with-submission-manager');  
                
            case 'HELP_FD_SUB_DATE_LIMITS':
                return __('Display form date limits, if you have set them in Form Dashboard --> Limits','custom-registration-form-builder-with-submission-manager');  
                
            case 'HELP_FD_SD_LIMIT_TEXT_BEFORE':
                return __('Text before date limit.','custom-registration-form-builder-with-submission-manager');  
                
            case 'HELP_FD_SD_LIMIT_TEXT_AFTER':
                return __('Text after date limit.','custom-registration-form-builder-with-submission-manager');  
                
            case 'HELP_FD_LS_REC':
                return __('Display last submission time.','custom-registration-form-builder-with-submission-manager');  
                
            case 'HELP_FD_LS_TEXT_BEFORE':
                return __('Text before last submission time.','custom-registration-form-builder-with-submission-manager'); 
                
            case 'HELP_FD_SH_F_NAME':
                return __('Display Name of the Form.','custom-registration-form-builder-with-submission-manager');     
            
            case 'HELP_FD_LS_TEXT_AFTER':
                return __('Text after last submission time.','custom-registration-form-builder-with-submission-manager');     
                
            case 'HELP_FD_F_DESC':
                return __('Display Description of the Form defined in Form Dashboard --> General.','custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_F_CHART_TYPE':
                return __('Select the Chart or graph you wish to display.','custom-registration-form-builder-with-submission-manager');    
            
            case 'HELP_FEED_CUSTOM_TEXT':
                return __('Use your own text instead of system generated text before user identification.','custom-registration-form-builder-with-submission-manager');    
            
             case 'LABEL_BORDER_COLOR':
                return __("Border Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BORDER_WIDTH':
                return __("Border Width", 'custom-registration-form-builder-with-submission-manager');
            
            case 'HELP_LAT':
                 return __('Enter latitude of the marker location.','custom-registration-form-builder-with-submission-manager');    
            
            case 'HELP_LONG':
                 return __('Enter longitude of the marker location.','custom-registration-form-builder-with-submission-manager'); 
            
            case 'HELP_ZOOM':
                return __('Define map zoom level. Zoom levels range between 0 (no zoom) to 18 or 21 (down to street level) depending on the location.','custom-registration-form-builder-with-submission-manager'); 
            
            case 'HELP_MAP_WIDTH':
                 return __('Width of the map widget in the form in pixels (px).','custom-registration-form-builder-with-submission-manager');    
            
            case 'LABEL_SELECTED':
                 return __('Selected','custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_RECORDS':
                return __('Records', 'custom-registration-form-builder-with-submission-manager');
                
            case 'FD_LABEL_LOGIN_FORM_FIELDS':
                return __("Fields", 'custom-registration-form-builder-with-submission-manager');    
                
            case 'LABEL_DISPLAY_AVATAR':
                return __("Displays user avatar when user accesses login box in logged in state.",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_DISPLAY_USERNAME':
                return __("Displays user’s first and last names when user accesses login box in logged in state.",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_DISPLAY_GREETINGS':
                return __("Displays greetings above the user’s name.",'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_GREETING_TEXT':
                return __("Enter the greetings text.",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_DISPLAY_CUSTOM_MSG':
                return __("Displays a custom message when user accesses login box in logged in state.",'custom-registration-form-builder-with-submission-manager');
                
            case 'FIELD_CUSTOM_MSG':
                return __("Enter the custom message you wish to display.",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_FIELD_BAR_COLOR':
                return __("Separator bar color",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_FIELD_BAR_COLOR':
                return __("Select the color for the separator bar.",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_ACCOUNT_TEXT':
                return __("Displays a link to user account page when user accesses login box in logged in state. To make it work, please make sure you have defined default user account page in Global Settings → Default Pages",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_LOGOUT_TEXT':
                return __("Displays a Logout link when user accesses login box in logged in state.",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_ACCOUNT_LINK':
                return __("The link text for user account link.",'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_LOGOUT_LINK':
                return __("The link text for user logout link.",'custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_USERNAME_ERROR':
                return __('The error message users see when they try to login using an invalid username.','custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_PASSWORD_ERROR':
                return __('The error message users see when they try to login using a valid username but invalid password. Use code {{username}} to render the valid username inside the error message. Please remember, confirming a valid username, without the password, may pose security risk. We recommend using a generic error message in both cases.','custom-registration-form-builder-with-submission-manager');
                
            case 'LABEL_SUBMISSION_ERROR':
                return __('The error message users see when they try to access the Submissions page without login.','custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_LABEL_COLOR':
                return __("Label Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_TEXT_COLOR':
                return __("Text Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_PLACEHOLDER_COLOR':
                return __("Placeholder Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_OUTLINE_COLOR':
                return __("Outline Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FOCUS_COLOR':
                return __("Focus Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FOCUS_BG_COLOR':
                return __("Background on Focus", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FORM_PADDING':
                return __("Form Padding", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SECTION_BG_COLOR':
                return __("Section Background Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SECTION_TEXT_COLOR':
                return __("Section Text Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_SECTION_TEXT_STYLE':
                return __("Section Text Style", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BORDER_COLOR':
                return __("Border Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BORDER_WIDTH':
                return __("Border Width", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BORDER_RADIUS':
                return __("Border Radius", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BORDER_STYLE':
                return __("Border Style", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BACKGROUND_IMAGE':
                return __("Background Image", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_IMAGE_REPEAT':
                return __("Image Repeat", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BUTTON_LABEL':
                return __("Button Label", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_FONT_COLOR':
                return __("Font Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_HOVER_COLOR':
                return __("Hover Color", 'custom-registration-form-builder-with-submission-manager');

            case 'LABEL_BACKGROUND_COLOR':
                return __("Background Color", 'custom-registration-form-builder-with-submission-manager');
                
            case 'MSG_ERR_USER_ACCOUNT_NOT_ACTIVATED':
                return __('Account has not been activated yet', 'custom-registration-form-builder-with-submission-manager');
            
            case 'INCATIVE_ACC_MSG':
                return __('Your account has not been activated yet.','custom-registration-form-builder-with-submission-manager');
            
            case 'FIELD_HELP_TEXT_ESign':
                return __('Add field which allows users to upload digital signature image with the form submission.', 'custom-registration-form-builder-with-submission-manager');
                
            case 'HELP_SUBMISSION_PDF_FONT':
                return __('If case you have issue with PDF characters not appearing properly, try changing this option.','custom-registration-form-builder-with-submission-manager');
            
            case 'LABEL_PDF_FONT':
                return __('Submission PDF Font','custom-registration-form-builder-with-submission-manager');
            
            case 'MSG_ASYNC_LOGIN':
                return __("Please wait while we are logging into the system.",'custom-registration-form-builder-with-submission-manager');
            
            case 'HIDE_MAGIC_PANEL_STYLER':
                return __('This will hide MagicPopup styling options on frontend for Admins. If you do not plan to use MagicPopup or have configured it and no longer wish to change its style, you should check this option.', 'custom-registration-form-builder-with-submission-manager');
                
            case 'RM_SOCIAL_ERR_ACC_UNAPPROVED':
                return __('Please wait for admin\'s approval before you can log in.', 'custom-registration-form-builder-with-submission-manager');
                
            case 'RM_SOCIAL_ERR_NEW_ACC_UNAPPROVED':
                return __('Account has been created. Please wait for admin\'s approval before you can log in.', 'custom-registration-form-builder-with-submission-manager');
            
            case 'SORT_FIELD_ORDER_DISC':
                return __('Use grab handles to sort fields order.', 'custom-registration-form-builder-with-submission-manager');
            
            case 'EDIT_BUTTON_LABEL_DISC':
                return __('Click on a button to edit its label.', 'custom-registration-form-builder-with-submission-manager');
                
            default:
                if(defined('REGMAGIC_ADDON'))
                    return RM_UI_Strings_Addon::get($identifier);
                else
                    return __('NO STRING FOUND', 'custom-registration-form-builder-with-submission-manager');
        }
    }

}
