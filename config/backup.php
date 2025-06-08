<?php

return [
    'store_path' => storage_path('app/backups'),
    'restore_path' => storage_path('app/restore'),
    'include_storage' => [
        'app/domain',
        'app/public',
    ],
    'exclude_tables' => [
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
    ],
];
