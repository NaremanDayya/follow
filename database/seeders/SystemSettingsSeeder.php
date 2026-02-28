<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
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
            'default_client_status' => [
                'value' => 'active',
                'type' => 'string',
                'description' => 'Default status for new clients'
            ],
            'chat_message_retention_days' => [
                'value' => '365',
                'type' => 'integer',
                'description' => 'Number of days to retain chat messages'
            ],
            'enable_real_time_notifications' => [
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable real-time notifications for chat and updates'
            ],
        ];

        foreach ($settings as $key => $data) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'type' => $data['type'],
                    'description' => $data['description'],
                ]
            );
        }
    }
}
