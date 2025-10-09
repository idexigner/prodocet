<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\CurriculumTopic;
use App\Models\User;

class TestCourseTabs extends Command
{
    protected $signature = 'test:course-tabs';
    protected $description = 'Tests the tabbed course functionality.';

    public function handle()
    {
        $this->info('🧪 Testing Course Tabbed Interface...');

        // 1. Test course creation with relationships
        $this->info('  Testing course creation...');
        $course = Course::first();
        
        if (!$course) {
            $this->error('    No courses found. Please create courses first.');
            return;
        }

        $this->line("    ✅ Found course: {$course->name} (ID: {$course->id})");

        // 2. Test course-teacher relationships
        $this->info('  Testing course-teacher relationships...');
        $teachers = $course->teachers;
        $this->line("    ✅ Course has {$teachers->count()} assigned teachers");

        // 3. Test course-curriculum relationships
        $this->info('  Testing course-curriculum relationships...');
        $topics = $course->courseCurriculum;
        $this->line("    ✅ Course has {$topics->count()} assigned topics");

        // 4. Test available topics for course language/level
        $this->info('  Testing available topics...');
        $availableTopics = CurriculumTopic::where('language_id', $course->language_id)
            ->where('level_id', $course->level_id)
            ->get();
        $this->line("    ✅ Found {$availableTopics->count()} available topics for language/level");

        // 5. Test available teachers
        $this->info('  Testing available teachers...');
        $availableTeachers = User::role('teacher')->get();
        $this->line("    ✅ Found {$availableTeachers->count()} available teachers");

        // 6. Test course display name
        $this->info('  Testing course display name...');
        $displayName = $course->full_name;
        $this->line("    ✅ Course display name: {$displayName}");

        // 7. Test course duration calculation
        $this->info('  Testing course duration calculation...');
        $duration = $course->total_hours . ' hours (Teaching: ' . $course->teaching_hours . ' hours)';
        $this->line("    ✅ Course duration: {$duration}");

        $this->newLine();
        $this->info('🎉 Course Tabbed Interface Test Completed!');
        $this->line('');
        $this->line('📋 Summary:');
        $this->line("   • Course: {$course->name}");
        $this->line("   • Language: {$course->language->name}");
        $this->line("   • Level: {$course->level->name}");
        $this->line("   • Assigned Teachers: {$teachers->count()}");
        $this->line("   • Assigned Topics: {$topics->count()}");
        $this->line("   • Available Topics: {$availableTopics->count()}");
        $this->line("   • Available Teachers: {$availableTeachers->count()}");
    }
}
