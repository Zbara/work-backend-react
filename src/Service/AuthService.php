<?php

namespace App\Service;

use App\Repository\ClientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class AuthService
{
    public array $result = [];
    public int $code = 200;

    private EntityManagerInterface $em;
    private ClientsRepository $clientsRepository;

    public function __construct(EntityManagerInterface $entityManager, ClientsRepository $clientsRepository)
    {
        $this->em = $entityManager;
        $this->clientsRepository = $clientsRepository;
    }

    public function helper(string $email, string $password)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if ($user = $this->clientsRepository->findOneBy(['email' => $email, 'password' => $password])) {
            $user->setLastdateAt(time());
            $this->em->flush();

            $session = new Session(new NativeSessionStorage(), new AttributeBag());
            $session->set('user', $user->getId());

            $response->setContent(json_encode(['status' => 1]))
                ->setStatusCode(Response::HTTP_OK)
                ->send();
        }
        $response->setContent(json_encode(['status' => 0]))->setStatusCode(Response::HTTP_UNAUTHORIZED)->send();
    }
}
