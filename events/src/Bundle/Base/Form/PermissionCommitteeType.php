<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\UserCommittee;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Event;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Services\Edition as EditionService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class PermissionType
 * @package App\Bundle\Base\Form
 */
class PermissionCommitteeType extends AbstractType
{
    private $userService;
    private $editionService;

    public function __construct(
        UserService $userService,
        EditionService $editionService
    )
    {
        $this->userService = $userService;
        $this->editionService = $editionService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', ChoiceType::class, [
                'placeholder' => 'Select',
                'mapped' => false,
                'required' => true,
            ])
            ->add('event', EntityType::class, [
                'mapped' => false,
                'placeholder' => 'Select',
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'class' => Event::class
            ])
            ->add('edition', ChoiceType::class, [
                'mapped' => false,
                'required' => true,
                'placeholder' => 'Select',
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('division', EntityType::class, [
                'required' => true,
                'placeholder' => 'Select',
                'class' => Division::class,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!isset($data['user'], $data['edition'])) {
            return;
        }

        $options = [
            'class' => User::class,
            'query_builder' => function (EntityRepository $er) use ($data) {
                return $er->createQueryBuilder('u')
                    ->where('u.id = ' . $data['user']);
            },
            'data' => $this->userService->getUserById($data['user']),
            'label' => false,
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'NotBlank.default']),
            ],
        ];

        $form->add('user', EntityType::class, $options);

        $form->add('edition', EntityType::class, [
            'class' => Edition::class,
            'query_builder' => function (EntityRepository $er) use ($data) {
                return $er->createQueryBuilder('e')
                    ->where('e.id =' . $data['edition']);
            },
            'data' => $this->editionService->getById($data['edition']),
            'label' => false,
            'required' => true,
            'constraints' => [
                new NotBlank(['message' => 'NotBlank.default']),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserCommittee::class
        ]);
    }
}
