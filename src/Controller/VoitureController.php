<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VoitureController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'DIRECTUS_URL')]
        private string $directusUrl
    ) {
    }

    // 1. LA ROUTE DU CATALOGUE
    #[Route('/voitures', name: 'app_voitures')]
    public function index(): Response
    {
        $voitures = [];
        $error = null;

        try {
            $response = $this->client->request('GET', $this->directusUrl . '/items/voiture');
            $content = $response->toArray();
            $voitures = $content['data'];
        } catch (TransportExceptionInterface $e) {
            $error = 'Le catalogue est temporairement indisponible. Veuillez réessayer dans quelques instants.';
        }

        return $this->render('catalogue/index.html.twig', [
            'voitures' => $voitures,
            'error'    => $error,
        ]);
    }

    // 2. LA ROUTE DE LA FICHE PRODUIT (DETAILS)
    #[Route('/voiture/{id}', name: 'app_voiture_show')]
    public function show(int $id): Response
    {
        try {
            $response = $this->client->request('GET', $this->directusUrl . '/items/voiture/' . $id);
            $content  = $response->toArray();
            $voiture  = $content['data'];
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'Fiche indisponible — Directus hors-ligne.');
            return $this->redirectToRoute('app_voitures');
        }

        return $this->render('voiture/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }
}