<?php

namespace App\Services;

use App\Models\SiteSetting;

class DestinationSettings
{
    private ?array $settings = null;

    public function values(): array
    {
        return $this->settings ??= array_replace([
            'operating_hours' => __('messages.operating_hours', [], 'id'),
            'operating_hours_en' => __('messages.operating_hours', [], 'en'),
            'address' => 'Desa Bakbakan, Gianyar, Bali',
            'address_en' => 'Bakbakan Village, Gianyar, Bali',
            'phone' => '', 'whatsapp' => config('payment.contact_whatsapp', ''), 'email' => '',
            'maps_url' => config('tourism.maps_url'), 'maps_embed' => config('tourism.maps_embed'),
            'rules' => '', 'rules_en' => '',
            'payment_minutes' => config('booking.payment_minutes', 120),
        ], SiteSetting::find('destination')?->value ?? []);
    }

    public function localized(string $key): string
    {
        $values = $this->values();

        return (string) ($values[app()->getLocale() === 'en' ? $key.'_en' : $key] ?? $values[$key] ?? '');
    }

    public function save(array $values): void
    {
        SiteSetting::updateOrCreate(['key' => 'destination'], ['value' => $values]);
        $this->settings = null;
    }
}
