<?php


use App\Models\Role;
use App\Models\User;
use App\Models\UserVerify;
use Carbon\Carbon;


uses(Tests\TestCase::class);




//login tests

it('testSuccessfulLoginByEmail', function () {
    $roles = Role::factory()->count(2)->create();
    $user = User::factory()
        ->hasAttached($roles)
        ->create();
    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->roles);
    $loginData = ['user_name' => $user->email, 'password' => 'password', 'role' => $roles[0]->name];
    $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonStructure([
            "success",
            "message",
            "data" => [
                "user" => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'mobile',
                    'gender_enum',
                    'birthday',
                    'is_active',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                "token",
            ]


        ]);

});
it('testSuccessfulLoginByMobile', function () {
    $roles = Role::factory()->count(2)->create();
    $user = User::factory()
        ->hasAttached($roles)
        ->create();

    $loginData = ['user_name' => $user->mobile, 'password' => 'password', 'role' => $roles[0]->name];
    $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonStructure([
            "success",
            "message",
            "data" => [
                "user" => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'mobile',
                    'gender_enum',
                    'birthday',
                    'is_active',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                "token",
            ]


        ]);

});
it('testRequiredUserNameInLogin', function () {


    $this->json('POST', 'api/auth/login', ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([
            "errors" => [
                "user_name" => ["The user name field is required."],
                "password" => ["The password field is required."],
                "role" => ["The role field is required."],
            ],
        ]);

});
it('testIncorrectlyPasswordInLogin', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $loginData = ['user_name' => $user->email, 'password' => 'test', 'role' => $role->name];
    $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([
            "success" => false,
            "errors" => "username or password is invalid",
            "data" => Null,
        ]);

});
it('testIncorrectlyUserNameInLogin', function () {
    $role = Role::factory()->create();
    $loginData = ['user_name' => 'a.b@gmail.com', 'password' => 'test', 'role' => $role->name];
    $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([
            "success" => false,
            "errors" => "username or password is invalid",
            "data" => Null,
            "message" => Null,
        ]);

});

//sendOtp tests

it('testRequiredUserNameInSendOtp', function () {

    $this->json('POST', 'api/auth/send-otp', ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([

            "errors" => [
                "user_name" => ["The user name field is required."],
            ],

        ]);

});
it('testSuccessfulSendOtp', function () {

    $Data = ['user_name' => '09198754968'];
    $this->json('POST', 'api/auth/send-otp', $Data, ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonStructure([
            "success",
            "message",
            "data"
        ]);

});

//forgot password

it('testSuccessfulForgotPassword', function () {
    $role = Role::factory()->create();
    $user = User::factory()
        ->hasAttached($role)
        ->create();
    $userVerify = UserVerify::factory(['user_name'=>$user->mobile])->create();

    $Data = ['user_name' => $userVerify->user_name, 'otp_code' => $userVerify->otp_code, 'role' => $role->name];
    $this->json('POST', 'api/auth/forgot-password', $Data, ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJsonStructure([
            "success",
            "message",
            "data" => [
                "user" => [
                    'id',
                    'email',
                    'created_at',
                    'updated_at',
                    "roles"
                ],
                "token",
            ]


        ]);

});
it('testRequiredForgotPassword', function () {

    $this->json('POST', 'api/auth/forgot-password', ['Accept' => 'application/json'])
        ->assertStatus(422)
        ->assertJson([

            "errors" => [
                "user_name" => ["The user name field is required."],
                "otp_code" => ["The otp code field is required."],
                "role" => ["The role field is required."],
            ],

        ]);

});
it('testDoesNotExistUserNameAndOtpCodeForgotPassword', function () {

    $role = Role::factory()->create();
    $Data = ['user_name' => 'a.b@gmail.com', 'otp_code' => '12345', 'role' => $role->name];
    $this->json('POST', 'api/auth/forgot-password', $Data, ['Accept' => 'application/json'])
        ->assertStatus(403)
        ->assertJson([
            "success" => false,
            "errors" => "username or code is invalid",
            "data" => Null,
            "message" => Null
        ]);

});
it('testTimeOutOtpCodeForgotPassword', function () {
    $userVerify = UserVerify::factory()->create(['created_at' => Carbon::now()->subMinutes(5)]);
    $role = Role::factory()->create();
    $Data = ['user_name' => $userVerify->user_name, 'otp_code' => $userVerify->otp_code, 'role' => $role->name];
    $this->json('POST', 'api/auth/forgot-password', $Data, ['Accept' => 'application/json'])
        ->assertStatus(403)
        ->assertJson([
            "success" => false,
            "errors" => "username or code is invalid",
            "data" => Null,
            "message" => Null

        ]);

});
