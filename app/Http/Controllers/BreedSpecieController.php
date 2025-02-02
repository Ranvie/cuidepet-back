<?php

namespace App\Http\Controllers;
use App\Http\Response\BusinessResponse;
use App\Services\BreedSpecieService;
use Illuminate\Http\JsonResponse;

class BreedSpecieController extends Controller {

    public function __construct(
        private BreedSpecieService $breedSpecieService,
    ){}

    public function list() :JsonResponse {
        $registers = $this->breedSpecieService->getList(50, 1);

        $response = new BusinessResponse(200, $registers);
        return $response->build();
    }
}
