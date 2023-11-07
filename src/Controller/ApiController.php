<?php

namespace App\Controller;

use App\Model\Company;
use App\services\FileService;
use App\services\SearchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{

    private array $allowedFormat = [
        'application/json',
        'application/csv'
    ];

    public function __construct(
        private SearchApi $searchApi,
        private FileService $fileService,
        private SerializerInterface $serializer
    ) {}

    #[Route('/search/{company_name}', name: 'search_company')]
    public function getCompanyFromName(string $company_name): JsonResponse
    {
        $companies = Company::toCompanies($this->searchApi->getCompanyFromInfo($company_name)->results);

        return $this->json($companies);
    }

    #[Route('/companies', name: 'get_companies', methods: 'GET')]
    public function getCompanies(Request $request): Response {

        $contentType = $request->headers->get('Content-type');

        if (!$this->formatAllowed($contentType)) throw new NotAcceptableHttpException('Le format n\'est pas autorisé');

        $fileCompanies =  $this->fileService->readfiles('*.json');

        $companies = $this->getCompaniesInformations($fileCompanies, $contentType);

        return $this->json($companies);
    }

    #[Route('/companies/{siren}', name: 'get_company', methods: 'GET')]
    public function getCompany(Request $request, string $siren): Response {

        $contentType = $request->headers->get('Content-type');

        if (!$this->formatAllowed($contentType)) throw new NotAcceptableHttpException('Le format n\'est pas autorisé');

        $files = $this->fileService->readfiles("$siren.json");

        if (!$files->hasResults()) {
            throw new NotFoundHttpException('Aucune entreprise n\'a trouvée');
        }

        $company = $this->getCompaniesInformations($files, $contentType)[0];

        return $this->json($company);
    }

    /**
     * @param Finder $fileCompanies
     * @param string|null $contentType
     * @return array
     */
    public function getCompaniesInformations(Finder $fileCompanies, ?string $contentType): array
    {
        $companies = [];

        foreach ($fileCompanies as $file) {
            switch($contentType) {
                case 'application/csv':
                    $object = $this->serializer->deserialize($file->getContents(), Company::class, 'json');
                    $companies[] = $this->serializer->serialize($object, 'csv', ['csv_delimiter' => ';']);
                    break;
                case 'application/json':
                default:
                    $companies[] = Company::toCompany(json_decode($file->getContents()));
                    break;
            }
        }

        return $companies;
    }

    private function formatAllowed(?string $contentType): bool {

        return in_array($contentType, $this->allowedFormat);
    }
}