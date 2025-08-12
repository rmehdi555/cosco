<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use App\Facades\Sms;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send SMS using service injection
     */
    public function sendSms(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $result = $this->smsService->send(
            $request->phone,
            $request->message
        );

        return response()->json($result);
    }

    /**
     * Send SMS using facade
     */
    public function sendSmsViaFacade(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $result = Sms::send(
            $request->phone,
            $request->message
        );

        return response()->json($result);
    }

    /**
     * Send bulk SMS
     */
    public function sendBulkSms(Request $request): JsonResponse
    {
        $request->validate([
            'phones' => 'required|array',
            'phones.*' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $result = Sms::sendBulk(
            $request->phones,
            $request->message
        );

        return response()->json($result);
    }

    /**
     * Get SMS service stats
     */
    public function getStats(): JsonResponse
    {
        $stats = Sms::getStats();
        return response()->json($stats);
    }
}
