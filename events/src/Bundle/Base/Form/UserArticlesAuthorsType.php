<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use App\Bundle\Base\Services\User as UserService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Doctrine\ORM\QueryBuilder;

/**
 * Class UserArticlesAuthorsType
 *
 * @package App\Bundle\Base\Form
 */
class UserArticlesAuthorsType extends AbstractType
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
     * UserArticlesAuthorsType constructor.
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
            ->add('searchIdentifier', ChoiceType::class, [
                'mapped' => false,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choices' => [
                    'E-mail' => 'email',
                    'CPF' => 'cpf',
                    'Passaporte' => 'passport',
                ],
                'data' => 'email',
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('searchValue', TextType::class, [
                'mapped' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('userAuthorIdFake', TextType::class, [
                'mapped' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('userAuthorId', HiddenType::class, [
                'label' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'error_bubbling' => false,
            ])
            ->add('order', HiddenType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->addElements($form, $data);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     */
    protected function addElements(FormInterface $form, $data = [])
    {
        if (! empty($data['userAuthorId'])) {
            $form->add('userAuthorId', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($data) {
                    $qb = $er->createQueryBuilder('u');
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('id'), $data['userAuthorId']));
                    $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));
                    $qb->setMaxResults(1);

                    return $qb;
                },
                'data' => $this->userService->getUserById($data['userAuthorId']),
                'label' => false,
                'required' => self::$validationEnabled,
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'error_bubbling' => false,
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserArticlesAuthors::class,
        ]);
    }
}
