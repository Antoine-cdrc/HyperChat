<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/friend')]
#[IsGranted('ROLE_USER')]
class FriendRequestController extends AbstractController
{
    #[Route('/accept/{id}', name: 'friend_accept')]
    public function accept(FriendRequest $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->getReceiver() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accepter cette demande.');
        }

        $request->setStatus('accepted');
        $entityManager->flush();

        $this->addFlash('success', 'Demande d\'ami acceptée !');
        return $this->redirectToRoute('home');
    }

    #[Route('/refuse/{id}', name: 'friend_refuse')]
    public function refuse(FriendRequest $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->getReceiver() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas refuser cette demande.');
        }

        $entityManager->remove($request);
        $entityManager->flush();

        $this->addFlash('success', 'Demande d\'ami refusée.');
        return $this->redirectToRoute('home');
    }
} 