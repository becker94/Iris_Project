<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        // On récupère toutes les voitures depuis Directus
        $response = $this->client->request(
            'GET',
            $this->directusUrl . '/items/voiture'
        );

        $content = $response->toArray();
        $voitures = $content['data'];

        // On envoie les données au dossier "catalogue" comme sur ton interface
        return $this->render('catalogue/index.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    // 2. LA ROUTE DE LA FICHE PRODUIT (DETAILS)
    #[Route('/voiture/{id}', name: 'app_voiture_show')]
    public function show(int $id): Response
    {
        // On récupère une seule voiture précise via son ID
        $response = $this->client->request(
            'GET',
            $this->directusUrl . '/items/voiture/' . $id
        );

        $content = $response->toArray();
        $voiture = $content['data'];

        // On envoie les données au dossier "voiture" pour la fiche détails
        return $this->render('voiture/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }
}