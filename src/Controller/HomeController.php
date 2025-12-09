<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    // #[Route('/home', name: 'app_home')]
    // public function index(): Response
    // {
    //     return $this->render('home/index.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }

    #[Route("/", "home")]
    function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        // dd($this->getUser());
        // $user = new User();
        // $user->setUsername('Nika')
        //     ->setEmail('nika@mugiwara.op')
        //     ->setPassword($hasher->hashPassword($user, 'nika'))
        //     ->setRoles([]);

        // $em->persist($user);
        // $em->flush();

        return $this->render('home/index.html.twig');
    }
}
