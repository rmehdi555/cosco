<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="SendOtpRequest",
 *   type="object",
 *   title="Send OTP Request",
 *   description="Request body for sending OTP for login",
 *   required={"cell_phone"},
 *   @OA\Property(property="cell_phone", type="string", example="09123456789", description="User's cell phone number"),
 * )
 */
class SendOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cell_phone' => 'required|string|exists:users,cell_phone',
        ];
    }

    public function messages()
    {
        return [
            'cell_phone.required' => 'شماره موبایل الزامی است.',
            'cell_phone.string' => 'شماره موبایل باید متن باشد.',
            'cell_phone.exists' => 'کاربری با این شماره موبایل یافت نشد.',
        ];
    }
}
