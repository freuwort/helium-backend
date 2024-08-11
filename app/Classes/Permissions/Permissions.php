<?php

namespace App\Classes\Permissions;

use Illuminate\Support\Collection;

class Permissions
{
    /**
     * System permissions:
     */

    // Admin
    public const SYSTEM_SUPER_ADMIN = 'system.super-admin'; // Only assigned to root user
    public const SYSTEM_ADMIN = 'system.admin';

    // Access
    public const SYSTEM_ACCESS_ADMIN_PANEL = 'system.access.admin.panel';

    // Roles
    public const SYSTEM_VIEW_ROLES = 'system.view.roles';
    public const SYSTEM_CREATE_ROLES = 'system.create.roles';
    public const SYSTEM_EDIT_ROLES = 'system.edit.roles';
    public const SYSTEM_DELETE_ROLES = 'system.delete.roles';
    public const SYSTEM_ASSIGN_ROLES = 'system.assign.roles';

    // Users
    public const SYSTEM_VIEW_USERS = 'system.view.users';
    public const SYSTEM_CREATE_USERS = 'system.create.users';
    public const SYSTEM_EDIT_USERS = 'system.edit.users';
    public const SYSTEM_DELETE_USERS = 'system.delete.users';
    public const SYSTEM_ENABLE_USERS = 'system.enable.users';

    // Companies
    public const SYSTEM_VIEW_COMPANIES = 'system.view.companies';
    public const SYSTEM_CREATE_COMPANIES = 'system.create.companies';
    public const SYSTEM_EDIT_COMPANIES = 'system.edit.companies';
    public const SYSTEM_DELETE_COMPANIES = 'system.delete.companies';

    // Media
    public const SYSTEM_ACCESS_MEDIA = 'system.access.media';


    /**
     * App permissions:
     */

    // Forms
    public const APP_VIEW_FORMS = 'app.view.forms';
    public const APP_CREATE_FORMS = 'app.create.forms';
    public const APP_EDIT_FORMS = 'app.edit.forms';
    public const APP_DELETE_FORMS = 'app.delete.forms';

    // Event
    public const APP_VIEW_EVENTS = 'app.view.events';
    public const APP_CREATE_EVENTS = 'app.create.events';
    public const APP_EDIT_EVENTS = 'app.edit.events';
    public const APP_DELETE_EVENTS = 'app.delete.events';

    // Content
    public const APP_VIEW_CONTENT = 'app.view.content';

    public const APP_CREATE_CONTENTSPACES = 'app.create.content.spaces';
    public const APP_EDIT_CONTENTSPACES = 'app.edit.content.spaces';
    public const APP_DELETE_CONTENTSPACES = 'app.delete.content.spaces';

    public const APP_CREATE_CONTENTCATEGORIES = 'app.create.content.categories';
    public const APP_EDIT_CONTENTCATEGORIES = 'app.edit.content.categories';
    public const APP_DELETE_CONTENTCATEGORIES = 'app.delete.content.categories';



