<?php

namespace App\Http\Controllers;

use App\Models\RefahUser;
use App\Models\RefahCart;
use App\Models\RefahOrganization;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Services\SmsService;
use App\Http\Requests\RefahRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Hekmatinasser\Verta\Verta;

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

    public function convertPersianDate(Request $request)
    {
        $persianDate = $request->input('persian_date');
        
        if (empty($persianDate)) {
            return response()->json(['success' => false, 'message' => 'تاریخ وارد نشده است']);
        }
        
        try {
            // Validate Persian date format (YYYY/MM/DD)
            if (!preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $persianDate)) {
                return response()->json(['success' => false, 'message' => 'فرمت تاریخ صحیح نیست']);
            }
            
            $parts = explode('/', $persianDate);
            $year = (int) $parts[0];
            $month = (int) $parts[1];
            $day = (int) $parts[2];
            
            // Validate date ranges
            if ($year < 1300 || $year > 1450) {
                return response()->json(['success' => false, 'message' => 'سال باید بین 1300 تا 1450 باشد']);
            }
            
            if ($month < 1 || $month > 12) {
                return response()->json(['success' => false, 'message' => 'ماه معتبر نیست']);
            }
            
            if ($day < 1 || $day > 31) {
                return response()->json(['success' => false, 'message' => 'روز معتبر نیست']);
            }
            
            // Convert Persian date to Gregorian using Verta
            $gregorianArray = Verta::jalaliToGregorian($year, $month, $day);
            $gregorianDate = sprintf('%04d-%02d-%02d', $gregorianArray[0], $gregorianArray[1], $gregorianArray[2]);
            
            return response()->json([
                'success' => true,
                'gregorian_date' => $gregorianDate,
                'persian_date' => $persianDate
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'تاریخ وارد شده معتبر نیست']);
        }
    }

    public function store(RefahRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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
            Log::error('Failed to send Refah Kala registration SMS', [
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
