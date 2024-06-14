<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateUserResource extends JsonResource
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

            'has_tfa_enabled' => $this->has_tfa_enabled,
            'has_tfa_totp_method_enabled' => $this->has_tfa_totp_method_enabled,
            'has_tfa_sms_method_enabled' => $this->has_tfa_sms_method_enabled,
            'has_tfa_email_method_enabled' => $this->has_tfa_email_method_enabled,
            'default_tfa_method' => optional($this->default_tfa_method)->type,

            'profile_image' => $this->profile_image,
            'profile_banner' => $this->profile_banner,
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
            
            'settings' => $this->settings,

            'roles' => $this->roles,
            'permissions' => $this->permissions->pluck('name'),
        ];
    }
}
