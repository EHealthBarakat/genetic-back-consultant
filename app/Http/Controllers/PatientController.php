<?php

namespace App\Http\Controllers;

use App\Http\Requests\Patient\PatientExistRequest;
use App\Http\Requests\Patient\PatientIndexRequest;
use App\Http\Requests\Patient\PatientStoreRequest;
use App\Http\Requests\Patient\PatientUpdateRequest;
use App\Models\City;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * @group Patient
 */
class PatientController extends Controller
{


    /**
     * @param PatientExistRequest $request
     * @return JsonResponse
     */
    public function exist_patient(PatientExistRequest $request): JsonResponse
    {
        $patient=Patient::query()->where('national_code',$request->national_code)->first();
        if ($patient) {

            return api_response(true, ['exist' => true], Response::HTTP_OK);

        }
        return api_response(true, ['exist' => false], Response::HTTP_OK);
    }


    /**
     * @param PatientIndexRequest $request
     * @return JsonResponse
     */
    public function index(PatientIndexRequest $request): JsonResponse
    {
        $perPage = request()->input('perPage', 12); // Default to 12 items per page if not specified
        $page = request()->input('page', 1); // Default to the first page if not specified
        $query = Patient::query()->with(['user', 'city']);

        if ($request->name) {
            $query->whereHas('user', function($query) use ($request) {
                $query->whereRaw(
                    "MATCH(users.first_name) AGAINST(?)",
                    array($request->get('name'))
                )->orWhereRaw(
                    "MATCH(users.last_name) AGAINST(?)",
                    array($request->get('name'))
                );
            });
        }
        if ($request->mobile) {
            $query->whereHas('user', function($query) use ($request) {
                $query->whereRaw(
                    "MATCH(users.mobile) AGAINST(?)",
                    array($request->get('mobile'))
                );
            });
        }
        if ($request->email) {
            $query->whereHas('user', function($query) use ($request) {
                $query->whereRaw(
                    "MATCH(users.email) AGAINST(?)",
                    array($request->get('email'))
                );
            });
        }
        if ($request->has('national_code')) {
            $query->whereRaw("MATCH(national_code) AGAINST(?)", [$request->national_code]);
        }

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }


        $patients = $query->paginate($perPage, ['*'], 'page', $page);

