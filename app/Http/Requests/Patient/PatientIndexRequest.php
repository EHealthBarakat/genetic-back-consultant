<?php

namespace App\Http\Requests\Patient;


use Illuminate\Foundation\Http\FormRequest;


class PatientIndexRequest extends FormRequest
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
            "name" => "nullable|string",
            "city_id" => "nullable|exists:cities,id",
            "national_code" => [
                'nullable',
                'string'
            ],
            'email'=> ["nullable","string"],
            'mobile'=>'nullable|string',
        ];
    }

    public function queryParameters()
    {
        return [
            'name', 'city_id', 'national_code','mobile','email'
        ];
    }
}
