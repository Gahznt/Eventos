<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Repository\DivisionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserThemesSearchType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'search';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('division', EntityType::class, [
                'required' => false,
                'class' => Division::class,
                'placeholder' => 'Select',
                'query_builder' => function (DivisionRepository $er) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    /*if (! empty($options['edition'])) {
                        /** @var Edition $edition *
                        $edition = $options['edition'];

                        if (count($edition->getEvent()->getDivisions()) > 0) {
                            $ids = [];
                            foreach ($edition->getEvent()->getDivisions() as $item) {
                                $ids[] = $item->getId();
                            }

                            $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('id'), $ids));
                        }
                    }*/

                    $qb->addOrderBy($er->replaceFieldAlias('id'), 'ASC');

                    return $qb;
                },
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Select',
                'choices' => UserThemes::THEME_EVALUATION_STATUS,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => UserThemes::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