        return api_response(true, $patients, Response::HTTP_OK);
    }


    /**
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $cities = City::query()->orderBy('show_order', 'asc')
            ->orderBy('name','asc')->get();
        $formFields = [
            [
                'label' => 'نام',
                'name' => 'first_name',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'نام را وارد کنید',
            ],
            [
                'label' => 'نام خانوادگی',
                'name' => 'last_name',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'نام خانوادگی را وارد کنید',
            ],
            [
                'label' => 'جنسیت',
                'name' => 'gender_enum',
                'type' => 'radio',
                'required' => true,
                'radios' => [
                    [
                        'label' => 'خانم',
                        'value' => 'خانم',
                        'name' => 'gender_enum',
                        'id' => 'male',
                    ],
                    [
                        'label' => 'آقا',
                        'value' => 'آقا',
                        'name' => 'gender_enum',
                        'id' => 'female',
                    ],
                ],
            ],
            [
                'label' => 'نام پدر',
                'name' => 'father_name',
                'type' => 'text',
                'placeholder' => 'نام پدر را وارد کنید',
                'required' => true,
            ],
            [
                'label' => 'کد ملی',
                'name' => 'national_code',
                'type' => 'text',
                'placeholder' => 'کد ملی را وارد کنید',
                'dir' => 'ltr',
                'required' => true,
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'وضعیت تاهل',
                'name' => 'marital_enum',
                'type' => 'radio',
                'required' => true,
                'radios' => [
                    [
                        'label' => 'مجرد',
                        'value' => 'مجرد',
                        'name' => 'marital_enum',
                        'id' => 'single',
                    ],
                    [
                        'label' => 'متاهل',
                        'value' => 'متاهل',
                        'name' => 'marital_enum',
                        'id' => 'married',
                    ],
                ],
            ],
            [
                'label' => 'کد ملی همسر',
                'name' => 'spouse_national_code',
                'type' => 'text',
                'placeholder' => 'کد ملی همسر را وارد کنید',
                'dir' => 'ltr',
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'تاریخ تولد',
                'name' => 'birthday',
                'type' => 'date',
                'placeholder' => 'تاریخ تولد را وارد کنید',
                'dir' => 'ltr',
                'required' => true,
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'شماره همراه',
                'name' => 'mobile',
                'type' => 'text',
                'placeholder' => 'شماره همراه را وارد کنید',
                'dir' => 'ltr',
                'required' => true,
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'مدرک تحصیلی',
                'name' => 'degree_enum',
                'type' => 'select',
                'defaultValue' => ['value' => 'کارشناسی', 'label' => 'کارشناسی'],
                'required' => true,
                'options' => [
                    ['value' => 'بدون مدرک', 'label' => 'بدون مدرک'],
                    ['value' => 'دیپلم', 'label' => 'دیپلم'],
                    ['value' => 'فوق دیپلم', 'label' => 'فوق دیپلم'],
                    ['value' => 'کارشناسی', 'label' => 'کارشناسی'],
                    ['value' => 'کارشناسی ارشد', 'label' => 'کارشناسی ارشد'],
                    ['value' => 'دکترا', 'label' => 'دکترا'],
                    ['value' => 'غیره', 'label' => 'غیره'],
                ],
            ],
            [
                'label' => 'ایمیل',
                'name' => 'email',
                'type' => 'email',
                'placeholder' => 'ایمیل را وارد کنید',
                'dir' => 'ltr',
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'شهر',
                'name' => 'city_id',
                'type' => 'select',
                'defaultValue' => ['value' => 'مازندران', 'label' => 'مازندران'],
                'required' => true,
                'options' => $cities,
            ],
            [
                'label' => 'آدرس',
                'name' => 'address',
                'type' => 'text',
                'placeholder' => 'آدرس را وارد کنید',
                'required' => true,
            ],
        ];
       return api_response(true, $formFields, Response::HTTP_OK, '');
    }


    /**
     * @param PatientStoreRequest $request
     * @return JsonResponse
     */
    public function store(PatientStoreRequest $request): JsonResponse
    {

        DB::beginTransaction();
        try {
            $user = User::query()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'gender_enum' => $request->gender_enum,
                'birthday' => $request->birthday,
                'password' => '-'
            ]);


            $patient = Patient::query()->create([
                'father_name' => $request->father_name,
                'spouse_national_code' => $request->spouse_national_code,
                'marital_enum' => $request->marital_enum,
                'degree_enum' => $request->degree_enum,
                'user_id' => $user->id,
                'city_id' => $request->city_id,
                'national_code' => $request->national_code,
                'address' => $request->address
            ]);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return api_response(false, null, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        return
            api_response(true, $patient->load(['user', 'city']), Response::HTTP_CREATED, 'اطلاعات بیمار ثبت شد!');
    }


    /**
     * @param Patient $patient
     * @return JsonResponse
     */
    public function show(Patient $patient): JsonResponse
    {
        return api_response(true, $patient->load(['user','city']), Response::HTTP_OK, '');
    }


    public function edit(Patient $patient): JsonResponse
    {
        $cities = City::query()->orderBy('show_order', 'asc')
            ->orderBy('name','asc')->get();
        $formFields = [
            [
                'label' => 'نام',
                'name' => 'first_name',
                'value' => $patient->user->first_name,
                'type' => 'text',
                'required' => true,
                'placeholder' => 'نام را وارد کنید',
            ],
            [
                'label' => 'نام خانوادگی',
                'name' => 'last_name',
                'value' => $patient->user->last_name,
                'type' => 'text',
                'required' => true,
                'placeholder' => 'نام خانوادگی را وارد کنید',
            ],
            [
                'label' => 'جنسیت',
                'name' => 'gender_enum',
                'value' => $patient->user->gender_enum,
                'type' => 'radio',
                'required' => true,
                'radios' => [
                    [
                        'label' => 'خانم',
                        'value' => 'خانم',
                        'name' => 'gender_enum',
                        'id' => 'male',
                    ],
                    [
                        'label' => 'آقا',
                        'value' => 'آقا',
                        'name' => 'gender_enum',
                        'id' => 'female',
                    ],
                ],
            ],
            [
                'label' => 'نام پدر',
                'name' => 'father_name',
                'value' => $patient->father_name,

                'type' => 'text',
                'placeholder' => 'نام پدر را وارد کنید',
                'required' => true,
            ],
            [
                'label' => 'کد ملی',
                'name' => 'national_code',
                'value' => $patient->national_code,
                'type' => 'text',
                'placeholder' => 'کد ملی را وارد کنید',
                'dir' => 'ltr',
                'required' => true,
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'وضعیت تاهل',
                'name' => 'marital_enum',
                'value' => $patient->marital_enum,
                'type' => 'radio',
                'required' => true,
                'radios' => [
                    [
                        'label' => 'مجرد',
                        'value' => 'مجرد',
                        'name' => 'marital_enum',
                        'id' => 'single',
                    ],
                    [
                        'label' => 'متاهل',
                        'value' => 'متاهل',
                        'name' => 'marital_enum',
                        'id' => 'married',
                    ],
                ],
            ],
            [
                'label' => 'کد ملی همسر',
                'name' => 'spouse_national_code',
                'value' => $patient->spouse_national_code,
                'type' => 'text',
                'placeholder' => 'کد ملی همسر را وارد کنید',
                'dir' => 'ltr',
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'تاریخ تولد',
                'name' =>'birthday',
                'value' =>$patient->user->birthday,
                'type' => 'date',
                'placeholder' => 'تاریخ تولد را وارد کنید',
                'dir' => 'ltr',
                'required' => true,
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'شماره همراه',
                'name' => 'mobile',
                'value' => $patient->user->mobile,
                'type' => 'text',
                'placeholder' => 'شماره همراه را وارد کنید',
                'dir' => 'ltr',
                'required' => true,
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'مدرک تحصیلی',
                'name' => 'degree_enum',
                'value' => $patient->degree_enum,
                'type' => 'select',
                'defaultValue' => ['value' => 'کارشناسی', 'label' => 'کارشناسی'],
                'required' => true,
                'options' => [
                    ['value' => 'بدون مدرک', 'label' => 'بدون مدرک'],
                    ['value' => 'دیپلم', 'label' => 'دیپلم'],
                    ['value' => 'فوق دیپلم', 'label' => 'فوق دیپلم'],
                    ['value' => 'کارشناسی', 'label' => 'کارشناسی'],
                    ['value' => 'کارشناسی ارشد', 'label' => 'کارشناسی ارشد'],
                    ['value' => 'دکترا', 'label' => 'دکترا'],
                    ['value' => 'غیره', 'label' => 'غیره'],
                ],
            ],
            [
                'label' => 'ایمیل',
                'name' => 'email',
                'value' => $patient->user->email,
                'type' => 'email',
                'placeholder' => 'ایمیل را وارد کنید',
                'dir' => 'ltr',
                'inputClassName' => 'placeholder-end',
            ],
            [
                'label' => 'شهر',
                'name' => 'city_id',
                'value' => $patient->city_id,
                'type' => 'select',
                'defaultValue' => ['value' => 'مازندران', 'label' => 'مازندران'],
                'required' => true,
                'options' => $cities,
            ],
            [
                'label' => 'آدرس',
                'name' => 'address',
                'value' => $patient->address,
                'type' => 'text',
                'placeholder' => 'آدرس را وارد کنید',
                'required' => true,
            ],
        ];
        return api_response(true, $formFields, Response::HTTP_OK, '');
    }


    /**
     * @param PatientUpdateRequest $request
     * @param Patient $patient
     * @return JsonResponse
     */
    public function update(PatientUpdateRequest $request, Patient $patient): JsonResponse
    {
        DB::beginTransaction();
        try {
            $patient->update([
                'father_name' => $request->father_name,
                'spouse_national_code' => $request->spouse_national_code,
                'marital_enum' => $request->marital_enum,
                'degree_enum' => $request->degree_enum,
                'city_id' => $request->city_id,
                'national_code' => $request->national_code,
                'address' => $request->address

            ]);
            $user = $patient->user();
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'gender_enum' => $request->gender_enum,
                'birthday' => $request->birthday

            ]);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return api_response(false, null, Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
        return
            api_response(true, $patient->load(['user', 'city']), Response::HTTP_OK, 'اطلاعات بیمار ویرایش شد!');
    }


    /**
     * @param Patient $patient
     * @return JsonResponse
     */
    public function destroy(Patient $patient): JsonResponse
    {
        DB::beginTransaction();
        try {
            $patient->user()->delete();
            $patient->delete();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return api_response(false, null, Response::HTTP_INTERNAL_SERVER_ERROR, 'خطایی رخ داده است!');
        }
        return api_response(true, null, Response::HTTP_OK, 'بیمار حذف شد!');
    }


    /**
     * @return JsonResponse
     */
    public function filters(): JsonResponse
    {
        $filters=resolve(PatientIndexRequest::class)->queryParameters();
        return api_response(true, $filters, Response::HTTP_OK, '');
    }
}
