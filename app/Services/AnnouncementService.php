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
        private AnnouncementMediaService $announcementMediaService,
        private FormService $formService
    ){}

    public function getList($limit, $page) {
        return $this->obAnnouncementModel->list($limit, $page);
    }

    public function getById($id, $relations = ['animal.breed', 'animal.specie', 'form', 'announcementMedia']) :object {
        $obAnnouncementDTO = $this->obAnnouncementModel->getById($id, $relations, true);
        if(!$obAnnouncementDTO){ throw new BusinessException('O anúncio não foi encontrado', 404); }

        return $obAnnouncementDTO;
    }

    public function getUserAnnouncement($userId, $announcementId){
        return $this->obAnnouncementModel->getUserAnnouncement($userId, $announcementId);
    }

    public function create($data) :object {
        $this->validateIfUserExists($data['userId']);
        $this->validateIfFormBelongsToUser($data['userId'], $data['formId']);

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

        return $announcementModel->getById($announcementId, ['animal.breed', 'animal.specie', 'form', 'announcementMedia']);
    }

    private function validateIfUserExists($userId){
        $this->userService->getById($userId);
    }

    private function validateIfFormBelongsToUser($userId, $formId){
        $userForm = $this->formService->getUserForm($userId, $formId);

        if(!$userForm){
            throw new BusinessException('O usuário não possui o formulário requisitado.', 404);
        }
    }

    public function edit($id, $data) {
        $this->validateIfUserExists($data['userId']);
        $this->validateIfAnnouncementBelongsToUser($data['userId'], $id);
        if(isset($data['formId'])) $this->validateIfFormBelongsToUser($data['userId'], $data['formId']);

        $announcementModel = $this->obAnnouncementModel->edit($id, $data, true, false);

        if(isset($data['animal'])){
            $animalData = $data['animal'];
            $announcementModel->animal()->getModel()->edit($id, $animalData);
        }

        if(isset($data['formId'])) $announcementModel->form()->associate($data['formId']);

        if(isset($data['announcementMedia'])){
            $announcementMediaData = $data['announcementMedia'];
            foreach ($announcementMediaData as $announcementMedia) {
                $announcementMedia['announcementId'] = $id;
                $this->changeMediaData($announcementMedia);
            }
        }

        return $announcementModel->getById($id, ['animal.breed', 'animal.specie', 'form', 'announcementMedia']);
    }

    private function validateIfAnnouncementBelongsToUser($userId, $announcementId){
        $announcement = $this->getUserAnnouncement($userId, $announcementId);

        if(!$announcement){
            throw new BusinessException('O anúncio requisitado não pertence ao usuário', 404);
        }
    }

    private function changeMediaData($announcementMedia){
        $mediaId = $announcementMedia['id'];
        $option = $announcementMedia['action'];

        switch ($option) {
            case 'UPD':
                $this->announcementMediaService->newInstance()->edit($mediaId, $announcementMedia);
                break;
            case 'DEL':
                $this->announcementMediaService->remove($mediaId);
                break;
            case 'ADD':
                $this->announcementMediaService->newInstance()->create($announcementMedia);
                break;
        }
    }

    public function remove($id = null) :bool {
        return $this->obAnnouncementModel->remove($id);
    }
}
