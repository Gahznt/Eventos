<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Services\User as UserService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AssociateType
 *
 * @package App\Bundle\Base\Form
 */
class AssociateType extends AbstractType
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * AssociateType constructor.
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
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choices' => $this->userService->getUserLoggedAssociationTypes(),
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                ],
            ])
            ->add('division', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                //'choice_translation_domain' => 'messages',
                'required' => true,
                'expanded' => true,
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'NotBlank.default',
                    ]),
                ],
            ])
            ->add('aditionals', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAssociation::class,
        ]);
    }
}
