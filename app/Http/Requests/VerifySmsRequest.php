<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="VerifySmsRequest",
 *   type="object",
 *   title="Verify SMS Request",
 *   description="Request body for SMS verification",
 *   required={"cell_phone","code"},
 *   @OA\Property(property="cell_phone", type="string", example="09123456789", description="User's cell phone number"),
 *   @OA\Property(property="code", type="string", example="1234", description="4-digit verification code"),
 * )
 */
class VerifySmsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cell_phone' => 'required|string|exists:users,cell_phone',
            'code' => 'required|string|size:4',
        ];
    }

    public function messages()
    {
        return [
            'cell_phone.required' => 'شماره موبایل الزامی است.',
            'cell_phone.string' => 'شماره موبایل باید متن باشد.',
            'cell_phone.exists' => 'کاربری با این شماره موبایل یافت نشد.',
            'code.required' => 'کد تایید الزامی است.',
            'code.string' => 'کد تایید باید متن باشد.',
            'code.size' => 'کد تایید باید 4 رقم باشد.',
        ];
    }
}
