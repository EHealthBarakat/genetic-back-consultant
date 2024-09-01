<?php

namespace App\Http\Requests\Patient;


use App\Enums\DegreeEnum;
use App\Enums\MaritalEnum;
use App\Enums\UserGenderEnum;
use App\Rules\NationalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PatientStoreRequest extends FormRequest
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
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],

            'mobile'=>["required",
                "string","regex:/^[0-9]{11}+$/",
                 Rule::unique('users', 'mobile')->whereNull('deleted_at')
            ],
            'gender_enum'=>['required', new Enum(UserGenderEnum::class)],
            'birthday'=>'required|date|date_format:Y-m-d',
            'father_name'        => 'required',
            'marital_enum'       => ['required', new Enum(MaritalEnum::class)],
            'degree_enum'        => ['required', new Enum(DegreeEnum::class)],
            'city_id' => 'required|exists:cities,id',
            'address'=>'required|string',
            'national_code' => [
                'required',
                'string',
                new NationalCode,
                Rule::unique('patients', 'national_code')->whereNull('deleted_at'),
                Rule::unique('patients', 'national_code')->where(function ($query) {
                    return $query->where('spouse_national_code', request('spouse_national_code'))->whereNull('deleted_at');
                }),
            ],
            'spouse_national_code' => [
                'nullable',
                'string',
                new NationalCode,
                Rule::unique('patients', 'spouse_national_code')->whereNull('deleted_at'),
                Rule::unique('patients', 'spouse_national_code')->where(function ($query) {
                    return $query->where('national_code', request('national_code'))->whereNull('deleted_at');
                }),
            ],
        ];
    }
}
