<?php

namespace App\services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrssafApi {

    private string $URSSAF_API_URL= 'https://mon-entreprise.urssaf.fr/api/v1/evaluate';

    public function __construct(
        private HttpClientInterface $client
    ) {}

    public function evaluateSalary(int $salary): mixed
    {
        $response = $this->client->request('POST', $this->URSSAF_API_URL, [
            'json' => [
                'situation' => [
                    'salarié . contrat . salaire brut' => [
                        'valeur' => $salary,
                        'unité' => '€ / mois',
                    ],
                    'salarié . contrat' => 'CDI',
                ],
                'expressions' => [
                    'salarié . rémunération . net . à payer avant impôt',
                    'salarié . cotisations . salarié',
                    'salarié . coût total employeur',
                ],
            ],
        ]);

        return json_decode($response->getContent(), true);
    }
}
