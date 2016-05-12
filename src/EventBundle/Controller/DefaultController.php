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
        //Récupération de l'utilisateur connecté
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        //Récupération des évenements dans un tableau
        $arrayEvents = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findAll();

        //Tri des événements par date
        $sortArrayEvents = $this->sortEventAction($arrayEvents);

        //Récupération des évenements créés par le joueur connecté
        $arrayEventsUser = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('eventOwner' => $userId));

        //Tri des événements de l'utilisateur par date
        $sortArrayEventsUser = $this->sortEventAction($arrayEventsUser);

        //Récupération des noms des participants
        $arrayMembersName = $this->getMembersName($userId);

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

        //Date du jour par défaut
        $event->setEventDate(new \DateTime('tomorrow'));

        //Date du jour
        $time = time();

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        //Action lors de la validation du form
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
            'arrayEvents' => $sortArrayEvents,
            'arrayEventsUser' => $sortArrayEventsUser,
            'arrayEventUserParticipation' => $arrayEventUserParticipation,
            'arrayMembersName' => $arrayMembersName,
            'idUser' => $userId,
            'today' => $time,
        ));
    }

    public function addMemberAction($idEvent)
    {
        //Récupération de l'utilisateur connecté
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        //Récupération de l'événement auquel le joueur souhaite participer
        $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('id' => $idEvent));

        //Ajout du joueur comme membre de l'événement
        $event[0]->addMember($userId);

        //Mise à jour de la base de données
        $em = $this->getDoctrine()->getManager();
        $em->persist($event[0]);
        $em->flush();

        return $this->redirect($this->generateUrl('event_homepage'));

    }

    public function supprMemberAction($idEvent)
    {
        //Récupération de l'utilisateur connecté
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        //Récupération de l'événement auquel le joueur souhaite ne plus participer
        $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('id' => $idEvent));

        //Suppression du joueur comme membre de l'événement
        $event[0]->supprMember($userId);

        //Mise à jour de la base de données
        $em = $this->getDoctrine()->getManager();
        $em->persist($event[0]);
        $em->flush();

        return $this->redirect($this->generateUrl('event_homepage'));

    }

    public function getMembersName($userId)
    {

        $eventRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Event');

        $accountNameRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:User');

        //Stockage des événements dans un tableau
        $arrayEvents = $eventRepository->findAll();

        //Tableau final qui contiendra les noms des membres pour chaque événement
        $result = array();

        foreach ($arrayEvents as $event) {

            //Tableau destiné à contenir les noms des membres du l'événement en cours
            $arrayMembersName = array();

            //Ajout de l'id de l'événement
            $arrayMembersName[0] = $event->getId();

            //Ajout du nom du créateur
            $idOwner = $event->getEventOwner();
            $accountNameOwner = $accountNameRepository->findBy(array('id' => $idOwner));
            $arrayMembersName[$idOwner] = $accountNameOwner[0]->getUsername();

            $participate = false;

            //Si des joueurs sont inscrits à l'événement
            if ($event->getEventMembers() != null) {
                //On stocke les id des membres dans un tableau
                $arrayMembersId = explode(";", $event->getEventMembers());

                //Si le joueur connecté fait parti des membres de l'événement, on passe la variable participate à true
                foreach ($arrayMembersId as $idMember) {
                    if ($idMember == $userId) {
                        $participate = true;
                    }
                }
            }

            //On ajoute la participation du joueur connecté
            $arrayMembersName[] = $participate;

            //Si des joueurs sont inscrits à l'événement
            if ($event->getEventMembers() != null) {
                //On stocke les id des membres dans un tableau
                $arrayMembersId = explode(";", $event->getEventMembers());

                //Pour chaque membre, on ajoute son nom dans le tableau
                foreach ($arrayMembersId as $idMember) {
                    $accountName = $accountNameRepository->findBy(array(
                        'id' => $idMember));
                    $arrayMembersName[$idMember] = $accountName[0]->getUsername();
                }
            }
            //On ajoute le tableau contenant les noms des membres dans le tableau final
            $result[] = $arrayMembersName;
        }

        return $result;
    }

    public function supprEventAction($idEvent){

        //Récupération de l'événement que le joueur souhaite supprimer
        $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('id' => $idEvent));

        //Suppression de l'événement
        $em = $this->getDoctrine()->getManager();
        $em->remove($event[0]);
        $em->flush();

        return $this->redirect($this->generateUrl('event_homepage'));
    }

    public function sortEventAction($arrayEvents)
    {
        //Tableau qui contiendra les dates des événements dans le format timestamp
        $arrayEventDates = array();

        //On ajoute dans ce tableau la date de chaque événement
        foreach ($arrayEvents as $event) {
            $idDateEvent = array();
            $idDateEvent[] = $event->getEventDate()->getTimestamp();
            $arrayEventDates[] = $idDateEvent;
        }

        //On trie les événements (arrayEvents) en fonction de leur date (arrayEventDates)
        array_multisort($arrayEventDates, SORT_ASC, $arrayEvents);

        return $arrayEvents;
    }
}