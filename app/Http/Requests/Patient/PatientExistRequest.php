<?php

namespace App\Http\Requests\Patient;

use App\Rules\NationalCode;
use Illuminate\Foundation\Http\FormRequest;


class PatientExistRequest extends FormRequest
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
            'national_code' => [
                'required',
                'string',
                new NationalCode,
            ],
        ];
    }
}
