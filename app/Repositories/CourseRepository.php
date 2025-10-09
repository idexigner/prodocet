<?php

namespace App\Repositories;

use App\Interfaces\CourseInterface;
use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository implements CourseInterface
{
    public function getAll(): Collection
    {
        return Course::with(['language', 'level', 'rateScheme', 'creator'])->get();
    }

    public function getById($id): ?Course
    {
        return Course::with(['language', 'level', 'rateScheme', 'creator', 'groups', 'curriculumTopics'])->find($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update($id, array $data): bool
    {
        $course = Course::find($id);
        if (!$course) {
            return false;
        }
        return $course->update($data);
    }

    public function delete($id): bool
    {
        $course = Course::find($id);
        if (!$course) {
            return false;
        }
        return $course->delete();
    }

    public function restore($id): bool
    {
        $course = Course::withTrashed()->find($id);
        if (!$course) {
            return false;
        }
        return $course->restore();
    }

    public function getByLanguage($languageId): Collection
    {
        return Course::with(['language', 'level', 'rateScheme'])
                    ->byLanguage($languageId)
                    ->active()
                    ->get();
    }

    public function getByLevel($levelId): Collection
    {
        return Course::with(['language', 'level', 'rateScheme'])
                    ->byLevel($levelId)
                    ->active()
                    ->get();
    }

    public function getByRateScheme($rateSchemeId): Collection
    {
        return Course::with(['language', 'level', 'rateScheme'])
                    ->where('rate_scheme_id', $rateSchemeId)
                    ->active()
                    ->get();
    }

    public function getActive(): Collection
    {
        return Course::with(['language', 'level', 'rateScheme'])
                    ->active()
                    ->get();
    }

    public function toggleStatus($id): bool
    {
        $course = Course::find($id);
        if (!$course) {
            return false;
        }
        return $course->update(['is_active' => !$course->is_active]);
    }

    public function duplicate($id): ?Course
    {
        $originalCourse = Course::find($id);
        if (!$originalCourse) {
            return null;
        }

        $newCourseData = $originalCourse->toArray();
        unset($newCourseData['id'], $newCourseData['created_at'], $newCourseData['updated_at']);
        $newCourseData['name'] = $originalCourse->name . ' (Copy)';
        $newCourseData['code'] = $originalCourse->code . '_copy_' . time();

        return Course::create($newCourseData);
    }

    public function getWithRelations($id): ?Course
    {
        return Course::with([
            'language',
            'level',
            'rateScheme',
            'creator',
            'groups.teacher',
            'groups.students',
            'curriculumTopics',
            'studentAcademicHours.student'
        ])->find($id);
    }

    public function search($query): Collection
    {
        return Course::with(['language', 'level', 'rateScheme'])
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->active()
                    ->get();
    }

    public function getPaginated($perPage = 15): LengthAwarePaginator
    {
        return Course::with(['language', 'level', 'rateScheme', 'creator'])
                    ->active()
                    ->paginate($perPage);
    }
}