    /**
     * Permissions setup:
     */
    public const GROUPED_PERMISSIONS = [
        'system' => [
            'title' => 'System Berechtigungen',
            'permissions' => [
                [
                    ['name' => self::SYSTEM_ADMIN, 'label' => 'Admin', 'description' => 'Der Admin hat Zugriff auf alle Bereiche des Systems und kann Benutzer und Rollen verwalten.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_ROLES, 'label' => 'Rollen anzeigen', 'description' => 'Der Benutzer kann die Rollen ansehen.'],
                    ['name' => self::SYSTEM_CREATE_ROLES, 'label' => 'Rollen erstellen', 'description' => 'Der Benutzer kann anderen Benutzern Rollen erstellen.'],
                    ['name' => self::SYSTEM_EDIT_ROLES, 'label' => 'Rollen bearbeiten', 'description' => 'Der Benutzer kann anderen Benutzern Rollen bearbeiten.'],
                    ['name' => self::SYSTEM_DELETE_ROLES, 'label' => 'Rollen löschen', 'description' => 'Der Benutzer kann anderen Benutzern Rollen löschen.'],
                    ['name' => self::SYSTEM_ASSIGN_ROLES, 'label' => 'Rollen zuweisen', 'description' => 'Der Benutzer kann anderen Benutzern Rollen zuweisen.'],
                ],
                [
                    ['name' => self::SYSTEM_ACCESS_ADMIN_PANEL, 'label' => 'Zutritt zum Admin Bereich', 'description' => 'Der Benutzer hat Zugriff auf den Admin Bereich.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_USERS, 'label' => 'Benutzer anzeigen', 'description' => 'Der Benutzer kann andere Benutzer ansehen.'],
                    ['name' => self::SYSTEM_CREATE_USERS, 'label' => 'Benutzer erstellen', 'description' => 'Der Benutzer kann andere Benutzer erstellen.'],
                    ['name' => self::SYSTEM_EDIT_USERS, 'label' => 'Benutzer bearbeiten', 'description' => 'Der Benutzer kann andere Benutzer bearbeiten.'],
                    ['name' => self::SYSTEM_DELETE_USERS, 'label' => 'Benutzer löschen', 'description' => 'Der Benutzer kann andere Benutzer löschen.'],
                    ['name' => self::SYSTEM_ENABLE_USERS, 'label' => 'Benutzer aktivieren', 'description' => 'Der Benutzer kann andere Benutzer aktivieren/deaktivieren.'],
                ],
                [
                    ['name' => self::SYSTEM_VIEW_COMPANIES, 'label' => 'Firmen anzeigen', 'description' => 'Der Benutzer kann die Firmen ansehen.'],
                    ['name' => self::SYSTEM_CREATE_COMPANIES, 'label' => 'Firmen erstellen', 'description' => 'Der Benutzer kann Firmen erstellen.'],
                    ['name' => self::SYSTEM_EDIT_COMPANIES, 'label' => 'Firmen bearbeiten', 'description' => 'Der Benutzer kann Firmen bearbeiten.'],
                    ['name' => self::SYSTEM_DELETE_COMPANIES, 'label' => 'Firmen löschen', 'description' => 'Der Benutzer kann Firmen löschen.'],
                ],
                [
                    ['name' => self::SYSTEM_ACCESS_MEDIA, 'label' => 'Medien Zugriff', 'description' => 'Der Benutzer kann auf die Medien zugreifen.'],
                ],
            ],
        ],

        'app.forms' => [
            'title' => 'App – Formulare',
            'permissions' => [
                [
                    ['name' => self::APP_VIEW_FORMS, 'label' => 'Formulare anzeigen', 'description' => 'Der Benutzer kann Formulare ansehen.'],
                    ['name' => self::APP_CREATE_FORMS, 'label' => 'Formulare erstellen', 'description' => 'Der Benutzer kann Formulare erstellen.'],
                    ['name' => self::APP_EDIT_FORMS, 'label' => 'Formulare bearbeiten', 'description' => 'Der Benutzer kann Formulare bearbeiten.'],
                    ['name' => self::APP_DELETE_FORMS, 'label' => 'Formulare löschen', 'description' => 'Der Benutzer kann Formulare löschen.'],
                ],
            ],
        ],

        'app.events' => [
            'title' => 'App – Events',
            'permissions' => [
                [
                    ['name' => self::APP_VIEW_EVENTS, 'label' => 'Events anzeigen', 'description' => 'Der Benutzer kann Events ansehen.'],
                    ['name' => self::APP_CREATE_EVENTS, 'label' => 'Events erstellen', 'description' => 'Der Benutzer kann Events erstellen.'],
                    ['name' => self::APP_EDIT_EVENTS, 'label' => 'Events bearbeiten', 'description' => 'Der Benutzer kann Events bearbeiten.'],
                    ['name' => self::APP_DELETE_EVENTS, 'label' => 'Events löschen', 'description' => 'Der Benutzer kann Events löschen.'],
                ],
            ],
        ],

        'app.content' => [
            'title' => 'App – Inhalte',
            'permissions' => [
                [
                    ['name' => self::APP_VIEW_CONTENT, 'label' => 'Zugriff auf den Inhalte-Bereich', 'description' => 'Der Benutzer kann auf den Bereich "Inhalte" zugreifen.'],
                ],
                [
                    ['name' => self::APP_CREATE_CONTENTSPACES, 'label' => 'Spaces erstellen', 'description' => 'Der Benutzer kann Spaces für Inhalte erstellen.'],
                    ['name' => self::APP_EDIT_CONTENTSPACES, 'label' => 'Spaces bearbeiten', 'description' => 'Der Benutzer kann Spaces für Inhalte bearbeiten.'],
                    ['name' => self::APP_DELETE_CONTENTSPACES, 'label' => 'Spaces löschen', 'description' => 'Der Benutzer kann Spaces für Inhalte löschen.'],
                ],
                [
                    ['name' => self::APP_CREATE_CONTENTCATEGORIES, 'label' => 'Kategorien erstellen', 'description' => 'Der Benutzer kann Kategorien für Inhalte erstellen.'],
                    ['name' => self::APP_EDIT_CONTENTCATEGORIES, 'label' => 'Kategorien bearbeiten', 'description' => 'Der Benutzer kann Kategorien für Inhalte bearbeiten.'],
                    ['name' => self::APP_DELETE_CONTENTCATEGORIES, 'label' => 'Kategorien löschen', 'description' => 'Der Benutzer kann Kategorien für Inhalte löschen.'],
                ],
            ],
        ],
    ];

    public const ADMIN_PERMISSIONS = [
        self::SYSTEM_SUPER_ADMIN,
        self::SYSTEM_ADMIN,
    ];

    public const FORBIDDEN_PERMISSIONS = [
        self::SYSTEM_SUPER_ADMIN,
    ];

    public const ELEVATED_PERMISSIONS = [
        self::SYSTEM_ADMIN,
        self::SYSTEM_CREATE_ROLES,
        self::SYSTEM_EDIT_ROLES,
        self::SYSTEM_DELETE_ROLES,
        self::SYSTEM_ASSIGN_ROLES,
    ];



    public static function all($extendedInfo = false, $includeSuperAdmin = false): array
    {
        $groupedPermissions = self::GROUPED_PERMISSIONS;

        if ($includeSuperAdmin)
        {
            $groupedPermissions['system']['permissions'][0][] = ['name' => self::SYSTEM_SUPER_ADMIN, 'label' => 'Super Admin', 'description' => 'Der Super Admin hat Zugriff auf alle Bereiche des Systems und kann Benutzer und Rollen verwalten.'];
        }
        
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



    public static function partOfAdmin(array|Collection $permissions): bool
    {
        return collect($permissions)->intersect(self::ADMIN_PERMISSIONS)->isNotEmpty();
    }

    public static function partOfForbidden(array|Collection $permissions): bool
    {
        return collect($permissions)->intersect(self::FORBIDDEN_PERMISSIONS)->isNotEmpty();
    }

    public static function partOfElevated(array|Collection $permissions): bool
    {
        return collect($permissions)->intersect(self::ELEVATED_PERMISSIONS)->isNotEmpty();
    }
}