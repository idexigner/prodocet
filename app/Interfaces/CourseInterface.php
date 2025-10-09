<?php

namespace App\Interfaces;

interface CourseInterface
{
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function getByLanguage($languageId);
    public function getByLevel($levelId);
    public function getByRateScheme($rateSchemeId);
    public function getActive();
    public function toggleStatus($id);
    public function duplicate($id);
    public function getWithRelations($id);
    public function search($query);
    public function getPaginated($perPage = 15);
}
