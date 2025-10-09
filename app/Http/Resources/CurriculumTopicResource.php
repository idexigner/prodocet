<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumTopicResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
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
            'order_index' => $this->order_index,
            'is_active' => $this->is_active,
            'status_text' => $this->is_active ? __('common.active') : __('common.inactive'),
            'group_sessions_count' => $this->whenLoaded('groupSessions', function () {
                return $this->groupSessions->count();
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
