<?php

namespace SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use StatsBundle\StatsBundle;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('SearchBundle::index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchPlayerAction(Request $request)
    {
        $searchData = $request->get('searchData');
        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from('AppBundle:AccountName', 'p')
            ->where("p.name LIKE :nameAccount")
            ->setParameter('nameAccount', '%'.$searchData.'%');

        $query = $qb->getQuery();
        $allName = $query->getResult();

        $arrayAllPlayer = array();
        $arrayFollow    = array();
        foreach($allName as $name)
        {
            $arrayAllPlayer[$name->getUserId()->getId()] = $name->getName();
            $fav = $this->isFavourite($name->getUserId()->getId());
            $arrayFollow[] = $fav;
        }
        return $this->render("SearchBundle::index.html.twig",array("dataSearch"=>$arrayAllPlayer,'fav'=>$arrayFollow));
    }

    /**
     * @param $idUser
     * @return bool
     */
    public function isFavourite($idUser)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $id_user = $user->getId();

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Favourites');

        $arrayFavourites = $repository->findBy(array('idAccount' => $id_user));
        $result = false;

        if (!empty($arrayFavourites)) {
            $mesfavoris = $arrayFavourites[0]->getIdFavourites();
            $favourites = explode(';', $mesfavoris);
            foreach ($favourites as $favourite) {
                if ($idUser == $favourite) $result = true;
            }
        }
        return $result;
    }

}
