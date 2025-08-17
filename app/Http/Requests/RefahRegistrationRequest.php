<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefahRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cell_phone' => 'required|string|max:20|unique:refah_users,cell_phone',
            'national_code' => 'required|string|max:20|unique:refah_users,national_code',
            'birth_date' => 'required|date',
            'birth_date_persian' => 'nullable|string|max:10',
            'gender' => 'required|in:male,female',
            'number_of_family_members' => 'required|integer|min:1',
            'country_id' => 'required|exists:countries,id',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'job' => 'required|string|max:255',
            'income' => 'required|numeric|min:0',
            'refah_organization_id' => 'required|exists:refah_organizations,id',
            'refah_cart_id' => 'required|exists:refah_cart,id',
            'how_to_receive' => 'required|in:in_person,mail_to_address',
            'payment_method' => 'required|in:cash,card,online,installment',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Unique validation messages
            'cell_phone.unique' => 'این شماره موبایل قبلاً ثبت شده است.',
            'national_code.unique' => 'این کد ملی قبلاً ثبت شده است.',

            // Required field messages
            'first_name.required' => 'نام الزامی است.',
            'last_name.required' => 'نام خانوادگی الزامی است.',
            'cell_phone.required' => 'شماره موبایل الزامی است.',
            'national_code.required' => 'کد ملی الزامی است.',
            'birth_date.required' => 'تاریخ تولد الزامی است.',
            'gender.required' => 'جنسیت الزامی است.',
            'number_of_family_members.required' => 'تعداد اعضای خانواده الزامی است.',
            'country_id.required' => 'انتخاب کشور الزامی است.',
            'province_id.required' => 'انتخاب استان الزامی است.',
            'city_id.required' => 'انتخاب شهر الزامی است.',
            'postal_code.required' => 'کد پستی الزامی است.',
            'address.required' => 'آدرس الزامی است.',
            'job.required' => 'شغل الزامی است.',
            'income.required' => 'درآمد الزامی است.',
            'refah_organization_id.required' => 'انتخاب سازمان الزامی است.',
            'refah_cart_id.required' => 'انتخاب بسته رفاهی الزامی است.',
            'how_to_receive.required' => 'نحوه دریافت الزامی است.',
            'payment_method.required' => 'روش پرداخت الزامی است.',

            // String validation messages
            'first_name.string' => 'نام باید متن باشد.',
            'last_name.string' => 'نام خانوادگی باید متن باشد.',
            'cell_phone.string' => 'شماره موبایل باید متن باشد.',
            'national_code.string' => 'کد ملی باید متن باشد.',
            'postal_code.string' => 'کد پستی باید متن باشد.',
            'address.string' => 'آدرس باید متن باشد.',
            'phone.string' => 'تلفن ثابت باید متن باشد.',
            'job.string' => 'شغل باید متن باشد.',

            // Max length validation messages
            'first_name.max' => 'نام نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'last_name.max' => 'نام خانوادگی نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'cell_phone.max' => 'شماره موبایل نمی‌تواند بیشتر از 20 کاراکتر باشد.',
            'national_code.max' => 'کد ملی نمی‌تواند بیشتر از 20 کاراکتر باشد.',
            'postal_code.max' => 'کد پستی نمی‌تواند بیشتر از 20 کاراکتر باشد.',
            'address.max' => 'آدرس نمی‌تواند بیشتر از 500 کاراکتر باشد.',
            'phone.max' => 'تلفن ثابت نمی‌تواند بیشتر از 20 کاراکتر باشد.',
            'job.max' => 'شغل نمی‌تواند بیشتر از 255 کاراکتر باشد.',

            // Other validation messages
            'birth_date.date' => 'فرمت تاریخ تولد معتبر نیست.',
            'gender.in' => 'جنسیت انتخاب شده معتبر نیست.',
            'number_of_family_members.integer' => 'تعداد اعضای خانواده باید عدد باشد.',
            'number_of_family_members.min' => 'تعداد اعضای خانواده باید حداقل 1 باشد.',
            'income.numeric' => 'درآمد باید عدد باشد.',
            'income.min' => 'درآمد نمی‌تواند منفی باشد.',
            'country_id.exists' => 'کشور انتخاب شده معتبر نیست.',
            'province_id.exists' => 'استان انتخاب شده معتبر نیست.',
            'city_id.exists' => 'شهر انتخاب شده معتبر نیست.',
            'refah_organization_id.exists' => 'سازمان انتخاب شده معتبر نیست.',
            'refah_cart_id.exists' => 'بسته رفاهی انتخاب شده معتبر نیست.',
            'how_to_receive.in' => 'نحوه دریافت انتخاب شده معتبر نیست.',
            'payment_method.in' => 'روش پرداخت انتخاب شده معتبر نیست.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'cell_phone' => 'شماره موبایل',
            'national_code' => 'کد ملی',
            'birth_date' => 'تاریخ تولد',
            'gender' => 'جنسیت',
            'number_of_family_members' => 'تعداد اعضای خانواده',
            'country_id' => 'کشور',
            'province_id' => 'استان',
            'city_id' => 'شهر',
            'postal_code' => 'کد پستی',
            'address' => 'آدرس',
            'phone' => 'تلفن ثابت',
            'job' => 'شغل',
            'income' => 'درآمد',
            'refah_organization_id' => 'سازمان',
            'refah_cart_id' => 'بسته رفاهی',
            'how_to_receive' => 'نحوه دریافت',
            'payment_method' => 'روش پرداخت',
        ];
    }
}
