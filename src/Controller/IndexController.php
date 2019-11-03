<?php

namespace App\Controller;

use App\Entity\Region;
use App\Form\IndexRegionType;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de la page d'accueil
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/index_region", name="index_region", methods={"GET", "POST"})
     * @param Request $request
     * @param RegionRepository $regionRepository
     * @return Response
     */
    public function index_region(Request $request, RegionRepository $regionRepository)
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
        return $this->render('index/region.html.twig', [
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
        ]);
    }
}
