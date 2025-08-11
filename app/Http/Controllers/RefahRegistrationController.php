<?php

namespace App\Http\Controllers;

use App\Models\RefahUser;
use App\Models\RefahCart;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RefahRegistrationController extends Controller
{
    public function showForm(): View
    {
        $refahCarts = RefahCart::where('is_active', true)->get();
        
        return view('refah.registration', compact('refahCarts'));
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
            'phone' => 'nullable|string|max:20',
            'job' => 'required|string|max:255',
            'income' => 'required|numeric|min:0',
            'refah_cart_id' => 'required|exists:refah_cart,id',
            'how_to_receive' => 'required|in:in_person,mail_to_address',
            'payment_method' => 'required|in:cash,card,online,installment',
        ]);

        RefahUser::create($validated);

        return redirect()->route('refah.registration')
            ->with('success', 'ثبت‌نام شما با موفقیت انجام شد.');
    }
}
