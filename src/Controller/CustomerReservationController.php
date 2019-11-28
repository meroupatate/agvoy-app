<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\CustomerReservationType;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/customer")
 */
class CustomerReservationController extends AbstractController
{
    /**
     * @Route("/", name="customer_reservation_index", methods={"GET"})
     * @param Security $security
     * @param ReservationRepository $reservationRepository
     * @return Response
     */
    public function index(Security $security, ReservationRepository $reservationRepository): Response
    {
        $reservations = $security->getUser()->getCustomer()->getReservations();

        return $this->render('customer_reservation/index.html.twig', [
            'reservations' => $reservations
        ]);
    }

    /**
     * @Route("/{id}", name="customer_reservation_delete", methods={"DELETE"})
     * @param Security $security
     * @param Request $request
     * @param Reservation $reservation
     * @return Response
     */
    public function delete(Security $security, Request $request, Reservation $reservation): Response
    {
        $customer = $security->getUser()->getCustomer();
        if ($customer != $reservation->getRoom()->getCustomer()) {
            throw new AccessDeniedException('Access denied...');
        }

        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_reservation_index');
    }
}
