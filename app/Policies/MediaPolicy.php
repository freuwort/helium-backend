<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\Media;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;

class MediaPolicy
{
    public function view(User $user, string $path): Response
    {
        // Check if user can view media
        if (!Media::canUser($user, ['read', 'write', 'admin'], $path)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function discover(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::ADMIN_PERMISSIONS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function upload(User $user, string $path): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can upload to path
        if (!Media::canUser($user, ['write', 'admin'], $path)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function createDirectory(User $user, string $path): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can create directory in path
        if (!Media::canUser($user, ['write', 'admin'], $path)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function rename(User $user, string $path): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can rename media
        if (!Media::canUser($user, ['write', 'admin'], $path)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function share(User $user, string $path): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can share media
        if (!Media::canUser($user, ['admin'], $path)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function move(User $user, string $path, string $destination): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can move media
        if (!Media::canUser($user, ['write', 'admin'], $destination)) return Response::deny('You are missing the required permission.');
        if (!Media::canUser($user, ['admin'], $path)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function moveMany(User $user, array $paths, string $destination): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can move media
        if (!Media::canUser($user, ['write', 'admin'], $destination)) return Response::deny('You are missing the required permission.');

        // Check specific paths
        foreach ($paths as $path)
        {
            if (!Media::canUser($user, ['admin'], $path)) return Response::deny('You are missing the required permission.');
        }

        return Response::allow();
    }



    public function copy(User $user, string $path, string $destination): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can copy media
        if (!Media::canUser($user, ['write', 'admin'], $destination)) return Response::deny('You are missing the required permission.');
        if (!Media::canUser($user, ['read', 'write', 'admin'], $path)) return Response::deny('You are missing the required permission.');
    }



    public function copyMany(User $user, array $paths, string $destination): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');

        // Check if user can copy media
        if (!Media::canUser($user, ['write', 'admin'], $destination)) return Response::deny('You are missing the required permission.');
        
        // Check specific paths
        foreach ($paths as $path)
        {
            if (!Media::canUser($user, ['read', 'write', 'admin'], $path)) return Response::deny('You are missing the required permission.');
        }

        return Response::allow();
    }



    public function delete(User $user, string $path): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');
        
        // Check if user can delete media
        if (!Media::canUser($user, ['admin'], $path)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function deleteMany(User $user, array $paths): Response
    {
        // Permission check
        if (!$user->can(Permissions::SYSTEM_ACCESS_MEDIA)) return Response::deny('You are missing the required permission.');
        
        // Check specific paths
        foreach ($paths as $path)
        {
            // Check if user can delete media
            if (!Media::canUser($user, ['admin'], $path)) return Response::deny('You are missing the required permission.');
        }

        return Response::allow();
    }
}
