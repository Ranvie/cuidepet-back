<?php

namespace App\Services\Interfaces;

interface IFormService extends IService {

    public function listFormByUser($userId);

}
