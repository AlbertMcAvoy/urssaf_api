<?php

namespace App\Controller;

use App\services\SearchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_home')]
class HomeController extends AbstractController
{

    public function __construct(
        private SearchApi $searchApi
    ) {}

    #[Route('/', name: 'app_home_index')]
    public function index(): Response
    {
        // $this->searchApi->getCompanyFromName('decalog');

        return $this->render('home/index.html.twig');
    }
}
