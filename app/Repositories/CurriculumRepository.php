<?php

namespace App\Repositories;

use App\Interfaces\CurriculumInterface;
use App\Models\CurriculumTopic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CurriculumRepository implements CurriculumInterface
{
    public function getAll(): Collection
    {
        return CurriculumTopic::with(['language', 'level'])->get();
    }

    public function getById($id): ?CurriculumTopic
    {
        return CurriculumTopic::with(['language', 'level'])->find($id);
    }

    public function create(array $data): CurriculumTopic
    {
        return CurriculumTopic::create($data);
    }

    public function update($id, array $data): bool
    {
        $topic = CurriculumTopic::find($id);
        if (!$topic) {
            return false;
        }
        return $topic->update($data);
    }

    public function delete($id): bool
    {
        $topic = CurriculumTopic::find($id);
        if (!$topic) {
            return false;
        }
        return $topic->delete();
    }

    public function getByLanguageLevel($languageId, $levelId): Collection
    {
        return CurriculumTopic::with(['language', 'level'])
                             ->byLanguageLevel($languageId, $levelId)
                             ->active()
                             ->ordered()
                             ->get();
    }

    public function getByLanguage($languageId): Collection
    {
        return CurriculumTopic::with(['language', 'level'])
                             ->where('language_id', $languageId)
                             ->active()
                             ->ordered()
                             ->get();
    }

    public function getByLevel($levelId): Collection
    {
        return CurriculumTopic::with(['language', 'level'])
                             ->where('level_id', $levelId)
                             ->active()
                             ->ordered()
                             ->get();
    }

    public function reorder(array $data): bool
    {
        try {
            foreach ($data as $item) {
                CurriculumTopic::where('id', $item['id'])
                              ->update(['order_index' => $item['order_index']]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function assignToCourse($courseId, array $topicIds): bool
    {
        // This would typically involve a pivot table or course_topic relationship
        // For now, we'll just return true as the topics are already linked via language/level
        return true;
    }

    public function getAvailableTopics($groupId): Collection
    {
        // Get topics that haven't been used in this group's sessions
        $usedTopicIds = \App\Models\GroupSession::where('group_id', $groupId)
                                                ->whereNotNull('topic_id')
                                                ->pluck('topic_id')
                                                ->toArray();

        $group = \App\Models\Group::with('course')->find($groupId);
        if (!$group) {
            return collect();
        }

        return CurriculumTopic::with(['language', 'level'])
                             ->byLanguageLevel($group->course->language_id, $group->course->level_id)
                             ->whereNotIn('id', $usedTopicIds)
                             ->active()
                             ->ordered()
                             ->get();
    }

    public function getUsedTopics($groupId): Collection
    {
        $usedTopicIds = \App\Models\GroupSession::where('group_id', $groupId)
                                                ->whereNotNull('topic_id')
                                                ->pluck('topic_id')
                                                ->toArray();

        return CurriculumTopic::with(['language', 'level'])
                             ->whereIn('id', $usedTopicIds)
                             ->active()
                             ->ordered()
                             ->get();
    }

    public function getActive(): Collection
    {
        return CurriculumTopic::with(['language', 'level'])
                             ->active()
                             ->ordered()
                             ->get();
    }

    public function search($query): Collection
    {
        return CurriculumTopic::with(['language', 'level'])
                             ->where('title', 'like', "%{$query}%")
                             ->orWhere('description', 'like', "%{$query}%")
                             ->active()
                             ->get();
    }

    public function getPaginated($perPage = 15): LengthAwarePaginator
    {
        return CurriculumTopic::with(['language', 'level'])
                             ->active()
                             ->ordered()
                             ->paginate($perPage);
    }
}
