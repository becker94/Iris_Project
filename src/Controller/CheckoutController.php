<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{

    #[Route('/checkout/{id}', name: 'app_checkout')]
    public function checkout(int $id): Response
    {
        return $this->redirectToRoute('app_fake_payment', ['id' => $id]);
    }

    #[Route('/paiement/succes', name: 'app_paiement_succes')]
    public function succes(): Response
    {
        return $this->render('paiement/success.html.twig');
    }

    #[Route('/paiement/annule', name: 'app_paiement_annule')]
    public function annule(): Response
    {
        return $this->render('paiement/cancel.html.twig');
    }
}
