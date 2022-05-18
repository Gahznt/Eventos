<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\SystemEnsalementRooms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SystemEnsalementRoomsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => SystemEnsalementRooms::ROOM_TYPES,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('local', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEnsalementRooms::class
        ]);
    }
}
