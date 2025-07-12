<?php

namespace Database\Seeders;

use App\Models\KeywordSet;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultKeywordSetsSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@riskcontrol.ng')->first();

        if (!$admin) {
            echo "❌ Admin user not found. Please run AdminUserSeeder first.\n";
            return;
        }

        $keywordSets = [
            [
                'job_title' => 'Software Developer',
                'keywords' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Git', 'HTML', 'CSS'],
                'description' => 'Backend developer with Laravel and PHP experience',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'job_title' => 'Frontend Developer',
                'keywords' => ['JavaScript', 'React', 'Vue.js', 'HTML', 'CSS', 'Bootstrap', 'Tailwind CSS'],
                'description' => 'Frontend developer with modern JavaScript frameworks',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'job_title' => 'Digital Marketing Manager',
                'keywords' => ['Digital Marketing', 'SEO', 'Google Ads', 'Social Media', 'Content Marketing', 'Analytics'],
                'description' => 'Marketing professional with digital expertise',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'job_title' => 'Accountant',
                'keywords' => ['Accounting', 'QuickBooks', 'Financial Analysis', 'Tax Preparation', 'Excel', 'Bookkeeping'],
                'description' => 'Qualified accountant with software experience',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'job_title' => 'Project Manager',
                'keywords' => ['Project Management', 'Agile', 'Scrum', 'Leadership', 'Planning', 'Risk Management'],
                'description' => 'Experienced project manager with agile methodologies',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'job_title' => 'Sales Representative',
                'keywords' => ['Sales', 'Customer Relations', 'CRM', 'Lead Generation', 'Negotiation', 'Communication'],
                'description' => 'Sales professional with customer relationship skills',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'job_title' => 'Data Analyst',
                'keywords' => ['Data Analysis', 'Excel', 'SQL', 'Power BI', 'Python', 'Statistics', 'Reporting'],
                'description' => 'Data analyst with statistical and reporting skills',
                'is_active' => true,
                'created_by' => $admin->id
            ],
            [
                'job_title' => 'Human Resources Manager',
                'keywords' => ['Human Resources', 'Recruitment', 'Employee Relations', 'HR Policy', 'Training', 'Performance Management'],
                'description' => 'HR professional with recruitment and management experience',
                'is_active' => true,
                'created_by' => $admin->id
            ]
        ];

        foreach ($keywordSets as $keywordSet) {
            KeywordSet::updateOrCreate(
                ['job_title' => $keywordSet['job_title']],
                $keywordSet
            );
        }

        echo "✅ Default keyword sets created:\n";
        foreach ($keywordSets as $set) {
            echo "📝 {$set['job_title']}\n";
        }
    }
}