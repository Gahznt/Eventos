<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Theme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('initials', TextType::class)
            ->add('editionId', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\Edition',
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => false
            ])
            ->add('ordination', TextType::class)
            ->add('portuguese', TextType::class)
            ->add('english', TextType::class)
            ->add('spanish', TextType::class)
            ->add('descriptionPortuguese', TextareaType::class)
            ->add('descriptionEnglish', TextareaType::class)
            ->add('descriptionSpanish', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Theme::class
        ]);
    }
}
