<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FicheProduitController extends AbstractController
{
    #[Route('/vehicule/{id}', name: 'app_fiche_produit')]
    public function index(int $id): Response
    {
        $vehicules = [
            1 => [
                'titre' => 'Ferrari F40',
                'sous_titre' => "L'icône absolue des années 80",
                'prix' => '4 500 €',
                'annee' => '1992',
                'puissance' => '478 ch',
                'acceleration' => '4.1 s',
                'carburant' => 'Essence',
                'image' => 'ferrari_f40.png',
                'description' => "Vivez l'expérience ultime de conduite pure. La Ferrari F40 est la dernière œuvre supervisée par Enzo Ferrari lui-même.",
                'badge' => 'Disponible'
            ],
            2 => [
                'titre' => 'Shelby GT 500',
                'sous_titre' => "La légende américaine",
                'prix' => '289 000 €',
                'annee' => '1967',
                'puissance' => '355 ch',
                'acceleration' => '6.8 s',
                'carburant' => 'Essence',
                'image' => 'shelby_1.jpg',
                'description' => "Un morceau d'histoire américaine. Le rugissement du V8 Ford vous transportera directement sur la route 66.",
                'badge' => 'Réservé'
            ],
            3 => [
                'titre' => 'Bentley Continental',
                'sous_titre' => "Le luxe sans compromis",
                'prix' => '320 000 €',
                'annee' => '2022',
                'puissance' => '650 ch',
                'acceleration' => '3.6 s',
                'carburant' => 'Hybride',
                'image' => 'bentley_1.jpg',
                'description' => "Le mariage parfait entre technologie moderne et artisanat traditionnel britannique.",
                'badge' => 'Disponible'
            ]
        ];

        if (!isset($vehicules[$id])) {
            throw $this->createNotFoundException("Ce véhicule n'existe pas");
        }

        return $this->render('fiche_produit/index.html.twig', [
            'vehicule' => $vehicules[$id]
        ]);
    }
}