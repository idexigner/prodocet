<?php

namespace App\Traits;

use App\Models\Course;
use App\Models\Group;
use App\Models\StudentAcademicHour;
use App\Models\GroupStudent;
use App\Models\GroupSession;
use Carbon\Carbon;

trait CourseModuleHelpers
{
    /**
     * Calculate teaching hours from duration in minutes.
     */
    public function calculateTeachingHours(int $durationMinutes): float
    {
        // HC = Teaching Hours (45-minute units)
        return round($durationMinutes / 45, 2);
    }

    /**
     * Calculate regular hours from teaching hours.
     */
    public function calculateRegularHours(int $teachingHours): float
    {
        return $teachingHours * 0.75; // Convert HC to regular hours
    }

    /**
     * Calculate total classes from teaching hours (assuming 90-minute classes).
     */
    public function calculateTotalClasses(int $teachingHours): int
    {
        return ceil($teachingHours / 2); // 2 HC per 90-minute class
    }

    /**
     * Check if student can enroll in group.
     */
    public function canStudentEnrollInGroup(int $studentId, int $groupId): bool
    {
        $group = Group::find($groupId);
        if (!$group || $group->is_full) {
            return false;
        }

        // Check if student is already enrolled
        $existingEnrollment = GroupStudent::where('group_id', $groupId)
                                        ->where('student_id', $studentId)
                                        ->first();

        return !$existingEnrollment;
    }

    /**
     * Check if student has sufficient academic hours.
     */
    public function hasSufficientAcademicHours(int $studentId, int $courseId, int $requiredHours): bool
    {
        $academicHours = StudentAcademicHour::where('student_id', $studentId)
                                         ->where('course_id', $courseId)
                                         ->where('status', 'active')
                                         ->first();

        if (!$academicHours) {
            return false;
        }

        return $academicHours->remaining_hours >= $requiredHours;
    }

    /**
     * Find compatible groups for student.
     */
    public function findCompatibleGroups(int $studentId, int $courseId, array $preferredSchedule = []): array
    {
        $course = Course::find($courseId);
        if (!$course) {
            return [];
        }

        $groups = Group::with(['teacher', 'course'])
                      ->where('course_id', $courseId)
                      ->where('status', 'active')
                      ->where('current_students', '<', \DB::raw('max_students'))
                      ->get();

        $compatibleGroups = [];

        foreach ($groups as $group) {
            if ($this->canStudentEnrollInGroup($studentId, $group->id)) {
                $compatibleGroups[] = $group;
            }
        }

        return $compatibleGroups;
    }

    /**
     * Check if session can be cancelled.
     */
    public function canCancelSession(int $sessionId): bool
    {
        $session = GroupSession::with('group')->find($sessionId);
        if (!$session) {
            return false;
        }

        if (!$session->group->can_cancel_classes) {
            return false;
        }

        $hoursUntilSession = now()->diffInHours($session->session_date . ' ' . $session->start_time, false);
        return $hoursUntilSession >= $session->group->cancellation_hours_advance;
    }

    /**
     * Get upcoming sessions for a group.
     */
    public function getUpcomingSessions(int $groupId, int $limit = 10): array
    {
        return GroupSession::where('group_id', $groupId)
                          ->where('session_date', '>=', now()->toDateString())
                          ->where('status', 'scheduled')
                          ->orderBy('session_date')
                          ->orderBy('start_time')
                          ->limit($limit)
                          ->get()
                          ->toArray();
    }

    /**
     * Get past sessions for a group.
     */
    public function getPastSessions(int $groupId, int $limit = 10): array
    {
        return GroupSession::where('group_id', $groupId)
                          ->where('session_date', '<', now()->toDateString())
                          ->orderBy('session_date', 'desc')
                          ->orderBy('start_time', 'desc')
                          ->limit($limit)
                          ->get()
                          ->toArray();
    }

