<?php

namespace App\Service;

use App\Repository\ClientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;

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
        $user = $this->clientsRepository->findOneBy(['email' => $email, 'password' => $password]);

        if (isset($user)) {
            $user->setLastdateAt(time());

            $this->em->flush();

            $this->setResult(['status' => 1], 200);
        } else {
            $this->setResult(['status' => 0], 401);
        }
    }

    private function setResult($result, $code)
    {
        $this->result = $result;
        $this->code = $code;
    }
}
