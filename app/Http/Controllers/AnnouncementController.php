<?php

namespace App\Http\Controllers;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Response\BusinessResponse;
use App\Services\AnnouncementService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller {

    public function __construct(
        private AnnouncementService $obAnnouncementService,
        private UserService $userService
    ){}

    public function list() :JsonResponse {
        $registers = $this->obAnnouncementService->getList(10, 1);

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
        $obAnnouncementDTO = $this->obAnnouncementService->edit($announcementId, $request->validated());

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return $response->build();
    }

    public function delete(int $announcementId) :JsonResponse {
        $this->obAnnouncementService->remove($announcementId);
        $response = new BusinessResponse(200, 'O anúncio '.$announcementId.' foi removido com sucesso.');
        return $response->build();
    }
}
