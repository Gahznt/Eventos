<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\City;
use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\State;
use App\Bundle\Base\Entity\User;
use App\Validator\Identifier;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SignUpType extends UserEditType
{
    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        // Importante!
        // Não chamar o addElements aqui, ele já é chamado dentro do parent::onPostSetData
        parent::onPostSetData($event);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (User::USER_RECORD_TYPE_BRAZILIAN == $data['recordType']) {
            $data['isForeignUseCpf'] = null;
            $data['isForeignUsePassport'] = null;

            $data['identifier'] = preg_replace('/[^0-9]*/', '', $data['identifier']);

            if (empty($data['country'])) {
                $data['country'] = Country::DEFAULT_LOCATE_ID;
            }
        } else {
            if (User::USER_FOREIGN_USE_PASSPORT_AUTOMATIC == $data['isForeignUsePassport']) {
                $data['identifier'] = 'F' . time();
            }

            if (empty($data['country']) && empty($data['state']) && empty($data['city'])) {
                $data['country'] = Country::OTHER_LOCATE_ID;
                $data['state'] = State::OTHER_STATE_ID;
                $data['city'] = City::OTHER_CITY_ID;
            }

        }

        $event->setData($data);

        // Importante!
        // Não chamar o addElements aqui, ele já é chamado dentro do parent::onPreSubmit
        parent::onPreSubmit($event);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     */
    protected function addElements(FormInterface $form, $data = [])
    {
        $form
            ->add('locale', ChoiceType::class, [
                'choices' => User::USER_LOCALES,
                'choice_translation_domain' => 'messages',
                'translation_domain' => true,
                'multiple' => false,
                'expanded' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('recordType', ChoiceType::class, [
                'choices' => User::USER_RECORD_TYPE,
                'choice_translation_domain' => 'messages',
                'translation_domain' => true,
                'multiple' => false,
                'expanded' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'recordType.not_blank',
                    ]),
                ],
            ])
            ->add('isForeignUseCpf', ChoiceType::class, [
                'choices' => User::USER_FOREIGN_USE_CPF,
                'choice_translation_domain' => 'messages',
                'translation_domain' => true,
                'multiple' => false,
                'expanded' => true,
                'constraints' =>
                    isset($data['recordType'])
                    && User::USER_RECORD_TYPE_FOREIGN == $data['recordType'] ?
                        [
                            new NotBlank(['message' => 'NotBlank.default']),
                        ] :
                        [],
            ])
            ->add('isForeignUsePassport', ChoiceType::class, [
                'choices' => User::USER_FOREIGN_USE_PASSPORT,
                'choice_translation_domain' => 'messages',
                'translation_domain' => true,
                'multiple' => false,
                'expanded' => true,
                'constraints' =>
                    isset($data['recordType'])
                    && User::USER_RECORD_TYPE_FOREIGN == $data['recordType']
                    && isset($data['isForeignUseCpf'])
                    && USER::USER_FOREIGN_USE_CPF_NO == $data['isForeignUseCpf'] ?
                        [
                            new NotBlank(['message' => 'NotBlank.default']),
                        ] :
                        [],
            ])
            ->add('identifier', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'doc.not_blank',
                    ]),
                    new Identifier(),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'invalid_message' => 'password.invalid',
                'first_options' => [
                    'always_empty' => false,
                ],
                'second_options' => [
                    'always_empty' => false,
                ],
                'constraints' => self::$step > 0 ? [
                    new NotBlank([
                        'message' => 'password.not_blank',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 255,
                    ]),
                    new PasswordRequirements([
                        'missingNumbersMessage' => 'password.with_number',
                        'requireNumbers' => true,
                        'minLength' => 8,
                    ]),
                ] : [],
            ]);

        parent::addElements($form, $data);
    }
}
