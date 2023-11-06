<?php

namespace App\Controller;

use App\services\SearchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: '')]
class HomeController extends AbstractController
{

    public function __construct(
        private SearchApi $searchApi
    ) {}

    #[Route('/', name: 'app_home_index')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }


    #[Route('/get_company/{company_name}', name: 'get_company')]
    public function getCompany(string $company_name): Response
    {
        $company = $this->searchApi->getCompanyFromName($company_name);

        return new JsonResponse($company);
    }}
