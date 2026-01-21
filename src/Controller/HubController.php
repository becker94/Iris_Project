<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HubController extends AbstractController
{
    #[Route('/hub', name: 'app_hub')]
    public function index(): Response
    {
        return $this->render('hub/index.html.twig', [
            'controller_name' => 'HubController',
        ]);
    }
}
