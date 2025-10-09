<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'full_name' => $this->full_name,
            'course' => [
                'id' => $this->course->id,
                'name' => $this->course->name,
                'code' => $this->course->code,
                'full_name' => $this->course->full_name,
                'language' => [
                    'id' => $this->course->language->id,
                    'code' => $this->course->language->code,
                    'name' => $this->course->language->name,
                ],
                'level' => [
                    'id' => $this->course->level->id,
                    'code' => $this->course->level->code,
                    'name' => $this->course->level->name,
                ],
                'rate_scheme' => [
                    'id' => $this->course->rateScheme->id,
                    'letter_code' => $this->course->rateScheme->letter_code,
                    'hourly_rate' => $this->course->rateScheme->hourly_rate,
                    'formatted_rate' => $this->course->rateScheme->formatted_rate,
                ],
                'teaching_hours' => $this->course->teaching_hours,
                'mode' => $this->course->mode,
            ],
            'teacher' => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->full_name,
                'email' => $this->teacher->email,
                'phone' => $this->teacher->phone,
            ],
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'duration_days' => $this->duration_days,
            'classroom' => $this->classroom,
            'virtual_link' => $this->virtual_link,
            'max_students' => $this->max_students,
            'current_students' => $this->current_students,
            'available_spots' => $this->available_spots,
            'is_full' => $this->is_full,
            'status' => $this->status,
            'status_text' => __('groups.status.' . $this->status),
            'can_cancel_classes' => $this->can_cancel_classes,
            'cancellation_hours_advance' => $this->cancellation_hours_advance,
            'is_active' => $this->is_active,
            'students' => $this->whenLoaded('students', function () {
                return $this->students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->full_name,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'academic_hours_purchased' => $student->pivot->academic_hours_purchased,
                        'academic_hours_used' => $student->pivot->academic_hours_used,
                        'enrollment_date' => $student->pivot->enrollment_date,
                        'status' => $student->pivot->status,
                        'final_grade' => $student->pivot->final_grade,
                    ];
                });
            }),
            'sessions' => $this->whenLoaded('sessions', function () {
                return $this->sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'session_date' => $session->session_date?->format('Y-m-d'),
                        'start_time' => $session->start_time?->format('H:i'),
                        'end_time' => $session->end_time?->format('H:i'),
                        'duration_minutes' => $session->duration_minutes,
                        'teaching_hours' => $session->teaching_hours,
                        'session_title' => $session->session_title,
                        'status' => $session->status,
                        'attendance_taken' => $session->attendance_taken,
                        'notes' => $session->notes,
                    ];
                });
            }),
            'sessions_count' => $this->whenLoaded('sessions', function () {
                return $this->sessions->count();
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
