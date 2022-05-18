<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('namePortuguese', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('titlePortuguese', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('descriptionPortuguese', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('nameEnglish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('titleEnglish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('descriptionEnglish', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('nameSpanish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('titleSpanish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('descriptionSpanish', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('status', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'STATUS_ACTIVE' => 1,
                    'STATUS_INACTIVE' => 0,
                ],
                'choice_translation_domain' => 'messages',
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('isHomolog', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isShowPreviousEventsHome', CheckboxType::class, [
                'required' => false,
            ])
            ->add('numberWords', NumberType::class, [
                'required' => true,
            ])
            ->add('issn', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('divisions', EntityType::class, [
                'class' => Division::class,
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                //'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Count([
                        'min' => 1,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
