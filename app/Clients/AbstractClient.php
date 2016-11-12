<?php

namespace Imperial\Simp\Clients;

use Goutte\Client;

abstract class AbstractClient
{
    protected $client;
    protected $crawler;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function get()
    {
        $this->crawler = $this->client->request('GET', $this->getUrl());

        return $this;
    }

    abstract public function getUrl();

    abstract public function run();
}
