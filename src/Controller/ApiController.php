<?php

namespace App\Controller;

use App\Model\Company;
use App\services\FileService;
use App\services\SearchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{

    private array $ALLOWED_FORMAT = [
        'application/json',
        'application/csv'
    ];

    public function __construct(
        private SearchApi $searchApi,
        private FileService $fileService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
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
        $this->verifyFormatAllowed($contentType);

        $fileCompanies =  $this->fileService->readFiles('*.json');

        $companies = $this->getCompaniesInformations($fileCompanies, $contentType);

        return $this->json($companies);
    }

    #[Route('/companies', name: 'create_companies', methods: 'POST')]
    public function createCompanies(Request $request): Response {

        $contentType = $request->headers->get('Content-type');
        $this->verifyFormatAllowed($contentType);

        $newCompany = $request->getContent();
        $newCompany = $this->serializer->deserialize($newCompany, Company::class, 'json');
        $this->verifyForm($newCompany);

        $jsonContent = $this->serializer->serialize($newCompany, 'json');
        $siren = $newCompany->getSiren();
        $files = $this->fileService->readFiles("$siren.json");

        if ($files->hasResults()) {
            throw new ConflictHttpException('L\'entreprise existe déjà');
        }

        $newFiles = $this->fileService->createFileWithContent("$siren.json", $jsonContent);
        $company = $this->getCompaniesInformations($newFiles, $contentType)[0];
        return $this->json($company, 201);
    }

    #[Route('/companies/{siren}', name: 'get_company', methods: 'GET')]
    public function getCompany(Request $request, string $siren): Response {

        $contentType = $request->headers->get('Content-type');
        $this->verifyFormatAllowed($contentType);

        $files = $this->fileService->readFiles("$siren.json");
        if (!$files->hasResults()) {
            throw new NotFoundHttpException('Aucune entreprise n\'a trouvée');
        }

        $company = $this->getCompaniesInformations($files, $contentType)[0];

        return $this->json($company);
    }

    #[Route('/companies/{siren}', name: 'update_company', methods: 'PATCH')]
    public function updatePartialCompany(Request $request, string $siren): Response {
        $this->verifyAuthentication($request);

        $contentType = $request->headers->get('Content-type');
        $this->verifyFormatAllowed($contentType);

        $files = $this->fileService->readFiles("$siren.json");
        if (!$files->hasResults()) {
            throw new NotFoundHttpException('Aucune entreprise n\'a trouvée');
        }

        $company = $this->getCompaniesInformations($files, 'application/json')[0];
        $object = json_decode($request->getContent());

        $this->updateCompany($siren, $company, $object);

        return $this->json($company);
    }

    #[Route('/companies/{siren}', name: 'delete_company', methods: 'DELETE')]
    public function deleteCompany(Request $request, string $siren): Response {
        $this->verifyAuthentication($request);

        $contentType = $request->headers->get('Content-type');
        $this->verifyFormatAllowed($contentType);

        $files = $this->fileService->readFiles("$siren.json");
        if (!$files->hasResults()) {
            throw new NotFoundHttpException('Aucune entreprise n\'a trouvée');
        }

        $company = $this->getCompaniesInformations($files, $contentType)[0];
        $this->fileService->deleteFile("$siren.json");

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
            $object = json_decode($file->getContents());
            $companies[] = match ($contentType) {
                'application/csv' => $this->serializer->serialize($object, 'csv', ['csv_delimiter' => ';']),
                default => Company::toCompany($object),
            };
        }

        return $companies;
    }

    /**
     * @param mixed $newCompany
     * @return void
     */
    public function verifyForm(mixed $newCompany): void
    {
        $errors = $this->validator->validate($newCompany);

        if (count($errors) > 0) {

            $errorsString = "";

            foreach ($errors as $error) {
                $errorMsg = $error->getMessage();
                $errorProperty = $error->getPropertyPath();
                $errorsString .= "$errorProperty : $errorMsg\n";
            }

            throw new BadRequestException($errorsString);
        }
    }

    private function verifyAuthentication(?Request $request): void {
        $adminLogin = $this->getParameter('ADMIN_LOGIN');
        $adminPassword = $this->getParameter('ADMIN_PASSWORD');
        $token = base64_encode("$adminLogin:$adminPassword");

        $userToken = explode(' ', $request->headers->get('Authorization'));


        if ($userToken[0] != 'basic' || $userToken[1] != $token) {
            throw new UnauthorizedHttpException('Basic', 'Identifiants incorrects');
        }
    }

    private function verifyFormatAllowed(?string $contentType): void {

        if (! in_array($contentType, $this->ALLOWED_FORMAT)) {
            throw new NotAcceptableHttpException('Le format n\'est pas autorisé');
        }
    }

    private function updateCompany(string $siren, Company $company, mixed $object) {

        $invalid = true;

        if (property_exists($object, 'nom_raison_sociale')) {
            $company->setNomRaisonSociale($object->nom_raison_sociale);
            $invalid = false;
        }
        if (property_exists($object, 'adresse')) {
            $company->setAdresse($object->adresse);
            $invalid = false;
        }
        if (property_exists($object, 'siret')) {
            $company->setSiret($object->siret);
            $invalid = false;
        }

        if ($invalid) throw new BadRequestException('Format invalide');

        $jsonContent = $this->serializer->serialize($company, 'json');
        $this->fileService->updateFileWithContent("$siren.json", $jsonContent);
    }
}