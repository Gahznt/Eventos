<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\SystemEvaluationAveragesSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SystemEvaluationAveragesType
 * @package App\Bundle\Base\Form
 */
class SystemEvaluationAveragesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('primary', NumberType::class, [
                'mapped' => true,
                'scale' => 2,
                'html5' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],

            ])
            ->add('secondary', NumberType::class, [
                'mapped' => true,
                'scale' => 2,
                'html5' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('saved', HiddenType::class, [
                'mapped' => true
            ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEvaluationAveragesSearch::class,
            'method' => 'post',
            'attr' => ['class' => 'row', 'novalidate' => 'novalidate', 'id' => 'systemEvaluationAveragesFormSearch'],
        ]);
    }
}
