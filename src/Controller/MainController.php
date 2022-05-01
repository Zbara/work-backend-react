<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Form\ClientType;
use App\Repository\ClientsRepository;
use App\Service\ClientService;
use App\Service\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/admin/users', name: 'app-main')]
    public function index(Request $request, ClientService $clientService, FormError $formError, ClientsRepository $clientsRepository): Response
    {
        $client = new Clients();
        $client->setUser($this->getUser());

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid()) {
                return $this->json($clientService->create($client));
            }
            return $this->json(['status' => 0, 'error' => ['messages' => $formError->getErrorMessages($form)]]);
        }

        return $this->render('main/users.html.twig',
        [
            'form' => $form->createView(),
            'items' => $clientsRepository->findBy(['user' => $this->getUser()], ['id' => 'DESC'])
        ]);
    }

    #[
        Route('/admin/users/remove', name: 'main-remove', methods: ['POST'])
    ]
    public function remove(Request $request, ClientsRepository $clientsRepository, ClientService $clientService): Response
    {
        if ($client = $clientsRepository->findOneBy(['id' => (int)$request->get('id', 0), 'user' => $this->getUser()])) {
            return $this->json($clientService->remove($client));
        }
        return $this->json(['status' => 0, 'error' => ['messages' => 'Аккаунт не найден!']]);
    }

}
