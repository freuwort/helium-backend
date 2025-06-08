<?php

namespace App\Jobs\Backup;

use App\Classes\Backup\DatabaseImporter;
use App\Classes\SimpleZip;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RestoreBackup implements ShouldQueue
{
    use Dispatchable, Queueable;

    public string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(): void
    {
        // Unzip the backup file
        $zip = new SimpleZip();
        $restorePath = config('backup.restore_path');

        Log::info('Starting backup restoration from: ' . $this->path);

        if (!$zip->extract($this->path, $restorePath)) {
            Log::error('Failed to extract backup zip file', ['path' => $this->path]);
            throw new \Exception('Failed to extract backup zip file');
        }
        
        File::delete($this->path);


        // Read metadata
        $metadataPath = "$restorePath/metadata.json";
        if (!File::exists($metadataPath)) {
            Log::error('Metadata file not found in backup', ['path' => $metadataPath]);
            throw new \Exception('Metadata file not found in backup');
        }

        $metadata = json_decode(File::get($metadataPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid metadata JSON', ['error' => json_last_error_msg()]);
            throw new \Exception('Invalid metadata JSON');
        }


        // Restore database
        try {
            $databaseImporter = new DatabaseImporter();
            $databaseImporter->disableConstraints();

            foreach (File::files("$restorePath/database") as $databaseFile) {
                $databaseImporter->importFromFile($databaseFile->getRealPath());
            }

            $databaseImporter->enableConstraints();
        }
        catch (\Exception $exception) {
            Log::error('Database import failed', ['error' => $exception->getMessage()]);
            throw new \Exception('Database import failed');
        }


        // Restore files
        foreach ($metadata['include_storage'] as $relativePath) {
            $sourcePath = "$restorePath/files/$relativePath";
            $destinationPath = storage_path($relativePath);

            if (!File::moveDirectory($sourcePath, $destinationPath, true)) {
                Log::error('Failed to move directory', ['source' => $sourcePath, 'destination' => $destinationPath]);
                throw new \Exception('Failed to move directory');
            }
        }


        // Clean up the uploaded backup file
        File::deleteDirectory($restorePath);
    }

    public function failed(\Exception $exception): void
    {
        File::deleteDirectory(config('backup.restore_path'));
    }
}
