<?php

namespace App\services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SearchApi
{

     private string $SEARCH_API_URL = 'https://recherche-entreprises.api.gouv.fr/search';

    public function __construct(
        private HttpClientInterface $client
    ) {}

    public function getCompanyFromInfo(string $info): mixed
    {
        $response = $this->client->request('GET', "$this->SEARCH_API_URL?q=$info");

        return json_decode($response->getContent());
    }
}