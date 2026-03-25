<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Service\MongoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function reservation(
        Request $request,
        EntityManagerInterface $em,
        MongoService $mongoService
    ): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {

                /** @var \App\Entity\Utilisateur $user */
                $user = $this->getUser();
                if (!$user) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Utilisateur non connecté'
                    ], 403);
                }

                // --- Gestion heure ---
                $reservation->setHeure($form->get('heure')->getData());

                // --- Rattacher user et status ---
                $reservation->setUser($user);
                $reservation->setStatus(Reservation::STATUS_EN_ATTENTE);

                // --- Persist reservation en SQL ---
                $em->persist($reservation);
                $em->flush();

                // --- Mettre à jour les stats quotidiennes dans MongoDB ---
                $date = $reservation->getDate()->format('Y-m-d');
                $nbCouvert = (int) $reservation->getNbCouvert();

                $mongoService->upsertDailyStats('daily_stats', $date, $nbCouvert);

                // --- Récupérer les stats du jour pour le retour ---
                $stats = $mongoService->getDailyStats('daily_stats', $date);

                return new JsonResponse([
                    'success' => true,
                    'message' => 'Votre réservation a été enregistrée !',
                    'stats' => $stats
                ]);
            }

            // --- Retour des erreurs du formulaire ---
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'message' => 'Le formulaire contient des erreurs',
                'errors' => $errors
            ]);
        }

        // --- Affichage normal du formulaire ---
        return $this->render('reservation.html.twig', [
            'form' => $form,
        ]);
    }
}