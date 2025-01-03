<?php

namespace App\Http\Controllers;
use App\Http\Requests\UserRequest;
use App\Http\Response\BusinessResponse;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function list(){
        $users = (new UserModel())->getAll();

        $response = new BusinessResponse(200, $users);
        return response()->json($response);
    }

    public function get(int $userId){
        $user = (new UserModel())->getById($userId);

        $response = new BusinessResponse(200, $user);
        return response()->json($response);
    }

    public function create(UserRequest $request){
        $requestData = $request->validated();
        $user = new UserModel();

        $response = new BusinessResponse(201, $user->create($requestData));
        return response()->json($response, 201);
    }

    public function update(int $userId, UserRequest $request){
        $requestData = $request->validated();
        $user = new UserModel();

        $response = new BusinessResponse(200, $user->edit($userId, $requestData));
        return response()->json($response);
    }

    public function delete(int $userId){
        $user = new UserModel();
        $user->remove($userId);

        $response = new BusinessResponse(200, "O usuário {$userId} foi deletado com sucesso.");
        return response()->json($response);
    }
}
