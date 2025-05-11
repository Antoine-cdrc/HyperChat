<?php

namespace App\Service;

use App\Entity\User;
use Twilio\Rest\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class TwilioService
{
    private $client;
    private $serviceSid;

    public function __construct(
        ParameterBagInterface $params,
        private LoggerInterface $logger
    ) {
        $accountSid = $params->get('twilio_account_sid');
        $authToken = $params->get('twilio_auth_token');
        $this->serviceSid = $params->get('twilio_service_sid');

        $this->client = new Client($accountSid, $authToken);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getServiceSid(): string
    {
        return $this->serviceSid;
    }

    public function getOrCreateChannel(User $user1, User $user2): string
    {
        // Créer un identifiant unique pour le channel basé sur les IDs des utilisateurs
        $channelName = sprintf('chat_%d_%d', min($user1->getId(), $user2->getId()), max($user1->getId(), $user2->getId()));

        try {
            // Essayer de trouver le channel existant
            $channels = $this->client->chat->v2->services($this->serviceSid)
                ->channels
                ->read(['uniqueName' => $channelName]);

            if (!empty($channels)) {
                return $channels[0]->sid;
            }

            // Créer un nouveau channel s'il n'existe pas
            $channel = $this->client->chat->v2->services($this->serviceSid)
                ->channels
                ->create([
                    'uniqueName' => $channelName,
                    'friendlyName' => sprintf('Chat entre %s et %s', $user1->getUsername(), $user2->getUsername()),
                    'type' => 'private'
                ]);

            // Ajouter les utilisateurs au channel
            $this->addUserToChannel($channel->sid, $user1);
            $this->addUserToChannel($channel->sid, $user2);

            return $channel->sid;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la création/récupération du channel: ' . $e->getMessage());
        }
    }

    private function addUserToChannel(string $channelSid, User $user): void
    {
        try {
            $this->client->chat->v2->services($this->serviceSid)
                ->channels($channelSid)
                ->members
                ->create([
                    'identity' => $user->getUsername()
                ]);
        } catch (\Exception $e) {
            // Ignorer l'erreur si l'utilisateur est déjà dans le channel
        }
    }

    public function sendMessageToChannel(string $channelSid, string $author, string $body): void
    {
        try {
            $this->logger->info('Tentative d\'envoi de message', [
                'channelSid' => $channelSid,
                'author' => $author,
                'body' => $body
            ]);

            $message = $this->client->chat->v2->services($this->serviceSid)
                ->channels($channelSid)
                ->messages
                ->create([
                    'from' => $author,
                    'body' => $body
                ]);

            $this->logger->info('Message envoyé avec succès', [
                'messageSid' => $message->sid
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi du message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erreur lors de l\'envoi du message: ' . $e->getMessage());
        }
    }
} 