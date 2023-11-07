<?php

namespace App\Controller;

use App\services\FileService;
use App\services\SearchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/search', name: 'search_')]
class SearchApiController extends AbstractController
{

    public function __construct(
        private SearchApi $searchApi
    ) {}

    #[Route('/{company_name}', name: 'get_company')]
    public function getCompanyFromName(string $company_name): Response
    {
        $company = $this->searchApi->getCompanyFromName($company_name);

        return new JsonResponse($company);
    }
}