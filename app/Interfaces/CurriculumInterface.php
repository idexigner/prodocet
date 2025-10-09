<?php

namespace App\Interfaces;

interface CurriculumInterface
{
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByLanguageLevel($languageId, $levelId);
    public function getByLanguage($languageId);
    public function getByLevel($levelId);
    public function reorder(array $data);
    public function assignToCourse($courseId, array $topicIds);
    public function getAvailableTopics($groupId);
    public function getUsedTopics($groupId);
    public function getActive();
    public function search($query);
    public function getPaginated($perPage = 15);
}
