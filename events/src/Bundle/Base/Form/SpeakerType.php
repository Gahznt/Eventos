<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Speaker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SpeakerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => [
                    'Nacional' => 0,
                    'Internacional' => 1,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('position', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => empty($builder->getData()->getId()),
                'constraints' => [
                        new File([
                            'maxSize' => '4096k',
                            'mimeTypes' => [
                                'image/gif',
                                'image/jpg',
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image file',
                        ])
                    ] + (empty($builder->getData()->getId()) ? [new NotBlank(['message' => 'NotBlank.default'])] : []),
            ])
            ->add('namePortuguese', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('curriculumLinkPortuguese', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('contentPortuguese', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('nameEnglish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('curriculumLinkEnglish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('contentEnglish', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('nameSpanish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('curriculumLinkSpanish', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('contentSpanish', TextareaType::class, [
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Speaker::class
        ]);
    }
}
