<?php

namespace TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $leagueController = new LeagueController();
        $hotsController = new HotsController();
        $arrayDataLol = array();
        
        $objHotsId = $this->getDoctrine()->getManager()->getRepository('AppBundle:Games');
        $arrayHotsId = $objHotsId->findBy(array('name' => 'Heroes Of The Storm'));
        $hotsId = $arrayHotsId[0]->getId();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $currentUser = $user->getId();
        $objHotsName = $this->getDoctrine()->getManager()->getRepository('AppBundle:AccountName');
        $hotsName = $objHotsName->findBy(array('game_id' => $hotsId));
        /* faire une boucle sur chaque joueur $hotsName[0]*/
        $hotsAccount = $hotsName[0]->getName();
        $hotsId = $hotsName[0]->getId();
        
        $arrayDataLol = $leagueController->getDataFromLolAction($request, $currentUser, $this);

        if (!empty($hotsAccount)) {
            $arrayDataHots = $hotsController->getDataFromHots($hotsAccount, $hotsId, $hotsId);
        }

        $gamesLol           = $arrayDataLol['AllData'];
        $arrayDataAllGame   = array_merge($arrayDataHots, $gamesLol);
        $arraySorted        = $this->sortGames($arrayDataAllGame);

        $arrayCommentAndThread  = array();
        $comments               = array();
        $threads                = array();

        foreach($arraySorted as $arrayGame)
        {
            $arrayCommentAndThread = $this->somethingAction($request, $arrayGame["idComm"]);
            array_push($comments, $arrayCommentAndThread['comments']);
            array_push($threads, $arrayCommentAndThread['threads']);

        }
        return $this->render('TimelineBundle:Default:index.html.twig', array('AllData' => $arraySorted, 'comments' => $comments, 'thread' => $threads));

    }

    /**
     * @param $arrayGames
     * @return mixed
     */
    private function sortGames($arrayGames)
    {
        $tmp = Array();
        foreach ($arrayGames as $ma) {
            $tmp[] = $ma["createDate"];
        }

        array_multisort($tmp, SORT_DESC, $arrayGames);
        for($i = 0; $i<count($arrayGames);$i++){
           $arrayGames[$i]['createDate'] =  date('d/m/Y H:i:s',$arrayGames[$i]['createDate']);
        }
        return $arrayGames;
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function somethingAction(Request $request, $id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->getUri());

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        return array('comments' => $comments, 'threads' => $thread);
    }
}