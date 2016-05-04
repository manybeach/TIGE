<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Util\LegacyFormHelper;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'),
                array(
                    'label' => 'Email',
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'class' => 'form-control input-md',
                        'placeholder' => 'Entrez votre email',
                    ),
                )
            )
            ->add(
                'address',
                null,
                array(
                    'label' => 'Adresse',
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'class' => 'form-control input-md',
                        'placeholder' => 'Entrez votre adresse',
                    ),
                )
            )
            ->add(
                'username',
                null,
                array(
                    'label' => 'Username',
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'class' => 'form-control input-md',
                        'placeholder' => 'Entrez votre username',
                    ),
                )
            )
            ->add(
                'plainPassword',
                LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'),
                array(
                    'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array(
                        'label' => 'Mot de passe',
                        'attr' => array(
                            'class' => 'form-control input-md',
                            'placeholder' => 'Entrez votre mot de passe',
                        ),
                    ),
                    'second_options' => array(
                        'label' => 'Confirmation du mot de passe',
                        'attr' => array(
                            'class' => 'form-control input-md',
                            'placeholder' => 'Confirmez votre mot de passe',
                        ),
                    ),
                    'invalid_message' => 'Mot de passe invalide',
                )
            );
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}