<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('eventName', TextType::class, array('label' => 'Nom',
                'attr' => array(
                    'class' => 'form-control input-md')))
            ->add('eventDescription', TextType::class, array('label' => 'Description',
                'attr' => array(
                    'class' => 'form-control input-md')))
            ->add('eventPlace', TextType::class, array('label' => 'Lieu', 'attr' => array(
                'class' => 'form-control input-md')))
            ->add('eventDate', DateType::class, array(
                'input'  => 'datetime',
                'widget' => 'choice',
                'label' => 'Date',
                'attr' => array(
                    'class' => 'form-control input-md')))
            ->add('eventMaxParticipant', IntegerType::class, array('label' => 'Nombre Participants Max',
                'attr' => array(
                    'class' => 'form-control input-md')))
            ->add('save', SubmitType::class, array('label' => "Enregistrer l'événement",
                'attr' => array(
                    'class' => 'btn btn-primary')))
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
