<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Chatbot AI KOPMA MART (opsional — kosongkan untuk mode database saja)
    'openai' => [
        'key'    => env('OPENAI_API_KEY'),
        'url'    => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),
        'model'  => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    // Nomor WA bisnis resmi KOPMA MART — format internasional tanpa "+" / spasi / "-".
    // Semua tombol "Pesan via WA" / "Bagikan struk" langsung ke https://wa.me/<nomor>?text=...
    // (tanpa gateway OpenWA). Ubah nilai default di sini bila nomor bisnis berganti.
    'wa' => [
        'number' => env('WA_BUSINESS_NUMBER', '087853783946'),
        'name'   => env('WA_BUSINESS_NAME', 'KOPMA MART'),
    ],

    /*
    |--------------------------------------------------------------------------
    | QRIS DINAMIS (EMVCo) — data merchant KOPMA MART
    |--------------------------------------------------------------------------
    | Dipakai QrisService untuk merangkai payload QRIS yang bisa discan
    | aplikasi bank / e-wallet. Ganti NMID & Merchant ID dengan data resmi
    | dari acquirer (Bank/QRIS) agar dana benar-benar masuk ke rekening toko.
    */
    'qris' => [
        'guid'              => env('QRIS_GUID', 'ID.CO.QRIS.WWW'),
        'nmid'              => env('QRIS_NMID', '9360001234567'),
        'merchant_id'       => env('QRIS_MERCHANT_ID', '93600012345670001'),
        'merchant_criteria' => env('QRIS_MERCHANT_CRITERIA', 'UMI'), // UMI/UKE/UME/UBE
        'merchant_name'     => env('QRIS_MERCHANT_NAME', 'KOPMA MART'),
        'merchant_city'     => env('QRIS_MERCHANT_CITY', 'YOGYAKARTA'),
        'postal_code'       => env('QRIS_POSTAL_CODE', '55281'),
        'mcc'               => env('QRIS_MCC', '5411'),      // 5411 = minimarket
        'currency'          => env('QRIS_CURRENCY', '360'),  // 360 = IDR
        'country'           => env('QRIS_COUNTRY', 'ID'),
        'terminal_label'    => env('QRIS_TERMINAL_LABEL', 'KOPMA01'),
    ],

    /*
    |---------------------------------------------------------------------
    | MIDTRANS SNAP - fallback bila tabel payment_settings kosong.
    |---------------------------------------------------------------------
    | Nilai utama diambil dari tabel payment_settings (menu Pengaturan
    | Bayar di admin). Env hanya dipakai sebagai cadangan.
    */
    'midtrans' => [
        'server_key'  => env('MIDTRANS_SERVER_KEY', ''),
        'client_key'  => env('MIDTRANS_CLIENT_KEY', ''),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
        'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),
    ],

];
