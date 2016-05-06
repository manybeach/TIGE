<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eventName', TextType::class, array('label' => 'Nom'))
            ->add('eventDescription', TextType::class, array('label' => 'Description'))
            ->add('eventPlace', TextType::class, array('label' => 'Lieu'))
            ->add('eventDate', DateType::class, array('label' => 'Date'))
            ->add('eventMembers', TextType::class, array('label' => 'Membres'))
            ->add('save', SubmitType::class, array('label' => "Enregistrer l'événement"))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Event'
        ));
    }
}
