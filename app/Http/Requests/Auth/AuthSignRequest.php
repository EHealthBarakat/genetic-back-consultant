<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;


class AuthSignRequest extends BaseRequest
{
    public function authorize():bool
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
