<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HeritageMotorsController extends AbstractController
{
    #[Route('/heritage/motors', name: 'app_heritage_motors')]
    public function index(): Response
    {
        return $this->render('heritage_motors/index.html.twig', [
            'controller_name' => 'HeritageMotorsController',
        ]);
    }
}
