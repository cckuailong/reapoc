<?php
/*
 * This document is intentionally not translatable, as it is intended to be for US citizens, and should therefore always be in English
 *
 * */
defined('ABSPATH') or die("you do not have acces to this page!");

$this->pages['ca']['cookie-statement']['document_elements'] = array(
    array(
        'content' => '<i>' . sprintf(_x("This page was last changed on %s, last checked on %s and applies to citizens and legal permanent residents of Canada.","Legal document cookie policy","complianz-gdpr"), '[publish_date]', '[checked_date]') . '</i><br>',
    ),
    array(
	    'title' => _x('Introduction', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => sprintf(_x('Our website, %s (hereinafter: "the website") uses cookies and other related technologies (for convenience all technologies are referred to as "cookies"). Cookies are also placed by third parties we have engaged. In the document below we inform you about the use of cookies on our website.',"Legal document cookie policy","complianz-gdpr"), '[domain]', '[article-cookie_names]'),
    ),

    array(
        'title' => _x('What are cookies?', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('A cookie is a small simple file that is sent along with pages of this website and stored by your browser on the hard drive of your computer or another device. The information stored therein may be returned to our servers or to the servers of the relevant third parties during a subsequent visit.','Legal document cookie policy',"complianz-gdpr"),
    ),
    array(
        'title' => _x('What are scripts?', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('A script is a piece of program code that is used to make our website function properly and interactively. This code is executed on our server or on your device.',"Legal document cookie policy","complianz-gdpr"),
    ),
    array(
        'title' => _x('What is a web beacon?', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('A web beacon (or a pixel tag) is a small, invisible piece of text or image on a website that is used to monitor traffic on a website. In order to do this, various data about you is stored using web beacons.',"Legal document cookie policy","complianz-gdpr"),
        'callback_condition' => 'NOT cmplz_uses_only_functional_cookies',
    ),
    array(
        'title' => _x('Third parties', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('We have made agreements about the use of cookies with other companies that place cookies. However, we cannot guarantee that these third parties handle your personal data in a reliable or secure manner. Parties such as Google are to be considered as independent data controllers. We recommend that you read the privacy statements of these companies.',"Legal document cookie policy","complianz-gdpr"),
        'callback_condition' => 'cmplz_site_shares_data',
    ),
    array(
        'title' => _x('Cookies', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
    ),
    array(
        'subtitle' => _x('Technical or functional cookies', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('Some cookies ensure that certain parts of the website work properly and that your user preferences remain known. By placing functional cookies, we make it easier for you to visit our website. This way, you do not need to repeatedly enter the same information when visiting our website and, for example, the items remain in your shopping cart until you have paid.','Legal document cookie policy', 'complianz-gdpr'),
    ),

    //statistics
    array(
        'subtitle' => _x('Statistics cookies', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('We use statistics cookies to optimize the website experience for our users. With these statistics cookies we get insights in the usage of our website.','Legal document cookie policy', 'complianz-gdpr'),
        'callback_condition' => 'cmplz_uses_statistics',
        'condition' => array('compile_statistics' => 'NOT no'),
    ),

    //ads
    array(
        'subtitle' => _x('Advertising cookies', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => sprintf(_x('On this website we use advertising cookies, enabling us to personalize the advertisements for you, and we (and third parties) gain insights into the campaign results. This happens based on a profile we create based on your click and surfing on and outside %s. With these cookies you, as website visitor are linked to a unique ID, so you do not see the same ad more than once for example.','Legal document cookie policy', 'complianz-gdpr'), '[domain]'),
        'condition' => array(
            'uses_ad_cookies' => 'yes',
            'uses_ad_cookies_personalized' => 'NOT no'
        ),
    ),

    array(
        'subtitle' => _x('Advertising cookies', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => sprintf(_x('On this website we use advertising cookies, enabling us to gain insights into the campaign results. This happens based on a profile we create based on your behavior on %s. With these cookies you, as website visitor, are linked to a unique ID but these cookies will not profile your behavior and interests to serve personalized ads.','Legal document cookie policy', 'complianz-gdpr'), '[domain]'),
        'condition' => array(
            'uses_ad_cookies' => 'yes',
            'uses_ad_cookies_personalized' => 'no'
        ),
    ),

    array(
        'content' => _x('You can object to the tracking by these cookies by clicking the "Manage Consent" button.','Legal document cookie policy', 'complianz-gdpr'),
        'condition' => array(
            'uses_ad_cookies' => 'yes',
        ),
    ),

	array(
		'subtitle' => _x('Marketing/Tracking cookies', 'cookie policy', 'complianz-gdpr'),
		'content' => _x('Marketing/Tracking cookies are cookies or any other form of local storage, used to create user profiles to display advertising or to track the user on this website or across several websites for similar marketing purposes.', 'cookie policy', 'complianz-gdpr'),
//		'condition' => array(
//			'uses_ad_cookies' => 'no',
//		),
		'callback_condition' => 'cmplz_uses_marketing_cookies',
	),

    array(
        'subtitle' => _x('Social media buttons', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => sprintf(_x('On our website we have included buttons for %s to promote webpages (e.g. “like”, “pin”) or share (e.g. “tweet”) on social networks like %s. These buttons work using pieces of code coming from %s themselves. This code places cookies. These social media buttons also can store and process certain information, so a personalized advertisement can be shown to you.','Legal document cookie policy', 'complianz-gdpr'), '[comma_socialmedia_on_site]', '[comma_socialmedia_on_site]', '[comma_socialmedia_on_site]'),
        'condition' => array('uses_social_media' => 'yes'),
    ),

    array(
        'content' => _x('Please read the privacy statement of these social networks (which can change regularly) to read what they do with your (personal) data which they process using these cookies. The data that is retrieved is anonymized as much as possible.','Legal document cookie policy', 'complianz-gdpr').' '.sprintf( _n( '%s is located in the United States.', '%s are located in the United States.',  cmplz_count_socialmedia(), 'complianz-gdpr'  ) ,'[comma_socialmedia_on_site]' ),
        'condition' => array('uses_social_media' => 'yes'),
    ),

	'cookie_names' => array(
		'title' => _x('Placed cookies', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
		'callback' => 'cmplz_used_cookies',
	),

  array(
      'title' => _x('Consent', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
      'content' => _x('When you visit our website for the first time, we will show you a pop-up with an explanation about cookies. You do have the right to opt-out and to object against the further use of non-functional cookies.',"Legal document cookie policy","complianz-gdpr"),
  ),
  array(
    'subtitle' => _x('Manage your consent settings', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
    'content' => '[cmplz-manage-consent]',
  ),
array(
  'content' =>_x('You can also disable the use of cookies via your browser, but please note that our website may no longer work properly.', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
),
array(
  'subtitle' => _x('Vendors', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
  'p' => false,
  'content' => '[cmplz-tcf-vendors]',
  'callback_condition' => array(
    'cmplz_tcf_active',
    'cmplz_site_shares_data',
  ),
  'condition' => array(
    'sensitive_information_processed' => 'yes'
  ),

),

    array(
	    'title' => _x('Your rights with respect to personal data', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'p'=> false,
        'content' =>
            '<p>'._x('You have the following rights with respect to your personal data:','Legal document cookie policy', 'complianz-gdpr').'</p>' .
            '<ul>
                <li>'._x('you may submit a request for access to the data we process about you;','Legal document cookie policy', 'complianz-gdpr').'</li>
                <li>'._x('you may object to the processing;','Legal document cookie policy', 'complianz-gdpr').'</li>
                <li>'._x('you may request an overview, in a commonly used format, of the data we process about you;','Legal document cookie policy', 'complianz-gdpr').'</li>
                <li>'._x('you may request correction or deletion of the data if it is incorrect or not or no longer relevant. Where appropriate, the amended information shall be transmitted to third parties having access to the information in question.','Legal document cookie policy', 'complianz-gdpr').'</li>
                <li>'._x('You have the right to withdraw consent at any time, subject to legal or contractual restrictions and reasonable notice. You will be informed of the implications of such withdrawal.','Legal document cookie policy', 'complianz-gdpr').'</li>
                <li>'._x('You have the right to address a challenge concerning non-compliance with PIPEDA to our organization and, if the issue is not resolved, to the Office of the Privacy Commissioner of Canada.','Legal document cookie policy', 'complianz-gdpr').'</li>
            </ul>' .
            '<p>'._x('To exercise these rights, please contact us. Please refer to the contact details at the bottom of this Cookie Policy. If you have a complaint about how we handle your data, we would like to hear from you.','Legal document cookie policy', 'complianz-gdpr').'</p>',
    ),

    array(
        'title' => _x('Enabling/disabling and deleting cookies', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('You can use your internet browser to automatically or manually delete cookies. You can also specify that certain cookies may not be placed. Another option is to change the settings of your internet browser so that you receive a message each time a cookie is placed. For more information about these options, please refer to the instructions in the Help section of your browser. Or you can indicate your preferences on the following page: https://youradchoices.ca ' ,'Legal document cookie policy', 'complianz-gdpr'),
    ),

    array(
	    'title' => _x('Contact details', 'Legal document cookie policy:paragraph title', 'complianz-gdpr'),
        'content' => _x('For questions and/or comments about our Cookie Policy and this statement, please contact us by using the following contact details:','Legal document cookie policy', 'complianz-gdpr'),
    ),

    array(
        'content' => '[organisation_name]<br>
                    [address_company]<br>
                    [country_company]<br>
                    Website: [domain] <br>
                    Email: [email_company] <br>
                    [telephone_company]',
    ),

    array(
        'content' => sprintf(_x('This Cookie Policy was synchronized with %scookiedatabase.org%s on %s', 'Legal document cookie policy', 'complianz-gdpr'),'<a href="https://cookiedatabase.org" target="_blank">', '</a>', '[sync_date]'),
        'callback_condition' => array(
	        'cmplz_cdb_reference_in_policy',
        )
    ),

);
