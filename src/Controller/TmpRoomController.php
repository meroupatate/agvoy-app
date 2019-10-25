<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Room;
use Symfony\Component\HttpFoundation\Response;

class TmpRoomController extends AbstractController
{
    /**
     * @Route("/rooms", name="rooms_index")
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
     * @Route("/rooms", name="rooms")
     */
    public function index()
    {
        return $this->render('room/index.html.twig', [
            'controller_name' => 'TmpRoomController',
        ]);
    }
    
    /**
     * Show a room
     *
     * @Route("room/{id}", name="room_show")
     *
     * @param int $id
     */
    public function show(Room $room)
    {
//         $roomRepo = $this->getDoctrine()->getRepository('App:Room');
//         $room = $roomRepo->findOneBy(['id' => $id]);
        
        if (!$room) {
            throw $this->createNotFoundException('The room does not exist');
        }
        
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
        
        
        
//         $res = '<p> Room #'.$id.'</p>';
//         $res .= '<p> Owner: '.$room->getOwner().'</p>';
//         $res .= '<p> Region: ';
//         $regions = $room->getRegions();
//         foreach ($regions as $region) {
//             $res .= $region;
//         }
//         $res .= '</p>';
//         $res .= '<p> Address: '.$room->getAddress().'</p>';
//         $res .= '<p> Price: '.$room->getPrice().'€</p>';
//         $res .= '<p> Description: '.$room->getDescription().'</p>';
//         $res .= '<p> Capacity: '.$room->getCapacity().' people</p>';
//         $res .= '<p> Superficy: '.$room->getSuperficy().'m²</p>';
        
//         $res .= '<p/><a href="' . $this->generateUrl('rooms_index') . '">Back</a>';
        
//         return new Response('<html><body>'. $res . '</body></html>');
    }

}
