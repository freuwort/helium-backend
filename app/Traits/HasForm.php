<?php

namespace App\Traits;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

trait HasForm
{
    protected $multipleSubmissions = false;

    public function submissions()
    {
        return $this->morphMany(FormSubmission::class, 'model');
    }

    public function form(): Form
    {
        return $this->belongsTo(Form::class);
    }



    public function getSubmissionAttribute()
    {
        return $this->submissions()->first()->data;
    }



    private function validateFormSubmission($formId, $data)
    {
        $form = Form::find($formId);

        if (!$form) return false;

        $validator = Validator::make($data, $form->validation_rules);

        if ($validator->fails()) return false;
        
        return $validator->validated();
    }



    public function submitForm($data, $formId = null)
    {
        $formId = $formId ?? $this->form->id;

        if (!$data) return;

        $validatedData = $this->validateFormSubmission($formId, $data);

        if (!$validatedData) return;
        
        if ($this->multipleSubmissions)
        {
            $this->submissions()->create([
                'form_id' => $formId,
                'data' => $validatedData,
            ]);
        }
        else
        {
            $this->submissions()->updateOrCreate([
                'form_id' => $formId,
            ], [
                'data' => $validatedData,
            ]);
        }
    }
}