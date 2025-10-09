<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\CurriculumTopic;
use App\Models\RateScheme;

class TestCourseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:course-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test course data and relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Course Data...');
        
        // Test rate schemes first
        $this->info("\n--- Rate Schemes ---");
        $rateSchemes = RateScheme::all();
        $this->info("Found {$rateSchemes->count()} rate schemes");
        foreach ($rateSchemes as $scheme) {
            $this->info("ID: {$scheme->id}, Name: '{$scheme->name}', Rate: $" . number_format($scheme->rate_per_hour, 2) . "/hour");
        }
        
        // Test courses
        $courses = Course::with(['level', 'language', 'rateScheme', 'curriculumTopics'])->get();
        $this->info("\nFound {$courses->count()} courses");
        
        foreach ($courses as $course) {
            $this->info("\n--- Course: {$course->name} (ID: {$course->id}) ---");
            $this->info("Level: " . ($course->level ? $course->level->name : 'N/A') . " (ID: {$course->level_id})");
            $this->info("Language: " . ($course->language ? $course->language->name : 'N/A') . " (ID: {$course->language_id})");
            $this->info("Rate Scheme: " . ($course->rateScheme ? $course->rateScheme->name . ' ($' . $course->rateScheme->rate_per_hour . '/hour)' : 'N/A') . " (ID: {$course->rate_scheme_id})");
            $this->info("Topics Count: " . $course->curriculumTopics->count());
            
            if ($course->curriculumTopics->count() > 0) {
                $totalHours = $course->curriculumTopics->sum('teaching_hours');
                $this->info("Total Hours: {$totalHours}");
                
                if ($course->rateScheme) {
                    $totalCost = $totalHours * $course->rateScheme->rate_per_hour;
                    $this->info("Total Cost: $" . number_format($totalCost, 2));
                }
                
                $this->info("Topics:");
                foreach ($course->curriculumTopics as $topic) {
                    $this->info("  - {$topic->name} ({$topic->teaching_hours} hours)");
                }
            }
        }
        
        // Test curriculum topics
        $this->info("\n--- Curriculum Topics ---");
        $topics = CurriculumTopic::with('level', 'language')->get();
        $this->info("Found {$topics->count()} curriculum topics");
        
        foreach ($topics->take(5) as $topic) {
            $this->info("  - ID: {$topic->id}, Name: '{$topic->name}', Level ID: {$topic->level_id}, Language ID: {$topic->language_id}, Level: " . ($topic->level ? $topic->level->name : 'N/A') . ", Language: " . ($topic->language ? $topic->language->name : 'N/A') . ", Hours: {$topic->teaching_hours}");
        }
        
        // Check specific course and its level/language
        $testCourse = Course::first();
        if ($testCourse) {
            $this->info("\n--- Testing Course Relationship ---");
            $this->info("Course: {$testCourse->name}");
            $this->info("Course Level ID: {$testCourse->level_id}");
            $this->info("Course Language ID: {$testCourse->language_id}");
            
            // Check if there are curriculum topics with matching level_id and language_id
            $matchingTopics = CurriculumTopic::where('level_id', $testCourse->level_id)
                                           ->where('language_id', $testCourse->language_id)
                                           ->get();
            $this->info("Matching topics for this course: {$matchingTopics->count()}");
            
            foreach ($matchingTopics as $topic) {
                $this->info("  - {$topic->name} ({$topic->teaching_hours} hours)");
            }
        }
        
        // Check if there are any courses with curriculum topics
        $this->info("\n--- Checking Course-Curriculum Relationships ---");
        $coursesWithTopics = Course::whereHas('curriculumTopics')->get();
        $this->info("Courses with curriculumTopics: {$coursesWithTopics->count()}");
        
        $coursesWithCourseCurriculum = Course::whereHas('courseCurriculum')->get();
        $this->info("Courses with courseCurriculum: {$coursesWithCourseCurriculum->count()}");
        
        // Test the curriculumTopics relationship for one course
        $testCourse = Course::with('curriculumTopics')->first();
        if ($testCourse) {
            $this->info("Test course '{$testCourse->name}' curriculumTopics count: {$testCourse->curriculumTopics->count()}");
            if ($testCourse->curriculumTopics->count() > 0) {
                foreach ($testCourse->curriculumTopics as $topic) {
                    $this->info("  - {$topic->name} ({$topic->teaching_hours} hours)");
                }
            }
        }
        
        // Check if there are any rate schemes with non-zero rates
        $this->info("\n--- Checking Rate Schemes ---");
        $validRateSchemes = RateScheme::where('hourly_rate', '>', 0)->get();
        $this->info("Rate schemes with rates > 0: {$validRateSchemes->count()}");
        
        // Check course_curriculum pivot table
        $this->info("\n--- Checking Course Curriculum Pivot Table ---");
        $pivotData = \DB::table('course_curriculum')->get();
        $this->info("Course curriculum entries: {$pivotData->count()}");
        foreach ($pivotData as $pivot) {
            $this->info("  Course ID: {$pivot->course_id}, Topic ID: {$pivot->curriculum_topic_id}, Order: {$pivot->order_index}");
        }
        
        // Check curriculum_topics table structure
        $this->info("\n--- Curriculum Topics Table Structure ---");
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('curriculum_topics');
        $this->info("Columns: " . implode(', ', $columns));
        
        // Check a sample curriculum topic with all fields
        $sampleTopic = CurriculumTopic::first();
        if ($sampleTopic) {
            $this->info("\n--- Sample Curriculum Topic ---");
            $this->info("ID: {$sampleTopic->id}");
            $this->info("Title: '{$sampleTopic->title}'");
            $this->info("Description: '{$sampleTopic->description}'");
            $this->info("Level ID: {$sampleTopic->level_id}");
            $this->info("Language ID: {$sampleTopic->language_id}");
            $this->info("Order Index: {$sampleTopic->order_index}");
            $this->info("Is Active: " . ($sampleTopic->is_active ? 'Yes' : 'No'));
        }
        
        // Test the new approach - fetch topics separately
        $this->info("\n--- Testing New Approach ---");
        $testCourse = Course::where('name', 'Spanish for Beginners')->first();
        if ($testCourse) {
            $topics = CurriculumTopic::where('level_id', $testCourse->level_id)
                                   ->where('language_id', $testCourse->language_id)
                                   ->orderBy('order_index')
                                   ->get();
            $this->info("Course: {$testCourse->name}");
            $this->info("Level ID: {$testCourse->level_id}, Language ID: {$testCourse->language_id}");
            $this->info("Found {$topics->count()} topics:");
            foreach ($topics as $topic) {
                $this->info("  - {$topic->title} (Order: {$topic->order_index})");
            }
        }
    }
}
