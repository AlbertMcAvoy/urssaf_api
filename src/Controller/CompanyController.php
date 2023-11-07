<?php

namespace App\Controller;

use App\Model\Company;
use App\services\FileService;
use App\services\SearchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/company', name: '')]
class CompanyController extends AbstractController
{

    public function __construct(
        private SearchApi $searchApi,
        private FileService $fileService,
        private SerializerInterface $serializer
    ) {}

    #[Route('/{siren}', name: 'company_details')]
    public function companyDetails(string $siren): Response
    {
        $company = Company::toCompany($this->searchApi->getCompanyFromInfo($siren)->results[0]);

        $jsonContent = $this->serializer->serialize($company, 'json');
        $csvContent = $this->serializer->serialize($company, 'csv', ['csv_delimiter' => ';']);

        $this->fileService->createAFileWithContent("$siren.json", $jsonContent);
        $this->fileService->createAFileWithContent("$siren.csv", $csvContent);

        return $this->render('home/company_details.html.twig', ["company" => $company]);
    }

}