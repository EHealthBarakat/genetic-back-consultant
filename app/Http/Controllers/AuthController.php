<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
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
            return api_response(false, null, Response::HTTP_UNPROCESSABLE_ENTITY, null, 'username or password is invalid');

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
}
