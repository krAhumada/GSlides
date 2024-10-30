<?php

namespace GooglePresentation;

use Google\Service\Drive;

class DriveService
{

    private Drive $driveService;

    public function __construct(GoogleClient $googleClient)
    {
        $this->driveService = new Drive($googleClient->getClient());
    }

    public function getDriveService(): Drive
    {
        return $this->driveService;
    }
}
