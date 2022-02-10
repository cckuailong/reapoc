<?php

namespace RebelCode\Wpra\Core\RestApi\Auth;

/**
 * A REST API auth handler that checks if the request is sent by a logged in administrator user.
 *
 * @since 4.13
 */
class AuthUserIsAdmin extends AbstractAuthValidator
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function _getValidationErrors($subject)
    {
        $userId = get_current_user_id();

        // If user is not logged in, return with a single error
        if ($userId === 0) {
            return [
                __('Not a user or not logged in', 'wprss')
            ];
        }

        if (user_can($userId, 'manage_options')) {
            return [];
        }

        return [
            __('User is not an administrator', 'wprss')
        ];
    }
}
