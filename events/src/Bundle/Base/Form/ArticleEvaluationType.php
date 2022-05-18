<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\SystemEvaluation;
use App\Bundle\Base\Entity\UserArticles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleEvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('criteriaOne', ChoiceType::class,[
                'choices' => SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaTwo', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaThree', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaFour', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaFive', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaSix', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaSeven', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaEight', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaNine', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaTen', ChoiceType::class,[
                'choices' =>SystemEvaluation::CRITERIA_OPTIONS,
                'expanded' => true
            ])
            ->add('criteriaFinal', ChoiceType::class,[
                'placeholder' => 'Select',
                'translation_domain' => 'messages',
                'choices' =>SystemEvaluation::CRITERIA_PRIMARY_OPTIONS,
            ])
            ->add('justification', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEvaluation::class
        ]);
    }
}
