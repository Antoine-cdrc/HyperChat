<?php

namespace App\Controller;

use App\Service\TwilioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/twilio')]
#[IsGranted('ROLE_USER')]
class TwilioTestController extends AbstractController
{
    #[Route('/test', name: 'twilio_test')]
    public function test(TwilioService $twilioService): Response
    {
        try {
            $client = $twilioService->getClient();
            $service = $client->chat->v2->services($twilioService->getServiceSid())->fetch();
            
            return $this->json([
                'status' => 'success',
                'message' => 'Connexion à Twilio réussie !',
                'service' => [
                    'sid' => $service->sid,
                    'friendlyName' => $service->friendlyName
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Erreur de connexion à Twilio : ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 