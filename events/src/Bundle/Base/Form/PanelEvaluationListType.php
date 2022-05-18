<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\PanelEvaluationList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PanelEvaluationListType
 * @package App\Bundle\Base\Form
 */
class PanelEvaluationListType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('divisionId', EntityType::class, [
                'class' => Division::class,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default'])
                ],
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ])
            ->add('id', EntityType::class, [
                'class' => Panel::class,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default'])
                ],
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ])
            ->add('statusEvaluation', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choices' => Panel::PANEL_EVALUATION_STATUS,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default'])
                ],
            ])
            ->add('search', TextType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['style' => 'display:none;']
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PanelEvaluationList::class,
            'method' => 'get',
            'attr' => ['novalidate' => 'novalidate', 'id' => 'themeListForm'],
        ]);
    }
}