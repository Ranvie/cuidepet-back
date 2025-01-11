<?php

namespace App\Services;

use App\Services\Interfaces\IFormService;

class FormService implements Interfaces\IFormService
{

    public function getList($limit, $page)
    {
        // TODO: Implement getList() method.
    }

    public function getById($id, $relations = [])
    {
        parent::getById($id, $relations);
    }

    public function create($data)
    {
        // TODO: Implement create() method.
    }

    public function edit($id, $data)
    {
        // TODO: Implement edit() method.
    }

    public function remove($id = null)
    {
        // TODO: Implement remove() method.
    }
}
