<?php

namespace App\Form;

use App\Entity\SocialLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SocialLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md mb-3',
                    'autocomplete' => 'off',
                    'placeholder' => 'exemple : Facebook'
                ]
            ])
            ->add('link', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md mb-3',
                    'autocomplete' => 'off',
                    'placeholder' => 'https://atelier-sahaza.facebook.com'
                ]
            ])
            ->add('icon', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md mb-3',
                    'autocompete' => 'off',
                    'placeholder' => 'exemple : bi bi-facebook'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialLink::class,
        ]);
    }
}
