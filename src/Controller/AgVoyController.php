<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Room;
use Symfony\Component\HttpFoundation\Response;

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
    /**
     * @Route("/listerooms", name="rooms_index")
     */
    
    public function listRoom()
    {
        $htmlpage = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des rooms</title>
    </head>
    <body>
        <h1>rooms list</h1>
        <p>Here are all your rooms:</p>
        <ul>';
        
        $em = $this->getDoctrine()->getManager();
        $rooms = $em->getRepository(Room::class)->findAll();
        foreach($rooms as $room) {
            $url= $this->generateUrl(
                'room_show',
                ['id' => $room->getId()]);
                $htmlpage .= '<li>
            <a href="'.$url.'">'.$room.'</a></li>';
        }
        $htmlpage .= '</ul>';
        
        $htmlpage .= '</body></html>';
        
        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
            );
    }
    
    /**
     * Show a room
     *
     * @Route("/{id}", name="room_show", requirements={"year"="\d+"})
     *    note that the year must be an integer, above
     *
     * @param String $title
     * @param Integer $year
     */
    public function show($id)
    {
        $roomRepo = $this->getDoctrine()->getRepository('App:Room');
        $room = $roomRepo->findOneBy(['id' => $id]);
        
        if (!$room) {
            throw $this->createNotFoundException('The room does not exist');
        }
        
        $res = '<p> Id :'.$id.'</p>';
        
        $res .= '<p/><a href="' . $this->generateUrl('rooms_index') . '">Back</a>';
        
        return new Response('<html><body>'. $res . '</body></html>');
    }
}
