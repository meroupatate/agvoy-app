<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\OwnerRoomType;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/owner/room")
 */
class OwnerRoomController extends AbstractController
{
    /**
     * @Route("/", name="owner_room_index", methods={"GET"})
     * @param Security $security
     * @param RoomRepository $roomRepository
     * @return Response
     */
    public function index(Security $security, RoomRepository $roomRepository): Response
    {
        $owner = $security->getUser()->getOwner();

        return $this->render('owner_room/index.html.twig', [
            'rooms' => $roomRepository->findBy(
                array('owner' => $owner)
            ),
        ]);
    }

    /**
     * @Route("/new", name="owner_room_new", methods={"GET","POST"})
     * @param Security $security
     * @param Request $request
     * @return Response
     */
    public function new(Security $security, Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(OwnerRoomType::class, $room);
        $form->handleRequest($request);

        $owner = $security->getUser()->getOwner();
        $room->setOwner($owner);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('owner_room_index');
        }

        return $this->render('owner_room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="owner_room_show", methods={"GET"})
     * @param Security $security
     * @param Room $room
     * @return Response
     */
    public function show(Security $security, Room $room): Response
    {
        $owner = $security->getUser()->getOwner();
        if ($owner != $room->getOwner()) {
            throw new AccessDeniedException('Access denied...');
        }
        return $this->render('owner_room/show.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="owner_room_edit", methods={"GET","POST"})
     * @param Security $security
     * @param Request $request
     * @param Room $room
     * @return Response
     */
    public function edit(Security $security, Request $request, Room $room): Response
    {
        $owner = $security->getUser()->getOwner();
        if ($owner != $room->getOwner()) {
            throw new AccessDeniedException('Access denied...');
        }

        $form = $this->createForm(OwnerRoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('owner_room_index');
        }

        return $this->render('owner_room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="owner_room_delete", methods={"DELETE"})
     * @param Security $security
     * @param Request $request
     * @param Room $room
     * @return Response
     */
    public function delete(Security $security, Request $request, Room $room): Response
    {
        $owner = $security->getUser()->getOwner();
        if ($owner != $room->getOwner()) {
            throw new AccessDeniedException('Access denied...');
        }

        if ($this->isCsrfTokenValid('delete' . $room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('owner_room_index');
    }
}
