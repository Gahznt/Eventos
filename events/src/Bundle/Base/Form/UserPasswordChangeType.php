<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\User;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserPasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'password.invalid',
                'first_options' => [
                    'always_empty' => false,
                ],
                'second_options' => [
                    'always_empty' => false,
                ],
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                    new NotBlank([
                        'message' => 'password.not_blank',
                    ]),
                    new PasswordRequirements([
                        'missingNumbersMessage' => 'password.with_number',
                        'requireNumbers' => true,
                        'minLength' => 8,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => User::class,
        ]);
    }
}
