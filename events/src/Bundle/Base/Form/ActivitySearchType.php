<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Repository\DivisionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ActivitySearchType
 *
 * @package App\Bundle\Base\Form
 */
class ActivitySearchType extends AbstractType
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
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'search';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setOptions($options);

        $builder
            /*->add('status', ChoiceType::class, [
                'choice_translation_domain' => 'messages',
                'choices' => [
                        'Todos' => '',
                    ] + Activity::ACTIVITY_EVALUATION_STATUS,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
            ])*/
            ->add('type', ChoiceType::class, [
                'choice_translation_domain' => 'messages',
                'choices' => [
                        'Todos' => '',
                    ] + Activity::ACTIVITY_TYPES,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
            ])
            ->add('division', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'translation_domain' => 'messages',
                'required' => false,
                'query_builder' => function (DivisionRepository $er) use ($builder, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    if (! empty($options['edition'])) {
                        /** @var Edition $edition */
                        $edition = $options['edition'];

                        if (count($edition->getEvent()->getDivisions()) > 0) {
                            $ids = [];
                            foreach ($edition->getEvent()->getDivisions() as $item) {
                                $ids[] = $item->getId();
                            }

                            $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('id'), $ids));
                        }
                    }

                    $qb->addOrderBy($er->replaceFieldAlias('portuguese'), 'ASC');

                    return $qb;
                },
            ])
            ->add('q', TextType::class, [
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Institution::class,
            'edition' => null,
        ]);
    }
}
