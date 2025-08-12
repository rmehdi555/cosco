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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cell_phone' => 'required|string|max:20',
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
