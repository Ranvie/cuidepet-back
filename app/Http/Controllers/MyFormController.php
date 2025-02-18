<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use App\Services\FormService;
use Illuminate\Http\Request;

class MyFormController extends Controller
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

    public function create(UserRequest $request)
    {
        // TODO: Implement create() method.
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
