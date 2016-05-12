<?php

// src/OC/PlatformBundle/Controller/AdvertController.php


namespace MyaccountBundle\Controller;


use AppBundle\Entity\AccountName;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class AccountNameController extends Controller

{

    public function addAction(Request $request)

    {


        // On crée un objet Advert

        $accountname = new AccountName();


        // On crée le FormBuilder grâce au service form factory

        $formBuilder = $this->get('form.factory')->createBuilder('form', $accountname);


        // On ajoute les champs de l'entité que l'on veut à notre formulaire

        $formBuilder

            ->add('date',      'date')

            ->add('title',     'text')

            ->add('content',   'textarea')

            ->add('author',    'text')

            ->add('published', 'checkbox')

            ->add('save',      'submit')

        ;



        // À partir du formBuilder, on génère le formulaire

        $form = $formBuilder->getForm();


        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule

        return $this->render('MyaccountBundle:AccountName:index.html.twig', array(
            'form' => $form->createView(),

        ));

    }

}