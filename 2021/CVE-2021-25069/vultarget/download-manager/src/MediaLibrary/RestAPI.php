<?php
/**
 * User: shahnuralam
 * Date: 8/12/18
 * Time: 2:17 AM
 */

namespace WPDM\MediaLibrary;


class RestAPI
{
    function __construct()
    {
        add_action('rest_api_init', array($this, 'introduceEndpoints'));
    }

    function introduceEndpoints()
    {

        //wpdm/v1/search-package
        register_rest_route('wpdm', '/media-access', array(
            'methods' => 'GET',
            'callback' => [$this, 'mediaAccess'],
            'permission_callback' => function () {
                return current_user_can(WPDM_ADMIN_CAP);
            }
        ));

        /*register_rest_route('wpdm/v1', '/create-term', array(
            'methods' => 'GET',
            'callback' => [$this, 'createTerm'],
            'permission_callback' => function () {
                return current_user_can(WPDM_ADMIN_CAP);
            }
        ));*/

        /*//wpdm/v1/link-templates
        register_rest_route( 'wpdm/v1', '/link-templates', array(
            'methods' => 'GET',
            'callback' => array($this, 'linkTemplates'),
        ) );

        //wpdm/v1/categories
        register_rest_route( 'wpdm/v1', '/categories', array(
            'methods' => 'GET',
            'callback' => array($this, 'categories'),
        ) );*/
    }

    function mediaAccess()
    {
        if (wpdm_query_var('mediaid', 'int') > 0) {

        }
    }
}
