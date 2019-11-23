<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Room;
use App\Form\IndexRegionType;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de la page d'accueil
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET", "POST"})
     * @param Request $request
     * @param RegionRepository $regionRepository
     * @return Response
     */
    public function index(Request $request, RegionRepository $regionRepository)
    {
        $form = $this->createForm(IndexRegionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityObject = $form->get('id')->getData();
            $id = $entityObject->getId();
            return $this->redirect($this->generateUrl('index_region_show', [
                'id' => $id,
            ]));
        }
        return $this->render('index/index.html.twig', [
            'regions' => $regionRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/index_region/{id}", name="index_region_show", methods="GET")
     * @param Region $region
     * @return Response
     */
    public function index_region_show(Region $region)
    {
        return $this->render('index/region_show.html.twig', [
            'region' => $region,
            'likes' => $this->get('session')->get('likes'),
        ]);
    }

    /**
     * Like a room
     *
     * @Route("/like/{id}", name="room_like")
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function like($id)
    {
        $likes = $this->get('session')->get('likes');

        if (!isset($likes)) {
            $this->get('session')->set('likes', []);
        }

        $likes = $this->get('session')->get('likes');

        // si l'identifiant n'est pas présent dans le tableau des likes, l'ajouter
        if (!in_array($id, $likes)) {
            $likes[] = $id;
        } else // sinon, le retirer du tableau
        {
            $likes = array_diff($likes, array($id));
        }
        $this->get('session')->set('likes', $likes);

        $room = $this->getDoctrine()
            ->getRepository(Room::class)
            ->find($id);
        $region = $room->getRegions()[0];

        return $this->redirectToRoute('index_region_show', array('id' => $region->getId()));
    }

    /**
     * @Route("/likes", name="likes_show", methods="GET")
     * @return Response
     */
    public function likes_show()
    {
        $likes = $this->get('session')->get('likes');

        if (!isset($likes)) {
            $this->get('session')->set('likes', []);
        }

        $likes = $this->get('session')->get('likes');
        
        $rooms = array();
        foreach ($likes as $like) {
            $rooms[] = $this->getDoctrine()
                ->getRepository(Room::class)
                ->find($like);
        }
        return $this->render('index/likes.html.twig', [
            'likes' => $likes,
            'rooms' => $rooms,
        ]);
    }
}
