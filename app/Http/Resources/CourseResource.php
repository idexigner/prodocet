<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'language' => [
                'id' => $this->language->id,
                'code' => $this->language->code,
                'name' => $this->language->name,
                'native_name' => $this->language->native_name,
                'display_name' => $this->language->display_name,
            ],
            'level' => [
                'id' => $this->level->id,
                'code' => $this->level->code,
                'name' => $this->level->name,
                'description' => $this->level->description,
                'order_index' => $this->level->order_index,
            ],
            'rate_scheme' => [
                'id' => $this->rateScheme->id,
                'letter_code' => $this->rateScheme->letter_code,
                'hourly_rate' => $this->rateScheme->hourly_rate,
                'formatted_rate' => $this->rateScheme->formatted_rate,
                'description' => $this->rateScheme->description,
            ],
            'teaching_hours' => $this->teaching_hours,
            'regular_hours' => $this->regular_hours,
            'total_classes' => $this->total_classes,
            'mode' => $this->mode,
            'mode_text' => __('courses.modes.' . $this->mode),
            'description' => $this->description,
            'is_curriculum_required' => $this->is_curriculum_required,
            'max_students_per_group' => $this->max_students_per_group,
            'is_active' => $this->is_active,
            'status_text' => $this->is_active ? __('common.active') : __('common.inactive'),
            'creator' => [
                'id' => $this->creator->id,
                'name' => $this->creator->full_name,
                'email' => $this->creator->email,
            ],
            'groups_count' => $this->whenLoaded('groups', function () {
                return $this->groups->count();
            }),
            'curriculum_topics_count' => $this->whenLoaded('curriculumTopics', function () {
                return $this->curriculumTopics->count();
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
