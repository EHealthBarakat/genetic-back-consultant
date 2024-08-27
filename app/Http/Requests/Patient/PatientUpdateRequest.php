<?php

namespace App\Http\Requests\Patient;

use App\Enums\DegreeEnum;
use App\Enums\MaritalEnum;
use App\Enums\UserGenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PatientUpdateRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email'=> ["nullable",
                "string","email",
                Rule::unique('users','email')->ignore(auth()->user()->id??null)
            ],
            'mobile'=>'required|string|regex:/^[0-9]{11}+$/',
            'gender_enum'=>['required', new Enum(UserGenderEnum::class)],
            'birthday'=>'required',
            'father_name'        => 'required',
            'marital_enum'       => ['required', new Enum(MaritalEnum::class)],
            'degree_enum'        => ['required', new Enum(DegreeEnum::class)],
            'city_id' => 'required',
            'address'=>'required|string',
            'national_code'=>'required|string',
            'spouse_national_code'=>'nullable|string',

        ];
    }
}
