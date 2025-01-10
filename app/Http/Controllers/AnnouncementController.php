<?php

namespace App\Http\Controllers;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Response\BusinessResponse;
use App\Services\AnnouncementService;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller {

    public function __construct(private AnnouncementService $obAnnouncementService){}

    public function list() :JsonResponse {
        $registers = $this->obAnnouncementService->getList(10, 1);

        $response = new BusinessResponse(200, $registers);
        return response()->json($response);
    }

    public function get(int $id) :JsonResponse {
        $obAnnouncementDTO = $this->obAnnouncementService->getById($id);

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return response()->json($response);
    }

    public function create(int $announcementId, AnnouncementRequest $request) :JsonResponse {
        $requestData = $request->validated();
        //TODO: Tem que resolver essa parte, tem que colocar o $announcementId no user_id do banco;

        $obAnnouncementDTO = $this->obAnnouncementService->create($requestData);

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return response()->json($response);
    }

    public function update(int $id, AnnouncementRequest $request) :JsonResponse {
        $obAnnouncementDTO = $this->obAnnouncementService->edit($id, $request->validated());

        $response = new BusinessResponse(200, $obAnnouncementDTO);
        return response()->json($response);
    }

    public function delete(int $id) :JsonResponse {
        $this->obAnnouncementService->remove($id);
        $response = new BusinessResponse(200, 'O anúncio '.$id.' foi removido com sucesso.');
        return response()->json($response);
    }
}
