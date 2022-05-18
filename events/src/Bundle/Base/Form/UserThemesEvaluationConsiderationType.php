<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesEvaluationLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserThemesEvaluationConsiderationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'mapped' => false,
                'placeholder' => false,
                'choices' => [
                    'THEME_EVALUATION_STATUS_WAITING' => UserThemes::THEME_EVALUATION_STATUS_WAITING,
                    'THEME_EVALUATION_STATUS_NOT_SELECTED' => UserThemes::THEME_EVALUATION_STATUS_NOT_SELECTED,
                    'THEME_EVALUATION_STATUS_SELECTED' => UserThemes::THEME_EVALUATION_STATUS_SELECTED,
                ],
                'expanded' => true,
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                    ]),
                ],
                // 'data' => 0,
                'required' => false,
            ])
            ->add('reason', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                ],
                'required' => true,
            ])
            ->add('visibleAuthor', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserThemesEvaluationLog::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
