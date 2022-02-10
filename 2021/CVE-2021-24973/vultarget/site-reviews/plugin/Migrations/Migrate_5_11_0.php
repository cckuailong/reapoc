<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Role;

class Migrate_5_11_0
{
    /**
     * @return bool
     */
    public function run()
    {
        return $this->migrateRoles();
    }

    /**
     * @return bool
     */
    protected function migrateRoles()
    {
        $roles = glsr(Role::class)->roles();
        $newCapabilities = ['create_posts', 'respond_to_posts', 'respond_to_others_posts'];
        foreach ($roles as $role => $capabilities) {
            foreach ($newCapabilities as $capability) {
                if (!in_array($capability, $capabilities)) {
                    continue;
                }
                $wpRole = get_role($role);
                if (!empty($wpRole)) {
                    $wpRole->add_cap(glsr(Role::class)->capability($capability));
                }
            }
        }
        return true;
    }
}
