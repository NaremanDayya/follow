<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'float' => (float) $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }

    public static function setValue(string $key, $value, string $type = 'string', string $description = null): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("system_setting_{$key}");
        
        return $setting;
    }

    public static function getBoolean(string $key, bool $default = false): bool
    {
        return (bool) static::getValue($key, $default);
    }

    public static function getInteger(string $key, int $default = 0): int
    {
        return (int) static::getValue($key, $default);
    }

    public static function initializeDefaults(): void
    {
        $defaults = [
            'allowed_client_late_days' => [
                'value' => '4',
                'type' => 'integer',
                'description' => 'Number of days before a client is considered late'
            ],
            'enable_email_notifications' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable email notifications for late clients'
            ],
            'company_name' => [
                'value' => 'Client Follow-Up System',
                'type' => 'string',
                'description' => 'Company name for reports and emails'
            ],
        ];

        foreach ($defaults as $key => $data) {
            static::setValue($key, $data['value'], $data['type'], $data['description']);
        }
    }
}
