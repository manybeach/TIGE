<?php
/**
 * Created by PhpStorm.
 * User: Anne-So
 * Date: 26/04/2016
 * Time: 09:55
 */

namespace AppBundle\Controller;
use AppBundle\Entity\Favourites;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class FavouritesController extends Controller
{
    public function addfavouritesAction($id_favoris){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $id_user = $user->getId();

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Favourites');

        $arrayFavourites = $repository->findBy(array(
            'idAccount' => $id_user));

        if (empty($arrayFavourites[0])){
            $favourites = new Favourites();
            $favourites->setIdAccount($user);
        }
        else
            $favourites = $arrayFavourites[0];

        $favourites->setIdFavourites($id_favoris);

        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($favourites);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();
        return new Response();
}

}