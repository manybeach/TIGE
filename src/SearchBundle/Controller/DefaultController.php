<?php

namespace SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
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

        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from('AppBundle:AccountName', 'p')
            ->where("p.name LIKE :nameAccount")
            ->setParameter('nameAccount', '%'.$searchData.'%');

        $query = $qb->getQuery();
        $allName = $query->getResult();

        $arrayAllPlayer = array();

        foreach($allName as $name)
        {
            $arrayAllPlayer[$name->getUserId()->getId()] = $name->getName();
        }
        return $this->render("SearchBundle::index.html.twig",array("dataSearch"=>$arrayAllPlayer));
    }

}
