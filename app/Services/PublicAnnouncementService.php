<?php

namespace App\Services;

use App\Classes\Filter;
use App\Exceptions\BusinessException;
use App\Http\Enums\AnnouncementTypes;
use App\Models\PublicAnnouncementModel;
use App\Services\Interfaces\IPublicAnnouncementService;

class PublicAnnouncementService implements IPublicAnnouncementService
{

    public function __construct(
        private PublicAnnouncementModel $obPublicAnnouncementModel,
    ){}

    public function getList($limit, $page, $type) {
        $this->validateAnnouncementTypeExists($type);
        return $this->obPublicAnnouncementModel->list($limit, $page, relations: ['user', 'animal'], filters: [new Filter('type', '=', $type)]);
    }

    public function getById($id, $relations = ['user', 'animal.breed', 'animal.specie', 'form', 'announcementMedia']) :object {
        $obAnnouncementDTO = $this->obPublicAnnouncementModel->getById($id, $relations, true);
        
        $this->validateAnnouncementExists($obAnnouncementDTO);
        return $obAnnouncementDTO;
    }

    private function validateAnnouncementTypeExists($type) {
        if(!AnnouncementTypes::tryFrom($type))
            throw new BusinessException('Atualmente, as únicas opções de listagem de anúncios são: perdidos (lost) ou doações (donations).', 404);
    }

    private function validateAnnouncementExists($obAnnouncement) {
        if(!$obAnnouncement)
            throw new BusinessException('O anúncio não foi encontrado', 404); 
    }

}
