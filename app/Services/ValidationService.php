<?php

namespace App\Services;

class ValidationService{
    public function __construct(
        private AnnouncementService $announcementService,
        private UserService         $userService,
        private FormService         $formService,
    ){
    }

    public function validateIfUserExists($userId){
        $this->userService->getById($userId);
    }

    public function validateIfAnnouncementExists($announcementId){
        $this->announcementService->getById($announcementId);
    }

    public function validateIfFormExists($formId){
        $this->formService->getById($formId);
    }
}
