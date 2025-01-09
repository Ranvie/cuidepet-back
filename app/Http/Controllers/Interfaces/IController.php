<?php

namespace App\Http\Controllers\Interfaces;
use App\Http\Requests\UserRequest;

interface IController {
    public function list();
    public function paginate();
    public function get(int $userId);
    public function create(UserRequest $request);
    public function update(int $userId, UserRequest $request);
    public function delete(int $userId);
}
