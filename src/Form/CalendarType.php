<?php

namespace App\Form;

use DateTime;
use App\Entity\Calendar;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\Time;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categorie', EntityType::class, ['class'=> Categorie::class, 'placeholder' => 'Choisissez un studio', 'choice_label'=>'nom', 'required'   => true])
            ->add('title', TextType::class, ['label'=>'Prénom et Nom', 'attr' => ['placeholder' => 'Votre Nom et Prénom','required'   => true]])
            ->add('start', DateTimeType::class, ['input'=>'datetime','date_widget' => 'single_text', 'time_widget' => 'single_text','placeholder' => "yyyy-mm-dd"])
            ->add('end', DateTimeType::class, ['input'=>'datetime','date_widget' => 'single_text', 'time_widget' => 'single_text', 'placeholder' => "yyyy-mm-dd"])
            ->add('description')
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}

