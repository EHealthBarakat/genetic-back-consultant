<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appointment\AppointmentStoreRequest;
use App\Http\Requests\Appointment\AppointmentUpdateRequest;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
/**
 * @group Appointment
 */
class AppointmentController extends Controller
{


    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $perPage = request()->input('perPage', 12); // Default to 10 items per page if not specified
        $page = request()->input('page', 1); // Default to the first page if not specified
        $patients = Appointment::query()->paginate($perPage, ['*'], 'page', $page);
        return api_response(true, $patients, Response::HTTP_OK);
    }


    /**
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $formFields = [
            [
                'label' => 'روز مراجعه',
                'type' => 'date',
                'name' => 'date',
                'required' => true,
            ],
            [
                'label' => 'ساعت مراجعه',
                'type' => 'time',
                'name' => 'time',
                'required' => true,
            ],
        ];
        return api_response(true, $formFields, Response::HTTP_OK, '');
    }


    /**
     * @param AppointmentStoreRequest $request
     * @return JsonResponse
     */
    public function store(AppointmentStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $appointment = Appointment::query()->create([
                'patient_id' => $request->patient_id,
                'creator_id' => Auth::id(),
                'Referred_at' => $request->Referred_at

            ]);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return api_response(false, null, Response::HTTP_INTERNAL_SERVER_ERROR, 'خطایی رخ داده است!');
        }
        return
            api_response(true, $appointment, Response::HTTP_CREATED, 'اطلاعات بیمار ثیت شد!');
    }


    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        //
    }


    /**
     * @param AppointmentUpdateRequest $request
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function update(AppointmentUpdateRequest $request, Appointment $appointment): JsonResponse
    {
        DB::beginTransaction();
        try {
            $appointment->update([
                'patient_id' => $request->patient_id,
                'creator_id' => Auth::id(),
                'Referred_at' => $request->Referred_at

            ]);


            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return api_response(false, null, Response::HTTP_INTERNAL_SERVER_ERROR, 'خطایی رخ داده است!');
        }
        return
            api_response(true, $appointment, Response::HTTP_OK, 'اطلاعات ویرایش شد!');
    }


    /**
     * @param Appointment $appointment
     * @return JsonResponse
     */
    public function destroy(Appointment $appointment): JsonResponse
    {
        DB::beginTransaction();
        try {

            $appointment->delete();

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return api_response(false, null, Response::HTTP_INTERNAL_SERVER_ERROR, 'خطایی رخ داده است!');
        }
        return api_response(true, null, Response::HTTP_OK, 'حذف شد!');
    }
}
