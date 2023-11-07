<?php

namespace App\Controller;

use App\services\SearchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/company', name: '')]
class CompanyController extends AbstractController
{

    public function __construct(
        private SearchApi $searchApi
    ) {}

    #[Route('/{siren}', name: 'company_details')]
    public function companyDetails(string $siren): Response
    {
        $company = $this->searchApi->getCompanyFromInfo($siren)->results[0];

        return $this->render('home/company_details.html.twig', ["company" => $company]);
    }

}