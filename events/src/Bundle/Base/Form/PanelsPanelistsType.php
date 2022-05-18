<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\PanelsPanelist;
use App\Bundle\Base\Services\User as UserService;
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
 * Class PanelsPanelistsType
 *
 * @package App\Bundle\Base\Form
 */
class PanelsPanelistsType extends AbstractType
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
     * PanelsPanelistsType constructor.
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
            ->add('countryId', EntityType::class, [
                'class' => Country::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => false,
            ])
            ->add('cpf', TextType::class, [
                'mapped' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('proponentCurriculumLattesLink', TextType::class)
            ->add('proponentCurriculumPdfPath', FileType::class)
            ->add('panelistId', HiddenType::class, [
                'label' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'error_bubbling' => false,
            ])
            ->add('panelistIdFake', TextType::class, [
                'attr' => ['readonly' => true],
                'mapped' => false,
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
                'required' => self::$validationEnabled && empty($data['proponentCurriculumPdfPath']),
                'constraints' => self::$validationEnabled && empty($data['proponentCurriculumPdfPath']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('proponentCurriculumPdfPath', FileType::class, [
                'label' => false,
                'data_class' => null,
                'required' => self::$validationEnabled && empty($data['proponentCurriculumLattesLink']),
                'constraints' => self::$validationEnabled && empty($data['proponentCurriculumLattesLink']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ]);

        if (isset($data['panelistId'])) {
            $data['panelistId'] = $this->userService->getUserById($data['panelistId']);
        }

        $event->setData($data);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PanelsPanelist::class,
        ]);
    }
}
