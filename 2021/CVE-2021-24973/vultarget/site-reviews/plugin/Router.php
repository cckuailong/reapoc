<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class Router
{
    /**
     * @var array
     */
    protected $unguardedActions;

    public function __construct()
    {
        $this->unguardedActions = glsr()->filterArray('router/unguarded-actions', [
            'dismiss-notice',
            'fetch-paged-reviews',
            'search-posts',
            'search-users',
        ]);
    }

    /**
     * @return void
     */
    public function routeAdminPostRequest()
    {
        $request = $this->getRequest();
        if ($this->isValidPostRequest($request)) {
            check_admin_referer($request->_action);
            $this->routeRequest('admin', $request);
        }
    }

    /**
     * @return void
     */
    public function routeAjaxRequest()
    {
        $request = $this->getRequest();
        $this->checkAjaxRequest($request);
        $this->checkAjaxNonce($request);
        $this->routeRequest('ajax', $request);
        wp_die();
    }

    /**
     * @return void
     */
    public function routePublicPostRequest()
    {
        if (glsr()->isAdmin()) {
            return;
        }
        $request = $this->getRequest();
        if ($this->isValidPostRequest($request) && $this->isValidPublicNonce($request)) {
            $this->routeRequest('public', $request);
        }
    }

    /**
     * @return void
     */
    protected function checkAjaxNonce(Request $request)
    {
        if (!is_user_logged_in() || in_array($request->_action, $this->unguardedActions)) {
            return;
        }
        if (empty($request->_nonce)) {
            $this->sendAjaxError('request is missing a nonce', $request);
        }
        if (!wp_verify_nonce($request->_nonce, $request->_action)) {
            $this->sendAjaxError('request failed the nonce check', $request, 403);
        }
    }

    /**
     * @return void
     */
    protected function checkAjaxRequest(Request $request)
    {
        if (empty($request->_action)) {
            $this->sendAjaxError('request must include an action', $request);
        }
        if (empty($request->_ajax_request)) {
            $this->sendAjaxError('request is invalid', $request);
        }
    }

    /**
     * All ajax requests in the plugin are triggered by a single action hook: glsr_action,
     * while each ajax route is determined by $_POST[request][_action].
     * @return Request
     */
    protected function getRequest()
    {
        $request = Helper::filterInputArray(glsr()->id);
        if (Helper::filterInput('action') == glsr()->prefix.'action') {
            $request['_ajax_request'] = true;
        }
        if ('submit-review' == Helper::filterInput('_action', $request)) {
            $request['_recaptcha-token'] = Helper::filterInput('g-recaptcha-response');
        }
        return new Request($request);
    }

    /**
     * @return bool
     */
    protected function isValidPostRequest(Request $request)
    {
        return !empty($request->_action) && empty($request->_ajax_request);
    }

    /**
     * @return bool
     */
    protected function isValidPublicNonce(Request $request)
    {
        if (is_user_logged_in() && !wp_verify_nonce($request->_nonce, $request->_action)) {
            glsr_log()->error('nonce check failed for public request')->debug($request);
            return false;
        }
        return true;
    }

    /**
     * @param string $type
     * @return void
     */
    protected function routeRequest($type, Request $request)
    {
        $actionHook = "route/{$type}/{$request->_action}";
        $request = glsr()->filterArray('route/request', $request->toArray(), $request->_action, $type);
        $request = new Request($request);
        glsr()->action($actionHook, $request);
        if (0 === did_action(glsr()->id.'/'.$actionHook)) {
            glsr_log('Unknown '.$type.' router request: '.$request->_action);
        }
    }

    /**
     * @param string $error
     * @param int $statusCode
     * @return void
     */
    protected function sendAjaxError($error, Request $request, $statusCode = 400)
    {
        glsr_log()->error($error)->debug($request);
        glsr(Notice::class)->addError(_x('There was an error (try reloading the page).', 'admin-text', 'site-reviews').' <code>'.$error.'</code>');
        wp_send_json_error([
            'code' => $statusCode,
            'message' => __('The form could not be submitted. Please notify the site administrator.', 'site-reviews'),
            'notices' => glsr(Notice::class)->get(),
            'error' => $error,
        ]);
    }
}
