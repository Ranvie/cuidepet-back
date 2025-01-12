<?php

namespace App\Services;
use App\Models\AnnouncementMediaModel;

class AnnouncementMediaService implements Interfaces\IAnnouncementMediaService{
    public function __construct(
        private AnnouncementMediaModel $announcementMediaModel
    ){}

    public function getList($limit, $page) :array {
        return $this->announcementMediaModel->list($limit, $page);
    }

    public function getById($id, $relations = [], $parse = true) :object {
        return $this->announcementMediaModel->getById($id, $relations, $parse);
    }

    public function create($data) :object {
        return $this->announcementMediaModel->create($data);
    }

    public function edit($id, $data) :object {
        return $this->announcementMediaModel->edit($id, $data);
    }

    public function remove($id = null) :bool {
        return $this->announcementMediaModel->remove($id);
    }

    public function newInstance() :AnnouncementMediaModel {
        return $this->announcementMediaModel->newModel();
    }
}
