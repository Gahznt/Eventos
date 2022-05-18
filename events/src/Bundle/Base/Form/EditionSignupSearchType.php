<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\EditionPaymentMode;
use App\Bundle\Base\Entity\EditionSignup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditionSignupSearchType
 *
 * @package App\Bundle\Base\Form
 */
class EditionSignupSearchType extends AbstractType
{
    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'search';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choice_translation_domain' => 'messages',
                'choices' => [
                    'Todos' => '',
                    'Quitado' => EditionSignup::EDITION_SIGNUP_STATUS_PAID,
                    'Pendente' => EditionSignup::EDITION_SIGNUP_STATUS_NOT_PAID,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
            ])
            ->add('mode', ChoiceType::class, [
                'choice_translation_domain' => 'messages',
                'choices' => ['Todos' => ''] + EditionPaymentMode::INITIALS,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
            ])
            ->add('q', TextType::class, [
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => EditionSignupSearch::class,
            // 'method' => 'get',
            // 'csrf_protection' => false,
            // 'attr' => ['class' => 'row', 'novalidate' => 'novalidate'],
        ]);
    }
}
