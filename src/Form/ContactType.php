<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class, [
                'label' => 'Adresse email'
            ])

            ->add('objet',ChoiceType::class, [
                'label' => 'Objet', 'choices' => [
                    'Annulation ou modification d\'un rendez-vous'=>1,
                    'Studio musique'=>2,
                    'Studio photo'=>3,
                    'Renseignements'=>4,
                    'Autre...'=>5]
            ])

            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['rows' => 5],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}