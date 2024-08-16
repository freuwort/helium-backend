<?php

namespace App\Policies;

use App\Classes\Permissions\Permissions;
use App\Models\ScreenPlaylist;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Collection;

class ScreenPlaylistPolicy
{
    public function basicViewAny(User $user): Response
    {
        return Response::allow();
    }



    public function basicView(User $user, ScreenPlaylist $playlist): Response
    {
        return Response::allow();
    }



    public function viewAny(User $user): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_SCREENPLAYLISTS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    

    public function view(User $user, ScreenPlaylist $playlist): Response
    {
        // Permission check
        if (!$user->can(Permissions::APP_VIEW_SCREENPLAYLISTS)) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function create(User $user): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENPLAYLISTS, Permissions::APP_CREATE_SCREENPLAYLISTS])) return Response::deny('You are missing the required permission.');
        
        return Response::allow();
    }



    public function update(User $user, ScreenPlaylist $playlist): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENPLAYLISTS, Permissions::APP_EDIT_SCREENPLAYLISTS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }



    public function delete(User $user, ScreenPlaylist $playlist): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENPLAYLISTS, Permissions::APP_DELETE_SCREENPLAYLISTS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function deleteMany(User $user, Collection $playlists): Response
    {
        // Permission check
        if (!$user->can([Permissions::APP_VIEW_SCREENPLAYLISTS, Permissions::APP_DELETE_SCREENPLAYLISTS])) return Response::deny('You are missing the required permission.');

        return Response::allow();
    }

    
    
    public function restore(User $user, ScreenPlaylist $playlist): Response
    {
        return Response::deny();
    }

    
    
    public function forceDelete(User $user, ScreenPlaylist $playlist): Response
    {
        return Response::deny();
    }
}
