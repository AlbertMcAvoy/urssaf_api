<?php

namespace App\services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrssafApi {

    private string $URSSAF_API_URL= 'https://mon-entreprise.urssaf.fr/api/v1/evaluate';

    public function __construct(
        private HttpClientInterface $client
    ) {}

    public function evaluateOpenEndedContracts(int $salary): mixed
    {
        $data = [
            'situation' => [
                'salarié . contrat . salaire brut' => [
                    'valeur' => $salary,
                    'unité' => '€ / mois'
                ],
                'salarié . contrat' => '\'CDI\''
            ],
            'expressions' => [
                'salarié . rémunération . net . à payer avant impôt',
                'salarié . cotisations . salarié',
                'salarié . coût total employeur'
            ],
        ];

        $jsonData = json_encode($data);

        return $this->evaluateContract($jsonData);
    }
    public function evaluateInternship(): mixed
    {
        $data = [
            'situation' => [
                'salarié . contrat' => '\'stage\''
            ],
            'expressions' => [
                'salarié . contrat . stage . gratification minimale'
            ],
        ];

        $jsonData = json_encode($data);

        return $this->evaluateContract($jsonData);
    }
    public function evaluateWorkStudy(int $salary): mixed
    {
        $data = [
            'situation' => [
                'salarié . contrat . salaire brut' => [
                    'valeur' => $salary,
                    'unité' => '€ / mois'
                ],
                'salarié . contrat' => '\'apprentissage\'',
            ],
            'expressions' => [
                'salarié . rémunération . net . à payer avant impôt',
                'salarié . cotisations . salarié',
                'salarié . coût total employeur'
            ],
        ];

        $jsonData = json_encode($data);

        return $this->evaluateContract($jsonData);
    }
    public function evaluateFixedTermContract(int $salary): mixed
    {
        $data = [
            'situation' => [
                'salarié . contrat . salaire brut' => [
                    'valeur' => $salary,
                    'unité' => '€ / mois'
                ],
                'salarié . contrat' => '\'CDD\'',
            ],
            'expressions' => [
                'salarié . rémunération . net . à payer avant impôt',
                'salarié . cotisations . salarié',
                'salarié . coût total employeur',
                'salarié . rémunération . indemnités CDD . fin de contrat'
            ],
        ];

        $jsonData = json_encode($data);

        return $this->evaluateContract($jsonData);
    }

    public function evaluateContract(mixed $jsonData): mixed
    {
        $response = $this->client->request('POST', $this->URSSAF_API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $jsonData,
        ]);

        return json_decode($response->getContent(), true);
    }
}
