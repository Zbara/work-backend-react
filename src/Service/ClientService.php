<?php

namespace App\Service;

use App\Entity\Clients;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ClientService
{


    private Environment $environment;
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Environment            $environment,
        EntityManagerInterface $entityManager,
        Security               $security
    )
    {
        $this->entityManager = $entityManager;
        $this->environment = $environment;
        $this->security = $security;
    }

    public function create(Clients $clients): array
    {
        $clients->setCreatedAt(time());

        $this->entityManager->persist($clients);
        $this->entityManager->flush();

        try {
            return [
                'status' => 1,
                'result' => [
                    'save' => $this->environment->render('main/users.item.html.twig', [
                        'users' => $clients
                    ]),
                    'messages' => 'Аккаунт добавлен.'
                ]
            ];
        } catch (LoaderError|SyntaxError|RuntimeError $exception) {
            return ['status' => 0, 'messages' => $exception->getMessage()];
        }
    }

    #[ArrayShape(['status' => "int", 'result' => "string[]"])]
    public function remove(Clients $client): array
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return [
            'status' => 1,
            'result' => [
                'messages' => 'Аккаунт удален!'
            ]
        ];
    }
}
