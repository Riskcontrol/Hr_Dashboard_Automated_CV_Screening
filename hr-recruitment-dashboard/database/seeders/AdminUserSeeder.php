<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@riskcontrol.ng'],
            [
                'name' => 'HR Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Create HR manager
        User::updateOrCreate(
            ['email' => 'hr@riskcontrol.ng'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password123'),
                'role' => 'hr_manager',
            ]
        );

        echo "✅ Admin users created:\n";
        echo "📧 Email: admin@riskcontrol.ng | Password: password123\n";
        echo "📧 Email: hr@riskcontrol.ng | Password: password123\n";
    }
}