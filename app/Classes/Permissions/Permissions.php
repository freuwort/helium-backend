<?php

namespace App\Classes\Permissions;

class Permissions
{
    /**
     * System permissions:
     */
    public const SYSTEM_SUPER_ADMIN = 'system.super-admin';
    public const SYSTEM_ADMIN = 'system.admin';

    // Access
    public const SYSTEM_ACCESS_ADMIN_PANEL = 'system.access.admin.panel';

    // Roles
    public const SYSTEM_VIEW_ROLES = 'system.view.roles';
    public const SYSTEM_ASSIGN_ROLES = 'system.assign.roles';
    public const SYSTEM_CREATE_ROLES = 'system.create.roles';
    public const SYSTEM_EDIT_ROLES = 'system.edit.roles';
    public const SYSTEM_DELETE_ROLES = 'system.delete.roles';

    // Users
    public const SYSTEM_VIEW_USERS = 'system.view.users';
    public const SYSTEM_CREATE_USERS = 'system.create.users';
    public const SYSTEM_EDIT_USERS = 'system.edit.users';
    public const SYSTEM_DELETE_USERS = 'system.delete.users';
    public const SYSTEM_ENABLE_USERS = 'system.enable.users';
    public const SYSTEM_DISABLE_USERS = 'system.disable.users';

    // Companies
    public const SYSTEM_VIEW_COMPANIES = 'system.view.companies';
    public const SYSTEM_CREATE_COMPANIES = 'system.create.companies';
    public const SYSTEM_EDIT_COMPANIES = 'system.edit.companies';
    public const SYSTEM_DELETE_COMPANIES = 'system.delete.companies';

    // Settings
    public const SYSTEM_VIEW_SETTINGS = 'system.view.settings';
    public const SYSTEM_EDIT_SETTINGS = 'system.edit.settings';
    public const SYSTEM_ENABLE_APPS = 'system.enable.apps';
    public const SYSTEM_DISABLE_APPS = 'system.disable.apps';

    // Media
    public const SYSTEM_VIEW_MEDIA = 'system.view.media';
    public const SYSTEM_UPLOAD_MEDIA = 'system.upload.media';
    public const SYSTEM_EDIT_MEDIA = 'system.edit.media';
    public const SYSTEM_SHARE_MEDIA = 'system.share.media';
    public const SYSTEM_DELETE_MEDIA = 'system.delete.media';



    /**
     * Permissions setup:
     */

