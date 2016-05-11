<?php

namespace EventBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        //Récupération de l'utilisateur connecté
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        //Récupération des évenements dans un tableau
        $arrayEvents = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findAll();

        //Récupération des évenements créés par le joueur connecté
        $arrayEventsUser = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('eventOwner' => $userId));

        //Récupération des noms des participants
        $arrayMembersName = $this->getMembersName();

        //Récupération des évenements auxquels le joueur connecté participe
        $arrayEventUserParticipation = array();
        foreach ($arrayEvents as $event) {
            $arrayMembers = explode(";", $event->getEventMembers());
            foreach ($arrayMembers as $member) {
                if ($member == $userId) {
                    $arrayEventUserParticipation[] = $event;
                }
            }
        }

        //Gestion du formulaire d'ajout d'évenement
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event->setEventOwner($userId);
            $event->setEventNbParticipants(1);

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
            'arrayEventsUser' => $arrayEventsUser,
            'arrayEventUserParticipation' => $arrayEventUserParticipation,
            'arrayMembersName' => $arrayMembersName,
            'idUser' => $userId,
        ));
    }

    public function addParticipantAction($idEvent)
    {
        //Récupération de l'utilisateur connecté
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        //Récupération des évenements créés par le joueur connecté
        $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('id' => $idEvent));

        $event[0]->addMembers($userId);

        $em = $this->getDoctrine()->getManager();
        $em->persist($event[0]);
        $em->flush();

        return $this->redirect($this->generateUrl('event_homepage'));

    }

    public function getMembersName()
    {

        $eventRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Event');

        $accountNameRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:AccountName');

        $arrayEvents = $eventRepository->findAll();

        $result = array();

        foreach ($arrayEvents as $event) {

            $arrayMembersName = array();
            $arrayMembersName[0] = $event->getId();

            $idOwner = $event->getEventOwner();
            $accountNameOwner = $accountNameRepository->findBy(array('user_id' => $idOwner));
            $arrayMembersName[$idOwner] = $accountNameOwner[0]->getName();

            if ($event->getEventMembers() != null) {
                $arrayMembersId = explode(";", $event->getEventMembers());

                foreach ($arrayMembersId as $idMember) {
                    $accountName = $accountNameRepository->findBy(array(
                        'user_id' => $idMember));
                    $arrayMembersName[$idMember] = $accountName[0]->getName();
                }
            }
            $result[] = $arrayMembersName;
        }
        return $result;
    }
}