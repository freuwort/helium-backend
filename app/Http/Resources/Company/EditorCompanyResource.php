<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditorCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'model' => [
                'logo' => $this->getProfileMedia('logo'),
                'banner' => $this->getProfileMedia('banner'),
                'name' => $this->name,
                'legal_form' => $this->legal_form,
                'description' => $this->description,
                'notes' => $this->notes,
                'deleted_at' => $this->deleted_at,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],

            'legal_details' => $this->legal_details,
            'identifiers' => $this->identifiers,
            'addresses' => $this->addresses,
            'phonenumbers' => $this->phonenumbers,
            'emails' => $this->emails,
            'links' => $this->links,
            'bank_connections' => $this->bank_connections,
            'dates' => $this->dates,
        ];
    }
}
