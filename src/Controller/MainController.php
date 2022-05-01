<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\User;
use App\Form\ClientType;
use App\Form\EditType;
use App\Repository\ClientsRepository;
use App\Repository\UserRepository;
use App\Service\AdminService;
use App\Service\ClientService;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[
        Route('/admin/edit', name: 'main-admin')
    ]
    public function editProfile(): Response
    {
        return $this->render('main/admin.html.twig');
    }

    #[
        Route('/admin/edit/save/login', name: 'main-admin-save')
    ]
    public function editProfileSave(Request $request, UserRepository $repository, EntityManagerInterface $manager): Response
    {
        if ($request->isXmlHttpRequest()) {
            if ($repository->findOneBy(['username' => $request->get('username')]) === null) {
                $user = $repository->findOneBy(['id' => $this->getUser()]);
                $user->setUsername($request->get('username'));

                $manager->flush();

                return $this->json(['status' => 1, 'result' => ['messages' => 'Логин изменен.']]);
            }
            return $this->json(['status' => 0, 'result' => ['messages' => 'Ошибка, логин занят.']]);
        }
        return $this->json([]);
    }

    #[
        Route('/admin/edit/save/pass', name: 'main-admin-save-pass')
    ]
    public function editProfileSavePass(Request $request, UserRepository $repository, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder): Response
    {
        if ($request->isXmlHttpRequest()) {

            if ($request->get('password')) {
                $user = $repository->findOneBy(['id' => $this->getUser()]);
                $user->setPassword($encoder->hashPassword($user, $request->get('password')));

                $manager->flush();

                return $this->json(['status' => 1, 'result' => ['messages' => 'Пароль изменен.']]);
            }
            return $this->json(['status' => 0, 'result' => ['messages' => 'Ошибка, старые пароли не совпали.']]);
        }
        return $this->json([]);
    }
}
