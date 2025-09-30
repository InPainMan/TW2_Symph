<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    // Route pour afficher un service en passant le nom dans l'URL
    #[Route('/service/{name}', name: 'app_service_show')]
    public function showService(string $name): Response
    {
        // Passe la variable 'name' au template Twig
        return $this->render('service/showService.html.twig', [
            'name' => $name,
        ]);
    }

    // Route pour revenir à la page d'accueil
    #[Route('/go-to-index', name: 'app_service_go_to_index')]
    public function goToIndex(): Response
    {
        // Redirige vers la route 'app_home' du HomeController
        return $this->redirectToRoute('app_home');
    }
}
