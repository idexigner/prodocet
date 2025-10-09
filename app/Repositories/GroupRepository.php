<?php

namespace App\Repositories;

use App\Interfaces\GroupInterface;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\GroupSession;
use App\Models\StudentAcademicHour;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GroupRepository implements GroupInterface
{
    public function getAll(): Collection
    {
        return Group::with(['course.language', 'course.level', 'teacher', 'students'])->get();
    }

    public function getById($id): ?Group
    {
        return Group::with(['course.language', 'course.level', 'teacher', 'students', 'sessions.topic'])->find($id);
    }

    public function create(array $data): Group
    {
        return Group::create($data);
    }

    public function update($id, array $data): bool
    {
        $group = Group::find($id);
        if (!$group) {
            return false;
        }
        return $group->update($data);
    }

    public function delete($id): bool
    {
        $group = Group::find($id);
        if (!$group) {
            return false;
        }
        return $group->delete();
    }

    public function restore($id): bool
    {
        $group = Group::withTrashed()->find($id);
        if (!$group) {
            return false;
        }
        return $group->restore();
    }

    public function getByTeacher($teacherId): Collection
    {
        return Group::with(['course.language', 'course.level', 'students'])
                    ->byTeacher($teacherId)
                    ->active()
                    ->get();
    }

    public function getByCourse($courseId): Collection
    {
        return Group::with(['teacher', 'students'])
                    ->byCourse($courseId)
                    ->active()
                    ->get();
    }

    public function getActive(): Collection
    {
        return Group::with(['course.language', 'course.level', 'teacher'])
                    ->active()
                    ->get();
    }

    public function getByStatus($status): Collection
    {
        return Group::with(['course.language', 'course.level', 'teacher'])
                    ->byStatus($status)
                    ->get();
    }

    public function enrollStudent($groupId, $studentId, array $data): bool
    {
        try {
            DB::beginTransaction();

            $group = Group::find($groupId);
            if (!$group || $group->is_full) {
                return false;
            }

            // Check if student already enrolled
            $existingEnrollment = GroupStudent::where('group_id', $groupId)
                                            ->where('student_id', $studentId)
                                            ->first();

            if ($existingEnrollment) {
                return false;
            }

            // Create enrollment
            GroupStudent::create([
                'group_id' => $groupId,
                'student_id' => $studentId,
                'academic_hours_purchased' => $data['academic_hours_purchased'],
                'enrollment_date' => now()->toDateString(),
                'status' => 'enrolled'
            ]);

            // Update group student count
            $group->increment('current_students');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function removeStudent($groupId, $studentId): bool
    {
        try {
            DB::beginTransaction();

            $enrollment = GroupStudent::where('group_id', $groupId)
                                    ->where('student_id', $studentId)
                                    ->first();

            if (!$enrollment) {
                return false;
            }

            $enrollment->delete();

            // Update group student count
            $group = Group::find($groupId);
            $group->decrement('current_students');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function transferStudent($groupId, $studentId, $newGroupId): bool
    {
        try {
            DB::beginTransaction();

            $enrollment = GroupStudent::where('group_id', $groupId)
                                    ->where('student_id', $studentId)
                                    ->first();

            if (!$enrollment) {
                return false;
            }

            $newGroup = Group::find($newGroupId);
            if (!$newGroup || $newGroup->is_full) {
                return false;
            }

            // Update enrollment
            $enrollment->update(['group_id' => $newGroupId]);

            // Update student counts
            Group::find($groupId)->decrement('current_students');
            $newGroup->increment('current_students');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getGroupStudents($groupId): Collection
    {
        return GroupStudent::with(['student'])
                          ->byGroup($groupId)
                          ->get();
    }

    public function updateStudentStatus($groupId, $studentId, $status): bool
    {
        $enrollment = GroupStudent::where('group_id', $groupId)
                                ->where('student_id', $studentId)
                                ->first();

        if (!$enrollment) {
            return false;
        }

        return $enrollment->update(['status' => $status]);
    }

    public function createSessions($groupId, array $sessions): bool
    {
        try {
            DB::beginTransaction();

            foreach ($sessions as $sessionData) {
                $sessionData['group_id'] = $groupId;
                GroupSession::create($sessionData);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function updateSession($sessionId, array $data): bool
    {
        $session = GroupSession::find($sessionId);
        if (!$session) {
            return false;
        }
        return $session->update($data);
    }

    public function cancelSession($sessionId): bool
    {
        $session = GroupSession::find($sessionId);
        if (!$session) {
            return false;
        }
        return $session->update(['status' => 'cancelled']);
    }

    public function rescheduleSession($sessionId, array $data): bool
    {
        $session = GroupSession::find($sessionId);
        if (!$session) {
            return false;
        }
        return $session->update(array_merge($data, ['status' => 'rescheduled']));
    }

    public function getGroupSessions($groupId): Collection
    {
        return GroupSession::with(['topic'])
                          ->byGroup($groupId)
                          ->orderBy('session_date')
                          ->orderBy('start_time')
                          ->get();
    }

    public function getUpcomingSessions(): Collection
    {
        return GroupSession::with(['group.course', 'group.teacher', 'topic'])
                          ->upcoming()
                          ->get();
    }

    public function consumeHours($studentId, $sessionId, $hoursUsed): bool
    {
        try {
            DB::beginTransaction();

            $session = GroupSession::find($sessionId);
            if (!$session) {
                return false;
            }

            $enrollment = GroupStudent::where('group_id', $session->group_id)
                                    ->where('student_id', $studentId)
                                    ->first();

            if (!$enrollment) {
                return false;
            }

            // Update used hours
            $enrollment->increment('academic_hours_used', $hoursUsed);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getStudentHours($studentId): Collection
    {
        return StudentAcademicHour::with(['course.language', 'course.level'])
                                ->byStudent($studentId)
                                ->active()
                                ->get();
    }

    public function transferHours($studentId, $fromGroupId, $toGroupId, $hours): bool
    {
        try {
            DB::beginTransaction();

            $fromEnrollment = GroupStudent::where('group_id', $fromGroupId)
                                        ->where('student_id', $studentId)
                                        ->first();

            $toEnrollment = GroupStudent::where('group_id', $toGroupId)
                                      ->where('student_id', $studentId)
                                      ->first();

            if (!$fromEnrollment || !$toEnrollment) {
                return false;
            }

            if ($fromEnrollment->remaining_hours < $hours) {
                return false;
            }

            // Transfer hours
            $fromEnrollment->decrement('academic_hours_used', $hours);
            $toEnrollment->increment('academic_hours_purchased', $hours);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function findCompatibleGroups($studentId, $courseId, $preferredSchedule): Collection
    {
        // This is a simplified version - in reality, you'd implement complex scheduling logic
        return Group::with(['course', 'teacher'])
                  ->byCourse($courseId)
                  ->where('status', 'active')
                  ->where('current_students', '<', DB::raw('max_students'))
                  ->get();
    }

    public function getWithRelations($id): ?Group
    {
        return Group::with([
            'course.language',
            'course.level',
            'course.rateScheme',
            'teacher',
            'students',
            'sessions.topic',
            'groupStudents.student'
        ])->find($id);
    }

    public function search($query): Collection
    {
        return Group::with(['course.language', 'course.level', 'teacher'])
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%")
                    ->orWhereHas('course', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->active()
                    ->get();
    }

    public function getPaginated($perPage = 15): LengthAwarePaginator
    {
        return Group::with(['course.language', 'course.level', 'teacher'])
                    ->active()
                    ->paginate($perPage);
    }
}
