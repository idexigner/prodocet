<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Days of the week (Monday to Saturday)
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        
        // Generate time slots from 7:00 AM to 12:00 PM (midnight)
        // Each slot is 30 minutes long
        $slots = [];
        
        foreach ($days as $day) {
            // Start from 7:00 AM
            $startHour = 7;
            $startMinute = 0;
            
            // Generate slots until 12:00 PM (midnight) - which is 24:00 or 00:00 next day
            while ($startHour < 24) {
                // Create start time
                $startTime = sprintf('%02d:%02d:00', $startHour, $startMinute);
                
                // Calculate end time (30 minutes later)
                $endHour = $startHour;
                $endMinute = $startMinute + 30;
                
                // Handle minute overflow
                if ($endMinute >= 60) {
                    $endHour++;
                    $endMinute = $endMinute - 60;
                }
                
                // Handle hour overflow (shouldn't happen in our case, but just in case)
                if ($endHour >= 24) {
                    $endHour = 0;
                }
                
                $endTime = sprintf('%02d:%02d:00', $endHour, $endMinute);
                
                // Add slot to array
                $slots[] = [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration' => 30, // 30 minutes
                    'day_of_week' => $day,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Move to next slot
                $startMinute += 30;
                if ($startMinute >= 60) {
                    $startHour++;
                    $startMinute = 0;
                }
            }
        }
        
        // Insert all slots into database
        DB::table('slots')->insert($slots);
        
        $this->command->info('✅ Created ' . count($slots) . ' time slots');
        $this->command->info('📅 Days: ' . implode(', ', array_map('ucfirst', $days)));
        $this->command->info('⏰ Time range: 7:00 AM to 12:00 PM (midnight)');
        $this->command->info('⏱️ Duration: 30 minutes per slot');
        
        // Show some examples
        $this->command->info('📋 Example slots created:');
        $this->command->line('   Monday: 7:00-7:30, 7:30-8:00, 8:00-8:30, ...');
        $this->command->line('   Tuesday: 7:00-7:30, 7:30-8:00, 8:00-8:30, ...');
        $this->command->line('   ... and so on for all days');
    }
}