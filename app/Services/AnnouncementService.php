<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\AnnouncementModel;
use App\Services\Interfaces\IAnnouncementService;

class AnnouncementService implements IAnnouncementService
{

    public function __construct(
        private AnnouncementModel $obAnnouncementModel,
        private UserService $userService,
        private AnnouncementMediaService $announcementMediaService
    ){}

    public function getList($limit, $page) {
        return $this->obAnnouncementModel->list($limit, $page);
    }

    public function getById($id, $relations = ['animal.breed', 'animal.specie', 'form', 'announcementMedia']) :object {
        $obAnnouncementDTO = $this->obAnnouncementModel->getById($id, $relations, true);
        if(!$obAnnouncementDTO){ throw new BusinessException('O anúncio não foi encontrado', 404); }

        //dd($obAnnouncementDTO);

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

        $announcementMediaData = $data['announcementMedia'];
        foreach ($announcementMediaData as $announcementMedia) {
            $announcementMedia['announcementId'] = $announcementId;
            $this->announcementMediaService->newInstance()->create($announcementMedia);
        }

        return $announcementModel->getById($announcementId, ['animal', 'form', 'announcementMedia']);
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
