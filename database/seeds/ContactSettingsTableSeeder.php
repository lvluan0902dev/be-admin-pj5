<?php

use Illuminate\Database\Seeder;

class ContactSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'title' => 'address',
                'content' => 'address',
                'status' => 0
            ],
            [
                'id' => 2,
                'title' => 'phone_number',
                'content' => 'phone_number',
                'status' => 0
            ],
            [
                'id' => 3,
                'title' => 'email',
                'content' => 'email',
                'status' => 0
            ],
            [
                'id' => 4,
                'title' => 'google_maps',
                'content' => 'google_maps',
                'status' => 0
            ],
        ];

        foreach ($data as $contact_setting) {
            \App\Models\ContactSetting::create($contact_setting);
        }
    }
}
