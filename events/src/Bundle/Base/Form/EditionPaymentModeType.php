<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\EditionPaymentMode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditionPaymentModeType
 *
 * @package App\Bundle\Base\Form
 */
class EditionPaymentModeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('value', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Select',
                'expanded' => true,
                'choice_translation_domain' => 'messages',
                'choices' => EditionPaymentMode::TYPES,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('initials', ChoiceType::class, [
                'placeholder' => 'Select',
                'expanded' => false,
                'choice_translation_domain' => 'messages',
                'choices' => EditionPaymentMode::INITIALS,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])

            ->add('hasFreeIndividualAssociation', CheckboxType::class, [
                // 'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditionPaymentMode::class,
        ]);
    }
}
