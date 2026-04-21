<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FakePaymentController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'DIRECTUS_URL')]
        private string $directusUrl,
    ) {}

    #[Route('/paiement/checkout/{id}', name: 'app_fake_payment', methods: ['GET'])]
    public function show(int $id): Response
    {
        $response = $this->client->request('GET', $this->directusUrl . '/items/voiture/' . $id);
        $voiture = $response->toArray()['data'];

        return $this->render('paiement/fake_checkout.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/paiement/checkout/{id}', name: 'app_fake_payment_submit', methods: ['POST'])]
    public function submit(int $id, Request $request): Response
    {
        // Simulation paiement réussi — aucune validation réelle
        return $this->redirectToRoute('app_paiement_succes');
    }
}
