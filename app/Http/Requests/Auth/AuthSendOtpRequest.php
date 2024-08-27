<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;


class AuthSendOtpRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'user_name' => 'required|string',
        ];
    }


}
