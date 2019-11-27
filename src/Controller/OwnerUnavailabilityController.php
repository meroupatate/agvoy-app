<?php

namespace App\Controller;

use App\Entity\Unavailability;
use App\Form\OwnerUnavailabilityType;
use App\Repository\UnavailabilityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/owner/unavailability")
 */
class OwnerUnavailabilityController extends AbstractController
{
    /**
     * @Route("/", name="owner_unavailability_index", methods={"GET"})
     * @param Security $security
     * @param UnavailabilityRepository $unavailabilityRepository
     * @return Response
     */
    public function index(Security $security, UnavailabilityRepository $unavailabilityRepository): Response
    {
        $rooms = $security->getUser()->getOwner()->getRooms();
        $unavailabilities = [];
        foreach ($rooms as $room) {
            $unavailabilities = array_merge($unavailabilities, $room->getUnavailabilities()->toArray());
        }

        return $this->render('owner_unavailability/index.html.twig', [
            'unavailabilities' => $unavailabilities
        ]);
    }

    /**
     * @Route("/new", name="owner_unavailability_new", methods={"GET","POST"})
     * @param Security $security
     * @param Request $request
     * @return Response
     */
    public function new(Security $security, Request $request): Response
    {
        $unavailability = new Unavailability();
        $form = $this->createForm(OwnerUnavailabilityType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
            $room = $data['room'];

            $unavailability->setStartDate($startDate);
            $unavailability->setEndDate($endDate);
            $unavailability->setRoom($room);

            $owner = $security->getUser()->getOwner();
            if ($owner == $room->getOwner()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($unavailability);
                $entityManager->flush();
            }

            return $this->redirectToRoute('owner_unavailability_index');
        }

        return $this->render('owner_unavailability/new.html.twig', [
            'unavailability' => $unavailability,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="owner_unavailability_show", methods={"GET"})
     * @param Security $security
     * @param Unavailability $unavailability
     * @return Response
     */
    public function show(Security $security, Unavailability $unavailability): Response
    {
        $owner = $security->getUser()->getOwner();
        if ($owner != $unavailability->getRoom()->getOwner()) {
            throw new AccessDeniedException('Access denied...');
        }
        return $this->render('owner_unavailability/show.html.twig', [
            'unavailability' => $unavailability,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="owner_unavailability_edit", methods={"GET","POST"})
     * @param Security $security
     * @param Request $request
     * @param Unavailability $unavailability
     * @return Response
     */
    public function edit(Security $security, Request $request, Unavailability $unavailability): Response
    {
        $owner = $security->getUser()->getOwner();
        if ($owner != $unavailability->getRoom()->getOwner()) {
            throw new AccessDeniedException('Access denied...');
        }

        $form = $this->createForm(OwnerUnavailabilityType::class, $unavailability);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $owner = $security->getUser()->getOwner();
            $room = $unavailability->getRoom();

            if ($owner == $room->getOwner()) {
                $this->getDoctrine()->getManager()->flush();
            }


            return $this->redirectToRoute('owner_unavailability_index');
        }

        return $this->render('owner_unavailability/edit.html.twig', [
            'unavailability' => $unavailability,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="owner_unavailability_delete", methods={"DELETE"})
     * @param Security $security
     * @param Request $request
     * @param Unavailability $unavailability
     * @return Response
     */
    public function delete(Security $security, Request $request, Unavailability $unavailability): Response
    {
        $owner = $security->getUser()->getOwner();
        if ($owner != $unavailability->getRoom()->getOwner()) {
            throw new AccessDeniedException('Access denied...');
        }

        if ($this->isCsrfTokenValid('delete' . $unavailability->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($unavailability);
            $entityManager->flush();
        }

        return $this->redirectToRoute('owner_unavailability_index');
    }
}
