<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\AnnouncementModel;
use App\Services\Interfaces\IAnnouncementService;

class AnnouncementService implements IAnnouncementService
{

    public function __construct(
        private AnnouncementModel $obAnnouncementModel,
        private UserService $userService
    ){}

    public function getList($limit, $page) :array {
        return $this->obAnnouncementModel->list($limit, $page);
    }

    public function getById($id, $relations = ['animal', 'form']) :object {
        $obAnnouncementDTO = $this->obAnnouncementModel->getById($id, $relations);
        if(!$obAnnouncementDTO){ throw new BusinessException('O anúncio não foi encontrado', 404); }

        return $obAnnouncementDTO;
    }

    public function create($data) :object {
        $this->validateIfUserExists($data['userId']);

        $announcementModel = $this->obAnnouncementModel->create($data, [], false);
        $announcementId = $announcementModel->getOriginal()['id'];

        $animalData = $data['animal'];
        $animalData['announcementId'] = $announcementId;
        $announcementModel->animal()->getModel()->create($animalData, [], false);

        $announcementModel->form()->associate($data['formId']);
        return $announcementModel->getById($announcementId, ['animal', 'form']);
    }

    private function validateIfUserExists($userId){
        $this->userService->getById($userId);
    }

    public function edit($id, $data) :object {
        $obAnnouncementDTO = $this->obAnnouncementModel->edit($id, $data);
        if(is_null($obAnnouncementDTO)){ throw new BusinessException('O anúncio não foi encontrado', 404); }

        return $obAnnouncementDTO;
    }

    public function remove($id = null) :bool {
        return $this->obAnnouncementModel->remove($id);
    }
}
