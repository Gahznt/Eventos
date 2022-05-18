<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\SystemEnsalementSchedulingArticles;
use App\Bundle\Base\Entity\UserArticles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EnsalementSectionArticlesType extends AbstractType
{
    private $em;

    /**
     * EnsalementPriorityType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userArticles', EntityType::class, [
                'class' => UserArticles::class,
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'attr' => [
                    'class' => 'd-none',
                ],
                'error_bubbling' => false,
            ])

            ->add('articleTitleFake', HiddenType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'd-none',
                ],
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null !== $data && null !== $data->getUserArticles()) {
            $form->add('articleTitleFake', HiddenType::class, [
                'required' => false,
                'mapped' => false,
                //'property_path' => 'UserArticles.title',
                'data' => sprintf('%s - %s', $data->getUserArticles()->getId(), $data->getUserArticles()->getTitle()),
                'attr' => [
                    'class' => 'd-none',
                    'value' => sprintf('%s - %s', $data->getUserArticles()->getId(), $data->getUserArticles()->getTitle()),
                ],
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEnsalementSchedulingArticles::class,
        ]);
    }
}
