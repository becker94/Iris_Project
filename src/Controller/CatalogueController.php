<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(VehiculeRepository $vehiculeRepository): Response
    {

        $vehicules = $vehiculeRepository->findAvailable();

        return $this->render('catalogue/index.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }
}