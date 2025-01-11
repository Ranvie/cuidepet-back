<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\AnnouncementModel;
use App\Services\Interfaces\IAnnouncementService;

class AnnouncementService implements IAnnouncementService
{

    public function __construct(
        private AnnouncementModel $obAnnouncementModel,
        private ValidationService $validationService
    ){}

    public function getList($limit, $page) :array {
        return $this->obAnnouncementModel->list($limit, $page);
    }

    public function getById($id, $relations = []) :object {
        $obAnnouncementDTO = $this->obAnnouncementModel->getById($id, $relations);
        if(!$obAnnouncementDTO){ throw new BusinessException('O anúncio não foi encontrado', 404); }

        return $obAnnouncementDTO;
    }

    public function create($data) :object {
        $this->validationService->validateIfUserExists($data['userId']);
        return $this->obAnnouncementModel->create($data);
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
