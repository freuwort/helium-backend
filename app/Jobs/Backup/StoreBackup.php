<?php

namespace App\Jobs\Backup;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Classes\SimpleZip;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreBackup implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct() {}

    public function handle(): void
    {
        // Create new zip archive
        $zip = new SimpleZip();
        $zipPath = config('backup.store_path').'/backup_'.date('Y-m-d_H-i-s').'.zip';

        if (!$zip->create($zipPath)) {
            Log::error('Failed to create backup zip file', ['path' => $zipPath]);
            throw new \Exception('Failed to create backup zip file');
        }


        // Add metadata
        $zip->addFromString("metadata.json", json_encode([
            'driver_storage' => config('filesystems.default'),
            'driver_database' => config('database.default'),
            'created_at' => now()->toDateTimeString(),
            'include_storage' => config('backup.include_storage'),
        ]));


        // Add media directories
        foreach (config('backup.include_storage') as $relativePath) {
            $zip->addDirectory(storage_path($relativePath), "files/$relativePath");
        }


        // Add database files
        $tables = DB::connection(config('database.default'))->getSchemaBuilder()->getTables();

        foreach ($tables as $table) {
            $table = (object) $table;

            // Skip excluded tables
            if (in_array($table->name, config('backup.exclude_tables'))) continue;

            $data = DB::table($table->name)->get();
            $zip->addFromString("database/$table->name.json", $data->toJson());
        }


        // Close the zip archive
        if (!$zip->close()) {
            Log::error('Failed to close backup zip file', ['path' => $zipPath]);
            throw new \Exception('Failed to close backup zip file');
        }

        
        // Dispatch notification
        // $this->user->notify(new BackupCreated());
    }
}
