<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\User;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FriendController extends AbstractController
{
    /**
     * Route pour envoyer une demande d'ami
     * 
     * @Route("/friend/request/{id}", name="friend_request")
     */
    #[Route('/friend/request/{id}', name: 'friend_request')]
    public function sendFriendRequest(User $receiver, EntityManagerInterface $em, FriendRequestRepository $friendRequestRepository): Response
    {
        $sender = $this->getUser();

        // Vérifier que l'utilisateur ne s'ajoute pas lui-même
        if ($sender === $receiver) {
            $this->addFlash('warning', 'Vous ne pouvez pas vous ajouter vous-même.');
            return $this->redirectToRoute('home');
        }

        // Vérifier si une demande existe déjà entre ces deux utilisateurs
        $existingRequest = $friendRequestRepository->findOneBy([
            'sender' => $sender,
            'receiver' => $receiver,
            'status' => 'pending', // La demande doit être en attente
        ]);

        if ($existingRequest) {
            $this->addFlash('info', 'Une demande d\'ami est déjà en cours.');
            return $this->redirectToRoute('home');
        }

        // Créer la nouvelle demande d'ami
        $friendRequest = new FriendRequest();
        $friendRequest->setSender($sender);
        $friendRequest->setReceiver($receiver);
        $friendRequest->setStatus('pending');
        $friendRequest->setCreatedAt(new \DateTime());

        $em->persist($friendRequest);
        $em->flush();

        $this->addFlash('success', 'Demande d\'ami envoyée !');
        return $this->redirectToRoute('home');
    }

    /**
     * Route pour accepter une demande d'ami
     * 
     * @Route("/friend/accept/{id}", name="friend_accept")
     */
    public function acceptFriendRequest(FriendRequest $friendRequest, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Vérifier que la demande d'ami est bien pour l'utilisateur connecté
        if ($friendRequest->getReceiver() !== $user) {
            $this->addFlash('error', 'Cette demande ne vous est pas destinée.');
            return $this->redirectToRoute('home');
        }

        // Accepter la demande
        $friendRequest->setStatus('accepted');
        $em->flush();

        $this->addFlash('success', 'Demande d\'ami acceptée.');
        return $this->redirectToRoute('home');
    }

    /**
     * Route pour refuser une demande d'ami
     * 
     * @Route("/friend/refuse/{id}", name="friend_refuse")
     */
    public function refuseFriendRequest(FriendRequest $friendRequest, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Vérifier que la demande d'ami est bien pour l'utilisateur connecté
        if ($friendRequest->getReceiver() !== $user) {
            $this->addFlash('error', 'Cette demande ne vous est pas destinée.');
            return $this->redirectToRoute('home');
        }

        // Refuser la demande
        $friendRequest->setStatus('refused');
        $em->flush();

        $this->addFlash('info', 'Demande d\'ami refusée.');
        return $this->redirectToRoute('home');
    }
}
