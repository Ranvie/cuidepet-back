<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/debug', function (Request $request) {

    $obj = new \App\Models\UserModel();

    $data = new \App\DTO\User\UserDatabase();
    $data->id = 1;
    $data->username = "joaosilva";
    $data->email = "joaosilva@gmail.com";
    $data->secondaryEmail = "joaosilva@outlook.com";
    $data->password = password_hash("senha123", PASSWORD_DEFAULT); // Utilize hash para segurança
    $data->imageProfile = "https://example.com/images/joaosilva.jpg"; // URL da imagem do perfil
    $data->mainPhone = "+55 11 91234-5678";
    $data->secondaryPhone = "+55 11 99876-5432";
    $data->active = true; // Usuário ativo
    $data->createdAt = date("Y-m-d H:i:s"); // Data de criação
    $data->updatedAt = date("Y-m-d H:i:s"); // Última atualização

    $resp = $obj->create($data);

    return response()->json($resp);
});
