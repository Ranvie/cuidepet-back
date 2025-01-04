<?php

namespace App\DTO\Announcement;

class AnnouncementDTO {

    public int $id;

    public string $type;

    public string $description;

    public string $lastSeenLatitude;

    public string $lastSeenLongitude;

    public string $mainImage;

    public string $address;
    public string $contactEmail;
    public string $contactPhone;

    public bool $timesFavorited;

    public bool $active;

    public bool $status;

    public string $createdAt;

    public string $updatedAt;

}
