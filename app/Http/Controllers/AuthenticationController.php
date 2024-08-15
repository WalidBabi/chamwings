<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterationRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\VerificationCodeRequest;
use App\Mail\Verification;
use App\Models\Passenger;
use App\Models\TravelRequirement;
use App\Models\User;
use App\Models\VerifyAccount;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthenticationController extends Controller
{
    //Register Function
    public function register(RegisterationRequest $registerationRequest)
    {
        $year = explode('-', $registerationRequest->date_of_birth);
        if ($registerationRequest->password != $registerationRequest->confirm_password) {
            return error('some thing went wrong', 'error password confirmation', 422);
        }

        $user = VerifyAccount::create([
            'title' => $registerationRequest->title,
            'first_name' => $registerationRequest->first_name,
            'last_name' => $registerationRequest->last_name,
            'email' => $registerationRequest->email,
            'password' => Hash::make($registerationRequest->password),
            'date_of_birth' => $registerationRequest->date_of_birth,
            'country_of_residence' => $registerationRequest->country_of_residence,
            'phone' => $registerationRequest->phone,
            'age' => Carbon::now()->year - $year[0],
            'code' => rand(1000, 9999),
            'created_at' => Carbon::now()->addMinutes(15),
        ]);

        try {
            Mail::to($registerationRequest->email)->send(new Verification($user->code));
        } catch (Exception $e) {
            $user->delete();
            return error('some thing went wrong', 'cannot send verification code, try arain later....', 422);
        }

        return success($user->email, 'we sent verification code to your email');
    }

    //Check Register Verification Function
    public function checkRegisterVerification($email, VerificationCodeRequest $verificationCodeRequest)
    {
        $verify = VerifyAccount::where('email', $email)->latest()->first();
        if ($verify && $verify->created_at > Carbon::now() && $verify->code == $verificationCodeRequest->code) {
            $user = User::create([
                'email' => $verify->email,
                'password' => $verify->password,
                'phone' => $verify->phone,
            ]);
            $travel_requirement = TravelRequirement::create([
                'title' => $verify->title,
                'first_name' => $verify->first_name,
                'last_name' => $verify->last_name,
                'date_of_birth' => $verify->date_of_birth,
                'country_of_residence' => $verify->country_of_residence,
                'age' => $verify->age,
            ]);
            Passenger::create([
                'user_id' => $user->user_id,
                'travel_requirement_id' => $travel_requirement->travel_requirement_id,
            ]);
            $verify->delete();

            $token = $user->createToken('user')->plainTextToken;

            return success($token, 'your account created successfully', 201);
        }

        return error('some thing went wrong', 'incorrect code please try again later', 422);
    }

    //Login Function
    public function login(LoginRequest $loginRequest)
    {
        $user = User::where('email', $loginRequest->email)->first();
        if (!$user || !Hash::check($loginRequest->password, $user->password)) {
            return error('some thing went wrong', 'incorrect email or password', 422);
        }

        $token = $user->createToken('user')->plainTextToken;
        if ($user->passenger) {
            $user->passenger->travelRequirement;
        } else {
            $user->employee->roles;
        }
        $data = [
            'token' => $token,
            'user' => $user
        ];
        return success($data, null);
    }

    //Profile Function
    public function profile()
    {
        $user = Auth::guard('user')->user();
        if ($user->passenger) {
            $user->passenger->travelRequirement->passports;
        } else {
            $user->employee->roles;
        }

        return success($user, null);
    }

    //Update Profile Function
    public function updateProfile(UpdateProfileRequest $updateProfileRequest)
    {
        $user = Auth::guard('user')->user();
        $user->update([
            'phone' => $updateProfileRequest->phone,
        ]);
        if ($updateProfileRequest->file('image')) {
            $path = $updateProfileRequest->file('image')->storePublicly('ProfileImage');
            $user->update([
                'image' => 'storage/' . $path,
            ]);
        }
        $year = explode('-', $updateProfileRequest->date_of_birth);
        $user->passenger->travelRequirement->update([
            'title' => $updateProfileRequest->title,
            'first_name' => $updateProfileRequest->first_name,
            'last_name' => $updateProfileRequest->last_name,
            'date_of_birth' => $updateProfileRequest->date_of_birth,
            'address' => $updateProfileRequest->address,
            'city' => $updateProfileRequest->city,
            'mobile_during_travel' => $updateProfileRequest->mobile_during_travel,
            'age' => Carbon::now()->year - $year[0],
            'gender' => $updateProfileRequest->gender,
            'nationality' => $updateProfileRequest->nationality,
            'country_of_residence' => $updateProfileRequest->country_of_residence,
        ]);

        $user->passenger->travelRequirement->passports;

        return success($user, 'your profile updated successfully');
    }

    //Logout Function
    public function logout()
    {
        $user = Auth::guard('user')->user();

        $user->tokens()->delete();

        return success('logout successfully', null);
    }

    //Forget Password Function
    public function forgetPassword(EmailVerificationRequest $emailVerificationRequest)
    {
        $verify = VerifyAccount::create([
            'email' => $emailVerificationRequest->email,
            'code' => rand(1000, 9999),
            'created_at' => Carbon::now()->addMinutes(15),
        ]);
        try {
            Mail::to($emailVerificationRequest->email)->send(new Verification($verify->code));
        } catch (Exception $e) {
            $verify->delete();
            return error('some thing went wrong', 'cannot send verification code, try arain later....', 422);
        }
        return success(null, 'we sent verification code to your email');
    }

    //Check Verification Code For Foreget Password Function
    public function checkVerification($email, VerificationCodeRequest $verificationCodeRequest)
    {
        $verify = VerifyAccount::where('email', $email)->latest()->first();
        if ($verify && $verify->created_at > Carbon::now() && $verify->code == $verificationCodeRequest->code) {
            $token = $verify->createToken('password')->plainTextToken;
            return success($token, 'verify successfully');
        }
    }

    //Reset Password Function
    public function resetPassword(ResetPasswordRequest $resetPasswordRequest)
    {
        $verify = Auth::guard('password')->user();
        $user = User::where('email', $verify->email)->first();
        if ($resetPasswordRequest->new_password === $resetPasswordRequest->confirm_password) {
            $user->update([
                'password' => Hash::make($resetPasswordRequest->new_password),
            ]);
            $verify->delete();
            return success(null, 'your password reset successfully');
        }
    }
}
