<?php

namespace Imperial\Simp\Clients;

use Imperial\Simp\Source;
use Goutte\Client;

abstract class AbstractClient
{
    protected $client;
    protected $crawler;
    protected $sourceModel;

    public function __construct(Source $sourceModel)
    {
        $this->sourceModel = $sourceModel;
        $this->client = new Client();
    }

    public function get()
    {
        $this->crawler = $this->client->request('GET', $this->getUrl());
        $this->sourceModel->retrieve();

        return $this;
    }

    public function getUrl()
    {
        return $this->sourceModel->url;
    }

    abstract public function run();
}
