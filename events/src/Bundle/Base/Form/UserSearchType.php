<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Entity\UserSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserSearchType
 * @package App\Bundle\Base\Form
 */
class UserSearchType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'search';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->
        add('since', DateType::class, [
            'format' => 'dd/MM/yyyy',
            'widget' => 'single_text',
            'html5' => false,
            'required' => false
        ])
        ->add('thru', DateType::class, [
            'format' => 'dd/MM/yyyy',
            'widget' => 'single_text',
            'html5' => false,
            'required' => false
        ])
        ->add('levels', ChoiceType::class, [
            'required' => false,
            'placeholder' => 'Select',
            'choices' => UserAssociation::USER_ASSOCIATIONS_LEVEL,
            'choice_translation_domain' => 'messages',
        ])
        ->add('payment', ChoiceType::class, [
            'required' => false,
            'placeholder' => 'Select',
            'choices' => UserAssociation::USER_PAYMENT_FILTER,
            'choice_translation_domain' => 'messages',
        ])
        ->add('paymentDays', ChoiceType::class, [
            'required' => false,
            'placeholder' => 'Select',
            'choices' => UserAssociation::USER_PAYMENT_DAYS_FILTER,
            'choice_translation_domain' => 'messages',
        ])
        ->add('search', TextType::class, [
            'required' => false
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Search',
            'attr' => ['style' => 'display:none;']
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSearch::class,
            'method' => 'get',
            'csrf_protection' => false,
            'attr' => ['class' => 'row', 'novalidate' => 'novalidate', 'id' => 'userFormSearch'],
        ]);
    }
}
