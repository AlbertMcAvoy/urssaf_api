<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class LoginController extends AbstractController {

    #[Route('/login', name:'login', methods:'POST')]
    public function login(Request $request): Response {

        $adminLogin = $this->getParameter('ADMIN_LOGIN');
        $adminPassword = $this->getParameter('ADMIN_PASSWORD');
        $userCredentials = json_decode($request->getContent());

        if ($userCredentials->login != $adminLogin || $userCredentials->password != $adminPassword) {
            throw new UnauthorizedHttpException('Basic', 'Invalid Credentials');
        }

        $token = base64_encode("$adminLogin:$adminPassword");

        return $this->json(['token' => $token]);
    }
}