    public const GROUPED_PERMISSIONS = [
        'system' => [
            'title' => 'System',
            'permissions' => [
                [
                    ['name' => self::SYSTEM_SUPER_ADMIN, 'label' => 'Super Admin', 'description' => 'Der Super Admin hat Zugriff auf alle Bereiche des Systems und kann Benutzer und Rollen verwalten.', 'readonly' => true],
                    ['name' => self::SYSTEM_ADMIN, 'label' => 'Admin', 'description' => 'Der Admin hat Zugriff auf alle Bereiche des Systems und kann Benutzer und Rollen verwalten.'],
                    ['name' => self::SYSTEM_ACCESS_ADMIN_PANEL, 'label' => 'Zutritt zum Admin Bereich', 'description' => 'Der Benutzer hat Zugriff auf den Admin Bereich.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_ROLES, 'label' => 'Rollen anzeigen', 'description' => 'Der Benutzer kann die Rollen ansehen.'],
                    ['name' => self::SYSTEM_ASSIGN_ROLES, 'label' => 'Rollen zuweisen', 'description' => 'Der Benutzer kann anderen Benutzern Rollen zuweisen.'],
                    ['name' => self::SYSTEM_CREATE_ROLES, 'label' => 'Rollen erstellen', 'description' => 'Der Benutzer kann anderen Benutzern Rollen erstellen.'],
                    ['name' => self::SYSTEM_EDIT_ROLES, 'label' => 'Rollen bearbeiten', 'description' => 'Der Benutzer kann anderen Benutzern Rollen bearbeiten.'],
                    ['name' => self::SYSTEM_DELETE_ROLES, 'label' => 'Rollen löschen', 'description' => 'Der Benutzer kann anderen Benutzern Rollen löschen.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_USERS, 'label' => 'Benutzer anzeigen', 'description' => 'Der Benutzer kann andere Benutzer ansehen.'],
                    ['name' => self::SYSTEM_CREATE_USERS, 'label' => 'Benutzer erstellen', 'description' => 'Der Benutzer kann andere Benutzer erstellen.'],
                    ['name' => self::SYSTEM_EDIT_USERS, 'label' => 'Benutzer bearbeiten', 'description' => 'Der Benutzer kann andere Benutzer bearbeiten.'],
                    ['name' => self::SYSTEM_DELETE_USERS, 'label' => 'Benutzer löschen', 'description' => 'Der Benutzer kann andere Benutzer löschen.'],
                    ['name' => self::SYSTEM_ENABLE_USERS, 'label' => 'Benutzer aktivieren', 'description' => 'Der Benutzer kann andere Benutzer aktivieren.'],
                    ['name' => self::SYSTEM_DISABLE_USERS, 'label' => 'Benutzer deaktivieren', 'description' => 'Der Benutzer kann andere Benutzer deaktivieren.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_COMPANIES, 'label' => 'Firmen anzeigen', 'description' => 'Der Benutzer kann die Firmen ansehen.'],
                    ['name' => self::SYSTEM_CREATE_COMPANIES, 'label' => 'Firmen erstellen', 'description' => 'Der Benutzer kann Firmen erstellen.'],
                    ['name' => self::SYSTEM_EDIT_COMPANIES, 'label' => 'Firmen bearbeiten', 'description' => 'Der Benutzer kann Firmen bearbeiten.'],
                    ['name' => self::SYSTEM_DELETE_COMPANIES, 'label' => 'Firmen löschen', 'description' => 'Der Benutzer kann Firmen löschen.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_SETTINGS, 'label' => 'Einstellungen anzeigen', 'description' => 'Der Benutzer kann die Einstellungen ansehen.'],
                    ['name' => self::SYSTEM_EDIT_SETTINGS, 'label' => 'Einstellungen bearbeiten', 'description' => 'Der Benutzer kann die Einstellungen bearbeiten.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_MEDIA, 'label' => 'Medien anzeigen', 'description' => 'Der Benutzer kann die Medien ansehen.'],
                    ['name' => self::SYSTEM_UPLOAD_MEDIA, 'label' => 'Medien hochladen', 'description' => 'Der Benutzer kann Medien hochladen.'],
                    ['name' => self::SYSTEM_EDIT_MEDIA, 'label' => 'Medien bearbeiten', 'description' => 'Der Benutzer kann Medien bearbeiten.'],
                    ['name' => self::SYSTEM_SHARE_MEDIA, 'label' => 'Medien teilen', 'description' => 'Der Benutzer kann Medien teilen.'],
                    ['name' => self::SYSTEM_DELETE_MEDIA, 'label' => 'Medien löschen', 'description' => 'Der Benutzer kann Medien löschen.'],
                ],
            ],
        ],

        // 'app.pages' => [
        //     'title' => 'Pages',
        //     'permissions' => [
        //         [
        //             ['name' => self::APP_PAGES_ACCESS_FRONTEND, 'label' => 'Zutritt zu Pages', 'description' => 'Der Benutzer hat Zugriff auf das Pages Frontend.'],
        //             ['name' => self::APP_PAGES_ACCESS_ADMIN_PANEL, 'label' => 'Zutritt zum Pages Admin Bereich', 'description' => 'Der Benutzer hat Zugriff auf den Pages Admin Bereich.'],
        //         ],
        //         [
        //             ['name' => self::APP_PAGES_VIEW_PAGES, 'label' => 'Seiten anzeigen', 'description' => 'Der Benutzer kann Seiten ansehen.'],
        //             ['name' => self::APP_PAGES_CREATE_PAGES, 'label' => 'Seiten erstellen', 'description' => 'Der Benutzer kann Seiten erstellen.'],
        //             ['name' => self::APP_PAGES_EDIT_PAGES, 'label' => 'Seiten bearbeiten', 'description' => 'Der Benutzer kann Seiten bearbeiten.'],
        //             ['name' => self::APP_PAGES_DELETE_PAGES, 'label' => 'Seiten löschen', 'description' => 'Der Benutzer kann Seiten löschen.'],
        //         ],
        //         [
        //             ['name' => self::APP_PAGES_VIEW_MENUS, 'label' => 'Menüs anzeigen', 'description' => 'Der Benutzer kann Menüs ansehen.'],
        //             ['name' => self::APP_PAGES_CREATE_MENUS, 'label' => 'Menüs erstellen', 'description' => 'Der Benutzer kann Menüs erstellen.'],
        //             ['name' => self::APP_PAGES_EDIT_MENUS, 'label' => 'Menüs bearbeiten', 'description' => 'Der Benutzer kann Menüs bearbeiten.'],
        //             ['name' => self::APP_PAGES_DELETE_MENUS, 'label' => 'Menüs löschen', 'description' => 'Der Benutzer kann Menüs löschen.'],
        //         ],
        //     ],
        // ],
    ];



    public static function all($extendedInfo = false)
    {
        $groupedPermissions = self::GROUPED_PERMISSIONS;
        
        // flatten permissions
        $permissions = [];

        foreach ($groupedPermissions as $group)
        {
            foreach ($group['permissions'] as $permissionGroup)
            {
                foreach ($permissionGroup as $permission)
                {
                    $permissions[] = ($extendedInfo ? $permission : $permission['name']);
                }
            }
        }

        return $permissions;
    }
}