<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/authentication/log-in', name: 'app_login', methods: ['POST'], format: 'JSON')]
    public function index(Request $request, AuthService $authService): Response
    {
        $params = json_decode($request->getContent());

        if ((isset($params->email) and isset($params->password))) {
            $authService->helper($params->email, $params->password);

            return $this->json($authService->result, $authService->code);
        }
        return $this->json(['status' => 'error'], 401);
    }

    #[Route('/api/authentication', name: 'app_authentication', format: 'JSON')]
    public function authentication(Request $request, AuthService $authService): Response
    {
        return $this->json([]);
    }

    #[Route('/api/authentication/log-out', name: 'app_log_out', format: 'JSON')]
    public function authenticationLogout(Request $request, AuthService $authService): Response
    {
        return $this->json([]);
    }
}
