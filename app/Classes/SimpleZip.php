<?php

namespace App\Classes;

use Illuminate\Support\Facades\File;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class SimpleZip
{
    protected ZipArchive $zip;

    public function __construct()
    {
        $this->zip = new ZipArchive();
    }

    /**
     * Opens a zip archive.
     *
     * @param string $path The path where the zip file will be created.
     * @param int|null $flags Optional flags for opening the zip archive.
     * @return bool Returns true on success, false on failure.
     */
    public function open(string $path, ?int $flags = null): bool
    {
        return $this->zip->open($path, $flags) === true;
    }

    public function create(string $path, int $flags = ZipArchive::CREATE | ZipArchive::OVERWRITE): bool
    {
        return $this->open($path, $flags);
    }

    /**
     * Adds all files from a directory to the zip archive recursively.
     *
     * @param string $directory The directory to add.
     */
    public function addDirectory(string $directory, ?string $prefix = ''): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($directory) + 1);
                $this->zip->addFile($filePath, "$prefix/$relativePath");
            }
        }
    }

    /**
     * Adds a file to the zip archive.
     *
     * @param string $file The file to add.
     * @param string|null $localName Optional local name for the file in the zip archive.
     */
    public function addFile(string $file, ?string $localName = null): void
    {
        $this->zip->addFile($file, $localName);
    }

    public function addFromString(string $localName, string $contents): void
    {
        $this->zip->addFromString($localName, $contents);
    }

    /**
     * Closes the zip archive.
     */
    public function close(): bool
    {
        return $this->zip->close();
    }

    /**
     * Zips a directory and saves it to the specified destination.
     *
     * @param string $source The source directory to zip.
     * @param string $destination The destination path for the zip file.
     * @return bool Returns true on success, false on failure.
     */
    public function zip(string $source, string $destination): bool
    {
        if (!$this->create($destination)) return false;

        $this->addDirectory($source);
        $this->close();
        return true;
    }

    /**
     * Extracts a zip file to the specified destination.
     *
     * @param string $source The path to the zip file.
     * @param string $destination The destination directory where files will be extracted.
     * @return bool Returns true on success, false on failure.
     */
    public function extract(string $source, string $destination): bool
    {
        if (!$this->open($source)) return false;

        $this->zip->extractTo($destination);
        $this->close();
        return true;
    }
}