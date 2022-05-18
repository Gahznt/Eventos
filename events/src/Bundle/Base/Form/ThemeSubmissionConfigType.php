<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class ThemeSubmissionConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new GreaterThanOrEqual([
                        'value' => date('Y'),
                        'message' => 'Deve ser igual ou superior a ' . date('Y'),
                    ]),
                    new LessThanOrEqual([
                        'value' => date('Y') + 10,
                        'message' => 'Deve ser igual ou inferior a ' . date('Y'),
                    ]),
                ],
            ])
            ->add('isCurrent', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isAvailable', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isEvaluationAvailable', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isResultAvailable', CheckboxType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ThemeSubmissionConfig::class,
        ]);
    }
}
