<?php

namespace App\Http\Resources\AccountingContact;

use App\Http\Resources\Address\BasicAddressResource;
use App\Http\Resources\Role\BasicRoleResource;
use App\Http\Resources\User\BasicUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountingContactResource extends JsonResource
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
            
            'type' => $this->type,
            'name' => $this->name,
            'vat_id' => $this->vat_id,
            'tax_id' => $this->tax_id,
            'customer_id' => $this->customer_id,
            'supplier_id' => $this->supplier_id,
            'employee_id' => $this->employee_id,
            'contact_person' => $this->contact_person,
            'version' => $this->version,

            'sync' => BasicUserResource::make($this->sync),
            'owner' => BasicUserResource::make($this->owner),
            
            'main_address' => BasicAddressResource::make($this->main_address),
            'billing_address' => BasicAddressResource::make($this->billing_address),
            'shipping_address' => BasicAddressResource::make($this->shipping_address),
        ];
    }
}
