<?php

namespace App\Services;

use App\Models\AnnouncementModel;
use App\Models\BreedSpecieModel;
use App\Services\Interfaces\IAnnouncementService;

class BreedSpecieService implements IAnnouncementService
{

    public function __construct(
        private BreedSpecieModel $breedSpecieModel
    ){}

    public function getList($limit, $page) {
        return $this->breedSpecieModel->list($limit, $page, relations: ['breed']);
    }

    public function getById($id, $relations = []) {
        return null;
    }

    public function create($data) {
        return null;
    }

    public function edit($id, $data) {
        return null;
    }

    public function remove($id = null) {
        return null;
    }
}
