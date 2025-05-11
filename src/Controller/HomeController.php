<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\FriendRequest;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em, FriendRequestRepository $friendRequestRepository): Response
    {
        $currentUser = $this->getUser();

        // Récupérer tous les utilisateurs sauf celui qui est connecté
        $users = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.id != :currentUserId')
            ->setParameter('currentUserId', $currentUser->getId())
            ->getQuery()
            ->getResult();

        // Récupérer les demandes d'amis reçues en attente
        $receivedRequests = $em->getRepository(FriendRequest::class)->findBy([
            'receiver' => $currentUser,
            'status' => 'pending'
        ]);

        // Récupérer les demandes d'amis envoyées en attente
        $sentRequests = $em->getRepository(FriendRequest::class)->findBy([
            'sender' => $currentUser,
            'status' => 'pending'
        ]);

        // Récupérer les amis (demandes acceptées)
        $friends = $friendRequestRepository->findFriends($currentUser);

        return $this->render('home/index.html.twig', [
            'users' => $users,
            'receivedRequests' => $receivedRequests,
            'sentRequests' => $sentRequests,
            'friends' => $friends
        ]);
    }
}
