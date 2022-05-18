<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('identifier', TextType::class)
            ->add('email', EmailType::class)
            ->add('nickname', TextType::class)
            ->add('birthday', DateType::class)
            ->add('brazilian', CheckboxType ::class)
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            ->add('nickname', TextType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
            ])
            ->add('zipcode', TextType::class)
            ->add('street', TextType::class)
            ->add('number', NumberType::class)
            ->add('complement', TextType::class)
            ->add('neighborhood', TextType::class)
            ->add('phone', NumberType::class)
            ->add('cellphone', NumberType::class)
            ->add('zipcode', TextType::class)
            ->add('street', TextType::class)
            ->add('portuguese', CheckboxType::class)
            ->add('english', CheckboxType::class)
            ->add('spanish', CheckboxType::class)
            ->add('newsletterAssociated', CheckboxType::class)
            ->add('newsletterEvents', CheckboxType::class)
            ->add('newsletterPartners', CheckboxType::class)
            ->add('recordType', CheckboxType::class)
            ->add('brazilian', CheckboxType::class)
            ->add('status', CheckboxType::class)
            ->add('country', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\Country',
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => false
            ])
            ->add('recordType', ChoiceType::class, [
                'choices'=> User::USER_RECORD_TYPE
                ]
            )
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}