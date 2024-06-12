<?php

namespace App\Http\Resources\Form;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'model_id' => $this->model_id,
            'model_type' => $this->model_type,
            'data' => $this->data,
            'files' => $this->files,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
