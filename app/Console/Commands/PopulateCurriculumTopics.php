<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CurriculumTopic;
use App\Models\CourseLevel;
use App\Models\Language;

class PopulateCurriculumTopics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:curriculum-topics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate curriculum topics with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating curriculum topics with sample data...');
        
        // Sample curriculum topics for Spanish Beginner A1
        $spanishA1Topics = [
            ['name' => 'Greetings and Introductions', 'teaching_hours' => 4, 'order_index' => 1],
            ['name' => 'Numbers and Basic Counting', 'teaching_hours' => 3, 'order_index' => 2],
            ['name' => 'Family and Relationships', 'teaching_hours' => 4, 'order_index' => 3],
            ['name' => 'Colors and Descriptions', 'teaching_hours' => 3, 'order_index' => 4],
            ['name' => 'Days of the Week and Time', 'teaching_hours' => 4, 'order_index' => 5],
            ['name' => 'Food and Drinks', 'teaching_hours' => 4, 'order_index' => 6],
            ['name' => 'Shopping and Money', 'teaching_hours' => 3, 'order_index' => 7],
            ['name' => 'Directions and Places', 'teaching_hours' => 4, 'order_index' => 8],
            ['name' => 'Weather and Seasons', 'teaching_hours' => 3, 'order_index' => 9],
            ['name' => 'Hobbies and Activities', 'teaching_hours' => 4, 'order_index' => 10],
        ];
        
        // Sample curriculum topics for English Intermediate B1
        $englishB1Topics = [
            ['name' => 'Present Perfect and Past Simple', 'teaching_hours' => 6, 'order_index' => 1],
            ['name' => 'Future Forms and Predictions', 'teaching_hours' => 5, 'order_index' => 2],
            ['name' => 'Conditionals and Hypothetical Situations', 'teaching_hours' => 6, 'order_index' => 3],
            ['name' => 'Reported Speech and Indirect Questions', 'teaching_hours' => 5, 'order_index' => 4],
            ['name' => 'Passive Voice and Causative Forms', 'teaching_hours' => 6, 'order_index' => 5],
            ['name' => 'Modal Verbs and Degrees of Certainty', 'teaching_hours' => 5, 'order_index' => 6],
            ['name' => 'Phrasal Verbs and Idiomatic Expressions', 'teaching_hours' => 6, 'order_index' => 7],
            ['name' => 'Formal and Informal Writing Styles', 'teaching_hours' => 5, 'order_index' => 8],
            ['name' => 'Business Communication and Meetings', 'teaching_hours' => 6, 'order_index' => 9],
            ['name' => 'Academic Writing and Research Skills', 'teaching_hours' => 5, 'order_index' => 10],
        ];
        
        // Get level and language IDs
        $spanishA1Level = CourseLevel::where('name', 'Beginner A1')->first();
        $englishB1Level = CourseLevel::where('name', 'Intermediate B1')->first();
        $spanishLanguage = Language::where('name', 'Spanish')->first();
        $englishLanguage = Language::where('name', 'English')->first();
        
        if (!$spanishA1Level || !$englishB1Level || !$spanishLanguage || !$englishLanguage) {
            $this->error('Required levels or languages not found in database');
            return;
        }
        
        // Update existing Spanish A1 topics
        $spanishA1Existing = CurriculumTopic::where('level_id', $spanishA1Level->id)
                                          ->where('language_id', $spanishLanguage->id)
                                          ->get();
        
        $this->info("Found {$spanishA1Existing->count()} existing Spanish A1 topics");
        
        foreach ($spanishA1Existing as $index => $topic) {
            if (isset($spanishA1Topics[$index])) {
                $topic->update([
                    'title' => $spanishA1Topics[$index]['name'],
                    'order_index' => $spanishA1Topics[$index]['order_index']
                ]);
                $this->info("Updated: {$topic->title}");
            }
        }
        
        // Update existing English B1 topics
        $englishB1Existing = CurriculumTopic::where('level_id', $englishB1Level->id)
                                          ->where('language_id', $englishLanguage->id)
                                          ->get();
        
        $this->info("Found {$englishB1Existing->count()} existing English B1 topics");
        
        foreach ($englishB1Existing as $index => $topic) {
            if (isset($englishB1Topics[$index])) {
                $topic->update([
                    'title' => $englishB1Topics[$index]['name'],
                    'order_index' => $englishB1Topics[$index]['order_index']
                ]);
                $this->info("Updated: {$topic->title}");
            }
        }
        
        $this->info('Curriculum topics populated successfully!');
    }
}
