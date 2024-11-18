<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Address\BasicAddressResource;
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
            
            'avatar' => $this->getProfileMedia('avatar'),
            'banner' => $this->getProfileMedia('banner'),
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,

            'requires_password_change' => $this->requires_password_change,
            'requires_two_factor' => $this->requires_two_factor,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,
            'last_login_at' => $this->last_login_at,
            'enabled_at' => $this->enabled_at,
            'blocked_at' => $this->blocked_at,
            'block_reason' => $this->block_reason,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            'name' => $this->name ?? '',
            'salutation' => $this->salutation,
            'prefix' => $this->prefix ?? '',
            'firstname' => $this->firstname ?? '',
            'middlename' => $this->middlename ?? '',
            'lastname' => $this->lastname ?? '',
            'suffix' => $this->suffix ?? '',
            'nickname' => $this->nickname ?? '',
            'legalname' => $this->legalname ?? '',

            'organisation' => $this->organisation ?? '',
            'department' => $this->department ?? '',
            'job_title' => $this->job_title ?? '',

            'customer_id' => $this->customer_id ?? '',
            'employee_id' => $this->employee_id ?? '',
            'member_id' => $this->member_id ?? '',

            'notes' => $this->notes ?? '',
            
            'main_address' => BasicAddressResource::make($this->main_address),
            'billing_address' => BasicAddressResource::make($this->billing_address),
            'shipping_address' => BasicAddressResource::make($this->shipping_address),

            'roles' => BasicRoleResource::collection($this->roles),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            
            // Extra
            'is_admin' => $this->is_admin,
            'has_forbidden_permissions' => $this->has_forbidden_permissions,
            'has_elevated_permissions' => $this->has_elevated_permissions,
            
            'has_tfa_enabled' => $this->has_tfa_enabled,
        ];
    }
}
