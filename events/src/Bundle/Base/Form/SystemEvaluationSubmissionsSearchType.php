<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluationSubmissionsSearch;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\UserThemesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SystemEvaluationSubmissionsSearchType
 *
 * @package App\Bundle\Base\Form
 */
class SystemEvaluationSubmissionsSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userThemes', EntityType::class, [
                'class' => UserThemes::class,
                'placeholder' => 'Select',
                'query_builder' => function (UserThemesRepository $er) use ($builder, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');
                    $qb->innerJoin(Division::class, 'd', 'WITH', 'd.id=ut.division');
                    $qb->andWhere($qb->expr()->eq('ut.status', UserThemes::THEME_EVALUATION_APPROVED));
                    $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));

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

                    $qb->addOrderBy('d.initials', 'ASC');
                    $qb->addOrderBy('ut.position', 'ASC');
                    $qb->addOrderBy('utd.portugueseTitle', 'ASC');

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%s - %d - %s',
                        $entity->getDivision() ? $entity->getDivision()->getInitials() : '',
                        $entity->getPosition(),
                        $entity->getDetails()->getTitle()
                    );
                },
            ])
            ->add('status', ChoiceType::class, [
                'placeholder' => 'Select',
                'choices' => UserArticles::ARTICLE_EVALUATION_STATUS,
            ])
//            ->add('type', ChoiceType::class, [
//                'expanded' => true,
//                'choices' =>  ['Sem erros' => 1, 'Com erros' => 2, 'Todas' => 3]
//            ])
            ->add('search', TextType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['style' => 'display:none;'],
            ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEvaluationSubmissionsSearch::class,
            'method' => 'get',
            'csrf_protection' => false,
            'attr' => ['class' => 'row', 'novalidate' => 'novalidate', 'id' => 'systemEvaluationSubmissionsSearch'],
            'edition' => null,
        ]);
    }
}
