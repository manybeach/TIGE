<?php

namespace MyaccountBundle\Controller;

use AppBundle\Entity\AccountName;
use AppBundle\Entity\Games;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Controller\FavouritesController;

class DefaultController extends Controller
{
    /**
     * default function
     * @return Response
     */
    public function indexAction()
    {
        //recover current user
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $arrayUser = array(
            'id_user' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail()
        );

        //recover all games
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Games');
        $games = $repository->findAll();

        $accountName = array();
        $arrayGames = array();
        foreach ($games as $game) {
            $idGame = $game->getId();
            $arrayGames[$idGame] = $game->getName();

            $repId = $this->getDoctrine()->getManager()->getRepository('AppBundle:AccountName');


            $myIdUser = $repId->findBy(array("user_id" => $user->getId()));
            $myIdGame = $repId->findBy(array("game_id" => $idGame));
            if (!empty($myIdGame) && !empty($myIdUser)) {
                $bool = true;
                foreach ($myIdGame as $objectAccN) {
                    $iduser = $objectAccN->getUserId();
                    $idObjUser = $iduser->getId();
                    $user = $this->get('security.token_storage')->getToken()->getUser();
                    $currentUser = $user->getId();
                    if ($idObjUser == $currentUser) {
                        $accountName[$idGame] = $objectAccN->getName();
                    } else {
                        $accountName[$idGame] = '';
                    }
                }
            } else {
                $accountName[$idGame] = '';
            }
        }

        /* Mathieu.P  - ajout de la liste des favoris sur l'onglet mon compte */
        $maListe = array();
        $mesFavoris = $this->listFavoris();
        foreach($mesFavoris as $favoris) {
            $maSousListe = array();
            $maSousListe['id'] = $favoris->getId();
            $maSousListe['name'] = $favoris->getName();
            $maListe[] = $maSousListe;
            
        }
        return $this->render('MyaccountBundle:Default:index.html.twig', array('user' => $arrayUser,
            'games' => $arrayGames, 'accountName' => $accountName, 'mesFavoris'=> $maListe));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function modAccountAction(Request $request)
    {
        return $this->redirect($this->generateUrl('myaccount_homepage', array('user' => '', 'games' => '', 'accountName' => '')));

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAccountAction(Request $request)
    {

        $idGame = $request->get('id_game');
        $pseudo = $request->get('pseudo');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $idUser = $user->getId();

        $this->addAccountGaming($idUser, $idGame, $pseudo);

        return $this->redirect($this->generateUrl('myaccount_homepage', array('user' => $idUser, 'games' => $idGame, 'accountName' => $pseudo)));
    }

    /**
     * add account
     * @param $idUser
     * @param $idGame
     * @param $pseudo
     * @return bool
     */
    private function addAccountGaming($idUser, $idGame, $pseudo)
    {
        $accountName = $this->getDoctrine()->getManager()->getRepository('AppBundle:AccountName')->myFindOne($idUser);

        $repId = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');

        $accountName = new AccountName();

        $user =
            $this->getDoctrine()
                ->getManager()
                ->find('AppBundle:User', $idUser);
        $game =
            $this->getDoctrine()
                ->getManager()
                ->find('AppBundle:Games', $idGame);

        $accountName->setName($pseudo);
        $accountName->setUserId($user);
        $accountName->setGameId($game);

        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save  (no queries yet)
        $em->persist($accountName);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return true;
    }

    /**
     * @return \AppBundle\Entity\AccountName[]|\AppBundle\Entity\Favourites[]|array
     */
    public function listFavoris()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $id_user = $user->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Favourites');

        $arrayFavourites = $repository->findBy(array(
            'idAccount' => $id_user));

        $result = array();

        if (!empty($arrayFavourites)) {
            $mesfavoris = $arrayFavourites[0]->getIdFavourites();
            $favourites = explode(';', $mesfavoris);
            foreach ($favourites as $favourite) {
                if (!empty($favourite))
                    $result[]=$favourite;
            }
        }
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:AccountName');
        $arrayFavourites = $repository->findBy(array('id'=>$result));
        return $arrayFavourites;

    }
}
