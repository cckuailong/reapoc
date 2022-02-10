<?php

namespace GeminiLabs\SiteReviews;

class Role
{
    /**
     * @param string $role
     * @return void
     */
    public function addCapabilities($role)
    {
        $roles = $this->roles();
        $wpRole = get_role($role);
        if (empty($wpRole) || !array_key_exists($role, $roles)) {
            return;
        }
        foreach ($roles[$role] as $capability) {
            $wpRole->add_cap($this->capability($capability));
        }
    }

    /**
     * @param string $capability
     * @param mixed ...$args
     * @return bool
     */
    public function can($capability, ...$args)
    {
        return in_array($capability, $this->capabilities())
            ? current_user_can($this->capability($capability), ...$args)
            : current_user_can($capability, ...$args);
    }

    /**
     * @return array
     */
    public function capabilities()
    {
        return [
            'create_posts',
            'delete_others_posts',
            'delete_post',
            'delete_posts',
            'delete_private_posts',
            'delete_published_posts',
            'edit_others_posts',
            'edit_post',
            'edit_posts',
            'edit_private_posts',
            'edit_published_posts',
            'publish_posts',
            'read_post',
            'read_private_posts',
            'respond_to_others_post',
            'respond_to_others_posts',
            'respond_to_post',
            'respond_to_posts',
        ];
    }

    /**
     * @param string $capability
     * @return string
     */
    public function capability($capability)
    {
        return str_replace('post', glsr()->post_type, $capability);
    }

    /**
     * @return void
     */
    public function hardResetAll()
    {
        $roles = array_keys($this->roles());
        array_walk($roles, [$this, 'removeCapabilities']);
        array_walk($roles, [$this, 'addCapabilities']);
    }

    /**
     * @param string $role
     * @return void
     */
    public function removeCapabilities($role)
    {
        $wpRole = get_role($role);
        if (empty($wpRole) || 'administrator' === $role) { // do not remove from administrator role
            return;
        }
        foreach ($this->capabilities() as $capability) {
            $wpRole->remove_cap($this->capability($capability));
        }
    }

    /**
     * @return void
     */
    public function resetAll()
    {
        $roles = array_keys($this->roles());
        array_walk($roles, [$this, 'addCapabilities']);
    }

    /**
     * @return array
     */
    public function roles()
    {
        return [
            'administrator' => [
                'create_posts',
                'delete_others_posts',
                'delete_posts',
                'delete_private_posts',
                'delete_published_posts',
                'edit_others_posts',
                'edit_posts',
                'edit_private_posts',
                'edit_published_posts',
                'publish_posts',
                'read_private_posts',
                'respond_to_others_posts',
                'respond_to_posts',
            ],
            'editor' => [
                'create_posts',
                'delete_others_posts',
                'delete_posts',
                'delete_private_posts',
                'delete_published_posts',
                'edit_others_posts',
                'edit_posts',
                'edit_private_posts',
                'edit_published_posts',
                'publish_posts',
                'read_private_posts',
                'respond_to_others_posts',
                'respond_to_posts',
            ],
            'author' => [
                'create_posts',
                'delete_posts',
                'delete_published_posts',
                'edit_posts',
                'edit_published_posts',
                'publish_posts',
                'respond_to_posts',
            ],
            'contributor' => [
                'delete_posts',
                'edit_posts',
                'respond_to_posts',
            ],
        ];
    }
}
