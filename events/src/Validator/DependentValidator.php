<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DependentValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $options = $constraint->getOptions();
        $values = [];
        $flag = true;

        if (!empty($options)) {
            $formData = $this->context->getObject()->getParent()->getData();

            foreach ($options as $key => $option) {
                $_function = 'get'.ucwords($option);
                array_push($values, [$option =>$formData->{$_function}()]);
            }
        }

        if (!empty($values)) {
            foreach ($options as $option) {
                if (empty($values[$option])) {
                    $flag = false;
                }
            }
        }

        if (!$flag && empty($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', 'proponentId')->addViolation();
        }

    }
}
