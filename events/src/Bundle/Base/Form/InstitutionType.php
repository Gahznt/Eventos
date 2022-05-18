<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\City;
use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Entity\State;
use App\Bundle\Base\Services\City as CityService;
use App\Bundle\Base\Services\Method as MethodService;
use App\Bundle\Base\Services\State as StateService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Bundle\Base\Services\Country as CountryService;
use App\Bundle\Base\Services\Util as UtilService;

class InstitutionType extends AbstractType
{
    private $translator;
    private $cityService;
    private $countryService;
    private $stateService;
    private $methodService;
    private $utilService;

    public function __construct(
        TranslatorInterface $translator,
        CityService $cityService,
        CountryService $countryService,
        StateService $stateService,
        MethodService $methodService,
        UtilService $utilService
    )
    {
        $this->translator = $translator;
        $this->cityService = $cityService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->methodService = $methodService;
        $this->utilService = $utilService;
    }

    /**
     * @param FormBuilderInterface $form
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $form, array $options)
    {
        $form
            ->add('name', TextType::class, [
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
            ->add('initials', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                    new Length([
                        'max' => 30,
                    ]),
                ],
            ])
            ->add('type', CheckboxType::class)
            ->add('paid', CheckboxType::class)
            ->add('status', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => Institution::INSTITUTION_STATUS,
                'choice_translation_domain' => 'messages',
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('phone', NumberType::class, [
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
            ->add('cellphone', NumberType::class, [
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
            ->add('street', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'street.not_blank',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('zipcode', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'zipcode.not_blank',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('number', NumberType::class, [
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
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                    new NotBlank([
                        'message' => 'neighborhood.not_blank',
                    ]),

                ],
            ])
            ->add('state', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\State',
                'required' => true,
                'mapped' => false,
                'data' => $options['data']->getCity() ? $options['data']->getCity()->getState() : null,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'Select',
                'constraints' => [
                    new NotBlank([
                        'message' => 'state.not_blank',
                    ]),
                ],

            ])
            ->add('city', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\City',
                'required' => true,
                'data' => $options['data']->getCity(),
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

        $form->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
        $form->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);

    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->addElements($form);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     * @param Country|null $country
     * @param State|null $state
     * @param City|null $city
     */
    protected function addElements(FormInterface $form, $data = [], ?Country $country = null, ?State $state = null, ?City $city = null)
    {
        if (!$country) {
            return;
        }

        $form->add('country', EntityType::class, [
            'class' => Country::class,
            'required' => true,
            'mapped' => false,
            'data' => $country,
            'choice_translation_domain' => 'messages',
            'placeholder' => 'Select',
            'constraints' => [
                new NotBlank([
                    'message' => 'country.not_blank',
                ]),
            ],
        ]);

        if (!$state) {
            return;
        }

        $cityChoices = $state->getCities()->getValues();

        $form->add('city', EntityType::class, [
            'class' => City::class,
            'required' => true,
            'choices' => $cityChoices,
            'choice_translation_domain' => 'messages',
            'placeholder' => 'Select',
            'constraints' => [
                new NotBlank([
                    'message' => 'city.not_blank',
                ]),
            ],
        ]);

        if (!$city) {
            return;
        }

        $form->get('city')->setData($city);

        $form->add('academics', CollectionType::class, [
            'entry_type' => UserAcademicsType::class,
            'entry_options' => [
                'label' => false,
            ],
            'label' => false,
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['style' => 'display:none;'],
            ]);

        $form->add('userEvaluationArticles', UserEvaluationArticlesType::class, [
            'label' => false,
        ]);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $state = null;
        $city = null;
        $country = null;

        if (isset($data['country'])) {

            $country = $this->countryService->getCountryById($data['country']);

            if (isset($data['state'])) {
                $state = $this->stateService->getStateById($data['state']);
            }

            if (isset($data['city'])) {
                $city = $this->cityService->getCityById($data['city']);
            }
        }


        $whiteListInt = ['phone', 'cellphone'];

//        if (isset($data['recordType']) && $data['recordType'] !== "2") {
//            array_push($whiteListInt, 'identifier');
//        }

        $this->utilService->onlyIntInputs($whiteListInt, $data);
        $event->setData($data);

        $this->addElements($form, $data, $country, $state, $city);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Institution::class,
            'csrf_protection' => false,
            'constraints' => new UniqueEntity([
                'fields' => ['name'],
                'entityClass' => Institution::class,
                'message' => 'doc.exist',
            ]),
        ]);
    }
}
