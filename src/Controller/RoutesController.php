<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoutesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('routes/accueil.html.twig', [
            'controller_name' => 'RoutesController',
        ]);
    }

    /**
     * @Route("/studios", name="studios")
     */
    public function studios(): Response
    {
        return $this->render('routes/studios.html.twig', [
            'controller_name' => 'RoutesController',
        ]);
    }

    /**
     * @Route("/tarifs", name="tarifs")
     */
    public function tarif(): Response
    {
        return $this->render('routes/tarifs.html.twig', [
            'controller_name' => 'RoutesController',
        ]);
    }

    /**
     * @Route("/label", name="label")
     */
    public function Label(): Response
    {
        return $this->render('routes/label.html.twig', [
            'controller_name' => 'RoutesController',
        ]);
    }

}
