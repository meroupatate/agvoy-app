<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\OwnerReservationType;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/owner/reservation")
 */
class OwnerReservationController extends AbstractController
{
    /**
     * @Route("/", name="owner_reservation_index", methods={"GET"})
     * @param Security $security
     * @param ReservationRepository $reservationRepository
     * @return Response
     */
    public function index(Security $security, ReservationRepository $reservationRepository): Response
    {
        $rooms = $security->getUser()->getOwner()->getRooms();
        $reservations = [];
        foreach ($rooms as $room) {
            $reservations = array_merge($reservations, $room->getReservations()->toArray());
        }

        return $this->render('owner_reservation/index.html.twig', [
            'reservations' => $reservations
        ]);
    }

    /**
     * @Route("/{id}", name="owner_reservation_delete", methods={"DELETE"})
     * @param Security $security
     * @param Request $request
     * @param Reservation $reservation
     * @return Response
     */
    public function delete(Security $security, Request $request, Reservation $reservation): Response
    {
        $owner = $security->getUser()->getOwner();
        if ($owner != $reservation->getRoom()->getOwner()) {
            throw new AccessDeniedException('Access denied...');
        }

        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('owner_reservation_index');
    }
}
