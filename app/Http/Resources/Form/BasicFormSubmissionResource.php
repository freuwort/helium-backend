<?php

namespace App\Http\Resources\Form;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasicFormSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'files' => $this->files,
        ];
    }
}
