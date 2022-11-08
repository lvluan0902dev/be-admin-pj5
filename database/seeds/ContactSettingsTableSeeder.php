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
                'content' => 'address'
            ],
            [
                'id' => 2,
                'title' => 'phone_number',
                'content' => 'phone_number'
            ],
            [
                'id' => 3,
                'title' => 'email',
                'content' => 'email'
            ],
            [
                'id' => 4,
                'title' => 'google_maps',
                'content' => 'google_maps'
            ],
        ];

        foreach ($data as $contact_setting) {
            \App\Models\ContactSetting::create($contact_setting);
        }
    }
}
