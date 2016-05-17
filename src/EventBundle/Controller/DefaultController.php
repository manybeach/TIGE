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
        //recover current user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        //recover event into array
        $arrayEvents = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findAll();

        $sortArrayEvents = $this->sortEventAction($arrayEvents);

        $arrayEventsUser = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('eventOwner' => $userId));

        $sortArrayEventsUser = $this->sortEventAction($arrayEventsUser);

        $arrayMembersName = $this->getMembersName($userId);

        //recover event where the current user is in
        $arrayEventUserParticipation = array();
        foreach ($arrayEvents as $event) {
            $arrayMembers = explode(";", $event->getEventMembers());
            foreach ($arrayMembers as $member) {
                if ($member == $userId) {
                    $arrayEventUserParticipation[] = $event;
                }
            }
        }

        $event = new Event();

        $event->setEventDate(new \DateTime('tomorrow'));
        $time = time();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        //action to do when form was validate
        if ($form->isSubmitted() && $form->isValid()) {

            $event->setEventOwner($userId);
            $event->setEventNbParticipants(1);

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save  (no queries yet)
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

    /**
     * @param $idEvent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addMemberAction($idEvent)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('id' => $idEvent));

        $event[0]->addMember($userId);

        //maj DataBase
        $em = $this->getDoctrine()->getManager();
        $em->persist($event[0]);
        $em->flush();

        return $this->redirect($this->generateUrl('event_homepage'));

    }

    /**
     * @param $idEvent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprMemberAction($idEvent)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();

        $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('id' => $idEvent));


        $event[0]->supprMember($userId);

        //MAJ DATABASE
        $em = $this->getDoctrine()->getManager();
        $em->persist($event[0]);
        $em->flush();

        return $this->redirect($this->generateUrl('event_homepage'));

    }

    /**
     * Recover MembersName
     * @param $userId
     * @return array
     */
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

        $arrayEvents = $eventRepository->findAll();

        $result = array();

        foreach ($arrayEvents as $event) {

            $arrayMembersName = array();

            $arrayMembersName[0] = $event->getId();
            //recover event where current user is
            $idOwner = $event->getEventOwner();
            $accountNameOwner = $accountNameRepository->findBy(array('id' => $idOwner));
            $arrayMembersName[$idOwner] = $accountNameOwner[0]->getUsername();

            $participate = false;

            //if player are in the event
            if ($event->getEventMembers() != null) {
                //recover all member of the event
                $arrayMembersId = explode(";", $event->getEventMembers());

                foreach ($arrayMembersId as $idMember) {
                    if ($idMember == $userId) {
                        $participate = true;
                    }
                }
            }

            $arrayMembersName[] = $participate;

            if ($event->getEventMembers() != null) {
                $arrayMembersId = explode(";", $event->getEventMembers());

                foreach ($arrayMembersId as $idMember) {
                    $accountName = $accountNameRepository->findBy(array(
                        'id' => $idMember));
                    $arrayMembersName[$idMember] = $accountName[0]->getUsername();
                }
            }
            $result[] = $arrayMembersName;
        }

        return $result;
    }

    /**
     * @param $idEvent
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprEventAction($idEvent){

        //recover event we want to del
        $event = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event')->findBy(array('id' => $idEvent));

        $em = $this->getDoctrine()->getManager();
        $em->remove($event[0]);
        $em->flush();

        return $this->redirect($this->generateUrl('event_homepage'));
    }

    /**
     * sort event
     * @param $arrayEvents
     * @return mixed
     */
    public function sortEventAction($arrayEvents)
    {
        $arrayEventDates = array();

        foreach ($arrayEvents as $event) {
            $idDateEvent = array();
            $idDateEvent[] = $event->getEventDate()->getTimestamp();
            $arrayEventDates[] = $idDateEvent;
        }

        array_multisort($arrayEventDates, SORT_ASC, $arrayEvents);

        return $arrayEvents;
    }
}