<?php

namespace App\Services\Interfaces;

interface IPublicAnnouncementService {
    public function getList($limit, $page, $type);
    public function getById($id, $relations);
}
