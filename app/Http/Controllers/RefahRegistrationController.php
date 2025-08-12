<?php

namespace App\Http\Controllers;

use App\Models\RefahUser;
use App\Models\RefahCart;
use App\Models\RefahOrganization;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RefahRegistrationController extends Controller
{
    public function showForm(): View
    {
        $refahCarts = RefahCart::where('is_active', true)->get();
        $refahOrganizations = RefahOrganization::where('is_active', true)->get();
        
        return view('refah.registration', compact('refahCarts', 'refahOrganizations'));
    }

    public function getProvinces(Request $request)
    {
        $provinces = Province::where('country_id', $request->country_id)->get();
        return response()->json($provinces);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('province_id', $request->province_id)->get();
        return response()->json($cities);
    }

    public function checkMobileAvailability(Request $request)
    {
        $cellPhone = $request->input('cell_phone');
        
        if (empty($cellPhone)) {
            return response()->json(['available' => true]);
        }
        
        $exists = RefahUser::where('cell_phone', $cellPhone)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'این شماره موبایل قبلاً ثبت شده است' : 'شماره موبایل در دسترس است'
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cell_phone' => 'required|string|max:20|unique:refah_users,cell_phone',
            'national_code' => 'required|string|max:20|unique:refah_users,national_code',
            'birth_date' => 'required|date',
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
        ], [
            'cell_phone.unique' => 'این شماره موبایل قبلاً ثبت شده است.',
            'national_code.unique' => 'این کد ملی قبلاً ثبت شده است.',
            'first_name.required' => 'نام الزامی است.',
            'last_name.required' => 'نام خانوادگی الزامی است.',
            'cell_phone.required' => 'شماره موبایل الزامی است.',
            'national_code.required' => 'کد ملی الزامی است.',
            'birth_date.required' => 'تاریخ تولد الزامی است.',
            'gender.required' => 'جنسیت الزامی است.',
            'number_of_family_members.required' => 'تعداد اعضای خانواده الزامی است.',
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
        ]);

        // Generate unique 6-digit code starting with non-zero
        $code = $this->generateUniqueCode();
        $validated['code'] = $code;

        $user = RefahUser::create($validated);

        // Send welcome SMS
        $smsService = app(SmsService::class);
        $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
        
        try {
            $smsService->sabtNamRefahKala(
                $validated['cell_phone'],
                $fullName,
                $code
            );
        } catch (\Exception $e) {
            // Log SMS error but don't fail the registration
            \Log::error('Failed to send Refah Kala registration SMS', [
                'user_id' => $user->id,
                'phone' => $validated['cell_phone'],
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('refah.registration')
            ->with('success', "ثبت‌نام شما با موفقیت انجام شد. کد پیگیری شما: {$code}");
    }

    /**
     * Generate a unique 6-digit code starting with non-zero
     */
    private function generateUniqueCode(): string
    {
        do {
            // Generate 6-digit code starting with 1-9, then 5 random digits
            $code = rand(1, 9) . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (RefahUser::where('code', $code)->exists());

        return $code;
    }
}
