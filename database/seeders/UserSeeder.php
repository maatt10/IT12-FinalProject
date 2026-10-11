<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if users already exist
        if (User::count() > 0) {
            $this->command->info('Users already exist. Skipping UserSeeder.');
            return;
        }

        User::create([
            'role' => 'owner',
            'first_name' => 'Lara',
            'middle_name' => null,
            'last_name' => 'Owner',
            'username' => 'lara',
            'password' => 'changeme123',
            'is_active' => true,
        ]);

        User::create([
            'role' => 'staff',
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => 'Staff',
            'username' => 'staff',
            'password' => 'changeme123',
            'is_active' => true,
        ]);

        $this->command->info('Users seeded: lara / staff (password: changeme123)');
    }
}