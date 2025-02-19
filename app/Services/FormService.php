<?php

namespace App\Services;

use App\Classes\Filter;
use App\Models\FormModel;
use App\Services\Interfaces\IFormService;

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
        $data['url'] = 'Url muito legal, me altera depois kkkkjk';
        $data['payload'] = 'Não há nada aqui, além de lágrimas, PHP';
        $data['title']   = 'Um título muito legal :)';

        $this->formModel->create($data);
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
