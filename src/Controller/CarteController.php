<?php

namespace App\Controller;

use App\Repository\FoodRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class CarteController extends AbstractController
{
    #[Route('/carte', name: 'app_carte', methods:['GET'])]
    public function index(Request $request, FoodRepository $platRepository, UtilisateurRepository $utilisateurRepository): Response
    {
        $plats = $platRepository->findAll();

        return $this->render('carte/index.html.twig', [
            'plats' => $plats,
        ]);
    }
}
