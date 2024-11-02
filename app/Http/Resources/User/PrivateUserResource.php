<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Address\BasicAddressResource;
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

            'avatar' => $this->getProfileMedia('avatar'),
            'banner' => $this->getProfileMedia('banner'),
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,

            'name' => $this->user_info->name ?? '',
            'fullname' => $this->user_info->fullname ?? '',
            'prefix' => $this->user_info->prefix ?? '',
            'firstname' => $this->user_info->firstname ?? '',
            'middlename' => $this->user_info->middlename ?? '',
            'lastname' => $this->user_info->lastname ?? '',
            'suffix' => $this->user_info->suffix ?? '',
            'nickname' => $this->user_info->nickname ?? '',
            'legalname' => $this->user_info->legalname ?? '',
            'organisation' => $this->user_info->organisation ?? '',
            'department' => $this->user_info->department ?? '',
            'job_title' => $this->user_info->job_title ?? '',
            'main_address' => BasicAddressResource::make($this->user_info->main_address),
            'billing_address' => BasicAddressResource::make($this->user_info->billing_address),
            'shipping_address' => BasicAddressResource::make($this->user_info->shipping_address),
            'customer_id' => $this->user_info->customer_id ?? '',
            'employee_id' => $this->user_info->employee_id ?? '',
            'member_id' => $this->user_info->member_id ?? '',
            'notes' => $this->user_info->notes ?? '',

            'requires_password_change' => $this->requires_password_change,
            'requires_two_factor' => $this->requires_two_factor,

            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,
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
