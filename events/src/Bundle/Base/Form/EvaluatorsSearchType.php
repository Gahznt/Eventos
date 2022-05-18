<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\EvaluatorsSearch;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluatorsSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('search', TextType::class)
                /*->add('users', EntityType::class, [
                    'class' => User::class,
                    'placeholder' => 'Select',
                    'query_builder' => function (UserRepository $er) use ($builder) {
                        return $er->getEvaluators();
                    },
                    'choice_label' => function ($user) {
                        return $user->getName();
                    },
                ])*/
                ->add('submit', SubmitType::class, [
                    'label' => 'Search',
                    'attr' => ['style' => 'display:none;']
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EvaluatorsSearch::class,
            'method' => 'get',
            'csrf_protection' => false,
            'attr' => ['class' => 'row', 'novalidate' => 'novalidate', 'id' => 'evaluatorsFormSearch'],
        ]);
    }
}
