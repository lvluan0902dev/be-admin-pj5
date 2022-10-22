<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
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
                'name' => 'admin',
                'email' => 'admin@admin.local',
                'password' => \Illuminate\Support\Facades\Hash::make('123456')
            ]
        ];

        foreach ($data as $user) {
            \App\User::create($user);
        }
    }
}
