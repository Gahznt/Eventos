<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\SystemEnsalementSessions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SystemEnsalementSessionsType extends AbstractType
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
                'choices' => SystemEnsalementSessions::SESSION_TYPES,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('date', DateType::class, [
                'format' => 'dd/MM/yyyy',
                'attr' => ['placeholder' => 'dd/mm/aaaa'],
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('start', TimeType::class, [
                // 'format' => 'HH:mm',
                'attr' => ['placeholder' => 'hh:ss'],
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('end', TimeType::class, [
                // 'format' => 'HH:mm',
                'attr' => ['placeholder' => 'hh:ss'],
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[start].data',
                        'message' => 'date.greater_than_start',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEnsalementSessions::class
        ]);
    }
}
