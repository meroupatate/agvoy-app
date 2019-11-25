<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Room;
use App\Form\IndexRegionType;
use App\Repository\RegionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('id', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => false,
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'choice',
                'input' => 'datetime_immutable',
                'label' => false,
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'choice',
                'input' => 'datetime_immutable',
                'label' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('form');
            $id = $data['id'];
            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
            return $this->redirect($this->generateUrl('index_region_show', [
                'id' => $id,
                'start' => implode('-', $startDate),
                'end' => implode('-', $endDate),
            ]));
        }
        return $this->render('index/index.html.twig', [
            'regions' => $regionRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/index_region/{id}/{start}/{end}", name="index_region_show", methods="GET")
     * @param Region $region
     * @return Response
     */
    public function index_region_show(Region $region, String $start, String $end)
    {
        $startDate = explode('-', $start);
        $endDate = explode('-', $end);
        $rooms = [];

        foreach ($region->getRooms() as $room) {
            $found = True;
            foreach ($room->getReservations() as $res) {
                if ($startDate >= $res->getStartDate() || $startDate <= $res->getEndDate()
                    || $endDate >= $res->getStartDate() || $endDate <= $res->getEndDate()) {
                    $found = False;
                }
            }
            foreach ($room->getUnavailabilities() as $unav) {
                if ($startDate >= $unav->getStartDate() || $startDate <= $unav->getEndDate()
                    || $endDate >= $unav->getStartDate() || $endDate <= $unav->getEndDate()) {
                    $found = False;
                }
            }
            if ($found) {
                $rooms[] = $room;
            }
        }

        return $this->render('index/region_show.html.twig', [
            'region' => $region,
            'rooms' => $rooms,
            'likes' => $this->get('session')->get('likes'),
            'start' => implode('-', $startDate),
            'end' => implode('-', $endDate),
        ]);
    }

    /**
     * Like a room
     *
     * @Route("/like/{id}/{start}/{end}", name="room_like")
     *
     * @param int $id
     * @param $start
     * @param $end
     * @return RedirectResponse
     */
    public function like($id, $start, $end)
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

        return $this->redirectToRoute('index_region_show', array('id' => $region->getId(), 'start' => $start, 'end' => $end));
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
