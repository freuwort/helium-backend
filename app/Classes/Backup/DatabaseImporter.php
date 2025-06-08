<?php

namespace App\Classes\Backup;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;


class DatabaseImporter
{
    protected string $driver;
    protected array $constraints = [];

    public function __construct()
    {
        $this->driver = DB::getDriverName();

        if (!in_array($this->driver, ['pgsql', 'mysql', 'sqlite'])) {
            Log::error("Unsupported database driver: {$this->driver}");
            throw new Exception("Unsupported database driver: {$this->driver}");
        }
    }

    public function disableConstraints(): void
    {
        Log::info('Disabling database constraints...');

        if ($this->driver === 'pgsql') {
            $this->constraints = DB::select(<<<SQL
                SELECT
                    con.conname AS constraint_name,
                    conrelid::regclass::text AS table_name,
                    pg_get_constraintdef(con.oid) AS constraint_definition
                FROM pg_constraint con
                JOIN pg_class rel ON rel.oid = con.conrelid
                WHERE con.contype = 'f'
            SQL);

            foreach ($this->constraints as $c) {
                DB::statement("ALTER TABLE \"{$c->table_name}\" DROP CONSTRAINT \"{$c->constraint_name}\";");
            }
        }
        elseif ($this->driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        elseif ($this->driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }
    }

    public function enableConstraints(): void
    {
        Log::info('Enabling database constraints...');

        if ($this->driver === 'pgsql') {
            foreach ($this->constraints as $c) {
                DB::statement("ALTER TABLE \"{$c->table_name}\" ADD CONSTRAINT \"{$c->constraint_name}\" {$c->constraint_definition};");
            }
        }
        elseif ($this->driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        elseif ($this->driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }

    public function import(string $tableName, array $data): void
    {
        // Check if the table name is valid
        if (!is_array($data) || empty($data)) {
            Log::warning("No data provided for table '$tableName'. Skipping import.");
            return;
        }

        // Check if the table exists
        if (!DB::getSchemaBuilder()->hasTable($tableName)) {
            Log::warning("Table '$tableName' does not exist. Skipping import.");
            return;
        }

        // Truncate the table if it is not empty
        if (DB::table($tableName)->count() > 0) {
            Log::info("Table '$tableName' is not empty. Truncating before import.");

            try {
                DB::table($tableName)->truncate();
            }
            catch (QueryException $e) {
                Log::warning("Table '$tableName' could not be truncated: {$e->getMessage()}");
                DB::table($tableName)->delete();
            }
        }

        // Import the data
        Log::info("Importing data into table '$tableName'...");
        DB::table($tableName)->insert($data);
    }

    public function importFromFile(string $filePath): void
    {
        Log::info("Importing data from file: $filePath");
        
        if (!File::exists($filePath)) {
            Log::error("File not found: $filePath");
            throw new Exception("File not found: $filePath");
        }

        $tableName = pathinfo($filePath, PATHINFO_FILENAME);

        try {
            $data = json_decode(File::get($filePath), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("JSON decode error: " . json_last_error_msg());
                throw new Exception("Invalid JSON in file: $filePath");
            }

            $this->import($tableName, $data);
        }
        catch (Exception $e) {
            Log::error("Failed to import data from file '$filePath': " . $e->getMessage());
            throw $e;
        }
    }
}