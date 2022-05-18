<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Keyword;
use App\Bundle\Base\Entity\UserArticlesKeywords;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserArticlesKeywordsType
 * @package App\Bundle\Base\Form
 */
class UserArticlesKeywordsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keywords', EntityType::class, [
                'class' => Keyword::class,
                'multiple'  => true,
                'label' => false,
                'attr' => ['id'=> 'userArticlesKeyWord', 'class' => 'form-control bs-multiselect multiple']
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserArticlesKeywords::class,
        ]);
    }
}