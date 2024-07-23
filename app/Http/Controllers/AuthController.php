<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthForgotPasswordRequest;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthSendOtpRequest;
use App\Models\User;
use App\Models\UserVerify;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


/**
 * @group Auth
 * @unauthenticated
 */
class AuthController extends Controller
{
    /**
     * @param $user_name
     * @return mixed
     */
    public function getUser($user_name): mixed
    {
        $type = $this->getUserNameType($user_name);
        if (!$type) {
            api_response(false, null, Response::HTTP_UNPROCESSABLE_ENTITY, null, 'Please enter the correct username')->throwResponse();

        }
        return User::where($type, $user_name)->first();

    }
    //Get username type

    /**
     * @param $user_name
     * @return false|string
     */
    public function getUserNameType($user_name): bool|string
    {

        if (filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif (preg_match('/^[0-9]{11}+$/', $user_name)) {
            return 'mobile';
        }

        return false;
    }

    /**
     * @param AuthLoginRequest $request
     * @return JsonResponse
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {

        // Check exists:
        $user = $this->getUser($request->user_name);

        if (!$user || !Hash::check($request->password, $user->password))
            return api_response( false, null, Response::HTTP_UNPROCESSABLE_ENTITY, null, 'username or password is invalid');

        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array($request->role, $roles))
            return api_response(false, null,
                Response::HTTP_FORBIDDEN, 'You do not have permission to access this panel!');


        // Revoke all tokens...


        $user->tokens()->where('name', $request->role)->delete();

        $token = $user->createToken($request->role)->plainTextToken;
        $user->save();
        $user['set_password'] = ($user->password == '-') ? false : true;
        return api_response(true, [
            'user' => $user,
            'token' => $token
        ], Response::HTTP_OK);


    }

    /**
     * @param AuthSendOtpRequest $request
     * @return JsonResponse
     */
    public function sendOtp(AuthSendOtpRequest $request, SmsService $sendSms)
    {

        // 1. Get Username type:
        $username = $request->user_name;
        $type = $this->getUserNameType($username);
        $is_already_sent = UserVerify::where("user_name", $username)
            ->where("created_at", ">", Carbon::now()->subMinutes(1))->count();
        if ($is_already_sent) {
            return api_response(false, null, Response::HTTP_FORBIDDEN, null, 'token is already sent');

        }

        // 3. Generate new token
        $user_verify = new UserVerify();
        $user_verify->user_name = $username;
        $user_verify->otp_code = rand(10000, 99999);
        $user_verify->save();
        // 4. Send:

        switch ($type) {
            case "mobile":

                $pattern_code = 'verify';
                $recipient = $username;
                $pattern_values = [$user_verify->otp_code];
                $response_sms = $sendSms->sendWithPattern($pattern_code, $recipient, $pattern_values);
                $success = ($response_sms[0]->return->status==200) ? true : false;
                $status=$response_sms[0]->return->status;
                $message=$response_sms[0]->return->message;
                break;

            case "email":

                Mail::to($username)->send(new \App\Mail\SendEmail(['verification_code' => $user_verify->otp_code], 'Verification Code', 'user_verify'));
                $status=200;
                $message='email sent.';
                $success =true;
                break;
        }



      return  api_response($success, null, $status, $message);
    }

    /**
     * @param AuthForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(AuthForgotPasswordRequest $request): JsonResponse
    {
        // 1. Get Username type:
        $username = $request->user_name;

        // 2. Check code:
        $is_correct = UserVerify::where("user_name", $username)
            ->where("otp_code", $request->otp_code)
            ->where("created_at", ">", Carbon::now()->subMinutes(5))
            ->count();

        if ($is_correct == 0) {

            return api_response(false, null, Response::HTTP_FORBIDDEN, null, 'username or code is invalid');

        }

        $user = $this->getUser($request->user_name);
        if (!$user) {
            return api_response(false, null, Response::HTTP_FORBIDDEN, null, 'username is invalid');
        }


        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array($request->role, $roles)) {


            return api_response(false, null,
                Response::HTTP_FORBIDDEN, 'You do not have permission to access this panel!');


        }


        $user->roles;
        // Revoke all tokens...
        $user->tokens()->where('name', $request->role)->delete();
        $token = $user->createToken($request->role)->plainTextToken;
        return api_response(true, [
            'token' => $token,
            'user' => $user
        ], Response::HTTP_OK, 'Token sent.');


    }
}
