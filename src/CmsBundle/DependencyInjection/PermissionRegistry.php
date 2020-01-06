<?php

namespace Opifer\CmsBundle\DependencyInjection;

class PermissionRegistry
{
    protected $permissions = [
        'CONFIG_INDEX',
        'CONTENT_INDEX',
        'CONTENT_CREATE',
        'CONTENT_DELETE',
        'CONTENT_DESIGNER',
        'CONTENT_DUPLICATE',
        'CONTENT_TYPE_INDEX',
        'CONTENT_TYPE_CREATE',
        'CONTENT_TYPE_DELETE',
        'CONTENT_TYPE_EDIT',
        'CRONJOB_INDEX',
        'CRONJOB_CREATE',
        'CRONJOB_EDIT',
        'CRONJOB_DELETE',
        'CRONJOB_RESET',
        'DASHBOARD_INDEX',
        'DOMAIN_INDEX',
        'DOMAIN_CREATE',
        'DOMAIN_EDIT',
        'FORM_INDEX',
        'FORM_CREATE',
        'FORM_DELETE',
        'FORM_EDIT',
        'LAYOUT_INDEX',
        'LAYOUT_CREATE',
        'LAYOUT_EDIT',
        'LAYOUT_DETAILS',
        'LAYOUT_DELETE',
        'LOCALE_INDEX',
        'LOCALE_CREATE',
        'LOCALE_EDIT',
        'MAILINGLIST_INDEX',
        'MAILINGLIST_CREATE',
        'MAILINGLIST_EDIT',
        'MAILINGLIST_SUBSCRIPTIONS',
        'MAILINGLIST_DELETE',
        'MEDIA_INDEX',
        'MEDIA_CREATE',
        'MEDIA_EDIT',
        'MEDIA_DELETE',
        'POST_INDEX',
        'POST_DELETE',
        'POST_NOTIFICATION',
        'POST_LIST',
        'POST_VIEW',
        'REDIRECT_INDEX',
        'REDIRECT_CREATE',
        'REDIRECT_DELETE',
        'REDIRECT_EDIT',
        'REVIEW_INDEX',
        'REVIEW_CREATE',
        'REVIEW_EDIT',
        'REVIEW_DELETE',
        'SITE_INDEX',
        'SITE_CREATE',
        'SITE_EDIT',
        'SITE_DELETE',
        'SUBSCRIPTION_INDEX',
        'TEMPLATE_INDEX',
        'TEMPLATE_CREATE',
        'TEMPLATE_EDIT',
        'TEMPLATE_DELETE',
        'USER_INDEX',
        'USER_EDIT',
        'USER_PROFILE',
        'USER_CREATE',
    ];

    public function addPermission(string $permission)
    {
         $this->permissions[] = $permission;
    }

    public function addPermissions(array $permissions)
    {
        $this->permissions = array_merge($this->permissions, $permissions);
    }

    public function hasPermission(string $permission) : bool
    {
        return in_array($permission, $this->permissions);
    }
}
