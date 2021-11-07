<?php

namespace App\Form;

use App\Entity\Artiste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArtisteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, ['label' => "Modifier la photo", 'data_class' => null, 'required' => false, 'empty_data' => ''])
            ->add('nom', TextType::class, ['label' => "Nom:"])
            ->add('role', TextType::class, ['label' => "Rôle:"])
            ->add('age', TextType::class, ['label' => "Âge:"])
            ->add('description', TextareaType::class, ['label' => "Description:"])
            ->add('urlinsta', UrlType::class, ['label' => "Instagram: ", 'required' => false])
            ->add('urlyoutube', UrlType::class, ['label' => " Youtube: ", 'required' => false])
            ->add('soumettre', SubmitType::class, ['label' => " Envoyer "]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artiste::class,
        ]);
    }
}
