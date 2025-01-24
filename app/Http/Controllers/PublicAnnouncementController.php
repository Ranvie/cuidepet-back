<?php

namespace App\Http\Controllers;
use App\Http\Response\BusinessResponse;
use App\Services\PublicAnnouncementService;
use Illuminate\Http\JsonResponse;

class PublicAnnouncementController extends Controller {

    public function __construct(
        private PublicAnnouncementService $obPublicAnnouncementService,
    ){}

    public function list(string $announcementType) :JsonResponse {
        $registers = $this->obPublicAnnouncementService->getList(10, 1, $announcementType);

        $response = new BusinessResponse(200, $registers);
        return $response->build();
    }

    public function get(int $announcementId) :JsonResponse {
        $obAnnouncementDTO = $this->obPublicAnnouncementService->getById($announcementId);

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return $response->build();
    }

}
