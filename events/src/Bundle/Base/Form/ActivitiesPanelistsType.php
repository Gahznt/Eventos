<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\ActivitiesPanelist;
use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Services\User as UserService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ActivitiesPanelistsType
 *
 * @package App\Bundle\Base\Form
 */
class ActivitiesPanelistsType extends AbstractType
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
     * ActivitiesPanelistsType constructor.
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
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
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
            ->add('panelist', TextType::class, [
                'label' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('panelistFake', TextType::class, [
                'attr' => [
                    'readonly' => true,
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    //new NotBlank(['message' => 'NotBlank.default']),
                ],
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

        if (! empty($data['panelist'])) {
            $form->add('panelist', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id =' . $data['panelist']);
                },
                'data' => $this->userService->getUserById($data['panelist']),
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
            'data_class' => ActivitiesPanelist::class,
        ]);
    }
}
