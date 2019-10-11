<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AgVoyController extends AbstractController
{
    /**
     * @Route("/ag/voy", name="ag_voy")
     */
    public function index()
    {
        return $this->render('ag_voy/index.html.twig', [
            'controller_name' => 'AgVoyController',
        ]);
    }
}
