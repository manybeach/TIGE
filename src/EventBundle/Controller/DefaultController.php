<?php

namespace EventBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        //Récupération des évenements dans un tableau
        $arrayEvents = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findAll();

        //Gestion du formulaire d'ajout d'évenement
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//                $repository = $this
//                    ->getDoctrine()
//                    ->getManager()
//                    ->getRepository('AppBundle:Games');

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save the Product (no queries yet)
            $em->persist($event);

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirectToRoute('event_homepage');
        }

        return $this->render('@Event/Default/index.html.twig', array(
            'form' => $form->createView(),
            'arrayEvents' => $arrayEvents,
        ));
    }
}