<?php

namespace App\Services\Interfaces;

interface IService {
    public function getList($limit, $page);
    public function getById($id, $relations);
    public function create($data);
    public function edit($id, $data);
    public function remove($id = null);
}
