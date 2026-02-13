<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ComingSoonController extends AbstractController
{
    #[Route('/bientot', name: 'app_coming_soon')]
    public function index(): Response
    {
        return $this->render('comingsoon/index.html.twig');
    }
}