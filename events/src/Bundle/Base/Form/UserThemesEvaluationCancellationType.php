<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\UserThemesEvaluationLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserThemesEvaluationCancellationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserThemesEvaluationLog::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
