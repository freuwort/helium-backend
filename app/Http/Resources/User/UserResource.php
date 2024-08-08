<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\BasicRoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'media' => $this->media,

            'avatar' => $this->getProfileMedia('avatar'),
            'banner' => $this->getProfileMedia('banner'),
            'email' => $this->email,
            'username' => $this->username,

            'name' => $this->name,
            'fullname' => $this->user_name->fullname ?? '',
            'prefix' => $this->user_name->prefix ?? '',
            'firstname' => $this->user_name->firstname ?? '',
            'middlename' => $this->user_name->middlename ?? '',
            'lastname' => $this->user_name->lastname ?? '',
            'suffix' => $this->user_name->suffix ?? '',
            'nickname' => $this->user_name->nickname ?? '',
            'legalname' => $this->user_name->legalname ?? '',

            'company' => $this->user_company->company ?? '',
            'department' => $this->user_company->department ?? '',
            'title' => $this->user_company->title ?? '',

            'email_verified_at' => $this->email_verified_at,
            'enabled_at' => $this->enabled_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'has_tfa_enabled' => $this->has_tfa_enabled,
            // 'has_tfa_enabled' => false,

            'roles' => BasicRoleResource::collection($this->roles),
            'permissions' => $this->permissions->pluck('name'),

            'is_admin' => $this->is_admin,
            'has_forbidden_permissions' => $this->has_forbidden_permissions,
            'has_elevated_permissions' => $this->has_elevated_permissions,
        ];
    }
}
