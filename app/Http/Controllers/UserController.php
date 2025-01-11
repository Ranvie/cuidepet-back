<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use App\Services\UserService;

class UserController extends Controller {

    public function __construct(private UserService $userService){}

    public function list(){
        $users = $this->userService->getList(10, 1);

        $response = new BusinessResponse(200, $users);
        return $response->build();
    }

    public function get(int $userId){
        $user = $this->userService->getById($userId);

        $response = new BusinessResponse(200, $user);
        return $response->build();
    }

    public function create(UserRequest $request){
        $requestData = $request->validated();
        $user = $this->userService->create($requestData);

        $response = new BusinessResponse(201, $user);
        return response()->json($response, 201);
    }

    public function update(int $userId, UserRequest $request){
        $requestData = $request->validated();
        $user = $this->userService->edit($userId, $requestData);

        $response = new BusinessResponse(200, $user);
        return $response->build();
    }

    public function delete(int $userId){
        $this->userService->remove($userId);

        $response = new BusinessResponse(200, "O usuário {$userId} foi deletado com sucesso.");
        return $response->build();
    }

    public function inactivate(int $userId){
        $this->userService->inactivate($userId);

        $response = new BusinessResponse(200, "O usuário {$userId} foi deletado com sucesso.");
        return $response->build();
    }
}
