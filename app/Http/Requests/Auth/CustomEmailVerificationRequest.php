<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class CustomEmailVerificationRequest extends EmailVerificationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = User::find($this->route('id'));

        if (!$user) return false;

        $this->merge(['user' => $user ]);
        $this->setUserResolver(function () use ($user) { return $user; });

        return parent::authorize();
    }
}
