<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="StoreAddressRequest",
 *   type="object",
 *   required={"country_id","province_id","city_id","postal_code","plaque","address","phone"},
 *   @OA\Property(property="country_id", type="integer", example=1),
 *   @OA\Property(property="province_id", type="integer", example=1),
 *   @OA\Property(property="city_id", type="integer", example=1),
 *   @OA\Property(property="postal_code", type="string", example="1234567890"),
 *   @OA\Property(property="plaque", type="string", example="12"),
 *   @OA\Property(property="address", type="string", example="خیابان انقلاب، پلاک 12"),
 *   @OA\Property(property="phone", type="string", example="02112345678"),
 *   @OA\Property(property="is_default", type="boolean", example=true),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 * )
 */
class StoreAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'country_id' => 'required|integer|exists:countries,id',
            'province_id' => 'required|integer|exists:provinces,id',
            'city_id' => 'required|integer|exists:cities,id',
            'postal_code' => 'required|string',
            'plaque' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $cityId = $this->input('city_id');
            $provinceId = $this->input('province_id');
            $countryId = $this->input('country_id');
            if ($cityId && $provinceId) {
                $city = \App\Models\City::find($cityId);
                if (!$city || $city->province_id != $provinceId) {
                    $validator->errors()->add('city_id', trans('validation.city_not_in_province'));
                }
            }
            if ($provinceId && $countryId) {
                $province = \App\Models\Province::find($provinceId);
                if (!$province || $province->country_id != $countryId) {
                    $validator->errors()->add('province_id', trans('validation.province_not_in_country'));
                }
            }
        });
    }
}
