<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\EditionFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditionFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => empty($builder->getData()->getId()),
                'constraints' => [] + (empty($builder->getData()->getId()) ? [new NotBlank(['message' => 'NotBlank.default'])] : []),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditionFile::class
        ]);
    }
}