    /**
     * Calculate student progress in group.
     */
    public function calculateStudentProgress(int $studentId, int $groupId): array
    {
        $enrollment = GroupStudent::where('student_id', $studentId)
                                ->where('group_id', $groupId)
                                ->first();

        if (!$enrollment) {
            return [
                'hours_purchased' => 0,
                'hours_used' => 0,
                'hours_remaining' => 0,
                'usage_percentage' => 0,
                'status' => 'not_enrolled'
            ];
        }

        $usagePercentage = $enrollment->academic_hours_purchased > 0 
            ? ($enrollment->academic_hours_used / $enrollment->academic_hours_purchased) * 100 
            : 0;

        return [
            'hours_purchased' => $enrollment->academic_hours_purchased,
            'hours_used' => $enrollment->academic_hours_used,
            'hours_remaining' => $enrollment->remaining_hours,
            'usage_percentage' => round($usagePercentage, 2),
            'status' => $enrollment->status
        ];
    }

    /**
     * Generate group code.
     */
    public function generateGroupCode(Course $course, int $sequence = 1): string
    {
        $languageCode = strtoupper($course->language->code);
        $levelCode = $course->level->code;
        $sequenceCode = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return "{$languageCode}-{$levelCode}-{$sequenceCode}";
    }

    /**
     * Generate course code.
     */
    public function generateCourseCode(string $languageCode, string $levelCode, int $sequence = 1): string
    {
        $languageCode = strtoupper($languageCode);
        $sequenceCode = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return "{$languageCode}-{$levelCode}-{$sequenceCode}";
    }

    /**
     * Check if group is in business hours for cancellation.
     */
    public function isBusinessHours(): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        $hour = $now->hour;

        // Monday to Friday: 8am to 8pm
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            return $hour >= 8 && $hour <= 20;
        }

        // Saturday: 9am to 3pm
        if ($dayOfWeek === 6) {
            return $hour >= 9 && $hour <= 15;
        }

        // Sunday: closed
        return false;
    }

    /**
     * Get curriculum topics for course.
     */
    public function getCurriculumTopicsForCourse(int $courseId): array
    {
        $course = Course::with(['language', 'level'])->find($courseId);
        if (!$course) {
            return [];
        }

        return \App\Models\CurriculumTopic::where('language_id', $course->language_id)
                                        ->where('level_id', $course->level_id)
                                        ->active()
                                        ->ordered()
                                        ->get()
                                        ->toArray();
    }

    /**
     * Get available topics for group (not yet used).
     */
    public function getAvailableTopicsForGroup(int $groupId): array
    {
        $group = Group::with('course')->find($groupId);
        if (!$group) {
            return [];
        }

        $usedTopicIds = GroupSession::where('group_id', $groupId)
                                  ->whereNotNull('topic_id')
                                  ->pluck('topic_id')
                                  ->toArray();

        return \App\Models\CurriculumTopic::where('language_id', $group->course->language_id)
                                        ->where('level_id', $group->course->level_id)
                                        ->whereNotIn('id', $usedTopicIds)
                                        ->active()
                                        ->ordered()
                                        ->get()
                                        ->toArray();
    }

    /**
     * Format duration for display.
     */
    public function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours}h {$remainingMinutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$remainingMinutes}m";
        }
    }

    /**
     * Calculate group duration in days.
     */
    public function calculateGroupDuration(Carbon $startDate, Carbon $endDate): int
    {
        return $startDate->diffInDays($endDate);
    }

    /**
     * Check if group is active.
     */
    public function isGroupActive(int $groupId): bool
    {
        $group = Group::find($groupId);
        return $group && $group->status === 'active' && $group->is_active;
    }

    /**
     * Get group statistics.
     */
    public function getGroupStatistics(int $groupId): array
    {
        $group = Group::with(['students', 'sessions'])->find($groupId);
        if (!$group) {
            return [];
        }

        $totalSessions = $group->sessions->count();
        $completedSessions = $group->sessions->where('status', 'completed')->count();
        $cancelledSessions = $group->sessions->where('status', 'cancelled')->count();
        $upcomingSessions = $group->sessions->where('status', 'scheduled')
                                           ->where('session_date', '>=', now()->toDateString())
                                           ->count();

        return [
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'cancelled_sessions' => $cancelledSessions,
            'upcoming_sessions' => $upcomingSessions,
            'completion_rate' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 0,
            'student_count' => $group->current_students,
            'max_students' => $group->max_students,
            'occupancy_rate' => round(($group->current_students / $group->max_students) * 100, 2),
        ];
    }
}
