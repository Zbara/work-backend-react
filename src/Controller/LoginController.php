<?php

namespace App\Controller;

use App\Repository\ClientsRepository;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/authentication/log-in', name: 'app_login', methods: ['POST', 'GET'], format: 'JSON')]
    public function index(Request $request, AuthService $authService)
    {
        $params = json_decode($request->getContent());

        if ((empty($params->email) and empty($params->password))) {
            return $this->json(['status' => 'error'], 401);
        }
        $authService->helper(trim($params->email), trim($params->password));
    }

    #[Route('/api/authentication', name: 'app_authentication', format: 'JSON')]
    public function authentication(Request $request, AuthService $authService, ClientsRepository $clientsRepository): Response
    {
        if($user = $clientsRepository->findOneBy(['id' => $request->cookies->get('user')])){
            return $this->json([
                'id' => $user->getId()
            ]);
        }
        return $this->json([], Response::HTTP_UNAUTHORIZED);
    }

    #[Route('/api/authentication/log-out', name: 'app_log_out', format: 'JSON')]
    public function authenticationLogout(Request $request, AuthService $authService, ClientsRepository $clientsRepository): Response
    {
        if($user = $clientsRepository->findOneBy(['id' => $request->cookies->get('user')])){
            $response = new Response();
            $response->headers->clearCookie('user');
            $response->setContent(json_encode([]))->send();
        }
        return $this->json([], Response::HTTP_UNAUTHORIZED);
    }
}
