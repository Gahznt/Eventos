<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Services\User as UserService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class PanelType
 *
 * @package App\Bundle\Base\Form
 */
class PanelType extends AbstractType
{
    /**
     * @var int
     */
    public static $step = 1;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * PanelType constructor.
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
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('divisionId', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('language', ChoiceType::class, [
                'choices' => Panel::LANGUAGE,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('justification', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 4000,
                    ]),
                ],
            ])
            ->add('suggestion', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 800,
                    ]),
                ],
            ])
            ->add('proponentCurriculumLattesLink', TextType::class)
            ->add('proponentCurriculumPdfPath', FileType::class)
            ->add('proponentIdFake', TextType::class, [
                'attr' => ['readonly' => true],
                'mapped' => false,
            ])
            ->add('proponentId', HiddenType::class, [
                'required' => true,
                'constraints' => self::$step > 2 ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'error_bubbling' => false,
            ])
            ->add('panelsPanelists', CollectionType::class, [
                'entry_type' => PanelsPanelistsType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => self::$step > 1 ? [
                    new Count([
                        'min' => 3,
                        'max' => 5,
                    ]),
                ] : [],
                'error_bubbling' => false,
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

        $form
            ->add('proponentCurriculumLattesLink', TextType::class, [
                'required' => self::$step > 2 && empty($data['proponentCurriculumPdfPath']),
                'constraints' => self::$step > 2 && empty($data['proponentCurriculumPdfPath']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('proponentCurriculumPdfPath', FileType::class, [
                'label' => false,
                'data_class' => null,
                'required' => self::$step > 2 && empty($data['proponentCurriculumLattesLink']),
                'constraints' => self::$step > 2 && empty($data['proponentCurriculumLattesLink']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ]);

        if (isset($data['proponentId'])) {
            $data['proponentId'] = $this->userService->getUserById($data['proponentId']);
        }

        $event->setData($data);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Panel::class,
        ]);
    }
}
