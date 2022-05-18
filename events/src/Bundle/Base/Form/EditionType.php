<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Edition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('color', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => [
                    'Azul' => 'bggBlue',
                    'Azul Brilhante' => 'bggBlueVivid',
                    'Azul Claro' => 'bggLBlueLight',
                    'Roxo' => 'bggPurple',
                    'Verde' => 'bggGreen',
                    'Verde Brilhante' => 'bggGreenVivid',
                    'Marrom' => 'bggBrown',
                    'Rosa' => 'bggRose',
                    'Pink' => 'bggPink',
                    'Cinza' => 'bggGray',
                    'Amarelo' => 'bggYellow',
                    'Vermelho' => 'bggRed',
                    'Laranja' => 'bggOrange',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('place', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('dateStart', DateType::class, [
                'format' => 'dd/MM/yyyy',
                'attr' => ['placeholder' => 'dd/mm/aaaa'],
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                        new NotBlank(['message' => 'NotBlank.default']),
                        //new Date(),
                    ] + (empty($builder->getData()->getId()) ? [
                        new GreaterThanOrEqual([
                            'value' => new \DateTime('now'),
                            'message' => 'date.greater_than_or_equal_today',
                        ]),
                    ] : []),
            ])
            ->add('dateEnd', DateType::class, [
                'format' => 'dd/MM/yyyy',
                'attr' => ['placeholder' => 'dd/mm/aaaa'],
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    //new Date(),
                    new GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[dateStart].data',
                        'message' => 'date.greater_than_or_equal_start',
                    ]),
                ],
            ])
            ->add('signupDeadline', DateType::class, [
                'format' => 'dd/MM/yyyy',
                'attr' => ['placeholder' => 'dd/mm/aaaa'],
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    //new Date(),
                ],
            ])
            ->add('namePortuguese', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('longnamePortuguese', TextType::class, [
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
            ->add('longnameEnglish', TextType::class, [
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
            ->add('longnameSpanish', TextType::class, [
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
                    'Ativo' => 1,
                    'Inativo' => 0,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('isHomolog', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isShowHome', CheckboxType::class, [
                'required' => true,
            ])
            ->add('homePosition', NumberType::class, [
                'required' => false,
            ])
            ->add('workload', TextType::class, [
                'required' => false,
            ])
            ->add('voluntaryWorkload', TextType::class, [
                'required' => false,
            ])
            ->add('certificateLayoutPath', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpg',
                                'image/jpeg',
                            ],
                            'mimeTypesMessage' => 'Formato inválido',
                        ]),
                    ] + (empty($builder->getData()->getId()) ? [new NotBlank(['message' => 'NotBlank.default'])] : []),
            ])
            ->add('certificateQrcodeSize', NumberType::class, [
                'required' => false,
            ])
            ->add('certificateQrcodePositionRight', NumberType::class, [
                'required' => false,
            ])
            ->add('certificateQrcodePositionBottom', NumberType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Edition::class,
        ]);
    }
}
