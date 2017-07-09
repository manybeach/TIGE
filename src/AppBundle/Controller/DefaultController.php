<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\UserAccountForm;

class DefaultController extends Controller
{

    public function homeAction(){
        return $this->render('AppBundle::accueil.html.twig');
    }
    /**
     * @Route("/nnn", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/admin")
     */
    public function adminAction()
    {

        return new Response('<html><body>Admin page!</body></html>');
    }
   /**
     * @return User current idUser
     */
    public function getCurrentUserId()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $idUser = $user->getId();
        return $idUser;
    }
}
