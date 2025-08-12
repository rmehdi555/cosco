<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(string $to, string $message, string $from = null)
 * @method static array sendBulk(array $recipients, string $message, string $from = null)
 * @method static array sabtNamRefahKala(string $phoneNumber, string $name, string $code)
 * @method static array sendVerificationCode(string $phoneNumber, string $code)
 * @method static array sendOrderStatus(string $phoneNumber, string $name, string $status, string $trackingCode)
 * @method static array getStats()
 *
 * @see \App\Services\SmsService
 */
class Sms extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sms';
    }
}
