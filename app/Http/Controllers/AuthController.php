<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $verificationCode = random_int(1000, 9999);
        $expiresAt = now()->addMinutes(10);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'cell_phone' => $request->cell_phone,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
            'email_verification_expires_at' => $expiresAt,
        ]);

        // Send verification email
        Mail::raw(trans('auth.verification_email_text', ['code' => $verificationCode]), function ($message) use ($user) {
            $message->to($user->email)->subject(trans('auth.verification_email_subject'));
        });

        return ApiResponse::success(new UserResource($user), trans('auth.register_success'), 201);
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ApiResponse::error(trans('auth.user_not_found'), null, 404);
        }
        if ($user->email_verified_at) {
            return ApiResponse::error(trans('auth.email_already_verified'), null, 400);
        }
        if ($user->verification_code !== $request->code) {
            return ApiResponse::error(trans('auth.invalid_verification_code'), null, 422);
        }
        if (now()->greaterThan($user->email_verification_expires_at)) {
            return ApiResponse::error(trans('auth.verification_code_expired'), null, 422);
        }
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->email_verification_expires_at = null;
        $user->save();
        return ApiResponse::success(null, trans('auth.email_verified_success'));
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ApiResponse::error(trans('auth.user_not_found'), null, 404);
        }
        $resetCode = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        // Store code in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $resetCode, 'created_at' => now()]
        );
        // Send reset code via email
        Mail::raw(trans('auth.reset_password_email_text', ['code' => $resetCode]), function ($message) use ($user) {
            $message->to($user->email)->subject(trans('auth.reset_password_email_subject'));
        });
        return ApiResponse::success(null, trans('auth.password_reset_code_sent'));
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$record) {
            return ApiResponse::error(trans('auth.reset_code_not_found'), null, 404);
        }
        if ($record->token !== $request->code) {
            return ApiResponse::error(trans('auth.invalid_reset_code'), null, 422);
        }
        if (now()->diffInMinutes($record->created_at) > 10) {
            return ApiResponse::error(trans('auth.reset_code_expired'), null, 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ApiResponse::error(trans('auth.user_not_found'), null, 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return ApiResponse::success(null, trans('auth.password_reset_success'));
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return ApiResponse::error(trans('auth.invalid_credentials'), null, 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->accessToken;

        return ApiResponse::success([
            'token' => $token,
            'user' => new UserResource($user),
        ], trans('auth.login_success'));
    }

    public function profile()
    {
        $user = Auth::user();
        return ApiResponse::success(new UserResource($user), trans('auth.profile_retrieved'));
    }
} 