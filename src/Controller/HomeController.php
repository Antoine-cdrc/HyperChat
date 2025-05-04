<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\FriendRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer tous les utilisateurs sauf celui qui est connecté
        $users = $em->getRepository(User::class)->findAll();

        // Récupérer les demandes d'amis pour l'utilisateur connecté
        $friendRequests = $em->getRepository(FriendRequest::class)->findBy([
            'receiver' => $this->getUser(),
            'status' => 'pending'
        ]);

        return $this->render('home/index.html.twig', [
            'users' => $users,
            'friendRequests' => $friendRequests, // Passer les demandes d'amis au template
        ]);
    }
}
