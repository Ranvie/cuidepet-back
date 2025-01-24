<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\PublicAnnouncementModel;
use App\Services\Interfaces\IPublicAnnouncementService;

class PublicAnnouncementService implements IPublicAnnouncementService
{

    public function __construct(
        private PublicAnnouncementModel $obPublicAnnouncementModel,
    ){}

    public function getList($limit, $page, $type) {
        if($type != 'lost' && $type != 'donation'){
            throw new BusinessException('As únicas opções de listagem de anúncios são: perdidos ou doações, atualmente.', 404);
        }

        return $this->obPublicAnnouncementModel->list($limit, $page, relations: ['user', 'animal'], type: $type);
    }

    public function getById($id, $relations = ['animal.breed', 'animal.specie', 'form', 'announcementMedia']) :object {
        $obAnnouncementDTO = $this->obPublicAnnouncementModel->getById($id, $relations, true);
        if(!$obAnnouncementDTO){ throw new BusinessException('O anúncio não foi encontrado', 404); }

        return $obAnnouncementDTO;
    }

}
