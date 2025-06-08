<?php

namespace App\Http\Controllers\Backup;

use App\Classes\Permissions\Permissions;
use App\Http\Controllers\Controller;
use App\Jobs\Backup\RestoreBackup;
use App\Jobs\Backup\StoreBackup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:'.Permissions::SYSTEM_ADMIN);
    }

    /**
     * Handle the incoming request to initiate a backup.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        StoreBackup::dispatch($request->user());
        return response('Ok', 202);
    }



    /**
     * Handle the incoming request to restore a backup.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request)
    {
        $request->validate([ 'file' => 'required|file|mimes:zip' ]);

        File::makeDirectory(config('backup.restore_path'), 0755, true, true);
        $request->file('file')->move(config('backup.restore_path'), 'backup.zip');

        RestoreBackup::dispatch(config('backup.restore_path').'/backup.zip');
        return response('Ok', 202);
    }



    /**
     * Handle the incoming request to list all backup files.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $backups = File::files(config('backup.store_path'));

        $backupFiles = array_filter($backups, function ($file) {
            return $file->getExtension() === 'zip';
        });

        // Map
        $backupNames = array_map(function ($file) {
            return [
                'id' => $file->getFilename(),
                'name' => $file->getFilename(),
                'created_at' => Carbon::createFromTimestamp($file->getCTime()),
                'updated_at' => Carbon::createFromTimestamp($file->getMTime()),
                'size' => $file->getSize(),
            ];
        }, $backupFiles);

        // Sort by created_at timestamp in descending order
        usort($backupNames, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return response()->json(['data' => $backupNames]);
    }



    /**
     * Handle the incoming request to download a backup file.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $path = config('backup.store_path').'/'.$request->backup;
        $filename = basename($path);

        if (!File::exists($path)) {
            return response()->json(['error' => 'Backup file not found'], 404);
        }

        return response()->download($path, $filename);
    }



    /**
     * Handle the incoming request to delete a backup file.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $path = config('backup.store_path').'/'.$request->backup;

        if (!File::exists($path)) {
            return response()->json(['error' => 'Backup file not found'], 404);
        }

        File::delete($path);
        return response('Ok', 200);
    }
}