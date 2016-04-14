<?php

namespace MyaccountBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $arrayUser = array(
            'id_user' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail()
        );

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Games');
        $games = $repository->findAll();

        $arrayGames = array();
        foreach($games as $game){
            array_push($arrayGames, $game->getName());
        }


        return $this->render('MyaccountBundle:Default:index.html.twig', array('user' => $arrayUser, 'games'=> $arrayGames));
    }
}