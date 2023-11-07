<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: '')]
class HomeController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/company/{siren}', name: 'company_details')]
    public function companyDetails(string $siren): Response
    {

        return $this->render('home/company_details.html.twig');
    }
}
