<?php

namespace App\DTO\Announcement;

class AnnouncementDTO {
    public $id;
    public $userId;
    public $formId;
    public $type;
    public $description;
    public $lastSeenLatitude;
    public $lastSeenLongitude;
    public $mainImage;
    public $address;
    public $contactEmail;
    public $contactPhone;
    public $timesFavorited;
    public $active;
    public $status;
    public $createdAt;
    public $updatedAt;
}
