<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use App\Services\FormService;

class FormController extends Controller
{

    public function __construct(
        private FormService $obFormService
    ){}

    //
    public function list(int $userId)
    {
        $registers = $this->obFormService->listFormByUser($userId);

        $response = new BusinessResponse(200, $registers);
        return $response->build();
    }

    public function get(int $userId)
    {
        // TODO: Implement get() method.
    }

    public function create(int $userId, UserFormRequest $request)
    {
        $obFormRequest = array_merge($request->validated(), ['userId' => $userId]);
        $registers = $this->obFormService->create($obFormRequest);
        return new BusinessResponse(200, $registers)->build();
    }

    public function update(int $userId, UserRequest $request)
    {
        // TODO: Implement update() method.
    }

    public function delete(int $userId)
    {
        // TODO: Implement delete() method.
    }
}
