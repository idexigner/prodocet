<?php

namespace App\Interfaces;

interface GroupInterface
{
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function getByTeacher($teacherId);
    public function getByCourse($courseId);
    public function getActive();
    public function getByStatus($status);
    public function enrollStudent($groupId, $studentId, array $data);
    public function removeStudent($groupId, $studentId);
    public function transferStudent($groupId, $studentId, $newGroupId);
    public function getGroupStudents($groupId);
    public function updateStudentStatus($groupId, $studentId, $status);
    public function createSessions($groupId, array $sessions);
    public function updateSession($sessionId, array $data);
    public function cancelSession($sessionId);
    public function rescheduleSession($sessionId, array $data);
    public function getGroupSessions($groupId);
    public function getUpcomingSessions();
    public function consumeHours($studentId, $sessionId, $hoursUsed);
    public function getStudentHours($studentId);
    public function transferHours($studentId, $fromGroupId, $toGroupId, $hours);
    public function findCompatibleGroups($studentId, $courseId, $preferredSchedule);
    public function getWithRelations($id);
    public function search($query);
    public function getPaginated($perPage = 15);
}
