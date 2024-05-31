<?php

namespace App\Classes\Permissions;

class Permissions
{
    /**
     * System permissions:
     */

    // Admin
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
    public const SYSTEM_ACCESS_MEDIA = 'system.access.media';


    /**
     * App permissions:
     */

    // Event
    public const APP_VIEW_EVENTS = 'app.view.events';
    public const APP_CREATE_EVENTS = 'app.create.events';
    public const APP_EDIT_EVENTS = 'app.edit.events';
    public const APP_DELETE_EVENTS = 'app.delete.events';




    /**
     * Permissions setup:
     */
    public const GROUPED_PERMISSIONS = [
        'system' => [
            'title' => 'System Berechtigungen',
            'permissions' => [
                [
                    ['name' => self::SYSTEM_SUPER_ADMIN, 'label' => 'Super Admin', 'description' => 'Der Super Admin hat Zugriff auf alle Bereiche des Systems und kann Benutzer und Rollen verwalten.'],
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
                    ['name' => self::SYSTEM_ACCESS_MEDIA, 'label' => 'Medien-App Zugriff', 'description' => 'Der Benutzer kann auf die Medien-App zugreifen.'],
                ],
            ],
        ],

        'app.events' => [
            'title' => 'App Berechtigungen: Events',
            'permissions' => [
                [
                    ['name' => self::APP_VIEW_EVENTS, 'label' => 'Events anzeigen', 'description' => 'Der Benutzer kann Events ansehen.'],
                    ['name' => self::APP_CREATE_EVENTS, 'label' => 'Events erstellen', 'description' => 'Der Benutzer kann Events erstellen.'],
                    ['name' => self::APP_EDIT_EVENTS, 'label' => 'Events bearbeiten', 'description' => 'Der Benutzer kann Events bearbeiten.'],
                    ['name' => self::APP_DELETE_EVENTS, 'label' => 'Events löschen', 'description' => 'Der Benutzer kann Events löschen.'],
                ],
            ],
        ],
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