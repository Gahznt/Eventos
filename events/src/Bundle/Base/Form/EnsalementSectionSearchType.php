<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEnsalementScheduling;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\UserThemesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EnsalementSectionSearchType
 *
 * @package App\Bundle\Base\Form
 */
class EnsalementSectionSearchType extends AbstractType
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return CoordinatorsSearchType
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setOptions($options);

        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'search';
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
        $options = $this->getOptions();

        if (! isset($data['division'])) {
            $data['division'] = '';
        }

        $form->add('division', EntityType::class, [
            'class' => Division::class,
            'placeholder' => 'Select',
            'choice_translation_domain' => 'messages',
            'required' => false,
            'mapped' => false,
            'constraints' => [
                // new NotBlank(['message' => 'NotBlank.default']),
            ],
        ]);

        $form->add('theme', ! empty($data['division']) ? EntityType::class : ChoiceType::class, [
                'placeholder' => 'Select',
                'attr' => [
                    'disabled' => ! empty($data['division']) ? false : true,
                ],
                'constraints' => ! empty($data['division']) ? [
                    // new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'required' => false,
                'mapped' => false,
            ] + (! empty($data['division']) ? [
                'class' => UserThemes::class,
                'query_builder' => function (UserThemesRepository $er) use ($form, $data, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');
                    $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), UserThemes::THEME_EVALUATION_APPROVED));
                    if (! empty($data['division'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('division'), $data['division']));
                    }

                    if (! empty($options['edition'])) {
                        /** @var Edition $edition */
                        $edition = $options['edition'];

                        if (count($edition->getEvent()->getDivisions()) > 0) {
                            $ids = [];
                            foreach ($edition->getEvent()->getDivisions() as $item) {
                                $ids[] = $item->getId();
                            }

                            $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('division'), $ids));
                        }
                    }

                    $qb->addOrderBy('ut.position', 'ASC');
                    $qb->addOrderBy('utd.portugueseTitle', 'ASC');

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%d - %s', $entity->getPosition(), $entity->getDetails()->getTitle());
                },
            ] : [
                'choice_translation_domain' => 'messages',
            ]));

        $form->add('q', SearchType::class, [
            'required' => false,
            'mapped' => false,
            'constraints' => [
                // new NotBlank(['message' => 'NotBlank.default']),
            ],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEnsalementScheduling::class,
            'edition' => null,
        ]);
    }
}

