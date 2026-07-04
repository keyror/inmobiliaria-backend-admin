<?php

namespace App\Support;

use App\Models\CompanySetting;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

class TenantMailer
{
    /**
     * Resolve the mailer and from address for the current tenant.
     *
     * If the tenant has a custom SMTP configured, returns a mailer built with
     * those credentials and the company's chosen from address.
     * Otherwise falls back to the platform default mailer.
     *
     * @return array{mailer: Mailer, from: Address|null}
     */
    public static function resolve(?CompanySetting $setting): array
    {
        if (! $setting?->has_custom_smtp || ! $setting->smtp_host) {
            return ['mailer' => Mail::mailer(), 'from' => null];
        }

        $mailer = Mail::build([
            'transport' => 'smtp',
            'host' => $setting->smtp_host,
            'port' => $setting->smtp_port ?? 587,
            'encryption' => $setting->smtp_encryption ?? 'tls',
            'username' => $setting->smtp_username,
            'password' => $setting->smtp_password,
            'timeout' => 30,
        ]);

        $from = $setting->smtp_from_email
            ? new Address($setting->smtp_from_email)
            : null;

        return ['mailer' => $mailer, 'from' => $from];
    }
}
