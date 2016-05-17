<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Games;
use AppBundle\Form\GamesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GamesController extends Controller
{
    /**
     * Create form for games
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $game = new Games();
        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                // tells Doctrine you want to (eventually) save the Product (no queries yet)
                $em->persist($game);
                // actually executes the queries (i.e. the INSERT query)
                $em->flush();
                return $this->redirectToRoute('myaccount_homepage');
        }

        return $this->render('game.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}