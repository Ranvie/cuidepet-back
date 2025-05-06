<?php

namespace App\Services;

use App\Models\FormModel;

class FormService implements Interfaces\IFormService
{

    public function __construct(
        private FormModel $formModel
    ){}

    public function getList($limit, $page)
    {
        // TODO: Implement getList() method.
    }

    public function listFormByUser($userId)
    {
        return $this->formModel->listFormByUser($userId);
    }

    public function getById($id, $relations = [])
    {
        return $this->formModel-$this->getById($id, $relations);
    }

    public function getUserForm($userId, $formId){
        return $this->formModel->getUserForm($userId, $formId);
    }

    public function create($data)
    {
        return $this->formModel->create($data);
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
