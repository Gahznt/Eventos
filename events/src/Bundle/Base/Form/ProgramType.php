<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\City;
use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\State;
use App\Bundle\Base\Repository\CityRepository;
use App\Bundle\Base\Repository\StateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $form, array $options)
    {
        $form->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $form->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Program $entity */
        $entity = $event->getData();

        $data = [];

        if ($entity && $entity->getCity()) {
            // $data['country'] = $entity->getCity()->getCountry()->getId();
            $data['state'] = $entity->getCity()->getState()->getId();
        }

        $this->addElements($form, $data);

        // campos não mapeados
        if ($entity && $entity->getCity()) {
            // $form->get('country')->setData($entity->getCity()->getCountry());
            $form->get('state')->setData($entity->getCity()->getState());
        }
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $data['phone'] = preg_replace('/[^0-9]*/', '', $data['phone']);
        $data['cellphone'] = preg_replace('/[^0-9]*/', '', $data['cellphone']);

        $event->setData($data);

        $this->addElements($form, $data);
    }

    protected function addElements(FormInterface $form, $data = [])
    {
        $form
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'name.not_blank',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('paid', CheckboxType::class)
            ->add('status', ChoiceType::class, [
                'choices' => Program::PROGRAM_STATUS,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choice_value' => function ($value) {
                    return (int)$value;
                },
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('sortPosition', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 20,
                    ]),
                    new NotBlank([
                        'message' => 'phone.not_blank',
                    ]),
                ],
            ])
            ->add('cellphone', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'cellphone.not_blank',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 20,
                    ]),
                ],
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
            ->add('website', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'name.not_blank',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('zipcode', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'zipcode.not_blank',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('street', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'street.not_blank',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('number', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'number.not_blank',
                    ]),
                    new Length([
                        'max' => 11,
                    ]),
                ],
            ])
            ->add('complement', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('neighborhood', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                ],
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
                        $qb->andWhere($qb->expr()->eq('s.country', Country::DEFAULT_LOCATE_ID));
                    }

                    $qb->addOrderBy('s.name', 'ASC');

                    return $qb;
                },
                'choice_label' => function (State $entity) {
                    return $entity->getName();
                },
                'choice_translation_domain' => 'messages',
                'placeholder' => 'Select',
                'constraints' => [
                    new NotBlank([
                        'message' => 'state.not_blank',
                    ]),
                ],

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
                'constraints' => [
                    new NotBlank([
                        'message' => 'city.not_blank',
                    ]),
                ],
            ])
            ->add('coordinator', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                    new NotBlank([
                        'message' => 'coordinator.not_blank',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
            'constraints' => [
                new UniqueEntity([
                    'fields' => ['email', 'status'],
                    'entityClass' => Program::class,
                    'message' => 'email.exist',
                ]),
            ],
        ]);
    }
}
