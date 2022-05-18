<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\City;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Repository\CityRepository;
use Doctrine\ORM\EntityRepository;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Bundle\Base\Services\City as CityService;

class UserSimplifiedType extends AbstractType
{
    /**
     * @var CityService
     */
    private CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recordType', ChoiceType::class, [
                'choices' => User::USER_RECORD_TYPE,
                'constraints' => [
                    new NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                ]
            ])
            ->add('country', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\Country',
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                ]
            ])
            ->add('identifier', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                ]
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'NotBlank.default'
                    ])
                ]
            ])
            ->add('birthday', DateType::class, [
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false,
                'required' => true,
                'invalid_message' => 'birthday.invalid',
                'constraints' => [
                    new NotBlank([
                        'message' => 'birthday.not_blank',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'dd/mm/aaaa',
                ],
            ])
            ->add('zipcode', TextType::class)
            ->add('street', TextType::class)
            ->add('number', NumberType::class)
            ->add('complement', TextType::class)
            ->add('neighborhood', TextType::class)
            ->add('state', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\State',
                'required' => false,
                'mapped' => false,
                'data' => $options['data']->getCity() ? $options['data']->getCity()->getState() : null,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'Select',
            ])
            ->add('city', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'placeholder' => 'Select',
            ])
            ->add('email', EmailType::class)
            ->add('cellphone', NumberType::class)
            ->add('phone', NumberType::class)
            ->add('extension', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
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
            ])
            ->add('institutionsPrograms', UserInstitutionsProgramsType::class, [
                'label' => false,
            ])
            ->add('newsletterAssociated', CheckboxType::class)
            ->add('newsletterEvents', CheckboxType::class)
            ->add('newsletterPartners', CheckboxType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!isset($data['city'])) {
            return;
        }

        $form->add('city', EntityType::class, [
            'class' => City::class,
            'query_builder' => function (EntityRepository $er) use ($data) {
                return $er->createQueryBuilder('c')
                    ->where('c.id =' . $data['city']);
            },
            'data' => $this->cityService->getCityById($data['city']),
            'label' => false,
            'required' => false
        ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}