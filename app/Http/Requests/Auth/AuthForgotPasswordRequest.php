<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;


class AuthForgotPasswordRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => 'required|string',
            'otp_code'=>'required|string',
            'role'=>'required|string|exists:roles,name',
        ];
    }

}
