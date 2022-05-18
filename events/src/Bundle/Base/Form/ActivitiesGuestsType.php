<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\ActivitiesGuest;
use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Services\User as UserService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ActivitiesGuestsType
 *
 * @package App\Bundle\Base\Form
 */
class ActivitiesGuestsType extends AbstractType
{
    /**
     * @var bool
     */
    public static $validationEnabled = true;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * ActivitiesGuestsType constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cpf', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => false,
                'required' => self::$validationEnabled,
                'constraints' => /*self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] :*/ [],
            ])
            ->add('proponentCurriculumLattesLink', TextType::class, [
                'label' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('proponentCurriculumPdfPath', FileType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    // new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('guest', HiddenType::class, [
                'label' => false,
                'required' => false,
                'constraints' => /*self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : */ [],
                'error_bubbling' => false,
            ])
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (! empty($data['guest'])) {
            $form->add('guest', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Select',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id =' . $data['guest']);
                },
                'data' => $this->userService->getUserById($data['guest']),
                'label' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ActivitiesGuest::class,
        ]);
    }
}
