<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\User;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

#[Route('/chat')]
#[IsGranted('ROLE_USER')]
class ChatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TwilioService $twilioService,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/{id}', name: 'app_chat_show', methods: ['GET'])]
    public function show(User $friend): Response
    {
        $currentUser = $this->getUser();
        
        // Récupérer les messages entre les deux utilisateurs
        $chats = $this->entityManager->getRepository(Chat::class)->findBy(
            [
                'sender' => [$currentUser, $friend],
                'receiver' => [$currentUser, $friend],
            ],
            ['createdAt' => 'ASC']
        );

        // Marquer les messages reçus comme lus
        foreach ($chats as $chat) {
            if ($chat->getReceiver() === $currentUser && !$chat->isRead()) {
                $chat->setIsRead(true);
            }
        }
        $this->entityManager->flush();

        // Récupérer la liste des amis
        $friends = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.id != :currentUser')
            ->setParameter('currentUser', $currentUser->getId())
            ->getQuery()
            ->getResult();

        // Marquer les messages non lus
        foreach ($friends as $f) {
            $unreadMessages = $this->entityManager->getRepository(Chat::class)
                ->findBy([
                    'sender' => $f,
                    'receiver' => $currentUser,
                    'isRead' => false
                ]);
            $f->hasUnreadMessages = count($unreadMessages) > 0;
        }

        return $this->render('chat/show.html.twig', [
            'friend' => $friend,
            'currentFriend' => $friend,
            'chats' => $chats,
            'friends' => $friends,
        ]);
    }

    #[Route('/{id}/send', name: 'app_chat_send', methods: ['POST'])]
    public function sendMessage(Request $request, User $friend): Response
    {
        $currentUser = $this->getUser();
        $message = $request->request->get('message');

        $this->logger->info('Tentative d\'envoi de message', [
            'from' => $currentUser->getUsername(),
            'to' => $friend->getUsername(),
            'message' => $message
        ]);

        if (!$message) {
            $this->addFlash('error', 'Le message ne peut pas être vide');
            return $this->redirectToRoute('app_chat_show', ['id' => $friend->getId()]);
        }

        // Créer un nouveau message
        $chat = new Chat();
        $chat->setSender($currentUser);
        $chat->setReceiver($friend);
        $chat->setMessage($message);
        $chat->setIsRead(false); // Le message est non lu par défaut

        // Envoyer le message via Twilio
        try {
            $channelSid = $this->twilioService->getOrCreateChannel($currentUser, $friend);
            $this->logger->info('Channel récupéré/créé', ['channelSid' => $channelSid]);
            
            $chat->setTwilioChannelSid($channelSid);
            
            $this->twilioService->sendMessageToChannel(
                $channelSid,
                $currentUser->getUsername(),
                $message
            );

            $this->entityManager->persist($chat);
            $this->entityManager->flush();

            $this->logger->info('Message enregistré en base de données', [
                'chatId' => $chat->getId()
            ]);

            $this->addFlash('success', 'Message envoyé avec succès');
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi du message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addFlash('error', 'Erreur lors de l\'envoi du message');
        }

        return $this->redirectToRoute('app_chat_show', ['id' => $friend->getId()]);
    }

    #[Route('/list', name: 'app_chat_list', methods: ['GET'])]
    public function list(): Response
    {
        $currentUser = $this->getUser();
        
        // Récupérer tous les chats de l'utilisateur
        $chats = $this->entityManager->getRepository(Chat::class)
            ->createQueryBuilder('c')
            ->where('c.sender = :user OR c.receiver = :user')
            ->setParameter('user', $currentUser)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        // Organiser les chats par conversation
        $conversations = [];
        foreach ($chats as $chat) {
            $otherUser = $chat->getSender() === $currentUser ? $chat->getReceiver() : $chat->getSender();
            $conversations[$otherUser->getId()] = $otherUser;
        }

        return $this->render('chat/list.html.twig', [
            'conversations' => $conversations,
        ]);
    }
} 