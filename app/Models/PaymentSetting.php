<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'server_key',
        'client_key',
        'merchant_id',
        'is_enabled',
        'environment',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function getMidtransConfig(): array
    {
        $setting = self::where('gateway', 'midtrans')->first();

        return [
            'server_key'  => $setting?->server_key ?? config('services.midtrans.server_key', ''),
            'client_key'  => $setting?->client_key ?? config('services.midtrans.client_key', ''),
            'merchant_id' => $setting?->merchant_id ?? config('services.midtrans.merchant_id', ''),
            'is_enabled'  => $setting?->is_enabled ?? false,
            'environment' => $setting?->environment ?? 'sandbox',
        ];
    }
}
