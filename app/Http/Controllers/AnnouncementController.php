<?php

namespace App\Http\Controllers;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Response\BusinessResponse;
use App\Services\AnnouncementService;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller {

    public function __construct(
        private AnnouncementService $obAnnouncementService
    ){}

    public function list(int $userId) :JsonResponse {
        $registers = $this->obAnnouncementService->getListByUser(10, 1, $userId);

        $response = new BusinessResponse(200, $registers);
        return $response->build();
    }

    public function get(int $userId, int $announcementId) :JsonResponse {
        $obAnnouncementDTO = $this->obAnnouncementService->getById($announcementId);

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return $response->build();
    }

    public function create(int $userId, AnnouncementRequest $request) :JsonResponse {
        $requestData = $request->validated();
        $requestData['userId'] = $userId;

        $obAnnouncementDTO = $this->obAnnouncementService->create($requestData);

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return $response->build();
    }

    public function update(int $userId, int $announcementId, AnnouncementRequest $request) :JsonResponse {
        $requestData = $request->validated();
        $requestData['userId'] = $userId;

        $obAnnouncementDTO = $this->obAnnouncementService->edit($announcementId, $requestData);

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return $response->build();
    }

    public function delete(int $announcementId) :JsonResponse {
        $this->obAnnouncementService->remove($announcementId);
        $response = new BusinessResponse(200, 'O anúncio '.$announcementId.' foi removido com sucesso.');
        return $response->build();
    }
}
