<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\City;
use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\Method;
use App\Bundle\Base\Entity\State;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Repository\CityRepository;
use App\Bundle\Base\Repository\StateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    /**
     * @var int
     */
    public static $step = 0;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param FormBuilderInterface $form
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $form, array $options)
    {
        $this->setOptions($options);

        $form->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $form->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var User $entity */
        $entity = $event->getData();

        $data = [];

        if ($entity && $entity->getCity()) {
            $data['country'] = $entity->getCity()->getCountry()->getId();
            $data['state'] = $entity->getCity()->getState()->getId();
        }

        $this->addElements($form, $data);

        // campos não mapeados
        if ($entity && $entity->getCity()) {
            $form->get('country')->setData($entity->getCity()->getCountry());
            $form->get('state')->setData($entity->getCity()->getState());
        }
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $data['phone'] = preg_replace('/[^0-9]*/', '', $data['phone']);
        $data['cellphone'] = preg_replace('/[^0-9]*/', '', $data['cellphone']);

        $event->setData($data);

        $this->addElements($form, $data);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     */
    protected function addElements(FormInterface $form, $data = [])
    {
        $form
            ->add('country', EntityType::class, [
                'required' => true,
                'class' => Country::class,
                'mapped' => false,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'Select',
                'constraints' => self::$step > 0 ? [
                    new NotBlank([
                        'message' => 'country.not_blank',
                    ]),
                ] : [],
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => self::$step > 0 ? [
                    new NotBlank([
                        'message' => 'name.not_blank',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ] : [],
            ])
            ->add('birthday', DateType::class, [
                'required' => true,
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false,
                'invalid_message' => 'birthday.invalid',
                'constraints' => self::$step > 0 ? [
                    new NotBlank([
                        'message' => 'birthday.not_blank',
                    ]),
                ] : [],
                'attr' => [
                    'placeholder' => 'dd/mm/aaaa',
                ],
            ])
            ->add('zipcode', TextType::class, [
                'required' =>
                    isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'],
                'constraints' =>
                    self::$step > 0
                    && isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'] ?
                        [
                            new NotBlank([
                                'message' => 'zipcode.not_blank',
                            ]),
                            new Length([
                                'max' => 255,
                            ]),
                        ] :
                        [],
            ])
            ->add('street', TextType::class, [
                'required' =>
                    isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'],
                'constraints' =>
                    self::$step > 0
                    && isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'] ?
                        [
                            new NotBlank([
                                'message' => 'street.not_blank',
                            ]),
                            new Length([
                                'max' => 255,
                            ]),
                        ] :
                        [],
            ])
            ->add('number', NumberType::class, [
                'required' =>
                    isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'],
                'constraints' =>
                    self::$step > 0
                    && isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'] ?
                        [
                            new NotBlank([
                                'message' => 'number.not_blank',
                            ]),
                            new Length([
                                'max' => 11,
                            ]),
                        ] :
                        [],
            ])
            ->add('complement', TextType::class, [
                'required' => false,
                'constraints' => self::$step > 0 ? [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                ] : [],
            ])
            ->add('neighborhood', TextType::class, [
                'required' => false,
                'constraints' => self::$step > 0 ? [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                ] : [],
            ])
            ->add('state', EntityType::class, [
                'required' => true,
                'class' => State::class,
                'mapped' => false,
                'query_builder' => function (StateRepository $er) use ($form, $data) {
                    $qb = $er->createQueryBuilder('s');

                    if (! empty($data['country'])) {
                        $qb->andWhere($qb->expr()->eq('s.country', $data['country']));
                    } else {
                        $qb->andWhere($qb->expr()->eq('s.country', 0));
                    }

                    $qb->addOrderBy('s.name', 'ASC');

                    return $qb;
                },
                'choice_label' => function (State $entity) {
                    return $entity->getName();
                },
                'choice_translation_domain' => 'messages',
                'placeholder' => 'Select',
                'constraints' => self::$step > 0 ? [
                    new NotBlank([
                        'message' => 'state.not_blank',
                    ]),
                ] : [],

            ])
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
                'query_builder' => function (CityRepository $er) use ($form, $data) {
                    $qb = $er->createQueryBuilder('c');

                    if (! empty($data['state'])) {
                        $qb->andWhere($qb->expr()->eq('c.state', $data['state']));
                    } else {
                        $qb->andWhere($qb->expr()->eq('c.state', 0));
                    }

                    $qb->addOrderBy('c.name', 'ASC');

                    return $qb;
                },
                'choice_label' => function (City $entity) {
                    return $entity->getName();
                },
                'choice_translation_domain' => 'messages',
                'placeholder' => 'Select',
                'constraints' => self::$step > 0 ? [
                    new NotBlank([
                        'message' => 'city.not_blank',
                    ]),
                ] : [],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'email.not_blank',
                    ]),
                    new Email([
                        'message' => 'email.invalid',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('cellphone', NumberType::class, [
                'required' =>
                    isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'],
                'constraints' =>
                    self::$step > 0
                    && isset($data['recordType'])
                    && User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType'] ?
                        [
                            new NotBlank([
                                'message' => 'cellphone.not_blank',
                            ]),
                            new Length([
                                'min' => 1,
                                'max' => 20,
                            ]),
                        ] :
                        [],
            ])
            ->add('phone', NumberType::class, [
                'required' => false,
                'constraints' => self::$step > 0 ? [
                    new Length([
                        'min' => 1,
                        'max' => 20,
                    ]),
                ] : [],
            ])
            ->add('extension', NumberType::class, [
                'required' => false,
                'constraints' => self::$step > 0 ? [
                    new Length([
                        'max' => 255,
                    ]),
                ] : [],
            ])
            ->add('academics', CollectionType::class, [
                'entry_type' => UserAcademicsType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
                'constraints' => /*UserAcademicsType::$validationEnabled ? [
                    new Count([
                        'min' => 1,
                    ]),
                ] : */ [],
                'error_bubbling' => false,
            ])
            ->add('userEvaluationArticles', UserEvaluationArticlesType::class, [
                'label' => false,
            ])
            ->add('institutionsPrograms', UserInstitutionsProgramsType::class, [
                'label' => false,
            ])
            ->add('newsletterAssociated', CheckboxType::class, [
                'required' => false,
            ])
            ->add('newsletterEvents', CheckboxType::class, [
                'required' => false,
            ])
            ->add('newsletterPartners', CheckboxType::class, [
                'required' => false,
            ])
            ->add('methods', EntityType::class, [
                'class' => Method::class,
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                //'mapped' => false,
                'required' => true,
                'constraints' => ! empty($data['userEvaluationArticles']['wantEvaluate']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Count([
                        'min' => 1,
                    ]),
                ] : [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'fields' => ['identifier'],
                    'entityClass' => User::class,
                    'message' => 'doc.exist',
                ]),
                new UniqueEntity([
                    'fields' => ['email'],
                    'entityClass' => User::class,
                    'message' => 'email.exist',
                ]),
            ],
        ]);
    }
}
