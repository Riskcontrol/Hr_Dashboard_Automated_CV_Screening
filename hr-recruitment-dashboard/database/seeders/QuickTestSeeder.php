<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KeywordSet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class QuickTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        KeywordSet::truncate();
        User::where('email', 'LIKE', '%@riskcontrol.ng')->delete();

        // Create admin user
        $admin = User::create([
            'name' => 'HR Administrator',
            'email' => 'admin@riskcontrol.ng',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create some keyword sets
        $keywordSets = [
            [
                'job_title' => 'Web Developer',
                'keywords' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'HTML', 'CSS'],
                'description' => 'Full stack web developer position',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'job_title' => 'Digital Marketer',
                'keywords' => ['Digital Marketing', 'SEO', 'Google Ads', 'Social Media', 'Content Marketing'],
                'description' => 'Digital marketing specialist role',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'job_title' => 'Data Analyst',
                'keywords' => ['Excel', 'SQL', 'Data Analysis', 'Power BI', 'Python', 'Statistics'],
                'description' => 'Data analysis and reporting position',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($keywordSets as $keywordSet) {
            KeywordSet::create($keywordSet);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Test data created successfully!');
        $this->command->info('📧 Admin Email: admin@riskcontrol.ng');
        $this->command->info('🔑 Password: password123');
    }
}