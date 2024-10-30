<?php

namespace GooglePresentation;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Slides;

class GoogleClient
{

    private Client $client;

    public function __construct(string $credentials)
    {

        $this->client = new Client();
        $this->client->setAuthConfig($credentials);
        $this->client->addScope([Slides::PRESENTATIONS, Drive::DRIVE]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
