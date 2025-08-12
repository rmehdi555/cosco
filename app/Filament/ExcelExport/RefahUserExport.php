<?php

namespace App\Filament\ExcelExport;

use App\Models\RefahUser;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RefahUserExport extends BaseExport
{
    /**
     * Export all Refah users to CSV format
     */
    public static function exportAll(): StreamedResponse
    {
        $users = RefahUser::with(['province', 'city', 'refahOrganization', 'refahCart'])->get();
        
        $filename = self::getFilename('refah-users');
        
        return self::generateCsvResponse($users, $filename);
    }

    /**
     * Export selected Refah users to CSV format
     */
    public static function exportSelected(Collection $selectedRecords): StreamedResponse
    {
        $users = $selectedRecords->load(['province', 'city', 'refahOrganization', 'refahCart']);
        
        $filename = self::getFilename('refah-users', true);
        
        return self::generateCsvResponse($users, $filename);
    }

    /**
     * Get CSV column headers in Persian
     */
    protected static function getCsvHeaders(): array
    {
        return [
            'شناسه',
            'نام',
            'نام خانوادگی',
            'کد ملی',
            'کد پیگیری',
            'شماره موبایل',
            'تلفن ثابت',
            'تاریخ تولد',
            'جنسیت',
            'تعداد اعضای خانواده',
            'استان',
            'شهر',
            'کد پستی',
            'آدرس',
            'شغل',
            'درآمد',
            'سازمان رفاه',
            'بسته رفاهی',
            'نحوه دریافت',
            'روش پرداخت',
            'تاریخ ثبت‌نام',
        ];
    }

    /**
     * Format user data for CSV export
     */
    protected static function formatRecordData($user): array
    {
        return [
            $user->id,
            $user->first_name,
            $user->last_name,
            $user->national_code,
            $user->code,
            $user->cell_phone,
            $user->phone ?? '-',
            $user->birth_date ? Verta::instance($user->birth_date)->format('Y/n/j') : '-',
            self::formatGender($user->gender),
            $user->number_of_family_members,
            $user->province?->title_fa ?? '-',
            $user->city?->title_fa ?? '-',
            $user->postal_code ?? '-',
            $user->address ?? '-',
            $user->job ?? '-',
            $user->income ? number_format($user->income) . ' ریال' : '-',
            $user->refahOrganization?->title ?? '-',
            $user->refahCart?->title ?? '-',
            self::formatDeliveryMethod($user->how_to_receive),
            self::formatPaymentMethod($user->payment_method),
            $user->created_at ? Verta::instance($user->created_at)->format('Y/n/j H:i') : '-',
        ];
    }

    /**
     * Format gender for display
     */
    private static function formatGender(?string $gender): string
    {
        return match($gender) {
            'male' => 'مرد',
            'female' => 'زن',
            default => $gender ?? '-'
        };
    }

    /**
     * Format delivery method for display
     */
    private static function formatDeliveryMethod(?string $method): string
    {
        return match($method) {
            'in_person' => 'حضوری',
            'mail_to_address' => 'ارسال به آدرس',
            default => $method ?? '-'
        };
    }

    /**
     * Format payment method for display
     */
    private static function formatPaymentMethod(?string $method): string
    {
        return match($method) {
            'cash' => 'نقدی',
            'card' => 'کارت',
            'online' => 'آنلاین',
            'installment' => 'اقساطی',
            default => $method ?? '-'
        };
    }
}
