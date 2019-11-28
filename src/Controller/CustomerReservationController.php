<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\Unavailability;
use App\Form\CustomerReservationType;
use App\Form\ReservationType;
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
        if ($customer != $reservation->getCustomer()) {
            throw new AccessDeniedException('Access denied...');
        }

        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_reservation_index');
    }

    /**
     * @Route("/show/{id}/{start}/{end}", name="customer_reservation_show", methods={"GET", "POST"})
     * @param Room $room
     * @param $start
     * @param $end
     * @param Request $request
     * @param Security $security
     * @return Response
     */
    public function show(Room $room, $start, $end, Request $request, Security $security): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(CustomerReservationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $security->getUser()->getCustomer()) {
            $reservation->setStartDate(\DateTime::createFromFormat('m-d-Y', $start));
            $reservation->setEndDate(\DateTime::createFromFormat('m-d-Y', $end));
            $reservation->setRoom($room);
            $reservation->setCustomer($security->getUser()->getCustomer());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('customer_reservation_index');
        }

        return $this->render('customer_reservation/show.html.twig', [
            'room' => $room,
            'start' => $start,
            'end' => $end,
            'form' => $form->createView()
        ]);
    }

}
