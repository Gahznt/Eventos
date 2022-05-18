<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\EditionDiscount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditionDiscountType
 *
 * @package App\Bundle\Base\Form
 */
class EditionDiscountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userIdentifier', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'doc.not_blank',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('percentage', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Select',
                'expanded' => false,
                'choice_translation_domain' => 'messages',
                'choices' => EditionDiscount::TYPES,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditionDiscount::class,
        ]);
    }
}
