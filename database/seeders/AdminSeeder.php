<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'admin@seplag.mt.gov.br')->first();

        if (!$user) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@seplag.mt.gov.br',
                'password' => Hash::make('123456789'),
            ]);
        }
    }
}
