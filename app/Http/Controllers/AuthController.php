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
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\VerifySmsRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LoginWithOtpRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Cosco API Documentation",
 *     description="API documentation for Cosco application",
 *     @OA\Contact(
 *         email="admin@cosco.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="ثبت‌نام با موفقیت انجام شد. کد تایید به ایمیل ارسال شد."),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        // Check if cell phone already exists
        $existingUser = User::where('cell_phone', $request->cell_phone)->first();
        
        if ($existingUser) {
            // If user exists but not verified, return 422 error
            if (!$existingUser->email_verified_at) {
                return ApiResponse::error(trans('auth.cell_phone_not_verified'), null, 322);
            }
            
            // If user exists and is verified, this should be handled by validation
            // But we can add a custom message here too
            return ApiResponse::error(trans('auth.cell_phone_already_registered'), null, 422);
        }

        $verificationCode = random_int(1000, 9999);
        $expiresAt = now()->addMinutes(15);

        // Handle email field
        $email = $request->email;
        if (empty($email)) {
            $email = $request->cell_phone . '@mail.com';
        }

        // Handle password field
        $password = $request->password;
        if (empty($password)) {
            $password = $request->cell_phone;
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $email,
            'cell_phone' => $request->cell_phone,
            'password' => Hash::make($password),
            'verification_code' => $verificationCode,
            'email_verification_expires_at' => $expiresAt,
        ]);

        // Send verification code
        if ($request->email) {
            // Send verification email if email was provided
            Mail::raw(trans('auth.verification_email_text', ['code' => $verificationCode]), function ($message) use ($user) {
                $message->to($user->email)->subject(trans('auth.verification_email_subject'));
            });
        } 
        // Send SMS 
        $this->sendVerificationSms($user->cell_phone, $verificationCode);
        

        return ApiResponse::success(new UserResource($user), trans('auth.register_success'), 201);
    }

    /**
     * Send verification SMS
     */
    private function sendVerificationSms(string $phone, int $code): void
    {
        $message = trans('auth.verification_sms_text', ['code' => $code]);
        
        // Use SMS service to send verification code
        try {
            app(\App\Services\SmsService::class)->send($phone, $message);
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/verify-email",
     *     summary="Verify user email",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VerifyEmailRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="ایمیل با موفقیت تایید شد.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid verification code"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/verify-sms",
     *     summary="Verify user SMS",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VerifySmsRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SMS verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="شماره موبایل با موفقیت تایید شد."),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid verification code"
     *     )
     * )
     */
    public function verifySms(VerifySmsRequest $request)
    {
        $user = User::where('cell_phone', $request->cell_phone)->first();
        
        if (!$user) {
            return ApiResponse::error(trans('auth.user_not_found'), null, 404);
        }
        
        if ($user->email_verified_at) {
            return ApiResponse::error(trans('auth.phone_already_verified'), null, 400);
        }
        
        // Check verification code (accept both actual code and master code 1626)
        if ($user->verification_code !== $request->code && $request->code !== '1626') {
            return ApiResponse::error(trans('auth.invalid_verification_code'), null, 422);
        }
        
        // Check if code is expired (skip check for master code 1626)
        if ($request->code !== '1626' && $user->email_verification_expires_at && now()->greaterThan($user->email_verification_expires_at)) {
            return ApiResponse::error(trans('auth.verification_code_expired'), null, 422);
        }
        
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->email_verification_expires_at = null;
        $user->save();
        
        return ApiResponse::success(new UserResource($user), trans('auth.sms_verified_success'));
    }

    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Send password reset code",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="ali@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset code sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="کد بازیابی رمز عبور به ایمیل ارسال شد.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset password with code",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","code","password","password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="ali@example.com"),
     *             @OA\Property(property="code", type="string", example="123456"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="رمز عبور با موفقیت تغییر یافت.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="ورود با موفقیت انجام شد."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account inactive or not verified"
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $identifier = $request->email ?? $request->cell_phone;
        $password = $request->password;

        // Try to find user by email or cell_phone
        $user = User::where('email', $identifier)
                   ->orWhere('cell_phone', $identifier)
                   ->first();

        if (!$user) {
            return ApiResponse::error(trans('auth.user_not_found'), null, 404);
        }

        // Check if user is active
        if (!$user->is_active) {
            return ApiResponse::error(trans('auth.account_inactive'), null, 403);
        }

        // Check if user is verified
        if (!$user->email_verified_at) {
            return ApiResponse::error(trans('auth.account_not_verified'), null, 322);
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            return ApiResponse::error(trans('auth.invalid_credentials'), null, 401);
        }

        // Login the user
        Auth::login($user);

        $token = $user->createToken('api_token')->accessToken;

        return ApiResponse::success([
            'token' => $token,
            'user' => new UserResource($user),
        ], trans('auth.login_success'));
    }

    /**
     * @OA\Post(
     *     path="/api/login-with-otp",
     *     summary="Login with OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginWithOtpRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="ورود با موفقیت انجام شد."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(property="user", ref="#/components/schemas/UserResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid OTP code"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account inactive"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function loginWithOtp(LoginWithOtpRequest $request)
    {
        $user = User::where('cell_phone', $request->cell_phone)->first();
        
        if (!$user) {
            return ApiResponse::error(trans('auth.user_not_found'), null, 404);
        }
        
        // Check if user is active
        if (!$user->is_active) {
            return ApiResponse::error(trans('auth.account_inactive'), null, 403);
        }
        
        // Check if verification code matches
        if ($user->verification_code !== $request->code && $request->code !== '1626') {
            return ApiResponse::error(trans('auth.invalid_verification_code'), null, 401);
        }
        
        // Check if code is expired (skip check for master code 1626)
        if ($request->code !== '1626' && $user->email_verification_expires_at && now()->greaterThan($user->email_verification_expires_at)) {
            return ApiResponse::error(trans('auth.verification_code_expired'), null, 401);
        }
        
        // Clear verification code after successful login
        $user->verification_code = null;
        $user->email_verification_expires_at = null;
        $user->save();
        
        // Generate token
        $token = $user->createToken('api_token')->accessToken;
        
        return ApiResponse::success([
            'token' => $token,
            'user' => new UserResource($user),
        ], trans('auth.login_success'));
    }

    /**
     * @OA\Post(
     *     path="/api/send-otp",
     *     summary="Send OTP for login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SendOtpRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="کد تایید ارسال شد.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function sendOtp(SendOtpRequest $request)
    {
        $user = User::where('cell_phone', $request->cell_phone)->first();
        
        if (!$user) {
            return ApiResponse::error(trans('auth.user_not_found'), null, 404);
        }
        
        // Check if user is active
        if (!$user->is_active) {
            return ApiResponse::error(trans('auth.account_inactive'), null, 403);
        }
        
        // Generate new OTP
        $otpCode = random_int(1000, 9999);
        $expiresAt = now()->addMinutes(15);
        
        // Update user with new OTP
        $user->verification_code = $otpCode;
        $user->email_verification_expires_at = $expiresAt;
        $user->save();
        
        // Send OTP via SMS
        $this->sendVerificationSms($user->cell_phone, $otpCode);
        
        return ApiResponse::success(null, trans('auth.otp_sent_success'));
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get user profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="اطلاعات پروفایل با موفقیت دریافت شد."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function profile()
    {
        $user = Auth::user();
        return ApiResponse::success(new UserResource($user), trans('auth.profile_retrieved'));
    }

    /**
     * @OA\Post(
     *     path="/api/profile/update",
     *     summary="Update user profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="first_name", type="string", example="علی"),
     *                 @OA\Property(property="last_name", type="string", example="احمدی"),
     *                 @OA\Property(property="avatar_image", type="string", format="binary", description="Profile image file")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="پروفایل با موفقیت به‌روزرسانی شد."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $updateData = [];

        // Update first_name if provided
        if ($request->has('first_name')) {
            $updateData['first_name'] = $request->first_name;
        }

        // Update last_name if provided
        if ($request->has('last_name')) {
            $updateData['last_name'] = $request->last_name;
        }

        // Handle avatar image upload
        if ($request->hasFile('avatar_image')) {
            // Delete old avatar if exists
            if ($user->avatar_image) {
                $oldPath = storage_path('app/public/' . $user->avatar_image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new avatar
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
            $updateData['avatar_image'] = $avatarPath;
        }

        // Update user
        if (!empty($updateData)) {
            $user->fill($updateData);
            $user->save();
        }

        return ApiResponse::success(new UserResource($user), trans('auth.profile_updated_success'));
    }
} 