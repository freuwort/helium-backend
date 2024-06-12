<?php

namespace App\Traits;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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



    private function validateFormSubmissionViaRequest(Request $request, Form $form)
    {
        $request->validate($form->getValidationRules());

        return $request->all();
    }

    private function uploadViaRequest(Request $request)
    {
        $files = [];

        foreach ($request->files as $key => $fileList)
        {
            $files[$key] = [];

            if (!is_array($fileList)) $fileList = [$fileList];

            foreach ($fileList as $file)
            {
                $files[$key][] = Media::upload('forms', $file)->src_path;
            }
        }

        return $files;
    }



    public function submitFormViaRequest(Request $request, $formId = null)
    {
        $form = Form::findOrFail($formId ?? $this->form->id);

        $data = $this->validateFormSubmissionViaRequest($request, $form);

        $files = $this->uploadViaRequest($request);
        
        if (!$this->multipleSubmissions)
        {
            $this->submissions()->get()->each->delete();
        }

        $this->submissions()->create([
            'form_id' => $formId,
            'data' => $data,
            'files' => $files,
        ]);
    }
}